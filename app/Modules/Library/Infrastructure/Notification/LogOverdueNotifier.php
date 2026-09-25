<?php

namespace App\Modules\Library\Infrastructure\Notification;

use App\Modules\Library\Ports\NotifierInterface;
use Illuminate\Support\Facades\Log;

/**
 * Aviso de vencidos: por ahora al log (visible en dashboard vía
 * ListOverdueLoans). Correo opcional a futuro si hay internet.
 */
class LogOverdueNotifier implements NotifierInterface
{
    public function notifyOverdueLoans(array $loans): void
    {
        Log::warning('Biblioteca: préstamos vencidos', [
            'total' => count($loans),
            'prestamos' => array_map(fn ($loan) => $loan->id(), $loans),
        ]);
    }
}
