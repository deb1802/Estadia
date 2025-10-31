<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionTest extends Model
{
    protected $table = 'AsignacionesTest';           // 👈 nombre real de la tabla
    protected $primaryKey = 'idAsignacionTest';
    public $timestamps = false;

    protected $fillable = [
        'fkTest',
        'fkPaciente',
        'fechaAsignacion',
        'fechaRespuesta',
        'puntajeTotal',
        'diagnosticoSugerido',
        'diagnosticoConfirmado',
        'confirmadoPor',
        'fechaConfirmacion',
        'notasClinicas',
        'subescalas',
    ];
}
