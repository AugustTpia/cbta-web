<?php

use App\Models\User;
use App\Modules\Library\Application\Loan\UseCases\BuildLoanReport;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\BookModel;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\LoanModel;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\MemberModel;
use App\Shared\Auth\Role;

beforeEach(function () {
    foreach (['admin' => 'A', 'biblioteca' => 'B', 'servicios_escolares' => 'S'] as $slug => $name) {
        Role::create(['slug' => $slug, 'name' => $name]);
    }

    $this->staff = User::factory()->create();
    $this->staff->assignRole('biblioteca');

    $this->memberId = MemberModel::create([
        'tipo' => 'alumno', 'nombre' => 'Juan', 'identificador' => 'A001', 'grupo_escolar' => '3° B',
    ])->id;

    $this->bookA = BookModel::create([
        'isbn' => '978607000001', 'titulo' => 'Libro A', 'autor' => 'X',
        'existencias_totales' => 5, 'existencias_disponibles' => 3, 'estado' => 'activo',
    ])->id;

    $this->bookB = BookModel::create([
        'isbn' => '978607000002', 'titulo' => 'Libro B', 'autor' => 'Y',
        'existencias_totales' => 5, 'existencias_disponibles' => 4, 'estado' => 'activo',
    ])->id;

    // Préstamo activo con 2 libros (A prestado 2 veces en total).
    $loan1 = LoanModel::create([
        'member_id' => $this->memberId, 'fecha_prestamo' => now()->subDays(2),
        'fecha_estimada_devolucion' => now()->addDays(5), 'estado' => 'activo',
    ]);
    $loan1->items()->create(['book_id' => $this->bookA, 'devuelto' => false]);
    $loan1->items()->create(['book_id' => $this->bookB, 'devuelto' => false]);

    $loan2 = LoanModel::create([
        'member_id' => $this->memberId, 'fecha_prestamo' => now()->subDays(10),
        'fecha_estimada_devolucion' => now()->addDays(1), 'estado' => 'devuelto',
    ]);
    $loan2->items()->create(['book_id' => $this->bookA, 'devuelto' => true, 'fecha_devolucion_real' => now()->subDays(9)]);
});

test('reporte de activos y total del periodo', function () {
    $report = app(BuildLoanReport::class);

    $activos = $report->handle(BuildLoanReport::ACTIVOS);
    expect($activos['rows'])->toHaveCount(2);

    $total = $report->handle(BuildLoanReport::TOTAL_PERIODO, [
        'desde' => now()->subMonth()->format('Y-m-d'), 'hasta' => now()->format('Y-m-d'),
    ]);
    expect($total['rows'][0][0])->toBe('3');
});

test('ranking de más prestados ordena de mayor a menor', function () {
    $report = app(BuildLoanReport::class);

    $ranking = $report->handle(BuildLoanReport::MAS_PRESTADOS);

    expect($ranking['rows'][0][0])->toBe('Libro A')
        ->and($ranking['rows'][0][2])->toBe('2')
        ->and($ranking['rows'][1][0])->toBe('Libro B');
});

test('historial por miembro y no devueltos filtrados', function () {
    $report = app(BuildLoanReport::class);

    $historial = $report->handle(BuildLoanReport::HISTORIAL_MIEMBRO, ['member_id' => $this->memberId]);
    expect($historial['rows'])->toHaveCount(3);

    $noDevueltos = $report->handle(BuildLoanReport::NO_DEVUELTOS, ['tipo_miembro' => 'alumno']);
    expect($noDevueltos['rows'])->toHaveCount(2);

    $otros = $report->handle(BuildLoanReport::NO_DEVUELTOS, ['tipo_miembro' => 'docente']);
    expect($otros['rows'])->toHaveCount(0);
});

test('vista de reportes y exportaciones responden', function () {
    $this->actingAs($this->staff)->get(route('biblioteca.reportes.index', ['tipo' => 'mas-prestados']))
        ->assertOk()->assertSee('Libro A');

    $this->actingAs($this->staff)->get(route('biblioteca.reportes.exportar', ['tipo' => 'activos', 'formato' => 'xlsx']))
        ->assertOk();

    $pdf = $this->actingAs($this->staff)->get(route('biblioteca.reportes.exportar', ['tipo' => 'activos', 'formato' => 'pdf']));
    $pdf->assertOk();
    expect($pdf->headers->get('content-type'))->toContain('pdf');
});
