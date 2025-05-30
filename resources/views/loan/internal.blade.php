@extends('layouts.app')
@section('title', 'Préstamos Internos')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Préstamos
        <small>Internos</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Todos los préstamos'])
        @can('loan.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action([\App\Http\Controllers\LoanController::class, 'create'])}}" 
                        data-container=".loans_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('loan.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="loans_table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Monto</th>
                            <th>Tipo</th>
                            <th>Tiempo</th>
                            <th>Interés</th>
                            <th>Total a pagar</th>
                            <th>Total pagado</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade loans_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection