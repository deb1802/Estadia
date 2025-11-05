<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonio;
use App\Models\RespuestaTestimonio;
use Illuminate\Http\Request;

class TestimonioController extends Controller
{
    /**
     * Mostrar lista de testimonios con sus respuestas y usuarios.
     */
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

    /**
     * Eliminar un testimonio (y sus respuestas en cascada si aplica).
     */
    public function destroy(string $id)
    {
        $testimonio = Testimonio::findOrFail($id);

        // Verificar permiso (si usas policies)
        $this->authorize('delete', $testimonio);

        // Eliminar en cascada (si tu modelo no lo hace)
        if ($testimonio->respuestas()->exists()) {
            $testimonio->respuestas()->delete();
        }

        $testimonio->delete();

        // Retornar con mensaje flash
        return redirect()
            ->route('admin.testimonios.index')
            ->with('deleted', 'Eliminado correctamente');
    }

    /**
     * Eliminar una respuesta individual de un testimonio.
     */
    public function destroyRespuesta(string $idTestimonio, string $idRespuesta)
    {
        $respuesta = RespuestaTestimonio::findOrFail($idRespuesta);

        $this->authorize('delete', $respuesta);

        $respuesta->delete();

        return redirect()
            ->route('admin.testimonios.index')
            ->with('deleted', 'Eliminado correctamente');
    }
}
