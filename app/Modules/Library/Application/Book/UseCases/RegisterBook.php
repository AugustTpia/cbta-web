<?php

namespace App\Modules\Library\Application\Book\UseCases;

use App\Modules\Library\Domain\Book\BarCode;
use App\Modules\Library\Domain\Book\Book;
use App\Modules\Library\Domain\Book\Isbn;
use App\Modules\Library\Ports\BookRepositoryInterface;
use InvalidArgumentException;

class RegisterBook
{
    public function __construct(private readonly BookRepositoryInterface $books) {}

    public function handle(
        string $isbn,
        ?string $barCode,
        string $titulo,
        string $autor,
        int $totalCopies,
    ): Book {
        if ($this->books->findByIsbn(trim($isbn)) !== null) {
            throw new InvalidArgumentException('Ya existe un libro con ese ISBN.');
        }

        if ($barCode !== null && trim($barCode) !== '' && $this->books->findByBarCode(trim($barCode)) !== null) {
            throw new InvalidArgumentException('Ya existe un libro con ese código de barras.');
        }

        $book = Book::register(
            new Isbn($isbn),
            $barCode !== null && trim($barCode) !== '' ? new BarCode($barCode) : null,
            $titulo,
            $autor,
            $totalCopies,
        );

        return $this->books->save($book);
    }
}
