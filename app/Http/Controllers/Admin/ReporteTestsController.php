<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AsignacionesTestsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteTestsController extends Controller
{
    /**
     * Query base: joins + filtros (SIN select de columnas).
     * Tablas:
     *  - AsignacionesTest (a)
     *  - Tests (t)
     *  - Medicos (m) -> Usuarios (um)
     *  - Pacientes (p) -> Usuarios (up)
     */
    protected function baseQuery(Request $request)
    {
        $q = DB::table('AsignacionesTest as a')
            ->join('Tests as t', 't.idTest', '=', 'a.fkTest')
            ->join('Medicos as m', 'm.id', '=', 't.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id') // datos médico
            ->join('Pacientes as p', 'p.id', '=', 'a.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id'); // datos paciente

        /* ===== Filtros ===== */
        // tipo (por nombre del test o tipoTrastorno)
        if ($tipo = trim((string) $request->get('tipo'))) {
            $q->where(function ($w) use ($tipo) {
                $w->where('t.nombre', 'like', "%{$tipo}%")
                  ->orWhere('t.tipoTrastorno', 'like', "%{$tipo}%");
            });
        }

        // estado (calculado con fechas)
        if ($estado = trim((string) $request->get('estado'))) {
            if ($estado === 'completado') {
                $q->whereNotNull('a.fechaRespuesta');
            } elseif ($estado === 'vencido') {
                $q->whereNull('a.fechaRespuesta')
                  ->whereRaw('a.fechaAsignacion < (NOW() - INTERVAL 30 DAY)');
            } elseif ($estado === 'pendiente') {
                $q->whereNull('a.fechaRespuesta')
                  ->whereRaw('a.fechaAsignacion >= (NOW() - INTERVAL 30 DAY)');
            }
        }

        // diagnóstico preliminar
        if ($diag = trim((string) $request->get('diagnostico'))) {
            $q->where('a.diagnosticoSugerido', 'like', "%{$diag}%");
        }

        // rango de fechas
        if ($desde = $request->get('desde')) {
            $q->whereDate('a.fechaAsignacion', '>=', $desde);
        }
        if ($hasta = $request->get('hasta')) {
            $q->whereDate('a.fechaAsignacion', '<=', $hasta);
        }

        return $q;
    }

    /**
     * Lista paginada para la vista.
     */
    public function index(Request $request)
    {
        $perPage = (int) max(10, (int) $request->get('per_page', 15));

        $rows = $this->baseQuery($request)
            ->selectRaw("
                a.idAsignacionTest as id,
                CONCAT(um.nombre, ' ', COALESCE(um.apellido, ''))   as medico,
                CONCAT(up.nombre, ' ', COALESCE(up.apellido, ''))   as paciente,
                a.fechaAsignacion                                   as fecha,
                t.nombre                                            as tipo_test,
                CASE
                    WHEN a.fechaRespuesta IS NOT NULL THEN 'completado'
                    WHEN a.fechaRespuesta IS NULL AND a.fechaAsignacion < (NOW() - INTERVAL 30 DAY) THEN 'vencido'
                    ELSE 'pendiente'
                END                                                 as estado,
                a.diagnosticoSugerido                               as diagnostico_preliminar
            ")
            ->orderByDesc('a.fechaAsignacion')
            ->paginate($perPage)
            ->appends($request->query());

        $filtros = [
            'tipo'        => (string) $request->get('tipo', ''),
            'estado'      => (string) $request->get('estado', ''),
            'diagnostico' => (string) $request->get('diagnostico', ''),
            'desde'       => (string) $request->get('desde', ''),
            'hasta'       => (string) $request->get('hasta', ''),
            'per_page'    => $perPage,
        ];

        return view('admin.reportes.tests.index', compact('rows', 'filtros'));
    }

    /**
     * Exportar a Excel con logo y título (usa FromQuery + WithMapping en el Export).
     */
    public function export(Request $request)
    {
        // Selección en el MISMO orden que mapeará el export:
        $query = $this->baseQuery($request)
            ->selectRaw("
                a.idAsignacionTest                                  as id,
                CONCAT(um.nombre, ' ', COALESCE(um.apellido, ''))   as medico,
                CONCAT(up.nombre, ' ', COALESCE(up.apellido, ''))   as paciente,
                a.fechaAsignacion                                   as fecha,
                t.nombre                                            as tipo_test,
                CASE
                    WHEN a.fechaRespuesta IS NOT NULL THEN 'completado'
                    WHEN a.fechaRespuesta IS NULL AND a.fechaAsignacion < (NOW() - INTERVAL 30 DAY) THEN 'vencido'
                    ELSE 'pendiente'
                END                                                 as estado,
                a.diagnosticoSugerido                               as diagnostico_preliminar
            ")
            ->orderByDesc('a.fechaAsignacion');

        // Ruta del logo (ajusta si tu logo está en otra carpeta)
        $logoPath = public_path('img/logo.png');

        $filename = 'reporte_asignaciones_tests_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new AsignacionesTestsExport($query, $logoPath),
            $filename
        );
    }
}
