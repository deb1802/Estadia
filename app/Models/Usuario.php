<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nombre exacto de la tabla (respetando mayúsculas si tu DB es sensible a ellas)
    protected $table = 'Usuarios';
    protected $primaryKey = 'idUsuario';
    public $timestamps = false;

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


    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Un usuario puede tener un registro de médico
    public function medico()
    {
        // FK en Medicos = usuario_id, PK en Usuarios = idUsuario
        return $this->hasOne(Medico::class, 'usuario_id', 'idUsuario');
    }

}
