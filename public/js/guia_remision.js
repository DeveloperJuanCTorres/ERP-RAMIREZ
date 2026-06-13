//====================================================
// GUIA DE REMISION ELECTRONICA
//====================================================

$(document).ready(function () {

    //-----------------------------------
    // ABRIR MODAL
    //-----------------------------------

    $('#modalGuia').on('shown.bs.modal', function () {

        $('.select2').select2({
            dropdownParent: $('#modalGuia'),
            width: '100%'
        });

        $('#contact_id').trigger('change');
        $('#location_id').trigger('change');

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


    //-----------------------------------
    // OBTENER SERIE SEGUN SUCURSAL
    //-----------------------------------

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
    


    //-----------------------------------
    // CAMBIAR CLIENTE
    //-----------------------------------

    $('#contact_id').change(function(){

        let s=$(this).find(':selected');

        let doc=s.data('doc');

        let direccion=s.data('address');

        $('#numero_doc').val(doc);

        $('#direccion').val(direccion);

        doc=String(doc);

        if(doc.length==11){

            $('#cliente_tipo_doc').val(6);

        }else{

            $('#cliente_tipo_doc').val(1);

        }

    });




    //-----------------------------------
    // AGREGAR PRODUCTO MANUAL
    //-----------------------------------

    $('#btnAgregarItem').click(function(){

        $('#tablaItems tbody').append(`

        <tr>

            <td>

                <input
                class="form-control producto">

            </td>

            <td>

                <input
                class="form-control descripcion">

            </td>

            <td>

                <input
                type="number"
                value="1"
                class="form-control cantidad">

            </td>

            <td>

                <button
                class="btn btn-danger eliminar">

                X

                </button>

            </td>

        </tr>

        `);

    });



    //-----------------------------------
    // ELIMINAR PRODUCTO
    //-----------------------------------

    $(document).on('click','.eliminar',function(){

        $(this).closest('tr').remove();

    });



    //-----------------------------------
    // AGREGAR DOCUMENTO RELACIONADO
    //-----------------------------------

    $('#btnAgregarDoc').click(function(){

        $('#documentos_relacionados').append(`

        <div class="row doc_rel mt-2">

            <div class="col-md-3">

                <select class="form-control tipo_doc_rel">

                    <option value="01">

                    FACTURA

                    </option>

                    <option value="02">

                    BOLETA

                    </option>

                </select>

            </div>

            <div class="col-md-2">

                <input
                class="form-control serie_doc_rel">

            </div>

            <div class="col-md-2">

                <input
                class="form-control numero_doc_rel">

            </div>

            <div class="col-md-3">

                <button
                class="btn btn-success btnCargarProductos">

                Cargar Productos

                </button>

            </div>

            <div class="col-md-2">

                <button
                class="btn btn-danger eliminarDoc">

                X

                </button>

            </div>

        </div>

        `);

    });



    //-----------------------------------
    // ELIMINAR DOCUMENTO
    //-----------------------------------

    $(document).on('click','.eliminarDoc',function(){

        $(this).closest('.doc_rel').remove();

    });




    //-----------------------------------
    // CARGAR PRODUCTOS DEL COMPROBANTE
    //-----------------------------------

    $(document).on('click','.btnCargarProductos',function(){

        let fila=$(this).closest('.doc_rel');

        let serie=fila.find('.serie_doc_rel').val();

        let numero=fila.find('.numero_doc_rel').val();

        $.get('/comprobante/productos',{

            serie:serie,

            numero:numero

        },function(res){

            if(!res.success){

                Swal.fire("No encontrado");

                return;

            }

            res.data.forEach(function(p){

                $('#tablaItems tbody').append(`

                <tr>

                    <td>

                    <input
                    value="${p.codigo}"
                    class="form-control producto">

                    </td>

                    <td>

                    <input
                    value="${p.descripcion}"
                    class="form-control descripcion">

                    </td>

                    <td>

                    <input
                    value="${p.cantidad}"
                    class="form-control cantidad">

                    </td>

                    <td>

                    <button
                    class="btn btn-danger eliminar">

                    X

                    </button>

                    </td>

                </tr>

                `);

            });

        });

    });


    $(document).ready(function(){
        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {

                $('#sell_list_filter_date_range').val(
                    start.format(moment_date_format) +
                    ' ~ ' +
                    end.format(moment_date_format)
                );

                $('#guia_table').DataTable().ajax.reload();
            }
        );

        $('#sell_list_filter_date_range').on(
            'cancel.daterangepicker',
            function() {

                $(this).val('');
                $('#guia_table').DataTable().ajax.reload();

            }
        );

        $('#guia_table').DataTable({

            processing: true,

            serverSide: true,

            aaSorting: [[0, 'desc']],

            ajax: {
                url: '/guiaSunat',
                data: function(d){

                    d.location_id = $('#sell_list_filter_location_id').val();

                    d.contact_id = $('#sell_list_filter_customer_id').val();

                    d.start_date = $('#sell_list_filter_date_range')
                        .data('daterangepicker')
                        .startDate
                        .format('YYYY-MM-DD');

                    d.end_date = $('#sell_list_filter_date_range')
                        .data('daterangepicker')
                        .endDate
                        .format('YYYY-MM-DD');

                }
            },

            columns: [

                {
                    data: 'fecha',
                    name: 'fecha'
                },

                {
                    data: 'serie',
                    name: 'serie'
                },

                {
                    data: 'numero',
                    name: 'numero'
                },

                {
                    data: 'contact_name',
                    name: 'contact_name'
                },

                {
                    data: 'estado_sunat',
                    name: 'estado_sunat',
                    orderable:false,
                    searchable:false
                },

                {
                    data: 'observacion',
                    name: 'observacion'
                },

                {
                    data: 'pdf',
                    name: 'pdf',
                    orderable:false,
                    searchable:false
                },

                {
                    data: 'xml',
                    name: 'xml',
                    orderable:false,
                    searchable:false
                },

                {
                    data: 'cdr',
                    name: 'cdr',
                    orderable:false,
                    searchable:false
                }

            ],

            fnDrawCallback: function(){

            }

        });

    });

});


//====================================================
// VEHICULOS SECUNDARIOS
//====================================================

$('#btnVehiculo').click(function(){

    $('#vehiculos_secundarios').append(`

    <div class="vehiculo_item row mt-2">

        <div class="col-md-10">

            <input
            type="text"
            class="form-control placa_secundaria"
            placeholder="Placa vehículo secundario">

        </div>

        <div class="col-md-2">

            <button
            class="btn btn-danger eliminarVehiculo">

            X

            </button>

        </div>

    </div>

    `);

});



$(document).on('click','.eliminarVehiculo',function(){

    $(this).closest('.vehiculo_item').remove();

});



//====================================================
// CONDUCTORES SECUNDARIOS
//====================================================

$('#btnConductor').click(function(){

    $('#conductores_secundarios').append(`

    <div class="conductor_item border p-2 mt-2">

        <div class="row">

            <div class="col-md-2">

                <select class="form-control tipo_sec">

                    <option value="1">

                    DNI

                    </option>

                    <option value="4">

                    CE

                    </option>

                    <option value="7">

                    PASAPORTE

                    </option>

                </select>

            </div>

            <div class="col-md-2">

                <input
                class="form-control numero_sec">

            </div>

            <div class="col-md-3">

                <input
                class="form-control nombre_sec">

            </div>

            <div class="col-md-3">

                <input
                class="form-control apellido_sec">

            </div>

            <div class="col-md-2">

                <input
                class="form-control licencia_sec">

            </div>

        </div>

        <br>

        <button
        class="btn btn-danger eliminarConductor">

        Eliminar

        </button>

    </div>

    `);

});



$(document).on('click','.eliminarConductor',function(){

    $(this).closest('.conductor_item').remove();

});



//====================================================
// ARMAR ARRAY ITEMS
//====================================================

function obtenerItems(){

    let items=[];

    $('#tablaItems tbody tr').each(function(){

        items.push({

            unidad_de_medida:"NIU",

            codigo:$(this).find('.producto').val(),

            descripcion:$(this).find('.descripcion').val(),

            cantidad:$(this).find('.cantidad').val()

        });

    });

    return items;

}



//====================================================
// ARMAR DOCUMENTOS RELACIONADOS
//====================================================

function obtenerDocumentos(){

    let documentos=[];

    $('.doc_rel').each(function(){

        documentos.push({

            tipo:$(this).find('.tipo_doc_rel').val(),

            serie:$(this).find('.serie_doc_rel').val(),

            numero:$(this).find('.numero_doc_rel').val()

        });

    });

    return documentos;

}



//====================================================
// ARMAR VEHICULOS
//====================================================

function obtenerVehiculos(){

    let vehiculos=[];

    $('.placa_secundaria').each(function(){

        if($(this).val()!=""){

            vehiculos.push({

                placa_numero:$(this).val()

            });

        }

    });

    return vehiculos;

}



//====================================================
// ARMAR CONDUCTORES SECUNDARIOS
//====================================================

function obtenerConductores(){

    let conductores=[];

    $('.conductor_item').each(function(){

        conductores.push({

            documento_tipo:

            $(this).find('.tipo_sec').val(),

            documento_numero:

            $(this).find('.numero_sec').val(),

            nombre:

            $(this).find('.nombre_sec').val(),

            apellidos:

            $(this).find('.apellido_sec').val(),

            numero_licencia:

            $(this).find('.licencia_sec').val()

        });

    });

    return conductores;

}



//====================================================
// GENERAR GUIA ELECTRONICA
//====================================================

$('#btnGenerarGuia').click(function(){

    let items=obtenerItems();

    if(items.length==0){

        Swal.fire({

            icon:'error',

            title:'Debe agregar al menos un producto'

        });

        return;

    }

    let documentos=obtenerDocumentos();

    let vehiculos=obtenerVehiculos();

    let conductores=obtenerConductores();



    let data={

        serie:$('#serie').val(),

        numero:$('#numero').val(),

        tipo_de_comprobante:7,

        operacion:"generar_guia",

        cliente_tipo_de_documento:
        $('#cliente_tipo_doc').val(),

        cliente_numero_de_documento:
        $('#numero_doc').val(),

        cliente_denominacion:
        $('#contact_id option:selected').text(),

        contact_id:
        $('#contact_id').val(),

        location_id:$('#location_id').val(),

        cliente_direccion:
        $('#direccion').val(),

        cliente_email:"",

        cliente_email_1:"",

        cliente_email_2:"",

        fecha_de_emision:
        $('#fecha_emision').val(),

        observaciones:
        $('#observaciones').val(),

        motivo_de_traslado:
        $('#motivo_traslado').val(),

        peso_bruto_total:
        $('#peso').val(),

        peso_bruto_unidad_de_medida:"KGM",

        numero_de_bultos:
        $('#bultos').val(),

        tipo_de_transporte:
        $('#tipo_transporte').val(),

        fecha_de_inicio_de_traslado:
        $('#fecha_traslado').val(),

        fecha_de_entrega_al_transportista:
        $('#fecha_traslado').val(),

        transportista_documento_tipo:"6",

        transportista_documento_numero:
        $('#transportista_numero').val(),

        transportista_denominacion:
        $('#transportista_nombre').val(),

        transportista_placa_numero:
        $('#placa').val(),

        conductor_documento_tipo:
        $('#conductor_tipo_doc').val(),

        conductor_documento_numero:
        $('#conductor_numero').val(),

        conductor_nombre:
        $('#conductor_nombre').val(),

        conductor_apellidos:
        $('#conductor_apellidos').val(),

        conductor_numero_licencia:
        $('#licencia').val(),

        punto_de_partida_ubigeo:
        $('#ubigeo_partida').val(),

        punto_de_partida_direccion:
        $('#direccion_partida').val(),

        punto_de_partida_codigo_establecimiento_sunat:
        "0000",

        punto_de_llegada_ubigeo:
        $('#ubigeo_llegada').val(),

        punto_de_llegada_direccion:
        $('#direccion_llegada').val(),

        punto_de_llegada_codigo_establecimiento_sunat:
        "0000",

        enviar_automaticamente_al_cliente:false,

        formato_de_pdf:"",

        items:items,

        documento_relacionado:documentos,

        vehiculos_secundarios:vehiculos,

        conductores_secundarios:conductores,

        _token:$('meta[name="csrf-token"]').attr('content')

    };



    console.log(data);



    Swal.fire({

        title:'Generando Guía...',

        allowOutsideClick:false,

        didOpen:()=>{

            Swal.showLoading();

        }

    });



    $.ajax({

        url:'/generar-guia',
        type:'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        processData: false,



        success:function(res){



            Swal.close();



            if(res.success){

                Swal.fire({

                    icon:'success',

                    title:'Guía generada correctamente'

                });



                window.open(

                    '/guia/imprimir/'+res.id,

                    '_blank'

                );



                $('#modalGuia').modal('hide');

            }

            else{

                Swal.fire({

                    icon:'error',

                    title:res.message

                });

            }

        },



        error:function(xhr){

            Swal.close();

            console.log(xhr.responseText);

            Swal.fire({

                icon:'error',

                title:'Error al generar la guía'

            });

        }

    });

});

