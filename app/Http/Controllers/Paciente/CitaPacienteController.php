<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaCanceladaMail;
use App\Models\Notificacion;
use Flash;
use Carbon\Carbon;

class CitaPacienteController extends Controller
{
    /** INDEX — Mostrar citas del paciente */
    public function index()
    {
        $usuario = Auth::user();

        $citas = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as uM', 'uM.idUsuario', '=', 'm.usuario_id')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->where('p.usuario_id', $usuario->idUsuario)
            ->select(
                'c.idCita', 'c.fechaHora', 'c.motivo', 'c.ubicacion', 'c.estado',
                'uM.nombre as medico_nombre', 'uM.apellido as medico_apellido', 'uM.email as medico_email'
            )
            ->orderBy('c.fechaHora', 'desc')
            ->paginate(10);

        return view('paciente.citas.index', compact('citas'));
    }

    /** CANCELAR — El paciente cancela su cita */
    public function cancelar($id)
    {
        $usuario = Auth::user();

        $cita = DB::table('Citas as c')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as uM', 'uM.idUsuario', '=', 'm.usuario_id')
            ->where('p.usuario_id', $usuario->idUsuario)
            ->where('c.idCita', $id)
            ->select(
                'c.*', 'uM.idUsuario as medico_usuario_id',
                'uM.nombre as medico_nombre', 'uM.apellido as medico_apellido', 'uM.email as medico_email'
            )
            ->first();

        if (!$cita) {
            Flash::error('❌ Cita no encontrada o no pertenece a tu cuenta.');
            return redirect()->route('paciente.citas.index');
        }

        DB::table('Citas')->where('idCita', $id)->update(['estado' => 'cancelada']);

        Notificacion::create([
            'fkUsuario' => $cita->medico_usuario_id,
            'titulo' => 'Cita cancelada por paciente',
            'mensaje' => 'El paciente ' . $usuario->nombre . ' ha cancelado la cita del ' .
                         Carbon::parse($cita->fechaHora)->format('d/m/Y H:i'),
            'tipo' => 'sistema',
            'fecha' => now(),
        ]);

        if (!empty($cita->medico_email)) {
            Mail::to($cita->medico_email)->send(new CitaCanceladaMail(
                medicoNombre: $cita->medico_nombre . ' ' . $cita->medico_apellido,
                pacienteNombre: $usuario->nombre . ' ' . $usuario->apellido,
                fechaCita: $cita->fechaHora,
                motivo: $cita->motivo,
                ubicacion: $cita->ubicacion,
                canceladaPor: 'paciente'
            ));
        }

        Flash::success('✅ Has cancelado la cita y se notificó al médico.');
        return redirect()->route('paciente.citas.index');
    }
}
