<?php

namespace App\Http\Controllers\Medico;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Flash;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ExpedienteController extends Controller
{
    /**
     * 📋 Listado de expedientes (solo del médico autenticado)
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();

        // Obtener el ID real del médico autenticado
        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        // Obtener todos los expedientes de pacientes del médico autenticado
        $expedientes = DB::table('Expedientes as e')
            ->join('Pacientes as p', 'p.id', '=', 'e.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medicoId)
            ->select(
                'e.idExpediente',
                'e.diagnosticos',
                'e.notasClinicas',
                'e.fechaActualizacion',
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as nombre_paciente")
            )
            ->orderByDesc('e.fechaActualizacion')
            ->paginate(10);

        return view('medico.expedientes.index', compact('expedientes'));
    }

    /**
     * 🧠 Crear un nuevo expediente (solo médico)
     */
    public function create()
    {
        $usuario = Auth::user();

        // Obtener ID del médico autenticado
        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        // Obtener lista de sus pacientes
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medicoId)
            ->select('p.id as idPaciente', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as nombreCompleto"))
            ->pluck('nombreCompleto', 'idPaciente');

        return view('medico.expedientes.create', compact('pacientes'));
    }

    /**
     * 💾 Guardar expediente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fkPaciente' => 'required|integer|exists:Pacientes,id',
            'antecedentes' => 'nullable|string',
            'diagnosticos' => 'nullable|string',
            'notasClinicas' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $validated['fechaActualizacion'] = now()->toDateString();

        DB::table('Expedientes')->insert($validated);

        Flash::success('Expediente clínico registrado correctamente.');
        return redirect()->route('medico.expedientes.index');
    }

    /**
     * 📄 Ver expediente completo (vista unificada)
     */
    public function show($id)
    {
        // ✅ Obtener expediente base con información del paciente
        $expediente = DB::table('Expedientes as e')
            ->join('Pacientes as p', 'e.fkPaciente', '=', 'p.id')
            ->join('Usuarios as u', 'p.usuario_id', '=', 'u.idUsuario')
            ->select('e.*', 'u.nombre', 'u.apellido')
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

        // 🧠 Tests aplicados
        $tests = DB::table('AsignacionesTest as a')
            ->join('Tests as t', 'a.fkTest', '=', 't.idTest')
            ->where('a.fkPaciente', $pacienteId)
            ->select(
                't.nombre as nombreTest',
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

        // 💬 Respuestas emocionales (JSON)
        $respuestas = DB::table('Emociones')
            ->where('fkPaciente', $pacienteId)
            ->orderBy('fechaHoraRegistro', 'desc')
            ->get()
            ->map(function ($item) {
                $emociones = json_decode($item->emocionesExperimentadas, true);
                $intensidades = json_decode($item->intensidades, true);

                $detalle = [];
                if ($emociones && $intensidades) {
                    foreach ($emociones as $emocion) {
                        $detalle[] = [
                            'emocion' => $emocion,
                            'intensidad' => $intensidades[$emocion] ?? null,
                        ];
                    }
                }

                return [
                    'fechaRegistro' => $item->fechaHoraRegistro,
                    'comentario' => $item->comentario,
                    'detalles' => $detalle
                ];
            });

        return view('medico.expedientes.show', compact(
            'expediente',
            'citas',
            'tests',
            'actividades',
            'medicamentos',
            'respuestas'
        ));
    }

    /**
     * ✏️ Editar expediente
     */
    public function edit($id)
    {
        $expediente = DB::table('Expedientes')->where('idExpediente', $id)->first();
        $usuario = Auth::user();

        if (!$expediente) {
            Flash::error('Expediente no encontrado.');
            return redirect()->route('medico.expedientes.index');
        }

        // Pacientes del médico autenticado
        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medicoId)
            ->select('p.id as idPaciente', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as nombreCompleto"))
            ->pluck('nombreCompleto', 'idPaciente');

        return view('medico.expedientes.edit', compact('expediente', 'pacientes'));
    }

    /**
     * 💾 Actualizar expediente
     */
    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'antecedentes' => 'nullable|string',
            'diagnosticos' => 'nullable|string',
            'notasClinicas' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $validated['fechaActualizacion'] = now()->toDateString();

        DB::table('Expedientes')->where('idExpediente', $id)->update($validated);

        Flash::success('Expediente clínico actualizado correctamente.');
        return redirect()->route('medico.expedientes.show', $id);
    }
}
