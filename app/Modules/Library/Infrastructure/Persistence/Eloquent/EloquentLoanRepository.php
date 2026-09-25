<?php

namespace App\Modules\Library\Infrastructure\Persistence\Eloquent;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Domain\Loan\LoanItem;
use App\Modules\Library\Ports\LoanRepositoryInterface;
use DateTimeImmutable;

class EloquentLoanRepository implements LoanRepositoryInterface
{
    public function findById(int $id): ?Loan
    {
        $model = LoanModel::with('items')->find($id);

        return $model === null ? null : $this->toDomain($model);
    }

    public function save(Loan $loan): Loan
    {
        $model = $loan->id() !== null ? LoanModel::findOrFail($loan->id()) : new LoanModel;

        $model->fill([
            'member_id' => $loan->memberId(),
            'fecha_prestamo' => $loan->loanDate(),
            'fecha_estimada_devolucion' => $loan->dueDate(),
            'estado' => $loan->status(),
        ])->save();

        $loan->setId($model->id);

        foreach ($loan->items() as $item) {
            $itemModel = $item->id() !== null ? LoanItemModel::findOrFail($item->id()) : new LoanItemModel;

            $itemModel->fill([
                'loan_id' => $model->id,
                'book_id' => $item->bookId(),
                'fecha_devolucion_real' => $item->returnedAt(),
                'devuelto' => $item->isReturned(),
            ])->save();

            $item->setId($itemModel->id);
        }

        return $loan;
    }

    public function listActive(): array
    {
        $now = new DateTimeImmutable;
        $active = [];

        foreach (LoanModel::with('items')->whereIn('estado', [Loan::ACTIVO, Loan::PARCIAL])->cursor() as $model) {
            $loan = $this->toDomain($model);

            // Un préstamo vencido pertenece a listOverdue aunque el job
            // diario aún no haya actualizado su estado.
            if (! $loan->isOverdue($now)) {
                $active[] = $loan;
            }
        }

        return $active;
    }

    public function listOverdue(?DateTimeImmutable $now = null): array
    {
        $now ??= new DateTimeImmutable;

        $overdue = [];

        foreach (LoanModel::with('items')->where('estado', '!=', Loan::DEVUELTO)->cursor() as $model) {
            $loan = $this->toDomain($model);

            if ($loan->isOverdue($now)) {
                $overdue[] = $loan;
            }
        }

        usort($overdue, fn (Loan $a, Loan $b) => $b->overdueDays($now) <=> $a->overdueDays($now));

        return $overdue;
    }

    public function listBetween(?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): array
    {
        $query = LoanModel::with('items')->orderBy('fecha_prestamo');

        if ($from !== null) {
            $query->where('fecha_prestamo', '>=', $from);
        }

        if ($to !== null) {
            $query->where('fecha_prestamo', '<=', $to);
        }

        return $query->get()->map(fn (LoanModel $model) => $this->toDomain($model))->all();
    }

    public function listByMember(int $memberId): array
    {
        return LoanModel::with('items')
            ->where('member_id', $memberId)
            ->orderByDesc('fecha_prestamo')
            ->get()
            ->map(fn (LoanModel $model) => $this->toDomain($model))
            ->all();
    }

    public function paginateActive(int $page, int $perPage, ?string $memberTerm = null, ?DateTimeImmutable $now = null): PaginatedResult
    {
        $now ??= new DateTimeImmutable;

        return $this->paginateLoans(
            $page, $perPage, $memberTerm,
            fn ($query) => $query->whereIn('estado', [Loan::ACTIVO, Loan::PARCIAL])
                ->where('fecha_estimada_devolucion', '>=', $now),
            'fecha_estimada_devolucion', 'asc',
        );
    }

    public function paginateOverdue(int $page, int $perPage, ?string $memberTerm = null, ?DateTimeImmutable $now = null): PaginatedResult
    {
        $now ??= new DateTimeImmutable;

        return $this->paginateLoans(
            $page, $perPage, $memberTerm,
            fn ($query) => $query->where('estado', '!=', Loan::DEVUELTO)
                ->where('fecha_estimada_devolucion', '<', $now),
            'fecha_estimada_devolucion', 'asc',
        );
    }

    private function paginateLoans(int $page, int $perPage, ?string $memberTerm, callable $scope, string $order, string $dir): PaginatedResult
    {
        $page = max(1, $page);
        $query = LoanModel::with('items');

        $scope($query);

        if ($memberTerm !== null && trim($memberTerm) !== '') {
            $term = trim($memberTerm);
            $query->whereHas('member', fn ($q) => $q->where('nombre', 'like', "%{$term}%"));
        }

        $total = (clone $query)->count();
        $items = $query->orderBy($order, $dir)->forPage($page, $perPage)->get()
            ->map(fn (LoanModel $model) => $this->toDomain($model))->all();

        return new PaginatedResult($items, $total, $page, $perPage);
    }

    private function toDomain(LoanModel $model): Loan
    {
        $items = [];

        foreach ($model->items as $itemModel) {
            $items[] = new LoanItem(
                $itemModel->id,
                $itemModel->book_id,
                $itemModel->devuelto,
                $itemModel->fecha_devolucion_real?->toDateTimeImmutable(),
            );
        }

        $memberType = $model->member?->tipo ?? 'alumno';

        return Loan::reconstitute(
            $model->id,
            $model->member_id,
            $memberType,
            $model->fecha_prestamo->toDateTimeImmutable(),
            $model->fecha_estimada_devolucion->toDateTimeImmutable(),
            $model->estado,
            $items,
        );
    }
}
