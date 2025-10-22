<?php

namespace App\Repositories;

use App\Models\Tutor;
use App\Repositories\BaseRepository;

class TutorRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'nombreCompleto',
        'parentesco',
        'telefono',
        'correo',
        'direccion',
        'observaciones',
        'fkPaciente'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Tutor::class;
    }
}
