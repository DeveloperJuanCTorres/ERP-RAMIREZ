@extends('layouts.app')
@section('title', 'Detalle Trámite')

@section('content')

<section class="content-header">

    <div class="row">
        <div class="col-md-6">
            <h1>Detalle del Trámite</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ url('/tramites') }}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Volver a Trámites
            </a>
        </div>
    </div>

</section>

<section class="content">

<div class="box box-primary">

<div class="box-body">

{{-- ================= INFO PRINCIPAL ================= --}}
<div class="row text-center">

    <div class="col-md-3">
        <div class="well well-sm">
            <b>Guía</b><br>
            {{ $data->guia }}
        </div>
    </div>

    <div class="col-md-3">
        <div class="well well-sm">
            <b>Motor</b><br>
            {{ $data->lot_number }}
        </div>
    </div>

    <div class="col-md-3">
        <div class="well well-sm">
            <b>Cliente</b><br>
            {{ $data->cliente }}
        </div>
    </div>

    <div class="col-md-3">
        <div class="well well-sm">
            <b>Comprobante</b><br>
            {{ $data->comprobante ?? '-' }}
        </div>
    </div>

</div>

<hr>

@php
$completo = $detalle 
    && $detalle->fecha_ingreso 
    && $detalle->importe 
    && $detalle->titulo 
    && $detalle->codigo_verificacion;

$estado = $detalle->estado ?? 'tramite';
@endphp

{{-- ================= PASO 1 ================= --}}
<div class="box box-info">

<div class="box-header with-border">
    <h4 class="box-title">Registro SUNAT</h4>
</div>

<div class="box-body">

<div class="row">

    <div class="col-md-3">
        <label>Fecha Ingreso</label>
        <input type="date" id="fecha_ingreso" class="form-control campo"
            value="{{ $detalle->fecha_ingreso ?? '' }}"
            {{ $completo ? 'disabled' : '' }}>
    </div>

    <div class="col-md-3">
        <label>Importe</label>
        <input type="number" id="importe" class="form-control campo"
            value="{{ $detalle->importe ?? '' }}"
            {{ $completo ? 'disabled' : '' }}>
    </div>

    <div class="col-md-3">
        <label>Título</label>
        <input type="text" id="titulo" class="form-control campo"
            value="{{ $detalle->titulo ?? '' }}"
            {{ $completo ? 'disabled' : '' }}>
    </div>

    <div class="col-md-3">
        <label>Código Verificación</label>
        <input type="text" id="codigo" class="form-control campo"
            value="{{ $detalle->codigo_verificacion ?? '' }}"
            {{ $completo ? 'disabled' : '' }}>
    </div>

</div>

<br>

@if(!$completo)
<button id="btnGuardar" class="btn btn-primary" onclick="guardarPaso1()">
    Guardar
</button>
@endif

</div>
</div>

{{-- ================= ESTADO ================= --}}
@if($completo)

<div class="box box-warning">

<div class="box-header with-border">
    <h4 class="box-title">Estado del Trámite</h4>
</div>

<div class="box-body text-center">

@if($estado == 'tramite')

<button class="btn btn-warning btn-lg" onclick="confirmarCambioEstado()">
    EN TRÁMITE
</button>

@else

<button class="btn btn-success btn-lg" disabled>
    RECIBIDO
</button>

@endif

</div>
</div>

@endif

{{-- ================= ENTREGA ================= --}}
@if($detalle && $detalle->estado == 'recibido')

<div class="box box-success">

<div class="box-header with-border">
    <h4 class="box-title">Entrega</h4>
</div>

<div class="box-body">

<div class="row">

    <div class="col-md-4">
        <label>Placa</label>
        <input type="text" id="placa" class="form-control"
            value="{{ $detalle->placa ?? '' }}"
            {{ !empty($detalle->placa) ? 'disabled' : '' }}>
    </div>

    <div class="col-md-3">
        <label>&nbsp;</label><br>

        @if(!$detalle->placa)
        <button class="btn btn-primary" onclick="guardarPlaca()">
            Guardar Placa
        </button>
        @endif
    </div>

    <div class="col-md-3">
        <label>Estado</label><br>

        @if(($detalle->estado_entrega ?? 'almacen') == 'almacen')
            <button class="btn btn-warning" onclick="confirmarEntrega()">
                EN ALMACÉN
            </button>
        @else
            <button class="btn btn-success" disabled>
                ENTREGADO
            </button>
        @endif

    </div>

</div>

</div>
</div>

@endif

</div>
</div>

</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

// VALIDACIÓN
function validarCampos(){
    let fecha = $('#fecha_ingreso').val();
    let importe = $('#importe').val();
    let titulo = $('#titulo').val();
    let codigo = $('#codigo').val();

    if(fecha && importe && titulo && codigo){
        $('#btnGuardar').hide();
        $('.campo').prop('disabled', true);
    }
}

$('.campo').on('keyup change', validarCampos);

$(document).ready(validarCampos);

// GUARDAR
function guardarPaso1(){
    $.post('/tramites/detalle/guardar', {
        lote: '{{ $data->lot_number }}',
        fecha_ingreso: $('#fecha_ingreso').val(),
        importe: $('#importe').val(),
        titulo: $('#titulo').val(),
        codigo: $('#codigo').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    }, () => location.reload());
}

// ESTADO
function confirmarCambioEstado(){
    Swal.fire({
        title: '¿Cambiar estado?',
        text: '¿Desea cambiar a RECIBIDO?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí'
    }).then((result) => {
        if (result.isConfirmed) cambiarEstado();
    });
}

function cambiarEstado(){
    $.post('/tramites/detalle/estado', {
        lote: '{{ $data->lot_number }}',
        estado: 'recibido',
        _token: $('meta[name="csrf-token"]').attr('content')
    }, () => location.reload());
}

// ENTREGA
function confirmarEntrega(){
    Swal.fire({
        title: '¿Entregar unidad?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí'
    }).then((result) => {
        if (result.isConfirmed) guardarEntrega();
    });
}

function guardarEntrega(){
    $.post('/tramites/detalle/estado-placa', {
        lote: '{{ $data->lot_number }}',
        estado_entrega: 'entregado',
        _token: $('meta[name="csrf-token"]').attr('content')
    }, () => location.reload());
}

function guardarPlaca(){
    let placa = $('#placa').val();

    if(!placa){
        Swal.fire('Error', 'Ingrese la placa', 'error');
        return;
    }

    $.post('/tramites/detalle/placa', {
        lote: '{{ $data->lot_number }}',
        placa: placa,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, () => {
        Swal.fire('Guardado', 'Placa registrada', 'success')
        .then(() => location.reload());
    });
}

</script>

@endsection