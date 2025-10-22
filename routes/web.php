<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\MedicamentoController;
use App\Http\Controllers\Medico\PacienteController;
use App\Http\Controllers\Paciente\TestimonioController;
use App\Http\Controllers\Paciente\RespuestaTestimonioController;
use App\Http\Controllers\Medico\ActividadesTController;


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
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 🧭 Dashboard principal del administrador
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // 👥 CRUD de usuarios
        Route::resource('usuarios', App\Http\Controllers\Admin\UsuarioController::class);

        // 💊 CRUD de medicamentos
        Route::resource('medicamentos', App\Http\Controllers\Admin\MedicamentoController::class);

        // 🧘‍♀️ CRUD de actividades terapéuticas
        Route::resource('actividades_terap', App\Http\Controllers\Medico\ActividadesTController::class)
            ->parameters(['actividades_terap' => 'actividad']);
        // Si quisieras limitar acciones visibles:
        // ->only(['index', 'show', 'destroy']);

        // 📊 Panel de estadísticas del administrador
        Route::get('/panel-estadisticas', function () {
            return view('admin.resumen_admin');
        })->name('panel.estadisticas');

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

        // CRUD de pacientes (solo médicos)
        Route::resource('pacientes', App\Http\Controllers\Medico\PacienteController::class);

        // CRUD de medicamentos (reutiliza el controlador del admin)
        Route::resource('medicamentos', App\Http\Controllers\Admin\MedicamentoController::class);

        // CRUD de actividades terapéuticas (usa parámetro 'actividad')
        Route::resource('actividades_terap', App\Http\Controllers\Medico\ActividadesTController::class)
            ->parameters(['actividades_terap' => 'actividad']);
    });



/* 💬 Sección del PACIENTE */
Route::middleware(['auth', 'rol:paciente'])->prefix('paciente')->name('paciente.')->group(function () {
    Route::get('/dashboard', function () {
        return view('paciente.dashboard');
    })->name('dashboard');

    // Foro de testimonios (misma vista para listar y publicar)
    Route::get('/testimonios', [\App\Http\Controllers\Paciente\TestimonioController::class, 'index'])
        ->name('testimonios.index');

    Route::post('/testimonios', [\App\Http\Controllers\Paciente\TestimonioController::class, 'store'])
        ->name('testimonios.store');

    Route::post('/testimonios/{idTestimonio}/respuestas', [RespuestaTestimonioController::class, 'store'])
        ->name('testimonios.respuestas.store');

});



/* 🛡️ Incluye las rutas de autenticación de Breeze */
require __DIR__.'/auth.php';

Route::resource('tutors', App\Http\Controllers\TutorController::class);