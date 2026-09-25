<?php

namespace App\Modules\Library\Infrastructure\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Vacía los datos operativos de biblioteca (libros, miembros, préstamos)
 * conservando usuarios, roles y asignaciones. Útil para arrancar de cero
 * un nuevo ciclo sin reinstalar nada.
 */
class ResetLibraryDataCommand extends Command
{
    protected $signature = 'biblioteca:datos-cero {--force : Omitir la confirmación}';

    protected $description = 'Borra libros, miembros y préstamos conservando usuarios y roles';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('¿Borrar TODOS los libros, miembros y préstamos? Los usuarios se conservan.')) {
            $this->line('Cancelado.');

            return self::SUCCESS;
        }

        DB::transaction(function () {
            DB::table('loan_items')->delete();
            DB::table('loans')->delete();
            DB::table('members')->delete();
            DB::table('books')->delete();
        });

        $this->info('Biblioteca en cero: libros, miembros y préstamos eliminados. Usuarios intactos.');

        return self::SUCCESS;
    }
}
