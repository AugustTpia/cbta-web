<?php

use App\Modules\Library\Domain\Book\BarCode;
use App\Modules\Library\Domain\Book\Book;
use App\Modules\Library\Domain\Book\BookNotAvailableException;
use App\Modules\Library\Domain\Book\Isbn;

function makeBook(int $total = 3): Book
{
    return Book::register(new Isbn('978-607-123456'), new BarCode('CBTA860001'), ' Don Quijote ', ' Cervantes ', $total);
}

test('registrar libro normaliza e iguala disponibles al total', function () {
    $book = makeBook(3);

    expect($book->titulo())->toBe('Don Quijote')
        ->and($book->isbn()->value())->toBe('978607123456')
        ->and($book->availableCopies())->toBe(3)
        ->and($book->isActive())->toBeTrue();
});

test('prestar decrementa y devolver incrementa', function () {
    $book = makeBook(2);

    $book->loanCopy();
    expect($book->availableCopies())->toBe(1);

    $book->returnCopy();
    expect($book->availableCopies())->toBe(2);
});

test('no se puede prestar sin existencias', function () {
    $book = makeBook(1);
    $book->loanCopy();

    expect(fn () => $book->loanCopy())->toThrow(BookNotAvailableException::class);
});

test('disponibles nunca excede el total ni es negativo', function () {
    expect(fn () => Book::register(new Isbn('123'), null, 'T', 'A', -1))->toThrow(InvalidArgumentException::class);

    $book = makeBook(1);
    expect(fn () => $book->returnCopy())->toThrow(BookNotAvailableException::class);
});

test('reducir el total por debajo de lo prestado falla', function () {
    $book = makeBook(3);
    $book->loanCopy();
    $book->loanCopy();

    expect(fn () => $book->updateDetails('T', 'A', 1))->toThrow(InvalidArgumentException::class);
});

test('dar de baja bloquea préstamos', function () {
    $book = makeBook(2);
    $book->deactivate();

    expect($book->isActive())->toBeFalse()
        ->and(fn () => $book->loanCopy())->toThrow(BookNotAvailableException::class);
});
