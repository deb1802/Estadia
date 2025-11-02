<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'             => ['required', 'string', 'max:100'],
            'presentacion'       => ['required', 'string', 'max:50'],
            'indicaciones'       => ['required', 'string'],
            'efectosSecundarios' => ['required', 'string'],
            'imagenMedicamento'  => ['nullable', 'image', 'max:2048'], // solo esta es opcional
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'             => 'El nombre del medicamento es obligatorio.',
            'nombre.max'                  => 'El nombre no debe exceder los 100 caracteres.',
            'presentacion.required'       => 'La presentación es obligatoria.',
            'presentacion.max'            => 'La presentación no debe exceder los 50 caracteres.',
            'indicaciones.required'       => 'Debes escribir las indicaciones del medicamento.',
            'efectosSecundarios.required' => 'Los efectos secundarios son obligatorios.',
            'imagenMedicamento.image'     => 'El archivo debe ser una imagen válida (JPG, PNG, etc.).',
            'imagenMedicamento.max'       => 'La imagen no puede superar los 2 MB.',
        ];
    }
}
