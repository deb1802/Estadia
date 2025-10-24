<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActividadesAsignadasController extends Controller
{
    // GET /paciente/mis-actividades
    public function index(Request $request)
    {
        // 1) Resolver el idPaciente a partir del usuario logueado
        $usuarioId = Auth::id();
        $pacienteId = DB::table('Pacientes')->where('usuario_id', $usuarioId)->value('id'); // Pacientes.id

        if (!$pacienteId) {
            abort(403, 'No se encontró el paciente actual.');
        }

        // 2) Traer asignaciones + datos de la actividad
        //    Puedes filtrar por estado con ?estado=pendiente|completada
        $estado = $request->query('estado'); // opcional

        $query = DB::table('AsignacionActividad as aa')
            ->join('Actividades as a', 'a.idActividad', '=', 'aa.fkActividad')
            ->select(
                'aa.idAsignacionActividad',
                'aa.estado',
                'aa.fechaAsignacion',
                'aa.fechaFinalizacion',
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

        if (in_array($estado, ['pendiente','completada'])) {
            $query->where('aa.estado', $estado);
        }

        $asignaciones = $query->paginate(10)->withQueryString();

        return view('paciente.actividades.index', compact('asignaciones', 'estado'));
    }

    // PATCH /paciente/mis-actividades/{asignacion}/completar
    public function completar($asignacionId)
    {
        // 1) Resolver paciente actual
        $usuarioId = Auth::id();
        $pacienteId = DB::table('Pacientes')->where('usuario_id', $usuarioId)->value('id');

        if (!$pacienteId) {
            abort(403, 'No se encontró el paciente actual.');
        }

        // 2) Asegurar pertenencia y que esté pendiente
        $afectadas = DB::table('AsignacionActividad')
            ->where('idAsignacionActividad', $asignacionId)
            ->where('fkPaciente', $pacienteId)
            ->where('estado', 'pendiente')
            ->update([
                'estado' => 'completada'
            ]);

        if ($afectadas === 0) {
            return back()->with('warning', 'No se pudo marcar como completada (ya estaba completada o no te pertenece).');
        }

        return back()->with('success', 'Actividad marcada como completada.');
    }
}
