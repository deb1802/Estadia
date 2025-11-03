<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Nombre de la aplicación
    |--------------------------------------------------------------------------
    |
    | Este valor es el nombre de tu aplicación. Se usa cuando el framework
    | necesita mostrar el nombre en notificaciones o en otros lugares.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Entorno de la aplicación
    |--------------------------------------------------------------------------
    |
    | Este valor determina el entorno en el que se ejecuta tu aplicación
    | (local, producción, etc.). Se configura en tu archivo ".env".
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Modo debug
    |--------------------------------------------------------------------------
    |
    | Cuando el modo debug está activado, se mostrarán mensajes detallados
    | de error. Si se desactiva, se mostrará una página de error genérica.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL de la aplicación
    |--------------------------------------------------------------------------
    |
    | Esta URL se usa para generar enlaces en la consola o en tareas artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Configuración de idioma (locale)
    |--------------------------------------------------------------------------
    |
    | Define el idioma predeterminado de tu aplicación. Aquí ya está
    | configurado en español (es). También el idioma de respaldo (fallback)
    | y el idioma usado por Faker para generar datos de ejemplo.
    |
    */

    'locale' => 'es',
    'fallback_locale' => 'es',
    'faker_locale' => 'es_MX',

    /*
    |--------------------------------------------------------------------------
    | Zona horaria
    |--------------------------------------------------------------------------
    |
    | Determina la zona horaria por defecto que usará PHP y Laravel.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Clave de encriptación
    |--------------------------------------------------------------------------
    |
    | Esta clave se usa por el servicio de encriptación de Laravel. Debe ser
    | una cadena aleatoria de 32 caracteres. Configúrala en tu archivo ".env".
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Modo mantenimiento
    |--------------------------------------------------------------------------
    |
    | Controla cómo Laravel administra el estado de mantenimiento del sistema.
    | Puedes usar el driver "file" o "cache".
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Proveedores de servicios
    |--------------------------------------------------------------------------
    |
    | Los proveedores listados aquí se cargarán automáticamente.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Proveedores del paquete...
         */

        /*
         * Proveedores de tu aplicación...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Alias de clases
    |--------------------------------------------------------------------------
    |
    | Los alias registrados aquí se cargan de forma automática cuando
    | inicia tu aplicación.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),

];
