<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Testimonio;
use App\Models\RespuestaTestimonio;
use App\Models\Paciente;

class RespuestaTestimonioController extends Controller
{
    public function store(Request $request, $idTestimonio)
    {
        // Validación
        $data = $request->validate([
            'contenido' => ['required','string','max:800'],
        ]);

        // Testimonio a responder
        $t = Testimonio::withCount('respuestas')->findOrFail($idTestimonio);

        // Límite 3 respuestas
        if ($t->respuestas_count >= 3) {
            return back()->withErrors('Este testimonio ya tiene 3 respuestas permitidas.');
        }

        // Paciente del usuario autenticado
        $userId   = Auth::user()->getKey(); // respeta tu PK (idUsuario)
        $paciente = Paciente::where('usuario_id', $userId)->firstOrFail();

        // Guardar respuesta
        RespuestaTestimonio::create([
            'fkTestimonio' => $t->idTestimonio,
            'fkPaciente'   => $paciente->id,
            'contenido'    => $data['contenido'],
            'fecha'        => now(),
        ]);

        return back()->with('success','Respuesta publicada.');
    }
}
