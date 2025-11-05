<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmocionPacienteController extends Controller
{
    public function index()
    {
        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();
        if (!$paciente) abort(403, 'No se encontró el paciente asociado.');

        $emociones = DB::table('Emociones as e')
            ->join('Actividades as a', 'a.idActividad', '=', 'e.fkActividad')
            ->select(
                'e.idEmocion',
                'a.titulo as actividad',
                'e.fechaHoraRegistro',
                'e.emocionesExperimentadas',
                'e.intensidades',
                'e.comentario'
            )
            ->where('e.fkPaciente', $paciente->id)
            ->orderByDesc('e.fechaHoraRegistro')
            ->get();

        return view('paciente.emociones.index', compact('emociones'));
    }

    public function create($idActividad)
    {
        return view('paciente.emociones.create', compact('idActividad'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fkActividad' => 'required|integer',
            'emocionesExperimentadas' => 'required|string', // ✅ Recibimos JSON string
            'intensidades' => 'required|array',             // ✅ Intensidades sigue siendo array
            'comentario' => 'nullable|string|max:500',
        ]);

        // Convertir emociones JSON → array
        $emocionesSel = json_decode($request->emocionesExperimentadas, true);
        if (!is_array($emocionesSel) || count($emocionesSel) === 0) {
            return back()->with('error', 'Selecciona al menos una emoción.');
        }

        // Ordenar intensidades por emoción
        $intensidadesFiltradas = [];
        foreach ($emocionesSel as $emo) {
            $val = (int)($request->intensidades[$emo] ?? 3);
            $intensidadesFiltradas[$emo] = max(1, min(5, $val));
        }

        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();

        DB::table('Emociones')->insert([
            'fkActividad' => $request->fkActividad,
            'fkPaciente' => $paciente->id,
            'emocionesExperimentadas' => json_encode($emocionesSel, JSON_UNESCAPED_UNICODE),
            'intensidades' => json_encode($intensidadesFiltradas, JSON_UNESCAPED_UNICODE),
            'comentario' => $request->comentario,
            'fechaHoraRegistro' => now(),
        ]);

        return redirect()->route('paciente.actividades.index')
    ->with('success', '¡Emoción registrada! Gracias por completar tu actividad 🤍');

    }

    public function edit($id)
    {
        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();
        $emocion = DB::table('Emociones')->where('idEmocion', $id)->where('fkPaciente', $paciente->id)->first();
        if (!$emocion) abort(403);

        return view('paciente.emociones.edit', compact('emocion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['comentario' => 'nullable|string|max:500']);

        $paciente = DB::table('Pacientes')->where('usuario_id', Auth::id())->first();

        DB::table('Emociones')
            ->where('idEmocion', $id)
            ->where('fkPaciente', $paciente->id)
            ->update(['comentario' => $request->comentario]);

        return redirect()->route('paciente.emociones.index')
            ->with('success', 'Comentario actualizado correctamente.');
    }
}
