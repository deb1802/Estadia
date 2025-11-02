<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Si usas policies/middleware ya controlan el acceso; aquí lo permitimos.
        return true;
    }

    /**
     * Normaliza antes de validar: recorta espacios y estandariza valores.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre'        => is_string($this->nombre) ? trim($this->nombre) : $this->nombre,
            'tipoTrastorno' => is_string($this->tipoTrastorno) ? trim($this->tipoTrastorno) : $this->tipoTrastorno,
            'descripcion'   => is_string($this->descripcion) ? trim($this->descripcion) : $this->descripcion,
            'estado'        => is_string($this->estado) ? strtolower(trim($this->estado)) : $this->estado,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre'        => ['required', 'string', 'max:150'],
            'tipoTrastorno' => ['nullable', 'string', 'max:120'],
            'descripcion'   => ['nullable', 'string'],
            'estado'        => ['required', 'in:activo,inactivo'],
        ];
    }

    /**
     * Etiquetas legibles para los campos (para los mensajes).
     */
    public function attributes(): array
    {
        return [
            'nombre'        => 'nombre del test',
            'tipoTrastorno' => 'tipo de trastorno',
            'descripcion'   => 'descripción',
            'estado'        => 'estado',
        ];
    }

    /**
     * Mensajes en español.
     */
    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string'   => 'El campo :attribute debe ser texto.',
            'max'      => 'El campo :attribute no puede tener más de :max caracteres.',
            'in'       => 'El campo :attribute debe ser una de las opciones válidas.',
        ];
    }
}
