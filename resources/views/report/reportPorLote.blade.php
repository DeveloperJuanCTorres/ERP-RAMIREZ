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
        <div class="d-flex align-items-center mb-3">
            <h5 class="mr-3">
                Resultados para lote: <strong>{{ $lot }}</strong>
            </h5>

            <button class="btn btn-warning btn-sm"
                    data-toggle="modal"
                    data-target="#modalColor">
                <i class="fas fa-palette"></i> Cambiar color
            </button>
        </div>
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
                            @if($row->nuevo_color)
                            <td>{{ $row->product_name }} - Color: {{ $row->nuevo_color }}</td>
                            @elseif($row->product_color)
                            <td>{{ $row->product_name }} - Color: {{ $row->product_color }}</td>
                            @else
                            <td>{{ $row->product_name }}</td>
                            @endif
                            <td>{{ $row->location_name }}</td>
                            <td>
                                @if($row->transaction_type == 'production_purchase')
                                    Transformación
                                @elseif($row->transaction_type == 'purchase')
                                    Compra
                                @elseif($row->transaction_type == 'opening_stock')
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

            <div class="modal fade" id="modalColor">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('reporte.lote.cambiarColor') }}">
                        @csrf

                        <input type="hidden" name="lot_number" value="{{ $lot }}">

                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title">Cambiar Color del Lote</h4>
                                <button type="button" class="close" data-dismiss="modal">
                                    &times;
                                </button>
                            </div>

                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Nuevo Color</label>
                                    <input type="text"
                                        name="nuevo_color"
                                        class="form-control"
                                        placeholder="Ej: Rojo, Azul, Negro"
                                        required>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">
                                    Guardar
                                </button>

                                <button type="button"
                                        class="btn btn-default"
                                        data-dismiss="modal">
                                    Cancelar
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        @endcomponent
    @endisset
</section>

@endsection