<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    // Nombre real de la tabla
    protected $table = 'Notificaciones';

    // Clave primaria
    protected $primaryKey = 'idNotificacion';

    // No usas created_at / updated_at
    public $timestamps = false;

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'fkUsuario',   // int
        'titulo',      // varchar(150)
        'mensaje',     // text
        'tipo',        // enum('sistema','correo')
        'fecha',       // datetime
    ];

    // Casts útiles
    protected $casts = [
        'fkUsuario' => 'integer',
        'fecha'     => 'datetime',
    ];
}
