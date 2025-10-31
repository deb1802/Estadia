<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpcionPregunta extends Model
{
    protected $table = 'OpcionesPregunta';
    protected $primaryKey = 'idOpcion';
    public $timestamps = false;

    protected $fillable = ['fkPregunta','etiqueta','valor','puntaje','orden'];

    public function pregunta()
    {
        return $this->belongsTo(PreguntaTest::class, 'fkPregunta', 'idPregunta');
    }
}
