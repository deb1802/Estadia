<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\CitaRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Flash;

class CitaController extends AppBaseController
{
    /** @var CitaRepository $citaRepository*/
    private $citaRepository;

    public function __construct(CitaRepository $citaRepo)
    {
        $this->citaRepository = $citaRepo;
    }

    /**
     * Display a listing of the Cita.
     */
    public function index(Request $request)
{
    $usuario = Auth::user();

    // ✅ Obtener ID real del médico en la tabla Medicos
    $medicoId = DB::table('Medicos')
        ->where('usuario_id', $usuario->idUsuario)
        ->value('id');

    // ✅ Obtener las citas con nombre completo del paciente
    $citas = DB::table('Citas as c')
        ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
        ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
        ->where('c.fkMedico', $medicoId)
        ->select(
            'c.idCita',
            'c.fechaHora',
            'c.motivo',
            'c.ubicacion',
            'c.estado',
            'u.nombre as paciente_nombre',
            'u.apellido as paciente_apellido'
        )
        ->orderBy('c.fechaHora', 'asc')
        ->paginate(10);

    return view('citas.index', compact('citas'));
}

    /**
     * Show the form for creating a new Cita.
     */
    public function create()
{
    $usuario = Auth::user();

    // ✅ Buscar el ID real del médico en la tabla Medicos (clave primaria)
    $medicoId = DB::table('Medicos')
        ->where('usuario_id', $usuario->idUsuario)
        ->value('id');

    if (!$medicoId) {
        abort(403, 'No se pudo identificar al médico autenticado.');
    }

    // ✅ Traer solo los pacientes asignados a este médico
    $pacientes = DB::table('Pacientes as p')
        ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
        ->where('p.medico_id', $medicoId)
        ->select('p.id', 'u.nombre', 'u.apellido')
        ->orderBy('u.nombre')
        ->get();

    return view('citas.create', compact('pacientes', 'medicoId'));
}


    /**
     * Store a newly created Cita in storage.
     */
    public function store(CreateCitaRequest $request)
    {
        $input = $request->all();

        $cita = $this->citaRepository->create($input);

        Flash::success('Cita saved successfully.');

        return redirect(route('medico.citas.index'));
    }

    /**
     * Display the specified Cita.
     */
    public function show($id)
    {
        $cita = $this->citaRepository->find($id);

        if (empty($cita)) {
            Flash::error('Cita not found');

            return redirect(route('citas.index'));
        }

        return view('citas.show')->with('cita', $cita);
    }

    /**
     * Show the form for editing the specified Cita.
     */
    public function edit($id)
    {
        $cita = $this->citaRepository->find($id);

        if (empty($cita)) {
            Flash::error('Cita not found');

            return redirect(route('medico.citas.index'));
        }

        return view('medico.citas.edit')->with('cita', $cita);
    }

    /**
     * Update the specified Cita in storage.
     */
    public function update($id, UpdateCitaRequest $request)
    {
        $cita = $this->citaRepository->find($id);

        if (empty($cita)) {
            Flash::error('Cita not found');

            return redirect(route('medico.citas.index'));
        }

        $cita = $this->citaRepository->update($request->all(), $id);

        Flash::success('Cita updated successfully.');

        return redirect(route('medico.citas.index'));
    }

    /**
     * Remove the specified Cita from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $cita = $this->citaRepository->find($id);

        if (empty($cita)) {
            Flash::error('Cita not found');

            return redirect(route('medico.citas.index'));
        }

        $this->citaRepository->delete($id);

        Flash::success('Cita deleted successfully.');

        return redirect(route('medico.citas.index'));
    }
}
