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
        .text-left  { text-align: left; }

        .titulo { font-size: 16px; font-weight: bold; text-align: center; }
        .subtitulo p { margin: 2px 0; font-size: 13px; }

        .totales td { font-weight: bold; background: #e8e8e8; }

        .page-break { page-break-after: always; }

        .nota-una-linea {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
            display: inline-block;
        }

        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

<div class="titulo">ESTADO DE CUENTA POR CLIENTE</div>

<div class="subtitulo">
    <p>Cliente: <strong>{{ $cliente->name }} {{ $cliente->supplier_business_name ?? '' }}</strong></p>
    <p>RUC / DNI: <strong>{{ $cliente->contact_id }}</strong></p>
    <p>Desde: <strong>{{ $inicio }}</strong> - Hasta: <strong>{{ $fin }}</strong></p>
</div>

<br>

{{-- ================= AGRUPAMOS COMPRAS POR TRANSACCIÓN ================= --}}
@php
    $comprasPorVenta = collect($compras)->groupBy('transaction_id');
@endphp

{{-- ================= RECORREMOS CADA COMPRA ================= --}}
@foreach($comprasPorVenta as $transaction_id => $detalleCompra)

@php
    $subtotalVenta = $detalleCompra->first()->subtotal_guia;
    $fechaVenta    = $detalleCompra->first()->fecha;

    $pagosDeVenta = collect($pagos)->where('transaction_id', $transaction_id);
@endphp

{{-- ===== CABECERA DE LA COMPRA ===== --}}
<table style="margin-top:10px;">
    <tr>
        <th width="25%">Fecha</th>
        <th width="25%">Subtotal Venta</th>
        <th width="25%">Total Pagado</th>
        <th width="25%">Saldo</th>
    </tr>
    <tr>
        <td>{{ $fechaVenta }}</td>
        <td class="text-right">{{ number_format($subtotalVenta,2) }}</td>
        <td class="text-right">{{ number_format($pagosDeVenta->sum('importe_cancelado'),2) }}</td>
        <td class="text-right">{{ number_format($subtotalVenta - $pagosDeVenta->sum('importe_cancelado'),2) }}</td>
    </tr>
</table>

<br>

{{-- ================= COMPRAS vs PAGOS ================= --}}
<table width="100%">
<tr>

{{-- ================= COMPRAS (MOTORES) ================= --}}
<td width="50%" valign="top">
<table>
    <thead>
        <tr><th colspan="6">DETALLE DE LA COMPRA</th></tr>
        <tr>
            <th>Fecha</th>
            <th>Invoice</th>
            <th>Motor</th>
            <th>Modelo</th>
            <th>Precio Venta</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detalleCompra as $c)
        <tr>
            <td>{{ $c->fecha }}</td>
            <td>{{ $c->ref_no }}</td>
            <td>{{ $c->nro_motor }}</td>
            <td class="text-left">{{ $c->modelo }}</td>
            <td class="text-right">{{ number_format($c->importe_venta,2) }}</td>
            <td class="text-right">{{ number_format($c->subtotal_guia,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</td>

{{-- ================= PAGOS DE ESA COMPRA ================= --}}
<td width="50%" valign="top">

@if($pagosDeVenta->count())
<table>
    <thead>
        <tr><th colspan="4">PAGOS DE ESTA COMPRA</th></tr>
        <tr>
            <th>Fecha</th>
            <th>Cuenta</th>
            <th>Nota</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagosDeVenta as $p)
        <tr>
            <td>{{ $p->fecha_pago }}</td>
            <td class="text-left">{{ $p->cuenta }}</td>
            <td class="text-left">
                <span class="nota-una-linea" title="{{ $p->nota_pago }}">
                    {{ $p->nota_pago }}
                </span>
            </td>
            <td class="text-right">{{ number_format($p->importe_cancelado,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<table>
    <tr><th>PAGOS</th></tr>
    <tr><td>SIN PAGOS</td></tr>
</table>
@endif

</td>
</tr>
</table>

<br>
<div class="page-break"></div>

@endforeach

{{-- ================= TOTALES FINALES ================= --}}
<table style="margin-top:10px;">
    <tr class="totales">
        <td width="70%">TOTAL COMPRAS</td>
        <td class="text-right">{{ number_format($totales->total_compras,2) }}</td>
    </tr>
    <tr class="totales">
        <td>TOTAL PAGOS</td>
        <td class="text-right">{{ number_format($totales->total_pagos,2) }}</td>
    </tr>
    <tr class="totales">
        <td>SALDO FINAL</td>
        <td class="text-right">{{ number_format($totales->saldo_final,2) }}</td>
    </tr>
</table>

<br><br>

<table style="width:100%; border:none;">
<tr>
    <td style="border:none; text-align:center">__________________________</td>
    <td style="border:none; text-align:center">__________________________</td>
</tr>
<tr>
    <td style="border:none; text-align:center"><strong>Firma del Cliente</strong></td>
    <td style="border:none; text-align:center"><strong>Firma de la Empresa</strong></td>
</tr>
</table>

</body>
</html>
