<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RecetaPacienteController extends Controller
{
    // Identifica al paciente autenticado
    private function pacienteActual()
    {
        $user = Auth::user(); // Usuarios.*
        abort_if(!$user, 401, 'No autenticado.');

        // Busca Pacientes.id por relacion o por usuario_id
        $paciente = DB::table('Pacientes')->where('usuario_id', $user->idUsuario)->first();
        abort_if(!$paciente, 403, 'Perfil de paciente no encontrado.');

        return $paciente;
    }

    // Lista de recetas del paciente
    public function index()
    {
        $paciente = $this->pacienteActual();

        $recetas = DB::table('RecetasMedicas as r')
            ->join('Medicos as m', 'm.id', '=', 'r.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->where('r.fkPaciente', $paciente->id)
            ->orderByDesc('r.fecha')
            ->orderByDesc('r.idReceta')
            ->get([
                'r.idReceta','r.fecha','r.observaciones',
                'um.nombre as medico_nombre', 'um.apellido as medico_apellido'
            ]);

        return view('paciente.recetas.index', compact('recetas'));
    }

    // Cargar receta completa y validar pertenencia del paciente
    private function cargarReceta(int $idReceta, int $pacienteId): array
    {
        $receta = DB::table('RecetasMedicas as r')
            ->join('Pacientes as p', 'p.id', '=', 'r.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id')
            ->join('Medicos as m', 'm.id', '=', 'r.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id')
            ->where('r.idReceta', $idReceta)
            ->first([
                'r.idReceta','r.fecha','r.observaciones',
                'r.fkPaciente','r.fkMedico',
                'up.nombre as paciente_nombre','up.apellido as paciente_apellido',
                'um.nombre as medico_nombre','um.apellido as medico_apellido',
                'm.cedulaProfesional','m.especialidad'
            ]);

        abort_if(!$receta, 404, 'La receta no existe.');
        abort_if((int)$receta->fkPaciente !== (int)$pacienteId, 403, 'No puedes ver esta receta.');

        $detalles = DB::table('Detalle_Medicamento as d')
            ->join('Medicamentos as med', 'med.idMedicamento', '=', 'd.fkMedicamento')
            ->where('d.fkReceta', $idReceta)
            ->orderBy('med.nombre')
            ->get([
                'd.idDetalleMedicamento',
                'med.idMedicamento',
                'med.nombre',
                'med.presentacion',
                'med.imagenMedicamento',
                'd.dosis','d.frecuencia','d.duracion',
            ]);

        return ['receta' => $receta, 'detalles' => $detalles];
    }

    // Ver receta en HTML
    public function show($idReceta)
    {
        $paciente = $this->pacienteActual();
        ['receta' => $receta, 'detalles' => $detalles] = $this->cargarReceta((int)$idReceta, (int)$paciente->id);

        return view('paciente.recetas.show', compact('receta','detalles'));
    }

    // Descargar/visualizar PDF (reusa tu template pro de médico)
    public function pdf($idReceta)
    {
        $paciente = $this->pacienteActual();
        ['receta' => $receta, 'detalles' => $detalles] = $this->cargarReceta((int)$idReceta, (int)$paciente->id);

        $pdf = Pdf::loadView('medico.recetas.pdf', [
            'receta'   => $receta,
            'detalles' => $detalles,
            'hoy'      => Carbon::now()->format('d/m/Y'),
        ])->setPaper('letter')
          ->setOption('dpi', 110)
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true);

        return $pdf->stream('Receta-'.$receta->idReceta.'.pdf');
    }
}
