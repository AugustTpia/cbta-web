<?php

namespace App\Modules\Library\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $member_id
 * @property Carbon $fecha_prestamo
 * @property Carbon $fecha_estimada_devolucion
 * @property string $estado
 */
class LoanModel extends Model
{
    protected $table = 'loans';

    protected $fillable = ['member_id', 'fecha_prestamo', 'fecha_estimada_devolucion', 'estado'];

    protected function casts(): array
    {
        return [
            'fecha_prestamo' => 'datetime',
            'fecha_estimada_devolucion' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<MemberModel, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberModel::class, 'member_id');
    }

    /**
     * @return HasMany<LoanItemModel, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(LoanItemModel::class, 'loan_id');
    }
}
