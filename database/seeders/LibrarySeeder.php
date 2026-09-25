<?php

namespace Database\Seeders;

use App\Modules\Library\Application\Book\UseCases\RegisterBook;
use App\Modules\Library\Application\Loan\UseCases\CreateLoan;
use App\Modules\Library\Application\Loan\UseCases\MarkOverdueLoans;
use App\Modules\Library\Application\Member\UseCases\RegisterGroup;
use App\Modules\Library\Application\Member\UseCases\RegisterMember;
use App\Modules\Library\Domain\Member\Member;
use DateTimeImmutable;
use Illuminate\Database\Seeder;

/**
 * Datos de ejemplo: 10 libros, 5 miembros y 3 préstamos (uno vencido).
 */
class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['978607070001', 'CBTA860001', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 4],
            ['978607070002', 'CBTA860002', 'Cien años de soledad', 'Gabriel García Márquez', 3],
            ['978607070003', null, 'La casa de Bernarda Alba', 'Federico García Lorca', 2],
            ['978607070004', 'CBTA860004', 'El llano en llamas', 'Juan Rulfo', 3],
            ['978607070005', 'CBTA860005', 'Pedro Páramo', 'Juan Rulfo', 2],
            ['978607070006', null, 'Bajo la misma estrella', 'John Green', 5],
            ['978607070007', 'CBTA860007', 'El principito', 'Antoine de Saint-Exupéry', 6],
            ['978607070008', 'CBTA860008', 'Matemáticas 3', 'SEP', 8],
            ['978607070009', 'CBTA860009', 'Biología: la vida en la Tierra', 'Audesirk', 4],
            ['978607070010', 'CBTA860010', 'Taller de lectura y redacción', 'SEP', 5],
        ];

        $bookIds = [];

        foreach ($books as [$isbn, $barCode, $titulo, $autor, $total]) {
            $bookIds[] = app(RegisterBook::class)->handle($isbn, $barCode, $titulo, $autor, $total)->id();
        }

        $registerMember = app(RegisterMember::class);

        $alumno1 = $registerMember->handle(Member::ALUMNO, 'Juan Pérez', 'A2024001', '3° B')->id();
        $registerMember->handle(Member::ALUMNO, 'María López', 'A2024002', '3° B');
        $docente = $registerMember->handle(Member::DOCENTE, 'Mtra. Ana Torres', 'D001')->id();
        $registerMember->handle(Member::PERSONAL, 'Sr. Pedro Gómez', 'P001');
        $grupo = app(RegisterGroup::class)->handle('3° B - Grupo de lectura', 'G-3B', '3° B')->id();

        $createLoan = app(CreateLoan::class);

        // Préstamo individual activo.
        $createLoan->handle($alumno1, new DateTimeImmutable('+7 days'), [$bookIds[0]]);

        // Préstamo grupal activo con 3 libros.
        $createLoan->handle($grupo, new DateTimeImmutable('+10 days'), [$bookIds[5], $bookIds[6], $bookIds[7]]);

        // Préstamo vencido de docente.
        $createLoan->handle($docente, new DateTimeImmutable('-4 days'), [$bookIds[1]]);

        app(MarkOverdueLoans::class)->handle();
    }
}
