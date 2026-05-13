<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class StockExport implements FromCollection
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($row) {
            return [
                'producto' => $row['producto'],
                'categoria' => $row['categoria'] ?? '-',
                'marca' => $row['marca'] ?? '-',
                'ubicacion' => $row['ubicacion'] ?? '-',
                'stock' => $row['stock'],
                'serie' => $row['serie'],
                'color' => $row['color'] ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Categoría',
            'Marca',
            'Ubicación',
            'Stock',
            'Serie',
            'Color'
        ];
    }
}
