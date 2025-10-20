<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard/summary
     * Filtros opcionales:
     * - sexo=masculino|femenino|otro   (filtra PACIENTES)
     * - from=YYYY-MM-DD                (fecha inicio para testimonios/respuestas)
     * - to=YYYY-MM-DD                  (fecha fin para testimonios/respuestas)
     */
    public function summary(Request $request)
    {
        $sexo = $request->query('sexo'); // null|masculino|femenino|otro
        $from = $request->query('from'); // null|YYYY-MM-DD
        $to   = $request->query('to');   // null|YYYY-MM-DD

        // ===== Usuarios totales y por rol =====
        $totalUsuarios = (int) DB::table('Usuarios')->count();

        $usuariosPorRol = DB::table('Usuarios')
            ->select('tipoUsuario', DB::raw('COUNT(*) as total'))
            ->groupBy('tipoUsuario')
            ->pluck('total', 'tipoUsuario'); // ['administrador'=>n,'medico'=>n,'paciente'=>n]

        // ===== Pacientes por sexo =====
        $pacQuery = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id');

        if ($sexo && in_array($sexo, ['masculino', 'femenino', 'otro'])) {
            $pacQuery->where('u.sexo', $sexo);
        }

        $pacientesPorSexo = $pacQuery
            ->select('u.sexo', DB::raw('COUNT(*) as total'))
            ->groupBy('u.sexo')
            ->pluck('total', 'u.sexo');

        $h = (int) ($pacientesPorSexo['masculino'] ?? 0);
        $m = (int) ($pacientesPorSexo['femenino'] ?? 0);
        $o = (int) ($pacientesPorSexo['otro'] ?? 0);
        $pacTotal = max(1, $h + $m + $o);

        $pacientesSexoPct = [
            'masculino' => round(($h / $pacTotal) * 100),
            'femenino'  => round(($m / $pacTotal) * 100),
            'otro'      => round(($o / $pacTotal) * 100),
        ];

        // ===== Totales adicionales =====
        $totalMedicos     = (int) DB::table('Medicos')->count();
        $totalPacientes   = (int) DB::table('Pacientes')->count();
        $totalActividades = (int) DB::table('Actividades')->count();

        // ===== Testimonios (simulan “tests psicológicos”) =====
        $testQuery = DB::table('Testimonios');
        if ($from) $testQuery->where('fecha', '>=', $from);
        if ($to)   $testQuery->where('fecha', '<=', $to);
        $totalTests = (int) $testQuery->count();

        $respQuery = DB::table('RespuestasTestimonio');
        if ($from) $respQuery->where('fecha', '>=', $from . ' 00:00:00');
        if ($to)   $respQuery->where('fecha', '<=', $to . ' 23:59:59');
        $totalRespuestasTest = (int) $respQuery->count();

        // ===== Estructura de respuesta =====
        return response()->json([
            'filters' => [
                'sexo' => $sexo,
                'from' => $from,
                'to'   => $to,
            ],
            'cards' => [
                'usuarios_total'      => $totalUsuarios,
                'usuarios_por_rol'    => [
                    'administrador' => (int) ($usuariosPorRol['administrador'] ?? 0),
                    'medico'        => (int) ($usuariosPorRol['medico'] ?? 0),
                    'paciente'      => (int) ($usuariosPorRol['paciente'] ?? 0),
                ],
                'medicos_total'       => $totalMedicos,
                'pacientes_total'     => $totalPacientes,
                'actividades_total'   => $totalActividades,
                'tests_total'         => $totalTests,
                'respuestas_test'     => $totalRespuestasTest,
            ],
            'charts' => [
                'pacientes_por_sexo' => [
                    'absolutos'   => ['masculino' => $h, 'femenino' => $m, 'otro' => $o],
                    'porcentaje'  => $pacientesSexoPct,
                ],
            ],
        ]);
    }
}
