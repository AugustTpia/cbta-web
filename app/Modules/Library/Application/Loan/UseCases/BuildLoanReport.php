<?php

namespace App\Modules\Library\Application\Loan\UseCases;

use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Ports\BookRepositoryInterface;
use App\Modules\Library\Ports\LoanRepositoryInterface;
use App\Modules\Library\Ports\MemberRepositoryInterface;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Genera los 6 reportes de control escolar como arreglos simples
 * (el controlador decide HTML, PDF o Excel).
 */
class BuildLoanReport
{
    public const ACTIVOS = 'activos';

    public const VENCIDOS = 'vencidos';

    public const TOTAL_PERIODO = 'total-periodo';

    public const NO_DEVUELTOS = 'no-devueltos';

    public const MAS_PRESTADOS = 'mas-prestados';

    public const HISTORIAL_MIEMBRO = 'historial-miembro';

    public const TYPES = [self::ACTIVOS, self::VENCIDOS, self::TOTAL_PERIODO, self::NO_DEVUELTOS, self::MAS_PRESTADOS, self::HISTORIAL_MIEMBRO];

    public const TITLES = [
        self::ACTIVOS => 'Préstamos activos',
        self::VENCIDOS => 'Préstamos vencidos',
        self::TOTAL_PERIODO => 'Total de libros prestados en el periodo',
        self::NO_DEVUELTOS => 'Libros no devueltos',
        self::MAS_PRESTADOS => 'Libros más prestados',
        self::HISTORIAL_MIEMBRO => 'Historial por miembro',
    ];

    public function __construct(
        private readonly LoanRepositoryInterface $loans,
        private readonly BookRepositoryInterface $books,
        private readonly MemberRepositoryInterface $members,
    ) {}

    /**
     * @param  array<string, mixed>  $filters  desde, hasta, tipo_miembro, member_id
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>, summary: string}
     */
    public function handle(string $type, array $filters = [], ?DateTimeImmutable $now = null): array
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException('Reporte desconocido.');
        }

        $now ??= new DateTimeImmutable;
        $from = isset($filters['desde']) && $filters['desde'] !== '' ? new DateTimeImmutable($filters['desde']) : null;
        $to = isset($filters['hasta']) && $filters['hasta'] !== '' ? new DateTimeImmutable($filters['hasta'].' 23:59:59') : null;

        return match ($type) {
            self::ACTIVOS => $this->activos($from, $to),
            self::VENCIDOS => $this->vencidos($from, $to, $now),
            self::TOTAL_PERIODO => $this->totalPeriodo($from, $to),
            self::NO_DEVUELTOS => $this->noDevueltos($filters['tipo_miembro'] ?? null),
            self::MAS_PRESTADOS => $this->masPrestados($from, $to),
            self::HISTORIAL_MIEMBRO => $this->historialMiembro((int) ($filters['member_id'] ?? 0)),
        };
    }

    /**
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>, summary: string}
     */
    private function activos(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $rows = [];

        foreach ($this->loans->listActive() as $loan) {
            if (! $this->inRange($loan, $from, $to)) {
                continue;
            }

            foreach ($loan->items() as $item) {
                if ($item->isReturned()) {
                    continue;
                }

                $rows[] = $this->loanRow($loan, $item->bookId());
            }
        }

        return $this->result(self::ACTIVOS, ['Libro', 'Quién lo tiene', 'Tipo', 'Préstamo', 'Devolución estimada'], $rows, count($rows).' libros en préstamo activo.');
    }

    /**
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>, summary: string}
     */
    private function vencidos(?DateTimeImmutable $from, ?DateTimeImmutable $to, DateTimeImmutable $now): array
    {
        $rows = [];

        foreach ($this->loans->listOverdue($now) as $loan) {
            if (! $this->inRange($loan, $from, $to)) {
                continue;
            }

            foreach ($loan->items() as $item) {
                if ($item->isReturned()) {
                    continue;
                }

                $rows[] = [...$this->loanRow($loan, $item->bookId()), (string) $loan->overdueDays($now)];
            }
        }

        return $this->result(self::VENCIDOS, ['Libro', 'Quién lo tiene', 'Tipo', 'Préstamo', 'Devolución estimada', 'Días de atraso'], $rows, count($rows).' libros vencidos.');
    }

    /**
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>, summary: string}
     */
    private function totalPeriodo(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $total = 0;

        foreach ($this->loans->listBetween($from, $to) as $loan) {
            $total += count($loan->items());
        }

        return $this->result(self::TOTAL_PERIODO, ['Total de libros prestados'], [[(string) $total]], "Del {$this->fmt($from)} al {$this->fmt($to)}: {$total} libros prestados.");
    }

    /**
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>, summary: string}
     */
    private function noDevueltos(?string $tipoMiembro): array
    {
        $rows = [];

        foreach ($this->loans->listActive() as $loan) {
            $member = $this->members->findById($loan->memberId());

            if ($tipoMiembro && $member?->tipo() !== $tipoMiembro) {
                continue;
            }

            foreach ($loan->items() as $item) {
                if ($item->isReturned()) {
                    continue;
                }

                $rows[] = $this->loanRow($loan, $item->bookId());
            }
        }

        $label = $tipoMiembro ? ' ('.ucfirst($tipoMiembro).')' : '';

        return $this->result(self::NO_DEVUELTOS, ['Libro', 'Quién lo tiene', 'Tipo', 'Préstamo', 'Devolución estimada'], $rows, count($rows).' libros sin devolver'.$label.'.');
    }

    /**
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>, summary: string}
     */
    private function masPrestados(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        /** @var array<int, int> $counts bookId => veces prestado */
        $counts = [];

        foreach ($this->loans->listBetween($from, $to) as $loan) {
            foreach ($loan->items() as $item) {
                $counts[$item->bookId()] = ($counts[$item->bookId()] ?? 0) + 1;
            }
        }

        arsort($counts);
        $rows = [];

        foreach ($counts as $bookId => $times) {
            $book = $this->books->findById($bookId);
            $rows[] = [$book?->titulo() ?? 'Libro eliminado', $book?->autor() ?? '—', (string) $times];
        }

        return $this->result(self::MAS_PRESTADOS, ['Libro', 'Autor', 'Veces prestado'], $rows, count($rows).' títulos prestados en el periodo.');
    }

    /**
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>, summary: string}
     */
    private function historialMiembro(int $memberId): array
    {
        $member = $this->members->findById($memberId);

        if ($member === null) {
            throw new InvalidArgumentException('Miembro no encontrado.');
        }

        $rows = [];

        foreach ($this->loans->listByMember($memberId) as $loan) {
            foreach ($loan->items() as $item) {
                $book = $this->books->findById($item->bookId());
                $rows[] = [
                    $book?->titulo() ?? 'Libro eliminado',
                    $loan->loanDate()->format('d/m/Y'),
                    $loan->dueDate()->format('d/m/Y'),
                    $item->isReturned() ? 'Devuelto '.$item->returnedAt()?->format('d/m/Y') : $loan->status(),
                ];
            }
        }

        return $this->result(self::HISTORIAL_MIEMBRO, ['Libro', 'Préstamo', 'Devolución estimada', 'Estado'], $rows, count($rows).' movimientos de '.$member->nombre().'.');
    }

    /**
     * @return array<int, string>
     */
    private function loanRow(Loan $loan, int $bookId): array
    {
        $book = $this->books->findById($bookId);
        $member = $this->members->findById($loan->memberId());

        return [
            $book?->titulo() ?? 'Libro eliminado',
            $member?->nombre() ?? '—',
            ucfirst($member?->tipo() ?? $loan->memberType()),
            $loan->loanDate()->format('d/m/Y'),
            $loan->dueDate()->format('d/m/Y'),
        ];
    }

    /**
     * @param  array<int, string>  $headings
     * @param  array<int, array<int, string>>  $rows
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>, summary: string}
     */
    private function result(string $type, array $headings, array $rows, string $summary): array
    {
        return ['title' => self::TITLES[$type], 'headings' => $headings, 'rows' => $rows, 'summary' => $summary];
    }

    private function inRange(Loan $loan, ?DateTimeImmutable $from, ?DateTimeImmutable $to): bool
    {
        if ($from !== null && $loan->loanDate() < $from) {
            return false;
        }

        return $to === null || $loan->loanDate() <= $to;
    }

    private function fmt(?DateTimeImmutable $date): string
    {
        return $date?->format('d/m/Y') ?? '—';
    }
}
