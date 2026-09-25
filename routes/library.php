<?php

use App\Modules\Library\Http\Controllers\BookController;
use App\Modules\Library\Http\Controllers\LoanController;
use App\Modules\Library\Http\Controllers\MemberController;
use App\Modules\Library\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'department:biblioteca'])
    ->prefix('biblioteca')
    ->name('biblioteca.')
    ->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('dashboard');

        Route::get('libros/buscar', [BookController::class, 'search'])->name('libros.buscar');
        Route::get('libros/exportar', [BookController::class, 'export'])->name('libros.exportar');
        Route::post('libros/{libro}/reactivar', [BookController::class, 'restore'])->name('libros.reactivar');
        Route::resource('libros', BookController::class)->except(['show'])->parameters(['libros' => 'libro']);

        Route::resource('miembros', MemberController::class)->only(['index', 'create', 'store']);
        Route::get('miembros/buscar', [MemberController::class, 'search'])->name('miembros.buscar');
        Route::get('miembros/exportar', [MemberController::class, 'export'])->name('miembros.exportar');

        Route::get('prestamos/exportar', [LoanController::class, 'export'])->name('prestamos.exportar');

        Route::resource('prestamos', LoanController::class)->only(['index', 'create', 'store'])->parameters(['prestamos' => 'prestamo']);
        Route::post('prestamos/{prestamo}/devolver', [LoanController::class, 'returnItem'])->name('prestamos.devolver');

        Route::get('reportes', [ReportController::class, 'index'])->name('reportes.index');
        Route::get('reportes/exportar', [ReportController::class, 'export'])->name('reportes.exportar');
    });
