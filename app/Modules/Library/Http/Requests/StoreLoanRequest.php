<?php

namespace App\Modules\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
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
            'member_id' => ['required', 'integer', 'exists:members,id'],
            'fecha_estimada_devolucion' => ['required', 'date', 'after:today'],
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['integer', 'exists:books,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha_estimada_devolucion.after' => 'La fecha de devolución debe ser futura.',
            'book_ids.min' => 'Agrega al menos un libro (escanea o busca).',
        ];
    }
}
