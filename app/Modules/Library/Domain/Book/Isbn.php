<?php

namespace App\Modules\Library\Domain\Book;

use InvalidArgumentException;

/**
 * ISBN como ValueObject: normaliza y valida formato básico.
 */
final class Isbn
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = str_replace(['-', ' '], '', trim($value));

        if ($normalized === '' || strlen($normalized) > 20) {
            throw new InvalidArgumentException('ISBN inválido.');
        }

        if (! preg_match('/^[0-9Xx]+$/', $normalized)) {
            throw new InvalidArgumentException('ISBN inválido: solo dígitos y X.');
        }

        $this->value = strtoupper($normalized);
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
