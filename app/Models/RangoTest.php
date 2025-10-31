<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RangoTest extends Model
{
    protected $table = 'RangosTest';
    protected $primaryKey = 'idRango';
    public $timestamps = false;

    protected $fillable = ['fkTest','minPuntaje','maxPuntaje','diagnostico','descripcion'];

    public function test()
    {
        return $this->belongsTo(Test::class, 'fkTest', 'idTest');
    }
}
