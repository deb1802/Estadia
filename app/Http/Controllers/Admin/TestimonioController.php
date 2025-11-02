<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonio;

class TestimonioController extends Controller
{
    // GET /admin/testimonios
    public function index()
    {
        $testimonios = Testimonio::with([
                'paciente.usuario',
                'respuestas.paciente.usuario',
            ])
            ->orderByDesc('fecha')
            ->paginate(10);

        return view('admin.testimonios.index', compact('testimonios'));
    }
}
