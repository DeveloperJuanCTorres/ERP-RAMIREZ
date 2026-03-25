@extends('layouts.app')
@section('title', __( 'lang_v1.all_sales'))

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <!-- <h1>lang( 'sale.sells') -->
    <h1>Facturación Electrónica
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
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Guías Electrónicas'])
        @can('direct_sell.access')
            @slot('tool')               

                <div class="box-tools">
                    <a class="btn btn-block btn-primary" data-toggle="modal" data-target="#modalGuia">
                    <i class="fa fa-truck"></i> Generar Guía</a>
                </div>
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
                        <th>Correo</th>                                           
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        @endif
    @endcomponent


</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="modalGuia" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">
                    <i class="fa fa-truck"></i> Guía de Remisión Electrónica
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <!-- ================= DATOS PRINCIPALES ================= -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h5 class="box-title">Datos Generales</h5>
                    </div>
                    <div class="box-body">
                        <div class="row">

                            <div class="col-md-4">
                                <label>Ubicación</label>
                                <select id="location_id" class="form-control select2" style="width: 100% !important;">
                                    @foreach($business_locations as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Serie</label>
                                <input type="text" id="serie" class="form-control" readonly>
                            </div>

                            <div class="col-md-3">
                                <label>Fecha Emisión</label>
                                <input type="date" id="fecha_emision" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label>Motivo</label>
                                <select id="motivo_traslado" class="form-control">
                                    <option value="01">Venta</option>
                                    <option value="04">Traslado misma empresa</option>
                                </select>
                            </div>

                        </div>                        
                        <br>

                        <div class="row">

                            <div class="col-md-4">
                                <label>Cliente</label>
                                <select id="contact_id" class="form-control select2" style="width: 100% !important;">
                                    @foreach($customers as $contact)
                                        <option 
                                            value="{{ $contact->id }}"
                                            data-doc="{{ $contact->contact_id }}"
                                            data-address="{{ $contact->address_line_1 }}"
                                        >
                                            {{ $contact->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Tipo Doc</label>
                                <input type="text" id="cliente_tipo_doc" class="form-control" readonly>
                            </div>

                            <div class="col-md-3">
                                <label>N° Documento</label>
                                <input type="text" id="numero_doc" class="form-control" readonly>
                            </div>

                            <div class="col-md-3">
                                <label>Dirección</label>
                                <input type="text" id="direccion" class="form-control">
                            </div>

                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Tipo DE Transporte</label>
                                <select id="tipo_transporte" class="form-control">
                                    <option value="02">02 - TRANSAPORTE PRIVADO</option>
                                    <option value="01">01 - TRANSPORTE PÚBLICO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= DOCUMENTOS ================= -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="box-title mb-0">Documentos Relacionados</h5>

                            <button class="btn btn-primary btn-sm" id="btnAgregarDoc">
                                <i class="fa fa-plus"></i> Agregar Documento
                            </button>
                        </div>
                        
                    </div>

                    <div class="box-body">                      

                        <div id="documentos_relacionados"></div>
                    </div>
                </div>

                <!-- ================= PRODUCTOS ================= -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h5 class="box-title">Productos</h5>
                    </div>

                    <div class="box-body">
                        <button class="btn btn-success btn-sm mb-2" id="btnAgregarItem">
                            <i class="fa fa-plus"></i> Agregar Item
                        </button>

                        <table class="table table-striped table-bordered" id="tablaItems">
                            <thead>
                                <tr>
                                    <th width="25%">Producto</th>
                                    <th width="45%">Descripción</th>
                                    <th width="15%">Cantidad</th>
                                    <th width="15%"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

                <!-- ================= ACORDEÓN ================= -->
                <div class="panel-group" id="accordionGuia">

                    <!-- TRASLADO -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordionGuia" href="#traslado">
                                    DATOS DEL TRASLADO
                                </a>
                            </h4>
                        </div>

                        <div id="traslado" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Fecha Inicio</label>
                                        <input type="date" id="fecha_traslado" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Peso</label>
                                        <input type="number" id="peso" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Unidad</label>
                                        <input type="text" value="KGM" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Bultos</label>
                                        <input type="number" id="bultos" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TRANSPORTISTA -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordionGuia" href="#transportista">
                                    TRANSPORTISTA
                                </a>
                            </h4>
                        </div>

                        <div id="transportista" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>RUC</label>
                                        <input type="text" id="transportista_numero" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nombre</label>
                                        <input type="text" id="transportista_nombre" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Placa</label>
                                        <input type="text" id="placa" class="form-control">
                                    </div>
                                </div>

                                <button class="btn btn-default btn-sm mt-2" id="btnVehiculo">
                                    + Vehículo Secundario
                                </button>

                                <div id="vehiculos_secundarios" class="mt-2"></div>

                            </div>
                        </div>
                    </div>

                    <!-- CONDUCTOR -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordionGuia" href="#conductor">
                                    CONDUCTOR
                                </a>
                            </h4>
                        </div>

                        <div id="conductor" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>DNI</label>
                                        <input type="text" id="conductor_numero" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Nombre</label>
                                        <input type="text" id="conductor_nombre" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Apellidos</label>
                                        <input type="text" id="conductor_apellidos" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Licencia</label>
                                        <input type="text" id="licencia" class="form-control">
                                    </div>
                                </div>

                                <button class="btn btn-default btn-sm mt-2" id="btnConductor">
                                    + Conductor Secundario
                                </button>

                                <div id="conductores_secundarios" class="mt-2"></div>

                            </div>
                        </div>
                    </div>

                    <!-- PARTIDA -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordionGuia" href="#partida">
                                    PUNTO DE PARTIDA
                                </a>
                            </h4>
                        </div>

                        <div id="partida" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Ubigeo</label>
                                        <select id="ubigeo_partida" class="form-control select2" style="width: 100% !important;"></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Dirección</label>
                                        <input type="text" id="direccion_partida" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Código SUNAT</label>
                                        <input type="text" value="0000" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LLEGADA -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordionGuia" href="#llegada">
                                    PUNTO DE LLEGADA
                                </a>
                            </h4>
                        </div>

                        <div id="llegada" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Ubigeo</label>
                                        <select id="ubigeo_llegada" class="form-control select2" style="width: 100% !important;"></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Dirección</label>
                                        <input type="text" id="direccion_llegada" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Código SUNAT</label>
                                        <input type="text" value="0000" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <hr>

                <label>Observaciones</label>
                <textarea id="observaciones" class="form-control"></textarea>

            </div>

            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">
                    Cancelar
                </button>

                <button class="btn btn-success" id="btnGenerarGuia">
                    <i class="fa fa-save"></i> Generar Guía
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
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
</script>

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
                "url": "/guiaSunat",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.is_direct_sale = 1;

                    d.location_id = $('#sell_list_filter_location_id').val();
                    d.contact_id = $('#sell_list_filter_customer_id').val();
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
            scrollX:        false,
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
            ],
        });

        

        $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source',  function() {
            sell_table.ajax.reload();
        });

        $('#only_subscriptions').on('ifChanged', function(event){
            sell_table.ajax.reload();
        });        
        
    });   

</script>

<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>


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
                url: '/generar-guia',
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


<script>

    $('#modalGuia').on('shown.bs.modal', function () {
        $('#location_id').trigger('change');
    });

    

    $('#btnGenerarGuia').click(function () {
        let productos = [];

        $('#tablaItems tbody tr').each(function () {
            let fila = $(this);

            productos.push({
                producto: fila.find('.producto').val(),
                motor: fila.find('td:eq(1)').text(),
                color: fila.find('.color').val(),
                chasis: fila.find('td:eq(3)').text(),
                cantidad: fila.find('td:eq(6)').text()
            });
        });

        if (productos.length === 0) {
            Swal.fire('Error', 'No hay productos', 'error');
            return;
        }

        // VALIDACIÓN BÁSICA
        if (!$('#fecha_traslado').val()) {
            Swal.fire('Error', 'Ingrese fecha de traslado', 'error');
            return;
        }

        Swal.fire({
            title: '¿Generar guía?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, generar'
        }).then((result) => {

            if (!result.isConfirmed) return;

            // 🔥 AQUÍ SOLO PREPARAMOS DATA (luego conectamos backend)
            let data = {
                motivo_traslado: $('#motivo_traslado').val(),
                fecha_traslado: $('#fecha_traslado').val(),
                tipo_transporte: $('#tipo_transporte').val(),

                transportista_numero: $('#transportista_numero').val(),
                transportista_nombre: $('#transportista_nombre').val(),
                placa: $('#placa').val(),

                conductor_numero: $('#conductor_numero').val(),
                conductor_nombre: $('#conductor_nombre').val(),
                conductor_apellidos: $('#conductor_apellidos').val(),
                licencia: $('#licencia').val(),

                direccion_partida: $('#direccion_partida').val(),
                ubigeo_partida: $('#ubigeo_partida').val(),

                direccion_llegada: $('#direccion_llegada').val(),
                ubigeo_llegada: $('#ubigeo_llegada').val(),

                observaciones: $('#observaciones').val(),

                productos: productos
            };

            console.log("DATA GUIA:", data);

            Swal.fire({
                icon: 'success',
                title: 'Datos listos',
                text: 'Ahora conectamos el backend'
            });

        });

    });

    $('#location_id').change(function () {
        let location_id = $(this).val();

        if (!location_id) return;

        $.get('/location-serie/' + location_id, function (data) {
            console.log("SERIE OBTENIDA:", data.serie);

            $('#serie').val(data.serie);

            if (!data.serie) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin serie',
                    text: 'La ubicación no tiene serie configurada'
                });
            }

        });

    });

    $('#contact_id').change(function () {
        let selected = $(this).find(':selected');

        let doc = selected.data('doc');
        doc = String(doc).trim();
        let address = selected.data('address');

        $('#numero_doc').val(doc);
        $('#direccion').val(address);

        if (doc.length == 11) {
            $('#cliente_tipo_doc').val('RUC'); // RUC
        } else if (doc.length == 8) {
            $('#cliente_tipo_doc').val('DNI'); // DNI
        } else {
            $('#cliente_tipo_doc').val('AA');
        }

    });

    $('#btnAgregarItem').click(function () {
        $('#tablaItems tbody').append(`
            <tr>
                <td><input type="text" class="form-control producto"></td>
                <td><input type="text" class="form-control descripcion"></td>
                <td><input type="number" class="form-control cantidad"></td>
                <td><button class="btn btn-danger btn-sm eliminar">X</button></td>
            </tr>
        `);

    });

    $(document).on('click', '.eliminar', function () {
        $(this).closest('tr').remove();
    });

    $('#btnVehiculo').click(function () {
        if ($('.vehiculo_item').length >= 2) {
            Swal.fire('Máximo 2 vehículos');
            return;
        }

        $('#vehiculos_secundarios').append(`
            <div class="vehiculo_item mt-2 d-flex">
                <input type="text" class="form-control" placeholder="Placa secundaria">
                <button class="btn btn-danger btn-sm eliminarVehiculo ml-2">X</button>
            </div>
        `);

    });

    $(document).on('click', '.eliminarVehiculo', function () {
        $(this).closest('.vehiculo_item').remove();
    });

    $('#btnConductor').click(function () {
        if ($('.conductor_item').length >= 2) {
            Swal.fire('Máximo 2 conductores');
            return;
        }

        $('#conductores_secundarios').append(`
            <div class="conductor_item mt-2 border p-2">              

                <div class="row">
                    <div class="col-md-3">
                        <label>DNI</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Nombre</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Apellidos</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Licencia</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <button class="btn btn-danger btn-sm mt-1 eliminarConductor">Eliminar</button>
            </div>
        `);

    });

    $(document).on('click', '.eliminarConductor', function () {
        $(this).closest('.conductor_item').remove();
    });

    $('#btnAgregarDoc').click(function () {
        $('#tablaItems tbody').html('');
        $('#documentos_relacionados').append(`
            <div class="row doc_rel mt-2 align-items-end">

                <div class="col-md-2">
                    <label>Tipo</label>
                    <input type="text" class="form-control tipo_doc_rel" placeholder="01">
                </div>

                <div class="col-md-2">
                    <label>Serie</label>
                    <input type="text" class="form-control serie_doc_rel">
                </div>

                <div class="col-md-3">
                    <label>Número</label>
                    <input type="text" class="form-control numero_doc_rel">
                </div>

                <div class="col-md-3" style="margin-top: 25px;">
                    <button class="btn btn-success btn-sm btnCargarProductos">
                        <i class="fa fa-download"></i> Cargar Productos
                    </button>
                </div>

                <div class="col-md-2" style="margin-top: 25px;">
                    <button class="btn btn-danger btn-sm eliminarDoc">X</button>
                </div>

            </div>
        `);
    });

    $(document).on('click', '.eliminarDoc', function () {
        $(this).closest('.doc_rel').remove();
    });

    $('#btnGenerarGuia').click(function () {
        let productos = [];

        $('#tablaItems tbody tr').each(function () {

            let fila = $(this);

            productos.push({
                producto: fila.find('.producto').val(),
                descripcion: fila.find('.descripcion').val(),
                cantidad: fila.find('.cantidad').val()
            });

        });

        if (productos.length === 0) {
            Swal.fire('Error', 'Debe agregar productos', 'error');
            return;
        }

        if (!$('#fecha_traslado').val()) {
            Swal.fire('Error', 'Ingrese fecha de traslado', 'error');
            return;
        }

        let data = {
            cliente_tipo_doc: $('#cliente_tipo_doc').val(),
            numero_doc: $('#numero_doc').val(),
            cliente: $('#contact_id option:selected').text(),
            direccion: $('#direccion').val(),

            motivo_traslado: $('#motivo_traslado').val(),
            tipo_transporte: $('#tipo_transporte').val(),

            productos: productos
        };

        console.log("DATA GUIA:", data);

        Swal.fire({
            icon: 'success',
            title: 'Datos listos',
            text: 'Siguiente paso: backend'
        });

    });
</script>


<script>
    $(document).on('click', '.btnCargarProductos', function () {
        let fila = $(this).closest('.doc_rel');

        let tipo = fila.find('.tipo_doc_rel').val();
        let serie = fila.find('.serie_doc_rel').val();
        let numero = fila.find('.numero_doc_rel').val();

        if (!tipo || !serie || !numero) {
            Swal.fire('Error', 'Complete tipo, serie y número', 'error');
            return;
        }

        $.get('/comprobante/productos', {
            serie: serie,
            numero: numero
        }, function (res) {

            if (!res.success) {
                Swal.fire('Error', res.message, 'error');
                return;
            }

            let productos = res.data;

            productos.forEach(p => {

                $('#tablaItems tbody').append(`
                    <tr>
                        <td>
                            <input type="text" class="form-control producto" value="${p.codigo}">
                        </td>
                        <td>
                            <input type="text" class="form-control descripcion" value="${p.descripcion}">
                        </td>
                        <td>
                            <input type="number" class="form-control cantidad" value="${p.cantidad}">
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm eliminar">X</button>
                        </td>
                    </tr>
                `);

            });

            Swal.fire({
                icon: 'success',
                title: 'Productos cargados'
            });

        });

    });

    // $('#modalGuia').on('shown.bs.modal', function () {
    //     $('#ubigeo_partida, #ubigeo_llegada').select2('destroy');

    //     $('#ubigeo_partida, #ubigeo_llegada').select2({
    //         dropdownParent: $('#modalGuia'),
    //         placeholder: 'Buscar distrito...',
    //         minimumInputLength: 2,
    //         width: '100%',
    //         allowClear: true,
    //         ajax: {
    //             url: '/ubigeos',
    //             dataType: 'json',
    //             delay: 250,
    //             data: function (params) {
    //                 return { q: params.term };
    //             },
    //             processResults: function (data) {
    //                 return { results: data.results || [] };
    //             }
    //         }
    //     });

    // });

    $('#modalGuia').on('shown.bs.modal', function () {

        // 🔥 Destruir TODOS los select2 dentro del modal
        $('#modalGuia .select2').each(function () {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
        });

        // 🔥 Volver a inicializar correctamente
        $('#modalGuia .select2').select2({
            dropdownParent: $('#modalGuia'),
            width: '100%'
        });

        // 🔥 SOLO los ubigeos con AJAX
        $('#ubigeo_partida, #ubigeo_llegada').select2({
            // dropdownParent: $('#modalGuia'),
            placeholder: 'Buscar distrito...',
            minimumInputLength: 2,
            width: '100%',
            allowClear: true,
            ajax: {
                url: '/ubigeos',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results || [] };
                }
            }
        });

    });


    // $('#modalGuia').on('hidden.bs.modal', function () {

    //     // 🔹 Limpiar inputs
    //     $('#modalGuia').find('input, textarea').val('');

    //     // 🔹 Resetear selects normales
    //     $('#modalGuia').find('select').val(null).trigger('change');

    //     // 🔹 Resetear Select2 correctamente
    //     $('#modalGuia .select2').each(function () {
    //         $(this).val(null).trigger('change');
    //     });

    //     // 🔹 Limpiar tabla de productos
    //     $('#tablaItems tbody').html('');

    //     // 🔹 Limpiar documentos relacionados
    //     $('#documentos_relacionados').html('');

    //     // 🔹 Limpiar vehículos y conductores
    //     $('#vehiculos_secundarios').html('');
    //     $('#conductores_secundarios').html('');

    // });
</script>
@endsection