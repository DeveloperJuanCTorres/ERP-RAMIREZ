@extends('layouts.app')
<style>
@media print {
    .no-print {
        display: none !important;
    }

    body {
        -webkit-print-color-adjust: exact;
    }
}
</style>
@section('title', 'Reporte Compras por Proveedor')

@section('content')

<section class="content-header">
    
    <div style="display:flex; justify-content:space-between; align-items:center;">
        
        <h1 style="margin:0;">
            Reporte Compras por Proveedor
        </h1>

        <div class="no-print">
            <button onclick="window.print()" class="btn btn-success">
                Imprimir
            </button>
        </div>

    </div>

</section>

<section class="content">

<div class="box box-primary">

    <div class="box-body">

        <h4>Proveedor: {{ $proveedor->supplier_business_name }}</h4>

        <h4>
            Desde: {{ $fecha_inicio }}
            &nbsp;&nbsp;&nbsp;
            Hasta: {{ $fecha_fin }}
        </h4>

        <hr>

        @php
            $total_general = 0;
        @endphp

        @foreach($compras as $compra)

            <h4>
                Compra N° {{ $compra->ref_no }}
            </h4>

            <p>
                Fecha:
                {{ date('d/m/Y', strtotime($compra->transaction_date)) }}
            </p>

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Inc. Impuesto</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                @php
                    $total_compra = 0;
                @endphp

                @foreach($compra->detalle as $item)

                    @php
                        $total_compra += $item->subtotal;
                    @endphp

                    <tr>
                        <td>{{ $item->producto }}</td>
                        <td>{{ number_format($item->quantity,2) }}</td>
                        <td>{{ number_format($item->purchase_price_inc_tax,2) }}</td>
                        <td>{{ number_format($item->subtotal,2) }}</td>
                    </tr>

                @endforeach

                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">
                            Total Compra
                        </th>

                        <th>
                            S/
                            {{ number_format($total_compra,2) }}
                        </th>
                    </tr>
                </tfoot>

            </table>

            @php
                $total_general += $total_compra;
            @endphp

            <br>

        @endforeach

        <div class="alert alert-info">

            <h3>
                Total General:
                S/ {{ number_format($total_general,2) }}
            </h3>

        </div>

    </div>

</div>

</section>

@endsection