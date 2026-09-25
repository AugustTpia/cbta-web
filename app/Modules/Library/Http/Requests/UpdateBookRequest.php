<?php

namespace App\Modules\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'titulo' => ['required', 'string', 'max:255'],
            'autor' => ['required', 'string', 'max:255'],
            'existencias_totales' => ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }
}
