<?php

use App\Models\User;
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

    for ($i = 1; $i <= 16; $i++) {
        BookModel::create([
            'isbn' => '978609'.str_pad((string) $i, 6, '0', STR_PAD_LEFT),
            'titulo' => 'Libro '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
            'autor' => 'Autor',
            'existencias_totales' => 2, 'existencias_disponibles' => 2,
            'estado' => $i === 16 ? 'baja' : 'activo',
        ]);
    }

    $this->juan = MemberModel::create([
        'tipo' => 'alumno', 'nombre' => 'Juan Pérez', 'identificador' => 'A001', 'grupo_escolar' => '3° B',
    ])->id;
    MemberModel::create(['tipo' => 'docente', 'nombre' => 'Ana Torres', 'identificador' => 'D001', 'grupo_escolar' => null]);
});

test('libros se paginan y filtran', function () {
    $staff = $this->staff;

    $this->actingAs($staff)->get(route('biblioteca.libros.index'))
        ->assertOk()->assertSee('Libro 15')->assertDontSee('Libro 16');

    $this->actingAs($staff)->get(route('biblioteca.libros.index', ['page' => 2]))
        ->assertOk()->assertSee('Libro 16');

    $this->actingAs($staff)->get(route('biblioteca.libros.index', ['q' => 'Libro 01']))
        ->assertOk()->assertSee('Libro 01')->assertDontSee('Libro 02');

    $this->actingAs($staff)->get(route('biblioteca.libros.index', ['estado' => 'baja']))
        ->assertOk()->assertSee('Libro 16')->assertDontSee('Libro 01');
});

test('miembros se filtran por tipo y texto', function () {
    $staff = $this->staff;

    $this->actingAs($staff)->get(route('biblioteca.miembros.index', ['tipo' => 'docente']))
        ->assertOk()->assertSee('Ana Torres')->assertDontSee('Juan Pérez');

    $this->actingAs($staff)->get(route('biblioteca.miembros.index', ['q' => 'A001']))
        ->assertOk()->assertSee('Juan Pérez')->assertDontSee('Ana Torres');
});

test('panel filtra por miembro', function () {
    $bookId = BookModel::first()->id;
    $loan = LoanModel::create([
        'member_id' => $this->juan, 'fecha_prestamo' => now(),
        'fecha_estimada_devolucion' => now()->addDays(5), 'estado' => 'activo',
    ]);
    $loan->items()->create(['book_id' => $bookId, 'devuelto' => false]);

    $this->actingAs($this->staff)->get(route('biblioteca.dashboard', ['miembro' => 'Juan']))
        ->assertOk()->assertSee('Juan Pérez');

    $this->actingAs($this->staff)->get(route('biblioteca.dashboard', ['miembro' => 'Nadie Xyz']))
        ->assertOk()->assertDontSee('Juan Pérez');
});

test('exportaciones de libros, miembros y préstamos responden', function () {
    $staff = $this->staff;

    $xlsx = $this->actingAs($staff)->get(route('biblioteca.libros.exportar', ['formato' => 'xlsx']));
    $xlsx->assertOk();
    expect($xlsx->headers->get('content-type'))->toContain('spreadsheet');

    $pdf = $this->actingAs($staff)->get(route('biblioteca.libros.exportar', ['formato' => 'pdf']));
    $pdf->assertOk();
    expect($pdf->headers->get('content-type'))->toContain('pdf');

    $this->actingAs($staff)->get(route('biblioteca.miembros.exportar', ['tipo' => 'alumno', 'formato' => 'xlsx']))->assertOk();
    $this->actingAs($staff)->get(route('biblioteca.miembros.exportar', ['formato' => 'pdf']))->assertOk();

    $this->actingAs($staff)->get(route('biblioteca.prestamos.exportar', ['formato' => 'xlsx']))->assertOk();

    $loansPdf = $this->actingAs($staff)->get(route('biblioteca.prestamos.exportar', ['formato' => 'pdf']));
    $loansPdf->assertOk();
    expect($loansPdf->headers->get('content-type'))->toContain('pdf');
});
