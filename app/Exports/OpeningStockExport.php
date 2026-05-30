<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class OpeningStockExport implements FromCollection
{
    public function collection()
    {
        $business_id = request()->session()->get('user.business_id');

        return DB::table('purchase_lines as pl')
            ->join('transactions as t', 'pl.transaction_id', '=', 't.id')
            ->join('products as p', 'pl.product_id', '=', 'p.id')
            ->where('t.type', 'opening_stock')
            ->where('t.status', 'received')
            ->where('t.business_id', $business_id)
            ->select(
                't.id as transaction_id',
                'p.name as producto',
                'pl.purchase_price_inc_tax as precio_compra',
                'pl.lot_number as lote',
                'pl.color as color'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Transacción',
            'Producto',
            'Precio compra inc. IGV',
            'Lote',
            'Color'
        ];
    }
}