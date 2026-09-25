<?php

use App\Models\User;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\BookModel;
use App\Shared\Auth\Role;

beforeEach(function () {
    foreach (['admin' => 'A', 'biblioteca' => 'B', 'servicios_escolares' => 'S'] as $slug => $name) {
        Role::create(['slug' => $slug, 'name' => $name]);
    }

    $this->staff = User::factory()->create();
    $this->staff->assignRole('biblioteca');
});

test('invitado no entra y otro departamento recibe 403', function () {
    $this->get(route('biblioteca.libros.index'))->assertRedirect(route('login'));

    $other = User::factory()->create();
    $other->assignRole('servicios_escolares');

    $this->actingAs($other)->get(route('biblioteca.libros.index'))->assertForbidden();
});

test('registrar libro por ISBN (camino feliz)', function () {
    $response = $this->actingAs($this->staff)->post(route('biblioteca.libros.store'), [
        'isbn' => '978-607-111111',
        'bar_code' => 'CBTA860001',
        'titulo' => 'El Quijote',
        'autor' => 'Cervantes',
        'existencias_totales' => 3,
    ]);

    $response->assertRedirect(route('biblioteca.libros.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('books', ['isbn' => '978607111111', 'existencias_disponibles' => 3]);
    $this->actingAs($this->staff)->get(route('biblioteca.libros.index'))->assertOk()->assertSee('El Quijote');
});

test('ISBN duplicado se rechaza', function () {
    $payload = ['isbn' => '978607222222', 'titulo' => 'A', 'autor' => 'B', 'existencias_totales' => 1];

    $this->actingAs($this->staff)->post(route('biblioteca.libros.store'), $payload)->assertSessionHasNoErrors();
    $this->actingAs($this->staff)->post(route('biblioteca.libros.store'), $payload)->assertSessionHasErrors('isbn');
});

test('editar, dar de baja y reactivar libro', function () {
    $this->actingAs($this->staff)->post(route('biblioteca.libros.store'), [
        'isbn' => '978607333333', 'titulo' => 'Viejo', 'autor' => 'X', 'existencias_totales' => 2,
    ]);

    $id = BookModel::where('isbn', '978607333333')->first()->id;

    $this->actingAs($this->staff)->put(route('biblioteca.libros.update', $id), [
        'titulo' => 'Nuevo', 'autor' => 'X', 'existencias_totales' => 2,
    ])->assertRedirect(route('biblioteca.libros.index'));

    $this->actingAs($this->staff)->delete(route('biblioteca.libros.destroy', $id))->assertRedirect();
    $this->assertDatabaseHas('books', ['id' => $id, 'estado' => 'baja']);

    $this->actingAs($this->staff)->post(route('biblioteca.libros.reactivar', $id))->assertRedirect();
    $this->assertDatabaseHas('books', ['id' => $id, 'estado' => 'activo']);
});

test('buscar por ISBN o código devuelve JSON', function () {
    $this->actingAs($this->staff)->post(route('biblioteca.libros.store'), [
        'isbn' => '978607444444', 'bar_code' => 'SCAN001', 'titulo' => 'T', 'autor' => 'A', 'existencias_totales' => 1,
    ]);

    $this->actingAs($this->staff)->getJson(route('biblioteca.libros.buscar', ['q' => 'SCAN001']))
        ->assertOk()->assertJson(['found' => true, 'book' => ['titulo' => 'T']]);

    $this->actingAs($this->staff)->getJson(route('biblioteca.libros.buscar', ['q' => 'NADA']))
        ->assertOk()->assertJson(['found' => false]);
});

test('registrar alumno, personal y grupo', function () {
    $staff = $this->staff;

    $this->actingAs($staff)->post(route('biblioteca.miembros.store'), [
        'tipo' => 'alumno', 'nombre' => 'Juan', 'identificador' => 'A001', 'grupo_escolar' => '3° B',
    ])->assertRedirect(route('biblioteca.miembros.index'))->assertSessionHasNoErrors();

    $this->actingAs($staff)->post(route('biblioteca.miembros.store'), [
        'tipo' => 'personal', 'nombre' => 'Ana', 'identificador' => 'P001',
    ])->assertSessionHasNoErrors();

    $this->actingAs($staff)->post(route('biblioteca.miembros.store'), [
        'tipo' => 'grupo', 'nombre' => '3° B Lectura', 'identificador' => 'G-3B', 'grupo_escolar' => '3° B',
    ])->assertSessionHasNoErrors();

    // Alumno sin grupo escolar se rechaza.
    $this->actingAs($staff)->post(route('biblioteca.miembros.store'), [
        'tipo' => 'alumno', 'nombre' => 'Sin grupo', 'identificador' => 'A002',
    ])->assertSessionHasErrors('grupo_escolar');

    $this->actingAs($staff)->get(route('biblioteca.miembros.index'))->assertOk()->assertSee('Juan');
});
