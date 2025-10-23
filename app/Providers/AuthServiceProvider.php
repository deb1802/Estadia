<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// Importa los modelos y sus policies
use App\Models\Medicamento;
use App\Policies\MedicamentoPolicy;

use App\Models\ActividadesTerap;
use App\Policies\ActividadesTerapPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de modelos con sus respectivas Policies.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Medicamento::class      => MedicamentoPolicy::class,
        ActividadesTerap::class => ActividadesTerapPolicy::class,
    ];

    /**
     * Registrar los servicios de autenticación/autorización.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate::before o Gate::after que altere permisos globales, coméntalo aquí.
        // Gate::before(function ($user, $ability) {
        //     return null;
        // });
    }
}
