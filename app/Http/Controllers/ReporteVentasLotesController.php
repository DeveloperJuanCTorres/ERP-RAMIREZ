<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteVentasLotesController extends Controller
{
    /**
     * REPORTE DE VENTAS POR LOTE
     */
    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');


        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

        $fecha_desde = $request->input(
            'fecha_desde',
            now()->startOfMonth()->format('Y-m-d')
        );

        $fecha_hasta = $request->input(
            'fecha_hasta',
            now()->format('Y-m-d')
        );

        $location_id = $request->input('location_id');

        $contact_id = $request->input('contact_id');

        $product_id = $request->input('product_id');


        /*
        |--------------------------------------------------------------------------
        | CONSULTA PRINCIPAL
        |--------------------------------------------------------------------------
        |
        | transactions
        |      ↓
        | transaction_sell_lines
        |      ↓
        | transaction_sell_lines_purchase_lines
        |      ↓
        | purchase_lines
        |      ↓
        | lot_number
        |
        |--------------------------------------------------------------------------
        */

        $query = DB::table('transaction_sell_lines as tsl')

            /*
            |--------------------------------------------------------------------------
            | VENTA
            |--------------------------------------------------------------------------
            */

            ->join(
                'transactions as t',
                't.id',
                '=',
                'tsl.transaction_id'
            )

            /*
            |--------------------------------------------------------------------------
            | RELACIÓN ENTRE VENTA Y LOTE
            |--------------------------------------------------------------------------
            */

            ->join(
                'transaction_sell_lines_purchase_lines as tspl',
                'tspl.sell_line_id',
                '=',
                'tsl.id'
            )

            /*
            |--------------------------------------------------------------------------
            | LÍNEA DE COMPRA DONDE ESTÁ EL LOT NUMBER
            |--------------------------------------------------------------------------
            */

            ->join(
                'purchase_lines as pl',
                'pl.id',
                '=',
                'tspl.purchase_line_id'
            )

            /*
            |--------------------------------------------------------------------------
            | PRODUCTO
            |--------------------------------------------------------------------------
            */

            ->join(
                'products as p',
                'p.id',
                '=',
                'tsl.product_id'
            )

            /*
            |--------------------------------------------------------------------------
            | CLIENTE
            |--------------------------------------------------------------------------
            */

            ->leftJoin(
                'contacts as c',
                'c.id',
                '=',
                't.contact_id'
            )

            /*
            |--------------------------------------------------------------------------
            | UBICACIÓN
            |--------------------------------------------------------------------------
            */

            ->leftJoin(
                'business_locations as bl',
                'bl.id',
                '=',
                't.location_id'
            )


            /*
            |--------------------------------------------------------------------------
            | CONDICIONES
            |--------------------------------------------------------------------------
            */

            ->where(
                't.business_id',
                $business_id
            )

            ->where(
                't.type',
                'sell'
            )

            ->where(
                't.status',
                'final'
            )


            /*
            |--------------------------------------------------------------------------
            | SOLO LOTES
            |--------------------------------------------------------------------------
            */

            ->whereNotNull(
                'pl.lot_number'
            )

            ->where(
                'pl.lot_number',
                '<>',
                ''
            )


            /*
            |--------------------------------------------------------------------------
            | FECHAS
            |--------------------------------------------------------------------------
            */

            ->whereDate(
                't.transaction_date',
                '>=',
                $fecha_desde
            )

            ->whereDate(
                't.transaction_date',
                '<=',
                $fecha_hasta
            );


        /*
        |--------------------------------------------------------------------------
        | FILTRO UBICACIÓN
        |--------------------------------------------------------------------------
        */

        if (!empty($location_id)) {

            $query->where(
                't.location_id',
                $location_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO CLIENTE
        |--------------------------------------------------------------------------
        */

        if (!empty($contact_id)) {

            $query->where(
                't.contact_id',
                $contact_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO PRODUCTO
        |--------------------------------------------------------------------------
        */

        if (!empty($product_id)) {

            $query->where(
                'tsl.product_id',
                $product_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RESULTADO
        |--------------------------------------------------------------------------
        */

        $ventas = $query

            ->select(

                /*
                |--------------------------------------------------------------------------
                | IDENTIFICADORES
                |--------------------------------------------------------------------------
                */

                'tsl.id as sell_line_id',

                't.id as transaction_id',

                /*
                |--------------------------------------------------------------------------
                | VENTA
                |--------------------------------------------------------------------------
                */

                't.transaction_date',

                't.invoice_no',

                /*
                |--------------------------------------------------------------------------
                | PRODUCTO
                |--------------------------------------------------------------------------
                */

                'p.id as product_id',

                'p.name as producto',

                /*
                |--------------------------------------------------------------------------
                | LOTE
                |--------------------------------------------------------------------------
                */

                'pl.id as purchase_line_id',

                'pl.lot_number',

                /*
                |--------------------------------------------------------------------------
                | CANTIDAD
                |--------------------------------------------------------------------------
                */

                'tspl.quantity as cantidad',

                /*
                |--------------------------------------------------------------------------
                | PRECIO
                |--------------------------------------------------------------------------
                */

                'tsl.unit_price',

                'tsl.unit_price_inc_tax',

                /*
                |--------------------------------------------------------------------------
                | CLIENTE
                |--------------------------------------------------------------------------
                */

                'c.id as cliente_id',

                DB::raw("
                    CASE
                        WHEN c.supplier_business_name IS NOT NULL
                            AND c.supplier_business_name <> ''
                        THEN c.supplier_business_name
                        ELSE c.name
                    END AS cliente
                "),

                /*
                |--------------------------------------------------------------------------
                | UBICACIÓN
                |--------------------------------------------------------------------------
                */

                'bl.id as location_id',

                'bl.name as ubicacion'
            )

            ->orderBy(
                't.transaction_date',
                'desc'
            )

            ->orderBy(
                'tsl.id',
                'asc'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | UBICACIONES
        |--------------------------------------------------------------------------
        */

        $locations = DB::table('business_locations')

            ->where(
                'business_id',
                $business_id
            )

            ->where(
                'is_active',
                1
            )

            ->orderBy('name')

            ->pluck(
                'name',
                'id'
            );


        /*
        |--------------------------------------------------------------------------
        | CLIENTES
        |--------------------------------------------------------------------------
        */

        $clientes = DB::table('contacts')

            ->where(
                'business_id',
                $business_id
            )

            ->where(
                'type',
                'customer'
            )

            ->select(
                'id',

                DB::raw("
                    CASE
                        WHEN supplier_business_name IS NOT NULL
                            AND supplier_business_name <> ''
                        THEN supplier_business_name
                        ELSE name
                    END AS cliente
                ")
            )

            ->orderBy('cliente')

            ->pluck(
                'cliente',
                'id'
            );


        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS
        |--------------------------------------------------------------------------
        |
        | Solo productos que tengan lotes registrados.
        |
        */

        $products = DB::table('products as p')

            ->join(
                'purchase_lines as pl',
                'pl.product_id',
                '=',
                'p.id'
            )

            ->where(
                'p.business_id',
                $business_id
            )

            ->whereNotNull(
                'pl.lot_number'
            )

            ->where(
                'pl.lot_number',
                '<>',
                ''
            )

            ->select(
                'p.id',
                'p.name'
            )

            ->distinct()

            ->orderBy('p.name')

            ->pluck(
                'name',
                'id'
            );


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $total_items = $ventas->count();

        $total_cantidad = $ventas->sum('cantidad');

        $total_venta = $ventas->sum(function ($venta) {

            return ($venta->unit_price_inc_tax ?? 0)
                * ($venta->cantidad ?? 0);
        });


        return view(
            'report.ventas_lotes',
            compact(
                'ventas',
                'locations',
                'clientes',
                'products',

                'fecha_desde',
                'fecha_hasta',

                'location_id',
                'contact_id',
                'product_id',

                'total_items',
                'total_cantidad',
                'total_venta'
            )
        );
    }


    /**
     * EXPORTAR PDF
     */
    public function pdf(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');


        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

        $fecha_desde = $request->input(
            'fecha_desde',
            now()->startOfMonth()->format('Y-m-d')
        );

        $fecha_hasta = $request->input(
            'fecha_hasta',
            now()->format('Y-m-d')
        );

        $location_id = $request->input('location_id');

        $contact_id = $request->input('contact_id');

        $product_id = $request->input('product_id');


        /*
        |--------------------------------------------------------------------------
        | CONSULTA
        |--------------------------------------------------------------------------
        */

        $query = DB::table('transaction_sell_lines as tsl')

            ->join(
                'transactions as t',
                't.id',
                '=',
                'tsl.transaction_id'
            )

            ->join(
                'transaction_sell_lines_purchase_lines as tspl',
                'tspl.sell_line_id',
                '=',
                'tsl.id'
            )

            ->join(
                'purchase_lines as pl',
                'pl.id',
                '=',
                'tspl.purchase_line_id'
            )

            ->join(
                'products as p',
                'p.id',
                '=',
                'tsl.product_id'
            )

            ->leftJoin(
                'contacts as c',
                'c.id',
                '=',
                't.contact_id'
            )

            ->leftJoin(
                'business_locations as bl',
                'bl.id',
                '=',
                't.location_id'
            )

            ->where(
                't.business_id',
                $business_id
            )

            ->where(
                't.type',
                'sell'
            )

            ->where(
                't.status',
                'final'
            )

            ->whereNotNull(
                'pl.lot_number'
            )

            ->where(
                'pl.lot_number',
                '<>',
                ''
            )

            ->whereDate(
                't.transaction_date',
                '>=',
                $fecha_desde
            )

            ->whereDate(
                't.transaction_date',
                '<=',
                $fecha_hasta
            );


        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

        if (!empty($location_id)) {

            $query->where(
                't.location_id',
                $location_id
            );
        }

        if (!empty($contact_id)) {

            $query->where(
                't.contact_id',
                $contact_id
            );
        }

        if (!empty($product_id)) {

            $query->where(
                'tsl.product_id',
                $product_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RESULTADO
        |--------------------------------------------------------------------------
        */

        $ventas = $query

            ->select(

                't.transaction_date',

                't.invoice_no',

                'p.name as producto',

                'pl.lot_number',

                'tspl.quantity as cantidad',

                'tsl.unit_price_inc_tax',

                DB::raw("
                    CASE
                        WHEN c.supplier_business_name IS NOT NULL
                            AND c.supplier_business_name <> ''
                        THEN c.supplier_business_name
                        ELSE c.name
                    END AS cliente
                "),

                'bl.name as ubicacion'
            )

            ->orderBy(
                't.transaction_date',
                'desc'
            )

            ->orderBy(
                'tsl.id',
                'asc'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $total_items = $ventas->count();

        $total_cantidad = $ventas->sum('cantidad');

        $total_venta = $ventas->sum(function ($venta) {

            return ($venta->unit_price_inc_tax ?? 0)
                * ($venta->cantidad ?? 0);
        });


        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'report.partials.ventas_lote_pdf',
            compact(
                'ventas',
                'fecha_desde',
                'fecha_hasta',
                'total_items',
                'total_cantidad',
                'total_venta'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | ORIENTACIÓN
        |--------------------------------------------------------------------------
        */

        $pdf->setPaper(
            'a4',
            'portrait'
        );


        return $pdf->stream(
            'reporte-ventas-lotes.pdf'
        );
    }
}
