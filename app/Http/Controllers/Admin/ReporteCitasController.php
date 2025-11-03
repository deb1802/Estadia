<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteCitasController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // 🔹 Agrupa las citas por mes del año seleccionado
        $citasPorMes = DB::table('Citas')
            ->selectRaw('MONTH(fechaHora) as mes, COUNT(*) as total')
            ->whereYear('fechaHora', $year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // 🔹 Total general
        $totalCitas = $citasPorMes->sum('total');

        // 🔹 Para los meses sin citas
        $meses = collect(range(1, 12))->map(function ($m) use ($citasPorMes) {
            $encontrado = $citasPorMes->firstWhere('mes', $m);
            return [
                'mes' => $m,
                'total' => $encontrado ? $encontrado->total : 0,
            ];
        });

        // 🔹 Porcentaje por mes
        $porcentajes = $meses->map(function ($m) use ($totalCitas) {
            return $totalCitas > 0 ? round(($m['total'] / $totalCitas) * 100, 2) : 0;
        });

        return view('admin.reportes.citas_por_mes', [
            'year' => $year,
            'meses' => $meses,
            'porcentajes' => $porcentajes,
            'totalCitas' => $totalCitas,
        ]);
    }
}
