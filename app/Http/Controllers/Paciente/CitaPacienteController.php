<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CitaPacienteController extends Controller
{
    public function index()
    {
        // Obtener ID del paciente autenticado
        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();

        if (!$paciente) {
            return back()->with('error', 'No se encontró el paciente autenticado.');
        }

        // Consultar las citas de ese paciente
        $citas = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->select(
                'c.idCita',
                DB::raw("CONCAT(um.nombre, ' ', um.apellido) as medico"),
                'c.fechaHora',
                'c.motivo',
                'c.ubicacion',
                'c.estado'
            )
            ->where('c.fkPaciente', $paciente->id)
            ->orderByDesc('c.fechaHora')
            ->get();

        return view('paciente.citas.index', compact('citas'));
    }

    public function show($id)
    {
        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();

        $cita = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->select(
                'c.*',
                DB::raw("CONCAT(um.nombre, ' ', um.apellido) as medico")
            )
            ->where('c.idCita', $id)
            ->where('c.fkPaciente', $paciente->id)
            ->first();

        if (!$cita) {
            return redirect()->route('paciente.citas.index')->with('error', 'Cita no encontrada.');
        }

        return view('paciente.citas.show', compact('cita'));
    }
}
