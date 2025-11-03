<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SeguimientoController extends Controller
{
    // 🔹 Lista de pacientes asignados al médico
    public function index()
    {
        $medico = DB::table('Medicos')->where('usuario_id', Auth::id())->first();

        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medico->id)
            ->select('p.id', 'u.nombre', 'u.apellido', 'u.email as correo', 'u.telefono')
            ->orderBy('u.nombre')
            ->get();

        return view('medico.seguimiento.index', compact('pacientes'));
    }

    // 🔹 Panel individual de seguimiento del paciente
    public function show($idPaciente)
    {
        $paciente = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $idPaciente)
            ->select('p.id', 'u.nombre', 'u.apellido', 'u.email as correo')
            ->first();

        // Línea de tiempo de citas
        $citas = DB::table('Citas')
            ->where('fkPaciente', $idPaciente)
            ->orderBy('fechaHora')
            ->get();

        // Emociones registradas
        $emociones = DB::table('Emociones')
            ->where('fkPaciente', $idPaciente)
            ->select('fechaHoraRegistro', 'emocionesExperimentadas', 'intensidad')
            ->orderBy('fechaHoraRegistro')
            ->get();

        return view('medico.seguimiento.show', compact('paciente', 'citas', 'emociones'));
    }
}
