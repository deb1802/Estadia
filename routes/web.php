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
use App\Http\Controllers\Medico\RecetaController;
use App\Http\Controllers\Medico\AsignacionMedicamentoController;
use App\Http\Controllers\Paciente\NotificacionesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Medico\ActividadesAsignadasController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\ReportePacientesController;
use App\Http\Controllers\Admin\RecetaAdminController;
use App\Http\Controllers\Medico\TestController;
use App\Http\Controllers\Medico\TestBuilderController;
use App\Http\Controllers\Medico\AsignacionTestController;
use App\Http\Controllers\Medico\ExpedienteController;


Route::pattern('actividad', '[0-9]+');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Rutas principales de la aplicación MindWare
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

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

        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

        Route::resource('usuarios', UsuarioController::class);
        Route::resource('medicamentos', MedicamentoController::class);
        Route::resource('tutores', TutorController::class)->names('tutores');

        Route::resource('citas', App\Http\Controllers\CitaController::class)
            ->names('citas')
            ->parameters(['citas' => 'cita']);

        Route::resource('actividades_terap', ActividadesTController::class)
            ->parameters(['actividades_terap' => 'actividad']);

        // 📊 Panel de estadísticas y reportes
        Route::get('/panel-estadisticas', fn() => view('admin.resumen_admin'))
            ->name('panel.estadisticas');
        Route::get('/reportes', fn() => view('admin.reportes.index'))->name('reportes.index');
        Route::get('/reportes/pacientes-genero', [ReportePacientesController::class, 'pacientesPorGenero'])->name('reportes.pacientes.genero');
        Route::get('/reportes/pacientes-genero/data', [ReportePacientesController::class, 'pacientesPorGeneroData'])->name('reportes.pacientes.genero.data');

        // 💊 Recetas
        Route::get('/recetas', [RecetaAdminController::class, 'index'])->name('recetas.index');
        Route::get('/recetas/{idReceta}', [RecetaAdminController::class, 'show'])->name('recetas.show');
        Route::get('/recetas/{idReceta}/pdf', [RecetaAdminController::class, 'pdf'])->name('recetas.pdf');

        // 📂 Expedientes clínicos (Administrador)
Route::get('/expedientes', [App\Http\Controllers\Admin\ExpedienteAdminController::class, 'index'])
    ->name('expedientes.index');

Route::get('/expedientes/{id}', [App\Http\Controllers\Admin\ExpedienteAdminController::class, 'show'])
    ->name('expedientes.show');

Route::delete('/expedientes/{id}', [App\Http\Controllers\Admin\ExpedienteAdminController::class, 'destroy'])
    ->name('expedientes.destroy');

    });



/* 🩺 Sección del MÉDICO */
Route::middleware(['auth', 'rol:medico'])
    ->prefix('medico')
    ->name('medico.')
    ->group(function () {

        Route::get('/dashboard', fn() => view('medico.dashboard'))->name('dashboard');

        Route::resource('pacientes', PacienteController::class);

        // 💊 Recetas
        Route::prefix('recetas')->name('recetas.')->group(function () {
            Route::get('crear', [RecetaController::class, 'create'])->name('create');
            Route::post('/', [RecetaController::class, 'store'])->name('store');
            Route::get('{idReceta}/detalle', [RecetaController::class, 'detalle'])->name('detalle');
            Route::post('{idReceta}/detalle', [RecetaController::class, 'agregarDetalle'])->name('detalle.agregar');
            Route::delete('{idReceta}/detalle/{idDetalle}', [RecetaController::class, 'borrarDetalle'])->name('detalle.borrar');
            Route::get('{idReceta}/pdf', [RecetaController::class, 'pdf'])->name('pdf');
            Route::get('{idReceta}', [RecetaController::class, 'show'])->name('show');
        });

        // 💊 Asignación de medicamentos
        Route::prefix('medicamentos')->name('medicamentos.')->group(function () {
            Route::get('asignar', [AsignacionMedicamentoController::class, 'create'])->name('asignar');
            Route::post('asignar', [AsignacionMedicamentoController::class, 'store'])->name('asignar.store');
        });
        Route::resource('medicamentos', MedicamentoController::class);

        // 🎯 Actividades terapéuticas
        Route::prefix('actividades_terap')->name('actividades_terap.')->group(function () {
            Route::get('asignar', [AsignacionActividadController::class, 'create'])->name('asignar');
            Route::post('asignar', [AsignacionActividadController::class, 'store'])->name('asignar.store');
            Route::get('asignadas', [ActividadesAsignadasController::class, 'index'])->name('asignadas');
        });
        Route::resource('actividades_terap', ActividadesTController::class)
            ->parameters(['actividades_terap' => 'actividad']);

        // 👨‍⚕️ Tutores
        Route::resource('tutores', TutorController::class)->names('tutores');

        // 🗓️ Citas
        Route::resource('citas', App\Http\Controllers\CitaController::class)
            ->names('citas')
            ->parameters(['citas' => 'cita']);
        Route::patch('citas/{idCita}/estado', [App\Http\Controllers\CitaController::class, 'actualizarEstado'])
            ->name('citas.actualizarEstado');

        // 🧠 Tests psicológicos
        Route::get('tests/asignar',  [AsignacionTestController::class, 'index'])->name('tests.asignar.index');
        Route::post('tests/asignar', [AsignacionTestController::class, 'store'])->name('tests.asignar.store');
        Route::get('tests/{idTest}/builder', [TestBuilderController::class, 'edit'])->name('tests.builder.edit');
        Route::put('tests/{idTest}/builder', [TestBuilderController::class, 'update'])->name('tests.builder.update');
        Route::pattern('idTest', '[0-9]+');
        Route::resource('tests', TestController::class)
            ->parameters(['tests' => 'idTest'])
            ->names('tests');

        // 🔔 Notificaciones
        Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
            Route::get('/', [App\Http\Controllers\Medico\NotificacionesController::class, 'index'])->name('index');
            Route::post('/{id}/leer', [App\Http\Controllers\Medico\NotificacionesController::class, 'markRead'])->name('markRead');
            Route::post('/leertodas', [App\Http\Controllers\Medico\NotificacionesController::class, 'markAllRead'])->name('markAll');
            Route::get('/fragment', [App\Http\Controllers\Medico\NotificacionesController::class, 'fragment'])->name('fragment');
        });

        // 🧩 Detalle de test respondido + Confirmar diagnóstico
        Route::get('tests/asignaciones/{idAsignacionTest}', 
            [App\Http\Controllers\Medico\AsignacionTestController::class, 'showDetalle']
        )->name('tests.asignaciones.show');
        Route::post('tests/asignaciones/{idAsignacionTest}/confirmar',
            [App\Http\Controllers\Medico\AsignacionTestController::class, 'confirmar']
        )->name('tests.asignaciones.confirmar');

        // ============================================================
        // 📂 EXPEDIENTES CLÍNICOS
        // ============================================================
        Route::resource('expedientes', ExpedienteController::class);
    });

/* 💬 Sección del PACIENTE */
Route::middleware(['auth', 'rol:paciente'])
    ->prefix('paciente')
    ->name('paciente.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('paciente.dashboard'))->name('dashboard');
        Route::get('/testimonios', [TestimonioController::class, 'index'])->name('testimonios.index');
        Route::post('/testimonios', [TestimonioController::class, 'store'])->name('testimonios.store');
        Route::post('/testimonios/{idTestimonio}/respuestas', [RespuestaTestimonioController::class, 'store'])
            ->name('testimonios.respuestas.store');
        Route::get('/tutores', [TutorController::class, 'index'])->name('tutores.index');
        Route::post('/notificaciones/{id}/leer', [NotificacionesController::class, 'markRead'])->name('notificaciones.markRead');
        Route::post('/notificaciones/leertodas', [NotificacionesController::class, 'markAllRead'])->name('notificaciones.markAll');
    });

require __DIR__.'/auth.php';

/* 🔹 Breadcrumbs */
require_once __DIR__ . '/breadcrumbs.php';