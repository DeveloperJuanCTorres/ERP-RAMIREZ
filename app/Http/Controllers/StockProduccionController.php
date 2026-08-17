<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StockProduccionController extends Controller
{
    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        /*
        |--------------------------------------------------------------------------
        | ÚLTIMO PROCESO DE CADA MOTOR
        |--------------------------------------------------------------------------
        |
        | Un mismo lot_number puede aparecer varias veces:
        |
        | M001 -> Carrocería
        | M001 -> Ensamblaje
        | M001 -> Tapiz
        | M001 -> Trimoto
        |
        | Solamente tomamos el último registro.
        |
        */

        $ultimosProcesos = DB::table('purchase_lines as pl')
            ->join('transactions as t', 't.id', '=', 'pl.transaction_id')

            ->select(
                'pl.id as purchase_line_id',
                'pl.lot_number',
                'pl.product_id',
                'pl.variation_id',

                'pl.quantity',
                'pl.quantity_sold',
                'pl.quantity_adjusted',
                'pl.quantity_returned',
                'pl.mfg_quantity_used',

                'pl.motor',
                'pl.chasis',
                'pl.fecha',

                't.id as transaction_id',
                't.location_id',
                't.transaction_date',

                DB::raw("
                    ROW_NUMBER() OVER (
                        PARTITION BY pl.lot_number
                        ORDER BY t.transaction_date DESC, pl.id DESC
                    ) AS rn
                ")
            )

            ->where('t.business_id', $business_id)

            ->where('t.type', 'production_purchase')

            ->whereNotNull('pl.lot_number')
            ->where('pl.lot_number', '<>', '');


        /*
        |--------------------------------------------------------------------------
        | STOCK DISPONIBLE
        |--------------------------------------------------------------------------
        */

        $stock = DB::query()

            ->fromSub($ultimosProcesos, 'ult')

            ->join(
                'products as p',
                'p.id',
                '=',
                'ult.product_id'
            )

            ->leftJoin(
                'business_locations as bl',
                'bl.id',
                '=',
                'ult.location_id'
            )

            ->select(

                'ult.purchase_line_id',
                'ult.transaction_id',

                'ult.lot_number',

                'ult.motor',
                'ult.chasis',

                'ult.product_id',

                'p.name as producto',

                'ult.location_id',

                'bl.name as ubicacion',

                'ult.transaction_date',

                'ult.fecha',

                'ult.quantity',
                'ult.quantity_sold',
                'ult.quantity_adjusted',
                'ult.quantity_returned',
                'ult.mfg_quantity_used'

            )

            ->where('ult.rn', 1)

            /*
            |--------------------------------------------------------------------------
            | STOCK DISPONIBLE
            |--------------------------------------------------------------------------
            |
            | Usamos COALESCE para evitar problemas cuando
            | alguno de estos campos sea NULL.
            |
            */

            ->whereRaw("
                (
                    COALESCE(ult.quantity, 0)
                    - COALESCE(ult.quantity_sold, 0)
                    - COALESCE(ult.quantity_adjusted, 0)
                    - COALESCE(ult.quantity_returned, 0)
                    - COALESCE(ult.mfg_quantity_used, 0)
                ) > 0
            ");


        /*
        |--------------------------------------------------------------------------
        | FILTRO NÚMERO DE MOTOR / LOTE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('lot_number')) {

            $stock->where(
                'ult.lot_number',
                'like',
                '%' . trim($request->lot_number) . '%'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO UBICACIÓN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('location_id')) {

            $stock->where(
                'ult.location_id',
                $request->location_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO PRODUCTO / ETAPA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('product_id')) {

            $stock->where(
                'ult.product_id',
                $request->product_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO FECHA DESDE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fecha_desde')) {

            $stock->whereDate(
                'ult.transaction_date',
                '>=',
                $request->fecha_desde
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO FECHA HASTA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fecha_hasta')) {

            $stock->whereDate(
                'ult.transaction_date',
                '<=',
                $request->fecha_hasta
            );

        }


        /*
        |--------------------------------------------------------------------------
        | RESULTADOS
        |--------------------------------------------------------------------------
        */

        $stocks = $stock
            ->orderByDesc('ult.transaction_date')
            ->paginate(25)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | UBICACIONES
        |--------------------------------------------------------------------------
        */

        $locations = DB::table('business_locations')

            ->where('business_id', $business_id)

            ->where('is_active', 1)

            ->orderBy('name')

            ->pluck('name', 'id');


        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS
        |--------------------------------------------------------------------------
        */

        $products = DB::table('products')

            ->where('business_id', $business_id)

            ->where('enable_stock', 1)

            ->orderBy('name')

            ->pluck('name', 'id');


        /*
        |--------------------------------------------------------------------------
        | RESUMEN
        |--------------------------------------------------------------------------
        |
        | Utilizamos la misma consulta base para que los totales
        | correspondan exactamente al stock mostrado.
        |
        */

        $baseResumen = DB::query()

            ->fromSub($ultimosProcesos, 'ult')

            ->where('ult.rn', 1)

            ->whereRaw("
                (
                    COALESCE(ult.quantity, 0)
                    - COALESCE(ult.quantity_sold, 0)
                    - COALESCE(ult.quantity_adjusted, 0)
                    - COALESCE(ult.quantity_returned, 0)
                    - COALESCE(ult.mfg_quantity_used, 0)
                ) > 0
            ");


        /*
        |--------------------------------------------------------------------------
        | APLICAR LOS MISMOS FILTROS AL RESUMEN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('lot_number')) {

            $baseResumen->where(
                'ult.lot_number',
                'like',
                '%' . trim($request->lot_number) . '%'
            );

        }

        if ($request->filled('location_id')) {

            $baseResumen->where(
                'ult.location_id',
                $request->location_id
            );

        }

        if ($request->filled('product_id')) {

            $baseResumen->where(
                'ult.product_id',
                $request->product_id
            );

        }

        if ($request->filled('fecha_desde')) {

            $baseResumen->whereDate(
                'ult.transaction_date',
                '>=',
                $request->fecha_desde
            );

        }

        if ($request->filled('fecha_hasta')) {

            $baseResumen->whereDate(
                'ult.transaction_date',
                '<=',
                $request->fecha_hasta
            );

        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL GENERAL
        |--------------------------------------------------------------------------
        */

        $totalStock = (clone $baseResumen)->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL CARROCERÍA
        |--------------------------------------------------------------------------
        */

        $totalCarroceria = (clone $baseResumen)

            ->join(
                'products as p',
                'p.id',
                '=',
                'ult.product_id'
            )

            ->whereRaw(
                'LOWER(p.name) LIKE ?',
                ['%carroceria%']
            )

            ->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL ENSAMBLAJE
        |--------------------------------------------------------------------------
        */

        $totalEnsamblaje = (clone $baseResumen)

            ->join(
                'products as p',
                'p.id',
                '=',
                'ult.product_id'
            )

            ->whereRaw(
                'LOWER(p.name) LIKE ?',
                ['%ensamblaje%']
            )

            ->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL TAPIZ
        |--------------------------------------------------------------------------
        */

        $totalTapiz = (clone $baseResumen)

            ->join(
                'products as p',
                'p.id',
                '=',
                'ult.product_id'
            )

            ->whereRaw(
                'LOWER(p.name) LIKE ?',
                ['%tapiz%']
            )

            ->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL TRIMOTO
        |--------------------------------------------------------------------------
        */

        $totalTrimoto = (clone $baseResumen)

            ->join(
                'products as p',
                'p.id',
                '=',
                'ult.product_id'
            )

            ->whereRaw(
                'LOWER(p.name) LIKE ?',
                ['%trimoto%']
            )

            ->count();


        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */

        return view(
            'report.produccion',
            compact(
                'stocks',
                'locations',
                'products',
                'totalStock',
                'totalCarroceria',
                'totalEnsamblaje',
                'totalTapiz',
                'totalTrimoto'
            )
        );
    }

    public function exportPdf(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        /*
        |--------------------------------------------------------------------------
        | ÚLTIMO PROCESO DE CADA MOTOR
        |--------------------------------------------------------------------------
        */

        $ultimosProcesos = DB::table('purchase_lines as pl')
            ->join('transactions as t', 't.id', '=', 'pl.transaction_id')

            ->select(
                'pl.id as purchase_line_id',
                'pl.lot_number',
                'pl.product_id',
                'pl.variation_id',

                'pl.quantity',
                'pl.quantity_sold',
                'pl.quantity_adjusted',
                'pl.quantity_returned',
                'pl.mfg_quantity_used',

                'pl.motor',
                'pl.chasis',
                'pl.fecha',

                't.id as transaction_id',
                't.location_id',
                't.transaction_date',

                DB::raw("
                    ROW_NUMBER() OVER (
                        PARTITION BY pl.lot_number
                        ORDER BY t.transaction_date DESC, pl.id DESC
                    ) AS rn
                ")
            )

            ->where('t.business_id', $business_id)

            ->where('t.type', 'production_purchase')

            ->whereNotNull('pl.lot_number')
            ->where('pl.lot_number', '<>', '');


        /*
        |--------------------------------------------------------------------------
        | STOCK
        |--------------------------------------------------------------------------
        */

        $stock = DB::query()

            ->fromSub($ultimosProcesos, 'ult')

            ->join(
                'products as p',
                'p.id',
                '=',
                'ult.product_id'
            )

            ->leftJoin(
                'business_locations as bl',
                'bl.id',
                '=',
                'ult.location_id'
            )

            ->select(

                'ult.purchase_line_id',
                'ult.transaction_id',

                'ult.lot_number',

                'ult.motor',
                'ult.chasis',

                'ult.product_id',

                'p.name as producto',

                'ult.location_id',

                'bl.name as ubicacion',

                'ult.transaction_date',

                'ult.fecha',

                'ult.quantity',
                'ult.quantity_sold',
                'ult.quantity_adjusted',
                'ult.quantity_returned',
                'ult.mfg_quantity_used'

            )

            ->where('ult.rn', 1)

            ->whereRaw("
                (
                    COALESCE(ult.quantity, 0)
                    - COALESCE(ult.quantity_sold, 0)
                    - COALESCE(ult.quantity_adjusted, 0)
                    - COALESCE(ult.quantity_returned, 0)
                    - COALESCE(ult.mfg_quantity_used, 0)
                ) > 0
            ");


        /*
        |--------------------------------------------------------------------------
        | FILTRO NÚMERO DE MOTOR
        |--------------------------------------------------------------------------
        */

        if ($request->filled('lot_number')) {

            $stock->where(
                'ult.lot_number',
                'like',
                '%' . trim($request->lot_number) . '%'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO UBICACIÓN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('location_id')) {

            $stock->where(
                'ult.location_id',
                $request->location_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO PRODUCTO / ETAPA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('product_id')) {

            $stock->where(
                'ult.product_id',
                $request->product_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FECHA DESDE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fecha_desde')) {

            $stock->whereDate(
                'ult.transaction_date',
                '>=',
                $request->fecha_desde
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FECHA HASTA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fecha_hasta')) {

            $stock->whereDate(
                'ult.transaction_date',
                '<=',
                $request->fecha_hasta
            );

        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER TODOS LOS REGISTROS
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        | No usamos paginate() porque el PDF debe contener todos
        | los resultados del filtro.
        |
        */

        $stocks = $stock
            ->orderByDesc('ult.transaction_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | GENERAR PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'report.produccion_pdf',
            [
                'stocks' => $stocks,
                'fecha_desde' => $request->fecha_desde,
                'fecha_hasta' => $request->fecha_hasta,
                'lot_number' => $request->lot_number,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | CONFIGURACIÓN
        |--------------------------------------------------------------------------
        */

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream(
            'stock-produccion-' . date('Y-m-d-His') . '.pdf'
        );
    }
}