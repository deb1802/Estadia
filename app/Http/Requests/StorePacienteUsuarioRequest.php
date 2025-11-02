<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Usuario;

class StorePacienteUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $soloLetras = "/^[A-Za-zÁÉÍÓÚáéíóúÑñ\\s]+$/u";

        return [
            // === Campos obligatorios ===
            'nombre'          => ['required','string','max:50',"regex:$soloLetras"],
            'apellido'        => ['required','string','max:50',"regex:$soloLetras"],
            'email'           => [
                'required','email:rfc,dns','max:150',
                Rule::unique(Usuario::class, 'email'),
            ],
            'contrasena'      => ['required','string','min:6'],
            'fechaNacimiento' => ['required','date','before_or_equal:today'],
            'sexo'            => ['required','in:masculino,femenino,otro'],
            'telefono'        => ['required','digits:10'],
            'estadoCuenta'    => ['required','in:activo,inactivo'],  // nuevo
            'padecimientos'   => ['required','string','max:1000'],   // nuevo
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'          => 'El nombre es obligatorio.',
            'nombre.regex'             => 'El nombre solo acepta letras y espacios.',
            'apellido.required'        => 'El apellido es obligatorio.',
            'apellido.regex'           => 'El apellido solo acepta letras y espacios.',
            'email.required'           => 'El correo es obligatorio.',
            'email.email'              => 'Ingresa un correo válido (ej. usuario@dominio.com).',
            'email.unique'             => 'Este correo ya está registrado.',
            'contrasena.required'      => 'La contraseña es obligatoria.',
            'contrasena.min'           => 'Mínimo :min caracteres.',
            'fechaNacimiento.required' => 'Indica la fecha de nacimiento.',
            'fechaNacimiento.before_or_equal' => 'La fecha no puede ser futura.',
            'sexo.required'            => 'Selecciona el sexo.',
            'telefono.required'        => 'El teléfono es obligatorio.',
            'telefono.digits'          => 'El teléfono debe tener exactamente 10 dígitos.',
            'estadoCuenta.required'    => 'Selecciona el estado de la cuenta.',
            'padecimientos.required'   => 'Describe los padecimientos o antecedentes relevantes.',
        ];
    }
}
