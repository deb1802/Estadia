<?php

namespace App\Http\Requests\Medico;

use Illuminate\Foundation\Http\FormRequest;

class TestBuilderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // PREGUNTAS
            'preguntas'                           => 'required|array|min:1',
            'preguntas.*.texto'                   => 'required|string',
            'preguntas.*.tipo'                    => 'required|in:opcion_unica,opcion_multiple,abierta',
            'preguntas.*.orden'                   => 'required|integer|min:1',

            // OPCIONES (solo si no es abierta)
            'preguntas.*.opciones'                => 'nullable|array',
            'preguntas.*.opciones.*.etiqueta'     => 'required_with:preguntas.*.opciones|string|max:200',
            'preguntas.*.opciones.*.valor'        => 'nullable|string|max:100',
            'preguntas.*.opciones.*.puntaje'      => 'required_with:preguntas.*.opciones|integer',
            'preguntas.*.opciones.*.orden'        => 'required_with:preguntas.*.opciones|integer|min:1',

            // RANGOS
            'rangos'                              => 'required|array|min:1',
            'rangos.*.minPuntaje'                 => 'required|integer',
            'rangos.*.maxPuntaje'                 => 'required|integer|gte:rangos.*.minPuntaje',
            'rangos.*.diagnostico'                => 'required|string|max:150',
            'rangos.*.descripcion'                => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'preguntas.required' => 'Debes agregar al menos una pregunta.',
            'rangos.required'    => 'Debes definir al menos un rango de evaluación.',
        ];
    }
}
