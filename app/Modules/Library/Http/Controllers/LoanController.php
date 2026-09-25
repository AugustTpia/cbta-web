<?php

namespace App\Modules\Library\Http\Controllers;

use App\Modules\Library\Application\Loan\UseCases\CreateLoan;
use App\Modules\Library\Application\Loan\UseCases\PaginateActiveLoans;
use App\Modules\Library\Application\Loan\UseCases\PaginateOverdueLoans;
use App\Modules\Library\Application\Loan\UseCases\ReturnLoanItem;
use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Http\Requests\StoreLoanRequest;
use App\Modules\Library\Infrastructure\Export\LoansExport;
use App\Modules\Library\Ports\BookRepositoryInterface;
use App\Modules\Library\Ports\MemberRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTimeImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controlador delgado: valida, delega al UseCase y responde.
 */
class LoanController
{
    public function index(
        Request $request,
        PaginateActiveLoans $active,
        PaginateOverdueLoans $overdue,
        MemberRepositoryInterface $members,
        BookRepositoryInterface $books,
    ): View {
        $memberTerm = $request->string('miembro')->toString() ?: null;

        $activePage = $active->handle($request->integer('pagA', 1), 10, $memberTerm);
        $overduePage = $overdue->handle($request->integer('pagV', 1), 10, $memberTerm);

        return view('library.loans.index', [
            'active' => $this->present($activePage->items(), $members, $books),
            'activePaginator' => $activePage,
            'overdue' => $this->present($overduePage->items(), $members, $books),
            'overduePaginator' => $overduePage,
            'memberTerm' => $request->string('miembro')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('library.loans.create');
    }

    public function store(StoreLoanRequest $request, CreateLoan $useCase): RedirectResponse
    {
        try {
            $loan = $useCase->handle(
                $request->integer('member_id'),
                new DateTimeImmutable($request->string('fecha_estimada_devolucion')->toString()),
                array_map('intval', $request->array('book_ids')),
            );
        } catch (RuntimeException|\InvalidArgumentException $e) {
            return back()->withErrors(['book_ids' => $e->getMessage()])->withInput();
        }

        return redirect()->route('biblioteca.dashboard')->with('status', 'Préstamo #'.$loan->id().' registrado.');
    }

    public function returnItem(int $prestamo, ReturnLoanItem $useCase, Request $request): RedirectResponse
    {
        try {
            $useCase->handle($prestamo, $request->integer('book_id'));
        } catch (RuntimeException|\InvalidArgumentException $e) {
            return back()->withErrors(['devolucion' => $e->getMessage()]);
        }

        return redirect()->route('biblioteca.dashboard')->with('status', 'Devolución registrada.');
    }

    /**
     * Exporta lo filtrado en un solo archivo con 2 hojas/tablas
     * (activos + vencidos).
     */
    public function export(
        Request $request,
        PaginateActiveLoans $active,
        PaginateOverdueLoans $overdue,
        MemberRepositoryInterface $members,
        BookRepositoryInterface $books,
    ): BinaryFileResponse|Response {
        $request->validate(['formato' => ['required', 'in:pdf,xlsx']]);

        $term = $request->string('miembro')->toString() ?: null;

        $activeRows = $this->exportRows(
            $this->present($active->handle(1, 5000, $term)->items(), $members, $books), false
        );
        $overdueRows = $this->exportRows(
            $this->present($overdue->handle(1, 5000, $term)->items(), $members, $books), true
        );

        $filename = 'prestamos-'.now()->format('Ymd-His');

        if ($request->string('formato')->toString() === 'pdf') {
            return Pdf::loadView('library.loans.pdf', [
                'active' => $activeRows,
                'overdue' => $overdueRows,
            ])->download($filename.'.pdf');
        }

        return Excel::download(new LoansExport($activeRows, $overdueRows), $filename.'.xlsx');
    }

    /**
     * @param  array<int, array<string, mixed>>  $presented
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, string>>}
     */
    private function exportRows(array $presented, bool $withOverdue): array
    {
        $headings = ['#', 'Quién lo tiene', 'Tipo', 'Libros', 'Préstamo', 'Devolución'];
        $rows = [];

        if ($withOverdue) {
            $headings[] = 'Días de atraso';
        }

        foreach ($presented as $loan) {
            $titles = [];

            foreach ($loan['items'] as $item) {
                if (! $item['devuelto']) {
                    $titles[] = $item['titulo'];
                }
            }

            $row = [
                (string) $loan['id'],
                $loan['miembro'],
                ucfirst($loan['tipo']),
                implode(', ', $titles),
                $loan['prestamo'],
                $loan['devolucion'],
            ];

            if ($withOverdue) {
                $row[] = (string) $loan['atraso'];
            }

            $rows[] = $row;
        }

        return [
            'title' => $withOverdue ? 'Préstamos vencidos' : 'Préstamos activos',
            'headings' => $headings,
            'rows' => $rows,
        ];
    }

    /**
     * Enriquece préstamos de dominio con nombres para la vista (solo lectura).
     *
     * @param  array<int, Loan>  $loans
     * @return array<int, array<string, mixed>>
     */
    private function present(array $loans, MemberRepositoryInterface $members, BookRepositoryInterface $books): array
    {
        $rows = [];

        foreach ($loans as $loan) {
            $member = $members->findById($loan->memberId());
            $items = [];

            foreach ($loan->items() as $item) {
                $book = $books->findById($item->bookId());
                $items[] = [
                    'book_id' => $item->bookId(),
                    'titulo' => $book?->titulo() ?? 'Libro eliminado',
                    'devuelto' => $item->isReturned(),
                    'fecha_devolucion' => $item->returnedAt()?->format('d/m/Y'),
                ];
            }

            $rows[] = [
                'id' => $loan->id(),
                'miembro' => $member?->nombre() ?? '—',
                'tipo' => $member?->tipo() ?? $loan->memberType(),
                'grupo' => $member?->grupoEscolar(),
                'prestamo' => $loan->loanDate()->format('d/m/Y'),
                'devolucion' => $loan->dueDate()->format('d/m/Y'),
                'atraso' => $loan->overdueDays(),
                'items' => $items,
            ];
        }

        return $rows;
    }
}
