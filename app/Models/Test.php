<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'Tests';
    protected $primaryKey = 'idTest';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'tipoTrastorno', 'descripcion', 'estado', 'fkMedico'
    ];

    // fkMedico -> Medicos.id  (tú ya usas 'id' en Medicos)
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'fkMedico', 'id');
    }

    public function preguntas()
    {
        return $this->hasMany(PreguntaTest::class, 'fkTest', 'idTest')->orderBy('orden');
    }

    public function rangos()
    {
        return $this->hasMany(RangoTest::class, 'fkTest', 'idTest')->orderBy('minPuntaje');
    }
}
