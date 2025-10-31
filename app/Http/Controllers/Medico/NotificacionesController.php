<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;

class NotificacionesController extends Controller
{
    public function unreadCount()
    {
        $userId = Auth::id();
        $unread = Notificacion::where('fkUsuario', $userId)
            ->where('leida', 0)
            ->count();

        return response()->json(['unread' => $unread]);
    }

    public function index(Request $request)
    {
        $userId = Auth::id();

        $items = Notificacion::where('fkUsuario', $userId)
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($items);
        }

        // Vista (opcional si después quieres /medico/notificaciones)
        return view('medico.notificaciones.index', compact('items'));
    }

    public function markRead(Request $request, $id)
    {
        $userId = Auth::id();

        $n = Notificacion::where('fkUsuario', $userId)
            ->where('idNotificacion', $id)
            ->firstOrFail();

        if (!$n->leida) {
            $n->leida = 1;
            $n->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Notificación marcada como leída.');
    }

    public function markAllRead(Request $request)
    {
        $userId = Auth::id();

        Notificacion::where('fkUsuario', $userId)
            ->where('leida', 0)
            ->update(['leida' => 1]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }
}
