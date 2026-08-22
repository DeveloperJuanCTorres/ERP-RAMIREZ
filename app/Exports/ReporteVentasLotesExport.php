<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ReporteVentasLotesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    protected $ventas;
    protected $total_cantidad;
    protected $total_venta;
    protected $fila = 0;

    public function __construct($ventas, $total_cantidad, $total_venta)
    {
        $this->ventas = $ventas;
        $this->total_cantidad = $total_cantidad;
        $this->total_venta = $total_venta;
    }

    public function collection()
    {
        return $this->ventas;
    }

    public function headings(): array
    {
        return [
            '#',
            'Fecha',
            'Producto',
            'Lot Number',
            'Cliente',
            'Ubicación',
            'Cantidad',
            'Precio Venta',
            'Invoice'
        ];
    }

    public function map($venta): array
    {
        return [
            ++$this->fila,
            Carbon::parse($venta->transaction_date)->format('d/m/Y'),
            $venta->producto,
            $venta->lot_number,
            $venta->cliente ?? 'Cliente genérico',
            $venta->ubicacion ?? 'Sin ubicación',
            $venta->cantidad,
            $venta->unit_price_inc_tax,
            $venta->invoice_no,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => '343A40']
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $ultimaFila = $this->ventas->count() + 2;

                $event->sheet->setCellValue("F{$ultimaFila}", 'TOTAL');
                $event->sheet->setCellValue("G{$ultimaFila}", $this->total_cantidad);
                $event->sheet->setCellValue("H{$ultimaFila}", $this->total_venta);

                $event->sheet->getStyle("F{$ultimaFila}:H{$ultimaFila}")
                    ->getFont()->setBold(true);

                $event->sheet->getStyle("H2:H{$ultimaFila}")
                    ->getNumberFormat()
                    ->setFormatCode('"S/" #,##0.00');
            }
        ];
    }
}