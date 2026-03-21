@extends('layouts.app')
@section('title', __('lang_v1.stock_transfers'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.stock_transfers')
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">

    {{-- FILTROS --}}
    @component('components.filters', ['title' => __('report.filters')])

        <div class="col-md-3">
            <div class="form-group">
                <label>Desde fecha:</label>
                <input type="date" id="filter_start_date" class="form-control">
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>Hasta fecha:</label>
                <input type="date" id="filter_end_date" class="form-control">
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>Desde ubicación:</label>
                <select id="filter_location_from" class="form-control">
                    <option value="">Todos</option>
                    @foreach($business_locations as $loc)
                        <option value="{{$loc->id}}">{{$loc->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>Hasta ubicación:</label>
                <select id="filter_location_to" class="form-control">
                    <option value="">Todos</option>
                    @foreach($business_locations as $loc)
                        <option value="{{$loc->id}}">{{$loc->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>Referencia:</label>
                <input type="text" id="filter_ref_no" class="form-control">
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>N° Motor:</label>
                <input type="text" id="filter_lote" class="form-control">
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>&nbsp;</label><br>
                <button class="btn btn-danger" id="btn_clear_filters">
                    Limpiar filtros
                </button>
            </div>
        </div>

    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_stock_transfers')])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\StockTransferController::class, 'create'])}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="stock_transfer_table">
                <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('lang_v1.location_from')</th>
                        <th>@lang('lang_v1.location_to')</th>
                        <th>@lang('sale.status')</th>
                        <th>@lang('lang_v1.shipping_charges')</th>
                        <th>@lang('stock_adjustment.total_amount')</th>
                        <th>@lang('purchase.additional_notes')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

@include('stock_transfer.partials.update_status_modal')

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
	<script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>
    <script>
        $('#btn_clear_filters').click(function () {
            $('#filter_start_date, #filter_end_date, #filter_location_from, #filter_location_to, #filter_ref_no, #filter_lote').val('');
            stock_transfer_table.ajax.reload();
        });
    </script>
@endsection