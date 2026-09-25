<?php

namespace App\Modules\Library\Application\Loan\UseCases;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Ports\LoanRepositoryInterface;

class PaginateOverdueLoans
{
    public function __construct(private readonly LoanRepositoryInterface $loans) {}

    public function handle(int $page = 1, int $perPage = 10, ?string $memberTerm = null): PaginatedResult
    {
        return $this->loans->paginateOverdue($page, $perPage, $memberTerm);
    }
}
