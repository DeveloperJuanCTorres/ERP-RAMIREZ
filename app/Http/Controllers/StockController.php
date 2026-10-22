<?php

namespace App\Http\Controllers;

use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\Product;
use Illuminate\Http\Request;
use App\Exports\StockExport;
use App\PurchaseLine;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $business_id = auth()->user()->business_id;

        // 🔥 reporte (aunque ahora usas AJAX, puedes dejarlo o quitarlo)
        $report = $this->getReport($request);

        // 🔽 filtros (multiempresa)
        $categories = Category::where('business_id', $business_id)
            ->pluck('name', 'id');

        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');

        $locations = BusinessLocation::where('business_id', $business_id)
            ->pluck('name', 'id');

        // 🔥 productos para el select
        $products = Product::where('business_id', $business_id)
            ->selectRaw("id, CONCAT(name, ' - ', sku) as name")
            ->pluck('name', 'id');

        return view('report.stock', compact(
            'report',
            'categories',
            'brands',
            'locations',
            'products' // 🔥 CLAVE
        ));
    }   

    private function getReport(Request $request)
    {
        $business_id = auth()->user()->business_id;

        /*
        |--------------------------------------------------------------------------
        | ORDEN DE FABRICACIÓN
        |--------------------------------------------------------------------------
        */

        $etapas = [
            'CARROCERIA' => 1,
            'PINTURA'    => 2,
            'ENSAMBLAJE' => 3,
            'TAPIZ'      => 4,
            'TRIMOTO'    => 5,
        ];

        /*
        |--------------------------------------------------------------------------
        | OBTENER REGISTROS
        |--------------------------------------------------------------------------
        */

        $registros = DB::table('purchase_lines as pl')
            ->join('transactions as t', 'pl.transaction_id', '=', 't.id')
            ->join('variations as v', 'pl.variation_id', '=', 'v.id')
            ->join('products as p', 'v.product_id', '=', 'p.id')

            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')

            ->where('t.business_id', $business_id)
            ->where('p.type', '!=', 'modifier')
            ->whereNotNull('pl.lot_number')
            ->where('pl.lot_number', '!=', '')
            ->whereIn('t.type', [
                'opening_stock',
                'production_purchase',
                'purchase_transfer',
                'purchase'
            ])
            ->where('t.status', 'received')

            ->select(
                'pl.id',
                'pl.lot_number',
                'pl.quantity',
                'pl.quantity_sold',
                'pl.quantity_returned',
                'pl.color',
                'pl.exp_date',

                'v.name as variacion',
                'v.default_purchase_price',

                'p.id as product_id',
                'p.name as producto',
                'p.sku',
                'p.category_id',
                'p.brand_id',

                'c.name as categoria',
                'b.name as marca',

                't.type as tipo_transaccion',
                't.transaction_date',

                't.location_id',
                'bl.name as ubicacion'
            )
            ->orderBy('t.transaction_date')
            ->orderBy('pl.id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | AGRUPAR POR SERIE
        |--------------------------------------------------------------------------
        */

        $reporte = collect();

        $series = $registros->groupBy('lot_number');

        foreach ($series as $serie => $movimientos) {

            /*
            |--------------------------------------------------------------------------
            | IDENTIFICAR ETAPA DE CADA PRODUCTO
            |--------------------------------------------------------------------------
            */

            $movimientos = $movimientos->map(function ($row) use ($etapas) {

                $nombre = mb_strtoupper($row->producto);

                $row->etapa = 0;

                foreach ($etapas as $palabra => $orden) {

                    if (strpos($nombre, $palabra) !== false) {
                        $row->etapa = $orden;
                        break;
                    }
                }

                return $row;
            });

            /*
            |--------------------------------------------------------------------------
            | DETERMINAR ÚLTIMA ETAPA
            |--------------------------------------------------------------------------
            */

            $ultimaEtapa = $movimientos->max('etapa');

            /*
            |--------------------------------------------------------------------------
            | CONSERVAR SOLO PRODUCTOS DE LA ÚLTIMA ETAPA
            |--------------------------------------------------------------------------
            */

            $movimientosActuales = $movimientos
                ->where('etapa', $ultimaEtapa);

            /*
            |--------------------------------------------------------------------------
            | AGRUPAR POR PRODUCTO
            |--------------------------------------------------------------------------
            */

            $productos = $movimientosActuales->groupBy('product_id');

            foreach ($productos as $product_id => $items) {

                /*
                |--------------------------------------------------------------------------
                | STOCK POR SERIE
                |--------------------------------------------------------------------------
                |
                | Una serie representa una unidad física.
                | Los ingresos duplicados no deben multiplicar el stock.
                |
                */

                $cantidadVendida = $items->sum(function ($row) {
                    return (float) $row->quantity_sold;
                });

                /*
                |--------------------------------------------------------------------------
                | SI YA FUE VENDIDO, NO MOSTRAR
                |--------------------------------------------------------------------------
                */

                if ($cantidadVendida >= 1) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | REGISTRO REPRESENTATIVO
                |--------------------------------------------------------------------------
                */

                $row = $items->sortByDesc(function ($item) {
                    return sprintf(
                        '%s-%010d',
                        $item->transaction_date,
                        $item->id
                    );
                })->first();

                /*
                |--------------------------------------------------------------------------
                | FILTROS
                |--------------------------------------------------------------------------
                */

                if ($request->filled('product_id')
                    && $row->product_id != $request->product_id) {
                    continue;
                }

                if ($request->filled('category_id')
                    && $row->category_id != $request->category_id) {
                    continue;
                }

                if ($request->filled('brand_id')
                    && $row->brand_id != $request->brand_id) {
                    continue;
                }

                if ($request->filled('location_id')
                    && $row->location_id != $request->location_id) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | AGREGAR AL REPORTE
                |--------------------------------------------------------------------------
                */

                $stock = 1;

                $reporte->push([
                    'nro' => 0,

                    'producto' => $row->producto,
                    'sku' => $row->sku,
                    'variacion' => $row->variacion,

                    'categoria' => $row->categoria,
                    'marca' => $row->marca,
                    'ubicacion' => $row->ubicacion,

                    'serie' => $serie,
                    'color' => $row->color ?: '-',
                    'modelo' => $row->variacion,

                    'stock' => $stock,

                    'valor_stock' => $stock *
                        ($row->default_purchase_price ?? 0),

                    'exp_date' => $row->exp_date
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ORDENAR Y ENUMERAR
        |--------------------------------------------------------------------------
        */

        return $reporte
            ->sortBy([
                ['ubicacion', 'asc'],
                ['producto', 'asc'],
                ['serie', 'asc']
            ])
            ->values()
            ->map(function ($row, $index) {
                $row['nro'] = $index + 1;
                return $row;
            });
    }
    

    // private function getReport(Request $request)
    // {
    //     $business_id = auth()->user()->business_id;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | ÚLTIMO MOVIMIENTO DE CADA SERIE
    //     |--------------------------------------------------------------------------
    //     */
    //     $ultimosMovimientos = DB::table('purchase_lines')
    //         ->join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
    //         ->join('variations as v', 'purchase_lines.variation_id', '=', 'v.id')
    //         ->join('products as p', 'v.product_id', '=', 'p.id')
    //         ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
    //         ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
    //         ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
    //         ->where('t.business_id', $business_id)
    //         ->where('p.type', '!=', 'modifier')
    //         ->whereNotNull('purchase_lines.lot_number')
    //         ->where('purchase_lines.lot_number', '!=', '')
    //         ->select(
    //             'purchase_lines.id',
    //             'purchase_lines.variation_id',
    //             'purchase_lines.lot_number',
    //             'purchase_lines.quantity',
    //             'purchase_lines.quantity_sold',
    //             'purchase_lines.quantity_returned',
    //             'purchase_lines.color',
    //             'purchase_lines.exp_date',
    //             'purchase_lines.created_at',

    //             'v.name as variacion',
    //             'v.default_purchase_price',

    //             'p.id as product_id',
    //             'p.name as producto',
    //             'p.sku',
    //             'p.category_id',
    //             'p.brand_id',

    //             'c.name as categoria',
    //             'b.name as marca',

    //             't.location_id',
    //             'bl.name as ubicacion'
    //         )
    //         ->orderByDesc('purchase_lines.created_at')
    //         ->get()
    //         ->groupBy('lot_number')
    //         ->map(function ($grupo) {
    //             return $grupo->first(); // último proceso de esa serie
    //         });

    //     /*
    //     |--------------------------------------------------------------------------
    //     | FILTRAR RESULTADOS
    //     |--------------------------------------------------------------------------
    //     */
    //     $reporte = collect($ultimosMovimientos)

    //         ->filter(function ($row) use ($request) {

    //             $stock = $row->quantity
    //                 - $row->quantity_sold
    //                 - ($row->quantity_returned ?? 0);

    //             if ($stock <= 0) {
    //                 return false;
    //             }

    //             if ($request->filled('product_id') && $row->product_id != $request->product_id) {
    //                 return false;
    //             }

    //             if ($request->filled('category_id') && $row->category_id != $request->category_id) {
    //                 return false;
    //             }

    //             if ($request->filled('brand_id') && $row->brand_id != $request->brand_id) {
    //                 return false;
    //             }

    //             if ($request->filled('location_id') && $row->location_id != $request->location_id) {
    //                 return false;
    //             }

    //             return true;
    //         })

    //         ->groupBy('ubicacion')

    //         ->flatMap(function ($itemsPorUbicacion) {

    //             return collect($itemsPorUbicacion)
    //                 ->values()
    //                 ->map(function ($row, $index) {

    //                     $stock = $row->quantity
    //                         - $row->quantity_sold
    //                         - ($row->quantity_returned ?? 0);

    //                     return [

    //                         'nro' => $index + 1,

    //                         'producto' => $row->producto,

    //                         'sku' => $row->sku,

    //                         'variacion' => $row->variacion,

    //                         'categoria' => $row->categoria,

    //                         'marca' => $row->marca,

    //                         'ubicacion' => $row->ubicacion,

    //                         'serie' => $row->lot_number,

    //                         'color' => $row->color ?: '-',

    //                         'modelo' => $row->variacion,

    //                         'stock' => $stock,

    //                         'valor_stock' => $stock * ($row->default_purchase_price ?? 0),

    //                         'exp_date' => $row->exp_date
    //                     ];
    //                 });
    //         })

    //         ->values();

    //     return $reporte;
    // }

    public function stockData(Request $request)
    {
        return response()->json($this->getReport($request));
    }

    public function exportExcel(Request $request)
    {
        $report = $this->getReport($request);

        return Excel::download(new StockExport($report), 'reporte_stock.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $report = $this->getReport($request);

        $pdf = Pdf::loadView('report.partials.stock_pdf', compact('report'));

        return $pdf->download('reporte_stock.pdf');
    }
}
