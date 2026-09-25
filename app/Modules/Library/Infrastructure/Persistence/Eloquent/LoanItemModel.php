<?php

namespace App\Modules\Library\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $loan_id
 * @property int $book_id
 * @property Carbon|null $fecha_devolucion_real
 * @property bool $devuelto
 */
class LoanItemModel extends Model
{
    protected $table = 'loan_items';

    protected $fillable = ['loan_id', 'book_id', 'fecha_devolucion_real', 'devuelto'];

    protected function casts(): array
    {
        return [
            'fecha_devolucion_real' => 'datetime',
            'devuelto' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<LoanModel, $this>
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(LoanModel::class, 'loan_id');
    }

    /**
     * @return BelongsTo<BookModel, $this>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(BookModel::class, 'book_id');
    }
}
