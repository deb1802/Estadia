<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTutorRequest;
use App\Http\Requests\UpdateTutorRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\TutorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <- usaremos Query Builder para el JOIN
use Flash;

class TutorController extends AppBaseController
{
    /** @var TutorRepository $tutorRepository */
    private $tutorRepository;

    public function __construct(TutorRepository $tutorRepo)
    {
        $this->tutorRepository = $tutorRepo;
    }

    /**
     * Display a listing of the Tutor.
     */
    public function index(Request $request)
    {
        $tutors = $this->tutorRepository->paginate(10);

        return view('tutors.index')
            ->with('tutors', $tutors);
    }

    /**
     * Show the form for creating a new Tutor.
     */
    public function create()
    {
        // Pacientes: INNER JOIN Pacientes (p) -> Usuarios (u) para "Nombre Apellido"
        // Tablas según tu BD: Pacientes.id, Pacientes.usuario_id -> Usuarios.idUsuario
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->select('p.id as paciente_id', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as display_name"))
            ->orderBy('u.nombre')
            ->orderBy('u.apellido')
            ->get();

        return view('tutors.create', compact('pacientes'));
    }

    /**
     * Store a newly created Tutor in storage.
     */
    public function store(CreateTutorRequest $request)
    {
        $input = $request->all();

        $tutor = $this->tutorRepository->create($input);

        Flash::success('Tutor guardado correctamente.');

        return redirect(route('tutors.index'));
    }

    /**
     * Display the specified Tutor.
     */
    public function show($id)
    {
        $tutor = $this->tutorRepository->find($id);

        if (empty($tutor)) {
            Flash::error('Tutor no encontrado');
            return redirect(route('tutors.index'));
        }

        return view('tutors.show')->with('tutor', $tutor);
    }

    /**
     * Show the form for editing the specified Tutor.
     */
    public function edit($id)
    {
        $tutor = $this->tutorRepository->find($id);

        if (empty($tutor)) {
            Flash::error('Tutor no encontrado');
            return redirect(route('tutors.index'));
        }

        // Misma lista para el select durante la edición
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->select('p.id as paciente_id', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as display_name"))
            ->orderBy('u.nombre')
            ->orderBy('u.apellido')
            ->get();

        return view('tutors.edit', compact('tutor', 'pacientes'));
    }

    /**
     * Update the specified Tutor in storage.
     */
    public function update($id, UpdateTutorRequest $request)
    {
        $tutor = $this->tutorRepository->find($id);

        if (empty($tutor)) {
            Flash::error('Tutor no encontrado');
            return redirect(route('tutors.index'));
        }

        $this->tutorRepository->update($request->all(), $id);

        Flash::success('Tutor actualizado correctamente.');

        return redirect(route('tutors.index'));
    }

    /**
     * Remove the specified Tutor from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $tutor = $this->tutorRepository->find($id);

        if (empty($tutor)) {
            Flash::error('Tutor no encontrado');
            return redirect(route('tutors.index'));
        }

        $this->tutorRepository->delete($id);

        Flash::success('Tutor eliminado correctamente.');

        return redirect(route('tutors.index'));
    }
}
