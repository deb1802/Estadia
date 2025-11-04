<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Repositories\TutorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Flash;

class TutorMedicoController extends Controller
{
    private TutorRepository $tutorRepository;

    public function __construct(TutorRepository $tutorRepo)
    {
        $this->tutorRepository = $tutorRepo;
    }

    private function base(): string
    {
        return 'medico.';
    }

    /** =====================================================
     *  INDEX — Búsqueda dinámica + filtro por médico
     * ===================================================== */
    public function index(Request $request)
    {
        $q = trim($request->get('search', ''));
        $type = $request->get('type', 'all');

        $query = \App\Models\Tutor::query()
            ->join('pacientes', 'tutores.fkPaciente', '=', 'pacientes.id')
            ->join('usuarios as uPac', 'pacientes.usuario_id', '=', 'uPac.idUsuario')
            ->select(
                'tutores.*',
                'uPac.nombre as paciente_nombre',
                'uPac.apellido as paciente_apellido'
            );

        // 🔍 Búsqueda dinámica
        if ($q !== '') {
            switch ($type) {
                case 'nombre':
                    $query->where(function ($qq) use ($q) {
                        $qq->where('tutores.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('tutores.apellido', 'LIKE', "%{$q}%");
                    });
                    break;
                case 'parentesco':
                    $query->where('tutores.parentesco', 'LIKE', "%{$q}%");
                    break;
                case 'paciente':
                    $query->where(function ($qq) use ($q) {
                        $qq->where('uPac.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('uPac.apellido', 'LIKE', "%{$q}%");
                    });
                    break;
                default:
                    $query->where(function ($qq) use ($q) {
                        $qq->where('tutores.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('tutores.apellido', 'LIKE', "%{$q}%")
                           ->orWhere('tutores.parentesco', 'LIKE', "%{$q}%")
                           ->orWhere('uPac.nombre', 'LIKE', "%{$q}%")
                           ->orWhere('uPac.apellido', 'LIKE', "%{$q}%");
                    });
                    break;
            }
        }

        // 🔹 Solo tutores de pacientes del médico autenticado
        $medicoId = auth()->user()->idUsuario ?? null;
        if ($medicoId) {
            $query->whereIn('pacientes.id', function ($sub) use ($medicoId) {
                $sub->select('id')
                    ->from('pacientes')
                    ->whereIn('medico_id', function ($sub2) use ($medicoId) {
                        $sub2->select('id')
                             ->from('medicos')
                             ->where('usuario_id', $medicoId);
                    });
            });
        }

        $tutors = $query->orderBy('idTutor', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('medico.tutores.table', compact('tutors'))->render();
        }

        return view('medico.tutores.index', compact('tutors', 'q', 'type'));
    }

    /** =====================================================
     *  SHOW — Detalle del tutor con datos de paciente
     * ===================================================== */
    public function show($id)
    {
        $tutor = DB::table('Tutores as t')
            ->leftJoin('Pacientes as p', 'p.id', '=', 't.fkPaciente')
            ->leftJoin('Usuarios as uPac', 'uPac.idUsuario', '=', 'p.usuario_id')
            ->select(
                't.*',
                'uPac.nombre as paciente_nombre',
                'uPac.apellido as paciente_apellido'
            )
            ->where('t.idTutor', $id)
            ->first();

        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route($this->base() . 'tutores.index');
        }

        return view('medico.tutores.show', compact('tutor'));
    }

    /** =====================================================
     *  CREATE — Formulario con pacientes del médico autenticado
     * ===================================================== */
    public function create()
    {
        $usuario = Auth::user();

        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->join('Medicos as m', 'm.id', '=', 'p.medico_id')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->select('p.id as paciente_id', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as display_name"))
            ->get();

        return view('medico.tutores.create', compact('pacientes'));
    }

    /** =====================================================
     *  STORE — Guardar nuevo tutor
     * ===================================================== */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:50',
            'apellido'      => 'required|string|max:50',
            'parentesco'    => 'nullable|string|max:50',
            'telefono'      => 'nullable|string|max:20',
            'correo'        => 'nullable|string|max:100',
            'direccion'     => 'nullable|string',
            'observaciones' => 'nullable|string',
            'fkPaciente'    => 'required|exists:Pacientes,id',
        ]);

        $this->tutorRepository->create($request->all());
        Flash::success('Tutor registrado correctamente.');

        return redirect()->route($this->base() . 'tutores.index');
    }

    /** =====================================================
     *  EDIT — Formulario de edición filtrado por médico
     * ===================================================== */
    public function edit($id)
    {
        $tutor = $this->tutorRepository->find($id);
        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route($this->base() . 'tutores.index');
        }

        $usuario = Auth::user();

        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->join('Medicos as m', 'm.id', '=', 'p.medico_id')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->select('p.id as paciente_id', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as display_name"))
            ->get();

        return view('medico.tutores.edit', compact('tutor', 'pacientes'));
    }

    /** =====================================================
     *  UPDATE — Actualizar tutor
     * ===================================================== */
    public function update($id, Request $request)
    {
        $tutor = $this->tutorRepository->find($id);
        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route($this->base() . 'tutores.index');
        }

        $this->tutorRepository->update($request->all(), $id);
        Flash::success('Tutor actualizado correctamente.');

        return redirect()->route($this->base() . 'tutores.index');
    }

    /** =====================================================
     *  DESTROY — Eliminar tutor
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
}
