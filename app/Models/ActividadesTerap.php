<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadesTerap extends Model
{
    protected $primaryKey = 'idActividad'; 
    public $table = 'Actividades';
    public $timestamps = false;


    public $fillable = [
        'titulo',
        'tipoContenido',
        'categoriaTerapeutica',
        'diagnosticoDirigido',
        'nivelSeveridad',
        'recurso',
        'fkMedico'
    ];

    protected $casts = [
        'titulo' => 'string',
        'tipoContenido' => 'string',
        'categoriaTerapeutica' => 'string',
        'diagnosticoDirigido' => 'string',
        'nivelSeveridad' => 'string',
        'recurso' => 'string',
        'fkMedico' => 'integer'
    ];

    public static array $rules = [
        'titulo' => 'required',
        'tipoContenido' => 'required',
        'categoriaTerapeutica' => 'required',
        'diagnosticoDirigido' => 'required',
        'nivelSeveridad' => 'required',
        'recurso' => 'nullable',
        'fkMedico' => 'required'
    ];

    
}
