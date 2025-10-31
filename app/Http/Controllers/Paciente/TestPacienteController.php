<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AsignacionTest;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Log;

class TestPacienteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']); // el rol se aplica en el group de rutas
    }

    /** 🔹 Obtiene Pacientes.id del usuario autenticado (Usuarios.idUsuario) */
    private function pacienteIdOrFail(): int
    {
        $user = Auth::user(); // debe tener idUsuario
        $pacienteId = DB::table('Pacientes')->where('usuario_id', $user->idUsuario)->value('id');
        abort_unless($pacienteId, 403, 'Tu cuenta no está vinculada a un perfil de paciente.');
        return (int) $pacienteId;
    }

    /** 🔹 Listado de tests asignados al paciente (vista index) */
    public function index(Request $request)
    {
        $pacienteId = $this->pacienteIdOrFail();

        $asignaciones = DB::table('AsignacionesTest as a')
            ->join('Tests as t', 't.idTest', '=', 'a.fkTest')
            ->where('a.fkPaciente', $pacienteId)
            ->orderByDesc('a.idAsignacionTest')
            ->select([
                'a.idAsignacionTest',
                'a.fechaAsignacion',
                'a.fechaRespuesta',
                'a.puntajeTotal',
                DB::raw("t.nombre as nombreTest"),
                DB::raw("t.tipoTrastorno as tipoTrastorno"),
            ])
            ->paginate(12)
            ->withQueryString();

        return view('paciente.tests.index', compact('asignaciones'));
    }

    /** 🔹 Cargar preguntas/opciones del test para responder */
    public function responder($idAsignacionTest)
    {
        $pacienteId = $this->pacienteIdOrFail();

        $asignacion = DB::table('AsignacionesTest as a')
            ->join('Tests as t', 't.idTest', '=', 'a.fkTest')
            ->where('a.idAsignacionTest', $idAsignacionTest)
            ->where('a.fkPaciente', $pacienteId)
            ->select([
                'a.idAsignacionTest', 'a.fkTest', 'a.fkPaciente',
                'a.fechaAsignacion', 'a.fechaRespuesta',
                DB::raw('t.nombre as nombreTest'),
                DB::raw('t.descripcion as descripcionTest'),
            ])
            ->first();

        abort_unless($asignacion, 404, 'Asignación no encontrada.');

        if (!empty($asignacion->fechaRespuesta)) {
            return redirect()->route('paciente.tests.resultado', $idAsignacionTest);
        }

        $preguntas = DB::table('PreguntasTest')
            ->where('fkTest', $asignacion->fkTest)
            ->orderBy('orden')
            ->get(['idPregunta','texto','tipo','orden']);

        $opciones = DB::table('OpcionesPregunta')
            ->whereIn('fkPregunta', $preguntas->pluck('idPregunta'))
            ->orderBy('orden')
            ->get(['idOpcion','fkPregunta','etiqueta','valor','puntaje','orden']);

        $opcionesPorPregunta = [];
        foreach ($opciones as $op) {
            $opcionesPorPregunta[$op->fkPregunta][] = $op;
        }

        return view('paciente.tests.responder', [
            'asignacion' => $asignacion,
            'preguntas'  => $preguntas,
            'opcionesPorPregunta' => $opcionesPorPregunta,
        ]);
    }

    /** 🔹 Guardar respuestas, calcular puntaje y notificar al médico */
    public function guardar(Request $request, $idAsignacionTest)
    {
        $pacienteId = $this->pacienteIdOrFail();

        $asignacion = DB::table('AsignacionesTest as a')
            ->join('Tests as t', 't.idTest', '=', 'a.fkTest')
            ->where('a.idAsignacionTest', $idAsignacionTest)
            ->where('a.fkPaciente', $pacienteId)
            ->select([
                'a.idAsignacionTest','a.fkTest','a.fkPaciente',
                'a.fechaAsignacion','a.fechaRespuesta',
                DB::raw('t.nombre as nombreTest')
            ])
            ->first();

        abort_unless($asignacion, 404, 'Asignación no encontrada.');

        if (!empty($asignacion->fechaRespuesta)) {
            return redirect()->route('paciente.tests.resultado', $idAsignacionTest);
        }

        $data = $request->validate([
            'respuestas' => 'required|array|min:1',
        ], [
            'respuestas.required' => 'Debes contestar el cuestionario.',
        ]);

        $preguntas = DB::table('PreguntasTest')
            ->where('fkTest', $asignacion->fkTest)
            ->orderBy('orden')
            ->get(['idPregunta','texto','tipo','orden']);

        if ($preguntas->isEmpty()) {
            return back()->withErrors(['respuestas' => 'El test no tiene preguntas configuradas.'])->withInput();
        }

        $mapPreguntas = $preguntas->keyBy('idPregunta');

        $opciones = DB::table('OpcionesPregunta')
            ->whereIn('fkPregunta', $preguntas->pluck('idPregunta'))
            ->orderBy('orden')
            ->get(['idOpcion','fkPregunta','etiqueta','valor','puntaje','orden']);

        $opsPorPregunta = [];
        $opById = [];
        foreach ($opciones as $op) {
            $opsPorPregunta[$op->fkPregunta][] = $op;
            $opById[$op->idOpcion] = $op;
        }

        DB::transaction(function () use ($idAsignacionTest, $data, $mapPreguntas, $opById, $asignacion) {
            DB::table('RespuestasTest')->where('fkAsignacionTest', $idAsignacionTest)->delete();
            $total = 0;

            foreach ($data['respuestas'] as $idPregunta => $valor) {
                $idPregunta = (int)$idPregunta;
                if (!$mapPreguntas->has($idPregunta)) continue;

                $tipo = $mapPreguntas[$idPregunta]->tipo;

                if ($tipo === 'opcion_unica') {
                    if (empty($valor)) continue;
                    $idOpcion = (int)$valor;
                    if (!isset($opById[$idOpcion]) || $opById[$idOpcion]->fkPregunta !== $idPregunta) continue;
                    $puntaje = (int)($opById[$idOpcion]->puntaje ?? 0);
                    $total += $puntaje;

                    DB::table('RespuestasTest')->insert([
                        'fkAsignacionTest' => $idAsignacionTest,
                        'fkPregunta'       => $idPregunta,
                        'fkOpcion'         => $idOpcion,
                        'respuestaAbierta' => null,
                        'puntajeObtenido'  => $puntaje,
                    ]);

                } elseif ($tipo === 'opcion_multiple') {
                    if (!is_array($valor) || empty($valor)) continue;
                    $idsOpciones = array_map('intval', $valor);
                    foreach ($idsOpciones as $idOpcion) {
                        if (!isset($opById[$idOpcion]) || $opById[$idOpcion]->fkPregunta !== $idPregunta) continue;
                        $puntaje = (int)($opById[$idOpcion]->puntaje ?? 0);
                        $total += $puntaje;
                        DB::table('RespuestasTest')->insert([
                            'fkAsignacionTest' => $idAsignacionTest,
                            'fkPregunta'       => $idPregunta,
                            'fkOpcion'         => $idOpcion,
                            'respuestaAbierta' => null,
                            'puntajeObtenido'  => $puntaje,
                        ]);
                    }

                } elseif ($tipo === 'abierta') {
                    $texto = is_string($valor) ? trim($valor) : '';
                    DB::table('RespuestasTest')->insert([
                        'fkAsignacionTest' => $idAsignacionTest,
                        'fkPregunta'       => $idPregunta,
                        'fkOpcion'         => null,
                        'respuestaAbierta' => $texto !== '' ? $texto : null,
                        'puntajeObtenido'  => 0,
                    ]);
                }
            }

            $rango = DB::table('RangosTest')
                ->where('fkTest', $asignacion->fkTest)
                ->where('minPuntaje', '<=', $total)
                ->where('maxPuntaje', '>=', $total)
                ->select('diagnostico', 'descripcion')
                ->first();

            DB::table('AsignacionesTest')
                ->where('idAsignacionTest', $idAsignacionTest)
                ->update([
                    'fechaRespuesta'      => now(),
                    'puntajeTotal'        => $total,
                    'diagnosticoSugerido' => $rango->diagnostico ?? null,
                ]);
        });

        // =============================================
        // 📨 5️⃣ Crear notificación para el MÉDICO
        // =============================================
        // === 5) Notificar al MÉDICO por sistema (sin diagnóstico en el texto)
        try {
            // 5.1 obtener medico dueño del paciente
            $medicoId = DB::table('Pacientes')->where('id', $asignacion->fkPaciente)->value('medico_id');

            // 5.2 obtener usuario (Usuarios.idUsuario) del médico
            $medicoUsuarioId = DB::table('Medicos')->where('id', $medicoId)->value('usuario_id');

            if ($medicoUsuarioId) {
                // Texto visible (SIN diagnóstico)
                $tituloN = 'Nuevo test respondido';
                $mensajeN = sprintf(
                    'El paciente %s respondió el test "%s". AID=%d',
                    Auth::user()->nombre ?? 'Paciente',
                    $asignacion->nombreTest,
                    (int) $idAsignacionTest   // ← marcador que usaremos en el botón "Ver detalle"
                );

                \App\Models\Notificacion::create([
                    'fkUsuario' => $medicoUsuarioId,
                    'titulo'    => $tituloN,
                    'mensaje'   => $mensajeN, // aquí va el AID=###
                    'tipo'      => 'sistema',
                    'fecha'     => now(),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('No se pudo crear notificación para el médico: '.$e->getMessage());
        }


        return redirect()
            ->route('paciente.tests.recibido', $idAsignacionTest)
            ->with('success', '✅ Respuestas guardadas. Tu médico revisará tus resultados.');
    }

    /** 🔹 Pantalla de acuse (“recibido”) */
    public function recibido($idAsignacionTest)
    {
        $pacienteId = $this->pacienteIdOrFail();

        $asignacion = DB::table('AsignacionesTest as a')
            ->join('Tests as t', 't.idTest', '=', 'a.fkTest')
            ->where('a.idAsignacionTest', $idAsignacionTest)
            ->where('a.fkPaciente', $pacienteId)
            ->select([
                'a.idAsignacionTest', 'a.fechaAsignacion', 'a.fechaRespuesta',
                DB::raw('t.nombre as nombreTest'),
                DB::raw('t.tipoTrastorno as tipoTrastorno'),
                DB::raw('t.descripcion as descripcionTest'),
            ])
            ->first();

        abort_unless($asignacion, 404, 'Asignación no encontrada.');

        if (empty($asignacion->fechaRespuesta)) {
            return redirect()->route('paciente.tests.responder', $idAsignacionTest);
        }

        return view('paciente.tests.recibido', [
            'asignacion' => $asignacion
        ]);
    }

    /** 🔹 Busca el usuario del médico (para notificarlo) */
    private function medicoUsuarioIdForAsignacion(int $fkPaciente, int $fkTest): ?int
    {
        try {
            $medicoUserId = DB::table('Pacientes as p')
                ->join('Medicos as m', 'm.id', '=', 'p.medico_id')
                ->where('p.id', $fkPaciente)
                ->value('m.usuario_id');
            if ($medicoUserId) return (int)$medicoUserId;
        } catch (\Throwable $e) {
            // si la columna no existe, pasamos
        }

        $medicoUserId = DB::table('Tests as t')
            ->join('Medicos as m', 'm.id', '=', 't.fkMedico')
            ->where('t.idTest', $fkTest)
            ->value('m.usuario_id');

        return $medicoUserId ? (int)$medicoUserId : null;
    }
}
