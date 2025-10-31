<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medico\TestBuilderRequest;
use App\Models\Test;
use App\Models\Medico;
use App\Models\PreguntaTest;
use App\Models\OpcionPregunta;
use App\Models\RangoTest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestBuilderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    private function medicoIdOrFail(): int
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $medicoId = Medico::where('usuario_id', $user->idUsuario)->value('id');
        if (!$medicoId) abort(403, 'Tu cuenta no está vinculada a un perfil de médico.');

        return (int)$medicoId;
    }

    /** 🔹 Mostrar editor de contenido del test */
    public function edit($idTest)
    {
        $medicoId = $this->medicoIdOrFail();

        $test = Test::where('fkMedico', $medicoId)
            ->with(['preguntas.opciones', 'rangos'])
            ->findOrFail($idTest);

        return view('medico.tests.builder', compact('test'));
    }

    /** 🔹 Guardar TODO (preguntas, opciones y rangos) */
    public function update(TestBuilderRequest $request, $idTest)
    {
        $medicoId = $this->medicoIdOrFail();

        $test = Test::where('fkMedico', $medicoId)->findOrFail($idTest);
        $data = $request->validated();

        // Validación extra: no traslapes
        $rangos = $data['rangos'];
        usort($rangos, fn($a, $b) => $a['minPuntaje'] <=> $b['minPuntaje']);
        for ($i = 1; $i < count($rangos); $i++) {
            if ($rangos[$i]['minPuntaje'] <= $rangos[$i - 1]['maxPuntaje']) {
                return back()->withErrors([
                    'rangos' => 'Los rangos no deben solaparse. Corrige los valores min/max.'
                ])->withInput();
            }
        }

        DB::transaction(function () use ($test, $data) {
            PreguntaTest::where('fkTest', $test->idTest)->delete();
            RangoTest::where('fkTest', $test->idTest)->delete();

            foreach ($data['preguntas'] as $preguntaData) {
                $pregunta = PreguntaTest::create([
                    'fkTest' => $test->idTest,
                    'texto'  => $preguntaData['texto'],
                    'tipo'   => $preguntaData['tipo'],
                    'orden'  => $preguntaData['orden'],
                ]);

                if (!empty($preguntaData['opciones']) && in_array($preguntaData['tipo'], ['opcion_unica','opcion_multiple'])) {
                    foreach ($preguntaData['opciones'] as $opcionData) {
                        OpcionPregunta::create([
                            'fkPregunta' => $pregunta->idPregunta,
                            'etiqueta'   => $opcionData['etiqueta'],
                            'valor'      => $opcionData['valor'] ?? null,
                            'puntaje'    => $opcionData['puntaje'],
                            'orden'      => $opcionData['orden'],
                        ]);
                    }
                }
            }

            foreach ($data['rangos'] as $rangoData) {
                RangoTest::create([
                    'fkTest'      => $test->idTest,
                    'minPuntaje'  => $rangoData['minPuntaje'],
                    'maxPuntaje'  => $rangoData['maxPuntaje'],
                    'diagnostico' => $rangoData['diagnostico'],
                    'descripcion' => $rangoData['descripcion'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('medico.tests.builder.edit', $test->idTest)
            ->with('success', '✅ Contenido del test guardado correctamente.');
    }
}
