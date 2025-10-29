<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActividadesAsignadasController extends Controller
{
   public function index(Request $request)
{
    // Médico autenticado
    $usuarioId = Auth::id();
    $medicoId = DB::connection('mysql')
        ->table('Medicos')
        ->where('usuario_id', $usuarioId)
        ->value('id');

    if (!$medicoId) {
        abort(403, 'No se pudo identificar al médico actual.');
    }

    // Filtros de la UI
    $estado = $request->query('estado');            // 'pendiente'|'completada'|null
    $f      = $request->query('f', '');             // ''|'paciente'|'diagnostico'|'tipo'
    $q      = trim((string) $request->query('q'));  // texto
    $qn     = \Illuminate\Support\Str::of($q)->lower()->trim()->value();

    // Query base
    $query = DB::connection('mysql')
        ->table('AsignacionActividad as aa')
        ->join('Actividades as a', 'a.idActividad', '=', 'aa.fkActividad')
        ->join('Pacientes as p', 'p.id', '=', 'aa.fkPaciente')
        ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
        ->select([
            'aa.idAsignacionActividad',
            'aa.estado',
            'aa.fechaAsignacion',
            'aa.fechaFinalizacion',
            'aa.indicaciones as indicacionesMedicas',
            'a.idActividad',
            'a.titulo',
            'a.tipoContenido',          // audio|video|lectura
            'a.categoriaTerapeutica',
            'a.diagnosticoDirigido',
            'a.nivelSeveridad',
            'a.recurso',
            'p.id as paciente_id',
            \DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,''))) as pacienteNombre")
        ])
        ->where('aa.fkMedico', $medicoId)
        ->orderByRaw("CASE WHEN aa.estado='pendiente' THEN 0 ELSE 1 END")
        ->orderByDesc('aa.fechaAsignacion');

    // Filtro: estado
    if (in_array($estado, ['pendiente', 'completada'], true)) {
        $query->where('aa.estado', $estado);
    }

    // Filtro: búsqueda
    if ($qn !== '') {
        if ($f === 'paciente') {
            $like = '%'.$qn.'%';
            $query->where(function ($qq) use ($like) {
                $qq->whereRaw('LOWER(u.nombre) LIKE ?', [$like])
                   ->orWhereRaw('LOWER(u.apellido) LIKE ?', [$like])
                   ->orWhereRaw("LOWER(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,''))) LIKE ?", [$like]);
            });
        } elseif ($f === 'diagnostico') {
            $like = '%'.$qn.'%';
            $query->whereRaw('LOWER(a.diagnosticoDirigido) LIKE ?', [$like]);
        } elseif ($f === 'tipo') {
            // Normalizamos audio|video|lectura (soporta variantes)
            $map = [
                'audio'   => 'audio',  'audios'  => 'audio',
                'video'   => 'video',  'vídeo'   => 'video', 'videos' => 'video',
                'lectura' => 'lectura','lecturas'=> 'lectura','leer'  => 'lectura',
            ];
            $canon = $map[$qn] ?? $qn;
            $query->whereRaw('LOWER(a.tipoContenido) = ?', [$canon]);
        } else {
            // Buscar en todo
            $like = '%'.$qn.'%';
            $query->where(function ($qq) use ($like) {
                $qq->whereRaw('LOWER(u.nombre) LIKE ?', [$like])
                   ->orWhereRaw('LOWER(u.apellido) LIKE ?', [$like])
                   ->orWhereRaw("LOWER(CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,''))) LIKE ?", [$like])
                   ->orWhereRaw('LOWER(a.diagnosticoDirigido) LIKE ?', [$like])
                   ->orWhereRaw('LOWER(a.titulo) LIKE ?', [$like])                // ⚠️ cambiar a nombreActividad si aplica
                   ->orWhereRaw('LOWER(a.tipoContenido) LIKE ?', [$like]);
            });
        }
    }

    // Paginación + mantener querystring
    $asignaciones = $query->paginate(12)->withQueryString();

    return view('medico.actividades_terap.actividadesAsignadas', [
        'asignaciones' => $asignaciones,
        'estado'       => $estado,
        'f'            => $f,
        'q'            => $q,
    ]);
}

}
