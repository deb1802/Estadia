<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\TutorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;

class TutorAdminController extends Controller
{
    private TutorRepository $tutorRepository;

    public function __construct(TutorRepository $tutorRepo)
    {
        $this->tutorRepository = $tutorRepo;
    }

    private function base(): string
    {
        return 'admin.';
    }

    /** =====================================================
     *  INDEX — con búsqueda dinámica AJAX
     * ===================================================== */
    public function index(Request $request)
    {
        $q = trim($request->get('search', ''));
        $type = $request->get('type', 'all');

        $query = DB::table('Tutores as t')
            ->leftJoin('Pacientes as p', 'p.id', '=', 't.fkPaciente')
            ->leftJoin('Usuarios as uPac', 'uPac.idUsuario', '=', 'p.usuario_id')
            ->select(
                't.*',
                'uPac.nombre as paciente_nombre',
                'uPac.apellido as paciente_apellido'
            );

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

                case 'paciente':
                    $query->where(function ($qq) use ($q) {
                        $qq->where('uPac.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('uPac.apellido', 'LIKE', "%{$q}%");
                    });
                    break;

                default:
                    $query->where(function ($qq) use ($q) {
                        $qq->where('t.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('t.apellido', 'LIKE', "%{$q}%")
                           ->orWhere('t.parentesco', 'LIKE', "%{$q}%")
                           ->orWhere('uPac.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('uPac.apellido', 'LIKE', "%{$q}%");
                    });
                    break;
            }
        }

        $tutors = $query->orderBy('t.idTutor', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.tutores.table', compact('tutors'))->render();
        }

        return view('admin.tutores.index', compact('tutors', 'q', 'type'));
    }

    /** =====================================================
     *  SHOW — Detalle de tutor
     * ===================================================== */
    public function show($id)
    {
        $tutor = $this->tutorRepository->find($id);

        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route($this->base() . 'tutores.index');
        }

        return view('admin.tutores.show', compact('tutor'));
    }

    /** =====================================================
     *  EDIT — Formulario de edición
     * ===================================================== */
    public function edit($id)
    {
        $tutor = $this->tutorRepository->find($id);

        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route($this->base() . 'tutores.index');
        }

        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->select('p.id as paciente_id', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as display_name"))
            ->get();

        return view('admin.tutores.edit', compact('tutor', 'pacientes'));
    }

    /** =====================================================
     *  UPDATE — Actualiza datos con mensaje visual
     * ===================================================== */
    public function update($id, Request $request)
    {
        $tutor = $this->tutorRepository->find($id);

        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route($this->base() . 'tutores.index');
        }

        $data = $request->validate([
            'nombre'        => 'required|string|max:50',
            'apellido'      => 'required|string|max:50',
            'parentesco'    => 'nullable|string|max:50',
            'telefono'      => 'nullable|string|max:20',
            'correo'        => 'nullable|email|max:100',
            'direccion'     => 'nullable|string',
            'observaciones' => 'nullable|string',
            'fkPaciente'    => 'required|integer|exists:Pacientes,id'
        ]);

        $this->tutorRepository->update($data, $id);

        Flash::success('Tutor actualizado correctamente.');
        return redirect()->route($this->base() . 'tutores.index');
    }

    /** =====================================================
     *  DESTROY — Elimina con mensaje y SweetAlert
     * ===================================================== */
    public function destroy($id)
    {
        $tutor = $this->tutorRepository->find($id);

        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route($this->base() . 'tutores.index');
        }

        $this->tutorRepository->delete($id);
        Flash::success('Tutor eliminado correctamente.');
        return redirect()->route($this->base() . 'tutores.index');
    }

    /** =====================================================
     *  BLOQUEAR CREATE / STORE para administrador
     * ===================================================== */
    public function create()
    {
        Flash::error('El administrador no puede registrar tutores.');
        return redirect()->route($this->base() . 'tutores.index');
    }

    public function store(Request $request)
    {
        Flash::error('El administrador no puede registrar tutores.');
        return redirect()->route($this->base() . 'tutores.index');
    }
}
