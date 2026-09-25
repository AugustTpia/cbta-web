<?php

namespace App\Shared\Auth\Console;

use App\Models\User;
use App\Shared\Auth\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Alta de usuarios internos (el registro público está desactivado).
 *
 * Interactivo:   php artisan auth:crear-usuario
 * No interactivo: php artisan auth:crear-usuario --name="..." --email="..." --password="..." --roles=biblioteca,admin
 */
class CreateUserCommand extends Command
{
    protected $signature = 'auth:crear-usuario
        {--name= : Nombre completo}
        {--email= : Correo electrónico}
        {--password= : Contraseña (mínimo 8 caracteres)}
        {--roles= : Departamentos separados por coma: biblioteca, servicios_escolares, admin}';

    protected $description = 'Crea un usuario interno y le asigna departamentos';

    public function handle(): int
    {
        $slugs = Role::pluck('name', 'slug');

        if ($slugs->isEmpty()) {
            $this->fail('No hay departamentos. Corre primero: php artisan db:seed --class=Database\\Seeders\\RoleSeeder');
        }

        $name = $this->option('name') ?? text('Nombre completo', required: true);
        $email = $this->option('email') ?? text('Correo electrónico', required: true);
        $plainPassword = $this->option('password') ?? password('Contraseña (mínimo 8 caracteres)', required: true);
        $roles = $this->option('roles') !== null
            ? explode(',', $this->option('roles'))
            : multiselect('Departamentos', $slugs->all(), required: true);

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $plainPassword, 'roles' => $roles],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
                'password' => ['required', 'string', 'min:8'],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => [Rule::in($slugs->keys()->all())],
            ],
            [],
            ['roles.*' => 'departamento']
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        /** @var User $user */
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($plainPassword),
        ]);

        // email_verified_at no es Fillable: se fija explícitamente.
        $user->forceFill(['email_verified_at' => now()])->save();

        foreach ($roles as $slug) {
            $user->assignRole(trim($slug));
        }

        $this->info("Usuario {$email} creado con departamentos: ".implode(', ', $roles));

        return self::SUCCESS;
    }
}
