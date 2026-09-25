<?php

namespace App\Modules\Library\Infrastructure\Persistence\Eloquent;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Domain\Member\Member;
use App\Modules\Library\Ports\MemberRepositoryInterface;

class EloquentMemberRepository implements MemberRepositoryInterface
{
    public function findById(int $id): ?Member
    {
        $model = MemberModel::find($id);

        return $model === null ? null : $this->toDomain($model);
    }

    public function findByIdentificador(string $identificador): ?Member
    {
        $model = MemberModel::where('identificador', trim($identificador))->first();

        return $model === null ? null : $this->toDomain($model);
    }

    public function save(Member $member): Member
    {
        $model = $member->id() !== null ? MemberModel::findOrFail($member->id()) : new MemberModel;

        $model->fill([
            'tipo' => $member->tipo(),
            'nombre' => $member->nombre(),
            'identificador' => $member->identificador(),
            'grupo_escolar' => $member->grupoEscolar(),
        ])->save();

        $member->setId($model->id);

        return $member;
    }

    public function search(string $term, int $limit = 20): array
    {
        $term = trim($term);

        if ($term === '') {
            return [];
        }

        return MemberModel::where('nombre', 'like', "%{$term}%")
            ->orWhere('identificador', 'like', "%{$term}%")
            ->orWhere('grupo_escolar', 'like', "%{$term}%")
            ->orderBy('nombre')
            ->limit($limit)
            ->get()
            ->map(fn (MemberModel $model) => $this->toDomain($model))
            ->all();
    }

    public function all(): array
    {
        return MemberModel::orderBy('nombre')->get()->map(fn (MemberModel $model) => $this->toDomain($model))->all();
    }

    public function paginateMembers(int $page, int $perPage, ?string $term = null, ?string $tipo = null): PaginatedResult
    {
        $query = MemberModel::query();

        if ($term !== null && trim($term) !== '') {
            $term = trim($term);
            $query->where(function ($w) use ($term) {
                $w->where('nombre', 'like', "%{$term}%")
                    ->orWhere('identificador', 'like', "%{$term}%")
                    ->orWhere('grupo_escolar', 'like', "%{$term}%");
            });
        }

        if ($tipo !== null && $tipo !== '') {
            $query->where('tipo', $tipo);
        }

        $total = (clone $query)->count();
        $items = $query->orderBy('nombre')->forPage(max(1, $page), $perPage)->get()
            ->map(fn (MemberModel $model) => $this->toDomain($model))->all();

        return new PaginatedResult($items, $total, max(1, $page), $perPage);
    }

    private function toDomain(MemberModel $model): Member
    {
        return Member::reconstitute(
            $model->id,
            $model->tipo,
            $model->nombre,
            $model->identificador,
            $model->grupo_escolar,
        );
    }
}
