<?php

namespace App\Modules\Library\Http\Controllers;

use App\Modules\Library\Application\Loan\UseCases\BuildLoanReport;
use App\Modules\Library\Infrastructure\Export\LoanReportExport;
use App\Modules\Library\Ports\MemberRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controlador delgado: valida filtros, delega al UseCase y responde
 * en HTML, PDF o Excel.
 */
class ReportController
{
    public function index(Request $request, BuildLoanReport $report, MemberRepositoryInterface $members): View
    {
        $type = $request->string('tipo')->toString() ?: BuildLoanReport::ACTIVOS;

        $result = $report->handle($type, [
            'desde' => $request->string('desde')->toString(),
            'hasta' => $request->string('hasta')->toString(),
            'tipo_miembro' => $request->string('tipo_miembro')->toString() ?: null,
            'member_id' => $request->integer('member_id') ?: null,
        ]);

        // La tabla HTML se pagina; las exportaciones llevan todas las filas.
        $page = max(1, $request->integer('page', 1));
        $perPage = 25;
        $paginator = new LengthAwarePaginator(
            array_slice($result['rows'], ($page - 1) * $perPage, $perPage),
            count($result['rows']),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('library.reports.index', [
            'types' => BuildLoanReport::TITLES,
            'filters' => $request->only(['tipo', 'desde', 'hasta', 'tipo_miembro', 'member_id']),
            'result' => $result,
            'pageRows' => $paginator->items(),
            'paginator' => $paginator,
            'members' => $members->all(),
        ]);
    }

    public function export(Request $request, BuildLoanReport $report): BinaryFileResponse|Response
    {
        $request->validate([
            'tipo' => ['required', 'in:'.implode(',', BuildLoanReport::TYPES)],
            'formato' => ['required', 'in:pdf,xlsx'],
        ]);

        $type = $request->string('tipo')->toString();
        $result = $report->handle($type, [
            'desde' => $request->string('desde')->toString(),
            'hasta' => $request->string('hasta')->toString(),
            'tipo_miembro' => $request->string('tipo_miembro')->toString() ?: null,
            'member_id' => $request->integer('member_id') ?: null,
        ]);

        $filename = 'reporte-'.$type.'-'.now()->format('Ymd-His');

        if ($request->string('formato')->toString() === 'pdf') {
            return Pdf::loadView('library.reports.pdf', ['result' => $result])->download($filename.'.pdf');
        }

        return Excel::download(new LoanReportExport($result['title'], $result['headings'], $result['rows']), $filename.'.xlsx');
    }
}
