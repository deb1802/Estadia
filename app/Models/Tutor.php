<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    // Nombre exacto de la tabla en tu BD
    protected $table = 'Tutores';

    // Llave primaria real
    protected $primaryKey = 'idTutor';

    // Tu tabla no tiene created_at / updated_at
    public $timestamps = false;

    // Campos asignables
    protected $fillable = [
        'nombreCompleto',
        'parentesco',
        'telefono',
        'correo',
        'direccion',
        'observaciones',
        'fkPaciente',
    ];

    // Tipos de datos
    protected $casts = [
        'idTutor'        => 'integer',
        'fkPaciente'     => 'integer',
        'nombreCompleto' => 'string',
        'parentesco'     => 'string',
        'telefono'       => 'string',
        'correo'         => 'string',
        'direccion'      => 'string',
        'observaciones'  => 'string',
    ];

    // Reglas de validación
    public static array $rules = [
        'nombreCompleto' => 'nullable|string|max:100',
        'parentesco'     => 'nullable|string|max:50',
        'telefono'       => 'nullable|string|max:20',
        'correo'         => 'nullable|string|max:100',
        'direccion'      => 'nullable|string',
        'observaciones'  => 'nullable|string',
        'fkPaciente'     => 'required|exists:Pacientes,id',
    ];

    /**
     * Relación: cada tutor pertenece a un paciente
     */
    public function paciente()
    {
        // foreignKey = fkPaciente en Tutores, ownerKey = id en Pacientes
        return $this->belongsTo(\App\Models\Paciente::class, 'fkPaciente', 'id');
    }

    /**
     * Configura la clave usada en las rutas (corrige el error en edit/update)
     */
    public function getRouteKeyName()
    {
        return 'idTutor';
    }
}
