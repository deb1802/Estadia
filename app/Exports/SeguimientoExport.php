<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class SeguimientoExport implements FromArray, WithStyles, WithTitle, WithDrawings
{
    protected $idPaciente;

    public function __construct($idPaciente)
    {
        $this->idPaciente = $idPaciente;
    }

    public function array(): array
    {
        $paciente = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.id', $this->idPaciente)
            ->select('u.nombre', 'u.apellido')
            ->first();

        $nombrePaciente = $paciente ? $paciente->nombre . ' ' . $paciente->apellido : 'Desconocido';

        $rows = [];

        // 🔹 Dejar espacio para el logo (filas 1-3) + fila 4 vacía
        $rows[] = [''];
        $rows[] = [''];
        $rows[] = [''];
        $rows[] = [''];

        // -------- INICIO DEL CONTENIDO DESDE FILA 5 --------
        $rows[] = ["Reporte de Seguimiento del Paciente: $nombrePaciente"];
        $rows[] = ['Fecha de generación:', now()->format('d/m/Y H:i')];
        $rows[] = [''];

        // 🟣 CITAS
        $rows[] = ['CITAS REALIZADAS'];
        $rows[] = ['Fecha', 'Motivo', 'Estado'];

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

        if ($citas->isEmpty()) $rows[] = ['Sin citas registradas'];

        $rows[] = [''];

        // 🟣 EMOCIONES
        $rows[] = ['EMOCIONES REGISTRADAS'];
        $rows[] = ['Fecha', 'Emociones experimentadas', 'Promedio Intensidad', 'Comentario'];

        $emociones = DB::table('Emociones')
            ->where('fkPaciente', $this->idPaciente)
            ->orderBy('fechaHoraRegistro')
            ->get();

        foreach ($emociones as $e) {
            $int = json_decode($e->intensidades, true) ?? [];
            $prom = count($int) ? array_sum($int) / count($int) : 0;

            $rows[] = [
                Carbon::parse($e->fechaHoraRegistro)->format('d/m/Y H:i'),
                $e->emocionesExperimentadas,
                round($prom, 1),
                $e->comentario
            ];
        }

        if ($emociones->isEmpty()) $rows[] = ['Sin emociones registradas'];

        $rows[] = [''];

        // 🟣 DIAGNÓSTICOS
        $rows[] = ['DIAGNÓSTICOS CLÍNICOS'];
        $rows[] = ['Fecha', 'Diagnóstico'];

        $diagnosticos = DB::table('Expedientes')
            ->where('fkPaciente', $this->idPaciente)
            ->orderBy('fechaActualizacion', 'desc')
            ->get();

        foreach ($diagnosticos as $d) {
            $rows[] = [
                Carbon::parse($d->fechaActualizacion)->format('d/m/Y'),
                $d->diagnosticos
            ];
        }

        if ($diagnosticos->isEmpty()) $rows[] = ['Sin diagnósticos clínicos registrados'];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // 🔹 Título centrado
        $sheet->mergeCells('A5:E5');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');

        // 🔹 Colorear encabezados de secciones
        $sheet->getStyle('A8:C8')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('D9C6EB');

        $sheet->getStyle('A12:D12')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('D9C6EB');

        $sheet->getStyle('A16:B16')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('D9C6EB');

        // 🔹 Bordes SOLO en encabezados
        $sheet->getStyle('A8:C8')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A12:D12')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A16:B16')->getBorders()->getAllBorders()->setBorderStyle('thin');

        // 🔹 Ajustar ancho de columnas
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Mindware Logo');
        $drawing->setDescription('Logo de Mindware');
        $drawing->setPath(public_path('img/mindware-logo.png'));
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    public function title(): string
    {
        return 'Reporte de Seguimiento';
    }
}
