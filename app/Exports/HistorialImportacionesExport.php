<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HistorialImportacionesExport implements FromCollection, WithHeadings
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        return collect($this->datos);
    }

    public function headings(): array
    {
        return [

            'Fecha',

            'Producto',

            'Motor',

            'Chasis',

            'Color',

            'Año',

            'Póliza',

            'Guía',

            'Contenedor'

        ];
    }
}