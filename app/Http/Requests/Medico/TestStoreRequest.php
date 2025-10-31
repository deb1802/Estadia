<?php

namespace App\Http\Requests\Medico;

use Illuminate\Foundation\Http\FormRequest;

class TestStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'        => 'required|string|max:150',
            'tipoTrastorno' => 'nullable|string|max:120',
            'descripcion'   => 'nullable|string',
            'estado'        => 'required|in:activo,inactivo',
        ];
    }
}
