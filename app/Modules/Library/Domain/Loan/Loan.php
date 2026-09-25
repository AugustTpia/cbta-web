<?php

namespace App\Modules\Library\Domain\Loan;

use App\Modules\Library\Domain\Member\Member;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Entidad Loan. Único punto de verdad para "¿está vencido?": isOverdue().
 *
 * estado: devuelto (todo devuelto) > vencido (vencido + pendientes) >
 *         parcial (algunos devueltos) > activo.
 */
final class Loan
{
    public const ACTIVO = 'activo';

    public const PARCIAL = 'parcial';

    public const DEVUELTO = 'devuelto';

    public const VENCIDO = 'vencido';

    /** Tope de libros por préstamo individual (grupal sin tope). */
    public const MAX_ITEMS_INDIVIDUAL = 5;

    /**
     * @param  array<int, LoanItem>  $items
     */
    private function __construct(
        private ?int $id,
        private int $memberId,
        private string $memberType,
        private DateTimeImmutable $loanDate,
        private DateTimeImmutable $dueDate,
        private string $status,
        private array $items = [],
    ) {
        $this->guardInvariants();
    }

    public static function create(
        int $memberId,
        string $memberType,
        DateTimeImmutable $dueDate,
        ?DateTimeImmutable $loanDate = null,
    ): self {
        return new self(null, $memberId, $memberType, $loanDate ?? new DateTimeImmutable, $dueDate, self::ACTIVO);
    }

    /**
     * @param  array<int, LoanItem>  $items
     */
    public static function reconstitute(
        ?int $id,
        int $memberId,
        string $memberType,
        DateTimeImmutable $loanDate,
        DateTimeImmutable $dueDate,
        string $status,
        array $items = [],
    ): self {
        return new self($id, $memberId, $memberType, $loanDate, $dueDate, $status, $items);
    }

    public function addItem(LoanItem $item): void
    {
        if ($this->isIndividual() && count($this->items) >= self::MAX_ITEMS_INDIVIDUAL) {
            throw new LoanLimitExceededException('Máximo '.self::MAX_ITEMS_INDIVIDUAL.' libros por préstamo individual.');
        }

        $this->items[] = $item;
    }

    public function markItemReturned(int $bookId, ?DateTimeImmutable $at = null): void
    {
        $now = $at ?? new DateTimeImmutable;

        foreach ($this->items as $item) {
            if ($item->bookId() === $bookId && ! $item->isReturned()) {
                $item->markReturned($now);
                $this->recalculateStatus($now);

                return;
            }
        }

        throw new InvalidArgumentException('El préstamo no incluye ese libro pendiente.');
    }

    public function isOverdue(?DateTimeImmutable $now = null): bool
    {
        $now ??= new DateTimeImmutable;

        return $now > $this->dueDate && $this->pendingCount() > 0;
    }

    public function recalculateStatus(?DateTimeImmutable $now = null): void
    {
        $now ??= new DateTimeImmutable;
        $total = count($this->items);
        $pending = $this->pendingCount();

        if ($total > 0 && $pending === 0) {
            $this->status = self::DEVUELTO;
        } elseif ($this->isOverdue($now)) {
            $this->status = self::VENCIDO;
        } elseif ($pending < $total) {
            $this->status = self::PARCIAL;
        } else {
            $this->status = self::ACTIVO;
        }
    }

    public function pendingCount(): int
    {
        $pending = 0;

        foreach ($this->items as $item) {
            if (! $item->isReturned()) {
                $pending++;
            }
        }

        return $pending;
    }

    /**
     * Días de atraso respecto a la fecha estimada (0 si no está vencido).
     */
    public function overdueDays(?DateTimeImmutable $now = null): int
    {
        $now ??= new DateTimeImmutable;

        if (! $this->isOverdue($now)) {
            return 0;
        }

        return (int) $now->diff($this->dueDate)->days;
    }

    public function isIndividual(): bool
    {
        return in_array($this->memberType, Member::INDIVIDUAL_TYPES, true);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function memberId(): int
    {
        return $this->memberId;
    }

    public function memberType(): string
    {
        return $this->memberType;
    }

    public function loanDate(): DateTimeImmutable
    {
        return $this->loanDate;
    }

    public function dueDate(): DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function status(): string
    {
        return $this->status;
    }

    /**
     * @return array<int, LoanItem>
     */
    public function items(): array
    {
        return $this->items;
    }

    private function guardInvariants(): void
    {
        if (! in_array($this->memberType, Member::TYPES, true)) {
            throw new InvalidArgumentException('Tipo de miembro inválido.');
        }

        if (! in_array($this->status, [self::ACTIVO, self::PARCIAL, self::DEVUELTO, self::VENCIDO], true)) {
            throw new InvalidArgumentException('Estado de préstamo inválido.');
        }
    }
}
