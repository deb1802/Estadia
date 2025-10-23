<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    // Nombre real de la tabla
    protected $table = 'Pacientes';

    // PK autoincrement (según tu base de datos)
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    // Campos permitidos
    protected $fillable = [
        'usuario_id',
        'medico_id',
        'padecimientos',
    ];

    /**
     * 🔹 Relación con la tabla Usuarios
     * Cada paciente está vinculado a un registro en Usuarios.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'idUsuario');
    }

    /**
     * 🔹 Relación con la tabla Médicos
     * Cada paciente tiene asignado un médico responsable.
     */
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id', 'id');
    }

    /**
     * 🔹 Relación con la tabla Tutores
     * Un paciente puede tener varios tutores asignados.
     */
    public function tutores()
    {
        return $this->hasMany(Tutor::class, 'fkPaciente', 'id');
    }

    /**
     * 🔹 Acceso rápido al nombre completo del paciente
     * (concatenando nombre + apellido desde Usuarios)
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->usuario->nombre} {$this->usuario->apellido}";
    }
}
