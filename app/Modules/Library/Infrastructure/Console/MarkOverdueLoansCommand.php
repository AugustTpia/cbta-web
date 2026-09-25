<?php

namespace App\Modules\Library\Infrastructure\Console;

use App\Modules\Library\Application\Loan\UseCases\MarkOverdueLoans;
use Illuminate\Console\Command;

class MarkOverdueLoansCommand extends Command
{
    protected $signature = 'biblioteca:marcar-vencidos';

    protected $description = 'Recalcula el estado de los préstamos vencidos y avisa';

    public function handle(MarkOverdueLoans $useCase): int
    {
        $count = $useCase->handle();

        $this->info("Préstamos vencidos: {$count}");

        return self::SUCCESS;
    }
}
