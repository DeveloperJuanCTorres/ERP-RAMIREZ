<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistorialImportacionesExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $datos;

    public function __construct(Collection $datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $cabecera = $this->datos->first();

        $rows = [

            ['HISTORIAL DE IMPORTACIÓN'],

            [],

            ['Transaction ID', $cabecera->transaction_id],
            ['Empresa', $cabecera->business_name],
            ['Fecha', Carbon::parse($cabecera->transaction_date)->format('d/m/Y')],
            ['Tipo', $cabecera->type],
            ['Estado', strtoupper($cabecera->status)],

            [],

            [
                'Producto',
                'Motor',
                'Chasis',
                'Color',
                'Año',
                'Póliza',
                'Guía',
                'Contenedor'
            ]

        ];

        foreach ($this->datos as $item) {

            $rows[] = [

                $item->producto,
                $item->motor,
                $item->chasis,
                $item->color,
                $item->anio,
                $item->poliza,
                $item->guia,
                $item->contenedor

            ];

        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:H1');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'horizontal' => 'center'
            ]
        ]);

        $sheet->getStyle('A2:A6')->getFont()->setBold(true);

        $sheet->getStyle('A7:H7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => [
                    'rgb' => '1F4E78'
                ]
            ]
        ]);

        return [];
    }
}