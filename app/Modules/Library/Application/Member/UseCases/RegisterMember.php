<?php

namespace App\Modules\Library\Application\Member\UseCases;

use App\Modules\Library\Domain\Member\Member;
use App\Modules\Library\Ports\MemberRepositoryInterface;
use InvalidArgumentException;

class RegisterMember
{
    public function __construct(private readonly MemberRepositoryInterface $members) {}

    public function handle(
        string $tipo,
        string $nombre,
        string $identificador,
        ?string $grupoEscolar = null,
    ): Member {
        if ($this->members->findByIdentificador(trim($identificador)) !== null) {
            throw new InvalidArgumentException('Ya existe un miembro con ese identificador.');
        }

        $member = Member::register($tipo, $nombre, $identificador, $grupoEscolar);

        return $this->members->save($member);
    }
}
