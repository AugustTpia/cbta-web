<?php

namespace App\Modules\Library\Application\Shared;

/**
 * Resultado paginado puro (sin acoplar los puertos al paginador de Laravel).
 */
final class PaginatedResult
{
    /**
     * @param  array<int, mixed>  $items
     */
    public function __construct(
        private readonly array $items,
        private readonly int $total,
        private readonly int $page,
        private readonly int $perPage,
    ) {}

    /**
     * @return array<int, mixed>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / $this->perPage));
    }

    public function hasPages(): bool
    {
        return $this->total > $this->perPage;
    }
}
