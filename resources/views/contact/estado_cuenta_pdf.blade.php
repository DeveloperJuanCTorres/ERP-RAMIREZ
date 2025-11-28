<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background: #f0f0f0; }

        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .titulo { font-size: 16px; font-weight: bold; text-align: center; }
        .subtitulo { margin-bottom: 10px; }

        .contenedor {
            width: 100%;
            display: table;
        }
        .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 5px;
        }

        .totales td { font-weight: bold; background: #e8e8e8; }
    </style>
</head>
<body>

<div class="titulo pt-4">ESTADO DE CUENTA POR CLIENTE</div>
<div class="subtitulo">
    <p style="font-size: 14px;">Cliente: <strong>{{ $cliente->name }}</strong></p>
    <p style="font-size: 14px;">RUC/DNI: <strong>{{ $cliente->contact_id }}</strong></p>
    <p style="font-size: 14px;">Desde: <strong>{{ $inicio }}</strong> Hasta: <strong>{{ $fin }}</strong></p>
</div>

<div class="contenedor">

    {{-- ================= TABLA IZQUIERDA : COMPRAS ================= --}}
    <div class="col">
        <table>
            <thead>
                <tr>
                    <th colspan="7">COMPRAS</th>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <th>Guía</th>
                    <th>Motor</th>
                    <th>Item</th>
                    <th>Modelo</th>
                    <th>Importe</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal_general = 0; @endphp
                @foreach($compras as $c)
                    @php $subtotal_general += $c->importe_venta; @endphp
                    <tr>
                        <td>{{ $c->fecha }}</td>
                        <td>{{ $c->guia }}</td>
                        <td>{{ $c->nro_motor }}</td>
                        <td>{{ $c->item }}</td>
                        <td class="text-left">{{ $c->modelo }}</td>
                        <td class="text-right">{{ number_format($c->importe_venta, 2) }}</td>
                        <td class="text-right">{{ number_format($c->subtotal_guia, 2) }}</td>
                    </tr>
                @endforeach

                <tr class="totales">
                    <td colspan="5">TOTAL</td>
                    <td colspan="2" class="text-right">
                        {{ number_format($totales->total_compras, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ================= TABLA DERECHA : PAGOS ================= --}}
    <div class="col">
        <table>
            <thead>
                <tr>
                    <th colspan="6">PAGOS</th>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <th>Cuenta</th>
                    <th>Nota</th>
                    <th>Importe</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>

                @php $saldo = $totales->total_compras; @endphp

                @foreach($pagos as $p)
                    @php $saldo -= $p->importe_cancelado; @endphp
                    <tr>
                        <td>{{ $p->fecha_pago }}</td>
                        <td class="text-left">{{ $p->cuenta }}</td>
                        <td>{{ $p->nota_pago }}</td>
                        <td class="text-right">
                            {{ number_format($p->importe_cancelado, 2) }}
                        </td>
                        <td class="text-right">
                            {{ number_format($saldo, 2) }}
                        </td>
                    </tr>
                @endforeach

                <tr class="totales">
                    <td colspan="3">A CUENTA</td>
                    <td colspan="2" class="text-right">
                        {{ number_format($totales->total_pagos, 2) }}
                    </td>
                </tr>
                <tr class="totales">
                    <td colspan="3">SALDO</td>
                    <td colspan="2" class="text-right">
                        {{ number_format($totales->saldo_final, 2) }}
                    </td>
                </tr>

            </tbody>
        </table>
    </div>    

</div>
    <div style="width: 100%; display: table; margin-top: 40px;">
        <div style="display: table-cell; text-align: center;">
            <p>__________________________</p>
            <p style="font-size: 14px;"><strong>{{ $cliente->name }}</strong></p>
        </div>
        <div style="display: table-cell; text-align: center;">
            <p>__________________________</p>
            <p style="font-size: 14px;"><strong>Importaciones Ramirez</strong></p>
        </div>
    </div>

</body>
</html>
