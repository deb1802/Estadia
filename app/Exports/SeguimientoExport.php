<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SeguimientoExport implements FromArray, WithStyles, WithTitle
{
    protected $idPaciente;

    public function __construct($idPaciente)
    {
        $this->idPaciente = $idPaciente;
    }

    /**
     * 🔹 Generar los datos del Excel con el mismo contenido que la vista
     */
    public function array(): array
    {
        $paciente = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $this->idPaciente)
            ->select('u.nombre', 'u.apellido')
            ->first();

        $nombrePaciente = $paciente
            ? $paciente->nombre . ' ' . $paciente->apellido
            : 'Desconocido';

        $rows = [];

        // 🔹 Encabezado general
        $rows[] = ["Reporte de Seguimiento del Paciente: $nombrePaciente"];
        $rows[] = ['Fecha de generación:', now()->format('d/m/Y H:i')];
        $rows[] = [''];
        $rows[] = ['CITAS REALIZADAS'];
        $rows[] = ['Fecha', 'Motivo', 'Estado'];

        // 📅 Citas
        $citas = DB::table('Citas')
            ->where('fkPaciente', $this->idPaciente)
            ->orderBy('fechaHora')
            ->get();

        foreach ($citas as $c) {
            $rows[] = [
                Carbon::parse($c->fechaHora)->format('d/m/Y H:i'),
                $c->motivo,
                ucfirst($c->estado)
            ];
        }

        if ($citas->isEmpty()) {
            $rows[] = ['Sin citas registradas'];
        }

        // Espacio
        $rows[] = [''];
        $rows[] = ['EMOCIONES REGISTRADAS'];
        $rows[] = ['Fecha', 'Emociones experimentadas', 'Intensidad', 'Comentario'];

        // 💬 Emociones
        $emociones = DB::table('Emociones')
            ->where('fkPaciente', $this->idPaciente)
            ->orderBy('fechaHoraRegistro')
            ->get();

        foreach ($emociones as $e) {
            $rows[] = [
                Carbon::parse($e->fechaHoraRegistro)->format('d/m/Y H:i'),
                $e->emocionesExperimentadas,
                $e->intensidad,
                $e->comentario
            ];
        }

        if ($emociones->isEmpty()) {
            $rows[] = ['Sin emociones registradas'];
        }

        // Espacio
        $rows[] = [''];
        $rows[] = ['DIAGNÓSTICOS CLÍNICOS'];
        $rows[] = ['Fecha', 'Diagnóstico'];

        // 🩺 Diagnósticos
        $diagnosticos = DB::table('Expedientes')
            ->where('fkPaciente', $this->idPaciente)
            ->select('diagnosticos', 'fechaActualizacion')
            ->orderBy('fechaActualizacion', 'desc')
            ->get();

        foreach ($diagnosticos as $d) {
            $rows[] = [
                Carbon::parse($d->fechaActualizacion)->format('d/m/Y'),
                $d->diagnosticos
            ];
        }

        if ($diagnosticos->isEmpty()) {
            $rows[] = ['Sin diagnósticos clínicos registrados'];
        }

        return $rows;
    }

    /**
     * 🔹 Estilos generales del Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Encabezado principal
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4')->getFont()->setBold(true)->getColor()->setARGB('6A5ACD');
        $sheet->getStyle('A8')->getFont()->setBold(true)->getColor()->setARGB('6A5ACD');
        $sheet->getStyle('A12')->getFont()->setBold(true)->getColor()->setARGB('6A5ACD');

        // Ajuste automático de columnas
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Fondo suave para secciones
        $sheet->getStyle('A4:C4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E6E6FA');

        $sheet->getStyle('A8:D8')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E6E6FA');

        $sheet->getStyle('A12:B12')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E6E6FA');
    }

    public function title(): string
    {
        return 'Reporte de Seguimiento';
    }
}
