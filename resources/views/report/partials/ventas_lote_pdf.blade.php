<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Reporte de Ventas por Lote
    </title>

    <style>

        @page {
            margin: 25px 20px;
        }

        body {

            font-family: DejaVu Sans, sans-serif;

            font-size: 9px;

            color: #000;

        }


        .titulo {

            text-align: center;

            font-size: 16px;

            font-weight: bold;

            margin-bottom: 5px;

        }


        .subtitulo {

            text-align: center;

            font-size: 10px;

            margin-bottom: 15px;

        }


        .filtros {

            margin-bottom: 15px;

        }


        .filtros table {

            width: 100%;

            border-collapse: collapse;

        }


        .filtros td {

            padding: 4px;

            border: 1px solid #ddd;

        }


        .filtros .label {

            font-weight: bold;

            background: #f5f5f5;

        }


        table.reporte {

            width: 100%;

            border-collapse: collapse;

        }


        table.reporte th {

            background: #343a40;

            color: white;

            padding: 6px;

            border: 1px solid #222;

            text-align: center;

        }


        table.reporte td {

            padding: 5px;

            border: 1px solid #ccc;

        }


        .text-center {

            text-align: center;

        }


        .text-right {

            text-align: right;

        }


        .total {

            font-weight: bold;

            background: #f5f5f5;

        }


        .resumen {

            margin-bottom: 15px;

        }


        .resumen table {

            width: 100%;

            border-collapse: collapse;

        }


        .resumen td {

            border: 1px solid #ddd;

            padding: 7px;

            text-align: center;

        }


        .resumen .numero {

            font-size: 13px;

            font-weight: bold;

        }

    </style>

</head>


<body>


    <div class="titulo">

        REPORTE DE VENTAS POR LOTE

    </div>


    <div class="subtitulo">

        Productos con número de lote vendidos

    </div>


    {{-- FILTROS --}}

    <div class="filtros">

        <table>

            <tr>

                <td class="label">
                    Fecha desde
                </td>

                <td>
                    {{ \Carbon\Carbon::parse(
                        $fecha_desde
                    )->format('d/m/Y') }}
                </td>

                <td class="label">
                    Fecha hasta
                </td>

                <td>
                    {{ \Carbon\Carbon::parse(
                        $fecha_hasta
                    )->format('d/m/Y') }}
                </td>

            </tr>

        </table>

    </div>


    {{-- RESUMEN --}}

    <div class="resumen">

        <table>

            <tr>

                <td>

                    <div>
                        ITEMS
                    </div>

                    <div class="numero">

                        {{ number_format(
                            $total_items,
                            0
                        ) }}

                    </div>

                </td>


                <td>

                    <div>
                        CANTIDAD
                    </div>

                    <div class="numero">

                        {{ number_format(
                            $total_cantidad,
                            0
                        ) }}

                    </div>

                </td>


                <td>

                    <div>
                        TOTAL VENTA
                    </div>

                    <div class="numero">

                        S/
                        {{ number_format(
                            $total_venta,
                            2
                        ) }}

                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- TABLA --}}

    <table class="reporte">

        <thead>

            <tr>

                <th width="4%">
                    #
                </th>

                <th width="9%">
                    Fecha
                </th>

                <th width="15%">
                    Producto
                </th>

                <th width="14%">
                    Lot Number
                </th>

                <th width="17%">
                    Cliente
                </th>

                <th width="13%">
                    Ubicación
                </th>

                <th width="7%">
                    Cant.
                </th>

                <th width="11%">
                    Precio
                </th>

                <th width="10%">
                    Invoice
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($ventas as $index => $venta)

                <tr>

                    <td class="text-center">

                        {{ $index + 1 }}

                    </td>


                    <td class="text-center">

                        {{ \Carbon\Carbon::parse(
                            $venta->transaction_date
                        )->format('d/m/Y') }}

                    </td>


                    <td>

                        {{ $venta->producto }}

                    </td>


                    <td>

                        <strong>

                            {{ $venta->lot_number }}

                        </strong>

                    </td>


                    <td>

                        {{ $venta->cliente ?? 'Cliente genérico' }}

                    </td>


                    <td>

                        {{ $venta->ubicacion ?? 'Sin ubicación' }}

                    </td>


                    <td class="text-center">

                        {{ number_format(
                            $venta->cantidad,
                            0
                        ) }}

                    </td>


                    <td class="text-right">

                        S/
                        {{ number_format(
                            $venta->unit_price_inc_tax,
                            2
                        ) }}

                    </td>


                    <td class="text-center">

                        {{ $venta->invoice_no }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="9"
                        class="text-center"
                    >

                        No se encontraron ventas
                        de productos con lote.

                    </td>

                </tr>

            @endforelse

        </tbody>


        <tfoot>

            <tr class="total">

                <td
                    colspan="6"
                    class="text-right"
                >

                    TOTAL

                </td>

                <td class="text-center">

                    {{ number_format(
                        $total_cantidad,
                        0
                    ) }}

                </td>

                <td class="text-right">

                    S/
                    {{ number_format(
                        $total_venta,
                        2
                    ) }}

                </td>

                <td></td>

            </tr>

        </tfoot>

    </table>


</body>

</html>