<?php

namespace App\Modules\Library\Application\Loan\UseCases;

use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Ports\LoanRepositoryInterface;
use DateTimeImmutable;

class ListOverdueLoans
{
    public function __construct(private readonly LoanRepositoryInterface $loans) {}

    /**
     * @return array<int, Loan> de más a menos atrasados
     */
    public function handle(?DateTimeImmutable $now = null): array
    {
        return $this->loans->listOverdue($now);
    }
}
