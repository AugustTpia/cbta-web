<?php

namespace App\Modules\Library\Application\Loan\UseCases;

use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Domain\Loan\LoanItem;
use App\Modules\Library\Ports\BookRepositoryInterface;
use App\Modules\Library\Ports\LoanRepositoryInterface;
use App\Modules\Library\Ports\MemberRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateLoan
{
    public function __construct(
        private readonly LoanRepositoryInterface $loans,
        private readonly BookRepositoryInterface $books,
        private readonly MemberRepositoryInterface $members,
    ) {}

    /**
     * @param  array<int, int>  $bookIds  uno o varios (grupal)
     */
    public function handle(int $memberId, DateTimeImmutable $dueDate, array $bookIds): Loan
    {
        $member = $this->members->findById($memberId);

        if ($member === null) {
            throw new InvalidArgumentException('Miembro no encontrado.');
        }

        if ($bookIds === []) {
            throw new InvalidArgumentException('El préstamo debe incluir al menos un libro.');
        }

        return DB::transaction(function () use ($member, $dueDate, $bookIds) {
            $loan = Loan::create($member->id(), $member->tipo(), $dueDate);

            foreach (array_unique($bookIds) as $bookId) {
                $book = $this->books->findById((int) $bookId);

                if ($book === null) {
                    throw new InvalidArgumentException('Libro no encontrado.');
                }

                $book->loanCopy();
                $this->books->save($book);

                $loan->addItem(new LoanItem(null, $book->id()));
            }

            return $this->loans->save($loan);
        });
    }
}
