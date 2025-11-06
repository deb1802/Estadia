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
        if (!$medico) {
            // Si el usuario logueado no es médico o no tiene fila en Medicos
            return view('medico.seguimiento.index', ['pacientes' => collect()]);
        }

        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medico->id) // ajusta a tu FK real si fuera fkMedico
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

        if (!$paciente) {
            abort(404);
        }

        // Línea de tiempo de citas (la vista ya controla el estilo)
        $citas = DB::table('Citas')
            ->where('fkPaciente', $idPaciente)
            ->orderBy('fechaHora')
            ->get();

        // Emociones registradas (👈 CAMBIO: ahora seleccionamos intensidades JSON)
        // La vista/JS espera: fechaHoraRegistro, emocionesExperimentadas (JSON), intensidades (JSON)
        $emociones = DB::table('Emociones')
            ->where('fkPaciente', $idPaciente)
            ->select(
                'fechaHoraRegistro',
                'emocionesExperimentadas',
                'intensidades' // <— antes era "intensidad"
            )
            ->orderBy('fechaHoraRegistro')
            ->get();

        // Actividades (completadas primero; luego el resto)
        $actividades = DB::table('AsignacionActividad as aa')
            ->join('Actividades as a', 'a.idActividad', '=', 'aa.fkActividad')
            ->where('aa.fkPaciente', $idPaciente)
            ->orderByDesc(DB::raw("aa.estado = 'completada'")) // true primero
            ->orderByDesc('aa.fechaFinalizacion')
            ->orderByDesc('aa.fechaAsignacion')
            ->select(
                'aa.idAsignacionActividad',
                'aa.estado',
                'aa.fechaAsignacion',
                'aa.fechaFinalizacion',
                'aa.indicaciones',
                'a.titulo',
                'a.tipoContenido',
                'a.categoriaTerapeutica',
                'a.diagnosticoDirigido',
                'a.nivelSeveridad'
            )
            ->limit(12)
            ->get();

        return view('medico.seguimiento.show', compact('paciente', 'citas', 'emociones', 'actividades'));
    }
}
