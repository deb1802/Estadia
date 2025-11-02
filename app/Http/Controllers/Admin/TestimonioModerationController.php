<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonio;
use App\Models\RespuestaTestimonio;
use Illuminate\Http\Request;

class TestimonioModerationController extends Controller
{
    /**
     * Elimina un testimonio completo junto con sus respuestas.
     * Ruta: DELETE /admin/testimonios/{idTestimonio}
     */
    public function destroyTestimonio($idTestimonio)
    {
        // Busca el testimonio con sus respuestas
        $testimonio = Testimonio::with('respuestas')
            ->where('idTestimonio', $idTestimonio)
            ->firstOrFail();

        // Autoriza (solo admin)
        $this->authorize('delete', $testimonio);

        // Elimina primero las respuestas (si tu FK no tiene cascade)
        if ($testimonio->respuestas && $testimonio->respuestas->count() > 0) {
            RespuestaTestimonio::where('fkTestimonio', $testimonio->idTestimonio)->delete();
        }

        // Luego el testimonio
        $testimonio->delete();

        return back()->with('success', '✅ Testimonio eliminado correctamente.');
    }

    /**
     * Elimina una respuesta individual.
     * Ruta: DELETE /admin/testimonios/{idTestimonio}/respuestas/{idRespuesta}
     */
    public function destroyRespuesta($idTestimonio, $idRespuesta)
    {
        // Busca la respuesta dentro del testimonio correcto
        $respuesta = RespuestaTestimonio::where('idRespuesta', $idRespuesta)
            ->where('fkTestimonio', $idTestimonio)
            ->firstOrFail();

        // Autoriza (solo admin)
        $this->authorize('delete', $respuesta);

        $respuesta->delete();

        return back()->with('success', '✅ Respuesta eliminada correctamente.');
    }
}
