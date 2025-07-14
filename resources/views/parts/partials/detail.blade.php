<script>
    window.part_id = @json($part_id);
</script>
@extends('layouts.app')
@section('title', 'Partes diarios')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Partes diarios
        <small>Internos</small>
    </h1>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">
                    @include('parts.partials.part_info')
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Historial de partes'])
        @can('loan.create')
            @slot('tool')
                
                 <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-toggle="modal" data-target="#daily_part_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('loan.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" data-loan-id="{{ $part_id }}" id="daily_part_table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th># Parte</th>
                            <th>Conductor</th>
                            <th>DNI</th>
                            <th>H-Inicio</th>
                            <th>H_Fin</th>
                            <th>Zona trabajo</th>
                            <th>Comubustible</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade" id="daily_part_modal"  tabindex="-1" role="dialog" aria-hidden="true"
    	aria-labelledby="gridSystemModalLabel">
        @include('parts.partials.daily_part')
    </div>

</section>

<!-- /.content -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection