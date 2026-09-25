<?php

use App\Models\User;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\MemberModel;
use App\Shared\Auth\Role;

beforeEach(function () {
    foreach (['admin' => 'A', 'biblioteca' => 'B', 'servicios_escolares' => 'S'] as $slug => $name) {
        Role::create(['slug' => $slug, 'name' => $name]);
    }

    $this->staff = User::factory()->create();
    $this->staff->assignRole('biblioteca');

    MemberModel::create(['tipo' => 'alumno', 'nombre' => 'Juan Pérez', 'identificador' => 'A2024001', 'grupo_escolar' => '3° B']);
    MemberModel::create(['tipo' => 'alumno', 'nombre' => 'María López', 'identificador' => 'A2024002', 'grupo_escolar' => '3° B']);
    MemberModel::create(['tipo' => 'docente', 'nombre' => 'Ana Torres', 'identificador' => 'D001', 'grupo_escolar' => null]);
});

test('busca por nombre, matrícula y grupo', function () {
    $staff = $this->staff;

    $this->actingAs($staff)->getJson(route('biblioteca.miembros.buscar', ['q' => 'Juan']))
        ->assertOk()->assertJsonCount(1, 'results')->assertJsonPath('results.0.identificador', 'A2024001');

    $this->actingAs($staff)->getJson(route('biblioteca.miembros.buscar', ['q' => 'A2024002']))
        ->assertOk()->assertJsonPath('results.0.nombre', 'María López');

    // "3° B" muestra a los alumnos de ese grupo y grado.
    $this->actingAs($staff)->getJson(route('biblioteca.miembros.buscar', ['q' => '3° B']))
        ->assertOk()->assertJsonCount(2, 'results');
});

test('término vacío o sin coincidencias devuelve vacío', function () {
    $staff = $this->staff;

    $this->actingAs($staff)->getJson(route('biblioteca.miembros.buscar', ['q' => '']))
        ->assertOk()->assertJson(['results' => []]);

    $this->actingAs($staff)->getJson(route('biblioteca.miembros.buscar', ['q' => 'ZZZ']))
        ->assertOk()->assertJson(['results' => []]);
});

test('invitado no puede buscar', function () {
    $this->getJson(route('biblioteca.miembros.buscar', ['q' => 'Juan']))->assertUnauthorized();
});
