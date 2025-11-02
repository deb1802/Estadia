<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $table = 'Expedientes';
    protected $primaryKey = 'idExpediente';
    public $timestamps = false;

    protected $fillable = [
        'fkPaciente',
        'antecedentes',
        'diagnosticos',
        'notasClinicas',
        'observaciones',
        'fechaActualizacion'
    ];

    // Relación con paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'fkPaciente', 'id');
    }
}
