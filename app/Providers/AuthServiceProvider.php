<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// === Modelos y Policies ===
use App\Models\Medicamento;
use App\Policies\MedicamentoPolicy;

use App\Models\ActividadesTerap;
use App\Policies\ActividadesTerapPolicy;

use App\Models\Testimonio;
use App\Models\RespuestaTestimonio;
use App\Policies\TestimonioPolicy;
use App\Policies\RespuestaTestimonioPolicy;

use App\Models\Test;
use App\Policies\TestPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de modelos con sus respectivas Policies.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Medicamento::class          => MedicamentoPolicy::class,
        ActividadesTerap::class     => ActividadesTerapPolicy::class,
        Testimonio::class           => TestimonioPolicy::class,
        RespuestaTestimonio::class  => RespuestaTestimonioPolicy::class,
        Test::class                 => TestPolicy::class, // opcional si ya la creaste
    ];

    /**
     * Registrar los servicios de autenticación/autorización.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ Los administradores pasan automáticamente cualquier policy
        Gate::before(function ($user, $ability) {
            return in_array($user->tipoUsuario, ['admin', 'administrador']) ? true : null;
        });
    }
}
