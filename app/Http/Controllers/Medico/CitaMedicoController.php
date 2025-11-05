<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaCanceladaMail;
use App\Mail\CitaProgramadaMail;
use App\Models\Notificacion;
use Flash;
use Carbon\Carbon;

class CitaMedicoController extends Controller
{
    /** INDEX — Listar citas del médico */
    public function index()
    {
        $usuario = Auth::user();

        $citas = DB::table('Citas as c')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->select(
                'c.idCita',
                'c.fechaHora',
                'c.motivo',
                'c.ubicacion',
                'c.estado',
                'up.nombre as paciente_nombre',
                'up.apellido as paciente_apellido',
                'up.email as paciente_email'
            )
            ->orderBy('c.fechaHora', 'desc')
            ->paginate(10);

        return view('medico.citas.index', compact('citas'));
    }

    /** SHOW — Ver detalles de una cita específica */
    public function show($id)
    {
        $usuario = Auth::user();

        $cita = DB::table('Citas as c')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->where('c.idCita', $id)
            ->select(
                'c.idCita',
                'c.fechaHora',
                'c.motivo',
                'c.ubicacion',
                'c.estado',
                'up.nombre as paciente_nombre',
                'up.apellido as paciente_apellido',
                'up.email as paciente_email',
                'um.nombre as medico_nombre',
                'um.apellido as medico_apellido'
            )
            ->first();

        if (!$cita) {
            Flash::error('❌ Cita no encontrada o no pertenece a este médico.');
            return redirect()->route('medico.citas.index');
        }

        return view('medico.citas.show', compact('cita'));
    }

    /** EDIT — Formulario de edición */
    public function edit($id)
    {
        $usuario = Auth::user();

        $cita = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->where('c.idCita', $id)
            ->select('c.*')
            ->first();

        if (!$cita) {
            Flash::error('❌ Cita no encontrada o no pertenece a este médico.');
            return redirect()->route('medico.citas.index');
        }

        return view('medico.citas.edit', compact('cita'));
    }

    /** UPDATE — Guardar cambios (reprogramación / edición) */
    public function update(Request $request, $id)
    {
        $usuario = Auth::user();

        $request->validate([
            'fechaHora' => ['required','date'],     // datetime-local del form
            'motivo'    => ['required','string','max:65535'],
            'ubicacion' => ['required','string','max:150'],
            'estado'    => ['required','in:programada,realizada,cancelada'],
        ]);

        // Verifica pertenencia de la cita al médico autenticado
        $cita = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->where('c.idCita', $id)
            ->select('c.*')
            ->first();

        if (!$cita) {
            Flash::error('❌ Cita no encontrada o no pertenece a este médico.');
            return redirect()->route('medico.citas.index');
        }

        // Normaliza fecha/hora a zona MX
        $nuevaFechaHora = Carbon::parse($request->fechaHora, 'America/Mexico_City')
            ->setTimezone('America/Mexico_City')
            ->format('Y-m-d H:i:s');

        // Actualiza
        DB::table('Citas')
            ->where('idCita', $id)
            ->update([
                'fechaHora' => $nuevaFechaHora,
                'motivo'    => $request->motivo,
                'ubicacion' => $request->ubicacion,
                'estado'    => $request->estado,
            ]);

        // Datos del paciente para notificar
        $paciente = DB::table('Citas as c')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->where('c.idCita', $id)
            ->select('up.idUsuario','up.nombre','up.apellido','up.email')
            ->first();

        // Notificación en sistema
        Notificacion::create([
            'fkUsuario' => $paciente->idUsuario,
            'titulo'    => 'Cita reprogramada',
            'mensaje'   => 'Tu médico ' . trim($usuario->nombre.' '.$usuario->apellido)
                           . ' reprogramó tu cita para el '
                           . Carbon::parse($nuevaFechaHora, 'America/Mexico_City')->format('d/m/Y H:i') . ' hrs.',
            'tipo'      => 'sistema',
            'fecha'     => now(),
        ]);

        // Correo al paciente (usa tu mailable existente)
        if (!empty($paciente->email)) {
            Mail::to($paciente->email)->send(new CitaProgramadaMail(
                pacienteNombre: trim($paciente->nombre.' '.$paciente->apellido),
                medicoNombre:   trim($usuario->nombre.' '.$usuario->apellido),
                fechaHora:      $nuevaFechaHora,
                motivo:         $request->motivo,
                ubicacion:      $request->ubicacion,
            ));
        }

        Flash::success('✅ Cita actualizada y paciente notificado.');
        return redirect()->route('medico.citas.index');
    }

    /** CANCELAR — El médico cancela una cita */
    public function cancelar($id)
    {
        $usuario = Auth::user();

        $cita = DB::table('Citas as c')
            ->join('Pacientes as p', 'p.id', '=', 'c.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->where('c.idCita', $id)
            ->select(
                'c.*',
                'up.idUsuario as paciente_usuario_id',
                'up.nombre as paciente_nombre',
                'up.apellido as paciente_apellido',
                'up.email as paciente_email'
            )
            ->first();

        if (!$cita) {
            Flash::error('❌ Cita no encontrada o no pertenece a este médico.');
            return redirect()->route('medico.citas.index');
        }

        DB::table('Citas')->where('idCita', $id)->update(['estado' => 'cancelada']);

        // Notificación al paciente
        Notificacion::create([
            'fkUsuario' => $cita->paciente_usuario_id,
            'titulo'    => 'Cita cancelada',
            'mensaje'   => 'Tu médico ' . trim($usuario->nombre.' '.$usuario->apellido)
                           . ' canceló tu cita del '
                           . Carbon::parse($cita->fechaHora, 'America/Mexico_City')->format('d/m/Y H:i') . ' hrs.',
            'tipo'      => 'sistema',
            'fecha'     => now(),
        ]);

        // Enviar correo
        if (!empty($cita->paciente_email)) {
            Mail::to($cita->paciente_email)->send(new CitaCanceladaMail(
                medicoNombre:   trim($usuario->nombre.' '.$usuario->apellido),
                pacienteNombre: trim($cita->paciente_nombre.' '.$cita->paciente_apellido),
                fechaCita:      $cita->fechaHora,
                motivo:         $cita->motivo,
                ubicacion:      $cita->ubicacion,
                canceladaPor:   'medico'
            ));
        }

        Flash::success('✅ Cita cancelada y paciente notificado.');
        return redirect()->route('medico.citas.index');
    }
}
