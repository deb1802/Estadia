<?php

namespace App\Repositories;

use App\Models\Expediente;
use App\Repositories\BaseRepository;

class ExpedienteRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'fkPaciente',
        'antecedentes',
        'diagnosticos',
        'notasClinicas',
        'historialCitas',
        'testsAplicados',
        'actividadesAsignadas',
        'respuestasEmocionales',
        'medicamentosPrescritos',
        'observaciones',
        'fechaActualizacion'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Expediente::class;
    }
}
