<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReporteEmocionalExport implements FromCollection, WithHeadings, WithStyles, WithDrawings, WithTitle
{
    protected $datos;

    public function __construct()
    {
        // 🔹 Obtener información completa (como la vista)
        $this->datos = DB::table('Expedientes')
            ->selectRaw('diagnosticos, COUNT(*) as total')
            ->whereNotNull('diagnosticos')
            ->groupBy('diagnosticos')
            ->orderByDesc('total')
            ->get();
    }

    /** 📊 Colección para el Excel */
    public function collection()
    {
        return $this->datos->map(fn($d) => [
            $d->diagnosticos,
            $d->total,
            round(($d->total / $this->datos->sum('total')) * 100, 2) . '%',
        ]);
    }

    /** 📋 Encabezados */
    public function headings(): array
    {
        return ['Diagnóstico', 'Total de Pacientes', 'Porcentaje'];
    }

    /** 🧾 Título de la hoja */
    public function title(): string
    {
        return 'Reporte de Clasificación';
    }

    /** 🎨 Estilos */
    public function styles(Worksheet $sheet)
    {
        // 🔹 Encabezado centrado y con color
        $sheet->getStyle('A1:C1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setRGB('E1D4F2');

        // 🔹 Centrar toda la tabla
        $sheet->getStyle('A:C')->getAlignment()->setHorizontal('center');

        // 🔹 Anchos
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);

        // 🔹 Bordes
        $rows = $sheet->getHighestRow();
        $sheet->getStyle("A1:C{$rows}")
            ->getBorders()->getAllBorders()->setBorderStyle('thin');

        // 🔹 Título general centrado arriba
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'Reporte de Clasificación por Estado Emocional');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

        // 🔹 Centrar la tabla
        $sheet->setShowGridlines(true);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(true);

        return [];
    }

    /** 🧷 Logo */
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
}
