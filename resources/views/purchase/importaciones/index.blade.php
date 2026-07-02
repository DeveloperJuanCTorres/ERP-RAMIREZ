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

            <div style="padding-top: 20px;">
                <a href="{{ route('historial-importaciones.excel', request()->all()) }}"
                class="btn btn-success">

                    <i class="fa fa-file-excel"></i>

                    Exportar Excel

                </a>

                <a href="{{ route('historial-importaciones.pdf', request()->all()) }}"
                class="btn btn-danger"
                target="_blank">

                    <i class="fa fa-file-pdf"></i>

                    Exportar PDF

                </a>
            </div>            

            <hr>

            <table id="tabla_importaciones" class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>Fecha</th>
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

                    @foreach($registros as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->transaction_date)->format('d/m/Y') }}</td>
                        <td>{{ $r->producto }}</td>
                        <td>{{ $r->motor }}</td>
                        <td>{{ $r->chasis }}</td>
                        <td>{{ $r->color }}</td>
                        <td>{{ $r->anio }}</td>
                        <td>{{ $r->poliza }}</td>
                        <td>{{ $r->guia }}</td>
                        <td>{{ $r->contenedor }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
            {{ $registros->appends(request()->all())->links() }}
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