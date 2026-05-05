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
        $report = $this->getReport($request);

        // 🔽 para los filtros
        $categories = Category::pluck('name', 'id');
        $brands = Brands::pluck('name', 'id');
        $locations = BusinessLocation::pluck('name', 'id');

        return view('report.stock', compact('report', 'categories', 'brands', 'locations'));
    }

    private function getReport(Request $request)
    {
        // 🔥 1. QUERY BASE PRODUCTOS
        $query = Product::with([
            'variations.variation_location_details.location',
            'category',
            'sub_category',
            'brand'
        ])
        ->where('type', '!=', 'modifier');

        // 🔍 FILTROS
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('product')) {
            $query->where('name', 'like', "%{$request->product}%");
        }

        if ($request->filled('location_id')) {
            $query->whereHas('variations.variation_location_details', function ($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        $products = $query->get();

        // 🔥 2. TRAER TODOS LOS LOTES EN UNA SOLA QUERY (OPTIMIZADO)
        $lotesData = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
            ->whereNotNull('purchase_lines.lot_number')
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
            ->orderBy('purchase_lines.created_at', 'asc') // FIFO
            ->get()
            ->groupBy(function ($item) {
                return $item->variation_id . '-' . $item->location_id;
            });

        // 🔥 3. ARMAR RESPUESTA
        return $products->flatMap(function ($product) use ($request, $lotesData) {

            return $product->variations->flatMap(function ($variation) use ($product, $request, $lotesData) {

                $details = $variation->variation_location_details
                    ->when(!empty($request->location_id), function ($collection) use ($request) {
                        return $collection->where('location_id', $request->location_id);
                    });

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

                    // 🔥 LOTES DESDE CACHE (SIN QUERY EXTRA)
                    $key = $variation->id . '-' . $detail->location_id;

                    $lotes = [];

                    if (isset($lotesData[$key])) {
                        $lotes = $lotesData[$key]->map(function ($l) {

                            $qty = $l->quantity
                                - $l->quantity_sold
                                - ($l->quantity_returned ?? 0);

                            return [
                                'lot_number' => $l->lot_number,
                                'qty' => $qty,
                                'exp_date' => $l->exp_date
                            ];
                        })->values()->toArray();
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
                        'lotes' => $lotes // 🔥 TODOS LOS LOTES
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
