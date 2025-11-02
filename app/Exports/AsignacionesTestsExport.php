<?php

namespace App\Exports;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AsignacionesTestsExport implements FromQuery, WithMapping, WithHeadings, WithCustomStartCell, WithEvents, WithDrawings, ShouldAutoSize
{
    protected QueryBuilder $query;
    protected ?string $logoPath;

    /** Aliases tolerantes por cada campo */
    protected array $aliases = [
        'id'          => ['id', 'idAsignacionTest', 'id_asignacion', 'id_asignacion_test'],
        'doctor'      => ['doctor','medico','doctor_nombre','medico_nombre','nombre_medico','m_nombre'],
        'paciente'    => ['paciente','paciente_nombre','nombre_paciente','p_nombre'],
        'fecha'       => ['fecha','fechaAsignacion','fecha_asignacion','created_at','asignado_en'],
        'tipo_test'   => ['tipo_test','test','tipo','nombre_test'],
        'estado'      => ['estado','status','estatus'],
        'diagnostico' => ['diagnostico_preliminar','diagnostico','resultado_preliminar'],
    ];

    public function __construct(QueryBuilder $query, ?string $logoPath = null)
    {
        $this->query    = $query;
        $this->logoPath = $logoPath && is_file($logoPath) ? $logoPath : null;
    }

    /** Utilidad: obtiene el primer alias existente de $row */
    protected function val($row, array $keys, $default = '')
    {
        foreach ($keys as $k) {
            if (is_object($row) && isset($row->$k) && $row->$k !== '' && $row->$k !== null) {
                return $row->$k;
            }
            if (is_array($row) && array_key_exists($k, $row) && $row[$k] !== '' && $row[$k] !== null) {
                return $row[$k];
            }
        }
        return $default;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Doctor que asignó',
            'Paciente',
            'Fecha de asignación',
            'Tipo de test',
            'Estado',
            'Diagnóstico preliminar',
        ];
    }

    public function query()
    {
        return $this->query;
    }

    public function map($row): array
    {
        // Campos con fallbacks
        $id        = (string) $this->val($row, $this->aliases['id'], '');
        $doctor    = (string) $this->val($row, $this->aliases['doctor'], '—');
        $paciente  = (string) $this->val($row, $this->aliases['paciente'], '—');
        $fechaRaw  =        $this->val($row, $this->aliases['fecha'], '');
        $fecha     = $fechaRaw ? str_replace('T', ' ', (string) $fechaRaw) : '';
        $tipoTest  = (string) $this->val($row, $this->aliases['tipo_test'], '—');
        $estado    = (string) $this->val($row, $this->aliases['estado'], 'pendiente');
        $diag      = (string) $this->val($row, $this->aliases['diagnostico'], '');

        return [$id, $doctor, $paciente, $fecha, $tipoTest, $estado, $diag];
    }

    public function drawings()
    {
        if (!$this->logoPath) return [];

        $drawing = new Drawing();
        $drawing->setPath($this->logoPath);
        $drawing->setHeight(52);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(2);
        $drawing->setOffsetY(2);

        return [$drawing];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Título
                $titleRange = 'A2:G2';
                $sheet->mergeCells($titleRange);
                $sheet->setCellValue('A2', 'REPORTE DE TESTS ASIGNADOS');
                $sheet->getStyle($titleRange)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '1B2A4A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Subtítulo
                $subtitleRange = 'A3:G3';
                $sheet->mergeCells($subtitleRange);
                $sheet->setCellValue('A3', 'Generado: ' . now()->format('Y-m-d H:i:s'));
                $sheet->getStyle($subtitleRange)->applyFromArray([
                    'font' => ['size' => 11, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Encabezados
                $headerRange = 'A5:G5';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1B2A4A']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D7DFE9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Alineaciones
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A6:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D6:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F6:F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Bordes finos
                $sheet->getStyle("A5:G{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
