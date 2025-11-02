<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

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
|  ROOTS por ROL
|========================================================= */
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
|========================================================= */

/** Recursos admin (index/create/show/edit) */
$adminResources = [
    'usuarios'         => 'Usuarios',
    'medicamentos'     => 'Medicamentos',
    'tutores'          => 'Tutores',
    'citas'            => 'Citas',
    'actividades_terap'=> 'Actividades Terapéuticas',
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

    // admin.<modulo>.show
    $nameShow = "admin.$slug.show";
    if (!Breadcrumbs::exists($nameShow)) {
        Breadcrumbs::for($nameShow, function (Trail $t, $modelOrId) use ($nameIndex) {
            $t->parent($nameIndex);
            $t->push(crumbTitle($modelOrId, 'Detalle'));
        });
    }

    // admin.<modulo>.edit
    $nameEdit = "admin.$slug.edit";
    if (!Breadcrumbs::exists($nameEdit)) {
        Breadcrumbs::for($nameEdit, function (Trail $t, $modelOrId) use ($nameIndex) {
            $t->parent($nameIndex);
            $t->push('Editar: '.crumbTitle($modelOrId));
        });
    }

}

/** Panel/Reportes/Backups/Recetas (GET) */
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

/* =========================================================
|  MÉDICO
|========================================================= */

/** Recursos médico */
$medicoResources = [
    'pacientes'         => 'Pacientes',
    'medicamentos'      => 'Medicamentos',
    'actividades_terap' => 'Actividades Terapéuticas',
    'tutores'           => 'Tutores',
    'citas'             => 'Citas',
    'tests'             => 'Tests Psicológicos', // resource principal
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
// medico.<modulo>.show
$nameShow = "$base.show";
if (!Breadcrumbs::exists($nameShow)) {
    Breadcrumbs::for($nameShow, function (Trail $t, $modelOrId) use ($nameIndex) {
        $t->parent($nameIndex);
        $t->push(crumbTitle($modelOrId, 'Detalle'));
    });
}

// medico.<modulo>.edit
$nameEdit = "$base.edit";
if (!Breadcrumbs::exists($nameEdit)) {
    Breadcrumbs::for($nameEdit, function (Trail $t, $modelOrId) use ($nameIndex) {
        $t->parent($nameIndex);
        $t->push('Editar: '.crumbTitle($modelOrId));
    });
}


}

/** Médico → Recetas (grupo personalizado) */
if (!Breadcrumbs::exists('medico.recetas.create')) {
    Breadcrumbs::for('medico.recetas.create', fn(Trail $t) =>
        $t->parent('medico.dashboard')->push('Recetas', route('medico.recetas.create'))->push('Crear'));
}
if (!Breadcrumbs::exists('medico.recetas.show')) {
    Breadcrumbs::for('medico.recetas.show', function (Trail $t, $idReceta) {
        $t->parent('medico.dashboard');
        $t->push('Recetas', route('medico.recetas.create')); // no tienes index; usamos create como lista/entrada
        $t->push('Detalle #'.$idReceta);
    });
}
if (!Breadcrumbs::exists('medico.recetas.detalle')) {
    Breadcrumbs::for('medico.recetas.detalle', function (Trail $t, $idReceta) {
        $t->parent('medico.recetas.show', $idReceta);
        $t->push('Detalle');
    });
}
if (!Breadcrumbs::exists('medico.recetas.pdf')) {
    Breadcrumbs::for('medico.recetas.pdf', function (Trail $t, $idReceta) {
        $t->parent('medico.recetas.show', $idReceta);
        $t->push('PDF');
    });
}

/** Médico → Medicamentos → Asignar (GET) */
if (!Breadcrumbs::exists('medico.medicamentos.asignar')) {
    Breadcrumbs::for('medico.medicamentos.asignar', fn(Trail $t) =>
        $t->parent('medico.medicamentos.index')->push('Asignar'));
}

/** Médico → Actividades Terap. → Asignar/Asignadas */
if (!Breadcrumbs::exists('medico.actividades_terap.asignar')) {
    Breadcrumbs::for('medico.actividades_terap.asignar', fn(Trail $t) =>
        $t->parent('medico.actividades_terap.index')->push('Asignar'));
}
if (!Breadcrumbs::exists('medico.actividades_terap.asignadas')) {
    Breadcrumbs::for('medico.actividades_terap.asignadas', fn(Trail $t) =>
        $t->parent('medico.actividades_terap.index')->push('Asignadas'));
}

/** Médico → Tests: Asignar, Builder, Asignaciones */
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

/** Médico → Notificaciones (vista) */
if (!Breadcrumbs::exists('medico.notificaciones.index')) {
    Breadcrumbs::for('medico.notificaciones.index', fn(Trail $t) =>
        $t->parent('medico.dashboard')->push('Notificaciones', route('medico.notificaciones.index')));
}

/* =========================================================
|  PACIENTE
|========================================================= */
if (!Breadcrumbs::exists('paciente.testimonios.index')) {
    Breadcrumbs::for('paciente.testimonios.index', fn(Trail $t) =>
        $t->parent('paciente.dashboard')->push('Testimonios', route('paciente.testimonios.index')));
}
if (!Breadcrumbs::exists('paciente.tutores.index')) {
    Breadcrumbs::for('paciente.tutores.index', fn(Trail $t) =>
        $t->parent('paciente.dashboard')->push('Tutores', route('paciente.tutores.index')));
}

/** Paciente → Recetas */
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

/** Paciente → Mis Actividades */
if (!Breadcrumbs::exists('paciente.actividades.index')) {
    Breadcrumbs::for('paciente.actividades.index', fn(Trail $t) =>
        $t->parent('paciente.dashboard')->push('Mis actividades', route('paciente.actividades.index')));
}

/** Paciente → Tests asignados */
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
