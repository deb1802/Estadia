<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\MedicamentoController;
use App\Http\Controllers\Medico\PacienteController;
use App\Http\Controllers\Paciente\TestimonioController;
use App\Http\Controllers\Paciente\RespuestaTestimonioController;
use App\Http\Controllers\Medico\ActividadesTController;
use App\Http\Controllers\Medico\AsignacionActividadController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\CitaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Route::pattern('actividad', '[0-9]+');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* 🌐 Página principal */
Route::get('/', function () {
    return view('welcome');
})->name('home');

/* 🧭 Dashboard general */
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
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

        // 👥 CRUD de usuarios
        Route::resource('usuarios', UsuarioController::class);

        // 💊 CRUD de medicamentos
        Route::resource('medicamentos', MedicamentoController::class);

        // 📘 CRUD de tutores
        Route::resource('tutores', TutorController::class)
            ->names('tutores')
            ->parameters(['tutores' => 'tutor']);

        // 📅 CRUD de citas (el admin ve todas)
        Route::resource('citas', CitaController::class)
            ->names('citas')
            ->parameters(['citas' => 'cita']);

        // 🧘‍♀️ Actividades terapéuticas
        Route::resource('actividades_terap', ActividadesTController::class)
            ->parameters(['actividades_terap' => 'actividad']);

        // 📊 Panel de estadísticas
        Route::get('/panel-estadisticas', fn() => view('admin.resumen_admin'))->name('panel.estadisticas');
    });

/* 🩺 Sección del MÉDICO */
Route::middleware(['auth', 'rol:medico'])
    ->prefix('medico')
    ->name('medico.')
    ->group(function () {

        // 🧭 Dashboard
        Route::get('/dashboard', fn() => view('medico.dashboard'))->name('dashboard');

        // 👨‍⚕️ CRUD de pacientes
        Route::resource('pacientes', PacienteController::class);

        // 💊 Medicamentos
        Route::resource('medicamentos', MedicamentoController::class);

        // 🧘‍♀️ Actividades terapéuticas
        Route::resource('actividades_terap', ActividadesTController::class)
            ->parameters(['actividades_terap' => 'actividad']);

        // 🎯 Asignación de actividades
        Route::prefix('actividades_terap')->name('actividades_terap.')->group(function () {
            Route::get('asignar', [AsignacionActividadController::class, 'create'])->name('asignar');
            Route::post('asignar', [AsignacionActividadController::class, 'store'])->name('asignar.store');
        });

        // 📘 CRUD de tutores (solo los del médico autenticado)
        Route::resource('tutores', TutorController::class)
            ->names('tutores')
            ->parameters(['tutores' => 'tutor']);

        // 🗓️ CRUD de citas (solo las del médico)
        Route::resource('citas', CitaController::class)
            ->names('citas')
            ->parameters(['citas' => 'cita']);
    });

/* 💬 Sección del PACIENTE */
Route::middleware(['auth', 'rol:paciente'])
    ->prefix('paciente')
    ->name('paciente.')
    ->group(function () {

        // 🧭 Dashboard
        Route::get('/dashboard', fn() => view('paciente.dashboard'))->name('dashboard');

        // 💬 Foro de testimonios
        Route::get('/testimonios', [TestimonioController::class, 'index'])->name('testimonios.index');
        Route::post('/testimonios', [TestimonioController::class, 'store'])->name('testimonios.store');
        Route::post('/testimonios/{idTestimonio}/respuestas', [RespuestaTestimonioController::class, 'store'])
            ->name('testimonios.respuestas.store');

        // 📘 Vista de tutores (solo lectura)
        Route::get('/tutores', [TutorController::class, 'index'])->name('tutores.index');

        // 🗓️ Citas (solo sus propias citas)
        Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
        Route::patch('/citas/{id}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    });

/* 🛡️ Rutas de autenticación (Breeze) */
require __DIR__ . '/auth.php';

/* 🧪 Ruta de prueba */
Route::get('/prueba-asignar', function (\Illuminate\Http\Request $request) {
    $actividadId = (int) $request->query('actividad', 4);

    if (!Auth::check()) {
        return '❌ No hay sesión activa';
    }

    $actividad = DB::table('Actividades')
        ->where('idActividad', $actividadId)
        ->first();

    if (!$actividad) {
        return '⚠️ No se encontró la actividad ' . $actividadId;
    }

    return [
        'usuario_actual' => Auth::user()->tipoUsuario ?? 'sin tipo',
        'actividad' => $actividad,
    ];
});
