<?php

namespace App\Shared\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Departamento/rol del personal (biblioteca, servicios escolares, admin).
 *
 * Vive en Shared/Auth para que los módulos (Library, futuros) solo
 * dependan de esta abstracción y nunca al revés.
 */
class Role extends Model
{
    public const ADMIN = 'admin';

    public const BIBLIOTECA = 'biblioteca';

    public const SERVICIOS_ESCOLARES = 'servicios_escolares';

    protected $fillable = ['slug', 'name'];

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
