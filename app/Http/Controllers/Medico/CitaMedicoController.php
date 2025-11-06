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
    public function index(Request $request)
{
    $usuario = Auth::user();

    $query = DB::table('Citas as c')
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
    'p.id as idPaciente',
    'up.nombre as paciente_nombre',
    'up.apellido as paciente_apellido',
    'um.nombre as medico_nombre',
    'um.apellido as medico_apellido'
        );


    // 🔍 Filtros de búsqueda
    if ($request->filled('search')) {
        $search = $request->search;
        $type = $request->type;

        $query->when($type === 'paciente', fn($q) => 
            $q->where('up.nombre', 'LIKE', "%$search%")
        )->when($type === 'estado', fn($q) => 
            $q->where('c.estado', 'LIKE', "%$search%")
        )->when($type === 'fecha', fn($q) => 
            $q->whereDate('c.fechaHora', $search)
        )->when($type === 'all', fn($q) =>
            $q->where(function ($q) use ($search) {
                $q->where('up.nombre', 'LIKE', "%$search%")
                  ->orWhere('c.estado', 'LIKE', "%$search%")
                  ->orWhereDate('c.fechaHora', $search);
            })
        );
    }

    $citas = $query->orderBy('c.fechaHora', 'DESC')->paginate(8);

    // ✅ Si es AJAX, solo regresamos la tabla (sin duplicar la vista)
    if ($request->ajax()) {
        return view('medico.citas.table', compact('citas'))->render();
    }

    // ✅ Vista normal
    return view('medico.citas.index', compact('citas'));
}


    /** CREATE — Formulario de creación de cita */
    public function create()
    {
        $usuario = Auth::user();

        $medico = DB::table('Medicos')
            ->where('usuario_id', $usuario->idUsuario)
            ->first();

        if (!$medico) {
            Flash::error('No se encontró el perfil del médico.');
            return redirect()->route('medico.citas.index');
        }

        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medico->id)
            ->select('p.id', DB::raw("CONCAT(u.nombre,' ',u.apellido) as nombre"))
            ->orderBy('u.apellido')
            ->orderBy('u.nombre')
            ->get();

        return view('medico.citas.create', compact('pacientes', 'medico'));
    }

    /** STORE — Guardar la nueva cita */
    public function store(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'fkPaciente' => ['required', 'integer', 'exists:Pacientes,id'],
            'fechaHora'  => ['required', 'date'],
            'motivo'     => ['required', 'string', 'max:65535'],
            'ubicacion'  => ['required', 'string', 'max:150'],
        ]);

        $medico = DB::table('Medicos')->where('usuario_id', $usuario->idUsuario)->first();
        if (!$medico) {
            Flash::error('No se encontró el médico asignado a este usuario.');
            return redirect()->route('medico.citas.index');
        }

        $fechaHora = Carbon::parse($request->fechaHora, 'America/Mexico_City')
            ->setTimezone('America/Mexico_City')
            ->format('Y-m-d H:i:s');

        $idCita = DB::table('Citas')->insertGetId([
            'fkMedico'  => $medico->id,
            'fkPaciente'=> $request->fkPaciente,
            'fechaHora' => $fechaHora,
            'motivo'    => $request->motivo,
            'ubicacion' => $request->ubicacion,
            'estado'    => 'programada',
        ]);

        $paciente = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $request->fkPaciente)
            ->select('u.idUsuario','u.nombre','u.apellido','u.email')
            ->first();

        Notificacion::create([
            'fkUsuario' => $paciente->idUsuario,
            'titulo'    => 'Nueva cita programada',
            'mensaje'   => 'Tu médico ' . trim($usuario->nombre.' '.$usuario->apellido)
                . ' programó una cita para el '
                . Carbon::parse($fechaHora)->format('d/m/Y H:i') . ' hrs.',
            'tipo'      => 'sistema',
            'fecha'     => now(),
        ]);

        if (!empty($paciente->email)) {
            Mail::to($paciente->email)->send(new CitaProgramadaMail(
                pacienteNombre: trim($paciente->nombre.' '.$paciente->apellido),
                medicoNombre:   trim($usuario->nombre.' '.$usuario->apellido),
                fechaHora:      $fechaHora,
                motivo:         $request->motivo,
                ubicacion:      $request->ubicacion,
            ));
        }

        Flash::success('✅ Cita creada y paciente notificado.');
        return redirect()->route('medico.citas.index');
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
                'c.idCita','c.fechaHora','c.motivo','c.ubicacion','c.estado',
                'up.nombre as paciente_nombre','up.apellido as paciente_apellido','up.email as paciente_email',
                'um.nombre as medico_nombre','um.apellido as medico_apellido'
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

    /** UPDATE — Guardar cambios */
    public function update(Request $request, $id)
    {
        $usuario = Auth::user();

        $request->validate([
            'fechaHora' => ['required','date'],
            'motivo'    => ['required','string','max:65535'],
            'ubicacion' => ['required','string','max:150'],
            'estado'    => ['required','in:programada,realizada,cancelada'],
        ]);

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

        $nuevaFechaHora = Carbon::parse($request->fechaHora, 'America/Mexico_City')
            ->setTimezone('America/Mexico_City')
            ->format('Y-m-d H:i:s');

        DB::table('Citas')->where('idCita', $id)
            ->update([
                'fechaHora' => $nuevaFechaHora,
                'motivo'    => $request->motivo,
                'ubicacion' => $request->ubicacion,
                'estado'    => $request->estado,
            ]);

        Flash::success('✅ Cita actualizada.');
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

        Notificacion::create([
            'fkUsuario' => $cita->paciente_usuario_id,
            'titulo'    => 'Cita cancelada',
            'mensaje'   => 'Tu médico '.trim($usuario->nombre.' '.$usuario->apellido)
                .' canceló tu cita del '.Carbon::parse($cita->fechaHora)->format('d/m/Y H:i').' hrs.',
            'tipo'      => 'sistema',
            'fecha'     => now(),
        ]);

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

    /** DESTROY — Eliminar cita definitivamente */
    public function destroy($id)
    {
        $usuario = Auth::user();

        $cita = DB::table('Citas as c')
            ->join('Medicos as m', 'm.id', '=', 'c.fkMedico')
            ->where('m.usuario_id', $usuario->idUsuario)
            ->where('c.idCita', $id)
            ->select('c.idCita')
            ->first();

        if (!$cita) {
            Flash::error('❌ La cita no existe o no pertenece a tu perfil.');
            return redirect()->route('medico.citas.index');
        }

        DB::table('Citas')->where('idCita', $id)->delete();

        Flash::success('🗑️ Cita eliminada correctamente.');
        return redirect()->route('medico.citas.index');
    }
}
