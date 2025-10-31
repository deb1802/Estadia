<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Test;
use App\Models\PreguntaTest;
use App\Models\OpcionPregunta;
use App\Models\RangoTest;

class Gad7Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 👉 Ajusta aquí el médico dueño del test:
            // Usamos el primero si existe; cámbialo si quieres otro.
            $medicoId = DB::table('Medicos')->orderBy('id')->value('id') ?? 1;

            // 1) Test
            $test = Test::create([
                'nombre'        => 'GAD-7 (Ansiedad) - Adaptado',
                'tipoTrastorno' => 'Ansiedad',
                'descripcion'   => 'Tamiz de ansiedad de 7 reactivos. Escala 0–3 (Nunca/Algunas veces/Con frecuencia/Casi siempre).',
                'estado'        => 'inactivo', // actívalo cuando quieras
                'fkMedico'      => $medicoId,
            ]);

            // 2) Preguntas (todas opción única, 0–3)
            $reactivos = [
                'Se ha sentido nervioso/a, ansioso/a o al borde?',
                'No puede dejar de preocuparse o controlar la preocupación',
                'Se preocupa demasiado por diferentes cosas',
                'Tiene dificultad para relajarse',
                'Se pone tan inquieto/a que le cuesta estar quieto/a',
                'Se irrita o molesta con facilidad',
                'Siente miedo como si algo terrible fuera a pasar',
            ];

            $etiquetas = [
                ['Nunca', 0], ['Algunas veces', 1], ['Con frecuencia', 2], ['Casi siempre', 3],
            ];

            foreach ($reactivos as $i => $texto) {
                $p = PreguntaTest::create([
                    'fkTest' => $test->idTest,
                    'texto'  => $texto,
                    'tipo'   => 'opcion_unica',
                    'orden'  => $i + 1,
                ]);

                foreach ($etiquetas as $idx => [$label, $score]) {
                    OpcionPregunta::create([
                        'fkPregunta' => $p->idPregunta,
                        'etiqueta'   => $label,
                        'puntaje'    => $score,
                        'orden'      => $idx + 1,
                    ]);
                }
            }

            // (Opcional) 1 abierta
            PreguntaTest::create([
                'fkTest' => $test->idTest,
                'texto'  => 'Describe situaciones que detonen tu ansiedad (opcional)',
                'tipo'   => 'abierta',
                'orden'  => count($reactivos) + 1,
            ]);

            // 3) Rangos (literatura del GAD-7: 0–21)
            $rangos = [
                [0, 4,  'Ansiedad mínima',            'Síntomas leves/transitorios. Reforzar autocuidado.'],
                [5, 9,  'Ansiedad leve',              'Monitoreo y psicoeducación. Técnicas de relajación.'],
                [10,14, 'Ansiedad moderada',          'Valorar intervención breve / derivación.'],
                [15,21, 'Ansiedad severa',            'Recomendable evaluación clínica integral.'],
            ];
            foreach ($rangos as [$min,$max,$dx,$desc]) {
                RangoTest::create([
                    'fkTest'      => $test->idTest,
                    'minPuntaje'  => $min,
                    'maxPuntaje'  => $max,
                    'diagnostico' => $dx,
                    'descripcion' => $desc,
                ]);
            }
        });
    }
}
