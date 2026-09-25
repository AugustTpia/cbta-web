<?php

namespace App\Modules\Library\Http\Controllers;

use App\Modules\Library\Application\Member\UseCases\PaginateMembers;
use App\Modules\Library\Application\Member\UseCases\RegisterGroup;
use App\Modules\Library\Application\Member\UseCases\RegisterMember;
use App\Modules\Library\Application\Member\UseCases\SearchMembers;
use App\Modules\Library\Domain\Member\Member;
use App\Modules\Library\Http\Requests\StoreMemberRequest;
use App\Modules\Library\Infrastructure\Export\LoanReportExport;
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
class MemberController
{
    public function index(Request $request, PaginateMembers $useCase): View
    {
        $filters = [
            'q' => $request->string('q')->toString(),
            'tipo' => $request->string('tipo')->toString(),
        ];

        return view('library.members.index', [
            'paginator' => $useCase->handle(
                $request->integer('page', 1),
                15,
                $filters['q'] !== '' ? $filters['q'] : null,
                $filters['tipo'] !== '' ? $filters['tipo'] : null,
            ),
            'filters' => $filters,
            'tipos' => Member::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('library.members.create', ['tipos' => Member::TYPES]);
    }

    public function store(StoreMemberRequest $request, RegisterMember $registerMember, RegisterGroup $registerGroup): RedirectResponse
    {
        $tipo = $request->string('tipo')->toString();

        if ($tipo === Member::GRUPO) {
            $member = $registerGroup->handle(
                $request->string('nombre')->toString(),
                $request->string('identificador')->toString(),
                $request->string('grupo_escolar')->toString(),
            );
        } else {
            $member = $registerMember->handle(
                $tipo,
                $request->string('nombre')->toString(),
                $request->string('identificador')->toString(),
                $request->filled('grupo_escolar') ? $request->string('grupo_escolar')->toString() : null,
            );
        }

        return redirect()->route('biblioteca.miembros.index')->with('status', 'Miembro "'.$member->nombre().'" registrado.');
    }

    /**
     * Exporta lo filtrado (todas las filas, no solo la página visible).
     */
    public function export(Request $request, PaginateMembers $useCase): BinaryFileResponse|Response
    {
        $request->validate(['formato' => ['required', 'in:pdf,xlsx']]);

        $term = $request->string('q')->toString() ?: null;
        $tipo = $request->string('tipo')->toString() ?: null;
        $members = $useCase->handle(1, 5000, $term, $tipo)->items();

        $rows = [];

        foreach ($members as $member) {
            $rows[] = [
                $member->nombre(),
                ucfirst($member->tipo()),
                $member->identificador(),
                $member->grupoEscolar() ?? '—',
            ];
        }

        $result = [
            'title' => 'Miembros de biblioteca',
            'headings' => ['Nombre', 'Tipo', 'Identificador', 'Grupo escolar'],
            'rows' => $rows,
            'summary' => count($rows).' miembros.',
        ];

        $filename = 'miembros-'.now()->format('Ymd-His');

        if ($request->string('formato')->toString() === 'pdf') {
            return Pdf::loadView('library.reports.pdf', ['result' => $result])->download($filename.'.pdf');
        }

        return Excel::download(new LoanReportExport($result['title'], $result['headings'], $rows), $filename.'.xlsx');
    }

    /**
     * JSON para el buscador de miembro en el formulario de préstamo.
     */
    public function search(Request $request, SearchMembers $useCase): JsonResponse
    {
        $results = [];

        foreach ($useCase->handle($request->string('q')->toString()) as $member) {
            $results[] = [
                'id' => $member->id(),
                'nombre' => $member->nombre(),
                'tipo' => $member->tipo(),
                'identificador' => $member->identificador(),
                'grupo' => $member->grupoEscolar(),
            ];
        }

        return response()->json(['results' => $results]);
    }
}
