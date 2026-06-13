@extends('layouts.app')
@section('title', __( 'lang_v1.all_sales'))

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <!-- <h1>lang( 'sale.sells') -->
    <h1>Guías Electrónicas
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
                    <i class="fa fa-truck"></i> Listado de Guías</a>
                </div>
            @endslot
        @endcan
        @if(auth()->user()->can('direct_sell.view') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
        @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
         @endphp
            <table class="table table-bordered table-striped ajax_view" id="guia_table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Serie</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Estado SUNAT</th>
                        <th>Observación</th>
                        <th>PDF</th>
                        <th>XML</th>
                        <th>CDR</th>                                         
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
                                <input type="hidden" id="invoice_scheme_id_guia" name="invoice_scheme_id_guia">
                                <input type="text" id="serie" name="serie" class="form-control" readonly>
                            </div>

                            <div class="col-md-3">
                                <label>Fecha Emisión</label>
                                <input type="date" id="fecha_emision" name="fecha_emision" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label>Motivo de traslado</label>
                                <select id="motivo_traslado" class="form-control">
                                    <option value="01">01 - VENTA</option>
                                    <option value="14">14 - VENTA SUJETA A CONFIRMACION DEL COMPRADOR</option>
                                    <option value="02">02 - COMPRA</option>
                                    <option value="04">04 - TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA</option>
                                    <option value="18">18 - TRASLADO EMISOR ITINERANTE CP</option>
                                    <option value="08">08 - IMPORTACION</option>
                                    <option value="09">09 - EXPORTACION</option>
                                    <option value="13">13 - OTROS</option>
                                    <option value="05">05 - CONSIGNACION</option>
                                    <option value="17">17 - TRASLADO DE BIENES PARA TRANSFORMACION</option>
                                    <option value="03">03 - VENTA CON ENTREGA A TERCEROS</option>
                                    <option value="06">06 - DEVOLUCION</option>
                                    <option value="07">07 - RECOJO DE BIENES TRANSFORMADOS</option>
                                </select>
                            </div>

                        </div>                        
                        <br>

                        <div class="row">

                            <div class="col-md-4">
                                <label>Cliente</label>
                                <select id="contact_id" name="contact_id" class="form-control select2" style="width: 100% !important;">
                                    @foreach($customers as $contact)
                                        <option 
                                            value="{{ $contact->id }}"
                                            data-doc="{{ $contact->contact_id }}"
                                            data-address="{{ $contact->address_line_1 }}"
                                        >
                                            {{ $contact->name }} {{ $contact->supplier_business_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Tipo Doc</label>
                                <input type="text" id="cliente_tipo_doc" name="cliente_tipo_doc" class="form-control" readonly>
                            </div>

                            <div class="col-md-3">
                                <label>N° Documento</label>
                                <input type="text" id="numero_doc" name="numero_doc" class="form-control" readonly>
                            </div>

                            <div class="col-md-3">
                                <label>Dirección</label>
                                <input type="text" id="direccion" name="direccion" class="form-control">
                            </div>

                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Tipo DE Transporte</label>
                                <select id="tipo_transporte" name="tipo_transporte" class="form-control">
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
                                        <input type="date" id="fecha_traslado" name="fecha_traslado" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Peso</label>
                                        <input type="number" id="peso" name="peso" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Unidad</label>
                                        <input type="text" value="KGM" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Bultos</label>
                                        <input type="number" id="bultos" name="bultos" class="form-control">
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
                                    <div class="col-md-2">
                                        <label>Tipo Documento</label>
                                        <select id="conductor_tipo_doc" class="form-control">
                                            <option value="1">DNI</option>
                                            <option value="4">CARNET DE EXTRANJERÍA</option>
                                            <option value="7">PASAPORTE</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Número Documento</label>
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
                                    <div class="col-md-2">
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
                <textarea id="observaciones" name="observaciones" class="form-control"></textarea>

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

@stop

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
</script>


<script src="{{ asset('js/guia_remision.js?v=' . $asset_v) }}"></script>


<script>
    $('#location_id').change(function () {
        let location_id = $(this).val();

        if (!location_id) return;

        $.get('/location-serie/' + location_id, function (data) {
            console.log("SERIE OBTENIDA:", data.serie + data.id);

            $('#serie').val(data.serie);
            $('#invoice_scheme_id_guia').val(data.id);

            if (!data.serie) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin serie',
                    text: 'La ubicación no tiene serie configurada'
                });
            }

        });

    });    
</script>


<script>

    // $('#modalGuia').on('shown.bs.modal', function () {
    //     $('#location_id').trigger('change');
    // });

    

    // $('#btnGenerarGuia').click(function () {
    //     let productos = [];

    //     $('#tablaItems tbody tr').each(function () {
    //         let fila = $(this);

    //         productos.push({
    //             producto: fila.find('.producto').val(),
    //             motor: fila.find('td:eq(1)').text(),
    //             color: fila.find('.color').val(),
    //             chasis: fila.find('td:eq(3)').text(),
    //             cantidad: fila.find('td:eq(6)').text()
    //         });
    //     });

    //     if (productos.length === 0) {
    //         Swal.fire('Error', 'No hay productos', 'error');
    //         return;
    //     }

    //     // VALIDACIÓN BÁSICA
    //     if (!$('#fecha_traslado').val()) {
    //         Swal.fire('Error', 'Ingrese fecha de traslado', 'error');
    //         return;
    //     }

    //     Swal.fire({
    //         title: '¿Generar guía?',
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonText: 'Sí, generar'
    //     }).then((result) => {

    //         if (!result.isConfirmed) return;

    //         // 🔥 AQUÍ SOLO PREPARAMOS DATA (luego conectamos backend)
    //         let data = {
    //             motivo_traslado: $('#motivo_traslado').val(),
    //             fecha_traslado: $('#fecha_traslado').val(),
    //             tipo_transporte: $('#tipo_transporte').val(),

    //             transportista_numero: $('#transportista_numero').val(),
    //             transportista_nombre: $('#transportista_nombre').val(),
    //             placa: $('#placa').val(),

    //             conductor_numero: $('#conductor_numero').val(),
    //             conductor_nombre: $('#conductor_nombre').val(),
    //             conductor_apellidos: $('#conductor_apellidos').val(),
    //             licencia: $('#licencia').val(),

    //             direccion_partida: $('#direccion_partida').val(),
    //             ubigeo_partida: $('#ubigeo_partida').val(),

    //             direccion_llegada: $('#direccion_llegada').val(),
    //             ubigeo_llegada: $('#ubigeo_llegada').val(),

    //             observaciones: $('#observaciones').val(),

    //             productos: productos
    //         };

    //         console.log("DATA GUIA:", data);

    //         Swal.fire({
    //             icon: 'success',
    //             title: 'Datos listos',
    //             text: 'Ahora conectamos el backend'
    //         });

    //     });

    // });

    // $('#location_id').change(function () {
    //     let location_id = $(this).val();

    //     if (!location_id) return;

    //     $.get('/location-serie/' + location_id, function (data) {
    //         console.log("SERIE OBTENIDA:", data.serie);

    //         $('#serie').val(data.serie);

    //         if (!data.serie) {
    //             Swal.fire({
    //                 icon: 'warning',
    //                 title: 'Sin serie',
    //                 text: 'La ubicación no tiene serie configurada'
    //             });
    //         }

    //     });

    // });

    // $('#contact_id').change(function () {
    //     let selected = $(this).find(':selected');

    //     let doc = selected.data('doc');
    //     doc = String(doc).trim();
    //     let address = selected.data('address');

    //     $('#numero_doc').val(doc);
    //     $('#direccion').val(address);

    //     if (doc.length == 11) {
    //         $('#cliente_tipo_doc').val(6); // RUC
    //     } else if (doc.length == 8) {
    //         $('#cliente_tipo_doc').val(1); // DNI
    //     } else {
    //         $('#cliente_tipo_doc').val('AA');
    //     }

    // });

    // $('#btnAgregarItem').click(function () {
    //     $('#tablaItems tbody').append(`
    //         <tr>
    //             <td><input type="text" class="form-control producto"></td>
    //             <td><input type="text" class="form-control descripcion"></td>
    //             <td><input type="number" class="form-control cantidad"></td>
    //             <td><button class="btn btn-danger btn-sm eliminar">X</button></td>
    //         </tr>
    //     `);

    // });

    // $(document).on('click', '.eliminar', function () {
    //     $(this).closest('tr').remove();
    // });

    // $('#btnVehiculo').click(function () {
    //     if ($('.vehiculo_item').length >= 2) {
    //         Swal.fire('Máximo 2 vehículos');
    //         return;
    //     }

    //     $('#vehiculos_secundarios').append(`
    //         <div class="vehiculo_item mt-2 d-flex">
    //             <input type="text" class="form-control" placeholder="Placa secundaria">
    //             <button class="btn btn-danger btn-sm eliminarVehiculo ml-2">X</button>
    //         </div>
    //     `);

    // });

    // $(document).on('click', '.eliminarVehiculo', function () {
    //     $(this).closest('.vehiculo_item').remove();
    // });

    // $('#btnConductor').click(function () {
    //     if ($('.conductor_item').length >= 2) {
    //         Swal.fire('Máximo 2 conductores');
    //         return;
    //     }

    //     $('#conductores_secundarios').append(`
    //         <div class="conductor_item mt-2 border p-2">              

    //             <div class="row">
    //                 <div class="col-md-2">
    //                     <label>Tipo Documento</label>
    //                     <select class="form-control">
    //                         <option value="1">DNI</option>
    //                         <option value="4">CARNET DE EXTRANJERÍA</option>
    //                         <option value="7">PASAPORTE</option>
    //                     </select>
    //                 </div>
    //                 <div class="col-md-2">
    //                     <label>Número Documento</label>
    //                     <input type="text" class="form-control">
    //                 </div>
    //                 <div class="col-md-3">
    //                     <label>Nombre</label>
    //                     <input type="text" class="form-control">
    //                 </div>
    //                 <div class="col-md-3">
    //                     <label>Apellidos</label>
    //                     <input type="text" class="form-control">
    //                 </div>
    //                 <div class="col-md-2">
    //                     <label>Licencia</label>
    //                     <input type="text" class="form-control">
    //                 </div>
    //             </div>

    //             <button class="btn btn-danger btn-sm mt-1 eliminarConductor">Eliminar</button>
    //         </div>
    //     `);

    // });

    // $(document).on('click', '.eliminarConductor', function () {
    //     $(this).closest('.conductor_item').remove();
    // });

    // $('#btnAgregarDoc').click(function () {
    //     $('#tablaItems tbody').html('');
    //     $('#documentos_relacionados').append(`
    //         <div class="row doc_rel mt-2 align-items-end">

    //             <div class="col-md-3">
    //                 <label>Tipo</label>
    //                 <select id="tipo_doc_rel" class="form-control">
    //                     <option value="02">BOLETA ELECTRÓNICA</option>
    //                     <option value="01">FACTURA ELECTRÓNICA</option>
    //                 </select>
    //             </div>

    //             <div class="col-md-2">
    //                 <label>Serie</label>
    //                 <input type="text" class="form-control serie_doc_rel">
    //             </div>

    //             <div class="col-md-3">
    //                 <label>Número</label>
    //                 <input type="text" class="form-control numero_doc_rel">
    //             </div>

    //             <div class="col-md-3" style="margin-top: 25px;">
    //                 <button class="btn btn-success btn-sm btnCargarProductos w-100">
    //                     <i class="fa fa-download"></i> Cargar Productos
    //                 </button>
    //             </div>

    //             <div class="col-md-1" style="margin-top: 25px;">
    //                 <button class="btn btn-danger btn-sm eliminarDoc">X</button>
    //             </div>

    //         </div>
    //     `);
    // });

    // $(document).on('click', '.eliminarDoc', function () {
    //     $(this).closest('.doc_rel').remove();
    // });

    // $('#btnGenerarGuia').click(function () {
    //     let productos = [];

    //     $('#tablaItems tbody tr').each(function () {

    //         let fila = $(this);

    //         productos.push({
    //             producto: fila.find('.producto').val(),
    //             descripcion: fila.find('.descripcion').val(),
    //             cantidad: fila.find('.cantidad').val()
    //         });

    //     });

    //     if (productos.length === 0) {
    //         Swal.fire('Error', 'Debe agregar productos', 'error');
    //         return;
    //     }

    //     if (!$('#fecha_traslado').val()) {
    //         Swal.fire('Error', 'Ingrese fecha de traslado', 'error');
    //         return;
    //     }

    //     let data = {
    //         cliente_tipo_doc: $('#cliente_tipo_doc').val(),
    //         numero_doc: $('#numero_doc').val(),
    //         cliente: $('#contact_id option:selected').text(),
    //         direccion: $('#direccion').val(),

    //         motivo_traslado: $('#motivo_traslado').val(),
    //         tipo_transporte: $('#tipo_transporte').val(),

    //         productos: productos
    //     };

    //     console.log("DATA GUIA:", data);

    //     Swal.fire({
    //         icon: 'success',
    //         title: 'Datos listos',
    //         text: 'Siguiente paso: backend'
    //     });

    // });
</script>


<script>
    // $(document).on('click', '.btnCargarProductos', function () {
    //     let fila = $(this).closest('.doc_rel');

    //     let tipo = fila.find('.tipo_doc_rel').val();
    //     let serie = fila.find('.serie_doc_rel').val();
    //     let numero = fila.find('.numero_doc_rel').val();

    //     if (!tipo || !serie || !numero) {
    //         Swal.fire('Error', 'Complete tipo, serie y número', 'error');
    //         return;
    //     }

    //     $.get('/comprobante/productos', {
    //         serie: serie,
    //         numero: numero
    //     }, function (res) {

    //         if (!res.success) {
    //             Swal.fire('Error', res.message, 'error');
    //             return;
    //         }

    //         let productos = res.data;

    //         productos.forEach(p => {

    //             $('#tablaItems tbody').append(`
    //                 <tr>
    //                     <td>
    //                         <input type="text" class="form-control producto" value="${p.codigo}">
    //                     </td>
    //                     <td>
    //                         <input type="text" class="form-control descripcion" value="${p.descripcion}">
    //                     </td>
    //                     <td>
    //                         <input type="number" class="form-control cantidad" value="${p.cantidad}">
    //                     </td>
    //                     <td>
    //                         <button class="btn btn-danger btn-sm eliminar">X</button>
    //                     </td>
    //                 </tr>
    //             `);

    //         });

    //         Swal.fire({
    //             icon: 'success',
    //             title: 'Productos cargados'
    //         });

    //     });

    // });

  
    // $('#modalGuia').on('shown.bs.modal', function () {

    //     // 🔥 Destruir TODOS los select2 dentro del modal
    //     $('#modalGuia .select2').each(function () {
    //         if ($(this).hasClass("select2-hidden-accessible")) {
    //             $(this).select2('destroy');
    //         }
    //     });

    //     // 🔥 Volver a inicializar correctamente
    //     $('#modalGuia .select2').select2({
    //         dropdownParent: $('#modalGuia'),
    //         width: '100%'
    //     });

    //     // 🔥 SOLO los ubigeos con AJAX
    //     $('#ubigeo_partida, #ubigeo_llegada').select2({
    //         // dropdownParent: $('#modalGuia'),
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

</script>
@endsection