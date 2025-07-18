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
    @component('components.filters', ['title' => __('report.filters')])        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('parts_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('parts_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent
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
                            <th>H. Trabajadas</th>
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

@stop
@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    //Date range as a button
    $('#parts_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#parts_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           daily_part_table.ajax.reload();
        }
    );
    $('#parts_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#parts_list_filter_date_range').val('');
        daily_part_table.ajax.reload();
    });
    
    
</script>



@endsection