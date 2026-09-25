<?php

namespace App\Modules\Library\Ports;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Domain\Book\Book;

interface BookRepositoryInterface
{
    public function findById(int $id): ?Book;

    public function findByIsbn(string $isbn): ?Book;

    public function findByBarCode(string $barCode): ?Book;

    /**
     * Busca por ISBN o código de barras (input del escáner).
     */
    public function findByIsbnOrBarCode(string $code): ?Book;

    public function save(Book $book): Book;

    /**
     * @return array<int, Book> ordenados por título
     */
    public function all(): array;

    public function paginateBooks(int $page, int $perPage, ?string $term = null, ?string $estado = null): PaginatedResult;
}
