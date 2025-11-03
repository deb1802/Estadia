<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeguimientoExport;

class ReporteSeguimientoController extends Controller
{
    /**
     * 🔹 Mostrar formulario para seleccionar paciente
     */
    public function index()
    {
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->select('p.id', 'u.nombre', 'u.apellido')
            ->orderBy('u.nombre')
            ->get();

        return view('admin.reportes.seguimiento.seguimiento', compact('pacientes'));
    }

    /**
     * 🔹 Generar el reporte de seguimiento visual en pantalla
     */
    public function generar(Request $request)
    {
        $idPaciente = $request->input('paciente');

        $paciente = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $idPaciente)
            ->select('u.nombre', 'u.apellido')
            ->first();

        $citas = DB::table('Citas as c')
            ->where('c.fkPaciente', $idPaciente)
            ->orderBy('c.fechaHora')
            ->get();

        $emociones = DB::table('Emociones')
            ->where('fkPaciente', $idPaciente)
            ->orderBy('fechaHoraRegistro')
            ->get();

        $diagnosticos = DB::table('Expedientes')
            ->where('fkPaciente', $idPaciente)
            ->select('diagnosticos', 'fechaActualizacion')
            ->get();

        return view('admin.reportes.seguimiento.resultado', compact('paciente', 'citas', 'emociones', 'diagnosticos'));
    }

    /**
     * 🔹 Exportar el reporte de seguimiento a Excel (CSV)
     */
    public function exportarExcel($idPaciente)
{
    $paciente = DB::table('Pacientes as p')
        ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
        ->where('p.id', $idPaciente)
        ->select('u.nombre', 'u.apellido')
        ->first();

    if (!$paciente) {
        return back()->with('error', 'Paciente no encontrado.');
    }

    $nombreArchivo = 'Reporte_Seguimiento_' . $paciente->nombre . '_' . $paciente->apellido . '.xlsx';

    return Excel::download(new SeguimientoExport($idPaciente), $nombreArchivo);
}
}
