<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    // Nombre real de la tabla
    protected $table = 'Pacientes';

    // PK autoincrement (según el ajuste que hicimos)
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    // Campos permitidos
    protected $fillable = [
        'usuario_id',
        'medico_id',
        'padecimientos',
    ];

    // Relaciones
    public function usuario()
    {
        // Usuarios.idUsuario es la PK de Usuarios
        return $this->belongsTo(Usuario::class, 'usuario_id', 'idUsuario');
    }

    public function medico()
    {
        // Medicos.id es la PK de Medicos
        return $this->belongsTo(Medico::class, 'medico_id', 'id');
    }
}
