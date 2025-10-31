<?php

namespace App\Services;

use App\Models\Test;
use App\Models\OpcionPregunta;
use Illuminate\Support\Collection;

class TestEvaluationService
{
    /**
     * Evalúa respuestas y devuelve puntaje total + rango/dx.
     *
     * @param Test $test
     * @param array $respuestas Formato:
     *   - Para opción única:   [pregunta_id => opcion_id]
     *   - Para opción múltiple:[pregunta_id => [opcion_id, opcion_id...]]
     *   - Abiertas:            [pregunta_id => 'texto'] (no suma)
     */
    public function evaluate(Test $test, array $respuestas): array
    {
        $total = 0;

        // Reunir todos los IDs de opciones seleccionadas
        $opcionIds = [];
        foreach ($respuestas as $pid => $val) {
            if (is_array($val)) {
                foreach ($val as $oid) {
                    if (is_numeric($oid)) $opcionIds[] = (int) $oid;
                }
            } elseif (is_numeric($val)) {
                $opcionIds[] = (int) $val;
            }
        }

        if (!empty($opcionIds)) {
            $opciones = OpcionPregunta::whereIn('idOpcion', $opcionIds)->get();
            $total = (int) $opciones->sum('puntaje');
        }

        // Buscar el rango correspondiente
        $rango = $test->rangos()
            ->where('minPuntaje', '<=', $total)
            ->where('maxPuntaje', '>=', $total)
            ->first();

        return [
            'total'       => $total,
            'diagnostico' => $rango?->diagnostico,
            'descripcion' => $rango?->descripcion,
            'rango'       => $rango?->toArray(),
        ];
    }
}
