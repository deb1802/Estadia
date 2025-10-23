<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    // Nombre real de la tabla
    protected $table = 'Medicos';

    // Llave primaria
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    // Campos asignables
    protected $fillable = [
        'usuario_id',
        'cedulaProfesional',
        'especialidad',
    ];

    /**
     * 🔹 Relación: cada médico pertenece a un usuario
     * (Usuarios.idUsuario → Medicos.usuario_id)
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'idUsuario');
    }

    /**
     * 🔹 Relación: cada médico puede tener muchos pacientes
     * (Pacientes.medico_id → Medicos.id)
     */
    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'medico_id', 'id');
    }

    /**
     * 🔹 Helper: obtener el nombre completo del médico
     * desde su usuario relacionado
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->usuario->nombre} {$this->usuario->apellido}";
    }
}
