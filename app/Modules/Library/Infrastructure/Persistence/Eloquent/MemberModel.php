<?php

namespace App\Modules\Library\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $tipo
 * @property string $nombre
 * @property string $identificador
 * @property string|null $grupo_escolar
 */
class MemberModel extends Model
{
    protected $table = 'members';

    protected $fillable = ['tipo', 'nombre', 'identificador', 'grupo_escolar'];

    /**
     * @return HasMany<LoanModel, $this>
     */
    public function loans(): HasMany
    {
        return $this->hasMany(LoanModel::class, 'member_id');
    }
}
