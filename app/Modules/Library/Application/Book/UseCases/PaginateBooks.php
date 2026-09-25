<?php

namespace App\Modules\Library\Application\Book\UseCases;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Ports\BookRepositoryInterface;

class PaginateBooks
{
    public function __construct(private readonly BookRepositoryInterface $books) {}

    public function handle(int $page = 1, int $perPage = 15, ?string $term = null, ?string $estado = null): PaginatedResult
    {
        return $this->books->paginateBooks($page, $perPage, $term, $estado);
    }
}
