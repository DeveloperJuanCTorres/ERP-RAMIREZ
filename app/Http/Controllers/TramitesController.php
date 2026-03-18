<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TramitesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('tramites as t')
                ->join('purchase_lines as pl', 'pl.guia', '=', 't.guia')
                ->leftJoin('comprobante_sunat as cs', function($join){
                    $join->whereRaw("cs.productos LIKE CONCAT('%Motor: ', pl.lot_number, '%')");
                })
                ->leftJoin('contacts as c', 'c.id', '=', 'cs.contact_id')
                ->select(
                    't.guia',
                    'pl.lot_number as numero_lote',
                    't.ciudad',
                    't.titulo',
                    't.fecha',
                    't.anio',
                    'c.name as cliente',
                    'cs.invoice_no as comprobante'
                );

            // FILTROS
            if ($request->guia) {
                $query->where('t.guia', 'like', '%' . $request->guia . '%');
            }

            if ($request->lote) {
                $query->where('pl.lot_number', 'like', '%' . $request->lote . '%');
            }

            return DataTables::of($query)

                ->addColumn('estado', function($row){
                    return $row->comprobante 
                        ? '<span class="label label-primary">FACTURADO</span>'
                        : '<span class="label label-warning">PENDIENTE</span>';
                })

                ->addColumn('accion', function($row){
                    if ($row->comprobante) {
                        return '<a href="/tramites/detalle/'.$row->numero_lote.'" class="btn btn-xs btn-primary">Ver</a>';
                    }
                    return '-';
                })

                ->rawColumns(['estado','accion'])
                ->make(true);
        }

        $guiasDisponibles = DB::table('purchase_lines')
            ->whereNotIn('guia', function($q){
                $q->select('guia')->from('tramites');
            })
            ->distinct()
            ->pluck('guia');

        return view('tramites.index', compact('guiasDisponibles'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            DB::table('tramites')->insert([
                'business_id' => session()->get('user.business_id'),
                'guia' => $request->guia,
                'ciudad' => $request->ciudad,
                'titulo' => $request->titulo,
                'fecha' => $request->fecha,
                'anio' => $request->anio,
            ]);

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function detalle($lote)
    {
        $data = DB::table('purchase_lines as pl')
            ->leftJoin('comprobante_sunat as cs', function($join){
                $join->whereRaw("cs.productos LIKE CONCAT('%', pl.lot_number, '%')");
            })
            ->leftJoin('contacts as c', 'c.id', '=', 'cs.contact_id')
            ->select(
                'pl.guia',
                'pl.lot_number',
                'pl.motor',
                'c.name as cliente',
                'cs.invoice_no as comprobante'
            )
            ->where('pl.lot_number', $lote)
            ->first();

        $detalle = DB::table('tramite_detalles')
            ->where('lot_number', $lote)
            ->first();

        return view('tramites.detalle', compact('data','detalle'));
    }

    public function guardarDetalle(Request $request)
    {
        DB::table('tramite_detalles')->updateOrInsert(
            ['lot_number' => $request->lote],
            [
                'fecha_ingreso' => $request->fecha_ingreso,
                'importe' => $request->importe,
                'titulo' => $request->titulo,
                'codigo_verificacion' => $request->codigo,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function cambiarEstado(Request $request)
    {
        DB::table('tramite_detalles')
            ->where('lot_number', $request->lote)
            ->update([
                'estado' => $request->estado
            ]);

        return response()->json(['success' => true]);
    }

    public function guardarPlaca(Request $request)
    {
        DB::table('tramite_detalles')
            ->where('lot_number', $request->lote)
            ->update([
                'placa' => $request->placa
            ]);

        return response()->json(['success' => true]);
    }

    public function guardarEstadoPlaca(Request $request)
    {
        DB::table('tramite_detalles')
            ->where('lot_number', $request->lote)
            ->update([
                'estado_entrega' => $request->estado_entrega
            ]);

        return response()->json(['success' => true]);
    }
}
