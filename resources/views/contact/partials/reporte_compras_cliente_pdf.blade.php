<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compras</title>

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

    {{-- CABECERA --}}
    <div class="titulo">REPORTE DE COMPRAS POR CLIENTE</div>

    <div class="subtitulo">
        <strong>Cliente:</strong> {{ $cliente->name . $cliente->supplier_business_name}} <br>
        <strong>Desde:</strong> {{ $inicio }} &nbsp;&nbsp;
        <strong>Hasta:</strong> {{ $fin }}
    </div>

    <table>
        <thead>
            <tr>
                <!-- <th>Fecha</th> -->
                <th>Factura</th>
                <th>Producto</th>
                <th>Motor</th>
                <th>Guía</th>
                <th>Contenedor</th>
                <th>Cant.</th>
                <th>P. Unit.</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
        @foreach($movimientos as $m)

            {{-- CABECERA DE FACTURA --}}
            @if($m['es_primero'])
                <tr>
                    <td colspan="8" class="sin-borde">
                        <strong>Factura:</strong> {{ $m['factura'] }} &nbsp;&nbsp;
                        <strong>Fecha:</strong> {{ $m['fecha'] }}
                    </td>
                </tr>
            @endif

            {{-- ITEM --}}
            <tr>
                <!-- <td class="text-center">{{ $m['fecha'] }}</td> -->
                <td class="text-center">{{ $m['factura'] }}</td>
                <td>{{ $m['producto'] }}</td>
                <td class="text-center">{{ $m['motor'] }}</td>
                <td class="text-center">{{ $m['guia'] }}</td>
                <td class="text-center">{{ $m['contenedor'] }}</td>
                <td class="text-center">{{ number_format($m['cantidad'], 0) }}</td>
                <td class="text-right">{{ number_format($m['precio_unitario'], 2) }}</td>
                <td class="text-right">{{ number_format($m['total_item'], 2) }}</td>
            </tr>

            {{-- SUBTOTAL --}}
            @if($m['es_ultimo'])
                <tr class="subtotal">
                    <td colspan="7" class="text-right">SUBTOTAL FACTURA</td>
                    <td class="text-right">
                        {{ number_format($m['subtotal'], 2) }}
                    </td>
                </tr>
            @endif

        @endforeach
        </tbody>

        {{-- TOTAL GENERAL --}}
        <tfoot>
            <tr class="total-general">
                <td colspan="7" class="text-right">TOTAL GENERAL</td>
                <td class="text-right">
                    {{ number_format($totalGeneral, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
