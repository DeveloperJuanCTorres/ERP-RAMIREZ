@extends('layouts.app')

@section('content')

<section class="content no-print">
    <!-- <h3 class="mb-4">Reporte por Lote</h3> -->
    @component('components.filters', ['title' => 'Reporte por Lote'])
        <form action="{{ route('reporte.lote.buscar') }}" method="GET" class="mb-4">

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('unit_id', 'Ingrese N° Motor:*') !!}
                <div class="input-group">
                    <input type="text" 
                            name="lot_number" 
                            class="form-control"
                            placeholder="Ingrese lote"
                            value="{{ $lot ?? '' }}" 
                            required>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search text-white fa-lg"></i></button>
                    </span>
                </div>
            </div>
        </div>

        </form>
    @endcomponent

    @isset($data)
        <h5 class="mb-3">Resultados para lote: <strong>{{ $lot }}</strong></h5>
        @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped ajax_view">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Ubicación</th>
                            <th>Tipo movimiento</th>
                            <th>Fecha Compra</th>
                            <th>Cant. Comprada</th>
                            <th>Cant. Vendida</th>
                            <th>Cliente</th>
                            <th>Factura Venta</th>
                            <th>Fecha Venta</th>
                            <th>Stock Restante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                        <tr>
                            <td>{{ $row->product_name }}</td>
                            <td>{{ $row->location_name }}</td>
                            <td>
                                @if($row->transaction_type == 'production_purchase')
                                    Transformación
                                @elseif($row->transaction_type == 'purchase')
                                    Compra
                                @elseif($row->transaction_type == 'stock_transfer')
                                    Transferencia
                                @elseif($row->transaction_type == 'sell')
                                    Venta
                                @elseif($row->transaction_type == 'purchase_transfer')
                                    Transferencia
                                @else
                                    {{ $row->transaction_type }}
                                @endif
                            </td>
                            <td>{{ $row->purchase_date }}</td>
                            <td>{{ $row->quantity_purchased }}</td>
                            <td>{{ $row->sell_quantity }}</td>
                            <td>{{ $row->customer_name }}</td>
                            <td>{{ $row->sell_invoice_no }}</td>
                            <td>{{ $row->sell_date }}</td>
                            <td>{{ $row->stock_remaining }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No se encontraron movimientos para este lote.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcomponent
    @endisset
</section>

@endsection