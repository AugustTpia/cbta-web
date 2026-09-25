<?php

namespace App\Modules\Library\Http\Requests;

use App\Modules\Library\Domain\Member\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(Member::TYPES)],
            'nombre' => ['required', 'string', 'max:255'],
            'identificador' => ['required', 'string', 'max:50', 'unique:members,identificador'],
            'grupo_escolar' => ['required_if:tipo,alumno,grupo', 'nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'grupo_escolar.required_if' => 'El grupo escolar es obligatorio para alumnos y grupos.',
            'identificador.unique' => 'Ya existe un miembro con ese identificador.',
        ];
    }
}
