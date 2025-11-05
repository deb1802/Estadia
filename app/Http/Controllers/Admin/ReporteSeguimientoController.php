<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeguimientoExport;

class ReporteSeguimientoController extends Controller
{
    /**
     * Mostrar formulario de selección de paciente
     */
    public function index()
    {
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->select('p.id as idPaciente', 'u.nombre', 'u.apellido')
            ->orderBy('u.nombre')
            ->get();

        return view('admin.reportes.seguimiento.seguimiento', compact('pacientes'));
    }

    /**
     * Generar reporte visual
     */
    public function generar(Request $request)
    {
        $idPaciente = $request->input('paciente');

        $paciente = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $idPaciente)
            ->select('p.id as idPaciente', 'u.nombre', 'u.apellido')
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
            ->orderBy('fechaActualizacion', 'desc')
            ->get();

        return view('admin.reportes.seguimiento.resultado', compact('paciente', 'citas', 'emociones', 'diagnosticos'));
    }

    /**
     * Exportar a Excel
     */
    public function exportarExcel($idPaciente)
    {
        $paciente = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $idPaciente)
            ->select('p.id as idPaciente', 'u.nombre', 'u.apellido')
            ->first();

        if (!$paciente) {
            return back()->with('error', 'Paciente no encontrado.');
        }

        $nombreArchivo = 'Reporte_Seguimiento_' . $paciente->nombre . '_' . $paciente->apellido . '.xlsx';

        return Excel::download(new SeguimientoExport($idPaciente), $nombreArchivo);
    }
}
