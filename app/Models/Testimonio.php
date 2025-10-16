<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonio extends Model
{
    use HasFactory;

    protected $table = 'Testimonios';
    protected $primaryKey = 'idTestimonio';
    public $timestamps = false;

    protected $fillable = ['fkPaciente', 'fecha', 'contenido'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'fkPaciente', 'id');
    }

    public function respuestas()
    {
        return $this->hasMany(RespuestaTestimonio::class, 'fkTestimonio', 'idTestimonio');
    }
}
