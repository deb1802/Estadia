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
use Illuminate\Database\Eloquent\Builder;

class TestBuilderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /** ===== Helpers de rol/alcance ===== */

    private function isAdmin(): bool
    {
        $u = Auth::user();
        return $u && in_array($u->tipoUsuario, ['admin', 'administrador']);
    }

    private function medicoIdOrFail(): int
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $medicoId = Medico::where('usuario_id', $user->idUsuario)->value('id');
        if (!$medicoId) abort(403, 'Tu cuenta no está vinculada a un perfil de médico.');

        return (int) $medicoId;
    }

    /**
     * Aplica alcance por rol:
     * - Admin: no filtra por fkMedico
     * - Médico: filtra por fkMedico
     */
    private function scopeByRole(Builder $q): Builder
    {
        if ($this->isAdmin()) return $q;
        return $q->where('fkMedico', $this->medicoIdOrFail());
    }

    /** ===== Acciones del Builder ===== */

    /** 🔹 Mostrar editor de contenido del test (admin o médico dueño) */
    public function edit($idTest)
    {
        $test = $this->scopeByRole(
            Test::with(['preguntas.opciones', 'rangos'])
        )->findOrFail($idTest);

        // Admin o dueño puede editar el contenido
        $this->authorize('update', $test);

        return view('medico.tests.builder', compact('test'));
    }

    /** 🔹 Guardar TODO (preguntas, opciones y rangos) */
    public function update(TestBuilderRequest $request, $idTest)
    {
        $test = $this->scopeByRole(Test::query())->findOrFail($idTest);
        $this->authorize('update', $test);

        $data = $request->validated();

        // Validación extra: que los rangos no se solapen
        $rangos = $data['rangos'] ?? [];
        usort($rangos, fn ($a, $b) => ($a['minPuntaje'] ?? 0) <=> ($b['minPuntaje'] ?? 0));
        for ($i = 1; $i < count($rangos); $i++) {
            if (($rangos[$i]['minPuntaje'] ?? 0) <= ($rangos[$i - 1]['maxPuntaje'] ?? -1)) {
                return back()->withErrors([
                    'rangos' => 'Los rangos no deben solaparse. Corrige los valores min/max.'
                ])->withInput();
            }
        }

        DB::transaction(function () use ($test, $data) {
            // Reset de contenido actual
            PreguntaTest::where('fkTest', $test->idTest)->delete();
            RangoTest::where('fkTest', $test->idTest)->delete();

            // Preguntas + opciones
            foreach (($data['preguntas'] ?? []) as $preguntaData) {
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

            // Rangos/diagnósticos
            foreach (($data['rangos'] ?? []) as $rangoData) {
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
            ->route($this->isAdmin() ? 'admin.tests.builder.edit' : 'medico.tests.builder.edit', $test->idTest)
            ->with('success', '✅ Contenido del test guardado correctamente.');
    }
}
