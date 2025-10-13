<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Importa correctamente el trait desde el namespace de Laravel
use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ Necesario para el login
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable; // ✅ Usa los traits correctos

    protected $table = 'usuarios';
    protected $primaryKey = 'idUsuario';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'contrasena',
        'fechaNacimiento',
        'sexo',
        'telefono',
        'tipoUsuario',
        'estadoCuenta',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    public $timestamps = false;

    /**
     * 🔐 Indica a Laravel qué campo es la contraseña
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }
}
