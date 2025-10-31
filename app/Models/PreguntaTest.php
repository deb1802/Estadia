<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaTest extends Model
{
    protected $table = 'PreguntasTest';
    protected $primaryKey = 'idPregunta';
    public $timestamps = false;

    protected $fillable = ['fkTest','texto','tipo','orden'];

    public function test()
    {
        return $this->belongsTo(Test::class, 'fkTest', 'idTest');
    }

    public function opciones()
    {
        return $this->hasMany(OpcionPregunta::class, 'fkPregunta', 'idPregunta')->orderBy('orden');
    }
}
