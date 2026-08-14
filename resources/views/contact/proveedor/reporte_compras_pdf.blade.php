<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compras - Proveedor</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
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
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .sin-borde {
            border: none;
        }

        .subtotal {
            font-weight: bold;
            background-color: #fafafa;
        }

        .total-general {
            font-size: 13px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    {{-- =========================================================
         CABECERA
    ========================================================== --}}

    <div class="titulo">
        REPORTE DE COMPRAS POR PROVEEDOR
    </div>

    <div class="subtitulo">
        <strong>Proveedor:</strong>
        {{ $proveedor->name }} {{ $proveedor->supplier_business_name ?? '' }}
        <br>

        <strong>Desde:</strong> {{ $inicio }}
        &nbsp;&nbsp;

        <strong>Hasta:</strong> {{ $fin }}
    </div>


    {{-- =========================================================
         DETALLE DE COMPRAS
    ========================================================== --}}

    <table>

        <thead>
            <tr>
                <th>#</th>
                <th>Factura</th>
                <th>Producto</th>
                <th>Lote</th>
                <th>Guía</th>
                <th>Contenedor</th>
                <th>Cant.</th>
                <th>P. Unit.</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>

        @php
            $item = 1;
        @endphp

        @foreach($movimientos as $m)

            {{-- =================================================
                 CABECERA DE COMPRA
            ================================================== --}}

            @if($m['es_primero'])

                @php
                    $item = 1;
                @endphp

                <tr>
                    <td colspan="9" class="sin-borde">

                        <strong>Compra:</strong>
                        {{ $m['factura'] }}

                        &nbsp;&nbsp;

                        <strong>Fecha:</strong>
                        {{ $m['fecha'] }}

                    </td>
                </tr>

            @endif


            {{-- =================================================
                 ITEM
            ================================================== --}}

            <tr>

                <td class="text-center">
                    {{ $item++ }}
                </td>

                <td class="text-center">
                    {{ $m['factura'] }}
                </td>

                <td>
                    {{ $m['producto'] }}
                </td>

                <td class="text-center">
                    {{ $m['motor'] ?? '' }}
                </td>

                <td class="text-center">
                    {{ $m['guia'] ?? '' }}
                </td>

                <td class="text-center">
                    {{ $m['contenedor'] ?? '' }}
                </td>

                <td class="text-center">
                    {{ number_format($m['cantidad'], 0) }}
                </td>

                <td class="text-right">
                    {{ number_format($m['precio_unitario'], 2) }}
                </td>

                <td class="text-right">
                    {{ number_format($m['total_item'], 2) }}
                </td>

            </tr>


            {{-- =================================================
                 RESUMEN DE LA COMPRA
            ================================================== --}}

            @if($m['es_ultimo'])

                <tr class="subtotal">

                    <td colspan="6" class="text-right">
                        <strong>SUBTOTAL COMPRA:</strong>
                    </td>

                    <td colspan="3" class="text-right">
                        {{ number_format($m['subtotal'], 2) }}
                    </td>

                </tr>

                <tr class="subtotal">

                    <td colspan="6" class="text-right">
                        <strong>PAGADO:</strong>
                    </td>

                    <td colspan="3" class="text-right">
                        {{ number_format($m['pagado'], 2) }}
                    </td>

                </tr>

                <tr class="subtotal">

                    <td colspan="6" class="text-right">
                        <strong>SALDO:</strong>
                    </td>

                    <td colspan="3" class="text-right">
                        {{ number_format($m['saldo'], 2) }}
                    </td>

                </tr>

            @endif

        @endforeach

        </tbody>


        {{-- =========================================================
             TOTALES GENERALES
        ========================================================== --}}

        <tfoot>

            <tr class="total-general">

                <td colspan="6" class="text-right">
                    <strong>TOTAL COMPRAS</strong>
                </td>

                <td colspan="3" class="text-right">
                    {{ number_format($totalGeneral, 2) }}
                </td>

            </tr>


            <tr class="total-general">

                <td colspan="6" class="text-right">
                    <strong>TOTAL PAGADO</strong>
                </td>

                <td colspan="3" class="text-right">
                    {{ number_format($totalPagadoGeneral, 2) }}
                </td>

            </tr>


            <tr class="total-general">

                <td colspan="6" class="text-right">
                    <strong>SALDO GENERAL</strong>
                </td>

                <td colspan="3" class="text-right">
                    {{ number_format($totalSaldoGeneral, 2) }}
                </td>

            </tr>

        </tfoot>

    </table>


    <br>
    <br>


    {{-- =========================================================
         RESUMEN GENERAL POR PRODUCTO
    ========================================================== --}}

    <h3 style="text-align:center;">
        RESUMEN GENERAL
    </h3>


    <table>

        <thead>

            <tr>

                <th>
                    Producto
                </th>

                <th width="120">
                    Cantidad
                </th>

                <th width="150">
                    Monto Total
                </th>

            </tr>

        </thead>


        <tbody>

            @foreach($resumenProductos as $r)

                <tr>

                    <td>
                        {{ $r['producto'] }}
                    </td>

                    <td class="text-center">
                        {{ number_format($r['cantidad'], 0) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($r['monto'], 2) }}
                    </td>

                </tr>

            @endforeach

        </tbody>


        <tfoot>

            <tr class="subtotal">

                <td class="text-right">
                    <strong>TOTALES</strong>
                </td>

                <td class="text-center">
                    <strong>
                        {{ number_format($totalCantidad, 0) }}
                    </strong>
                </td>

                <td class="text-right">
                    <strong>
                        {{ number_format($totalMonto, 2) }}
                    </strong>
                </td>

            </tr>

        </tfoot>

    </table>

</body>
</html>