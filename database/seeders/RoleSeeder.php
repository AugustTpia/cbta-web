<?php

namespace Database\Seeders;

use App\Models\User;
use App\Shared\Auth\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * @var array<string, string> slug => nombre
     */
    public const ROLES = [
        Role::ADMIN => 'Administrador',
        Role::BIBLIOTECA => 'Biblioteca',
        Role::SERVICIOS_ESCOLARES => 'Servicios Escolares',
    ];

    /**
     * @var array<string, array<int, string>> email => roles
     */
    public const DEMO_USERS = [
        'admin@cbta86.edu.mx' => [
            'name' => 'Administrador',
            'roles' => [Role::ADMIN, Role::BIBLIOTECA],
        ],
        'biblioteca@cbta86.edu.mx' => [
            'name' => 'Personal de Biblioteca',
            'roles' => [Role::BIBLIOTECA],
        ],
    ];

    public function run(): void
    {
        foreach (self::ROLES as $slug => $name) {
            Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        foreach (self::DEMO_USERS as $email => $data) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );

            // email_verified_at no es Fillable: se fija explícitamente.
            if (! $user->hasVerifiedEmail()) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            foreach ($data['roles'] as $slug) {
                $user->assignRole($slug);
            }
        }
    }
}
