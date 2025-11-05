<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $table = 'Citas';
    protected $primaryKey = 'idCita';
    public $timestamps = false;

    protected $fillable = [
        'fkMedico',
        'fkPaciente',
        'fechaHora',
        'motivo',
        'ubicacion',
        'estado',
    ];

    protected $casts = [
        'fechaHora' => 'datetime',
        'motivo' => 'string',
        'ubicacion' => 'string',
        'estado' => 'string',
    ];

    public static array $rules = [
        'fkMedico'   => 'nullable|integer|exists:Medicos,id',
        'fkPaciente' => 'nullable|integer|exists:Pacientes,id',
        'fechaHora'  => 'required|date',
        'motivo'     => 'required|string|max:65535',
        'ubicacion'  => 'required|string|max:150',
        'estado'     => 'nullable|string',
    ];

    /**
     * 🔹 Relación con el médico que programó la cita
     */
    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'fkMedico', 'id');
    }

    /**
     * 🔹 Relación con el paciente asignado a la cita
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'fkPaciente', 'id');
    }
}
