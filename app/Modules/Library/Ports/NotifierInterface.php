<?php

namespace App\Modules\Library\Ports;

use App\Modules\Library\Domain\Loan\Loan;

interface NotifierInterface
{
    /**
     * @param  array<int, Loan>  $loans
     */
    public function notifyOverdueLoans(array $loans): void;
}
