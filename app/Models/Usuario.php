<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ Para permitir login si se usa Breeze o Auth
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
<<<<<<< HEAD
    use HasFactory, Notifiable;

    protected $table = 'Usuarios';
    protected $primaryKey = 'idUsuario';
    public $incrementing = true;
    public $timestamps = false;
=======
    protected $table = 'Usuarios';          // Nombre real de tu tabla
    protected $primaryKey = 'idUsuario';    // Llave primaria
    public $timestamps = false;             // 🔹 Desactiva created_at y updated_at
>>>>>>> upstream/main

    /**
     * Campos que pueden asignarse masivamente
     */
    protected $fillable = [
        'nombre',
        'apellido',
<<<<<<< HEAD
        'email',          // ✅ cambiado de 'correo' a 'email'
=======
        'email',
>>>>>>> upstream/main
        'contrasena',
        'fechaNacimiento',
        'sexo',
        'telefono',
        'tipoUsuario',
        'estadoCuenta',
    ];

<<<<<<< HEAD
    /**
     * Campos que deben ocultarse (por ejemplo, contraseñas)
     */
    protected $hidden = [
        'contrasena',
    ];

    /**
     * Si el modelo se usa para autenticación, define el campo de contraseña
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }
=======
    protected $attributes = [
        'estadoCuenta' => 'activo',
    ];

    protected $casts = [
        'fechaNacimiento' => 'date',
    ];
>>>>>>> upstream/main
}
