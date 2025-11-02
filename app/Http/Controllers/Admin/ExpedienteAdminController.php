<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Expediente;

class ExpedienteAdminController extends Controller
{
    public function index()
    {
        // El admin ve todos los expedientes de todos los médicos
        $expedientes = DB::table('Expedientes as e')
            ->join('Pacientes as p', 'e.fkPaciente', '=', 'p.id')
            ->join('Usuarios as u', 'p.usuario_id', '=', 'u.idUsuario')
            ->join('Medicos as m', 'p.medico_id', '=', 'm.id')
            ->join('Usuarios as um', 'm.usuario_id', '=', 'um.idUsuario')
            ->select(
                'e.idExpediente',
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as nombre_paciente"),
                DB::raw("CONCAT(um.nombre, ' ', um.apellido) as nombre_medico"),
                'e.diagnosticos',
                'e.notasClinicas',
                'e.fechaActualizacion'
            )
            ->orderBy('e.fechaActualizacion', 'desc')
            ->get();

        return view('admin.expedientes.index', compact('expedientes'));
    }

    public function show($id)
    {
        // Datos básicos del expediente
        $expediente = DB::table('Expedientes as e')
            ->join('Pacientes as p', 'e.fkPaciente', '=', 'p.id')
            ->join('Usuarios as u', 'p.usuario_id', '=', 'u.idUsuario')
            ->select(
                'e.*',
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as nombre_paciente")
            )
            ->where('e.idExpediente', $id)
            ->first();

        if (!$expediente) {
            abort(404, 'Expediente no encontrado');
        }

        $pacienteId = $expediente->fkPaciente;

        // 🗓️ Citas
        $citas = DB::table('Citas')
            ->where('fkPaciente', $pacienteId)
            ->select('fechaHora', 'motivo', 'ubicacion', 'estado')
            ->orderBy('fechaHora', 'desc')
            ->get();

        // 🧠 Tests
        $tests = DB::table('AsignacionesTest as a')
            ->join('Tests as t', 'a.fkTest', '=', 't.idTest')
            ->where('a.fkPaciente', $pacienteId)
            ->select(
                't.nombre',
                't.tipoTrastorno',
                'a.puntajeTotal',
                'a.diagnosticoSugerido',
                'a.diagnosticoConfirmado',
                'a.fechaRespuesta'
            )
            ->orderByDesc('a.fechaRespuesta')
            ->get();

        // 🧘 Actividades
        $actividades = DB::table('AsignacionActividad as aa')
            ->join('Actividades as act', 'aa.fkActividad', '=', 'act.idActividad')
            ->where('aa.fkPaciente', $pacienteId)
            ->select('act.titulo as nombreActividad', 'aa.estado', 'aa.fechaAsignacion')
            ->orderBy('aa.fechaAsignacion', 'desc')
            ->get();

        // 💊 Medicamentos
        $medicamentos = DB::table('RecetasMedicas as r')
            ->join('Detalle_Medicamento as d', 'r.idReceta', '=', 'd.fkReceta')
            ->join('Medicamentos as m', 'd.fkMedicamento', '=', 'm.idMedicamento')
            ->where('r.fkPaciente', $pacienteId)
            ->select('m.nombre', 'd.dosis', 'r.fecha as fechaReceta')
            ->orderBy('r.fecha', 'desc')
            ->get();

        // 💬 Respuestas emocionales
        $respuestas = DB::table('Emociones')
            ->where('fkPaciente', $pacienteId)
            ->select('emocionesExperimentadas as emocion', 'intensidad', 'fechaHoraRegistro as fechaRegistro')
            ->orderBy('fechaHoraRegistro', 'desc')
            ->get();

        return view('admin.expedientes.show', compact('expediente', 'citas', 'tests', 'actividades', 'medicamentos', 'respuestas'));
    }

    public function destroy($id)
    {
        DB::table('Expedientes')->where('idExpediente', $id)->delete();

        return redirect()->route('admin.expedientes.index')
            ->with('success', 'Expediente eliminado correctamente.');
    }
}
