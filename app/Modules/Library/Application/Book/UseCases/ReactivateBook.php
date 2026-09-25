<?php

namespace App\Modules\Library\Application\Book\UseCases;

use App\Modules\Library\Domain\Book\Book;
use App\Modules\Library\Ports\BookRepositoryInterface;

class ReactivateBook
{
    public function __construct(private readonly BookRepositoryInterface $books) {}

    public function handle(int $id): Book
    {
        $book = $this->books->findById($id);

        if ($book === null) {
            throw new \InvalidArgumentException('Libro no encontrado.');
        }

        $book->reactivate();

        return $this->books->save($book);
    }
}
