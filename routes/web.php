<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\MedicamentoController;
use App\Http\Controllers\Medico\PacienteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran todas las rutas web de la aplicación.
| Estas rutas se cargan a través del RouteServiceProvider y se
| asignan al grupo "web" middleware.
|
*/

/* 🌐 Página principal */
Route::get('/', function () {
    return view('welcome'); // Tu página principal personalizada
})->name('home');

/* 🧭 Dashboard general (solo ejemplo, se redirige según tipo de usuario al iniciar sesión) */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/* 👤 Perfil del usuario autenticado */
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* 👑 Sección del ADMINISTRADOR */
Route::middleware(['auth', 'rol:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // CRUD de usuarios
    Route::resource('usuarios', UsuarioController::class);
});

/* 🩺 Sección del MÉDICO */

Route::middleware(['auth', 'rol:medico'])
    ->prefix('medico')
    ->name('medico.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            return view('medico.dashboard');
        })->name('dashboard');

        // CRUD de pacientes 
        Route::resource('pacientes', PacienteController::class);
    });


/* 💬 Sección del PACIENTE */
Route::middleware(['auth', 'rol:paciente'])->prefix('paciente')->name('paciente.')->group(function () {
    Route::get('/dashboard', function () {
        return view('paciente.dashboard');
    })->name('dashboard');
});

/* 🛡️ Incluye las rutas de autenticación de Breeze */
require __DIR__.'/auth.php';


