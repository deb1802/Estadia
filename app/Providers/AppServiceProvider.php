<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar compatibilidad con columnas ENUM al usar Doctrine DBAL
        if (class_exists(Type::class) && !Type::hasType('enum')) {
            Type::addType('enum', StringType::class);
        }

        Schema::defaultStringLength(191);
    }
}
