@extends('layouts.app')

@section('content')

<section class="content-header">
    <h1>Historial de Importaciones Masivas</h1>
</section>

<section class="content">

    <div class="box">

        <div class="box-body">

            <form method="GET">

                <div class="row">

                    <div class="col-md-3">
                        <label>Desde</label>
                        <input type="date"
                            name="desde"
                            class="form-control"
                            value="{{ $desde }}">
                    </div>

                    <div class="col-md-3">
                        <label>Hasta</label>
                        <input type="date"
                            name="hasta"
                            class="form-control"
                            value="{{ $hasta }}">
                    </div>

                    <div class="col-md-3">
                        <label>Producto</label>
                        <select class="form-control select2"
                                name="producto">
                            <option value="">Todos</option>
                            @foreach($productos as $id => $nombre)
                                <option value="{{ $id }}"
                                    {{ $producto == $id ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Motor</label>
                        <input type="text"
                            class="form-control"
                            name="motor"
                            value="{{ $motor }}">
                    </div>

                    <div class="col-md-2">
                        <label>Guía</label>
                        <input type="text"
                            class="form-control"
                            name="guia"
                            value="{{ $guia }}">
                    </div>

                    <div class="col-md-2">
                        <label>Contenedor</label>
                        <input type="text"
                            class="form-control"
                            name="contenedor"
                            value="{{ $contenedor }}">
                    </div>

                    <div class="col-md-2">

                        <label>&nbsp;</label>

                        <button class="btn btn-primary btn-block">
                            Buscar
                        </button>

                    </div>                   

                </div>

            </form>          

            <hr>

            @foreach($registros as $transactionId => $items)

                @php
                    $cabecera = $items->first();
                @endphp

                <div class="box box-primary">

                    <div class="box-header with-border">

                        <div class="pull-right">

                            <button
                                class="btn btn-primary"
                                data-toggle="collapse"
                                data-target="#detalle{{ $transactionId }}">

                                <strong>
                                    Ver detalle
                                </strong>

                            </button>

                            <a href="{{ route('historial-importaciones.excel', ['transaction' => $cabecera->transaction_id]) }}"
                            class="btn btn-success btn-xs">
                                <i class="fa fa-file-excel"></i> Excel
                            </a>

                            <a href="{{ route('historial-importaciones.pdf', ['transaction' => $cabecera->transaction_id]) }}"
                            class="btn btn-danger btn-xs"
                            target="_blank">
                                <i class="fa fa-file-pdf"></i> PDF
                            </a>
                        </div>                        

                    </div>

                    <div class="box-body">

                        <table class="table table-bordered">

                            <tr>
                                <th>Transacción ID</th>
                                <td>{{ $transactionId }}</td>
                                
                                <th>Fecha</th>
                                <td>{{ \Carbon\Carbon::parse($cabecera->transaction_date)->format('d/m/Y') }}</td>

                                <th>Ubicación</th>
                                <td>{{ $cabecera->business_name }}</td>
                            </tr>

                        </table>

                    </div>

                    <div id="detalle{{ $transactionId }}" class="collapse">

                        <table class="table table-striped table-bordered">

                            <thead>

                                <tr>
                                    <th>Producto</th>
                                    <th>Motor</th>
                                    <th>Chasis</th>
                                    <th>Color</th>
                                    <th>Año</th>
                                    <th>Póliza</th>
                                    <th>Guía</th>
                                    <th>Contenedor</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($items as $item)

                                <tr>

                                    <td>{{ $item->producto }}</td>
                                    <td>{{ $item->motor }}</td>
                                    <td>{{ $item->chasis }}</td>
                                    <td>{{ $item->color }}</td>
                                    <td>{{ $item->anio }}</td>
                                    <td>{{ $item->poliza }}</td>
                                    <td>{{ $item->guia }}</td>
                                    <td>{{ $item->contenedor }}</td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endforeach
            <!--  $registros->appends(request()->all())->links()  -->
        </div>

    </div>

</section>


<script>

    $(document).ready(function(){

        $('.select2').select2({
            width: '100%'
        });

    });

</script>

<script>
    $(document).ready(function () {

        $('#tabla_importaciones').DataTable({

            dom: 'Bfrtip',

            pageLength: 50,

            buttons: [

                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'btn btn-success'
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimir',
                    className: 'btn btn-primary'
                }

            ]

        });

    });
</script>


@endsection