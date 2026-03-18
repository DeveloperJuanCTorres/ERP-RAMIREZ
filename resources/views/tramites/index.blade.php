@extends('layouts.app')
@section('title', 'Trámites')

@section('content')

<section class="content-header no-print">
    <h1>Panel Trámites</h1>
</section>

<section class="content no-print">

    {{-- FILTROS --}}
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                <label>Guía:</label>
                <input type="text" id="filter_guia" class="form-control">
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>N° Lote:</label>
                <input type="text" id="filter_lote" class="form-control">
            </div>
        </div>
    @endcomponent

    {{-- TABLA --}}
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Listado de Trámites'])

        @slot('tool')
            <div class="box-tools">
                <button class="btn btn-block btn-primary" data-toggle="modal" data-target="#modalTramite">
                    <i class="fa fa-plus"></i> Nuevo Trámite
                </button>
            </div>
        @endslot

        <table class="table table-bordered table-striped ajax_view" id="tramites_table">
            <thead>
                <tr>
                    <th>Guía</th>
                    <th>N° Lote</th>
                    <th>Ciudad</th>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Año</th>
                    <th>Cliente</th>
                    <th>Comprobante</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    @endcomponent

</section>

{{-- MODAL --}}
<div class="modal fade" id="modalTramite">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4>Registrar Trámite</h4>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>Guía</label>
                    <select id="guia" class="form-control select2" style="width:100%">
                        <option value="">Seleccione</option>
                        @foreach($guiasDisponibles as $g)
                            <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Ciudad</label>
                    <input type="text" id="ciudad" class="form-control">
                </div>

                <div class="form-group">
                    <label>Título</label>
                    <input type="text" id="titulo" class="form-control">
                </div>

                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" id="fecha" class="form-control">
                </div>

                <div class="form-group">
                    <label>Año</label>
                    <input type="number" id="anio" class="form-control">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" onclick="guardarTramite()">Guardar</button>
            </div>

        </div>
    </div>
</div>

@stop

@section('javascript')

<script>
$(document).ready(function(){

    let table = $('#tramites_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/tramites',
            data: function(d){
                d.guia = $('#filter_guia').val();
                d.lote = $('#filter_lote').val();
            }
        },
        columns: [
            {data: 'guia'},
            {data: 'numero_lote'},
            {data: 'ciudad'},
            {data: 'titulo'},
            {data: 'fecha'},
            {data: 'anio'},
            {data: 'cliente'},
            {data: 'comprobante'},
            {data: 'estado'},
            {data: 'accion'}
        ]
    });

    $('#filter_guia, #filter_lote').keyup(function(){
        table.ajax.reload();
    });

});

function guardarTramite(){
    $.post('/tramites', {
        guia: $('#guia').val(),
        ciudad: $('#ciudad').val(),
        titulo: $('#titulo').val(),
        fecha: $('#fecha').val(),
        anio: $('#anio').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function(){
        $('#modalTramite').modal('hide');
        $('#tramites_table').DataTable().ajax.reload();
    });
}
</script>

@endsection