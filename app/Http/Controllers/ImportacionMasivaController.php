<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HistorialImportacionesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ImportacionMasivaController extends Controller
{
    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        $query = $this->obtenerConsulta($request);

        $registros = $query
            ->orderByDesc('t.transaction_date')
            ->get()
            ->groupBy('transaction_id');

        $productos = DB::table('products')
            ->where('business_id', $business_id)
            ->orderBy('name')
            ->pluck('name', 'id');

        // dd($registros);

        return view(
            'purchase.importaciones.index',
            [
                'registros' => $registros,
                'productos' => $productos,
                'desde' => $request->desde,
                'hasta' => $request->hasta,
                'producto' => $request->producto,
                'motor' => $request->motor,
                'guia' => $request->guia,
                'contenedor' => $request->contenedor,
            ]
        );
    }

    private function obtenerConsulta(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        $desde = $request->desde;
        $hasta = $request->hasta;

        $motor = $request->motor;
        $guia = $request->guia;
        $contenedor = $request->contenedor;
        $producto = $request->producto;

        $query = DB::table('purchase_lines as pl')

            ->join('transactions as t', 'pl.transaction_id', '=', 't.id')

            ->join('products as p', 'pl.product_id', '=', 'p.id')

            ->join('business as b', 't.business_id', '=', 'b.id')

            ->select(

                't.id as transaction_id',
                't.business_id',
                'b.name as business_name',
                't.transaction_date',
                't.type',
                't.status',

                'pl.id',
                'p.name as producto',
                'pl.motor',
                'pl.chasis',
                'pl.color',
                'pl.anio',
                'pl.poliza',
                'pl.guia',
                'pl.contenedor',
            )

            ->where('t.type', 'opening_stock')
            ->where('t.status', 'received')
            ->where('t.business_id', $business_id)
            ->whereNotNull('pl.motor')
            ->where('pl.motor', '<>', '')

            ->whereNotNull('pl.chasis')
            ->where('pl.chasis', '<>', '')

            ->whereNotNull('pl.color')
            ->where('pl.color', '<>', '')

            ->whereNotNull('pl.poliza')
            ->where('pl.poliza', '<>', '');

        if ($desde) {
            $query->whereDate('t.transaction_date', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('t.transaction_date', '<=', $hasta);
        }

        if (!empty($producto)) {
            $query->where('pl.product_id', $producto);
        }

        if (!empty($motor)) {
            $query->where('pl.motor', 'like', "%{$motor}%");
        }

        if (!empty($guia)) {
            $query->where('pl.guia', 'like', "%{$guia}%");
        }

        if (!empty($contenedor)) {
            $query->where('pl.contenedor', 'like', "%{$contenedor}%");
        }

        return $query;
    }

    public function excel($transactionId)
    {
        $datos = DB::table('purchase_lines as pl')
            ->join('transactions as t', 'pl.transaction_id', '=', 't.id')
            ->join('products as p', 'pl.product_id', '=', 'p.id')
            ->join('business as b', 't.business_id', '=', 'b.id')
            ->where('pl.transaction_id', $transactionId)
            ->select(
                't.id as transaction_id',
                't.transaction_date',
                't.type',
                't.status',
                'b.name as business_name',

                'p.name as producto',
                'pl.motor',
                'pl.chasis',
                'pl.color',
                'pl.anio',
                'pl.poliza',
                'pl.guia',
                'pl.contenedor'
            )
            ->orderBy('pl.id')
            ->get();

        if ($datos->isEmpty()) {
            abort(404);
        }

        return Excel::download(
            new HistorialImportacionesExport($datos),
            'Importacion_'.$transactionId.'.xlsx'
        );
    }
  
    public function pdf($transactionId)
    {
        $datos = DB::table('purchase_lines as pl')

            ->join('transactions as t','pl.transaction_id','=','t.id')

            ->join('products as p','pl.product_id','=','p.id')

            ->join('business as b','t.business_id','=','b.id')

            ->select(
                't.id as transaction_id',
                't.transaction_date',
                't.type',
                't.status',
                'b.name as business_name',

                'p.name as producto',
                'pl.motor',
                'pl.chasis',
                'pl.color',
                'pl.anio',
                'pl.poliza',
                'pl.guia',
                'pl.contenedor'
            )

            ->where('pl.transaction_id',$transactionId)

            ->get();

        $cabecera = $datos->first();

        $pdf = Pdf::loadView(
            'purchase.importaciones.pdf',
            compact('cabecera','datos')
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream("Importacion_$transactionId.pdf");
    }

}
