<?php

namespace App\Modules\Library\Application\Loan\UseCases;

use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Ports\LoanRepositoryInterface;

class ListActiveLoans
{
    public function __construct(private readonly LoanRepositoryInterface $loans) {}

    /**
     * @return array<int, Loan>
     */
    public function handle(): array
    {
        return $this->loans->listActive();
    }
}
