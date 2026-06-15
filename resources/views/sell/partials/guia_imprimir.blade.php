<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Guía de Remisión Electrónica</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            padding: 20px;

        }

        table {

            width: 100%;
            border-collapse: collapse;

        }

        td {

            vertical-align: top;

        }

        .header-right {

            border: 2px solid #000;
            width: 260px;

        }

        .header-right .ruc {

            text-align: center;
            padding: 8px;
            font-size: 18px;
            font-weight: bold;

        }

        .header-right .titulo {

            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            text-align: center;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;

        }

        .header-right .numero {

            text-align: center;
            padding: 10px;
            font-size: 22px;
            font-weight: bold;

        }

        .linea {

            margin-top: 8px;
            margin-bottom: 8px;
            border-top: 1px solid #555;

        }

        .info td {

            padding: 2px 0;

        }

        .label {

            font-weight: bold;
            width: 95px;

        }

        .seccion {

            margin-top: 10px;

        }

        .seccion h4 {

            background: #efefef;
            padding: 4px;
            border: 1px solid #ccc;
            margin-bottom: 5px;

        }

        .no-print {

            margin-bottom: 15px;

        }

        @media print {

            .no-print {

                display: none;

            }

            body {

                padding: 5mm;

            }

        }
    </style>

</head>

<body>

    <table>

        <tr>

            <td width="65%">

                 @php
                    $business_id = session('user.business_id');

                    if($business_id == 1 || $business_id == 7){
                        $imgPath = public_path('img/facturacion1-bg.png');
                        $alt = "imp-ramirez";
                    }else
                    {
                        $imgPath = public_path('img/a1ramirez-facturacion.png');
                        $alt = "a1-ramirez";
                    }

                    $imgData = base64_encode(file_get_contents($imgPath));
                    $imgType = pathinfo($imgPath, PATHINFO_EXTENSION);
                @endphp

                @if($business_id == 1 || $business_id == 7)
                    <img src="data:image/{{ $imgType }};base64,{{ $imgData }}" alt="{{$alt}}" style="width:100%;height:180px;object-fit:contain;">
                @else
                    <img src="data:image/{{ $imgType }};base64,{{ $imgData }}" alt="{{$alt}}" style="width:100%;height:180px;object-fit:contain;">
                @endif

             

            </td>

            <td width="35%">

                <div class="header-right">

                    <div class="ruc">

                        RUC {{ session('business.ruc') }}

                    </div>

                    <div class="titulo">

                        GUÍA DE REMISIÓN

                        ELECTRÓNICA

                    </div>

                    <div class="numero">

                        {{ $guia->serie }}

                        -

                        {{ str_pad($guia->numero,6,"0",STR_PAD_LEFT) }}

                    </div>

                </div>

            </td>

        </tr>

    </table>

    <div class="linea"></div>

    <table class="info">

        <tr>

            <td class="label">

                FECHA

            </td>

            <td>

                :

                {{ $envio["fecha_de_emision"] ?? '' }}

            </td>

            <td class="label">

                MOTIVO

            </td>

           
            <td>

                :
                @if($envio["motivo_de_traslado"] == 01)
                    VENTA
                @endif

                @if($envio["motivo_de_traslado"] == 04)
                    TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA
                @endif

            </td>

        </tr>

        <tr>

            <td class="label">

                CLIENTE

            </td>

            <td>

                :

                {{ $envio["cliente_denominacion"] ?? '' }}

            </td>

            <td class="label">

                RUC/DNI

            </td>

            <td>

                :

                {{ $envio["cliente_numero_de_documento"] ?? '' }}

            </td>

        </tr>

        <tr>

            <td class="label">

                DIRECCIÓN

            </td>

            <td colspan="3">

                :

                {{ $envio["cliente_direccion"] ?? '' }}

            </td>

        </tr>

    </table>

    <div class="linea"></div>

    <table>

        <tr>

            <td width="50%">

                <div class="seccion">

                    <h4>

                        PUNTO DEPARTIDA

                    </h4>

                    <table class="info">

                        <tr>

                            <td class="label">

                                UBIGEO

                            </td>

                            <td>

                                :

                                {{ $envio["punto_de_partida_ubigeo"] ?? '' }}

                            </td>

                        </tr>

                        <tr>

                            <td class="label">

                                DIRECCIÓN

                            </td>

                            <td>

                                :

                                {{ $envio["punto_de_partida_direccion"] ?? '' }}

                            </td>

                        </tr>

                    </table>

                </div>

            </td>

            <td width="50%">

                <div class="seccion">

                    <h4>

                        PUNTO DE LLEGADA

                    </h4>

                    <table class="info">

                        <tr>

                            <td class="label">

                                UBIGEO

                            </td>

                            <td>

                                :

                                {{ $envio["punto_de_llegada_ubigeo"] ?? '' }}

                            </td>

                        </tr>

                        <tr>

                            <td class="label">

                                DIRECCIÓN

                            </td>

                            <td>

                                :

                                {{ $envio["punto_de_llegada_direccion"] ?? '' }}

                            </td>

                        </tr>

                    </table>

                </div>

            </td>

        </tr>

    </table>

    <div class="linea"></div>

    <table>

        <tr>

            <td width="50%">

                <strong>

                    TRANSPORTISTA

                </strong>

                <br><br>

                RUC:

                {{ $envio["transportista_documento_numero"] ?? '' }}

                <br>

                {{ $envio["transportista_denominacion"] ?? '' }}

                <br>

                PLACA:

                {{ $envio["transportista_placa_numero"] ?? '' }}

            </td>

            <td width="50%">

                <strong>

                    CHOFER

                </strong>

                <br><br>

                DNI:

                {{ $envio["conductor_documento_numero"] ?? '' }}

                <br>

                {{ ($envio["conductor_nombre"] ?? '').' '.($envio["conductor_apellidos"] ?? '') }}

                <br>

                LICENCIA:

                {{ $envio["conductor_numero_licencia"] ?? '' }}

            </td>

        </tr>

    </table>

    <div class="linea"></div>

    <!-- ==============================
DETALLE DE BIENES TRASLADADOS
============================== -->

    <h4 style="margin-bottom:5px;">

        DETALLE DE LOS BIENES TRANSPORTADOS

    </h4>

    <table
        style="
width:100%;
border-collapse:collapse;
font-size:11px;
">

        <thead>

            <tr
                style="
border-top:1px solid #000;
border-bottom:1px solid #000;
">

                <th
                    style="
text-align:left;
padding:5px;
width:15%;
">

                    CÓDIGO

                </th>

                <th
                    style="
text-align:left;
padding:5px;
">

                    DESCRIPCIÓN

                </th>

                <th
                    style="
text-align:center;
padding:5px;
width:10%;
">

                    UM

                </th>

                <th
                    style="
text-align:center;
padding:5px;
width:10%;
">

                    CANT.

                </th>

            </tr>

        </thead>

        <tbody>

            @foreach(($envio["items"] ?? []) as $item)

            <tr
                style="
border-bottom:1px solid #ddd;
">

                <td
                    style="
padding:6px;
">

                    {{ $item["codigo"] ?? '' }}

                </td>

                <td
                    style="
padding:6px;
">

                    {{ $item["descripcion"] ?? '' }}

                </td>

                <td
                    style="
padding:6px;
text-align:center;
">

                    {{ $item["unidad_de_medida"] ?? '' }}

                </td>

                <td
                    style="
padding:6px;
text-align:center;
">

                    {{ $item["cantidad"] ?? '' }}

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>



    <br>



    @if(!empty($envio["documento_relacionado"]))

    <h4 style="margin-bottom:5px;">

        DOCUMENTOS RELACIONADOS

    </h4>

    <table
        style="
width:100%;
border-collapse:collapse;
font-size:11px;
">

        <thead>

            <tr
                style="
border-top:1px solid #000;
border-bottom:1px solid #000;
">

                <th
                    style="
padding:5px;
text-align:left;
width:20%;
">

                    TIPO

                </th>

                <th
                    style="
padding:5px;
text-align:left;
width:20%;
">

                    SERIE

                </th>

                <th
                    style="
padding:5px;
text-align:left;
">

                    NÚMERO

                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($envio["documento_relacionado"] as $doc)

            <tr>

                <td style="padding:5px;">

                    {{ $doc["tipo"] ?? '' }}

                </td>

                <td style="padding:5px;">

                    {{ $doc["serie"] ?? '' }}

                </td>

                <td style="padding:5px;">

                    {{ $doc["numero"] ?? '' }}

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    @endif



    <br>

    <div
        style="
border-top:1px solid #000;
padding-top:8px;
">

        <strong>

            OBSERVACIONES

        </strong>

        <br><br>

        {{ $envio["observaciones"] ?? '' }}

    </div>

    <br>

    @if(!empty($guia->sunat_description))

    <div
        style="
margin-top:15px;
border-top:1px solid #999;
padding-top:8px;
font-size:10px;
">

        <strong>

            OBSERVACIÓN SUNAT

        </strong>

        <br>

        {{ $guia->sunat_description }}

    </div>

    @endif



    @if(!empty($envio["vehiculos_secundarios"]))

    <br>

    <strong>

        VEHÍCULOS SECUNDARIOS

    </strong>

    <br><br>

    <table
        style="
width:100%;
border-collapse:collapse;
font-size:11px;
">

        <thead>

            <tr
                style="
border-top:1px solid #000;
border-bottom:1px solid #000;
">

                <th
                    style="
text-align:left;
padding:4px;
width:10%;
">

                    #

                </th>

                <th
                    style="
text-align:left;
padding:4px;
">

                    PLACA

                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($envio["vehiculos_secundarios"] as $i=>$vehiculo)

            <tr>

                <td style="padding:4px;">

                    {{ $i+1 }}

                </td>

                <td style="padding:4px;">

                    {{ $vehiculo["placa_numero"] ?? '' }}

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    @endif




    @if(!empty($envio["conductores_secundarios"]))

    <br>

    <strong>

        CONDUCTORES SECUNDARIOS

    </strong>

    <br><br>

    <table
        style="
width:100%;
border-collapse:collapse;
font-size:11px;
">

        <thead>

            <tr
                style="
border-top:1px solid #000;
border-bottom:1px solid #000;
">

                <th style="padding:4px;">

                    DOCUMENTO

                </th>

                <th style="padding:4px;">

                    NOMBRE

                </th>

                <th style="padding:4px;">

                    LICENCIA

                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($envio["conductores_secundarios"] as $conductor)

            <tr>

                <td style="padding:4px;">

                    {{ $conductor["documento_numero"] ?? '' }}

                </td>

                <td style="padding:4px;">

                    {{ ($conductor["nombre"] ?? '').' '.($conductor["apellidos"] ?? '') }}

                </td>

                <td style="padding:4px;">

                    {{ $conductor["numero_licencia"] ?? '' }}

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    @endif



    <br><br><br>

    <!-- <table style="width:100%;">

        <tr>

            <td width="50%" style="text-align:center;">

                __________________________________

                <br>

                REMITENTE

            </td>

            <td width="50%" style="text-align:center;">

                __________________________________

                <br>

                TRANSPORTISTA

            </td>

        </tr>

    </table> -->



    <br><br>

    <div
        style="
border-top:1px solid #999;
padding-top:8px;
font-size:10px;
text-align:center;
">

        Representación impresa de la GUIA DE REMISIÓN REMITENTE ELECTRÓNICA, para ver el documento
visita https://grupovesergenperu.pse.pe/20495764398
Emitido mediante un PROVEEDOR Autorizado por la SUNAT mediante Resolución de Intendencia
No.034-005-0005315


        <br>

        {{ session('business.name') }}

        @if(!empty(session('business.ruc')))
        - RUC {{ session('business.ruc') }}
        @endif

        <br>

        @if($guia->aceptada)

        <!-- <strong>

            ACEPTADA POR SUNAT

        </strong> -->

        @else

        <!-- <strong>

            PENDIENTE DE RESPUESTA DE SUNAT

        </strong> -->

        @endif

        <br>

        @if(!empty($guia->hash))

        Hash:

        {{ $guia->hash }}

        @endif

    </div>

</body>

</html>

<script>
    window.onload = () => {
        window.print();
        setTimeout(() => window.close(), 800); // ✅ Se cierra sola luego de imprimir
    };
</script>