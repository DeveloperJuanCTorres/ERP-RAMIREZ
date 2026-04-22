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
                'Producto' => $row['producto'],
                'SKU' => $row['sku'],
                'Variación' => $row['variacion'],
                'Categoría' => $row['categoria'],
                'Marca' => $row['marca'],
                'Ubicación' => $row['ubicacion'],
                'Stock' => $row['stock'],
                'Stock Min' => $row['stock_minimo'],
                'Valor' => $row['valor_stock'],
                'Estado' => $row['estado'],
            ];
        });
    }
}
