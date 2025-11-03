<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteEmocionalExport;

class ReporteEmocionalController extends Controller
{
    public function index()
    {
        // 🔹 Obtener todos los diagnósticos de los expedientes
        $diagnosticos = DB::table('Expedientes')
            ->selectRaw('diagnosticos, COUNT(*) as total')
            ->whereNotNull('diagnosticos')
            ->groupBy('diagnosticos')
            ->orderByDesc('total')
            ->get();

        // 🔹 Calcular totales y porcentajes
        $totalPacientes = $diagnosticos->sum('total');
        $porcentajes = $diagnosticos->map(function ($item) use ($totalPacientes) {
            $item->porcentaje = round(($item->total / $totalPacientes) * 100, 2);
            return $item;
        });

        return view('admin.reportes.emocional.index', compact('diagnosticos', 'totalPacientes', 'porcentajes'));
    }

    public function export()
{
    return Excel::download(new \App\Exports\ReporteEmocionalExport, 'Reporte_Clasificacion_Emocional.xlsx');
}

}
