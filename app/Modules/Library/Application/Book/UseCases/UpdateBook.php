<?php

namespace App\Modules\Library\Application\Book\UseCases;

use App\Modules\Library\Domain\Book\Book;
use App\Modules\Library\Ports\BookRepositoryInterface;
use InvalidArgumentException;

class UpdateBook
{
    public function __construct(private readonly BookRepositoryInterface $books) {}

    public function handle(int $id, string $titulo, string $autor, int $totalCopies): Book
    {
        $book = $this->books->findById($id);

        if ($book === null) {
            throw new InvalidArgumentException('Libro no encontrado.');
        }

        $book->updateDetails($titulo, $autor, $totalCopies);

        return $this->books->save($book);
    }
}
