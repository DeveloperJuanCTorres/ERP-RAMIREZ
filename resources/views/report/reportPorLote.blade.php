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

    @isset($datos)
        <div class="d-flex align-items-center mb-3">
            <h5 class="mr-3">
                Resultados para lote: <strong>{{ $lot }}</strong>
            </h5>

            <h5 class="mr-3">
                Chasis: 
                <strong>{{ $datos[0]->chasis ?? '-' }}</strong>
            </h5>

            <h5 class="mr-3">
                Póliza: 
                <strong>{{ $datos[0]->poliza ?? '-' }}</strong>
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
                        <!-- <tr>
                            <th>Movimiento</th>                            
                            <th>Ubicación</th>
                            <th>Producto</th>
                            <th>Fecha Compra</th>
                            <th>Cant. Comprada</th>
                            <th>Cant. Vendida</th>
                            <th>Cliente</th>
                            <th>Factura Venta</th>
                            <th>Fecha Venta</th>
                            <th>Stock Restante</th>
                        </tr> -->
                        <tr>
                            <th>Fecha</th>
                            <th>Movimiento</th>
                            <th>Ubicación</th>
                            <th>Producto</th>
                            <th>Referencia</th>
                            <th>Cliente / Proveedor</th>
                            <th>Usuario</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                       

                        @forelse($data as $row)

                            <tr>

                                <td>{{ \Carbon\Carbon::parse($row->fecha)->format('d/m/Y H:i') }}</td>

                                <td>

                                    @switch($row->movimiento)

                                        @case('Compra')
                                            <span class="label label-success">
                                                Compra
                                            </span>
                                        @break

                                        @case('Venta')
                                            <span class="label label-danger">
                                                Venta
                                            </span>
                                        @break

                                        @case('Transferencia')
                                            <span class="label label-primary">
                                                Transferencia
                                            </span>
                                        @break

                                        @default
                                            <span class="label label-default">
                                                {{ $row->movimiento }}
                                            </span>

                                    @endswitch

                                </td>

                                <td>{{ $row->ubicacion }}</td>

                                <td>

                                    {{ $row->producto }}

                                    @if(!empty($row->color))
                                        <br>
                                        <small>
                                            Color:
                                            {{ $row->color }}
                                        </small>
                                    @endif

                                </td>

                                <td>{{ $row->referencia }}</td>

                                <td>{{ $row->cliente }}</td>

                                <td>{{ $row->usuario }}</td>

                                <td>{{ $row->monto }}</td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center">
                                    No se encontraron movimientos.
                                </td>
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