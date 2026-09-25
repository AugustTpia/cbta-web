<?php

namespace App\Modules\Library\Infrastructure\Export;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LoanReportExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @param  array<int, string>  $headings
     * @param  array<int, array<int, string>>  $rows
     */
    public function __construct(
        private readonly string $title,
        private readonly array $headings,
        private readonly array $rows,
    ) {}

    public function collection(): Collection
    {
        return new Collection($this->rows);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return mb_substr($this->title, 0, 31);
    }
}
