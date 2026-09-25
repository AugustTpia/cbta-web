<?php

use App\Models\User;
use App\Modules\Library\Domain\Loan\Loan;
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

    $this->bookIds = [];
    foreach (['111' => 'L1', '222' => 'L2', '333' => 'L3', '444' => 'L4', '555' => 'L5', '666' => 'L6'] as $isbn => $titulo) {
        $this->bookIds[] = BookModel::create([
            'isbn' => $isbn, 'titulo' => $titulo, 'autor' => 'A',
            'existencias_totales' => 2, 'existencias_disponibles' => 2, 'estado' => 'activo',
        ])->id;
    }
});

function loanPayload($test, array $bookIds): array
{
    return [
        'member_id' => $test->memberId,
        'fecha_estimada_devolucion' => now()->addWeek()->format('Y-m-d'),
        'book_ids' => $bookIds,
    ];
}

test('formulario de préstamo trae buscador de miembro', function () {
    $this->actingAs($this->staff)->get(route('biblioteca.prestamos.create'))
        ->assertOk()
        ->assertSee('busca por nombre')
        ->assertDontSee('<select', false);
});

test('cada libro pendiente muestra su propio botón Devolver junto al título', function () {
    $ids = array_slice($this->bookIds, 0, 2);
    $this->actingAs($this->staff)->post(route('biblioteca.prestamos.store'), loanPayload($this, $ids));

    $html = $this->actingAs($this->staff)->get(route('biblioteca.dashboard'))->assertOk()->getContent();

    // Un botón por libro pendiente y sin columna separada de devoluciones.
    expect(substr_count($html, '>Devolver</button>'))->toBe(2)
        ->and($html)->not->toContain('>Devolver</th>');
});

test('crear préstamo descuenta existencias (camino feliz)', function () {
    $ids = array_slice($this->bookIds, 0, 2);

    $this->actingAs($this->staff)->post(route('biblioteca.prestamos.store'), loanPayload($this, $ids))
        ->assertRedirect(route('biblioteca.dashboard'))->assertSessionHasNoErrors();

    foreach ($ids as $id) {
        $this->assertDatabaseHas('books', ['id' => $id, 'existencias_disponibles' => 1]);
    }
    $this->assertDatabaseHas('loans', ['member_id' => $this->memberId, 'estado' => 'activo']);
});

test('no se presta libro sin existencias', function () {
    BookModel::where('id', $this->bookIds[0])->update(['existencias_disponibles' => 0]);

    $this->actingAs($this->staff)->post(route('biblioteca.prestamos.store'), loanPayload($this, [$this->bookIds[0]]))
        ->assertSessionHasErrors('book_ids');

    $this->assertDatabaseCount('loans', 0);
});

test('individual con 6 libros se rechaza', function () {
    $this->actingAs($this->staff)->post(route('biblioteca.prestamos.store'), loanPayload($this, $this->bookIds))
        ->assertSessionHasErrors('book_ids');

    $this->assertDatabaseCount('loans', 0);
});

test('devolución parcial y total liberan existencias', function () {
    $ids = array_slice($this->bookIds, 0, 2);
    $this->actingAs($this->staff)->post(route('biblioteca.prestamos.store'), loanPayload($this, $ids));

    $loanId = LoanModel::first()->id;

    $this->actingAs($this->staff)->post(route('biblioteca.prestamos.devolver', $loanId), ['book_id' => $ids[0]])
        ->assertRedirect();
    $this->assertDatabaseHas('loans', ['id' => $loanId, 'estado' => Loan::PARCIAL]);
    $this->assertDatabaseHas('books', ['id' => $ids[0], 'existencias_disponibles' => 2]);

    $this->actingAs($this->staff)->post(route('biblioteca.prestamos.devolver', $loanId), ['book_id' => $ids[1]])
        ->assertRedirect();
    $this->assertDatabaseHas('loans', ['id' => $loanId, 'estado' => Loan::DEVUELTO]);
});

test('vencidos se listan y el job los marca', function () {
    $loan = LoanModel::create([
        'member_id' => $this->memberId,
        'fecha_prestamo' => now()->subWeeks(3),
        'fecha_estimada_devolucion' => now()->subDays(5),
        'estado' => 'activo',
    ]);
    $loan->items()->create(['book_id' => $this->bookIds[0], 'devuelto' => false]);

    $this->actingAs($this->staff)->get(route('biblioteca.dashboard'))->assertOk()->assertSee('Vencidos');

    $this->artisan('biblioteca:marcar-vencidos')->assertSuccessful();

    $this->assertDatabaseHas('loans', ['id' => $loan->id, 'estado' => Loan::VENCIDO]);
});
