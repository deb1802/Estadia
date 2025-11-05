<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\Notificacion;
use App\Mail\CitaCanceladaMail;
use Flash;
use Carbon\Carbon;

class CitaAdminController extends Controller
{
    /** INDEX — Listar todas las citas */
    public function index()
    {
        $citas = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->select(
                'c.idCita', 'c.fechaHora', 'c.motivo', 'c.ubicacion', 'c.estado',
                'um.nombre as medico_nombre', 'um.apellido as medico_apellido',
                'up.nombre as paciente_nombre', 'up.apellido as paciente_apellido',
                'up.email as paciente_email'
            )
            ->orderBy('c.fechaHora', 'desc')
            ->paginate(10);

        return view('admin.citas.index', compact('citas'));
    }

    /** SHOW — Ver detalles de una cita */
    public function show($id)
    {
        $cita = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->where('c.idCita', $id)
            ->select(
                'c.*',
                'um.nombre as medico_nombre', 'um.apellido as medico_apellido',
                'up.nombre as paciente_nombre', 'up.apellido as paciente_apellido',
                'up.email as paciente_email'
            )
            ->first();

        if (!$cita) {
            Flash::error('❌ Cita no encontrada.');
            return redirect()->route('admin.citas.index');
        }

        return view('admin.citas.show', compact('cita'));
    }

    /** DESTROY — Eliminar una cita */
    public function destroy($id)
    {
        $cita = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->where('c.idCita', $id)
            ->select(
                'c.*',
                'um.nombre as medico_nombre', 'um.apellido as medico_apellido',
                'up.nombre as paciente_nombre', 'up.apellido as paciente_apellido',
                'up.email as paciente_email',
                'up.idUsuario as paciente_usuario_id'
            )
            ->first();

        if (!$cita) {
            Flash::error('❌ Cita no encontrada.');
            return redirect()->route('admin.citas.index');
        }

        // Eliminar registro
        DB::table('Citas')->where('idCita', $id)->delete();

        // 🔹 Notificación
        Notificacion::create([
            'fkUsuario' => $cita->paciente_usuario_id,
            'titulo' => 'Cita eliminada por administrador',
            'mensaje' => 'Tu cita programada para el ' . Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') . ' ha sido eliminada del sistema.',
            'tipo' => 'sistema',
            'fecha' => now(),
        ]);

        // 🔹 Enviar correo
        if (!empty($cita->paciente_email)) {
            Mail::to($cita->paciente_email)->send(new CitaCanceladaMail(
                medicoNombre: $cita->medico_nombre . ' ' . $cita->medico_apellido,
                pacienteNombre: $cita->paciente_nombre . ' ' . $cita->paciente_apellido,
                fechaCita: $cita->fechaHora,
                motivo: $cita->motivo,
                ubicacion: $cita->ubicacion,
                canceladaPor: 'admin'
            ));
        }

        Flash::success('✅ Cita eliminada y paciente notificado.');
        return redirect()->route('admin.citas.index');
    }
}
