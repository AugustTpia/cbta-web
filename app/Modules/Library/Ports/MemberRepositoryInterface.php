<?php

namespace App\Modules\Library\Ports;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Domain\Member\Member;

interface MemberRepositoryInterface
{
    public function findById(int $id): ?Member;

    public function findByIdentificador(string $identificador): ?Member;

    public function save(Member $member): Member;

    /**
     * Búsqueda por nombre, identificador o grupo escolar (buscador del
     * formulario de préstamo). Devuelve máximo $limit.
     *
     * @return array<int, Member>
     */
    public function search(string $term, int $limit = 20): array;

    /**
     * @return array<int, Member> ordenados por nombre
     */
    public function all(): array;

    public function paginateMembers(int $page, int $perPage, ?string $term = null, ?string $tipo = null): PaginatedResult;
}
