<?php

namespace App\Repositories;

use App\Models\Cita;
use App\Repositories\BaseRepository;

class CitaRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'fkMedico',
        'fkPaciente',
        'fechaHora',
        'motivo',
        'ubicacion',
        'estado'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Cita::class;
    }
}
