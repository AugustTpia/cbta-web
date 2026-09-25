<?php

namespace App\Modules\Library\Infrastructure\Persistence\Eloquent;

use App\Modules\Library\Application\Shared\PaginatedResult;
use App\Modules\Library\Domain\Book\BarCode;
use App\Modules\Library\Domain\Book\Book;
use App\Modules\Library\Domain\Book\Isbn;
use App\Modules\Library\Ports\BookRepositoryInterface;

class EloquentBookRepository implements BookRepositoryInterface
{
    public function findById(int $id): ?Book
    {
        $model = BookModel::find($id);

        return $model === null ? null : $this->toDomain($model);
    }

    public function findByIsbn(string $isbn): ?Book
    {
        $model = BookModel::where('isbn', $isbn)->first();

        return $model === null ? null : $this->toDomain($model);
    }

    public function findByBarCode(string $barCode): ?Book
    {
        $model = BookModel::where('bar_code', $barCode)->first();

        return $model === null ? null : $this->toDomain($model);
    }

    public function findByIsbnOrBarCode(string $code): ?Book
    {
        $code = trim($code);

        $model = BookModel::where('isbn', $code)->orWhere('bar_code', $code)->first();

        return $model === null ? null : $this->toDomain($model);
    }

    public function save(Book $book): Book
    {
        $model = $book->id() !== null ? BookModel::findOrFail($book->id()) : new BookModel;

        $model->fill([
            'isbn' => $book->isbn()->value(),
            'bar_code' => $book->barCode()?->value(),
            'titulo' => $book->titulo(),
            'autor' => $book->autor(),
            'existencias_totales' => $book->totalCopies(),
            'existencias_disponibles' => $book->availableCopies(),
            'estado' => $book->status(),
        ])->save();

        $book->setId($model->id);

        return $book;
    }

    public function all(): array
    {
        return BookModel::orderBy('titulo')->get()->map(fn (BookModel $model) => $this->toDomain($model))->all();
    }

    public function paginateBooks(int $page, int $perPage, ?string $term = null, ?string $estado = null): PaginatedResult
    {
        $query = BookModel::query();

        if ($term !== null && trim($term) !== '') {
            $term = trim($term);
            $query->where(function ($w) use ($term) {
                $w->where('titulo', 'like', "%{$term}%")
                    ->orWhere('autor', 'like', "%{$term}%")
                    ->orWhere('isbn', 'like', "%{$term}%")
                    ->orWhere('bar_code', 'like', "%{$term}%");
            });
        }

        if ($estado !== null && $estado !== '') {
            $query->where('estado', $estado);
        }

        $total = (clone $query)->count();
        $items = $query->orderBy('titulo')->forPage(max(1, $page), $perPage)->get()
            ->map(fn (BookModel $model) => $this->toDomain($model))->all();

        return new PaginatedResult($items, $total, max(1, $page), $perPage);
    }

    private function toDomain(BookModel $model): Book
    {
        return Book::reconstitute(
            $model->id,
            new Isbn($model->isbn),
            $model->bar_code !== null ? new BarCode($model->bar_code) : null,
            $model->titulo,
            $model->autor,
            $model->existencias_totales,
            $model->existencias_disponibles,
            $model->estado,
        );
    }
}
