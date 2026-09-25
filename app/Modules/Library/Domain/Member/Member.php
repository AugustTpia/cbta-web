<?php

namespace App\Modules\Library\Domain\Member;

use InvalidArgumentException;

/**
 * Entidad Member: alumno, docente, personal o grupo.
 * Un grupo representa una clase completa responsable de un préstamo grupal.
 */
final class Member
{
    public const ALUMNO = 'alumno';

    public const DOCENTE = 'docente';

    public const PERSONAL = 'personal';

    public const GRUPO = 'grupo';

    public const TYPES = [self::ALUMNO, self::DOCENTE, self::PERSONAL, self::GRUPO];

    /** Tipos que piden prestado como individuos (tope de libros). */
    public const INDIVIDUAL_TYPES = [self::ALUMNO, self::DOCENTE, self::PERSONAL];

    private function __construct(
        private ?int $id,
        private string $tipo,
        private string $nombre,
        private string $identificador,
        private ?string $grupoEscolar,
    ) {
        $this->guardInvariants();
    }

    public static function register(
        string $tipo,
        string $nombre,
        string $identificador,
        ?string $grupoEscolar = null,
    ): self {
        return new self(null, $tipo, trim($nombre), trim($identificador), $grupoEscolar !== null ? trim($grupoEscolar) : null);
    }

    public static function reconstitute(
        ?int $id,
        string $tipo,
        string $nombre,
        string $identificador,
        ?string $grupoEscolar,
    ): self {
        return new self($id, $tipo, $nombre, $identificador, $grupoEscolar);
    }

    public function isGroup(): bool
    {
        return $this->tipo === self::GRUPO;
    }

    public function isIndividual(): bool
    {
        return in_array($this->tipo, self::INDIVIDUAL_TYPES, true);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function tipo(): string
    {
        return $this->tipo;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function identificador(): string
    {
        return $this->identificador;
    }

    public function grupoEscolar(): ?string
    {
        return $this->grupoEscolar;
    }

    private function guardInvariants(): void
    {
        if (! in_array($this->tipo, self::TYPES, true)) {
            throw new InvalidArgumentException('Tipo de miembro inválido.');
        }

        if ($this->nombre === '' || $this->identificador === '') {
            throw new InvalidArgumentException('Nombre e identificador son obligatorios.');
        }

        if (in_array($this->tipo, [self::ALUMNO, self::GRUPO], true) && ($this->grupoEscolar === null || $this->grupoEscolar === '')) {
            throw new InvalidArgumentException('El grupo escolar es obligatorio para alumnos y grupos.');
        }
    }
}
