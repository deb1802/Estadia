<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Flash;

class CitaMedicoController extends Controller
{
    /**
     * Mostrar todas las citas del médico autenticado.
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();

        // ✅ Buscar el ID real del médico en la tabla Medicos
        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        // ✅ Obtener las citas del médico con nombre del paciente
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

        return view('medico.citas.index', compact('citas'));
    }

    /**
     * Formulario para crear una nueva cita.
     */
    public function create()
    {
        $usuario = Auth::user();

        // ✅ ID real del médico
        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        if (!$medicoId) {
            abort(403, 'No se pudo identificar al médico autenticado.');
        }

        // ✅ Pacientes asignados a este médico
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medicoId)
            ->select('p.id', 'u.nombre', 'u.apellido')
            ->orderBy('u.nombre')
            ->get();

        return view('medico.citas.create', compact('pacientes', 'medicoId'));
    }

    /**
     * Guardar una cita nueva en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fkPaciente' => 'required|exists:Pacientes,id',
            'fechaHora' => 'required|date',
            'motivo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
        ]);

        $usuario = Auth::user();

        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        DB::table('Citas')->insert([
            'fkMedico'   => $medicoId,
            'fkPaciente' => $request->fkPaciente,
            'fechaHora'  => $request->fechaHora,
            'motivo'     => $request->motivo,
            'ubicacion'  => $request->ubicacion,
            'estado'     => 'programada',
        ]);

        return redirect()->route('medico.citas.index');
    }

    /**
     * Mostrar los detalles de una cita.
     */
    public function show($id)
    {
        $usuario = Auth::user();

        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        $cita = DB::table('Citas as c')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('c.fkMedico', $medicoId)
            ->where('c.idCita', $id)
            ->select(
                'c.*',
                'u.nombre as paciente_nombre',
                'u.apellido as paciente_apellido'
            )
            ->first();

        if (!$cita) {
            Flash::error('No se encontró la cita.');
            return redirect()->route('medico.citas.index');
        }

        return view('medico.citas.show', compact('cita'));
    }

    /**
     * Formulario para editar una cita.
     */
    public function edit($id)
    {
        $cita = DB::table('Citas')->where('idCita', $id)->first();

        if (!$cita) {
            Flash::error('Cita no encontrada.');
            return redirect()->route('medico.citas.index');
        }

        $usuario = Auth::user();
        $medicoId = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->value('id');

        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medicoId)
            ->select('p.id', 'u.nombre', 'u.apellido')
            ->get();

        return view('medico.citas.edit', compact('cita', 'pacientes'));
    }

    /**
     * Actualizar una cita existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fkPaciente' => 'required|exists:Pacientes,id',
            'fechaHora' => 'required|date',
            'motivo' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'required|in:programada,realizada,cancelada',
        ]);

        DB::table('Citas')
            ->where('idCita', $id)
            ->update([
                'fkPaciente' => $request->fkPaciente,
                'fechaHora' => $request->fechaHora,
                'motivo' => $request->motivo,
                'ubicacion' => $request->ubicacion,
                'estado' => $request->estado,
            ]);


        return redirect()->route('medico.citas.index');
    }

    /**
     * Eliminar una cita.
     */
    public function destroy($id)
    {
        $cita = DB::table('Citas')->where('idCita', $id)->first();

        if (!$cita) {
            Flash::error('Cita no encontrada.');
            return redirect()->route('medico.citas.index');
        }

        DB::table('Citas')->where('idCita', $id)->delete();


        return redirect()->route('medico.citas.index');
    }
}
