@extends('layouts.app')
@section('title', 'Partes')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Registro de Maquinarias</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('parts_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('parts_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('parts_list_filter_supplier_id',  __('purchase.supplier') . ':') !!}
                {!! Form::select('parts_list_filter_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('parts_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('parts_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Todos los alquileres'])
        @can('loan.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action([\App\Http\Controllers\DiaryPartController::class, 'create'])}}" 
                        data-container=".parts_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('loan.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="parts_table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Maquinaria</th>
                            <th>Observacion</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade parts_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@stop
@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    //Date range as a button
    $('#parts_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#parts_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           parts_table.ajax.reload();
        }
    );
    $('#parts_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#parts_list_filter_date_range').val('');
        parts_table.ajax.reload();
    });
    
    
</script>

@endsection