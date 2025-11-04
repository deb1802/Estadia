<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard/summary
     * Filtros opcionales:
     * - sexo=masculino|femenino|otro   (filtra métricas relacionadas con Pacientes)
     * - from=YYYY-MM-DD                (fecha inicio)
     * - to=YYYY-MM-DD                  (fecha fin)
     * - estado_cita=programada|realizada|cancelada  (filtra Citas)
     */
    public function summary(Request $request)
    {
        // ====== Filtros ======
        $sexo        = trim((string)$request->query('sexo', '')); // '', masculino, femenino, otro
        $from        = $request->query('from');                   // YYYY-MM-DD | null
        $to          = $request->query('to');                     // YYYY-MM-DD | null
        $estadoCita  = trim((string)$request->query('estado_cita', '')); // '', programada|realizada|cancelada

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
        $toDate   = $to   ? Carbon::parse($to)->endOfDay()     : null;

        // ========= TARJETAS =========

        // Usuarios por rol (tipoUsuario) con posible filtro de fechas usando fechaRegistro
        $usuariosPorRol = $this->usuariosPorRol($fromDate, $toDate);
        $totalUsuarios  = array_sum($usuariosPorRol);

        // Totales de Médicos y Pacientes (si se desea tomar en cuenta fechas, usamos fechaRegistro del Usuario)
        $totalMedicos   = $this->totalMedicos($fromDate, $toDate);
        $totalPacientes = $this->totalPacientes($fromDate, $toDate, $sexo);

        // Actividades disponibles (no hay columna de fechas -> total simple)
        $totalActividades = Schema::hasTable('Actividades')
            ? (int) DB::table('Actividades')->count() : 0;

        // Tests psicológicos respondidos (AsignacionesTest con fechaRespuesta NOT NULL).
        $testsRespondidos = $this->testsRespondidos($fromDate, $toDate, $sexo);

        // ========= GRÁFICAS =========

        // Pacientes por sexo (absolutos y porcentaje)
        [$pacSexoAbs, $pacSexoPct] = $this->pacientesPorSexo($fromDate, $toDate);

        // Citas por estado (programada/realizada/cancelada) con filtros por fecha y sexo
        $citasPorEstado = $this->citasPorEstado($fromDate, $toDate, $estadoCita, $sexo);

        // Pastel por estado emocional (primer elemento del JSON emocionesExperimentadas)
        $emocionPastel = $this->emocionPastel($fromDate, $toDate, $sexo);

        // Top medicamentos más recetados (por conteo de filas de Detalle_Medicamento dentro del rango de RecetasMedicas.fecha)
        $topMedicamentos = $this->topMedicamentos($fromDate, $toDate);

        return response()->json([
            'filters' => [
                'sexo'        => $sexo,
                'from'        => $fromDate?->toDateString(),
                'to'          => $toDate?->toDateString(),
                'estado_cita' => $estadoCita,
            ],
            'cards' => [
                'usuarios_total'     => $totalUsuarios,
                'usuarios_por_rol'   => $usuariosPorRol,     // { administrador: X, medico: Y, paciente: Z, ... }
                'medicos_total'      => $totalMedicos,
                'pacientes_total'    => $totalPacientes,
                'actividades_total'  => $totalActividades,
                'tests_total'        => $testsRespondidos,    // puedes mostrarlo como "respondidos"
                'respuestas_test'    => $testsRespondidos,
            ],
            'charts' => [
                'pacientes_por_sexo' => [
                    'absolutos'  => $pacSexoAbs,  // { masculino:n, femenino:n, otro:n }
                    'porcentaje' => $pacSexoPct,  // { masculino:%, femenino:%, otro:% }
                ],
                'citas_por_estado'   => $citasPorEstado,   // { programada:n, realizada:n, cancelada:n }
                'emocion_pastel'     => $emocionPastel,    // { feliz:n, triste:n, ... } o '(sin_dato)'
                'top_medicamentos'   => $topMedicamentos,  // [ { nombre, total }, ... ]
            ],
        ], 200);
    }

    /* ===================== Helpers adaptados a tu esquema ===================== */

    private function usuariosPorRol(?Carbon $from, ?Carbon $to): array
    {
        if (!Schema::hasTable('Usuarios')) return ['administrador'=>0,'medico'=>0,'paciente'=>0];

        $q = DB::table('Usuarios');
        if ($from) $q->where('fechaRegistro', '>=', $from);
        if ($to)   $q->where('fechaRegistro', '<=', $to);

        $rows = $q->select('tipoUsuario', DB::raw('COUNT(*) as total'))
                  ->groupBy('tipoUsuario')
                  ->pluck('total', 'tipoUsuario')
                  ->toArray();

        // Normaliza claves esperadas y añade extras si existieran
        $base = ['administrador'=>0,'medico'=>0,'paciente'=>0];
        foreach ($rows as $rol => $n) {
            $base[$rol] = (int)$n;
        }
        return $base;
    }

    private function totalMedicos(?Carbon $from, ?Carbon $to): int
    {
        if (!Schema::hasTable('Medicos')) return 0;

        // Si hay Usuarios, aplicamos rango de fechas sobre fechaRegistro del usuario ligado
        if (Schema::hasTable('Usuarios')) {
            $q = DB::table('Medicos as m')
                ->join('Usuarios as u', 'u.idUsuario', '=', 'm.usuario_id');

            if ($from) $q->where('u.fechaRegistro', '>=', $from);
            if ($to)   $q->where('u.fechaRegistro', '<=', $to);

            return (int) $q->count();
        }

        return (int) DB::table('Medicos')->count();
    }

    private function totalPacientes(?Carbon $from, ?Carbon $to, string $sexo): int
    {
        if (!Schema::hasTable('Pacientes') || !Schema::hasTable('Usuarios')) return 0;

        $q = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id');

        if ($sexo !== '' && in_array($sexo, ['masculino','femenino','otro'], true)) {
            $q->whereRaw('LOWER(TRIM(u.sexo)) = ?', [$sexo]);
        }
        if ($from) $q->where('u.fechaRegistro', '>=', $from);
        if ($to)   $q->where('u.fechaRegistro', '<=', $to);

        return (int) $q->count();
    }


    private function pacientesPorSexo(?Carbon $from, ?Carbon $to): array
{
    if (!Schema::hasTable('Pacientes') || !Schema::hasTable('Usuarios')) {
        $abs = ['masculino'=>0,'femenino'=>0,'otro'=>0];
        return [$abs, $abs];
    }

    $q = DB::table('Pacientes as p')
        ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id');

    if ($from) $q->where('u.fechaRegistro', '>=', $from);
    if ($to)   $q->where('u.fechaRegistro', '<=', $to);

    // 🔑 Normaliza también en el filtro (evita perder registros por "Masculino"/" femenino ")
    $q->whereRaw("LOWER(TRIM(COALESCE(u.sexo, ''))) IN ('masculino','femenino','otro')");

    // 🔑 Y agrupa por el mismo valor normalizado
    $rows = $q->selectRaw("LOWER(TRIM(u.sexo)) as sexo_norm, COUNT(*) as total")
              ->groupBy('sexo_norm')
              ->pluck('total', 'sexo_norm')
              ->toArray();

    $h = (int)($rows['masculino'] ?? 0);
    $m = (int)($rows['femenino']  ?? 0);
    $o = (int)($rows['otro']      ?? 0);

    $abs = ['masculino'=>$h, 'femenino'=>$m, 'otro'=>$o];
    $sum = $h + $m + $o;

    $pct = [
        'masculino' => $sum ? (int) round(($h / $sum) * 100) : 0,
        'femenino'  => $sum ? (int) round(($m / $sum) * 100) : 0,
        'otro'      => $sum ? (int) round(($o / $sum) * 100) : 0,
    ];

    return [$abs, $pct];
}



    private function citasPorEstado(?Carbon $from, ?Carbon $to, string $estado, string $sexo): array
    {
        if (!Schema::hasTable('Citas')) return [];

        $q = DB::table('Citas as c');

        // Fechas: usa DATETIME Citas.fechaHora
        if ($from) $q->where('c.fechaHora', '>=', $from);
        if ($to)   $q->where('c.fechaHora', '<=', $to);

        // Estado puntual
        if ($estado !== '' && in_array($estado, ['programada','realizada','cancelada'], true)) {
            $q->where('c.estado', $estado);
        }

        // Filtro por sexo del paciente (Citas -> Pacientes -> Usuarios)
        if ($sexo !== '' && in_array($sexo, ['masculino','femenino','otro'], true) &&
            Schema::hasTable('Pacientes') && Schema::hasTable('Usuarios')) {
            $q->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
              ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
              ->where('u.sexo', $sexo);
        }

        return $q->select('c.estado', DB::raw('COUNT(*) as total'))
                 ->groupBy('c.estado')
                 ->pluck('total', 'c.estado')
                 ->toArray();
    }

    private function emocionPastel(?Carbon $from, ?Carbon $to, string $sexo): array
    {
        if (!Schema::hasTable('Emociones')) return [];

        $q = DB::table('Emociones as e');

        // Fechas: usa DATETIME Emociones.fechaHoraRegistro
        if ($from) $q->where('e.fechaHoraRegistro', '>=', $from);
        if ($to)   $q->where('e.fechaHoraRegistro', '<=', $to);

        // SIN filtro por sexo: no se hacen joins con Pacientes/Usuarios

        // Tomamos el PRIMER elemento del arreglo JSON en emocionesExperimentadas (guardado como TEXT).
        // Si no es JSON válido o viene vacío -> '(sin_dato)'
        $rows = $q->selectRaw("
                    CASE
                        WHEN JSON_VALID(e.emocionesExperimentadas)
                        THEN COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(e.emocionesExperimentadas, '$[0]')), ''), '(sin_dato)')
                        ELSE '(sin_dato)'
                    END as emocion,
                    COUNT(*) as total
                ")
                ->groupBy('emocion')
                ->pluck('total', 'emocion')
                ->toArray();

        return $rows;
    }


    private function topMedicamentos(?Carbon $from, ?Carbon $to): array
    {
        if (!Schema::hasTable('Detalle_Medicamento') ||
            !Schema::hasTable('RecetasMedicas') ||
            !Schema::hasTable('Medicamentos')) {
            return [];
        }

        $q = DB::table('Detalle_Medicamento as d')
            ->join('RecetasMedicas as r', 'r.idReceta', '=', 'd.fkReceta')
            ->leftJoin('Medicamentos as m', 'm.idMedicamento', '=', 'd.fkMedicamento');

        // Filtramos por fecha (DATE) de la receta
        if ($from) $q->where('r.fecha', '>=', $from->toDateString());
        if ($to)   $q->where('r.fecha', '<=', $to->toDateString());

        $rows = $q->selectRaw('COALESCE(m.nombre, "(sin_nombre)") as nombre, COUNT(*) as total')
                  ->groupBy('nombre')
                  ->orderByDesc('total')
                  ->limit(8)
                  ->get();

        return $rows->map(fn($r) => ['nombre' => $r->nombre, 'total' => (int)$r->total])->toArray();
    }

        private function testsRespondidos(?Carbon $from, ?Carbon $to, string $sexo): int
    {
        if (!Schema::hasTable('AsignacionesTest')) return 0;

        // Solo cuenta asignaciones con respuesta (sin filtro de fechas).
        $q = DB::table('AsignacionesTest as a')
            ->whereNotNull('a.fechaRespuesta');

        // Si se pide por sexo, une con Pacientes y Usuarios para filtrar por u.sexo.
        if ($sexo !== '' 
            && in_array($sexo, ['masculino','femenino','otro'], true)
            && Schema::hasTable('Pacientes') 
            && Schema::hasTable('Usuarios')) 
        {
            $q->join('Pacientes as p', 'p.id', '=', 'a.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('u.sexo', $sexo);
        }

        return (int) $q->count();
    }

}
