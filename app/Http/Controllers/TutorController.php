<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTutorRequest;
use App\Http\Requests\UpdateTutorRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\TutorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Flash;

class TutorController extends AppBaseController
{
    /** @var TutorRepository */
    private $tutorRepository;

    public function __construct(TutorRepository $tutorRepo)
    {
        $this->tutorRepository = $tutorRepo;
    }

    /**
     * 🧩 Mostrar lista de tutores
     * - Admin: ve todos los tutores.
     * - Médico: solo tutores de sus pacientes.
     * - Paciente: solo sus propios tutores.
     */
    public function index(Request $request)
{
    $usuario = Auth::user();

    // Si es administrador: ve todos los tutores
    if ($usuario->tipoUsuario === 'administrador') {
        $tutors = DB::table('Tutores as t')
            ->leftJoin('Pacientes as p', 'p.id', '=', 't.fkPaciente')
            ->leftJoin('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->select('t.*', 'u.nombre as paciente_nombre', 'u.apellido as paciente_apellido')
            ->orderBy('t.nombreCompleto')
            ->paginate(10);
    }

    // Si es médico: solo los de sus pacientes
    elseif ($usuario->tipoUsuario === 'medico') {
        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        $tutors = DB::table('Tutores as t')
            ->join('Pacientes as p', 'p.id', '=', 't.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medicoId)
            ->select('t.*', 'u.nombre as paciente_nombre', 'u.apellido as paciente_apellido')
            ->orderBy('t.nombreCompleto')
            ->paginate(10);
    }

    // Si es paciente: solo sus tutores
    else {
        $pacienteId = DB::table('Pacientes')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        $tutors = DB::table('Tutores as t')
            ->where('t.fkPaciente', $pacienteId)
            ->paginate(10);
    }

    if ($request->ajax()) {
        return view('tutors.table', compact('tutors'))->render();
    }

    return view('tutors.index', compact('tutors'));
}






    /**
     * 🩺 Formulario para crear un nuevo tutor
     * - Solo los médicos pueden crear.
     * - El médico solo ve a sus pacientes.
     */
    public function create()
    {
        $usuario = Auth::user();

        if (!$usuario->esRol('medico')) {
            Flash::error('No tienes permisos para crear tutores.');
            return redirect()->route('tutors.index');
        }

        // 🔹 Obtener pacientes del médico autenticado
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->join('Medicos as m', 'm.id', '=', 'p.medico_id')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->select('p.id as paciente_id', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as display_name"))
            ->orderBy('u.nombre')
            ->get();

        return view('tutors.create', compact('pacientes'));
    }

    /**
 * 🧠 Guardar nuevo tutor
 * - Solo médico.
 */
public function store(CreateTutorRequest $request)
{
    $usuario = Auth::user();

    if (!$usuario->esRol('medico')) {
        Flash::error('Solo los médicos pueden registrar tutores.');
        return redirect()->route(
            Auth::user()->tipoUsuario === 'medico'
                ? 'medico.tutores.index'
                : 'admin.tutores.index'
        );
    }

    $this->tutorRepository->create($request->all());

    Flash::success('Tutor registrado correctamente.');

    return redirect()->route(
        Auth::user()->tipoUsuario === 'medico'
            ? 'medico.tutores.index'
            : 'admin.tutores.index'
    );
}


    /**
     * 📄 Mostrar información de un tutor (lectura)
     */
    public function show($id)
    {
        $usuario = Auth::user();

        $tutor = DB::table('Tutores as t')
            ->join('Pacientes as p', 'p.id', '=', 't.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->select(
                't.*',
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as paciente_nombre")
            )
            ->where('t.idTutor', $id)
            ->first();

        if (!$tutor) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route('tutors.index');
        }

        // 🔹 Validación de acceso
        if ($usuario->esRol('paciente') && $tutor->fkPaciente != $usuario->paciente->id) {
            Flash::error('No tienes permiso para ver este tutor.');
            return redirect()->route('tutors.index');
        }

        return view('tutors.show', compact('tutor'));
    }

    /**
     * ✏️ Editar tutor (solo médico o admin)
     */
    public function edit($id)
    {
        $usuario = Auth::user();
        $tutor = $this->tutorRepository->find($id);

        if (empty($tutor)) {
            Flash::error('Tutor no encontrado');
            return redirect()->route('tutors.index');
        }

        if ($usuario->esRol('paciente')) {
            Flash::error('No tienes permiso para editar tutores.');
            return redirect()->route('tutors.index');
        }

        // 🔹 Pacientes disponibles
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->select('p.id as paciente_id', DB::raw("CONCAT(u.nombre, ' ', u.apellido) as display_name"))
            ->orderBy('u.nombre')
            ->get();

        return view('tutors.edit', compact('tutor', 'pacientes'));
    }

    /**
     * 💾 Actualizar tutor
     */
    public function update($id, UpdateTutorRequest $request)
    {
        $usuario = Auth::user();

        if ($usuario->esRol('paciente')) {
            Flash::error('No tienes permiso para actualizar tutores.');
            return redirect()->route('tutors.index');
        }

        $tutor = $this->tutorRepository->find($id);

        if (empty($tutor)) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route('tutors.index');
        }

        $this->tutorRepository->update($request->all(), $id);

        Flash::success('Tutor actualizado correctamente.');
        return redirect()->route('tutors.index');
    }

    /**
     * 🗑️ Eliminar tutor
     * - Solo médico o administrador.
     */
    public function destroy($id)
    {
        $usuario = Auth::user();

        if ($usuario->esRol('paciente')) {
            Flash::error('No tienes permiso para eliminar tutores.');
            return redirect()->route('tutors.index');
        }

        $tutor = $this->tutorRepository->find($id);

        if (empty($tutor)) {
            Flash::error('Tutor no encontrado.');
            return redirect()->route('tutors.index');
        }

        $this->tutorRepository->delete($id);

        Flash::success('Tutor eliminado correctamente.');
        return redirect()->route('tutors.index');
    }
}
