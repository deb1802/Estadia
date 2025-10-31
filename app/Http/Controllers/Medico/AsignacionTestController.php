<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\AsignacionTest;   // tabla AsignacionesTest
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Test;
use App\Mail\TestAsignadoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AsignacionTestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']); // el rol lo aplica el Route Group
    }

    /** 🔹 Obtiene Medicos.id según el usuario logueado */
    private function medicoIdOrFail(): int
    {
        $user = Auth::user();
        $medicoId = Medico::where('usuario_id', $user->idUsuario)->value('id');
        abort_unless($medicoId, 403, 'Tu cuenta no está vinculada a un perfil de médico.');
        return (int) $medicoId;
    }

    /** 🔹 GET: pantalla para asignar tests */
    public function index(Request $request)
    {
        $medicoId = $this->medicoIdOrFail();

        // 🔸 Pacientes del médico (si existe la relación)
        $pacientesQuery = Paciente::query();
        if (Schema::hasColumn('Pacientes', 'medico_id')) {
            $pacientesQuery->where('medico_id', $medicoId);
        } elseif (Schema::hasColumn('Pacientes', 'fkMedico')) {
            $pacientesQuery->where('fkMedico', $medicoId);
        }

        $pacientes = $pacientesQuery
            ->orderBy('id', 'desc')
            ->limit(200)
            ->get();

        // 🔸 Tests creados por el médico
        $q = trim((string) $request->get('q', ''));
        $tests = Test::where('fkMedico', $medicoId)
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('tipoTrastorno', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('idTest')
            ->paginate(12)
            ->withQueryString();

        return view('medico.tests.asignar', compact('pacientes', 'tests'));
    }

    /** 🔹 POST: asignar uno o varios tests a un paciente + enviar correo */
    public function store(Request $request)
    {
        $medicoId = $this->medicoIdOrFail();

        // ✅ Validar datos
        $data = $request->validate([
            'paciente_id' => 'required|integer',
            'tests'       => 'required|array|min:1',
            'tests.*'     => 'integer',
        ], [
            'paciente_id.required' => 'Selecciona un paciente.',
            'tests.required'       => 'Selecciona al menos un test.',
        ]);

        // ✅ Verificar que los tests pertenecen al médico
        $testsDelMedico = Test::where('fkMedico', $medicoId)
            ->whereIn('idTest', $data['tests'])
            ->pluck('idTest')
            ->all();

        if (count($testsDelMedico) !== count($data['tests'])) {
            return back()->withErrors([
                'tests' => 'Se intentó asignar un test que no pertenece a tu cuenta.'
            ])->withInput();
        }

        // ✅ Verificar paciente existente
        $pacienteId = (int) $data['paciente_id'];
        $paciente = Paciente::find($pacienteId);
        if (!$paciente) {
            return back()->withErrors(['paciente_id' => 'Paciente inválido.'])->withInput();
        }

        /* ======================================================
           1️⃣ Insertar asignaciones en la tabla AsignacionesTest
        ====================================================== */
        DB::transaction(function () use ($data) {
            $ahora = now();
            foreach ($data['tests'] as $testId) {
                AsignacionTest::create([
                    'fkTest'              => (int) $testId,
                    'fkPaciente'          => (int) $data['paciente_id'],
                    'fechaAsignacion'     => $ahora,
                    'fechaRespuesta'      => null,
                    'puntajeTotal'        => null,
                    'diagnosticoSugerido' => null,
                    'diagnosticoConfirmado'=> null,
                    'confirmadoPor'       => null,
                    'fechaConfirmacion'   => null,
                    'notasClinicas'       => null,
                    'subescalas'          => null,
                ]);
            }
        });

        /* ======================================================
           2️⃣ Enviar correo al paciente (sin registrar notificación)
        ====================================================== */
        try {
            // 🔸 Obtener correo y nombre del paciente
            $pacienteRow = DB::table('Pacientes as p')
                ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
                ->where('p.id', $pacienteId)
                ->select('u.email', 'u.nombre', 'u.apellido')
                ->first();

            // 🔸 Obtener nombre del médico
            $medicoRow = DB::table('Medicos as m')
                ->join('Usuarios as u', 'u.idUsuario', '=', 'm.usuario_id')
                ->where('m.id', $medicoId)
                ->select('u.nombre', 'u.apellido')
                ->first();

            // 🔸 Obtener información de los tests asignados
            $testsInfo = Test::whereIn('idTest', $data['tests'])
                ->get(['nombre', 'tipoTrastorno']);

            if ($pacienteRow && !empty($pacienteRow->email)) {
                $toEmail        = $pacienteRow->email;
                $pacienteNombre = trim(($pacienteRow->nombre ?? '') . ' ' . ($pacienteRow->apellido ?? ''));
                $medicoNombre   = trim(($medicoRow->nombre ?? '') . ' ' . ($medicoRow->apellido ?? ''));
                $fechaAsignacion = now();
                $urlAccion       = url('/paciente/tests');

                // ✉️ Enviar correo
                Mail::to($toEmail)->send(new TestAsignadoMail(
                    pacienteNombre:  $pacienteNombre,
                    medicoNombre:    $medicoNombre,
                    testsAsignados:  $testsInfo->map(fn($t) => [
                        'nombre' => $t->nombre,
                        'tipo'   => $t->tipoTrastorno
                    ])->toArray(),
                    fechaAsignacion: $fechaAsignacion,
                    urlAccion:       $urlAccion
                ));
            }
        } catch (\Throwable $e) {
            \Log::error('❌ Error enviando correo de test asignado: '.$e->getMessage());
        }

        /* ======================================================
           3️⃣ Redirigir con mensaje de éxito
        ====================================================== */
        return redirect()
            ->route('medico.tests.asignar.index')
            ->with('success', '✅ Test(s) asignado(s) correctamente y correo enviado al paciente.');
    }

    public function detalle($idAsignacionTest)
{
    try {
        $userId   = Auth::id();
        $medicoId = DB::table('Medicos')->where('usuario_id', $userId)->value('id');
        abort_unless($medicoId, 403, 'No se encontró perfil de médico.');

        // Armar query base
        $q = DB::table('AsignacionesTest as a')
            ->join('Tests as t', 't.idTest', '=', 'a.fkTest')
            ->join('Pacientes as p', 'p.id', '=', 'a.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('a.idAsignacionTest', $idAsignacionTest);

        // Restringir por pertenencia del paciente al médico SI existe la columna correspondiente
        if (Schema::hasColumn('Pacientes', 'medico_id')) {
            $q->where('p.medico_id', $medicoId);
        } elseif (Schema::hasColumn('Pacientes', 'fkMedico')) {
            $q->where('p.fkMedico', $medicoId);
        } // si no existe ninguna, no filtramos (entorno demo)

        $asig = $q->select([
                'a.*',
                DB::raw('t.nombre as nombreTest'),
                DB::raw('t.tipoTrastorno as tipoTrastorno'),
                DB::raw('u.nombre as nomPac'),
                DB::raw('u.apellido as apePac'),
            ])->first();

        abort_unless($asig, 404, 'Asignación no encontrada.');

        $respuestas = DB::table('RespuestasTest as r')
            ->join('PreguntasTest as p', 'p.idPregunta', '=', 'r.fkPregunta')
            ->leftJoin('OpcionesPregunta as o', 'o.idOpcion', '=', 'r.fkOpcion')
            ->where('r.fkAsignacionTest', $idAsignacionTest)
            ->orderBy('p.orden')
            ->select([
                'p.texto as pregunta',
                'p.tipo as tipoPregunta',
                'o.etiqueta as opcion',
                'r.respuestaAbierta',
                'r.puntajeObtenido'
            ])
            ->get();

        return view('medico.tests._detalle_asignacion', [
            'asig'        => $asig,
            'respuestas'  => $respuestas,
            'csrf'        => csrf_token(),
            'prefillDiag' => $asig->diagnosticoConfirmado ?: $asig->diagnosticoSugerido,
        ]);

    } catch (\Throwable $e) {
        Log::error('Detalle asignación falló', [
            'asignacion' => $idAsignacionTest,
            'medicoId'   => $medicoId ?? null,
            'msg'        => $e->getMessage(),
        ]);

        // HTML cortito para el modal (evita el dump enorme)
        return response(
            '<div class="text-danger">No se pudo cargar el detalle. '
            .htmlentities($e->getMessage()).'</div>',
            500
        );
    }
}

// App\Http\Controllers\Medico\AsignacionTestController.php

public function confirmar(Request $request, $idAsignacionTest)
{
    $medicoId = $this->medicoIdOrFail();

    $data = $request->validate([
        'diagnostico_confirmado' => ['nullable','string','max:150'],
        'notas_clinicas'         => ['nullable','string','max:10000'],
    ]);

    // Verificamos pertenencia: la asignación -> test -> pertenece al médico logueado
    $row = \DB::table('AsignacionesTest as a')
        ->join('Tests as t','t.idTest','=','a.fkTest')
        ->where('a.idAsignacionTest', $idAsignacionTest)
        ->where('t.fkMedico', $medicoId)
        ->select('a.idAsignacionTest')
        ->first();

    abort_unless($row, 403, 'No puedes confirmar este resultado.');

    \DB::table('AsignacionesTest')
        ->where('idAsignacionTest', $idAsignacionTest)
        ->update([
            'diagnosticoConfirmado' => $data['diagnostico_confirmado'] ?? null,
            'notasClinicas'         => $data['notas_clinicas'] ?? null,
            'confirmadoPor'         => $medicoId,
            'fechaConfirmacion'     => now(),
        ]);

    // Si te lo piden vía AJAX, responde JSON; si no, redirige con flash
    if ($request->expectsJson() || $request->wantsJson()) {
        return response()->json(['ok' => true]);
    }

    return redirect()
        ->route('medico.tests.asignaciones.show', $idAsignacionTest)
        ->with('success', '✅ Diagnóstico confirmado correctamente.');
}

public function showDetalle($idAsignacionTest)
{
    // 🧑‍⚕️ Medico actual
    $user      = \Auth::user();
    $medicoId  = \DB::table('Medicos')->where('usuario_id', $user->idUsuario)->value('id');

    abort_unless($medicoId, 403, 'Tu cuenta no está vinculada a un perfil de médico.');

    // 📄 Traer asignación + test + paciente (solo si el test pertenece a este médico)
    $asig = \DB::table('AsignacionesTest as a')
        ->join('Tests as t', 't.idTest', '=', 'a.fkTest')
        ->join('Pacientes as p', 'p.id', '=', 'a.fkPaciente')
        ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
        ->where('a.idAsignacionTest', $idAsignacionTest)
        ->where('t.fkMedico', $medicoId) // seguridad: el test es del médico logueado
        ->select([
            'a.*',
            \DB::raw('t.nombre as nombreTest'),
            \DB::raw('up.nombre as nomPac'),
            \DB::raw('up.apellido as apePac'),
        ])
        ->first();

    abort_unless($asig, 404, 'Asignación no encontrada.');

    // 🧩 Respuestas (pregunta + etiqueta opción + puntaje)
    $respuestas = \DB::table('RespuestasTest as r')
        ->join('PreguntasTest as p', 'p.idPregunta', '=', 'r.fkPregunta')
        ->leftJoin('OpcionesPregunta as o', 'o.idOpcion', '=', 'r.fkOpcion')
        ->where('r.fkAsignacionTest', $idAsignacionTest)
        ->orderBy('p.orden')
        ->select([
            \DB::raw('p.texto as pregunta'),
            \DB::raw('o.etiqueta as opcion'),
            'r.respuestaAbierta',
            'r.puntajeObtenido',
        ])
        ->get();

    return view('medico.tests.detalle_asignacion', [
        'asig'       => $asig,
        'respuestas' => $respuestas,
    ]);
}


}
