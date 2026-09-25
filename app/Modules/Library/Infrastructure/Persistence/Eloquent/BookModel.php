<?php

namespace App\Modules\Library\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $isbn
 * @property string|null $bar_code
 * @property string $titulo
 * @property string $autor
 * @property int $existencias_totales
 * @property int $existencias_disponibles
 * @property string $estado
 */
class BookModel extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'isbn', 'bar_code', 'titulo', 'autor',
        'existencias_totales', 'existencias_disponibles', 'estado',
    ];

    /**
     * @return HasMany<LoanItemModel, $this>
     */
    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItemModel::class, 'book_id');
    }
}
