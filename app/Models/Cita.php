<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'Citas';
    protected $primaryKey = 'idCita';
    public $timestamps = false;

    public $fillable = [
        'fkMedico',
        'fkPaciente',
        'fechaHora',
        'motivo',
        'ubicacion',
        'estado'
    ];

    protected $casts = [
        'fechaHora' => 'datetime',
        'motivo' => 'string',
        'ubicacion' => 'string',
        'estado' => 'string'
    ];

    public static array $rules = [
        'fkMedico' => 'nullable',
        'fkPaciente' => 'nullable',
        'fechaHora' => 'required',
        'motivo' => 'required|string|max:65535',
        'ubicacion' => 'required|string|max:150',
        'estado' => 'nullable|string'
    ];

    public function fkmedico(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Medico::class, 'fkMedico');
    }

    public function fkpaciente(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Paciente::class, 'fkPaciente');
    }
}
