<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonio;
use App\Models\Paciente;
use Illuminate\Support\Facades\Auth;

class TestimonioController extends Controller
{
    // GET /paciente/testimonios
    public function index()
    {
        $testimonios = Testimonio::with([
                'paciente',                    // autor del testimonio
                'respuestas.paciente'          // autores de respuestas
            ])
            ->orderByDesc('fecha')
            ->paginate(10);

        return view('paciente.testimonios.index', compact('testimonios'));
    }

    // POST /paciente/testimonios
    public function store(Request $request)
    {
        $data = $request->validate([
            'contenido' => ['required','string','max:2000'],
        ]);

        // Detectar el id del usuario autenticado (idUsuario o id)
         $userId = Auth::user()->getKey();

        // Buscar el paciente ligado a ese usuario
        $paciente = Paciente::where('usuario_id', $userId)->firstOrFail();

        Testimonio::create([
            'fkPaciente' => $paciente->id,            // ojo: tu PK en Pacientes es 'id'
            'fecha'      => now()->toDateString(),
            'contenido'  => $data['contenido'],
        ]);

        return back()->with('success', '¡Gracias por compartir tu experiencia!');
    }
}
