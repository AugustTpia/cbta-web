<?php

namespace App\Modules\Library\Application\Member\UseCases;

use App\Modules\Library\Domain\Member\Member;

/**
 * Alta de grupo (clase completa responsable de un préstamo grupal).
 * Delega en RegisterMember con tipo fijo: no requiere lista de alumnos.
 */
class RegisterGroup
{
    public function __construct(private readonly RegisterMember $registerMember) {}

    public function handle(string $nombre, string $identificador, string $grupoEscolar): Member
    {
        return $this->registerMember->handle(Member::GRUPO, $nombre, $identificador, $grupoEscolar);
    }
}
