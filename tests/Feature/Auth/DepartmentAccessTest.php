<?php

use App\Models\User;
use App\Shared\Auth\Role;

beforeEach(function () {
    foreach (['admin' => 'Administrador', 'biblioteca' => 'Biblioteca', 'servicios_escolares' => 'Servicios Escolares'] as $slug => $name) {
        Role::create(['slug' => $slug, 'name' => $name]);
    }
});

test('el registro público está desactivado', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register')->assertNotFound();
});

test('personal de biblioteca entra a /biblioteca', function () {
    $user = User::factory()->create();
    $user->assignRole('biblioteca');

    $this->actingAs($user)->get(route('biblioteca.dashboard'))->assertOk()->assertSee('Préstamos');
    $this->actingAs($user)->get(route('biblioteca.libros.index'))->assertOk();
});

test('invitado es redirigido al login en /biblioteca', function () {
    $this->get(route('biblioteca.dashboard'))->assertRedirect(route('login'));
});

test('otro departamento recibe 403 en /biblioteca', function () {
    $user = User::factory()->create();
    $user->assignRole('servicios_escolares');

    $this->actingAs($user)->get(route('biblioteca.dashboard'))->assertForbidden();
});

test('admin entra a /biblioteca aunque no tenga ese rol', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)->get(route('biblioteca.libros.index'))->assertOk();
});

test('login con un solo departamento redirige a su home', function () {
    $user = User::factory()->create();
    $user->assignRole('biblioteca');

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('biblioteca.dashboard', absolute: false));
});

test('login como admin redirige al hub aunque tenga un solo departamento', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $user->assignRole('biblioteca');

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('hub', absolute: false));
});

test('el hub del admin muestra Administración y el del personal no', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $staff = User::factory()->create();
    $staff->assignRole('biblioteca');
    $staff->assignRole('servicios_escolares');

    $this->actingAs($admin)->get(route('hub'))->assertOk()->assertSee('Administración');
    $this->actingAs($staff)->get(route('hub'))->assertOk()->assertDontSee('Administración');
});

test('login con varios departamentos redirige al hub', function () {
    $user = User::factory()->create();
    $user->assignRole('biblioteca');
    $user->assignRole('servicios_escolares');

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('hub', absolute: false));
});
