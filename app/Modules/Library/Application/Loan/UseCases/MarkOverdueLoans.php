<?php

namespace App\Modules\Library\Application\Loan\UseCases;

use App\Modules\Library\Ports\LoanRepositoryInterface;
use App\Modules\Library\Ports\NotifierInterface;
use DateTimeImmutable;

/**
 * Job diario: recalcula el estado de los vencidos y avisa.
 */
class MarkOverdueLoans
{
    public function __construct(
        private readonly LoanRepositoryInterface $loans,
        private readonly NotifierInterface $notifier,
    ) {}

    /**
     * @return int cantidad de préstamos marcados como vencidos
     */
    public function handle(?DateTimeImmutable $now = null): int
    {
        $overdue = $this->loans->listOverdue($now);

        foreach ($overdue as $loan) {
            $loan->recalculateStatus($now);
            $this->loans->save($loan);
        }

        if ($overdue !== []) {
            $this->notifier->notifyOverdueLoans($overdue);
        }

        return count($overdue);
    }
}
