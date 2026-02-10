@inject('request', 'Illuminate\Http\Request')

@if($request->segment(1) == 'pos' && ($request->segment(2) == 'create' || $request->segment(3) == 'edit'
 || $request->segment(2) == 'payment'))
    @php
        $pos_layout = true;
    @endphp
@else
    @php
        $pos_layout = false;
    @endphp
@endif

@php
    $whitelist = ['127.0.0.1', '::1'];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ Session::get('business.name') }}</title>
        
        @include('layouts.partials.css')

        @yield('css')
    </head>

    <body class="@if($pos_layout) hold-transition lockscreen @else hold-transition skin-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'blue-light'}}@endif sidebar-mini @endif">
        <div class="wrapper thetop">
            <script type="text/javascript">
                if(localStorage.getItem("upos_sidebar_collapse") == 'true'){
                    var body = document.getElementsByTagName("body")[0];
                    body.className += " sidebar-collapse";
                }
            </script>
            @if(!$pos_layout)
                @include('layouts.partials.header')
                @include('layouts.partials.sidebar')
            @else
                @include('layouts.partials.header-pos')
            @endif

            @if(in_array($_SERVER['REMOTE_ADDR'], $whitelist))
                <input type="hidden" id="__is_localhost" value="true">
            @endif

            <!-- Content Wrapper. Contains page content -->
            <div class="@if(!$pos_layout) content-wrapper @endif">
                <!-- empty div for vuejs -->
                <div id="app">
                    @yield('vue')
                </div>
                <!-- Add currency related field-->
                <input type="hidden" id="__code" value="{{session('currency')['code']}}">
                <input type="hidden" id="__symbol" value="{{session('currency')['symbol']}}">
                <input type="hidden" id="__thousand" value="{{session('currency')['thousand_separator']}}">
                <input type="hidden" id="__decimal" value="{{session('currency')['decimal_separator']}}">
                <input type="hidden" id="__symbol_placement" value="{{session('business.currency_symbol_placement')}}">
                <input type="hidden" id="__precision" value="{{session('business.currency_precision', 2)}}">
                <input type="hidden" id="__quantity_precision" value="{{session('business.quantity_precision', 2)}}">
                <!-- End of currency related field-->
                @can('view_export_buttons')
                    <input type="hidden" id="view_export_buttons">
                @endcan
                @if(isMobile())
                    <input type="hidden" id="__is_mobile">
                @endif
                @if (session('status'))
                    <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
                @endif
                @yield('content')

                <div class='scrolltop no-print'>
                    <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
                </div>

                @if(config('constants.iraqi_selling_price_adjustment'))
                    <input type="hidden" id="iraqi_selling_price_adjustment">
                @endif

                <!-- This will be printed -->
                <section class="invoice print_section" id="receipt_section">
                </section>
                
            </div>
            @include('home.todays_profit_modal')
            <!-- /.content-wrapper -->

            @if(!$pos_layout)
                @include('layouts.partials.footer')
            @else
                @include('layouts.partials.footer_pos')
            @endif

            <audio id="success-audio">
              <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
            <audio id="error-audio">
              <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
            <audio id="warning-audio">
              <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
              <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
            </audio>
        </div>

        @if(!empty($__additional_html))
            {!! $__additional_html !!}
        @endif

        @include('layouts.partials.javascripts')

        <div class="modal fade view_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>

        @if(!empty($__additional_views) && is_array($__additional_views))
            @foreach($__additional_views as $additional_view)
                @includeIf($additional_view)
            @endforeach
        @endif

        <div class="modal fade" id="modalReporteCompras" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form method="GET"
                    action="{{ route('reportes.compras.productos') }}"
                    target="_blank">

                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fa fa-file-invoice-dollar"></i>
                                Generar Reporte de Compras
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-3">
                                    <label>Desde</label>
                                    <input type="date" name="fecha_inicio" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Hasta</label>
                                    <input type="date" name="fecha_fin" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>Modelo</label>
                                    <select name="product_id" id="modal_product_id" class="form-control select2">
                                        <option value="">Todos</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label>Contenedor</label>
                                    <input type="text" name="contenedor" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Guía</label>
                                    <input type="text" name="guia" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Proveedor</label>
                                    <select name="proveedor_id" id="modal_proveedor_id" class="form-control select2">
                                        <option value="">Todos</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Estado</label>
                                    <select name="estado" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="V">V</option>
                                        <option value="S">S</option>
                                        <option value="F">F</option>
                                        <option value="T">T</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Generar Reporte
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <script>
        $('#btn_reporte_compras').on('click', function (e) {
            e.preventDefault();

            $('#modalReporteCompras').modal('show');

            if (!$('#modalReporteCompras').data('loaded')) {

                $.get("{{ route('ajax.reporte.compras.data') }}", function (data) {

                    // MODELOS
                    const productSelect = $('#modal_product_id');
                    productSelect.html('<option value="">Todos</option>');

                    data.products.forEach(p => {
                        productSelect.append(
                            `<option value="${p.id}">${p.name}</option>`
                        );
                    });

                    // PROVEEDORES
                    const proveedorSelect = $('#modal_proveedor_id');
                    proveedorSelect.html('<option value="">Todos</option>');

                    data.proveedores.forEach(p => {
                        proveedorSelect.append(
                            `<option value="${p.id}">${p.supplier_business_name}</option>`
                        );
                    });

                    // INICIALIZAR SELECT2 (AMBOS)
                    $('#modal_product_id, #modal_proveedor_id').select2({
                        dropdownParent: $('#modalReporteCompras'),
                        placeholder: 'Seleccione una opción',
                        allowClear: true,
                        width: '100%'
                    });

                    $('#modalReporteCompras').data('loaded', true);
                });
            }
        });
        </script>



        <script>
        $('#modalReporteCompras').on('shown.bs.modal', function () {
            $('.select2').select2({
                dropdownParent: $('#modalReporteCompras'),
                placeholder: 'Buscar modelo...',
                allowClear: true
            });
        });
        </script>

    </body>

</html>