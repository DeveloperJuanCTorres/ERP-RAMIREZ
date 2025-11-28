<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background: #f0f0f0; }

        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .titulo { font-size: 16px; font-weight: bold; text-align: center; }
        .subtitulo { margin-bottom: 10px; }

        .totales td { font-weight: bold; background: #e8e8e8; }
    </style>
</head>
<body>

<div class="titulo pt-4">ESTADO DE CUENTA POR CLIENTE</div>
<div class="subtitulo">
    <p style="font-size: 14px;">Cliente: <strong>{{ $cliente->name }} {{ $cliente->	supplier_business_name }}</strong></p>
    <p style="font-size: 14px;">RUC/DNI: <strong>{{ $cliente->contact_id }}</strong></p>
    <p style="font-size: 14px;">Desde: <strong>{{ $inicio }}</strong> Hasta: <strong>{{ $fin }}</strong></p>
</div>

{{-- ================= TABLA : COMPRAS ================= --}}
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

{{-- ================= TABLA : PAGOS ================= --}}
<table>
    <thead>
        <tr>
            <th colspan="5">PAGOS</th>
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
                <td class="text-right">{{ number_format($p->importe_cancelado, 2) }}</td>
                <td class="text-right">{{ number_format($saldo, 2) }}</td>
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

{{-- ================= FIRMAS ================= --}}
<table style="width: 100%; margin-top: 40px; border: none;">
    <tr>
        <td style="text-align: center; border: none;">
            __________________________
        </td>
    </tr>
    <tr>
        <td style="text-align: center; border: none;">
            <strong>Firma del Cliente</strong>
        </td>
    </tr>
</table>

<table style="width: 100%; margin-top: 20px; border: none;">
    <tr>
        <td style="text-align: center; border: none;">
            __________________________
        </td>
    </tr>
    <tr>
        <td style="text-align: center; border: none;">
            <strong>Firma de la Empresa</strong>
        </td>
    </tr>
</table>

</body>
</html>
