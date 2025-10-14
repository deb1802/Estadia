<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUsuariosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    return [
        'nombre'       => 'required',
        'apellido'     => 'required',
        'email'        => 'required|email|unique:Usuarios,email',
        'contrasena'   => 'required|min:6',
        'tipoUsuario'  => 'required|in:administrador,medico',
        'estadoCuenta' => 'nullable|in:activo,inactivo',

        // ➕ agrega estos como opcionales para que entren en validated()
        'fechaNacimiento' => 'nullable|date',
        'sexo'            => 'nullable|in:masculino,femenino,otro',
        'telefono'        => 'nullable|string',
    ];
}

}
