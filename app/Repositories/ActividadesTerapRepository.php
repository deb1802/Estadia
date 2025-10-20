<?php

namespace App\Repositories;

use App\Models\ActividadesTerap;
use App\Repositories\BaseRepository;

class ActividadesTerapRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return ActividadesTerap::class;
    }
}
