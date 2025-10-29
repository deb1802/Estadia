<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportePacientesController extends Controller
{
    /**
     * Renderiza la vista inicial del reporte.
     * Primera carga del servidor (sin recargar al filtrar; eso lo hará el endpoint JSON).
     */
    public function pacientesPorGenero(Request $request)
{
    $u = 'Usuarios';

    // 1) Usa SQL plano, NO un Expression
    $edadSql = "
        CASE 
          WHEN $u.fechaNacimiento IS NULL OR $u.fechaNacimiento = '0000-00-00' THEN NULL
          ELSE TIMESTAMPDIFF(YEAR, $u.fechaNacimiento, CURDATE())
        END
    ";

    $base = DB::table($u)->where("$u.tipoUsuario", 'paciente');

    // === Filtros ===
    if ($request->filled('sexo')) {
        $base->where("$u.sexo", $request->sexo);
    }
    if ($request->filled('edad_min') && $request->filled('edad_max')) {
        $base->whereBetween(DB::raw($edadSql), [(int)$request->edad_min, (int)$request->edad_max]);
    } elseif ($request->filled('edad_min')) {
        $base->where(DB::raw($edadSql), '>=', (int)$request->edad_min);
    } elseif ($request->filled('edad_max')) {
        $base->where(DB::raw($edadSql), '<=', (int)$request->edad_max);
    }
    if ($request->filled('fecha_inicio')) {
        $base->where("$u.fechaRegistro", '>=', $request->fecha_inicio.' 00:00:00');
    }
    if ($request->filled('fecha_fin')) {
        $base->where("$u.fechaRegistro", '<=', $request->fecha_fin.' 23:59:59');
    }

    // === Datos iniciales ===
    $pacientes = (clone $base)
        ->select([
            "$u.idUsuario as idUsuario",
            "$u.nombre","$u.apellido","$u.email","$u.sexo",
            "$u.fechaRegistro",
        ])
        ->selectRaw("$edadSql as edad")   // 👈 aquí el cambio
        ->orderBy("$u.fechaRegistro", 'desc')
        ->limit(50)
        ->get();

    // === Conteos para la dona ===
    $conteos = (clone $base)
        ->select("$u.sexo", DB::raw('COUNT(*) as total'))
        ->groupBy("$u.sexo")
        ->pluck('total', "$u.sexo");

    $total     = (int) $conteos->sum();
    $labels    = ['Masculino','Femenino','Otro'];
    $dataDonut = [
        (int)($conteos['masculino'] ?? 0),
        (int)($conteos['femenino']  ?? 0),
        (int)($conteos['otro']      ?? 0),
    ];

    return view('admin.reportes.pacientes_genero', [
        'pacientes' => $pacientes,
        'total'     => $total,
        'labels'    => $labels,
        'dataDonut' => $dataDonut,
        'filtros'   => $request->all(),
    ]);
}

public function pacientesPorGeneroData(Request $request)
{
    $u = 'Usuarios';

    // Igual que arriba
    $edadSql = "
        CASE 
          WHEN $u.fechaNacimiento IS NULL OR $u.fechaNacimiento = '0000-00-00' THEN NULL
          ELSE TIMESTAMPDIFF(YEAR, $u.fechaNacimiento, CURDATE())
        END
    ";

    $base = DB::table($u)->where("$u.tipoUsuario", 'paciente');

    if ($request->filled('sexo')) {
        $base->where("$u.sexo", $request->sexo);
    }
    if ($request->filled('edad_min') && $request->filled('edad_max')) {
        $base->whereBetween(DB::raw($edadSql), [(int)$request->edad_min, (int)$request->edad_max]);
    } elseif ($request->filled('edad_min')) {
        $base->where(DB::raw($edadSql), '>=', (int)$request->edad_min);
    } elseif ($request->filled('edad_max')) {
        $base->where(DB::raw($edadSql), '<=', (int)$request->edad_max);
    }
    if ($request->filled('fecha_inicio')) {
        $base->where("$u.fechaRegistro", '>=', $request->fecha_inicio.' 00:00:00');
    }
    if ($request->filled('fecha_fin')) {
        $base->where("$u.fechaRegistro", '<=', $request->fecha_fin.' 23:59:59');
    }

    $rows = (clone $base)
        ->select([
            "$u.idUsuario as id",
            "$u.nombre","$u.apellido","$u.email","$u.sexo",
            "$u.fechaRegistro",
        ])
        ->selectRaw("$edadSql as edad")   // 👈 aquí también
        ->orderBy("$u.fechaRegistro", 'desc')
        ->limit(800)
        ->get();

    $conteos = (clone $base)
        ->select("$u.sexo", DB::raw('COUNT(*) as total'))
        ->groupBy("$u.sexo")
        ->pluck('total', "$u.sexo");

    $total     = (int) $conteos->sum();
    $dataDonut = [
        (int)($conteos['masculino'] ?? 0),
        (int)($conteos['femenino']  ?? 0),
        (int)($conteos['otro']      ?? 0),
    ];

    $dataRows = $rows->map(function($r){
        return [
            'id'     => $r->id,
            'nombre' => trim(($r->nombre ?? '').' '.($r->apellido ?? '')),
            'email'  => $r->email,
            'sexo'   => $r->sexo ?: '—',
            'edad'   => is_null($r->edad) ? '—' : (string)$r->edad,
            'fecha'  => \Carbon\Carbon::parse($r->fechaRegistro)->format('d/m/Y H:i'),
        ];
    });

    return response()->json([
        'total'     => $total,
        'dataDonut' => $dataDonut,
        'rows'      => $dataRows,
    ]);
}

}
