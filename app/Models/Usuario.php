<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nombre exacto de la tabla (respetando mayúsculas si tu DB lo requiere)
    protected $table = 'Usuarios';
    protected $primaryKey = 'idUsuario';
    public $timestamps = false;

    // Campos asignables
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

    // Campos ocultos en respuestas JSON
    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    // Contraseña personalizada (Laravel la busca con getAuthPassword)
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * 🔹 Relación: un usuario puede tener un registro de médico
     * (Usuarios.idUsuario → Medicos.usuario_id)
     */
    public function medico()
    {
        return $this->hasOne(Medico::class, 'usuario_id', 'idUsuario');
    }

    /**
     * 🔹 Relación: un usuario puede tener un registro de paciente
     * (Usuarios.idUsuario → Pacientes.usuario_id)
     */
    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'usuario_id', 'idUsuario');
    }

    /**
     * 🔹 Helper: nombre completo del usuario
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * 🔹 Helper: verificar si el usuario tiene un rol específico
     * Ejemplo de uso: Auth::user()->esRol('medico')
     */
    public function esRol(string $rol): bool
    {
        return strtolower($this->tipoUsuario) === strtolower($rol);
    }
}
