<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // permite la validación
    }

    public function rules(): array
    {
        return [
            'titulo'               => 'required|string|max:255',
            'tipoContenido'        => 'required|in:audio,video,lectura',
            'categoriaTerapeutica' => 'required|string|max:255',
            'diagnosticoDirigido'  => 'required|string|max:255',
            'nivelSeveridad'       => 'required|string|max:255',
            'link'                 => 'nullable|url',
            'archivo'              => 'nullable|file|mimes:pdf,mp3,mp4,avi,mov,jpg,jpeg,png|max:102400',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'tipoContenido.required' => 'El tipo de contenido es obligatorio.',
            'categoriaTerapeutica.required' => 'La categoría terapéutica es obligatoria.',
            'diagnosticoDirigido.required' => 'El diagnóstico dirigido es obligatorio.',
            'nivelSeveridad.required' => 'El nivel de severidad es obligatorio.',
            'tipoContenido.in' => 'Selecciona un tipo de contenido válido (audio, video o lectura).',
            'link.url' => 'El enlace proporcionado no tiene un formato válido.',
            'archivo.mimes' => 'El archivo debe ser PDF, MP3, MP4, AVI, MOV, JPG o PNG.',
            'archivo.max' => 'El archivo no debe superar los 100 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'tipoContenido' => 'tipo de contenido',
            'categoriaTerapeutica' => 'categoría terapéutica',
            'diagnosticoDirigido' => 'diagnóstico dirigido',
            'nivelSeveridad' => 'nivel de severidad',
            'archivo' => 'archivo',
            'link' => 'enlace',
        ];
    }
}
