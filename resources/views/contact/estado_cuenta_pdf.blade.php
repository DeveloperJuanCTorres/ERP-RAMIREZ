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

        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

<div class="titulo">ESTADO DE CUENTA POR CLIENTE</div>

<div class="subtitulo">
    <p>Cliente: <strong>{{ $cliente->name }} {{ $cliente->supplier_business_name ?? '' }}</strong></p>
    <p>Documento: <strong>{{ $cliente->contact_id }}</strong></p>
    <p>Desde: <strong>{{ $inicio }}</strong> - Hasta: <strong>{{ $fin }}</strong></p>
</div>

<br>

@php
    $comprasPorVenta = collect($compras)->groupBy('transaction_id');
@endphp

<table>
    <thead>
        <tr>
            <th colspan="6">COMPRAS</th>
            <th colspan="4">PAGOS</th>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Invoice</th>
            <th>Motor</th>
            <th>Modelo</th>
            <th>Precio</th>
            <th>Subtotal Venta</th>

            <th>Fecha Pago</th>
            <th>Cuenta</th>
            <th>Nota</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>

@foreach($comprasPorVenta as $transaction_id => $detalleCompra)

    @php
        $pagosDeVenta = collect($pagos)->where('transaction_id', $transaction_id)->values();
        $maxFilas = max($detalleCompra->count(), $pagosDeVenta->count());
        $subtotalVenta = $detalleCompra->first()->subtotal_guia;
        $fechaVenta    = $detalleCompra->first()->fecha;
        $invoiceVenta  = $detalleCompra->first()->invoice_no ?? $detalleCompra->first()->guia;
    @endphp

    @for($i = 0; $i < $maxFilas; $i++)

        @php
            $c = $detalleCompra[$i] ?? null;
            $p = $pagosDeVenta[$i] ?? null;
        @endphp

        <tr>

            {{-- ===== FECHA (SOLO 1 VEZ) ===== --}}
            @if($i == 0)
                <td rowspan="{{ $maxFilas }}">{{ $fechaVenta }}</td>
                <td rowspan="{{ $maxFilas }}">{{ $invoiceVenta }}</td>
            @endif

            {{-- ===== MOTORES ===== --}}
            <td>{{ $c->nro_motor ?? '' }}</td>
            <td class="text-left">{{ $c->modelo ?? '' }}</td>
            <td class="text-right">
                {{ isset($c) ? number_format($c->importe_venta,2) : '' }}
            </td>

            {{-- ===== SUBTOTAL (SOLO 1 VEZ) ===== --}}
            @if($i == 0)
                <td rowspan="{{ $maxFilas }}" class="text-right">
                    {{ number_format($subtotalVenta,2) }}
                </td>
            @endif

            {{-- ===== PAGOS ===== --}}
            <td>{{ $p->fecha_pago ?? '' }}</td>
            <td class="text-left">{{ $p->cuenta ?? '' }}</td>
            <td class="text-left">{{ $p->nota_pago ?? '' }}</td>
            <td class="text-right">
                {{ isset($p) ? number_format($p->importe_cancelado,2) : '' }}
            </td>

        </tr>

    @endfor

    {{-- ===== RESUMEN POR VENTA ===== --}}
    <tr class="totales">
        <td colspan="5" class="text-right">TOTAL VENTA</td>
        <td class="text-right">{{ number_format($subtotalVenta,2) }}</td>

        <td colspan="3" class="text-right">TOTAL PAGADO</td>
        <td class="text-right">
            {{ number_format($pagosDeVenta->sum('importe_cancelado'),2) }}
        </td>
    </tr>

    <tr class="totales">
        <td colspan="9" class="text-right">SALDO DE ESTA VENTA</td>
        <td class="text-right">
            {{ number_format($subtotalVenta - $pagosDeVenta->sum('importe_cancelado'),2) }}
        </td>
    </tr>

@endforeach

    </tbody>
</table>

<br>

{{-- ============ TOTALES GENERALES ============ --}}
<table style="margin-top:15px;">
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
