<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    protected $table = 'Medicos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'cedulaProfesional',
        'especialidad',
    ];

    // Médico pertenece a un Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'idUsuario');
    }

    // (Opcional) Si tienes tabla Pacientes con medico_id
    // public function pacientes()
    // {
    //     return $this->hasMany(Paciente::class, 'medico_id', 'id');
    // }
}
