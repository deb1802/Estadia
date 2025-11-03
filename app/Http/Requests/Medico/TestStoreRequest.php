<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta acción.
     */
    public function authorize(): bool
    {
        // Si tu política de roles lo permite, puedes dejarlo así:
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'nombre'        => 'required|string|max:150',
            'tipoTrastorno' => 'nullable|string|max:120',
            'descripcion'   => 'required|string',
            'estado'        => 'required|in:activo,inactivo',
        ];
    }

    /**
     * Mensajes personalizados en español.
     */
    public function messages(): array
{
    return [
        'required' => 'El campo :attribute es obligatorio.',
        'string'   => 'El campo :attribute debe contener texto válido.',
        'max'      => 'El campo :attribute no puede tener más de :max caracteres.',
        'in'       => 'El valor seleccionado en :attribute no es válido.',
    ];
}

public function attributes(): array
{
    return [
        'nombre'        => 'nombre del test',
        'tipoTrastorno' => 'tipo de trastorno',
        'descripcion'   => 'descripción del test', // mejora la legibilidad
        'estado'        => 'estado',
    ];
}

}
