<?php

namespace App\Modules\Library\Domain\Loan;

use DateTimeImmutable;
use RuntimeException;

/**
 * Renglón de libro dentro de un préstamo.
 */
final class LoanItem
{
    public function __construct(
        private ?int $id,
        private int $bookId,
        private bool $returned = false,
        private ?DateTimeImmutable $returnedAt = null,
    ) {}

    public function markReturned(DateTimeImmutable $at): void
    {
        if ($this->returned) {
            throw new RuntimeException('El libro ya fue devuelto.');
        }

        $this->returned = true;
        $this->returnedAt = $at;
    }

    public function isReturned(): bool
    {
        return $this->returned;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function bookId(): int
    {
        return $this->bookId;
    }

    public function returnedAt(): ?DateTimeImmutable
    {
        return $this->returnedAt;
    }
}
