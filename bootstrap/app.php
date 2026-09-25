<?php

use App\Http\Middleware\EnsureHasDepartment;
use App\Modules\Library\Infrastructure\Console\MarkOverdueLoansCommand;
use App\Modules\Library\Infrastructure\Console\ResetLibraryDataCommand;
use App\Modules\Library\LibraryServiceProvider;
use App\Shared\Auth\Console\CreateUserCommand;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'department' => EnsureHasDepartment::class,
        ]);
    })
    ->withCommands([
        CreateUserCommand::class,
        MarkOverdueLoansCommand::class,
        ResetLibraryDataCommand::class,
    ])
    ->withProviders([
        LibraryServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
