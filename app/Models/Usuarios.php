<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuarios extends Model
{
    use HasFactory;

    protected $table = 'Usuarios'; // Mantiene la mayúscula si tu BD la usa
    protected $primaryKey = 'idUsuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',           // ✅ cambiado de 'correo' a 'email'
        'contrasena',
        'fechaNacimiento',
        'sexo',
        'telefono',
        'tipoUsuario',
        'estadoCuenta',
    ];

    protected $casts = [
        'nombre'          => 'string',
        'apellido'        => 'string',
        'email'           => 'string',  // ✅ actualizado
        'contrasena'      => 'string',
        'fechaNacimiento' => 'date',
        'sexo'            => 'string',
        'telefono'        => 'string',
        'tipoUsuario'     => 'string',
        'estadoCuenta'    => 'string',
    ];

    public static array $rules = [
        'nombre'       => 'required|string|max:50',
        'email'        => 'required|email|unique:Usuarios,email', // ✅ actualizado
        'contrasena'   => 'required|min:6',
        'tipoUsuario'  => 'required|in:administrador,medico,paciente',
        'estadoCuenta' => 'required|in:activo,inactivo',
    ];
}
