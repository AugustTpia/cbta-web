<?php

use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Domain\Loan\LoanItem;
use App\Modules\Library\Domain\Loan\LoanLimitExceededException;
use App\Modules\Library\Domain\Member\Member;

test('préstamo nuevo está activo y no vencido', function () {
    $loan = Loan::create(1, Member::ALUMNO, new DateTimeImmutable('+7 days'));

    expect($loan->status())->toBe(Loan::ACTIVO)
        ->and($loan->isOverdue())->toBeFalse();
});

test('isOverdue es el único punto de verdad del vencimiento', function () {
    $overdue = Loan::create(1, Member::DOCENTE, new DateTimeImmutable('-2 days'));
    $overdue->addItem(new LoanItem(null, 10));

    $future = Loan::create(1, Member::DOCENTE, new DateTimeImmutable('+2 days'));
    $future->addItem(new LoanItem(null, 10));

    expect($overdue->isOverdue())->toBeTrue()
        ->and($future->isOverdue())->toBeFalse()
        ->and($overdue->overdueDays())->toBe(2);
});

test('estado se recalcula: parcial, devuelto y vencido', function () {
    $loan = Loan::create(1, Member::GRUPO, new DateTimeImmutable('+7 days'));
    $loan->addItem(new LoanItem(null, 1));
    $loan->addItem(new LoanItem(null, 2));

    $loan->markItemReturned(1);
    expect($loan->status())->toBe(Loan::PARCIAL);

    $loan->markItemReturned(2);
    expect($loan->status())->toBe(Loan::DEVUELTO)
        ->and($loan->isOverdue())->toBeFalse();
});

test('préstamo vencido con pendientes queda vencido aunque haya devoluciones', function () {
    $loan = Loan::create(1, Member::GRUPO, new DateTimeImmutable('-3 days'));
    $loan->addItem(new LoanItem(null, 1));
    $loan->addItem(new LoanItem(null, 2));

    $loan->markItemReturned(1, new DateTimeImmutable('-2 days'));

    expect($loan->status())->toBe(Loan::VENCIDO)
        ->and($loan->isOverdue())->toBeTrue();
});

test('préstamo individual tiene tope de 5 libros, grupal no', function () {
    $loan = Loan::create(1, Member::ALUMNO, new DateTimeImmutable('+7 days'));

    for ($i = 1; $i <= 5; $i++) {
        $loan->addItem(new LoanItem(null, $i));
    }

    expect(fn () => $loan->addItem(new LoanItem(null, 6)))->toThrow(LoanLimitExceededException::class);

    $group = Loan::create(2, Member::GRUPO, new DateTimeImmutable('+7 days'));

    for ($i = 1; $i <= 10; $i++) {
        $group->addItem(new LoanItem(null, $i));
    }

    expect($group->pendingCount())->toBe(10);
});

test('miembro exige grupo escolar para alumno y grupo', function () {
    expect(fn () => Member::register(Member::ALUMNO, 'Juan', 'A001'))->toThrow(InvalidArgumentException::class);

    $grupo = Member::register(Member::GRUPO, '3° B - Lectura', 'G-3B', '3° B');

    expect($grupo->isGroup())->toBeTrue();

    $personal = Member::register(Member::PERSONAL, 'Ana', 'P001');

    expect($personal->isIndividual())->toBeTrue();
});
