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
            ->when($request->filled('product_id'), fn($q) => $q->where('id', $request->product_id))
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('sub_category_id'), fn($q) => $q->where('sub_category_id', $request->sub_category_id))
            ->when($request->filled('brand_id'), fn($q) => $q->where('brand_id', $request->brand_id))

            ->when($request->filled('location_id'), function ($q) use ($request) {
                $q->whereHas('variations.variation_location_details', function ($q2) use ($request) {
                    $q2->where('location_id', $request->location_id);
                });
            })

            ->get();

        /*
        |--------------------------------------------------------------------------
        | 2. LOTES (OPTIMIZADO)
        |--------------------------------------------------------------------------
        */
        $lotesQuery = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->whereNotNull('purchase_lines.lot_number');

        if ($request->filled('location_id')) {
            $lotesQuery->where('t.location_id', $request->location_id);
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
                'purchase_lines.created_at'
            )
            ->orderBy('purchase_lines.created_at', 'asc')
            ->get()
            ->groupBy(fn($item) => $item->variation_id . '-' . $item->location_id);

        /*
        |--------------------------------------------------------------------------
        | 3. RESPUESTA
        |--------------------------------------------------------------------------
        */
        return $products->flatMap(function ($product) use ($request, $lotesData) {

            return $product->variations->flatMap(function ($variation) use ($product, $request, $lotesData) {

                $details = $variation->variation_location_details
                    ->when($request->filled('location_id'), fn($c) => $c->where('location_id', $request->location_id));

                return $details->map(function ($detail) use ($product, $variation, $lotesData) {

                    $stock = $detail->qty_available ?? 0;
                    $min = $product->alert_quantity ?? 0;

                    // 🔥 ESTADO
                    if ($stock <= 0) {
                        $estado = 'SIN STOCK';
                    } elseif ($stock <= $min) {
                        $estado = 'CRITICO';
                    } elseif ($stock <= ($min * 1.5)) {
                        $estado = 'BAJO';
                    } else {
                        $estado = 'NORMAL';
                    }

                    // 🔥 LOTES
                    $key = $variation->id . '-' . $detail->location_id;

                    $lotes = [];

                    if (isset($lotesData[$key])) {
                        $lotes = $lotesData[$key]
                            ->map(function ($l) {

                                $qty = $l->quantity
                                    - $l->quantity_sold
                                    - ($l->quantity_returned ?? 0);

                                if ($qty <= 0) return null;

                                return [
                                    'lot_number' => $l->lot_number,
                                    'qty' => $qty,
                                    'exp_date' => $l->exp_date
                                ];
                            })
                            ->filter()
                            ->values()
                            ->toArray();
                    }

                    return [
                        'producto' => $product->name,
                        'sku' => $product->sku,
                        'variacion' => $variation->name,
                        'categoria' => optional($product->category)->name,
                        'marca' => optional($product->brand)->name,
                        'ubicacion' => optional($detail->location)->name,
                        'stock' => $stock,
                        'stock_minimo' => $min,
                        'valor_stock' => $stock * ($variation->default_purchase_price ?? 0),
                        'estado' => $estado,
                        'lotes' => $lotes
                    ];
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
