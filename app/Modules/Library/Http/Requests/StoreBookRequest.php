<?php

namespace App\Modules\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'isbn' => ['required', 'string', 'max:20', 'unique:books,isbn'],
            'bar_code' => ['nullable', 'string', 'max:40', 'unique:books,bar_code'],
            'titulo' => ['required', 'string', 'max:255'],
            'autor' => ['required', 'string', 'max:255'],
            'existencias_totales' => ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'isbn.unique' => 'Ya existe un libro con ese ISBN.',
            'bar_code.unique' => 'Ya existe un libro con ese código de barras.',
        ];
    }
}
