<?php

namespace App\Modules\Library\Ports;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Domain\Loan\Loan;
use DateTimeImmutable;

interface LoanRepositoryInterface
{
    public function findById(int $id): ?Loan;

    public function save(Loan $loan): Loan;

    /**
     * @return array<int, Loan>
     */
    public function listActive(): array;

    /**
     * @return array<int, Loan> ordenados de más a menos atrasados
     */
    public function listOverdue(?DateTimeImmutable $now = null): array;

    /**
     * Todos los préstamos (activos e históricos) cuya fecha de préstamo
     * cae en el rango. Sin filtro si algún extremo es null.
     *
     * @return array<int, Loan>
     */
    public function listBetween(?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): array;

    /**
     * @return array<int, Loan> historial completo de un miembro
     */
    public function listByMember(int $memberId): array;

    public function paginateActive(int $page, int $perPage, ?string $memberTerm = null, ?DateTimeImmutable $now = null): PaginatedResult;

    public function paginateOverdue(int $page, int $perPage, ?string $memberTerm = null, ?DateTimeImmutable $now = null): PaginatedResult;
}
