<?php

namespace App\Modules\Library\Domain\Book;

use InvalidArgumentException;

/**
 * Código de barras como ValueObject. Puede ser null en Book
 * cuando el libro aún no tiene código impreso.
 */
final class BarCode
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);

        if ($normalized === '' || strlen($normalized) > 40) {
            throw new InvalidArgumentException('Código de barras inválido.');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
