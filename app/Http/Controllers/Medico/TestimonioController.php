<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Testimonio;

class TestimonioController extends Controller
{
    // GET /medico/testimonios
    public function index()
    {
        $testimonios = Testimonio::with([
                'paciente.usuario',     // autor del testimonio + datos de usuario
                'respuestas.paciente.usuario', // autores de respuestas + usuario
            ])
            ->orderByDesc('fecha')
            ->paginate(10);

        // Usa la vista “solo lectura” para admin/medico
        return view('medico.testimonios.index', compact('testimonios'));
    }
}
