<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmocionPacienteController extends Controller
{
    /**
     * Listado de emociones del paciente autenticado.
     */
    public function index()
    {
        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();

        $emociones = DB::table('Emociones as e')
            ->join('Actividades as a', 'a.idActividad', '=', 'e.fkActividad')
            ->select(
                'e.idEmocion',
                'a.titulo as actividad',
                'e.fechaHoraRegistro',
                'e.emocionesExperimentadas',
                'e.intensidad',
                'e.comentario'
            )
            ->where('e.fkPaciente', $paciente->id)
            ->orderByDesc('e.fechaHoraRegistro')
            ->get();

        return view('paciente.emociones.index', compact('emociones'));
    }

    /**
     * Mostrar formulario para registrar emoción (al finalizar actividad).
     */
    public function create($idActividad)
    {
        return view('paciente.emociones.create', compact('idActividad'));
    }

    /**
     * Guardar una nueva emoción.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fkActividad' => 'required|integer',
            'emocionesExperimentadas' => 'required|array|min:1',
            'intensidad' => 'nullable|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
        ]);

        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();

        DB::table('Emociones')->insert([
            'fkActividad' => $request->fkActividad,
            'fkPaciente' => $paciente->id,
            'emocionesExperimentadas' => json_encode($request->emocionesExperimentadas),
            'intensidad' => $request->intensidad,
            'comentario' => $request->comentario,
            'fechaHoraRegistro' => now(),
        ]);

        return redirect()->route('paciente.emociones.index')
                         ->with('success', 'Emoción registrada correctamente.');
    }

    /**
     * Editar solo el comentario.
     */
    public function edit($id)
    {
        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();

        $emocion = DB::table('Emociones')
            ->where('idEmocion', $id)
            ->where('fkPaciente', $paciente->id)
            ->first();

        if (!$emocion) {
            abort(403, 'No tienes permiso para editar esta emoción.');
        }

        return view('paciente.emociones.edit', compact('emocion'));
    }

    /**
     * Actualizar comentario.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['comentario' => 'nullable|string|max:500']);

        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();

        DB::table('Emociones')
            ->where('idEmocion', $id)
            ->where('fkPaciente', $paciente->id)
            ->update(['comentario' => $request->comentario]);

        return redirect()->route('paciente.emociones.index')
                         ->with('success', 'Comentario actualizado correctamente.');
    }
}
