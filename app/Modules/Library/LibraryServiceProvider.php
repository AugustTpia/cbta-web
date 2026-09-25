<?php

namespace App\Modules\Library;

use App\Modules\Library\Infrastructure\Notification\LogOverdueNotifier;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\EloquentBookRepository;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\EloquentLoanRepository;
use App\Modules\Library\Infrastructure\Persistence\Eloquent\EloquentMemberRepository;
use App\Modules\Library\Ports\BookRepositoryInterface;
use App\Modules\Library\Ports\LoanRepositoryInterface;
use App\Modules\Library\Ports\MemberRepositoryInterface;
use App\Modules\Library\Ports\NotifierInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Los UseCases dependen de los Ports; este provider conecta cada
 * interfaz con su implementación Eloquent.
 */
class LibraryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookRepositoryInterface::class, EloquentBookRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, EloquentMemberRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, EloquentLoanRepository::class);
        $this->app->bind(NotifierInterface::class, LogOverdueNotifier::class);
    }
}
