<?php

namespace App\Modules\Library\Infrastructure\Export;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LoansExport implements Export, WithMultipleSheets
{
    /**
     * @param  array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>}  $active
     * @param  array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>}  $overdue
     */
    public function __construct(
        private readonly array $active,
        private readonly array $overdue,
    ) {}

    /**
     * @return array<int, LoanReportExport>
     */
    public function sheets(): array
    {
        return [
            new LoanReportExport($this->active['title'], $this->active['headings'], $this->active['rows']),
            new LoanReportExport($this->overdue['title'], $this->overdue['headings'], $this->overdue['rows']),
        ];
    }
}
