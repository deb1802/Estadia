<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class RecetaController extends Controller
{
    /**
     * Pantalla para crear la CABECERA de una receta.
     * Espera ?paciente={id} desde el botón en el show del paciente.
     * Vista: resources/views/medico/recetas/create.blade.php
     */
    public function create(Request $request)
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) abort(401, 'No autenticado.');

        $medico = DB::table('Medicos')
            ->select('id', 'usuario_id')
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$medico) abort(403, 'El usuario autenticado no está vinculado a un médico.');

        $pacienteId = (int) $request->query('paciente', 0);
        if ($pacienteId <= 0) abort(404, 'Falta ?paciente o es inválido.');

        $paciente = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $pacienteId)
            ->first(['p.id as idPaciente', 'u.nombre', 'u.apellido']);

        if (!$paciente) abort(404, 'El paciente no existe.');

        // Validar pertenencia
        if ((int) DB::table('Pacientes')->where('id', $pacienteId)->value('medico_id') !== (int) $medico->id) {
            abort(403, 'El paciente no pertenece al médico actual.');
        }

        return view('medico.recetas.create', [
            'medicoId'    => $medico->id,
            'paciente'    => $paciente,
            'hoy'         => Carbon::now()->toDateString(),
        ]);
    }

    /**
     * Crea la cabecera en RecetasMedicas y redirige a la pantalla de DETALLE.
     * Vista de destino: medico/recetas/detalle.blade.php
     */
    public function store(Request $request): RedirectResponse
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) abort(401, 'No autenticado.');

        $medico = DB::table('Medicos')
            ->select('id', 'usuario_id')
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$medico) abort(403, 'El usuario autenticado no está vinculado a un médico.');

        $data = $request->validate([
            'fkPaciente'    => ['required', 'integer', 'min:1'],
            'fecha'         => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
        ]);

        // Validate paciente existence & ownership
        $pac = DB::table('Pacientes')->select('id', 'medico_id')->where('id', $data['fkPaciente'])->first();
        if (!$pac) {
            throw ValidationException::withMessages(['fkPaciente' => 'El paciente no existe.']);
        }
        if ((int) $pac->medico_id !== (int) $medico->id) {
            throw ValidationException::withMessages(['fkPaciente' => 'El paciente no pertenece al médico actual.']);
        }

        $idReceta = DB::table('RecetasMedicas')->insertGetId([
            'fecha'         => $data['fecha'],
            'observaciones' => $data['observaciones'] ?? null,
            'fkMedico'      => $medico->id,         // Medicos.id
            'fkPaciente'    => $pac->id,            // Pacientes.id
        ]);

        // Redirigir a la pantalla de detalle para agregar medicamentos
        // (Ajustaremos la ruta más tarde; usamos un nombre de ruta sugerido)
        return redirect()->route('medico.recetas.detalle', ['idReceta' => $idReceta])
            ->with('success', 'Receta creada. Ahora agrega los medicamentos.');
    }

    /**
     * Muestra la pantalla para agregar uno o varios medicamentos a la receta.
     * Vista: resources/views/medico/recetas/detalle.blade.php
     */
    public function detalle($idReceta)
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) abort(401, 'No autenticado.');

        $medico = DB::table('Medicos')
            ->select('id', 'usuario_id')
            ->where('usuario_id', $usuarioId)
            ->first();
        if (!$medico) abort(403, 'El usuario autenticado no está vinculado a un médico.');

        // Cargar cabecera de receta (y validar que pertenezca al médico)
        $receta = DB::table('RecetasMedicas as r')
            ->join('Pacientes as p', 'p.id', '=', 'r.fkPaciente')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('r.idReceta', $idReceta)
            ->first([
                'r.idReceta', 'r.fecha', 'r.observaciones',
                'r.fkMedico', 'r.fkPaciente',
                'p.id as idPaciente',
                'u.nombre', 'u.apellido'
            ]);

        if (!$receta) abort(404, 'La receta no existe.');
        if ((int) $receta->fkMedico !== (int) $medico->id) abort(403, 'No puedes editar esta receta.');

        // Cargar catálogo de medicamentos
        $medicamentos = DB::table('Medicamentos')
            ->orderBy('nombre')
            ->get(['idMedicamento', 'nombre', 'presentacion']);

        // Cargar líneas existentes
        $detalles = DB::table('Detalle_Medicamento as d')
            ->leftJoin('Medicamentos as m', 'm.idMedicamento', '=', 'd.fkMedicamento')
            ->where('d.fkReceta', $idReceta)
            ->orderByRaw('COALESCE(m.nombre, "zzzz")') // que los NULL queden al final
            ->get([
                'd.idDetalleMedicamento',
                'd.fkMedicamento',
                DB::raw('COALESCE(m.nombre, "Medicamento eliminado") as medicamento'),
                DB::raw('COALESCE(m.presentacion, "-") as presentacion'),
                'd.dosis','d.frecuencia','d.duracion',
            ]);

        return view('medico.recetas.detalle', [
            'receta'       => $receta,
            'medicamentos' => $medicamentos,
            'detalles'     => $detalles,
        ]);
    }

    /**
     * Inserta una línea en Detalle_Medicamento para la receta dada.
     */
    public function agregarDetalle(Request $request, $idReceta): RedirectResponse
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) abort(401, 'No autenticado.');

        $medico = DB::table('Medicos')->select('id')->where('usuario_id', $usuarioId)->first();
        if (!$medico) abort(403, 'No autorizado.');

        // Validar receta del médico
        $receta = DB::table('RecetasMedicas')->where('idReceta', $idReceta)->first();
        if (!$receta) abort(404, 'La receta no existe.');
        if ((int) $receta->fkMedico !== (int) $medico->id) abort(403, 'No puedes editar esta receta.');

        // Validación
        $data = $request->validate([
            'fkMedicamento' => ['required', 'integer', 'min:1'],
            'dosis'         => ['required', 'string', 'max:100'],
            'frecuencia'    => ['required', 'string', 'max:100'],
            'duracion'      => ['required', 'string', 'max:100'],
        ]);

        // Verificar medicamento
        $exists = DB::table('Medicamentos')->where('idMedicamento', $data['fkMedicamento'])->exists();
        if (!$exists) {
            throw ValidationException::withMessages(['fkMedicamento' => 'El medicamento no existe.']);
        }

        // Insertar (respetando UNIQUE fkReceta+fkMedicamento)
        try {
            DB::table('Detalle_Medicamento')->insert([
                'fkReceta'      => $idReceta,
                'fkMedicamento' => (int) $data['fkMedicamento'],
                'dosis'         => $data['dosis'],
                'frecuencia'    => $data['frecuencia'],
                'duracion'      => $data['duracion'],
            ]);
        } catch (\Throwable $e) {
            // Si choca el UNIQUE, lo avisamos de forma amable
            if (str_contains(strtolower($e->getMessage()), 'duplicate') ||
                str_contains(strtolower($e->getMessage()), 'unique')) {
                return back()->withInput()->with('error', 'Ese medicamento ya está agregado en esta receta.');
            }
            report($e);
            return back()->withInput()->with('error', 'No se pudo agregar el medicamento: '.$e->getMessage());
        }

        return redirect()->route('medico.recetas.detalle', ['idReceta' => $idReceta])
            ->with('success', 'Medicamento agregado.');
    }

    /**
     * (Opcional) Eliminar una línea del detalle.
     */
    public function borrarDetalle($idReceta, $idDetalle): RedirectResponse
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) abort(401, 'No autenticado.');

        $medico = DB::table('Medicos')->select('id')->where('usuario_id', $usuarioId)->first();
        if (!$medico) abort(403, 'No autorizado.');

        // Validar que la línea sea de una receta del médico
        $detalle = DB::table('Detalle_Medicamento')->where('idDetalleMedicamento', $idDetalle)->first();
        if (!$detalle) abort(404, 'El detalle no existe.');

        $receta = DB::table('RecetasMedicas')->where('idReceta', $detalle->fkReceta)->first();
        if (!$receta || (int) $receta->fkMedico !== (int) $medico->id || (int) $receta->idReceta !== (int) $idReceta) {
            abort(403, 'No puedes modificar este detalle.');
        }

        DB::table('Detalle_Medicamento')->where('idDetalleMedicamento', $idDetalle)->delete();

        return redirect()->route('medico.recetas.detalle', ['idReceta' => $idReceta])
            ->with('success', 'Línea eliminada.');
    }
    private function cargarRecetaCompleta(int $idReceta, int $medicoId): array
    {
        // Cabecera
        $receta = DB::table('RecetasMedicas as r')
            ->join('Pacientes as p', 'p.id', '=', 'r.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')   // Paciente
            ->join('Medicos as m', 'm.id', '=', 'r.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')   // Médico
            ->where('r.idReceta', $idReceta)
            ->first([
                'r.idReceta', 'r.fecha', 'r.observaciones',
                'r.fkMedico', 'r.fkPaciente',
                'up.nombre as paciente_nombre', 'up.apellido as paciente_apellido',
                'um.nombre as medico_nombre', 'um.apellido as medico_apellido',
                'm.cedulaProfesional', 'm.especialidad',
            ]);

        if (!$receta) abort(404, 'La receta no existe.');
        if ((int) $receta->fkMedico !== (int) $medicoId) abort(403, 'No puedes ver esta receta.');

        // Detalle
        $detalles = DB::table('Detalle_Medicamento as d')
        ->leftJoin('Medicamentos as med', 'med.idMedicamento', '=', 'd.fkMedicamento')
        ->where('d.fkReceta', $idReceta)
        ->orderByRaw('COALESCE(med.nombre, "zzzz")')
        ->get([
            'd.idDetalleMedicamento',
            'med.idMedicamento',
            DB::raw('COALESCE(med.nombre, "Medicamento eliminado") as nombre'),
            DB::raw('COALESCE(med.presentacion, "-") as presentacion'),
            'd.dosis', 'd.frecuencia', 'd.duracion',
        ]);

        return ['receta' => $receta, 'detalles' => $detalles];
    }

    /**
     * 👁️ Vista HTML para ver la receta completa.
     */
    public function show($idReceta)
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) abort(401, 'No autenticado.');

        $medico = DB::table('Medicos')->select('id')->where('usuario_id', $usuarioId)->first();
        if (!$medico) abort(403, 'No autorizado.');

        ['receta' => $receta, 'detalles' => $detalles] = $this->cargarRecetaCompleta((int)$idReceta, (int)$medico->id);

        return view('medico.recetas.show', [
            'receta'   => $receta,
            'detalles' => $detalles,
        ]);
    }
    public function finalizar(Request $request, int $idReceta): RedirectResponse
{
    $usuarioId = Auth::id();
    if (!$usuarioId) abort(401, 'No autenticado.');

    $medico = DB::table('Medicos')->select('id', 'usuario_id')
        ->where('usuario_id', $usuarioId)->first();

    if (!$medico) abort(403, 'El usuario autenticado no está vinculado a un médico.');

    $receta = DB::table('RecetasMedicas')
        ->where('idReceta', $idReceta)
        ->first();

    if (!$receta) abort(404, 'Receta no encontrada.');
    if ((int)$receta->fkMedico !== (int)$medico->id)
        abort(403, 'No puedes finalizar esta receta.');

    // Asegurar que haya medicamentos
    $tieneDetalle = DB::table('Detalle_Medicamento')
        ->where('fkReceta', $idReceta)
        ->exists();

    if (!$tieneDetalle) {
        return back()->with('error', 'No puedes finalizar una receta vacía. Agrega al menos un medicamento.');
    }

    // Insertar la notificación
    try {
        $pacienteUsuario = DB::table('RecetasMedicas as r')
            ->join('Pacientes as p', 'p.id', '=', 'r.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->where('r.idReceta', $idReceta)
            ->value('up.idUsuario');

        $medicoUser = DB::table('Medicos as m')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'm.usuario_id')
            ->where('m.id', $medico->id)
            ->select('u.nombre', 'u.apellido')
            ->first();

        $medicoNombre = trim(($medicoUser->nombre ?? '') . ' ' . ($medicoUser->apellido ?? ''));

        $titulo  = 'Receta médica disponible';
        $mensaje = "Tu médico {$medicoNombre} finalizó una nueva receta médica. "
                 . "Puedes consultarla en el módulo de «Mis recetas».";

        if ($pacienteUsuario) {
            DB::table('Notificaciones')->insert([
                'fkUsuario' => (int)$pacienteUsuario,
                'titulo'    => $titulo,
                'mensaje'   => $mensaje,
                'tipo'      => 'sistema',
                'fecha'     => now(),
                'leida'     => 0,
            ]);
        }

    } catch (\Throwable $e) {
        report($e); // No interrumpe el flujo
    }

   return redirect("medico/pacientes/{$receta->fkPaciente}")
        ->with('success', 'Receta finalizada y paciente notificado correctamente.');
}


    /**
     * 🧾 Genera el PDF de la receta (formato clásico).
     */
    public function pdf($idReceta)
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) abort(401, 'No autenticado.');

        $medico = DB::table('Medicos')->select('id')->where('usuario_id', $usuarioId)->first();
        if (!$medico) abort(403, 'No autorizado.');

        ['receta' => $receta, 'detalles' => $detalles] = $this->cargarRecetaCompleta((int)$idReceta, (int)$medico->id);

        $pdf = Pdf::loadView('medico.recetas.pdf', [
            'receta'   => $receta,
            'detalles' => $detalles,
            'hoy'      => \Carbon\Carbon::now()->format('d/m/Y'),
        ])->setPaper('letter');

        $filename = 'Receta-'.$receta->idReceta.'-'.Str::slug($receta->paciente_nombre.'-'.$receta->paciente_apellido).'.pdf';
        return $pdf->stream($filename);
    }
}
