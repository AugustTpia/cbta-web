<?php

namespace App\Modules\Library\Application\Book\UseCases;

use App\Modules\Library\Domain\Book\Book;
use App\Modules\Library\Ports\BookRepositoryInterface;

/**
 * Usado por el input del escáner en el formulario de préstamo:
 * acepta ISBN tecleado/manual o código de barras escaneado.
 */
class FindBookByIsbnOrBarCode
{
    public function __construct(private readonly BookRepositoryInterface $books) {}

    public function handle(string $code): ?Book
    {
        $code = trim($code);

        if ($code === '') {
            return null;
        }

        return $this->books->findByIsbnOrBarCode($code);
    }
}
