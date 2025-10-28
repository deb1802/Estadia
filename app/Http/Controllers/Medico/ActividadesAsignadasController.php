<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notificacion; 
use Illuminate\Support\Facades\Mail;
use App\Mail\ActividadAsignadaMail;

class AsignacionActividadController extends Controller
{
    /**
     * GET /medico/actividades_terap/asignar?actividad=ID
     * Muestra el formulario para asignar una actividad a un paciente.
     */
    public function create(Request $request)
    {
        $actividadId = (int) $request->query('actividad');
        if ($actividadId <= 0) {
            return redirect()->route('medico.actividades_terap.index')
                ->withErrors(['actividad' => 'Falta el parámetro actividad en la URL.']);
        }

        // Actividad (en MySQL, por idActividad)
        $actividad = DB::connection('mysql')
            ->table('Actividades')
            ->where('idActividad', $actividadId)
            ->first();

        if (!$actividad) {
            return redirect()->route('medico.actividades_terap.index')
                ->withErrors(['actividad' => "No se encontró la actividad con ID {$actividadId}."]);
        }

        // Usuario logueado (Usuarios.idUsuario)
        $usuarioId = Auth::id();

        // id del MÉDICO (Medicos.id) a partir del usuario actual
        $medicoId = DB::connection('mysql')
            ->table('Medicos')
            ->where('usuario_id', $usuarioId)   // Medicos.usuario_id -> Usuarios.idUsuario
            ->value('id');

        if (!$medicoId) {
            return redirect()->route('medico.actividades_terap.index')
                ->withErrors(['medico' => 'No se pudo identificar al médico actual.']);
        }

        // Pacientes de ese médico (Pacientes.medico_id) + nombre desde Usuarios
        $pacientes = DB::connection('mysql')
            ->table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medicoId)
            ->orderBy('u.nombre')
            ->orderBy('u.apellido')
            ->get([
                'p.id', // Pacientes.id
                DB::raw("CONCAT(u.nombre,' ',u.apellido) as display_name"),
            ]);

        return view('medico.actividades_terap.create-asignacion', [
            'actividad' => $actividad,
            'pacientes' => $pacientes,
            'medicoId'  => $medicoId, // OJO: este es Medicos.id (no Usuarios.idUsuario)
        ]);
    }

    /**
     * POST /medico/actividades_terap/asignar
     * Crea el registro en AsignacionActividad.
     */
    public function store(Request $request)
    {
        // Validación de inputs
        $data = $request->validate([
            'fkActividad'       => ['required', 'integer'],
            'fkPaciente'        => ['required', 'integer'],   // Pacientes.id
            'fkMedico'          => ['required', 'integer'],   // Medicos.id
            'fechaFinalizacion' => ['nullable', 'date'],
            // 'observaciones'   => ['nullable', 'string', 'max:1000'], // si aún usas este campo en otra tabla
            // NUEVO: indicaciones (texto libre para decir “escucha este audio…”, “lee esta lectura…”, etc.)
            'indicaciones'      => ['nullable', 'string', 'max:10000'],
        ]);

        // 1) La actividad existe
        $actividad = DB::connection('mysql')
            ->table('Actividades')
            ->where('idActividad', $data['fkActividad'])
            ->first();

        if (!$actividad) {
            return back()->withErrors(['fkActividad' => 'La actividad seleccionada no existe.'])->withInput();
        }

        // 2) El médico del formulario coincide con el médico logueado
        $usuarioId = Auth::id();
        $medicoIdLogueado = DB::connection('mysql')
            ->table('Medicos')
            ->where('usuario_id', $usuarioId)
            ->value('id');

        if ((int)$medicoIdLogueado !== (int)$data['fkMedico']) {
            return back()->withErrors(['fkMedico' => 'El médico autenticado no coincide con el enviado.'])->withInput();
        }

        // 3) El paciente pertenece a ese médico (Pacientes.medico_id)
        $paciente = DB::connection('mysql')
            ->table('Pacientes')
            ->where('id', $data['fkPaciente'])
            ->where('medico_id', $data['fkMedico'])
            ->first();

        if (!$paciente) {
            return back()->withErrors(['fkPaciente' => 'El paciente no pertenece al médico actual.'])->withInput();
        }

        // 4) Inserta en AsignacionActividad
        $insert = [
            'fkActividad'       => $data['fkActividad'],
            'fkPaciente'        => $data['fkPaciente'],        // Pacientes.id
            'fkMedico'          => $data['fkMedico'],          // Medicos.id
            'fechaAsignacion'   => now()->toDateString(),
            'fechaFinalizacion' => $data['fechaFinalizacion'] ?: null,
            'estado'            => 'pendiente',
            // NUEVO: guarda indicaciones (puede ser texto largo con links, etc.)
            'indicaciones'      => $data['indicaciones'] ?? null,
            // 'observaciones'   => $data['observaciones'] ?? null, // si decides mantenerlo
        ];

        DB::connection('mysql')->table('AsignacionActividad')->insert($insert);

        /* =======================================
           Registrar notificación en BD
        ======================================= */

        // Buscar usuario del paciente
        $pacienteUser = DB::connection('mysql')
            ->table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $data['fkPaciente'])
            ->select('u.idUsuario', 'u.nombre', 'u.apellido')
            ->first();

        if ($pacienteUser) {
            $mensaje = 'Tu médico te ha asignado una nueva actividad terapéutica.';
            Notificacion::create([
                'fkUsuario' => $pacienteUser->idUsuario,
                'titulo'    => 'Nueva Actividad Asignada',
                'mensaje'   => $mensaje,
                'tipo'      => 'sistema',
                'fecha'     => now(),
            ]);
        }

        // === Obtener datos del paciente (nombre y correo) ===
        $pacienteRow = DB::connection('mysql')
            ->table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $data['fkPaciente'])
            ->select('u.idUsuario', 'u.nombre', 'u.apellido', 'u.email')
            ->first();

        // === Obtener nombre del médico ===
        $medicoRow = DB::connection('mysql')
            ->table('Medicos as m')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'm.usuario_id')
            ->where('m.id', $data['fkMedico'])
            ->select('u.nombre', 'u.apellido')
            ->first();

        // === Determinar el email del paciente ===
        $toEmail = null;
        if ($pacienteRow) {
            $toEmail = $pacienteRow->email ?? null;
        }

        // === Enviar correo si tenemos email ===
        if ($toEmail) {
            $pacienteNombre  = trim(($pacienteRow->nombre ?? '').' '.($pacienteRow->apellido ?? ''));
            $medicoNombre    = $medicoRow ? trim(($medicoRow->nombre ?? '').' '.($medicoRow->apellido ?? '')) : null;
            $actividadNombre = $actividad->nombreActividad ?? 'Actividad terapéutica';
            $actividadDesc   = $actividad->descripcion ?? null;

            // URL del botón del correo
            $urlAccion = url('/paciente/actividades');

            // ENVÍO SIN COLA:
            Mail::to($toEmail)->send(new ActividadAsignadaMail(
                pacienteNombre:       $pacienteNombre,
                medicoNombre:         $medicoNombre,
                actividadNombre:      $actividadNombre,
                actividadDescripcion: $actividadDesc,
                fechaAsignacion:      now(),
                fechaLimite:          $data['fechaFinalizacion'] ?? null,
                urlAccion:            $urlAccion
            ));

            // Registrar también en tu tabla Notificaciones como "correo"
            Notificacion::create([
                'fkUsuario' => $pacienteRow->idUsuario,
                'titulo'    => 'Correo enviado: Nueva Actividad',
                'mensaje'   => 'Se envió un correo con los detalles de la actividad asignada.',
                'tipo'      => 'correo',
                'fecha'     => now(),
            ]);
        }

        return redirect()
            ->route('medico.actividades_terap.index')
            ->with('success', 'Actividad asignada correctamente.');
    }
}
