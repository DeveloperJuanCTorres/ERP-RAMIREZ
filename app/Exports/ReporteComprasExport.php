<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteComprasExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $datos;

    public function __construct(Collection $datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        return $this->datos->map(function ($item) {
            return [
                'Fecha'        => optional($item->created_at)->format('d/m/Y'),
                'Modelo'       => $item->producto,
                'Proveedor'    => $item->proveedor,
                'Motor'        => $item->motor,
                'Chasis'       => $item->chasis,
                'Color'        => $item->color,
                'Año'          => $item->anio,
                'Contenedor'   => $item->contenedor,
                'Guía'         => $item->guia,
                'Estado'       => $item->estado,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Modelo',
            'Proveedor',
            'Motor',
            'Chasis',
            'Color',
            'Año',
            'Contenedor',
            'Guía',
            'Estado',
        ];
    }
}