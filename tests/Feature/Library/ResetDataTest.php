<?php

use App\Models\User;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\BookModel;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\LoanModel;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\MemberModel;
use App\Shared\Auth\Role;

test('datos-cero vacía biblioteca y conserva usuarios', function () {
    Role::create(['slug' => 'biblioteca', 'name' => 'Biblioteca']);
    $user = User::factory()->create();
    $user->assignRole('biblioteca');

    $member = MemberModel::create(['tipo' => 'alumno', 'nombre' => 'Juan', 'identificador' => 'A1', 'grupo_escolar' => '3° B']);
    $book = BookModel::create([
        'isbn' => '978607000001', 'titulo' => 'T', 'autor' => 'A',
        'existencias_totales' => 1, 'existencias_disponibles' => 1, 'estado' => 'activo',
    ]);
    $loan = LoanModel::create([
        'member_id' => $member->id, 'fecha_prestamo' => now(),
        'fecha_estimada_devolucion' => now()->addDays(5), 'estado' => 'activo',
    ]);
    $loan->items()->create(['book_id' => $book->id, 'devuelto' => false]);

    $this->artisan('biblioteca:datos-cero', ['--force' => true])->assertSuccessful();

    expect(BookModel::count())->toBe(0)
        ->and(MemberModel::count())->toBe(0)
        ->and(LoanModel::count())->toBe(0)
        ->and(User::count())->toBe(1)
        ->and(Role::count())->toBe(1);
});
