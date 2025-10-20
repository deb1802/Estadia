<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\ActividadesTerapPolicy;
// Importaciones necesarias
use App\Models\Medicamento;
use App\Policies\MedicamentoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de modelos con sus respectivas Policies.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Medicamento::class => MedicamentoPolicy::class,
        ActividadesTerap::class => ActividadesTerapPolicy::class,
    ];

    /**
     * Registrar los servicios de autenticación/autorización.
     */
    public function boot(): void
    {
        // Aquí Laravel ya aplicará automáticamente las policies registradas.
    }
}
