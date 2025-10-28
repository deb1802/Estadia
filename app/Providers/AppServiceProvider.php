<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;
use Illuminate\Database\Connection;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // 👇 Arreglo definitivo para ENUM en InfyOm/Doctrine
        $platform = app(Connection::class)->getDoctrineSchemaManager()->getDatabasePlatform();

        if (!Type::hasType('enum')) {
            Type::addType('enum', StringType::class);
        }

        $platform->registerDoctrineTypeMapping('enum', 'string');
    }
}
