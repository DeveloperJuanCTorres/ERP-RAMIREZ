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

    // private function getReport(Request $request)
    // {
    //     $business_id = auth()->user()->business_id;

      
    //     $products = Product::with([
    //             'variations.variation_location_details.location',
    //             'category',
    //             'sub_category',
    //             'brand'
    //         ])
    //         ->where('business_id', $business_id)
    //         ->where('type', '!=', 'modifier')

    //         // 🔥 FILTROS
    //         ->when($request->filled('product_id'), fn($q) => $q->where('id', $request->product_id))
    //         ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
    //         ->when($request->filled('sub_category_id'), fn($q) => $q->where('sub_category_id', $request->sub_category_id))
    //         ->when($request->filled('brand_id'), fn($q) => $q->where('brand_id', $request->brand_id))

    //         ->when($request->filled('location_id'), function ($q) use ($request) {
    //             $q->whereHas('variations.variation_location_details', function ($q2) use ($request) {
    //                 $q2->where('location_id', $request->location_id);
    //             });
    //         })

    //         ->get();

    //     $lotesQuery = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
    //         ->where('t.business_id', $business_id)
    //         ->whereNotNull('purchase_lines.lot_number');

    //     if ($request->filled('location_id')) {
    //         $lotesQuery->where('t.location_id', $request->location_id);
    //     }

    //     $lotesData = $lotesQuery
    //         ->select(
    //             'purchase_lines.variation_id',
    //             't.location_id',
    //             'purchase_lines.lot_number',
    //             'purchase_lines.quantity',
    //             'purchase_lines.quantity_sold',
    //             'purchase_lines.quantity_returned',
    //             'purchase_lines.exp_date',
    //             'purchase_lines.created_at',
    //             'purchase_lines.color'
    //         )
    //         ->orderBy('purchase_lines.created_at', 'asc')
    //         ->get()
    //         ->groupBy(fn($item) => $item->variation_id . '-' . $item->location_id);

       
    //     return $products->flatMap(function ($product) use ($request, $lotesData) {

    //         return $product->variations->flatMap(function ($variation) use ($product, $request, $lotesData) {

    //             $details = $variation->variation_location_details
    //                 ->when($request->filled('location_id'), fn($c) =>
    //                     $c->where('location_id', $request->location_id)
    //                 );

    //             return $details->flatMap(function ($detail) use ($product, $variation, $lotesData) {

    //                 $key = $variation->id . '-' . $detail->location_id;

    //                 if (!isset($lotesData[$key])) {
    //                     return [];
    //                 }

    //                 return $lotesData[$key]
    //                     ->groupBy('lot_number')
    //                     ->map(function ($grupo) use ($product, $variation, $detail) {

    //                         $first = $grupo->first();

    //                         $qty = $grupo->sum(function ($l) {
    //                             return $l->quantity
    //                                 - $l->quantity_sold
    //                                 - ($l->quantity_returned ?? 0);
    //                         });

    //                         if ($qty <= 0) {
    //                             return null;
    //                         }

    //                         return [
    //                             'producto' => $product->name,
    //                             'sku' => $product->sku,
    //                             'variacion' => $variation->name,
    //                             'categoria' => optional($product->category)->name,
    //                             'marca' => optional($product->brand)->name,
    //                             'ubicacion' => optional($detail->location)->name,

    //                             // 🔥 NUEVO
    //                             'serie' => $first->lot_number,
    //                             'color' => $first->color ?? '-',
    //                             'modelo' => $variation->name,

    //                             'stock' => $qty,
    //                             'valor_stock' => $qty * ($variation->default_purchase_price ?? 0),
    //                             'exp_date' => $first->exp_date
    //                         ];
    //                     })
    //                     ->filter()
    //                     ->values();
    //             });

    //         });

    //     })->values();
    // }

    private function getReport(Request $request)
    {
        $business_id = auth()->user()->business_id;

        /*
        |--------------------------------------------------------------------------
        | 1. PRODUCTOS BASE
        |--------------------------------------------------------------------------
        */
        $products = Product::with([
                'variations.variation_location_details.location',
                'category',
                'sub_category',
                'brand'
            ])
            ->where('business_id', $business_id)
            ->where('type', '!=', 'modifier')

            // 🔥 FILTROS
            ->when($request->filled('product_id'), fn($q) =>
                $q->where('id', $request->product_id)
            )

            ->when($request->filled('category_id'), fn($q) =>
                $q->where('category_id', $request->category_id)
            )

            ->when($request->filled('sub_category_id'), fn($q) =>
                $q->where('sub_category_id', $request->sub_category_id)
            )

            ->when($request->filled('brand_id'), fn($q) =>
                $q->where('brand_id', $request->brand_id)
            )

            ->when($request->filled('location_id'), function ($q) use ($request) {

                $q->whereHas('variations.variation_location_details', function ($q2) use ($request) {
                    $q2->where('location_id', $request->location_id);
                });

            })

            ->get();

        /*
        |--------------------------------------------------------------------------
        | 2. LOTES
        |--------------------------------------------------------------------------
        */
        $lotesQuery = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
            ->where('t.business_id', $business_id)
            ->whereNotNull('purchase_lines.lot_number');

        if ($request->filled('location_id')) {

            $lotesQuery->where(
                't.location_id',
                $request->location_id
            );
        }

        $lotesData = $lotesQuery
            ->select(
                'purchase_lines.variation_id',
                't.location_id',
                'purchase_lines.lot_number',
                'purchase_lines.quantity',
                'purchase_lines.quantity_sold',
                'purchase_lines.quantity_returned',
                'purchase_lines.exp_date',
                'purchase_lines.created_at',
                'purchase_lines.color'
            )
            ->orderBy('purchase_lines.created_at', 'asc')
            ->get()
            ->groupBy(fn($item) =>
                $item->variation_id . '-' . $item->location_id
            );

        /*
        |--------------------------------------------------------------------------
        | 3. MAPA GLOBAL DE COLORES
        |--------------------------------------------------------------------------
        |
        | Obtiene el primer color válido por:
        | variation_id + lot_number
        |
        */
        $colorMap = PurchaseLine::whereNotNull('lot_number')
            ->whereNotNull('color')
            ->where('color', '!=', '')
            ->select(
                'variation_id',
                'lot_number',
                'color'
            )
            ->get()
            ->groupBy(fn($item) =>
                $item->variation_id . '-' . $item->lot_number
            )
            ->map(fn($group) =>
                optional($group->first())->color
            );

        /*
        |--------------------------------------------------------------------------
        | 4. RESPUESTA
        |--------------------------------------------------------------------------
        */
        return $products->flatMap(function ($product) use (
            $request,
            $lotesData,
            $colorMap
        ) {

            return $product->variations->flatMap(function (
                $variation
            ) use (
                $product,
                $request,
                $lotesData,
                $colorMap
            ) {

                $details = $variation->variation_location_details
                    ->when(
                        $request->filled('location_id'),
                        fn($c) =>
                            $c->where(
                                'location_id',
                                $request->location_id
                            )
                    );

                return $details->flatMap(function (
                    $detail
                ) use (
                    $product,
                    $variation,
                    $lotesData,
                    $colorMap
                ) {

                    $key = $variation->id . '-' . $detail->location_id;

                    if (!isset($lotesData[$key])) {
                        return [];
                    }

                    return $lotesData[$key]
                        ->groupBy('lot_number')
                        ->map(function ($grupo) use (
                            $product,
                            $variation,
                            $detail,
                            $colorMap
                        ) {

                            $first = $grupo->first();

                            $qty = $grupo->sum(function ($l) {

                                return $l->quantity
                                    - $l->quantity_sold
                                    - ($l->quantity_returned ?? 0);
                            });

                            if ($qty <= 0) {
                                return null;
                            }

                            return [

                                'producto' => $product->name,

                                'sku' => $product->sku,

                                'variacion' => $variation->name,

                                'categoria' => optional(
                                    $product->category
                                )->name,

                                'marca' => optional(
                                    $product->brand
                                )->name,

                                'ubicacion' => optional(
                                    $detail->location
                                )->name,

                                // 🔥 SERIE
                                'serie' => $first->lot_number,

                                // 🔥 COLOR GLOBAL
                                'color' => $colorMap[
                                    $variation->id . '-' . $first->lot_number
                                ] ?? '-',

                                'modelo' => $variation->name,

                                'stock' => $qty,

                                'valor_stock' => $qty * (
                                    $variation->default_purchase_price ?? 0
                                ),

                                'exp_date' => $first->exp_date
                            ];
                        })
                        ->filter()
                        ->values();
                });

            });

        })->values();
    }

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
