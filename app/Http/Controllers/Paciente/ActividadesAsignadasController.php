<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActividadesAsignadasController extends Controller
{
    /**
     * 📋 Mostrar las actividades asignadas al paciente autenticado
     */
    public function index(Request $request)
    {
        // 1️⃣ Resolver el idPaciente a partir del usuario logueado
        $usuarioId = Auth::id();
        $pacienteId = DB::table('Pacientes')->where('usuario_id', $usuarioId)->value('id'); // Pacientes.id

        if (!$pacienteId) {
            abort(403, 'No se encontró el paciente actual.');
        }

        // 2️⃣ Traer asignaciones + datos de la actividad
        $estado = $request->query('estado'); // opcional

        $query = DB::table('AsignacionActividad as aa')
            ->join('Actividades as a', 'a.idActividad', '=', 'aa.fkActividad')
            ->select(
                'aa.idAsignacionActividad',
                'aa.estado',
                'aa.fechaAsignacion',
                'aa.fechaFinalizacion',
                'aa.indicaciones',
                'a.idActividad',
                'a.titulo',
                'a.tipoContenido',
                'a.categoriaTerapeutica',
                'a.diagnosticoDirigido',
                'a.nivelSeveridad',
                'a.recurso'
            )
            ->where('aa.fkPaciente', $pacienteId)
            ->orderByRaw("CASE WHEN aa.estado='pendiente' THEN 0 ELSE 1 END") // pendientes primero
            ->orderByDesc('aa.fechaAsignacion');

        if (in_array($estado, ['pendiente', 'completada'])) {
            $query->where('aa.estado', $estado);
        }

        $asignaciones = $query->paginate(10)->withQueryString();

        return view('paciente.actividades.index', compact('asignaciones', 'estado'));
    }

    /**
     * ✅ Marcar una actividad como completada y redirigir al registro emocional
     */
    public function completar($asignacionId)
    {
        // 1️⃣ Identificar paciente actual
        $usuarioId = Auth::id();
        $pacienteId = DB::table('Pacientes')->where('usuario_id', $usuarioId)->value('id');

        if (!$pacienteId) {
            abort(403, 'No se encontró el paciente actual.');
        }

        // 2️⃣ Verificar que la asignación pertenece al paciente y está pendiente
        $asignacion = DB::table('AsignacionActividad')
            ->where('idAsignacionActividad', $asignacionId)
            ->where('fkPaciente', $pacienteId)
            ->where('estado', 'pendiente')
            ->first();

        if (!$asignacion) {
            return back()->with('warning', 'No se pudo marcar como completada (ya estaba completada o no te pertenece).');
        }

        // 3️⃣ Actualizar el estado a "completada"
        DB::table('AsignacionActividad')
            ->where('idAsignacionActividad', $asignacionId)
            ->update([
                'estado' => 'completada',
                'fechaFinalizacion' => now(),
            ]);

        // 4️⃣ Obtener la actividad asociada
        $actividadId = $asignacion->fkActividad;

        // 5️⃣ Redirigir directamente al formulario de registro emocional
        return redirect()
            ->route('paciente.emociones.create', $actividadId)
            ->with('success', 'Actividad completada. Ahora registra cómo te sentiste al realizarla.');
    }
}
