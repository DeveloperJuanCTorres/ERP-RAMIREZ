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
            ->paginate(50);

        $productos = DB::table('products')
            ->where('business_id', $business_id)
            ->orderBy('name')
            ->pluck('name', 'id');

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

            ->select(

                'pl.id',
                'p.name as producto',
                'pl.motor',
                'pl.chasis',
                'pl.color',
                'pl.anio',
                'pl.poliza',
                'pl.guia',
                'pl.contenedor',
                't.transaction_date'
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

    public function excel(Request $request)
    {
        $datos = $this->obtenerConsulta($request)
            ->orderBy('transaction_date', 'desc')
            ->get();

        $coleccion = $datos->map(function ($item) {

            return [

                'Fecha' => date('d/m/Y', strtotime($item->transaction_date)),

                'Producto' => $item->producto,

                'Motor' => $item->motor,

                'Chasis' => $item->chasis,

                'Color' => $item->color,

                'Año' => $item->anio,

                'Póliza' => $item->poliza,

                'Guía' => $item->guia,

                'Contenedor' => $item->contenedor

            ];

        });

        return Excel::download(

            new HistorialImportacionesExport($coleccion),

            'Historial_Importaciones.xlsx'

        );
    }

    public function pdf(Request $request)
    {
        $datos = $this->obtenerConsulta($request)
            ->orderByDesc('t.transaction_date')
            ->get();

        $pdf = Pdf::loadView(
            'purchase.importaciones.pdf',
            compact('datos')
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Historial_Importaciones.pdf');

        // return $pdf->download('Historial_Importaciones.pdf');
    }
}
