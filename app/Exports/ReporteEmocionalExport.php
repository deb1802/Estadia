<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReporteEmocionalExport implements FromCollection, WithHeadings, WithStyles, WithDrawings, WithTitle, WithCustomStartCell
{
    protected $datos;

    public function __construct()
    {
        $this->datos = DB::table('Expedientes')
            ->selectRaw('diagnosticos, COUNT(*) as total')
            ->whereNotNull('diagnosticos')
            ->groupBy('diagnosticos')
            ->orderByDesc('total')
            ->get();
    }

    /** 📊 Datos */
    public function collection()
    {
        $total = max(1, $this->datos->sum('total')); // evitar división por 0
        return $this->datos->map(fn($d) => [
            $d->diagnosticos,
            $d->total,
            round(($d->total / $total) * 100, 2) . '%',
        ]);
    }

    /** 📋 Encabezados */
    public function headings(): array
    {
        return ['Diagnóstico', 'Total de Pacientes', 'Porcentaje'];
    }

    /** ▶️ La tabla empieza en A5 (1–3 logo, 4 título) */
    public function startCell(): string
    {
        return 'A5';
    }

    /** 🎨 Estilos */
    public function styles(Worksheet $sheet)
    {
        // Fila 4: Título centrado
        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'Reporte de Clasificación por Estado Emocional');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal('center');

        // Fila 5: Encabezados de la tabla
        $sheet->getStyle('A5:C5')->getFont()->setBold(true);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:C5')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setRGB('CFC1E8'); // lila pastel

        // Columnas centradas y anchos
        $sheet->getStyle('A:C')->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);

        // Bordes solo para tabla (desde encabezados hacia abajo)
        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 5) {
            $sheet->getStyle("A5:C{$lastRow}")
                ->getBorders()->getAllBorders()->setBorderStyle('thin');
        }

        // Opcional: más espacio bajo el logo
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(22);

        return [];
    }

    /** 🧷 Logo (ocupa filas 1–3) */
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Mindware Logo');
        $drawing->setDescription('Logo de Mindware');
        $drawing->setPath(public_path('img/mindware-logo.png'));
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');
        // Si quieres separarlo más del título:
        // $drawing->setOffsetY(2);
        return $drawing;
    }

    /** 🏷 Título de la hoja */
    public function title(): string
    {
        return 'Reporte de Clasificación';
    }
}
