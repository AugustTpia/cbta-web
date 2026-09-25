<?php

use App\Modules\Library\Domain\Book\BarCode;
use App\Modules\Library\Domain\Book\Book;
use App\Modules\Library\Domain\Book\Isbn;
use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Domain\Loan\LoanItem;
use App\Modules\Library\Domain\Member\Member;
use App\Modules\Library\Ports\BookRepositoryInterface;
use App\Modules\Library\Ports\LoanRepositoryInterface;
use App\Modules\Library\Ports\MemberRepositoryInterface;

test('repositorio de libros guarda y busca por isbn o código', function () {
    $repo = app(BookRepositoryInterface::class);

    $book = Book::register(new Isbn('978607111111'), new BarCode('CBTA860100'), 'El Quijote', 'Cervantes', 2);
    $repo->save($book);

    expect($repo->findByIsbnOrBarCode('978607111111')?->titulo())->toBe('El Quijote')
        ->and($repo->findByIsbnOrBarCode('CBTA860100')?->titulo())->toBe('El Quijote')
        ->and($repo->findByIsbnOrBarCode('NOEXISTE'))->toBeNull();
});

test('repositorio de miembros busca por identificador', function () {
    $repo = app(MemberRepositoryInterface::class);

    $repo->save(Member::register(Member::ALUMNO, 'Juan Pérez', 'A001', '3° B'));

    expect($repo->findByIdentificador('A001')?->nombre())->toBe('Juan Pérez');
});

test('repositorio de préstamos persiste items y lista vencidos', function () {
    $books = app(BookRepositoryInterface::class);
    $members = app(MemberRepositoryInterface::class);
    $loans = app(LoanRepositoryInterface::class);

    $book = $books->save(Book::register(new Isbn('978607222222'), null, 'Libro', 'Autor', 5));
    $member = $members->save(Member::register(Member::DOCENTE, 'Mtra. Ana', 'D001'));

    $overdue = Loan::create($member->id(), Member::DOCENTE, new DateTimeImmutable('-2 days'));
    $overdue->addItem(new LoanItem(null, $book->id()));
    $book->loanCopy();
    $books->save($book);
    $loans->save($overdue);

    $found = $loans->findById($overdue->id());
    expect($found?->pendingCount())->toBe(1)
        ->and($found?->isOverdue())->toBeTrue();

    $listed = $loans->listOverdue();
    expect($listed)->toHaveCount(1)
        ->and($listed[0]->overdueDays())->toBe(2);

    expect($loans->listActive())->toHaveCount(0);
    expect($books->findById($book->id())?->availableCopies())->toBe(4);
});
