<?php

namespace App\Modules\Library\Domain\Book;

use InvalidArgumentException;

/**
 * Entidad Book. Pura: sin Eloquent, sin facades.
 *
 * Regla: 0 <= existencias_disponibles <= existencias_totales.
 */
final class Book
{
    public const ACTIVE = 'activo';

    public const WITHDRAWN = 'baja';

    private function __construct(
        private ?int $id,
        private Isbn $isbn,
        private ?BarCode $barCode,
        private string $titulo,
        private string $autor,
        private int $totalCopies,
        private int $availableCopies,
        private string $status,
    ) {
        $this->guardInvariants();
    }

    public static function register(
        Isbn $isbn,
        ?BarCode $barCode,
        string $titulo,
        string $autor,
        int $totalCopies,
    ): self {
        return new self(null, $isbn, $barCode, trim($titulo), trim($autor), $totalCopies, $totalCopies, self::ACTIVE);
    }

    /**
     * Reconstruye desde persistencia (los datos ya pasaron validación al guardarse).
     */
    public static function reconstitute(
        ?int $id,
        Isbn $isbn,
        ?BarCode $barCode,
        string $titulo,
        string $autor,
        int $totalCopies,
        int $availableCopies,
        string $status,
    ): self {
        return new self($id, $isbn, $barCode, $titulo, $autor, $totalCopies, $availableCopies, $status);
    }

    public function loanCopy(): void
    {
        if ($this->status !== self::ACTIVE) {
            throw new BookNotAvailableException('El libro está dado de baja.');
        }

        if ($this->availableCopies <= 0) {
            throw new BookNotAvailableException('Sin existencias disponibles.');
        }

        $this->availableCopies--;
    }

    public function returnCopy(): void
    {
        if ($this->availableCopies >= $this->totalCopies) {
            throw new BookNotAvailableException('Inconsistencia: no hay copias prestadas por devolver.');
        }

        $this->availableCopies++;
    }

    public function updateDetails(string $titulo, string $autor, int $totalCopies): void
    {
        $loaned = $this->totalCopies - $this->availableCopies;

        if ($totalCopies < $loaned) {
            throw new InvalidArgumentException('El total no puede ser menor que las copias prestadas ('.$loaned.').');
        }

        $this->titulo = trim($titulo);
        $this->autor = trim($autor);
        $this->totalCopies = $totalCopies;
        $this->availableCopies = $totalCopies - $loaned;
    }

    public function assignBarCode(BarCode $barCode): void
    {
        $this->barCode = $barCode;
    }

    public function deactivate(): void
    {
        $this->status = self::WITHDRAWN;
    }

    public function reactivate(): void
    {
        $this->status = self::ACTIVE;
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function isbn(): Isbn
    {
        return $this->isbn;
    }

    public function barCode(): ?BarCode
    {
        return $this->barCode;
    }

    public function titulo(): string
    {
        return $this->titulo;
    }

    public function autor(): string
    {
        return $this->autor;
    }

    public function totalCopies(): int
    {
        return $this->totalCopies;
    }

    public function availableCopies(): int
    {
        return $this->availableCopies;
    }

    public function status(): string
    {
        return $this->status;
    }

    private function guardInvariants(): void
    {
        if ($this->titulo === '' || $this->autor === '') {
            throw new InvalidArgumentException('Título y autor son obligatorios.');
        }

        if ($this->totalCopies < 0 || $this->availableCopies < 0) {
            throw new InvalidArgumentException('Las existencias no pueden ser negativas.');
        }

        if ($this->availableCopies > $this->totalCopies) {
            throw new InvalidArgumentException('Disponibles no puede exceder el total.');
        }

        if (! in_array($this->status, [self::ACTIVE, self::WITHDRAWN], true)) {
            throw new InvalidArgumentException('Estado de libro inválido.');
        }
    }
}
