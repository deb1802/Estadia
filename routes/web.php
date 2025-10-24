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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Paciente\NotificacionesController;
use App\Http\Controllers\Medico\RecetaController;
use App\Http\Controllers\Medico\AsignacionMedicamentoController;

Route::pattern('actividad', '[0-9]+');

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
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // 👥 CRUD de usuarios
        Route::resource('usuarios', UsuarioController::class);

        // 💊 CRUD de medicamentos
        Route::resource('medicamentos', MedicamentoController::class);

        // 📘 CRUD de tutores (nuevo)
        Route::resource('tutores', TutorController::class)
            ->names('tutores'); // ✅ cambia nombres internos a tutores.*

        // 🧘‍♀️ Actividades terapéuticas (ambos roles)
        Route::resource('actividades_terap', ActividadesTController::class)
            ->parameters(['actividades_terap' => 'actividad']);

        // 📊 Panel de estadísticas
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
        Route::get('/dashboard', fn() => view('medico.dashboard'))->name('dashboard');

        // CRUD de pacientes
        Route::resource('pacientes', PacienteController::class);

        // ============================================================
        // 📄 RECETAS MÉDICAS (cabecera + detalle + ver + pdf)
        // ============================================================
        Route::prefix('recetas')->name('recetas.')->group(function () {
            // Crear CABECERA (viene desde botón en show de paciente: ?paciente=ID)
            Route::get('crear', [RecetaController::class, 'create'])->name('create');
            Route::post('/',     [RecetaController::class, 'store'])->name('store');

            // DETALLE (agregar líneas de medicamentos a una receta)
            Route::get('{idReceta}/detalle',                 [RecetaController::class, 'detalle'])->name('detalle');
            Route::post('{idReceta}/detalle',                [RecetaController::class, 'agregarDetalle'])->name('detalle.agregar');
            Route::delete('{idReceta}/detalle/{idDetalle}',  [RecetaController::class, 'borrarDetalle'])->name('detalle.borrar');

            // 🧾 PDF (más específico que {idReceta}, por eso va antes)
            Route::get('{idReceta}/pdf', [RecetaController::class, 'pdf'])->name('pdf');

            // 👁️ Ver receta (HTML)
            Route::get('{idReceta}', [RecetaController::class, 'show'])->name('show');
        });

        // ============================================================
        // 💊 ASIGNACIÓN DIRECTA DESDE CATÁLOGO DE MEDICAMENTOS
        // (usa querystring ?medicamento=ID, no choca con resource)
        // ============================================================
        Route::prefix('medicamentos')->name('medicamentos.')->group(function () {
            Route::get('asignar',  [AsignacionMedicamentoController::class, 'create'])->name('asignar');
            Route::post('asignar', [AsignacionMedicamentoController::class, 'store'])->name('asignar.store');
        });

        // CRUD de medicamentos (resource actual)
        Route::resource('medicamentos', MedicamentoController::class);

        // ✅ ASIGNACIÓN de actividades (ya existente)
        Route::prefix('actividades_terap')->name('actividades_terap.')->group(function () {
            Route::get('asignar',  [AsignacionActividadController::class, 'create'])->name('asignar');
            Route::post('asignar', [AsignacionActividadController::class, 'store'])->name('asignar.store');
        });

        // 🧘‍♀️ Actividades terapéuticas (resource)
        Route::resource('actividades_terap', ActividadesTController::class)
            ->parameters(['actividades_terap' => 'actividad']);

        // 👨‍⚕️ CRUD de tutores
        Route::resource('tutores', TutorController::class)
            ->names('tutores')
            ->parameters(['tutores' => 'tutor']);
    });




/* 💬 Sección del PACIENTE */
Route::middleware(['auth', 'rol:paciente'])
    ->prefix('paciente')
    ->name('paciente.')
    ->group(function () {

        // 🏠 Dashboard principal
        Route::get('/dashboard', fn() => view('paciente.dashboard'))->name('dashboard');

        // 💬 Foro de testimonios
        Route::get('/testimonios', [TestimonioController::class, 'index'])->name('testimonios.index');
        Route::post('/testimonios', [TestimonioController::class, 'store'])->name('testimonios.store');
        Route::post('/testimonios/{idTestimonio}/respuestas', [RespuestaTestimonioController::class, 'store'])
            ->name('testimonios.respuestas.store');

        // 👨‍🏫 Vista de tutores (solo lectura)
        Route::get('/tutores', [TutorController::class, 'index'])->name('tutores.index');

        // 🔔 Notificaciones
        Route::post('/notificaciones/{id}/leer', [NotificacionesController::class, 'markRead'])
            ->name('notificaciones.markRead');
        Route::post('/notificaciones/leertodas', [NotificacionesController::class, 'markAllRead'])
            ->name('notificaciones.markAll');

        // 🧾 📄 RECETAS MÉDICAS (solo las del paciente autenticado)
        Route::prefix('recetas')->name('recetas.')->group(function () {
            Route::get('/', [App\Http\Controllers\Paciente\RecetaPacienteController::class, 'index'])->name('index');
            Route::get('/{idReceta}', [App\Http\Controllers\Paciente\RecetaPacienteController::class, 'show'])->name('show');
            Route::get('/{idReceta}/pdf', [App\Http\Controllers\Paciente\RecetaPacienteController::class, 'pdf'])->name('pdf');
        });

        // ✅ 🧘‍♀️ ACTIVIDADES ASIGNADAS AL PACIENTE
        Route::prefix('mis-actividades')->name('actividades.')->group(function () {
            // Listado (con filtro opcional ?estado=pendiente|completada)
            Route::get('/', [App\Http\Controllers\Paciente\ActividadesAsignadasController::class, 'index'])
                ->name('index');

            // Marcar una asignación como completada
            Route::patch('/{asignacion}/completar', [App\Http\Controllers\Paciente\ActividadesAsignadasController::class, 'completar'])
                ->name('completar');
        });
    });




/* 🛡️ Incluye las rutas de autenticación de Breeze */
require __DIR__.'/auth.php';


