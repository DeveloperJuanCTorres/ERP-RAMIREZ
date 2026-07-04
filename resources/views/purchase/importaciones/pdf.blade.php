<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:11px;
            color:#222;
        }

        h2{
            text-align:center;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .cabecera{
            margin-bottom:20px;
        }

        .cabecera th{
            width:18%;
            background:#efefef;
            border:1px solid #999;
            text-align:left;
            padding:6px;
        }

        .cabecera td{
            border:1px solid #999;
            padding:6px;
        }

        .detalle th{

            background:#1F4E78;
            color:white;
            border:1px solid #666;
            padding:6px;

        }

        .detalle td{

            border:1px solid #999;
            padding:5px;

        }

        .titulo{

            font-size:18px;
            font-weight:bold;
            text-align:center;
            margin-bottom:20px;

        }

    </style>

</head>

<body>

    <div class="titulo">

        HISTORIAL DE IMPORTACIÓN

    </div>

    <table class="cabecera">

        <tr>

            <th>Transacción</th>
            <td>{{ $cabecera->transaction_id }}</td>

            <th>Empresa</th>
            <td>{{ $cabecera->business_name }}</td>

        </tr>

        <tr>

            <th>Fecha</th>
            <td>{{ \Carbon\Carbon::parse($cabecera->transaction_date)->format('d/m/Y') }}</td>

            <th>Tipo</th>
            <td>{{ strtoupper($cabecera->type) }}</td>

        </tr>

        <tr>

            <th>Estado</th>
            <td>{{ strtoupper($cabecera->status) }}</td>

            <th>Cantidad</th>
            <td>{{ count($datos) }} unidades</td>

        </tr>

    </table>

    <table class="detalle">

        <thead>

            <tr>

                <th>Producto</th>
                <th>Motor</th>
                <th>Chasis</th>
                <th>Color</th>
                <th>Año</th>
                <th>Póliza</th>
                <th>Guía</th>
                <th>Contenedor</th>

            </tr>

        </thead>

        <tbody>

            @foreach($datos as $item)

                <tr>

                    <td>{{ $item->producto }}</td>
                    <td>{{ $item->motor }}</td>
                    <td>{{ $item->chasis }}</td>
                    <td>{{ $item->color }}</td>
                    <td style="text-align:center">{{ $item->anio }}</td>
                    <td>{{ $item->poliza }}</td>
                    <td>{{ $item->guia }}</td>
                    <td style="text-align:center">{{ $item->contenedor }}</td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>