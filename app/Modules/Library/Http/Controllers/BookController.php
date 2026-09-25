<?php

namespace App\Modules\Library\Http\Controllers;

use App\Modules\Library\Application\Book\UseCases\DeactivateBook;
use App\Modules\Library\Application\Book\UseCases\FindBookByIsbnOrBarCode;
use App\Modules\Library\Application\Book\UseCases\PaginateBooks;
use App\Modules\Library\Application\Book\UseCases\ReactivateBook;
use App\Modules\Library\Application\Book\UseCases\RegisterBook;
use App\Modules\Library\Application\Book\UseCases\UpdateBook;
use App\Modules\Library\Http\Requests\StoreBookRequest;
use App\Modules\Library\Http\Requests\UpdateBookRequest;
use App\Modules\Library\Infrastructure\Export\LoanReportExport;
use App\Modules\Library\Ports\BookRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controlador delgado: valida, delega al UseCase y responde.
 */
class BookController
{
    public function index(Request $request, PaginateBooks $useCase): View
    {
        $filters = [
            'q' => $request->string('q')->toString(),
            'estado' => $request->string('estado')->toString(),
        ];

        return view('library.books.index', [
            'paginator' => $useCase->handle(
                $request->integer('page', 1),
                15,
                $filters['q'] !== '' ? $filters['q'] : null,
                $filters['estado'] !== '' ? $filters['estado'] : null,
            ),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('library.books.create');
    }

    public function store(StoreBookRequest $request, RegisterBook $useCase): RedirectResponse
    {
        $book = $useCase->handle(
            $request->string('isbn')->toString(),
            $request->filled('bar_code') ? $request->string('bar_code')->toString() : null,
            $request->string('titulo')->toString(),
            $request->string('autor')->toString(),
            $request->integer('existencias_totales'),
        );

        return redirect()->route('biblioteca.libros.index')->with('status', 'Libro "'.$book->titulo().'" registrado.');
    }

    public function edit(int $libro, BookRepositoryInterface $books): View
    {
        $book = $books->findById($libro);

        abort_if($book === null, 404);

        return view('library.books.edit', ['book' => $book]);
    }

    public function update(UpdateBookRequest $request, int $libro, UpdateBook $useCase): RedirectResponse
    {
        try {
            $useCase->handle(
                $libro,
                $request->string('titulo')->toString(),
                $request->string('autor')->toString(),
                $request->integer('existencias_totales'),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['existencias_totales' => $e->getMessage()])->withInput();
        }

        return redirect()->route('biblioteca.libros.index')->with('status', 'Libro actualizado.');
    }

    public function destroy(int $libro, DeactivateBook $useCase): RedirectResponse
    {
        $useCase->handle($libro);

        return redirect()->route('biblioteca.libros.index')->with('status', 'Libro dado de baja.');
    }

    public function restore(int $libro, ReactivateBook $useCase): RedirectResponse
    {
        $useCase->handle($libro);

        return redirect()->route('biblioteca.libros.index')->with('status', 'Libro reactivado.');
    }

    /**
     * Exporta lo filtrado (todas las filas, no solo la página visible).
     */
    public function export(Request $request, PaginateBooks $useCase): BinaryFileResponse|Response
    {
        $request->validate(['formato' => ['required', 'in:pdf,xlsx']]);

        $term = $request->string('q')->toString() ?: null;
        $estado = $request->string('estado')->toString() ?: null;
        $books = $useCase->handle(1, 5000, $term, $estado)->items();

        $rows = [];

        foreach ($books as $book) {
            $rows[] = [
                $book->titulo(),
                $book->autor(),
                $book->isbn()->value(),
                $book->barCode()?->value() ?? '—',
                $book->availableCopies().' / '.$book->totalCopies(),
                $book->isActive() ? 'Activo' : 'Baja',
            ];
        }

        $result = [
            'title' => 'Catálogo de libros',
            'headings' => ['Título', 'Autor', 'ISBN', 'Código', 'Disponibles', 'Estado'],
            'rows' => $rows,
            'summary' => count($rows).' libros.',
        ];

        $filename = 'libros-'.now()->format('Ymd-His');

        if ($request->string('formato')->toString() === 'pdf') {
            return Pdf::loadView('library.reports.pdf', ['result' => $result])->download($filename.'.pdf');
        }

        return Excel::download(new LoanReportExport($result['title'], $result['headings'], $rows), $filename.'.xlsx');
    }

    /**
     * JSON para el input del escáner en el formulario de préstamo.
     */
    public function search(Request $request, FindBookByIsbnOrBarCode $useCase): JsonResponse
    {
        $book = $useCase->handle($request->string('q')->toString());

        if ($book === null) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'book' => [
                'id' => $book->id(),
                'titulo' => $book->titulo(),
                'autor' => $book->autor(),
                'disponibles' => $book->availableCopies(),
                'activo' => $book->isActive(),
            ],
        ]);
    }
}
