<?php

namespace App\Modules\Library\Application\Member\UseCases;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Ports\MemberRepositoryInterface;

class PaginateMembers
{
    public function __construct(private readonly MemberRepositoryInterface $members) {}

    public function handle(int $page = 1, int $perPage = 15, ?string $term = null, ?string $tipo = null): PaginatedResult
    {
        return $this->members->paginateMembers($page, $perPage, $term, $tipo);
    }
}
