<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Expresiones
        $soloLetras = "/^[A-Za-zÁÉÍÓÚáéíóúÑñ\\s]+$/u";
        $soloLetrasNum = "/^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\\-\\s]+$/u";

        return [
            'nombre'             => ['required','string','max:50',"regex:$soloLetras"],
            'apellido'           => ['required','string','max:50',"regex:$soloLetras"],
            'email'              => ['required','email:rfc,dns','max:150','unique:usuarios,email'],
            'contrasena'         => ['required','string','min:6'],
            'fechaNacimiento'    => ['required','date','before_or_equal:today'],
            'sexo'               => ['required','in:masculino,femenino,otro'],
            'telefono'           => ['required','digits:10'],
            'tipoUsuario'        => ['required','in:administrador,medico'],

            // Solo si el tipo es médico
            'cedulaProfesional'  => ['required_if:tipoUsuario,medico','regex:'.$soloLetrasNum,'between:5,20'],
            'especialidad'       => ['required_if:tipoUsuario,medico',"regex:$soloLetras",'max:100'],

            'estadoCuenta'       => ['required','in:activo,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'            => 'Has olvidado añadir tu nombre.',
            'nombre.regex'               => 'Solo letras y espacios (sin números).',
            'apellido.required'          => 'Has olvidado añadir tu apellido.',
            'apellido.regex'             => 'Solo letras y espacios (sin números).',

            'email.required'             => 'Has olvidado introducir tu correo electrónico.',
            'email.email'                => 'Introduce un correo válido (ej. usuario@dominio.com).',
            'email.unique'               => 'Este correo ya está registrado.',

            'contrasena.required'        => 'Necesitas una contraseña.',
            'contrasena.min'             => 'La contraseña debe tener al menos :min caracteres.',

            'fechaNacimiento.required'   => 'Indica tu fecha de nacimiento.',
            'fechaNacimiento.date'       => 'La fecha no tiene un formato válido.',
            'fechaNacimiento.before_or_equal' => 'La fecha no puede ser futura.',

            'sexo.required'              => 'Selecciona una opción.',

            'telefono.required'          => 'Has olvidado añadir tu teléfono.',
            'telefono.digits'            => 'El teléfono debe contener exactamente 10 dígitos.',

            'tipoUsuario.required'       => 'Selecciona el tipo de usuario.',

            'cedulaProfesional.required_if' => 'La cédula es obligatoria para médicos.',
            'cedulaProfesional.regex'       => 'La cédula solo acepta letras, números y guiones.',
            'cedulaProfesional.between'     => 'La cédula debe tener entre :min y :max caracteres.',

            'especialidad.required_if'   => 'La especialidad es obligatoria para médicos.',
            'especialidad.regex'         => 'La especialidad solo acepta letras y espacios.',
            'especialidad.max'           => 'Máximo :max caracteres.',

            'estadoCuenta.required'      => 'Selecciona el estado de la cuenta.',
        ];
    }
}
