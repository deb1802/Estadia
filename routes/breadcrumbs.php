<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

/* =========================================================
| Helper genérico para mostrar títulos en breadcrumbs
========================================================= */
if (!function_exists('crumbTitle')) {
    function crumbTitle($item, string $fallback = 'Registro'): string {
        if (is_object($item)) {
            return $item->nombre
                ?? $item->titulo
                ?? $item->name
                ?? (isset($item->id) ? '#'.$item->id : $fallback);
        }
        if (is_array($item)) {
            return $item['nombre']
                ?? $item['titulo']
                ?? $item['name']
                ?? (isset($item['id']) ? '#'.$item['id'] : $fallback);
        }
        // string / int
        return '#'.(string) $item;
    }
}

/* =========================================================
|  DASHBOARDS (ROOTS por ROL)
========================================================= */
if (!Breadcrumbs::exists('admin.dashboard')) {
    Breadcrumbs::for('admin.dashboard', fn(Trail $t) =>
        $t->push('Dashboard', route('admin.dashboard')));
}
if (!Breadcrumbs::exists('medico.dashboard')) {
    Breadcrumbs::for('medico.dashboard', fn(Trail $t) =>
        $t->push('Dashboard', route('medico.dashboard')));
}
if (!Breadcrumbs::exists('paciente.dashboard')) {
    Breadcrumbs::for('paciente.dashboard', fn(Trail $t) =>
        $t->push('Dashboard', route('paciente.dashboard')));
}

/* =========================================================
|  ADMIN
========================================================= */

/** Recursos admin (index/create/show/edit) */
$adminResources = [
    'usuarios'          => 'Usuarios',
    'medicamentos'      => 'Medicamentos',
    'tutores'           => 'Tutores',
    'citas'             => 'Citas',
    'actividades_terap' => 'Actividades Terapéuticas',
];

foreach ($adminResources as $slug => $label) {
    $nameIndex = "admin.$slug.index";
    if (!Breadcrumbs::exists($nameIndex)) {
        Breadcrumbs::for($nameIndex, function (Trail $t) use ($label, $nameIndex) {
            $t->parent('admin.dashboard');
            $t->push($label, route($nameIndex));
        });
    }

    $nameCreate = "admin.$slug.create";
    if (!Breadcrumbs::exists($nameCreate)) {
        Breadcrumbs::for($nameCreate, function (Trail $t) use ($nameIndex) {
            $t->parent($nameIndex);
            $t->push('Crear');
        });
    }

    $nameShow = "admin.$slug.show";
    if (!Breadcrumbs::exists($nameShow)) {
        Breadcrumbs::for($nameShow, function (Trail $t, $modelOrId) use ($nameIndex) {
            $t->parent($nameIndex);
            $t->push(crumbTitle($modelOrId, 'Detalle'));
        });
    }

    $nameEdit = "admin.$slug.edit";
    if (!Breadcrumbs::exists($nameEdit)) {
        Breadcrumbs::for($nameEdit, function (Trail $t, $modelOrId) use ($nameIndex) {
            $t->parent($nameIndex);
            $t->push('Editar: '.crumbTitle($modelOrId));
        });
    }
}

/* Respaldo y restauracion */
if (!Breadcrumbs::exists('admin.backup.index')) {
    Breadcrumbs::for('admin.backup.index', fn(Trail $t) =>
        $t->parent('admin.dashboard')->push('Respaldo y restauración', route('admin.backup.index'))
    );
}

/* === Panel / Reportes / Backups / Recetas === */
if (!Breadcrumbs::exists('admin.panel.estadisticas')) {
    Breadcrumbs::for('admin.panel.estadisticas', fn(Trail $t) =>
        $t->parent('admin.dashboard')->push('Panel de estadísticas', route('admin.panel.estadisticas')));
}
if (!Breadcrumbs::exists('admin.reportes.index')) {
    Breadcrumbs::for('admin.reportes.index', fn(Trail $t) =>
        $t->parent('admin.dashboard')->push('Reportes', route('admin.reportes.index')));
}
if (!Breadcrumbs::exists('admin.reportes.pacientes.genero')) {
    Breadcrumbs::for('admin.reportes.pacientes.genero', fn(Trail $t) =>
        $t->parent('admin.reportes.index')->push('Pacientes por género', route('admin.reportes.pacientes.genero')));
}
if (!Breadcrumbs::exists('admin.backups.index')) {
    Breadcrumbs::for('admin.backups.index', fn(Trail $t) =>
        $t->parent('admin.dashboard')->push('Respaldos', route('admin.backups.index')));
}
if (!Breadcrumbs::exists('admin.backups.diag')) {
    Breadcrumbs::for('admin.backups.diag', fn(Trail $t) =>
        $t->parent('admin.backups.index')->push('Diagnóstico', route('admin.backups.diag')));
}
if (!Breadcrumbs::exists('admin.recetas.index')) {
    Breadcrumbs::for('admin.recetas.index', fn(Trail $t) =>
        $t->parent('admin.dashboard')->push('Recetas', route('admin.recetas.index')));
}
if (!Breadcrumbs::exists('admin.recetas.show')) {
    Breadcrumbs::for('admin.recetas.show', function (Trail $t, $idReceta) {
        $t->parent('admin.recetas.index');
        $t->push('Detalle #'.$idReceta);
    });
}
if (!Breadcrumbs::exists('admin.recetas.pdf')) {
    Breadcrumbs::for('admin.recetas.pdf', function (Trail $t, $idReceta) {
        $t->parent('admin.recetas.show', $idReceta);
        $t->push('PDF');
    });
}
// 📂 Listado de expedientes (ADMIN)
if (!Breadcrumbs::exists('admin.expedientes.index')) {
    Breadcrumbs::for('admin.expedientes.index', fn(Trail $t) =>
        $t->parent('admin.dashboard')
          ->push('Expedientes Clínicos', route('admin.expedientes.index')));
}
/* === NUEVOS REPORTES ADMIN === */

// 📊 Reporte de seguimiento de pacientes
if (!Breadcrumbs::exists('admin.reportes.seguimiento')) {
    Breadcrumbs::for('admin.reportes.seguimiento', fn(Trail $t) =>
        $t->parent('admin.reportes.index')->push('Seguimiento de pacientes', route('admin.reportes.seguimiento')));
}
if (!Breadcrumbs::exists('admin.reportes.seguimiento.excel')) {
    Breadcrumbs::for('admin.reportes.seguimiento.excel', function (Trail $t, $idPaciente) {
        $t->parent('admin.reportes.seguimiento');
        $t->push('Exportar Excel #'.$idPaciente, route('admin.reportes.seguimiento.excel', $idPaciente));
    });
}

// 📆 Reporte visual de citas por mes
if (!Breadcrumbs::exists('admin.reportes.citas.mes')) {
    Breadcrumbs::for('admin.reportes.citas.mes', fn(Trail $t) =>
        $t->parent('admin.reportes.index')->push('Citas por mes', route('admin.reportes.citas.mes')));
}

// 🧠 Reporte de clasificación emocional
if (!Breadcrumbs::exists('admin.reportes.emocional')) {
    Breadcrumbs::for('admin.reportes.emocional', fn(Trail $t) =>
        $t->parent('admin.reportes.index')->push('Clasificación emocional', route('admin.reportes.emocional')));
}
if (!Breadcrumbs::exists('admin.reportes.emocional.export')) {
    Breadcrumbs::for('admin.reportes.emocional.export', fn(Trail $t) =>
        $t->parent('admin.reportes.emocional')->push('Exportar'));
}

/* =========================================================
|  MÉDICO
========================================================= */
$medicoResources = [
    'pacientes'         => 'Pacientes',
    'medicamentos'      => 'Medicamentos',
    'actividades_terap' => 'Actividades Terapéuticas',
    'tutores'           => 'Tutores',
    'citas'             => 'Citas',
    'tests'             => 'Tests Psicológicos',
];

foreach ($medicoResources as $slug => $label) {
    $base = "medico.$slug";
    $nameIndex = "$base.index";
    if (!Breadcrumbs::exists($nameIndex)) {
        Breadcrumbs::for($nameIndex, function (Trail $t) use ($label, $nameIndex) {
            $t->parent('medico.dashboard');
            $t->push($label, route($nameIndex));
        });
    }
    $nameCreate = "$base.create";
    if (!Breadcrumbs::exists($nameCreate)) {
        Breadcrumbs::for($nameCreate, function (Trail $t) use ($nameIndex) {
            $t->parent($nameIndex);
            $t->push('Crear');
        });
    }
    $nameShow = "$base.show";
    if (!Breadcrumbs::exists($nameShow)) {
        Breadcrumbs::for($nameShow, function (Trail $t, $modelOrId) use ($nameIndex) {
            $t->parent($nameIndex);
            $t->push(crumbTitle($modelOrId, 'Detalle'));
        });
    }
    $nameEdit = "$base.edit";
    if (!Breadcrumbs::exists($nameEdit)) {
        Breadcrumbs::for($nameEdit, function (Trail $t, $modelOrId) use ($nameIndex) {
            $t->parent($nameIndex);
            $t->push('Editar: '.crumbTitle($modelOrId));
        });
    }
}

/* === Recetas médico === */
if (!Breadcrumbs::exists('medico.recetas.create')) {
    Breadcrumbs::for('medico.recetas.create', fn(Trail $t) =>
        $t->parent('medico.dashboard')->push('Recetas', route('medico.recetas.create'))->push('Crear'));
}
if (!Breadcrumbs::exists('medico.recetas.show')) {
    Breadcrumbs::for('medico.recetas.show', function (Trail $t, $idReceta) {
        $t->parent('medico.dashboard');
        $t->push('Recetas', route('medico.recetas.create'));
        $t->push('Detalle #'.$idReceta);
    });
}

/* === Medicamentos → Asignar === */
if (!Breadcrumbs::exists('medico.medicamentos.asignar')) {
    Breadcrumbs::for('medico.medicamentos.asignar', fn(Trail $t) =>
        $t->parent('medico.medicamentos.index')->push('Asignar'));
}

/* === Actividades terapéuticas → Asignar/Asignadas === */
if (!Breadcrumbs::exists('medico.actividades_terap.asignar')) {
    Breadcrumbs::for('medico.actividades_terap.asignar', fn(Trail $t) =>
        $t->parent('medico.actividades_terap.index')->push('Asignar'));
}
if (!Breadcrumbs::exists('medico.actividades_terap.asignadas')) {
    Breadcrumbs::for('medico.actividades_terap.asignadas', fn(Trail $t) =>
        $t->parent('medico.actividades_terap.index')->push('Asignadas'));
}

/* === Tests médico === */
if (!Breadcrumbs::exists('medico.tests.asignar.index')) {
    Breadcrumbs::for('medico.tests.asignar.index', fn(Trail $t) =>
        $t->parent('medico.tests.index')->push('Asignar'));
}
if (!Breadcrumbs::exists('medico.tests.builder.edit')) {
    Breadcrumbs::for('medico.tests.builder.edit', function (Trail $t, $idTest) {
        $t->parent('medico.tests.index');
        $t->push('Builder #'.$idTest);
    });
}
if (!Breadcrumbs::exists('medico.tests.asignaciones.show')) {
    Breadcrumbs::for('medico.tests.asignaciones.show', function (Trail $t, $idAsignacionTest) {
        $t->parent('medico.tests.index');
        $t->push('Asignación #'.$idAsignacionTest);
    });
}

/* === Notificaciones médico === */
if (!Breadcrumbs::exists('medico.notificaciones.index')) {
    Breadcrumbs::for('medico.notificaciones.index', fn(Trail $t) =>
        $t->parent('medico.dashboard')->push('Notificaciones', route('medico.notificaciones.index')));
}

/* === EXPEDIENTES clínicos médico === */
if (!Breadcrumbs::exists('medico.expedientes.index')) {
    Breadcrumbs::for('medico.expedientes.index', fn(Trail $t) =>
        $t->parent('medico.dashboard')->push('Expedientes', route('medico.expedientes.index')));
}
if (!Breadcrumbs::exists('medico.expedientes.show')) {
    Breadcrumbs::for('medico.expedientes.show', function (Trail $t, $id) {
        $t->parent('medico.expedientes.index');
        $t->push('Detalle #'.$id, route('medico.expedientes.show', $id));
    });
}
if (!Breadcrumbs::exists('medico.expedientes.create')) {
    Breadcrumbs::for('medico.expedientes.create', fn(Trail $t) =>
        $t->parent('medico.expedientes.index')->push('Crear'));
}
if (!Breadcrumbs::exists('medico.expedientes.edit')) {
    Breadcrumbs::for('medico.expedientes.edit', function (Trail $t, $id) {
        $t->parent('medico.expedientes.show', $id);
        $t->push('Editar');
    });
}

/* === Testimonios médico === */
if (!Breadcrumbs::exists('medico.testimonios.index')) {
    Breadcrumbs::for('medico.testimonios.index', fn(Trail $t) =>
        $t->parent('medico.dashboard')->push('Testimonios', route('medico.testimonios.index')));
}
/* === Seguimiento de Pacientes (Médico) === */
if (!Breadcrumbs::exists('medico.seguimiento.index')) {
    Breadcrumbs::for('medico.seguimiento.index', fn(Trail $t) =>
        $t->parent('medico.dashboard')->push('Seguimiento de pacientes', route('medico.seguimiento.index')));
}

if (!Breadcrumbs::exists('medico.seguimiento.show')) {
    Breadcrumbs::for('medico.seguimiento.show', function (Trail $t, $idPaciente) {
        $t->parent('medico.seguimiento.index');
        $t->push('Detalle del paciente #'.$idPaciente, route('medico.seguimiento.show', $idPaciente));
    });

    // 🧠 Emociones registradas (médico)
if (!Breadcrumbs::exists('medico.emociones.index')) {
    Breadcrumbs::for('medico.emociones.index', fn(Trail $t) =>
        $t->parent('medico.dashboard')->push('Registro emocional', route('medico.emociones.index')));
}

}

/* =========================================================
|  PACIENTE
========================================================= */
if (!Breadcrumbs::exists('paciente.testimonios.index')) {
    Breadcrumbs::for('paciente.testimonios.index', fn(Trail $t) =>
        $t->parent('paciente.dashboard')->push('Testimonios', route('paciente.testimonios.index')));
}
if (!Breadcrumbs::exists('paciente.tutores.index')) {
    Breadcrumbs::for('paciente.tutores.index', fn(Trail $t) =>
        $t->parent('paciente.dashboard')->push('Tutores', route('paciente.tutores.index')));
}
if (!Breadcrumbs::exists('paciente.tutores.show')) {
    Breadcrumbs::for('paciente.tutores.show', function ($trail, $tutor) {
        $trail->parent('paciente.tutores.index');
        $trail->push('Detalles');
    });
}
if (!Breadcrumbs::exists('paciente.recetas.index')) {
    Breadcrumbs::for('paciente.recetas.index', fn(Trail $t) =>
        $t->parent('paciente.dashboard')->push('Mis recetas', route('paciente.recetas.index')));
}
if (!Breadcrumbs::exists('paciente.recetas.show')) {
    Breadcrumbs::for('paciente.recetas.show', function (Trail $t, $idReceta) {
        $t->parent('paciente.recetas.index');
        $t->push('Detalle #'.$idReceta);
    });
}
if (!Breadcrumbs::exists('paciente.recetas.pdf')) {
    Breadcrumbs::for('paciente.recetas.pdf', function (Trail $t, $idReceta) {
        $t->parent('paciente.recetas.show', $idReceta);
        $t->push('PDF');
    });
}
if (!Breadcrumbs::exists('paciente.actividades.index')) {
    Breadcrumbs::for('paciente.actividades.index', fn(Trail $t) =>
        $t->parent('paciente.dashboard')->push('Mis actividades', route('paciente.actividades.index')));
}
if (!Breadcrumbs::exists('paciente.tests.index')) {
    Breadcrumbs::for('paciente.tests.index', fn(Trail $t) =>
        $t->parent('paciente.dashboard')->push('Mis tests', route('paciente.tests.index')));
}
if (!Breadcrumbs::exists('paciente.tests.responder')) {
    Breadcrumbs::for('paciente.tests.responder', function (Trail $t, $idAsignacionTest) {
        $t->parent('paciente.tests.index');
        $t->push('Responder #'.$idAsignacionTest);
    });
}
if (!Breadcrumbs::exists('paciente.tests.recibido')) {
    Breadcrumbs::for('paciente.tests.recibido', function (Trail $t, $idAsignacionTest) {
        $t->parent('paciente.tests.index');
        $t->push('Enviado #'.$idAsignacionTest);
    });
}
