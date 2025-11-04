<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Flash;

class TutorPacienteController extends Controller
{
    private function base(): string
    {
        return 'paciente.';
    }

    /** =====================================================
     *  INDEX — Lista con búsqueda AJAX de tutores del paciente autenticado
     * ===================================================== */
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $q = trim($request->get('search', ''));
        $type = $request->get('type', 'all');

        $query = DB::table('Tutores as t')
            ->join('Pacientes as p', 'p.id', '=', 't.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('u.idUsuario', $usuario->idUsuario)
            ->select(
                't.idTutor',
                't.nombre',
                't.apellido',
                't.parentesco',
                't.telefono',
                't.correo',
                't.observaciones'
            );

        // 🔍 Búsqueda dinámica
        if ($q !== '') {
            switch ($type) {
                case 'nombre':
                    $query->where(function ($qq) use ($q) {
                        $qq->where('t.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('t.apellido', 'LIKE', "%{$q}%");
                    });
                    break;

                case 'parentesco':
                    $query->where('t.parentesco', 'LIKE', "%{$q}%");
                    break;

                default:
                    $query->where(function ($qq) use ($q) {
                        $qq->where('t.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('t.apellido', 'LIKE', "%{$q}%")
                           ->orWhere('t.parentesco', 'LIKE', "%{$q}%");
                    });
                    break;
            }
        }

        $tutors = $query->orderBy('t.idTutor', 'desc')->paginate(10);

        // 🔁 Si es una solicitud AJAX, solo devolvemos la tabla (no el layout completo)
        if ($request->ajax()) {
            return view('paciente.tutores.table', compact('tutors'))->render();
        }

        return view('paciente.tutores.index', compact('tutors', 'q', 'type'));
    }

    /** =====================================================
     *  SHOW — Ver detalle del tutor seleccionado
     * ===================================================== */
    public function show($id)
    {
        $usuario = Auth::user();

        $tutor = DB::table('Tutores as t')
            ->join('Pacientes as p', 'p.id', '=', 't.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('u.idUsuario', $usuario->idUsuario)
            ->where('t.idTutor', $id)
            ->select(
                't.idTutor',
                't.nombre',
                't.apellido',
                't.parentesco',
                't.telefono',
                't.correo',
                't.direccion',
                't.observaciones'
            )
            ->first();

        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route($this->base() . 'tutores.index');
        }

        return view('paciente.tutores.show', compact('tutor'));
    }
}
