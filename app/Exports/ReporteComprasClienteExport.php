<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteComprasClienteExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $movimientos;

    public function __construct($movimientos)
    {
        $this->movimientos = $movimientos;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->movimientos as $movimiento) {

            $data->push([
                'Fecha'             => $movimiento['fecha'],
                'Factura'           => $movimiento['factura'],
                'Producto'          => $movimiento['producto'],
                'Motor'             => $movimiento['motor'],
                'Chasis'            => $movimiento['chasis'],
                'Póliza'            => $movimiento['poliza'],
                'Guía'              => $movimiento['guia'],
                'Contenedor'        => $movimiento['contenedor'],
                'Cantidad'          => $movimiento['cantidad'],
                'Precio Unitario'   => $movimiento['precio_unitario'],
                'Total Item'        => $movimiento['total_item'],
                'Subtotal Factura'  => $movimiento['es_ultimo']
                    ? $movimiento['subtotal']
                    : '',
                'Pagado'            => $movimiento['es_ultimo']
                    ? $movimiento['pagado']
                    : '',
                'Saldo'             => $movimiento['es_ultimo']
                    ? $movimiento['saldo']
                    : '',
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Factura',
            'Producto',
            'Motor',
            'Chasis',
            'Póliza',
            'Guía',
            'Contenedor',
            'Cantidad',
            'Precio Unitario',
            'Total Item',
            'Subtotal Factura',
            'Pagado',
            'Saldo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 13,
            'B' => 18,
            'C' => 35,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 18,
            'H' => 18,
            'I' => 12,
            'J' => 18,
            'K' => 18,
            'L' => 18,
            'M' => 18,
            'N' => 18,
        ];
    }
}