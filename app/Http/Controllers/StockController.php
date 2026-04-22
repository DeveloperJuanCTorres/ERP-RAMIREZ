<?php

namespace App\Http\Controllers;

use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\Product;
use Illuminate\Http\Request;
use App\Exports\StockExport;
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

    private function getReport($request)
    {
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

        return $products->flatMap(function ($product) use ($request) {

            return $product->variations->flatMap(function ($variation) use ($product, $request) {

                $details = $variation->variation_location_details()
                    ->when(!empty($request->location_id), function ($q) use ($request) {
                        $q->where('location_id', $request->location_id);
                    })
                    ->get();

                return $details->map(function ($detail) use ($product, $variation) {

                    $stock = $detail->qty_available;
                    $min = $product->alert_quantity ?? 0;

                    if ($stock <= 0) {
                        $estado = 'SIN STOCK';
                    } elseif ($stock <= $min) {
                        $estado = 'CRITICO';
                    } elseif ($stock <= ($min * 1.5)) {
                        $estado = 'BAJO';
                    } else {
                        $estado = 'NORMAL';
                    }

                    return [
                        'producto' => $product->name,
                        'sku' => $product->sku,
                        'variacion' => $variation->name,
                        'categoria' => optional($product->category)->name,
                        'subcategoria' => optional($product->sub_category)->name,
                        'marca' => optional($product->brand)->name,
                        'ubicacion' => optional($detail->location)->name,
                        'stock' => $stock,
                        'stock_minimo' => $min,
                        'valor_stock' => $stock * ($variation->default_purchase_price ?? 0),
                        'estado' => $estado,
                    ];
                });

            });

        });
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
