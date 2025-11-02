<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CitaAdminController extends Controller
{
    public function index()
    {
        // 📋 El admin ve todas las citas del sistema
        $citas = DB::table('Citas as c')
            ->join('Medicos as m', 'c.fkMedico', '=', 'm.id')
            ->join('Pacientes as p', 'c.fkPaciente', '=', 'p.id')
            ->join('Usuarios as um', 'm.usuario_id', '=', 'um.idUsuario')
            ->join('Usuarios as up', 'p.usuario_id', '=', 'up.idUsuario')
            ->select(
                'c.idCita',
                'c.fechaHora',
                'c.motivo',
                'c.estado',
                'c.ubicacion',
                DB::raw("CONCAT(um.nombre, ' ', um.apellido) as medico"),
                DB::raw("CONCAT(up.nombre, ' ', up.apellido) as paciente")
            )
            ->orderByDesc('c.fechaHora')
            ->get();

        return view('admin.citas.index', compact('citas'));
    }

    public function show($id)
    {
        $cita = DB::table('Citas as c')
            ->join('Medicos as m', 'c.fkMedico', '=', 'm.id')
            ->join('Pacientes as p', 'c.fkPaciente', '=', 'p.id')
            ->join('Usuarios as um', 'm.usuario_id', '=', 'um.idUsuario')
            ->join('Usuarios as up', 'p.usuario_id', '=', 'up.idUsuario')
            ->select(
                'c.*',
                DB::raw("CONCAT(um.nombre, ' ', um.apellido) as medico"),
                DB::raw("CONCAT(up.nombre, ' ', up.apellido) as paciente")
            )
            ->where('c.idCita', $id)
            ->first();

        return view('admin.citas.show', compact('cita'));
    }

    public function destroy($id)
    {
        DB::table('Citas')->where('idCita', $id)->delete();

        return redirect()->route('admin.citas.index')->with('success', 'Cita eliminada correctamente.');
    }
}
