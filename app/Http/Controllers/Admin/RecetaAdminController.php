<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RecetaAdminController extends Controller
{
    /**
     * GET /admin/recetas
     * Lista TODAS las recetas en tarjetas, con búsqueda por nombre del paciente (?q=)
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $query = DB::table('RecetasMedicas as r')
            ->join('Pacientes as p', 'p.id', '=', 'r.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id') // paciente
            ->join('Medicos as m', 'm.id', '=', 'r.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id') // médico
            ->select([
                'r.idReceta',
                'r.fecha',
                'r.observaciones',
                'r.fkPaciente',
                'r.fkMedico',
                'up.nombre as paciente_nombre',
                'up.apellido as paciente_apellido',
                'um.nombre as medico_nombre',
                'um.apellido as medico_apellido',
                'm.cedulaProfesional',
                'm.especialidad',
            ])
            ->orderByDesc('r.fecha')
            ->orderByDesc('r.idReceta');

        if ($q !== '') {
            // Buscar SOLO por nombre del paciente
            // Opción A: por nombre y/o apellido
            $query->where(function ($w) use ($q) {
                $w->where('up.nombre', 'like', "%{$q}%")
                  ->orWhere('up.apellido', 'like', "%{$q}%");
            });

            // Opción B (si tu MySQL lo permite): CONCAT
            // $query->whereRaw("CONCAT(up.nombre,' ',up.apellido) LIKE ?", ["%{$q}%"]);
        }

        // Paginar tarjetas (12 por página)
        $recetas = $query->paginate(12)->withQueryString();

        return view('admin.recetas.index', [
            'recetas' => $recetas,
            'q'       => $q,
        ]);
    }

    /**
     * Carga receta completa (sin validar pertenencia a paciente)
     */
    private function cargarReceta(int $idReceta): array
    {
        $receta = DB::table('RecetasMedicas as r')
            ->join('Pacientes as p', 'p.id', '=', 'r.fkPaciente')
            ->join('Usuarios as up', 'up.idUsuario', '=', 'p.usuario_id') // paciente
            ->join('Medicos as m', 'm.id', '=', 'r.fkMedico')
            ->join('Usuarios as um', 'um.idUsuario', '=', 'm.usuario_id') // médico
            ->where('r.idReceta', $idReceta)
            ->first([
                'r.idReceta','r.fecha','r.observaciones',
                'r.fkPaciente','r.fkMedico',
                'up.nombre as paciente_nombre','up.apellido as paciente_apellido',
                'um.nombre as medico_nombre','um.apellido as medico_apellido',
                'm.cedulaProfesional','m.especialidad',
            ]);

        abort_if(!$receta, 404, 'La receta no existe.');

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

    /**
     * GET /admin/recetas/{idReceta}
     * Ver receta en HTML (admin puede ver cualquiera)
     */
    public function show($idReceta)
    {
        ['receta' => $receta, 'detalles' => $detalles] = $this->cargarReceta((int)$idReceta);

        // Puedes reutilizar tu vista de paciente si quieres, o una específica de admin:
        // return view('paciente.recetas.show', compact('receta','detalles'));
        return view('admin.recetas.show', compact('receta','detalles'));
    }

    /**
     * GET /admin/recetas/{idReceta}/pdf
     * Descargar/visualizar PDF (reusa tu template pro de médico)
     */
    public function pdf($idReceta)
    {
        ['receta' => $receta, 'detalles' => $detalles] = $this->cargarReceta((int)$idReceta);

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
