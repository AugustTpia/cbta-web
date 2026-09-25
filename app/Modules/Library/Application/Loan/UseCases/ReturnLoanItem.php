<?php

namespace App\Modules\Library\Application\Loan\UseCases;

use App\Modules\Library\Domain\Loan\Loan;
use App\Modules\Library\Ports\BookRepositoryInterface;
use App\Modules\Library\Ports\LoanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReturnLoanItem
{
    public function __construct(
        private readonly LoanRepositoryInterface $loans,
        private readonly BookRepositoryInterface $books,
    ) {}

    public function handle(int $loanId, int $bookId): Loan
    {
        return DB::transaction(function () use ($loanId, $bookId) {
            $loan = $this->loans->findById($loanId);

            if ($loan === null) {
                throw new InvalidArgumentException('Préstamo no encontrado.');
            }

            $book = $this->books->findById($bookId);

            if ($book === null) {
                throw new InvalidArgumentException('Libro no encontrado.');
            }

            $loan->markItemReturned($bookId);
            $this->loans->save($loan);

            $book->returnCopy();
            $this->books->save($book);

            return $loan;
        });
    }
}
