<?php

namespace App\Modules\Library\Application\Member\UseCases;

use App\Modules\Library\Domain\Member\Member;
use App\Modules\Library\Ports\MemberRepositoryInterface;

/**
 * Buscador del formulario de préstamo: por nombre, matrícula/clave
 * o grupo y grado (muestra los alumnos de ese grupo).
 */
class SearchMembers
{
    public function __construct(private readonly MemberRepositoryInterface $members) {}

    /**
     * @return array<int, Member>
     */
    public function handle(string $term, int $limit = 20): array
    {
        return $this->members->search($term, $limit);
    }
}
