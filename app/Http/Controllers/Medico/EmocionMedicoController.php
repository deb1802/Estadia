<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmocionMedicoController extends Controller
{
    /**
     * Mostrar listado de emociones registradas por los pacientes del médico.
     */
    public function index(Request $request)
    {
        $medico = DB::table('Medicos')->where('usuario_id', Auth::id())->first();

        // 🔹 Filtros opcionales
        $filtroPaciente = $request->input('paciente');
        $filtroActividad = $request->input('actividad');

        $query = DB::table('Emociones as e')
            ->join('Pacientes as p', 'e.fkPaciente', '=', 'p.id')
            ->join('Usuarios as up', 'p.usuario_id', '=', 'up.idUsuario')
            ->join('Actividades as a', 'a.idActividad', '=', 'e.fkActividad')
            ->select(
                'e.idEmocion',
                DB::raw("CONCAT(up.nombre, ' ', up.apellido) as paciente"),
                'a.titulo as actividad',
                'e.fechaHoraRegistro',
                'e.emocionesExperimentadas',
                'e.intensidades', // ✅ Antes: e.intensidad (ya no existe)
                'e.comentario'
            )
            ->where('p.medico_id', $medico->id);

        if ($filtroPaciente) {
            $query->where(DB::raw("CONCAT(up.nombre, ' ', up.apellido)"), 'like', "%$filtroPaciente%");
        }

        if ($filtroActividad) {
            $query->where('a.titulo', 'like', "%$filtroActividad%");
        }

        $emociones = $query->orderByDesc('e.fechaHoraRegistro')->get();

        // 🔹 Listas únicas para filtros select
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'p.usuario_id', '=', 'u.idUsuario')
            ->where('p.medico_id', $medico->id)
            ->select('p.id', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as nombre"))
            ->get();

        $actividades = DB::table('Actividades')
            ->where('fkMedico', $medico->id)
            ->select('idActividad', 'titulo')
            ->get();

        return view('medico.emociones.index', compact('emociones', 'pacientes', 'actividades', 'filtroPaciente', 'filtroActividad'));
    }

    /**
     * Eliminar una emoción (solo el médico puede hacerlo).
     */
    public function destroy($id)
    {
        $medico = DB::table('Medicos')->where('usuario_id', Auth::id())->first();

        // Verificar que la emoción pertenezca a un paciente del médico
        $emocion = DB::table('Emociones as e')
            ->join('Pacientes as p', 'e.fkPaciente', '=', 'p.id')
            ->where('e.idEmocion', $id)
            ->where('p.medico_id', $medico->id)
            ->first();

        if (!$emocion) {
            abort(403, 'No tienes permiso para eliminar esta emoción.');
        }

        DB::table('Emociones')->where('idEmocion', $id)->delete();

        return redirect()->route('medico.emociones.index')
                         ->with('success', 'Emoción eliminada correctamente.');
    }
}
