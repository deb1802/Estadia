<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ Para permitir login si se usa Breeze o Auth
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'Usuarios';
    protected $primaryKey = 'idUsuario';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * Campos que pueden asignarse masivamente
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',          // ✅ cambiado de 'correo' a 'email'
        'contrasena',
        'fechaNacimiento',
        'sexo',
        'telefono',
        'tipoUsuario',
        'estadoCuenta',
    ];

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
}
