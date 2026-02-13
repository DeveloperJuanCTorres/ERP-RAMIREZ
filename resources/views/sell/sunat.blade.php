@extends('layouts.app')
@section('title', __( 'lang_v1.all_sales'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <!-- <h1>lang( 'sale.sells') -->
    <h1>Panel SUNAT
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        @include('sell.partials.sell_list_filters_sunat')
        @if(!empty($sources))
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sell_list_filter_source',  __('lang_v1.sources') . ':') !!}

                    {!! Form::select('sell_list_filter_source', $sources, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                </div>
            </div>
        @endif
    @endcomponent
    <!-- component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_sales')]) -->
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Facturación Electrónica'])
        @can('direct_sell.access')
            @slot('tool')               

                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="#" data-toggle="modal" data-target="#modalBuscarPedido">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
                <div class="box-tools" style="padding-right: 20px;">
                    <a class="btn btn-block btn-success" href="#" data-toggle="modal" data-target="#modalServicio">
                    <i class="fa fa-plus"></i> Facturar Servicios</a>
                </div>
                <div class="box-tools" style="padding-right: 20px;">
                    <a class="btn btn-block btn-info" href="#" data-toggle="modal" data-target="#modalConsultarSerie">
                    <i class="fa fa-plus"></i> Consultar Serie</a>
                </div>

                <!-- <button type="button" 
                        class="btn btn-danger box-tools" 
                        data-toggle="popover" 
                        title="Alerta de pago" 
                        data-placement="left"
                        data-content="Facturación Electrónica suspendida por falta de pago, comunicarse con el Área de Soporte">
                        Alerta de pago
                </button> -->
            @endslot
        @endcan
        @if(auth()->user()->can('direct_sell.view') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
        @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
         @endphp
            <table class="table table-bordered table-striped ajax_view" id="sell_table">
                <thead>
                    <tr>
                        <!-- <th>lang('messages.action')</th> -->
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado Sunat</th>
                        <th>Observación</th>
                        <th>PDF</th>
                        <th>XML</th>
                        <th>CDR</th>
                        <th>Sunat</th>
                        <th>Productos</th>      
                        <th>Correo</th>               
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        @endif
    @endcomponent
    <div class="modal fade" id="modalConsultarSerie" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fa fa-search"></i> Consultar Motor
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Últimos 5 dígitos del motor</label>
                        <input type="text" id="motor" class="form-control"
                            maxlength="5" placeholder="Ej: 86041">
                    </div>

                    <button class="btn btn-primary" id="btnBuscarMotor">
                        <i class="fa fa-search"></i> Buscar
                    </button>

                    <hr>

                    <!-- RESULTADO -->
                    <div id="resultadoMotor"></div>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="productDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Productos del Comprobante</h4>
            <button type="button" class="close" data-dismiss="modal">×</button>
        </div>
        <div class="modal-body"></div>
        </div>
    </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<!-- Modal venta-->
<div class="modal fade" id="modalBuscarPedido" tabindex="-1" role="dialog" aria-labelledby="modalBuscarPedidoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title text-white" id="modalBuscarPedidoLabel">Buscar Pedido por Documento1</h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Formulario de búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <input type="text" id="documento" class="form-control" placeholder="Ingrese N° de documento...">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success btn-block" id="btnBuscar">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

                <div class="row" style="padding-top: 15px;padding-bottom: 15px;">
                    <input type="hidden" id="ref_no">
                    <input type="hidden" id="contact_id">
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('comprobante',  'Comprobante:') !!}
                            {!! Form::select(
                                'invoice_scheme_id',
                                [],
                                null,
                                [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('messages.please_select'),
                                    'id' => 'invoice_scheme_id'
                                ]
                            ); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="">Moneda:</label>
                        <select class="form-control select2" style="width: 100%;" name="" id="moneda">
                            <option value="1">SOLES</option>
                            <option value="2">DÓLARES</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="">Fecha Emisión:</label>
                        <input class="form-control" type="date" id="fecha_emision" min="" max="">
                    </div>
                    <div class="col-md-2">
                        <label for="">Tipo Pago:</label>
                        <select class="form-control select2" style="width: 100%;" name="" id="tipo_pago">
                            <option value="contado">CONTADO</option>
                            <option value="credito">CREDITO</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="grupo_fecha_pago">
                        <label for="">Fecha Pago:</label>
                        <input class="form-control" type="date" id="fecha_pago" min="" max="">
                    </div>
                </div>
                <div class="row" hidden id="datos_cliente" style="padding-bottom: 15px;">  
                    <div class="col-md-4">
                        <label id="tipodoc"></label>
                        <div style="display: flex;">
                            <!-- <p id="numerodoc"></p> -->
                             <input class="form-control" type="text" id="numerodoc">
                            <button class="btn btn-primary buscar_sunat" type="button">Buscar</button>
                        </div>                        
                    </div> 
                    <div class="col-md-4">
                        <label for="">Cliente:</label>
                        <input class="form-control"  type="text" id="cliente">
                        <!-- <p for="" id="cliente"></p> -->
                    </div>      
                    <div class="col-md-4">
                        <label for="">Dirección:</label>
                        <input class="form-control"  type="text" id="address">
                        <!-- <p for="" id="cliente"></p> -->
                    </div>          
                </div>

                <!-- Tabla de resultados -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tablaItems">
                        <thead class="thead-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Motor</th>
                                <th>Color</th>
                                <th>Chasis</th>
                                <th>Poliza</th>
                                <th>Año</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se llenan los datos dinámicamente -->
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGenerarComprobante">
                    <i class="fa fa-file-invoice"></i> Generar Comprobante
                </button>
            </div>

        </div>
    </div>
</div>
<!-- fin modal -->

<!-- Modal Servicio -->
<div class="modal fade" id="modalServicio" tabindex="-1" role="dialog" aria-labelledby="modalBuscarPedidoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            
            <div class="modal-header bg-success text-white">
                <h3 class="modal-title text-secondary" id="modalBuscarPedidoLabel">Buscar Pedido por Documento</h3>
                <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Formulario de búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <input type="text" id="documento_servicio" class="form-control" placeholder="Ingrese N° de documento...">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success btn-block" id="btnBuscarServicio">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

                <div class="row" style="padding-top: 15px;padding-bottom: 15px;">
                    <input type="hidden" id="ref_no_servicio">
                    <input type="hidden" id="contact_id_servicio">
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('comprobante',  'Comprobante:') !!}
                            {!! Form::select('invoice_scheme_id_servicio', $invoice_schemes, $default_invoice_schemes->id, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select'), 'id' => 'invoice_scheme_id_servicio']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="">Moneda:</label>
                        <select class="form-control select2" style="width: 100%;" name="" id="moneda_servicio">
                            <option value="1">SOLES</option>
                            <option value="2">DÓLARES</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="">Fecha Emisión:</label>
                        <input class="form-control" type="date" id="fecha_emision_servicio" min="" max="">
                    </div>
                    <div class="col-md-4">
                        <label for="">Tipo detracción:</label>
                        <select class="form-control select2" style="width: 100%;" name="" id="tipo_detraccion_servicio">
                            <option value="0">Operacion NO sujeta a detracción</option>
                            <option value="17">019 Arrendamiento de bienes muebles</option>
                        </select>
                    </div>                    
                </div>
                <div class="row" style="padding-bottom: 15px;">
                    <div class="col-md-2">
                        <label for="">Tipo Pago:</label>
                        <select class="form-control select2" style="width: 100%;" name="" id="tipo_pago_servicio">
                            <option value="contado">CONTADO</option>
                            <option value="credito">CREDITO</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="grupo_fecha_pago_servicio">
                        <label for="">Fecha Pago:</label>
                        <input class="form-control" type="date" id="fecha_pago_servicio" min="" max="">
                    </div>
                </div>
                <div class="row" hidden id="datos_cliente_servicio" style="padding-bottom: 15px;">  
                    <div class="col-md-4">
                        <label id="tipodoc_servicio"></label>
                        <div style="display: flex;">
                            <!-- <p id="numerodoc"></p> -->
                             <input class="form-control" type="text" id="numerodoc_servicio">
                            <button class="btn btn-primary buscar_sunat_servicio" type="button">Buscar</button>
                        </div>                        
                    </div> 
                    <div class="col-md-4">
                        <label for="">Cliente:</label>
                        <input class="form-control"  type="text" id="cliente_servicio">
                        <!-- <p for="" id="cliente"></p> -->
                    </div>      
                    <div class="col-md-4">
                        <label for="">Dirección:</label>
                        <input class="form-control"  type="text" id="address_servicio">
                        <!-- <p for="" id="cliente"></p> -->
                    </div>          
                </div>

                <!-- Tabla de resultados -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tablaServicio">
                        <thead class="thead-dark bg-success">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se llenan los datos dinámicamente -->
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGenerarComprobanteServicio">
                    <i class="fa fa-file-invoice"></i> Generar Comprobante
                </button>
            </div>

        </div>
    </div>
</div>
<!-- fin modal -->

<!-- Modal Nota de Crédito -->
<div class="modal fade" id="modalNotaCredito" tabindex="-1" role="dialog" aria-labelledby="modalNotaCreditoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white">Generar Nota de Crédito</h5>
                <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close">
                    <span class="text-secondary" aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Aquí se carga la info vía Ajax -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGenerarNC">Generar Nota de Crédito</button>
            </div>

        </div>
    </div>
</div>
<!-- Fin Modal Nota de Crédito -->

<!-- Modal para envbiar correo -->
 <div class="modal fade" id="modalEnviarCorreo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">Enviar Comprobante por Correo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="comprobante_id">

                <div class="form-group">
                    <label>Correo destino</label>
                    <input type="email" id="correo_destino" class="form-control" placeholder="cliente@email.com" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnEnviarCorreo">
                    Enviar
                </button>
            </div>

        </div>
    </div>
</div>


@include('sell.partials.modal_comprobante')

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->

@stop

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready( function(){
        //Date range as a button
        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
            sell_table.ajax.reload();
        });

        sell_table = $('#sell_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            "ajax": {
                "url": "/panelSunat",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.is_direct_sale = 1;

                    d.location_id = $('#sell_list_filter_location_id').val();
                    d.customer_id = $('#sell_list_filter_customer_id').val();
                    d.payment_status = $('#sell_list_filter_payment_status').val();
                    d.created_by = $('#created_by').val();
                    d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                    d.service_staffs = $('#service_staffs').val();

                    if($('#shipping_status').length) {
                        d.shipping_status = $('#shipping_status').val();
                    }

                    if($('#sell_list_filter_source').length) {
                        d.source = $('#sell_list_filter_source').val();
                    }

                    if($('#only_subscriptions').is(':checked')) {
                        d.only_subscriptions = 1;
                    }

                    d = __datatable_ajax_callback(d);
                }
            },
            scrollY:        "75vh",
            scrollX:        true,
            scrollCollapse: true,
            columns: [
                { data: 'type', name: 'type' },
                { data: 'fecha_emision', name: 'fecha_emision' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'contact_name', name: 'contact_name' },
                { data: 'total', name: 'total' },
                { data: 'estado_sunat', orderable: false, searchable: false },
                { data: 'observacion', orderable: false, searchable: false },
                { data: 'pdf', orderable: false, searchable: false },
                { data: 'xml', orderable: false, searchable: false },
                { data: 'cdr', orderable: false, searchable: false },
                { data: 'sunat', orderable: false, searchable: false },
                { data: 'email', orderable: false, searchable: false },
                { data: 'productos', orderable: false, searchable: false },            
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sell_table'));
            },        
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(6)').attr('class', 'clickable_td');
            }
        });

        $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source',  function() {
            sell_table.ajax.reload();
        });

        $('#only_subscriptions').on('ifChanged', function(event){
            sell_table.ajax.reload();
        });

        $(document).on('click', 'button.envio_sunat_button', function() {
            var type = $(this).attr("type-id");
            if (type == 'sell') {
                swal({
                    title: 'Envío de Factura electrónica',
                    text: '¿Estás seguro de enviar este documento a sunat?',
                    icon: "success",
                    buttons: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var data = $(this).attr("data-id");
                        $.ajax({
                            method: "POST",
                            url: "/enviarsunat",
                            dataType: "json",
                            data: {id: data},
                            success: function(result){
                                if(result.status == true){
                                    toastr.success(result.msg);
                                    sell_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            }
            if (type == 'sell_return')
            {
                const select = document.createElement("select");
                const opt1 = document.createElement("option");
                const opt2 = document.createElement("option");
                const opt3 = document.createElement("option");
                const opt4 = document.createElement("option");
                const opt5 = document.createElement("option");
                const opt6 = document.createElement("option");
                const opt7 = document.createElement("option");
                const opt8 = document.createElement("option");
                const opt9 = document.createElement("option");
                const opt10 = document.createElement("option");
                const opt11 = document.createElement("option");
                const opt12 = document.createElement("option");
                const opt13 = document.createElement("option");
                const placeholder = document.createElement("placeholder");

                

                opt1.value = "1";
                opt1.text = "ANULACIÓN DE LA OPERACIÓN";
                opt2.value = "2";
                opt2.text = "ANULACIÓN POR ERROR EN EL RUC";
                opt3.value = "3";
                opt3.text = "CORRECCIÓN POR ERROR EN LA DESCRIPCIÓN";
                opt4.value = "4";
                opt4.text = "DESCUENTO GLOBAL";
                opt5.value = "5";
                opt5.text = "DESCUENTO POR ÍTEM";
                opt6.value = "6";
                opt6.text = "DEVOLUCIÓN TOTAL";
                opt7.value = "7";
                opt7.text = "DEVOLUCIÓN POR ÍTEM";
                opt8.value = "8";
                opt8.text = "BONIFICACIÓN";
                opt9.value = "9";
                opt9.text = "DISMINUCIÓN EN EL VALOR";
                opt10.value = "10";
                opt10.text = "OTROS CONCEPTOS";
                opt11.value = "11";
                opt11.text = "AJUSTES AFECTOS AL IVAP";
                opt12.value = "12";
                opt12.text = "AJUSTES DE OPERACIONES DE EXPORTACIÓN";
                opt13.value = "13";
                opt13.text = "AJUSTES - MONTOS Y/O FECHAS DE PAGO";

                select.className = "form-control";

                select.add(opt1, null);
                select.add(opt2, null);
                select.add(opt3, null);
                select.add(opt4, null);
                select.add(opt5, null);
                select.add(opt6, null);
                select.add(opt7, null);
                select.add(opt8, null);
                select.add(opt9, null);
                select.add(opt10, null);
                select.add(opt11, null);
                select.add(opt12, null);
                select.add(opt13, null);
                swal({
                    title: 'Envío de Nota de Crédito',
                    text: '¿Estás seguro de enviar este documento a sunat?',
                    icon: "success",
                    content : select,
                    buttons: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var data = $(this).attr("data-id");
                        console.log(select.value);
                        $.ajax({
                            method: "POST",
                            url: "/enviarsunat",
                            dataType: "json",
                            data: {id: data,motivo_id: select.value},
                            success: function(result){
                                if(result.status == true){
                                    toastr.success(result.msg);
                                    sell_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            }
            
        });

        $(document).on('click', 'button.anulacion_sunat_button', function() {
            Swal.fire({
                title: 'Motivo de la anulación',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = $(this).data("id");
                    var motivo = result.value;
                    
                    $.ajax({
                        method: "POST",
                        url: "/anulacionSunat",
                        dataType: "json",
                        data: { id: data, motivo: motivo },
                        success: function(result){
                            if(result.status){
                                toastr.success(result.msg);
                                sell_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', 'a.nota_credito_a', function() {
            swal({
            title: 'Observación',
            inputAttributes: {
                autocapitalize: 'off'
            },    
            content: "input",        
            buttons: true,
            allowOutsideClick: () => !swal.isLoading()
            }).then((willDelete) => {
                if (willDelete) {
                    var data = $(this).attr("data-id");
                    $.ajax({
                        method: "POST",
                        url: "/notaCreditoSunat",
                        dataType: "json",
                        data: {id: data, observacion: willDelete},
                        success: function(result){
                            if(result.status == true){
                                toastr.success(result.msg);
                                sell_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
        
    });

    $(document).on('click', '.show_products_btn', function () {
        var productos = $(this).data('products');

        try {
            productos = typeof productos === 'string' ? JSON.parse(productos) : productos;
        } catch (e) {
            console.error("Error al parsear productos", e);
            return;
        }

        let html = `
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cant.</th>
                        <th>P. Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
        `;

        productos.forEach(p => {
            html += `
                <tr>
                    <td style="white-space: normal;">${p.descripcion}</td>
                    <td>${p.cantidad}</td>
                    <td>${parseFloat(p.precio_unitario).toFixed(2)}</td>
                    <td>${parseFloat(p.total).toFixed(2)}</td>
                </tr>
            `;
        });

        html += `</tbody></table>`;

        $('#productDetailModal .modal-body').html(html);
        $('#productDetailModal').modal('show');
    });

</script>

<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>


<script>
    $(document).ready(function() {

        $(document).on('click', '.buscar_sunat', function() {
            let comprobante = $('#invoice_scheme_id option:selected').text().toLowerCase();
            $contacto = $('#numerodoc').val();
            $type = '';
            if (comprobante.includes('boleta')) {
                $type = 'boleta'
            }
            if (comprobante.includes('factura')) {            
                $type = 'factura'
            }
        
            console.log($type);
            if($type=='')
            {
                toastr.error('Debe seleccionar el comporbante "Boleta" o "Factura"');
            }
            else
            {
                if($type == 'boleta')
                {
                    //toastr.info('Estamos trabajando en ello');
                    $.ajax({
                        method: 'POST',
                        url: '/consulta_dni',
                        dataType: 'json',
                        data: {id: $contacto},
                        success: function(result) {
                            if (result.status == true) {
                                $('#cliente').val(result.msg.nombres + ' ' + result.msg.apellidoPaterno + ' ' + result.msg.apellidoMaterno);
                                toastr.success('La persona: ' + result.msg.nombres + ' se encontró con éxito');
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
                if($type == 'factura')
                {
                    $.ajax({
                        method: 'POST',
                        url: '/consulta_ruc',
                        dataType: 'json',
                        data: {id: $contacto},
                        success: function(result) {
                            if (result.status == true) {
                                $('#cliente').val(result.msg.razonSocial);
                                $('#address').val(result.msg.direccion);
                                toastr.success('La empresa: ' + result.msg.razonSocial + ' se encontró con éxito');
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            }  
            
        });

        $(document).on('click', '.buscar_sunat_servicio', function() {
            let comprobante = $('#invoice_scheme_id_servicio option:selected').text().toLowerCase();
            $contacto = $('#numerodoc_servicio').val();
            $type = '';
            if (comprobante.includes('boleta')) {
                $type = 'boleta'
            }
            if (comprobante.includes('factura')) {            
                $type = 'factura'
            }
        
            console.log($type);
            if($type=='')
            {
                toastr.error('Debe seleccionar el comporbante "Boleta" o "Factura"');
            }
            else
            {
                if($type == 'boleta')
                {
                    //toastr.info('Estamos trabajando en ello');
                    $.ajax({
                        method: 'POST',
                        url: '/consulta_dni',
                        dataType: 'json',
                        data: {id: $contacto},
                        success: function(result) {
                            if (result.status == true) {
                                $('#cliente_servicio').val(result.msg.nombres + ' ' + result.msg.apellidoPaterno + ' ' + result.msg.apellidoMaterno);
                                toastr.success('La persona: ' + result.msg.nombres + ' se encontró con éxito');
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
                if($type == 'factura')
                {
                    $.ajax({
                        method: 'POST',
                        url: '/consulta_ruc',
                        dataType: 'json',
                        data: {id: $contacto},
                        success: function(result) {
                            if (result.status == true) {
                                $('#cliente_servicio').val(result.msg.razonSocial);
                                $('#address_servicio').val(result.msg.direccion);
                                toastr.success('La empresa: ' + result.msg.razonSocial + ' se encontró con éxito');
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            }  
            
        });

        // Acción de buscar
        $('#btnBuscar').click(function() {
            let documento = $('#documento').val().trim();
            if(documento === '') {
                alert('Ingrese un número de documento');
                return;
            }

            // Petición AJAX al servidor
            $.ajax({
                url: '/pedidos/buscar', // ruta Laravel
                method: 'GET',
                data: { documento: documento },
                success: function(response) {
                    let tbody = $('#tablaItems tbody');
                    $('#datos_cliente').show();
                    tbody.empty();


                    let $invoiceSelect = $('#invoice_scheme_id');

                    $invoiceSelect.empty();
                    $invoiceSelect.append('<option value="">Seleccione comprobante</option>');

                    if (response.invoice_schemes && response.invoice_schemes.length > 0) {
                        response.invoice_schemes.forEach(item => {

                            let texto = item.prefix
                                ? `${item.name} - ${item.prefix}`
                                : item.name;

                            let selected = item.is_default == 1 ? 'selected' : '';

                            $invoiceSelect.append(`
                                <option value="${item.id}" ${selected}>
                                    ${texto}
                                </option>
                            `);
                        });
                    }

                    // refrescar select2
                    $invoiceSelect.trigger('change.select2');

                if (response.contact) {
                        // Mostramos el nombre del cliente en el label
                        $('#contact_id').val(response.contact.id);
                        $('#ref_no').val(response.ref_no);
                        if (response.contact.name) {
                            $('#tipodoc').text('DNI:');
                            $('#numerodoc').val(response.contact.contact_id);
                            $('#cliente').val(response.contact.name);
                            $('#address').val(response.contact.address_line_1);
                        }
                        else{
                            $('#tipodoc').text('RUC:');
                            $('#numerodoc').val(response.contact.contact_id);
                            $('#cliente').val(response.contact.supplier_business_name);
                            $('#address').val(response.contact.address_line_1);
                        }
                        
                    } else {
                        $('#cliente').text('Cliente no encontrado');
                    }

                    if(response.products.length === 0) {
                        tbody.append('<tr><td colspan="10" class="text-center">No se encontraron resultados sin facturar</td></tr>');
                        return;
                    }

                    response.products.forEach(item => {
                        tbody.append(`
                            <tr data-id="${item.id}">
                                <td>
                                    <input type="text" class="form-control form-control-sm producto" value="${item.producto}">
                                </td>
                                <td>${item.motor}</td>
                                <td>
                                    <input type="text" class="form-control form-control-sm color" value="${item.color}">
                                </td>
                                <td>${item.chasis}</td>
                                <td>${item.poliza}</td>
                                <td>${item.anio}</td>
                                <td>${item.cantidad}</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm precio" value="${item.precio}" min="0" step="0.01">
                                </td>
                                <td class="subtotal">${(item.cantidad * item.precio).toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger btnEliminar"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        `);
                    });
                },
                error: function() {
                    alert('Error al buscar el documento');
                }
            });
            
        });

        // Acción de buscar servicio
        $('#btnBuscarServicio').click(function() {
            let documento = $('#documento_servicio').val().trim();
            if(documento === '') {
                alert('Ingrese un número de documento');
                return;
            }

            // Petición AJAX al servidor
            $.ajax({
                url: '/pedidos/buscar', // ruta Laravel
                method: 'GET',
                data: { documento: documento },
                success: function(response) {
                    let tbody = $('#tablaServicio tbody');
                    $('#datos_cliente_servicio').show();
                    tbody.empty();

                if (response.contact) {
                        // Mostramos el nombre del cliente en el label
                        $('#contact_id_servicio').val(response.contact.id);
                        $('#ref_no_servicio').val(response.ref_no);
                        if (response.contact.name) {
                            $('#tipodoc_servicio').text('DNI:');
                            $('#numerodoc_servicio').val(response.contact.contact_id);
                            $('#cliente_servicio').val(response.contact.name);
                            $('#address_servicio').val(response.contact.address_line_1);
                        }
                        else{
                            $('#tipodoc_servicio').text('RUC:');
                            $('#numerodoc_servicio').val(response.contact.contact_id);
                            $('#cliente_servicio').val(response.contact.supplier_business_name);
                            $('#address_servicio').val(response.contact.address_line_1);
                        }
                        
                    } else {
                        $('#cliente_servicio').text('Cliente no encontrado');
                    }

                    if(response.products.length === 0) {
                        tbody.append('<tr><td colspan="10" class="text-center">No se encontraron resultados sin facturar</td></tr>');
                        return;
                    }

                    response.products.forEach(item => {
                        tbody.append(`
                            <tr data-id="${item.id}">
                                <td>
                                    <input type="text" class="form-control form-control-sm producto_servicio" value="${item.producto}">
                                </td>
                                <td>${item.cantidad}</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm precio_servicio" value="${item.precio}" min="0" step="0.01">
                                </td>
                                <td class="subtotal_servicio">${(item.cantidad * item.precio).toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger btnEliminarServicio"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        `);
                    });
                },
                error: function() {
                    alert('Error al buscar el documento');
                }
            });
            
        });

        // Actualizar subtotal cuando cambie el precio
        $(document).on('input', '.precio', function() {
            let row = $(this).closest('tr');
            
            // Obtener cantidad y precio como números válidos
            let cantidad = parseFloat(row.find('td:eq(6)').text()) || 0;
            let precio = parseFloat($(this).val()) || 0;

            // Calcular subtotal
            let subtotal = cantidad * precio;

            // Si el subtotal no es un número, mostrar 0.00
            if (isNaN(subtotal)) subtotal = 0;

            // Actualizar en la tabla con dos decimales
            row.find('.subtotal').text(subtotal.toFixed(2));
        });

        // Actualizar subtotal cuando cambie el precio SERVICIO
        $(document).on('input', '.precio_servicio', function() {
            let row = $(this).closest('tr');
            
            // Obtener cantidad y precio como números válidos
            let cantidad = parseFloat(row.find('td:eq(1)').text()) || 0;
            let precio = parseFloat($(this).val()) || 0;

            // Calcular subtotal
            let subtotal = cantidad * precio;

            // Si el subtotal no es un número, mostrar 0.00
            if (isNaN(subtotal)) subtotal = 0;

            // Actualizar en la tabla con dos decimales
            row.find('.subtotal_servicio').text(subtotal.toFixed(2));
        });

        // Eliminar ítem
        $(document).on('click', '.btnEliminar', function() {
            $(this).closest('tr').remove();
        });

        // Eliminar ítem SERVICIO
        $(document).on('click', '.btnEliminarServicio', function() {
            $(this).closest('tr').remove();
        });

        $('#modalBuscarPedido').on('hidden.bs.modal', function () {
            $('#documento').val('');              // limpia el campo de búsqueda
            $('#tablaItems tbody').empty();       // limpia los resultados
        });

        $('#modalServicio').on('hidden.bs.modal', function () {
            $('#documento_servicio').val('');              // limpia el campo de búsqueda
            $('#tablaServicio tbody').empty();       // limpia los resultados
        });

    });
</script>

<script>
    $(document).ready(function () {

        // Cuando el usuario hace clic en "Generar Comprobante"
        $('#btnGenerarComprobante').click(function () {

            // 1️⃣ Obtener datos del cliente
            let contact_id = $('#contact_id').val();
            let ref_no = $('#ref_no').val();
            let tipo_doc = $('#tipodoc').text();
            let numero_doc = $('#numerodoc').val();
            let cliente = $('#cliente').val();
            let direccion = $('#address').val();
            let fecha_emision = $('#fecha_emision').val();
            let tipo_pago = $('#tipo_pago').val();
            let fecha_pago = $('#fecha_pago').val();
            let moneda = $('#moneda').val();
            let comprobante_id = $('select[name="invoice_scheme_id"]').val();

            // 2️⃣ Obtener los productos de la tabla
            let productos = [];
            $('#tablaItems tbody tr').each(function () {
                let fila = $(this);
                productos.push({
                    producto: fila.find('.producto').val(),
                    motor: fila.find('td:eq(1)').text(),
                    color: fila.find('.color').val(),
                    chasis: fila.find('td:eq(3)').text(),
                    poliza: fila.find('td:eq(4)').text(),
                    anio: fila.find('td:eq(5)').text(),
                    cantidad: fila.find('td:eq(6)').text(),
                    precio: parseFloat(fila.find('.precio').val()) || 0,
                    subtotal: fila.find('td:eq(8)').text(),
                });
            });

            if (productos.length === 0) {
                alert("No hay productos para generar el comprobante.");
                return;
            }

            // 3️⃣ Confirmar acción
            if (!confirm("¿Deseas generar el comprobante con estos productos?")) return;
          

            // 4️⃣ Enviar los datos a tu API Laravel
            $.ajax({
                url: '/generar-comprobante',
                method: 'POST',
                data: {
                    contact_id: contact_id,
                    ref_no: ref_no,
                    tipo_doc: tipo_doc,
                    numero_doc: numero_doc,
                    cliente: cliente,
                    direccion: direccion,
                    comprobante_id: comprobante_id,
                    productos: productos,
                    fecha_emision: fecha_emision,
                    tipo_pago: tipo_pago,
                    fecha_pago: fecha_pago,
                    moneda: moneda,
                    _token: '{{ csrf_token() }}' // por seguridad si no usas Sanctum
                },
                success: function (response) {
                    alert("✅ Comprobante generado correctamente: " + response.numero_comprobante);
                    $('#modalBuscarPedido').modal('hide');

                    $('#sell_table').DataTable().ajax.reload(null, false);

                    // ✅ abrir nueva pestaña antes del AJAX para evitar bloqueo del navegador
                    let ventana = window.open('', '_blank');

                    let url = '/comprobante/vista/' + response.id_comprobante;
                    ventana.location.href = url;
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert("❌ Error al generar el comprobante.");
                }
            });
        });

    });
</script>

<script>
    $(document).ready(function () {

        // Cuando el usuario hace clic en "Generar Comprobante"
        $('#btnGenerarComprobanteServicio').click(function () {

            // 1️⃣ Obtener datos del cliente
            let contact_id = $('#contact_id_servicio').val();
            let ref_no = $('#ref_no_servicio').val();
            let tipo_doc = $('#tipodoc_servicio').text();
            let numero_doc = $('#numerodoc_servicio').val();
            let cliente = $('#cliente_servicio').val();
            let direccion = $('#address_servicio').val();
            let fecha_emision = $('#fecha_emision_servicio').val();
            let tipo_pago = $('#tipo_pago_servicio').val();
            let tipo_detraccion = $('#tipo_detraccion_servicio').val();
            let fecha_pago = $('#fecha_pago_servicio').val();
            let moneda = $('#moneda_servicio').val();
            let comprobante_id = $('select[name="invoice_scheme_id_servicio"]').val();

            // 2️⃣ Obtener los productos de la tabla
            let productos = [];
            $('#tablaServicio tbody tr').each(function () {
                let fila = $(this);
                productos.push({
                    producto: fila.find('.producto_servicio').val(),
                    cantidad: fila.find('td:eq(1)').text(),
                    precio: parseFloat(fila.find('.precio_servicio').val()) || 0,
                    subtotal: fila.find('td:eq(3)').text(),
                });
            });

            if (productos.length === 0) {
                alert("No hay productos para generar el comprobante.");
                return;
            }

            // 3️⃣ Confirmar acción
            if (!confirm("¿Deseas generar el comprobante con estos productos?")) return;
          

            // 4️⃣ Enviar los datos a tu API Laravel
            $.ajax({
                url: '/generar-comprobante-servicio',
                method: 'POST',
                data: {
                    contact_id: contact_id,
                    ref_no: ref_no,
                    tipo_doc: tipo_doc,
                    numero_doc: numero_doc,
                    cliente: cliente,
                    direccion: direccion,
                    comprobante_id: comprobante_id,
                    productos: productos,
                    fecha_emision: fecha_emision,
                    tipo_pago: tipo_pago,
                    tipo_detraccion: tipo_detraccion,
                    fecha_pago: fecha_pago,
                    moneda: moneda,
                    _token: '{{ csrf_token() }}' // por seguridad si no usas Sanctum
                },
                success: function (response) {
                    alert("✅ Comprobante generado correctamente: " + response.numero_comprobante);
                    $('#modalServicio').modal('hide');

                    $('#sell_table').DataTable().ajax.reload(null, false);

                    // ✅ abrir nueva pestaña antes del AJAX para evitar bloqueo del navegador
                    let ventana = window.open('', '_blank');

                    let url = '/comprobante/vista/' + response.id_comprobante;
                    ventana.location.href = url;
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert("❌ Error al generar el comprobante.");
                }
            });
        });

    });
</script>

<script>
    $('#btnImprimirFactura').click(function() {
        // Obtener el contenido del div de la factura
        var factura = document.getElementById('contenidoComprobante').innerHTML;

        // Crear un iframe oculto
        var iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.width = '0px';
        iframe.style.height = '0px';
        iframe.style.border = '0';
        document.body.appendChild(iframe);

        var doc = iframe.contentWindow.document;
        doc.open();
        doc.write('<html><head><title>Factura</title>');

        // Incluir estilos
        doc.write('<link rel="stylesheet" href="/css/bootstrap.min.css">');
        doc.write('<style>');
        doc.write(`
            .factura-container { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #000; padding: 10px; background: #fff; }
            .factura-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
            .factura-empresa { width: 65%; }
            .factura-empresa h3 { margin: 0; font-weight: bold; }
            .factura-datos { width: 30%; border: 2px solid #000; text-align: center; padding: 10px; }
            .factura-datos h4 { font-weight: bold; margin-bottom: 5px; }
            .tabla-detalle th, .tabla-detalle td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 12px; }
            .tabla-detalle { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            .factura-totales { text-align: right; margin-top: 10px; }
            .factura-totales p { margin: 2px 0; font-size: 14px; }
            .factura-footer { margin-top: 20px; font-size: 11px; text-align: center; border-top: 1px solid #000; padding-top: 10px; }
        `);
        doc.write('</style>');

        doc.write('</head><body>');
        doc.write(factura);
        doc.write('</body></html>');
        doc.close();

        // Imprimir y luego eliminar el iframe
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            document.body.removeChild(iframe);
        }, 1000);
    });
</script>

<script>
    $(document).ready(function() {

    function validarFechaEmision() {
        let input = $('#fecha_emision');
        let fecha_val = input.val();

        if (!fecha_val) return true;

        let fecha = new Date(fecha_val);
        let min = new Date(input.attr('min'));
        let max = new Date(input.attr('max'));

        if (input.attr('min') && fecha < min || input.attr('max') && fecha > max) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha inválida',
                text: `Debe estar entre ${input.attr('min')} y ${input.attr('max')}`,
            }).then(() => {
                input.val('');
                input.focus();
            });
            return false;
        }
        return true;
    }

    function validarFechaEmisionServicio() {
        let input = $('#fecha_emision_servicio');
        let fecha_val = input.val();

        if (!fecha_val) return true;

        let fecha = new Date(fecha_val);
        let min = new Date(input.attr('min'));
        let max = new Date(input.attr('max'));

        if (input.attr('min') && fecha < min || input.attr('max') && fecha > max) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha inválida',
                text: `Debe estar entre ${input.attr('min')} y ${input.attr('max')}`,
            }).then(() => {
                input.val('');
                input.focus();
            });
            return false;
        }
        return true;
    }

    function validarFechaPago() {
        let tipoPago = $('#tipo_pago').val();
        let fechaPago = new Date($('#fecha_pago').val());
        let fechaEmision = new Date($('#fecha_emision').val());

        if (tipoPago === 'credito') {
            if ($('#fecha_pago').val() && fechaPago < fechaEmision) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fecha incorrecta',
                    text: 'La fecha de pago debe ser igual o mayor a la de emisión.',
                }).then(() => {
                    $('#fecha_pago').val($('#fecha_emision').val()).focus();
                });
                return false;
            }
        }
        return true;
    }

    function validarFechaPagoServicio() {
        let tipoPago = $('#tipo_pago_servicio').val();
        let fechaPago = new Date($('#fecha_pago_servicio').val());
        let fechaEmision = new Date($('#fecha_emision_servicio').val());

        if (tipoPago === 'credito') {
            if ($('#fecha_pago').val() && fechaPago < fechaEmision) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fecha incorrecta',
                    text: 'La fecha de pago debe ser igual o mayor a la de emisión.',
                }).then(() => {
                    $('#fecha_pago_servicio').val($('#fecha_emision_servicio').val()).focus();
                });
                return false;
            }
        }
        return true;
    }

    function actualizarRestriccionesFecha() {
        let comprobante = $('#invoice_scheme_id option:selected').text().toLowerCase();
        let fechaActual = new Date();
        let fechaMin = new Date();

        if (comprobante.includes('factura')) {
            $('#tipodoc').text('RUC:');
            fechaMin.setDate(fechaActual.getDate() - 3);
        } else if (comprobante.includes('boleta')) {
            $('#tipodoc').text('DNI:');
            fechaMin.setDate(fechaActual.getDate() - 7);
        } else {
            fechaMin = null;
        }

        let minFormat = fechaMin ? fechaMin.toISOString().split('T')[0] : "";
        let maxFormat = fechaActual.toISOString().split('T')[0];

        $('#fecha_emision').attr('max', maxFormat);
        $('#fecha_emision').attr('min', minFormat);
    }

    function actualizarRestriccionesFechaServicio() {
        let comprobante = $('#invoice_scheme_id_servicio option:selected').text().toLowerCase();
        let fechaActual = new Date();
        let fechaMin = new Date();

        if (comprobante.includes('factura')) {
            $('#tipodoc_servicio').text('RUC:');
            fechaMin.setDate(fechaActual.getDate() - 3);
        } else if (comprobante.includes('boleta')) {
            $('#tipodoc_servicio').text('DNI:');
            fechaMin.setDate(fechaActual.getDate() - 7);
        } else {
            fechaMin = null;
        }

        let minFormat = fechaMin ? fechaMin.toISOString().split('T')[0] : "";
        let maxFormat = fechaActual.toISOString().split('T')[0];

        $('#fecha_emision_servicio').attr('max', maxFormat);
        $('#fecha_emision_servicio').attr('min', minFormat);
    }

    function actualizarFechaPago() {
        let tipoPago = $('#tipo_pago').val();
        let fechaEmision = $('#fecha_emision').val();

        if (tipoPago === 'contado') {
            $('#grupo_fecha_pago').hide();
            $('#fecha_pago').val('');
            $('#fecha_pago').removeAttr('required');
        } else {
            $('#grupo_fecha_pago').show();
            $('#fecha_pago').attr('required', true);

            if (fechaEmision) {
                $('#fecha_pago').attr('min', fechaEmision);
                if (!$('#fecha_pago').val()) {
                    $('#fecha_pago').val(fechaEmision);
                }
            } else {
                $('#fecha_pago').val('');
                $('#fecha_pago').removeAttr('min');
            }
        }
    }

    function actualizarFechaPagoServicio() {
        let tipoPago = $('#tipo_pago_servicio').val();
        let fechaEmision = $('#fecha_emision_servicio').val();

        if (tipoPago === 'contado') {
            $('#grupo_fecha_pago_servicio').hide();
            $('#fecha_pago_servicio').val('');
            $('#fecha_pago_servicio').removeAttr('required');
        } else {
            $('#grupo_fecha_pago_servicio').show();
            $('#fecha_pago_servicio').attr('required', true);

            if (fechaEmision) {
                $('#fecha_pago_servicio').attr('min', fechaEmision);
                if (!$('#fecha_pago_servicio').val()) {
                    $('#fecha_pago_servicio').val(fechaEmision);
                }
            } else {
                $('#fecha_pago_servicio').val('');
                $('#fecha_pago_servicio').removeAttr('min');
            }
        }
    }

    // Eventos
    $('#fecha_emision').on('change', function() {
        validarFechaEmision();
        actualizarFechaPago();
    });

    $('#fecha_emision_servicio').on('change', function() {
        validarFechaEmisionServicio();
        actualizarFechaPagoServicio();
    });

    $('#fecha_pago').on('change', validarFechaPago);
    $('#invoice_scheme_id').change(actualizarRestriccionesFecha);
    $('#tipo_pago').change(actualizarFechaPago);

    $('#fecha_pago_servicio').on('change', validarFechaPagoServicio);
    $('#invoice_scheme_id_servicio').change(actualizarRestriccionesFechaServicio);
    $('#tipo_pago_servicio').change(actualizarFechaPagoServicio);

    $('#grupo_fecha_pago').hide(); // Inicial oculto si es contado
    $('#grupo_fecha_pago_servicio').hide(); // Inicial oculto si es contado

    actualizarRestriccionesFecha();
    actualizarFechaPago();

    actualizarRestriccionesFechaServicio();
    actualizarFechaPagoServicio();

});
</script>

<!-- nota de credito info  -->
 <script>
    $(document).on('click', '.nota_credito_sunat_button', function () {
        let id = $(this).data('id');

        // Mostrar loading mientras trae la info
        $('#modalNotaCredito .modal-body').html('<p>Cargando información...</p>');
        $('#modalNotaCredito').modal('show');

        $.ajax({
            url: `/comprobantes/${id}/nota-credito-info`,
            type: 'GET',
            success: function (res) {
                if (res.success) {
                    let c = res.data;

                    let html = `
                        <div class="alert alert-info text-center"><h4>Comprobante: ${c.invoice_no}</h4> </div>

                                <div class="form-group">
                                    {!! Form::label('comprobante',  'Comprobante:') !!}
                                    {!! Form::select('invoice_scheme_id_servicio', $invoice_schemes, $default_invoice_schemes->id, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select'), 'id' => 'invoice_scheme_id_nota']); !!}
                                </div>
                                <p><b>Cliente:</b> ${c.name}</p>
                                <p><b>Dirección:</b> ${c.address}</p>
                                <p><b>Fecha emisión:</b> ${c.fecha_emision}</p>
                                <p><b>Total:</b> S/ ${c.total}</p>

                                <div class="form-group mt-2">
                                    <label>Tipo de Nota de Crédito</label>
                                    <select id="tipo_nc" class="form-control">
                                        <option value="1">ANULACIÓN DE LA OPERACIÓN</option>
                                        <option value="2">ANULACIÓN POR ERROR EN EL RUC</option>
                                        <option value="3">CORRECCIÓN POR ERROR EN LA DESCRIPCIÓN</option>
                                        <option value="4">DESCUENTO GLOBAL</option>
                                        <option value="5">DESCUENTO POR ÍTEM</option>
                                        <option value="6">DEVOLUCIÓN TOTAL</option>
                                        <option value="7">DEVOLUCIÓN POR ÍTEM</option>
                                        <option value="8">BONIFICACIÓN</option>
                                        <option value="9">DISMINUCIÓN EN EL VALOR</option>
                                        <option value="10">OTROS CONCEPTOS</option>
                                        <option value="11">AJUSTES AFECTOS AL IVAP</option>
                                        <option value="12">AJUSTES DE OPERACIONES DE EXPORTACIÓN</option>
                                        <option value="13">AJUSTES - MONTOS Y/O FECHAS DE PAGO</option>
                                    </select>
                                </div>
                    `;

                    $('#modalNotaCredito .modal-body').html(html);
                    $('#btnGenerarNC').data('id', c.id);                    
                }
            }
        });
    });

 </script>
 <!-- Generar nota de credito -->
  <script>
    $('#btnGenerarNC').on('click', function () {
        let id = $(this).data('id');
        let tipo = $('#tipo_nc').val();
        let invoice_scheme_id = $('#invoice_scheme_id_nota').val();

        $.ajax({
            url: `/comprobantes/${id}/generar-nota-credito`,
            type: 'POST',
            data: {
                tipo: tipo,
                invoice_scheme_id: invoice_scheme_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.status) {
                    $('#modalNotaCredito').modal('hide');
                    toastr.success(res.message);
                    $('#sell_table').DataTable().ajax.reload(null, false);
                }
                else{
                    toastr.error(res.message);
                }
            }
        });
    });

  </script>

<script>
    $('#btnBuscarMotor').click(function () {

        let motor = $('#motor').val();
        let html = '';

        if (!motor) {
            alert('Ingrese los últimos dígitos del motor');
            return;
        }

        $('#resultadoMotor').html('<p class="text-info">Buscando...</p>');

        $.post("{{ route('consultar.motor') }}", {
            _token: "{{ csrf_token() }}",
            motor: motor
        }, function (data) {

            if (data.length === 0) {
                $('#resultadoMotor').html(
                    '<div class="alert alert-warning">No se encontró información</div>'
                );
                return;
            }

            html += `
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Comprobante</th>
                        <th>Motor</th>
                    </tr>
                </thead>
                <tbody>
            `;

            data.forEach(row => {
                html += `
                    <tr>
                        <td>${row.name}</td>
                        <td>${row.type}</td>
                        <td>${row.invoice_no}</td>
                        <td>${row.motor_completo}</td>
                    </tr>
                `;
            });

            html += '</tbody></table>';

            $('#resultadoMotor').html(html);

        }).fail(function () {
            $('#resultadoMotor').html(
                '<div class="alert alert-danger">Error al consultar</div>'
            );
        });
    });
</script>

<!-- Enviar comporbantes por correo -->

<script>
    $(document).on('click', '.open_email_modal', function () {
        let id = $(this).data('id');
        let email = $(this).data('email');

        $('#comprobante_id').val(id);
        $('#correo_destino').val(email);

        $('#modalEnviarCorreo').modal('show');
    });

    $('#btnEnviarCorreo').on('click', function () {
        let id = $('#comprobante_id').val();
        let correo = $('#correo_destino').val();

        if(!correo){
            alert('Ingrese un correo válido');
            return;
        }

        $.ajax({
            url: '/sunat/enviar-email/' + id,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                correo: correo
            },
            success: function(response){
                if(response.success){
                    alert('Correo enviado correctamente');
                    $('#modalEnviarCorreo').modal('hide');
                } else {
                    alert(response.message);
                }
            }
        });
    });

</script>
@endsection