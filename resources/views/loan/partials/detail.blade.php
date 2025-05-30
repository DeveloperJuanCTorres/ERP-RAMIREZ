<script>
    window.loan_id = @json($loan_id);
</script>
@extends('layouts.app')
@section('title', 'Préstamos Internos')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Préstamos
        <small>Internos</small>
    </h1>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">
                    @include('loan.partials.loan_info_tab')
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Historial de pagos'])
        
        @can('loan.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" data-loan-id="{{ $loan_id }}" id="loan_payments_table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Monto a pagar</th>
                            <th>Metodo de pago</th>
                            <th>Observacion</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade loan_payments_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>

<!-- /.content -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection