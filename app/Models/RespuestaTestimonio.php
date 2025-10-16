<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaTestimonio extends Model
{
    use HasFactory;

    protected $table = 'RespuestasTestimonio';
    protected $primaryKey = 'idRespuesta';
    public $timestamps = false;

    protected $fillable = ['fkTestimonio', 'fkPaciente', 'contenido', 'fecha'];

    public function testimonio()
    {
        return $this->belongsTo(Testimonio::class, 'fkTestimonio', 'idTestimonio');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'fkPaciente', 'id');
    }
}
