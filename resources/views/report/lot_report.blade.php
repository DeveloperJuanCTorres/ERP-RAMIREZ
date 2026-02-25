@extends('layouts.app')
@section('title', __('lang_v1.lot_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('lang_v1.lot_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getStockReport']), 'method' => 'get', 'id' => 'stock_report_filter_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('category_id', __('category.category') . ':') !!}
                        {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                        {!! Form::select('sub_category', array(), null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_category_id']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('brand', __('product.brand') . ':') !!}
                        {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('unit',__('product.unit') . ':') !!}
                        {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('created_by', 'Usuario:') !!}
                        {!! Form::select('created_by', $users, null, [
                            'placeholder' => __('messages.all'),
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'id' => 'created_by'
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('filter_date', 'Fecha de pago:') !!}
                        {!! Form::date('filter_date', null, [
                            'class' => 'form-control',
                            'id' => 'filter_date'
                        ]) !!}
                    </div>
                </div>

                @if(Module::has('Manufacturing'))
                    <div class="col-md-3">
                        <div class="form-group">
                            <br>
                            <div class="checkbox">
                                <label>
                                  {!! Form::checkbox('only_mfg', 1, false, 
                                  [ 'class' => 'input-icheck', 'id' => 'only_mfg_products']); !!} {{ __('manufacturing::lang.only_mfg_products') }}
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    <div class="mb-3 text-right">
        <button class="btn btn-success" id="print_report">
            <i class="fa fa-print"></i> Imprimir constancia de pagos
        </button>

        <button class="btn btn-danger" id="pagar_seleccionados">
            <i class="fa fa-credit-card"></i> Pagar seleccionados
        </button>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="lot_report">
                    <thead>
                        <tr>
                            <th></th>
                            <th>SKU</th>
                            <th>@lang('business.product')</th>
                            <th>@lang('lang_v1.lot_number')</th>
                            <th>@lang('product.exp_date')</th>
                            <th>@lang('report.current_stock')</th>
                            <th>@lang('report.total_unit_sold')</th>
                            <th>@lang('lang_v1.total_unit_adjusted')</th>
                            
                            <th>Acciones</th>
                            <th>Fecha de pago</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                            <td id="footer_total_stock"></td>
                            <td id="footer_total_sold"></td>
                            <td id="footer_total_adjusted"></td>
                            <td></td> <!-- Acciones -->
                            <td></td> <!-- Fecha de pago -->
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

    <script>
        $('#print_report').click(function () {

            let params = $.param({
                location_id: $('#location_id').val(),
                category_id: $('#category_id').val(),
                sub_category_id: $('#sub_category_id').val(),
                brand_id: $('#brand').val(),
                unit_id: $('#unit').val(),
                created_by: $('#created_by').val(),
                filter_date: $('#filter_date').val(),
                only_mfg_products: $('#only_mfg_products').is(':checked') ? 1 : 0
            });

            window.open('/reports/lot-report-print?' + params, '_blank');
        });
    </script>
@endsection