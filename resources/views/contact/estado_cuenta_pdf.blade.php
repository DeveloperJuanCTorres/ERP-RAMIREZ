<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
        }

        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitulo p {
            margin: 2px 0;
            font-size: 13px;
        }

        .totales td {
            font-weight: bold;
            background: #e8e8e8;
        }

        .page-break {
            page-break-after: always;
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

@php
    $filasPorPagina = 20;

    $totalFilas = max(count($compras), count($pagos));

    $compras = array_pad($compras, $totalFilas, null);
    $pagos   = array_pad($pagos, $totalFilas, null);

    $bloques = array_chunk(
        array_map(null, $compras, $pagos),
        $filasPorPagina
    );

    $saldo = $totales->total_compras;
@endphp

@foreach($bloques as $bloque)

<table style="margin-top:10px;">
    <thead>
        <tr>
            <th colspan="6">COMPRAS</th>
            <th colspan="5">PAGOS</th>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Guía</th>
            <th>Motor</th>
            <th>Modelo</th>
            <th>Importe</th>
            <th>Subtotal</th>

            <th>Fecha</th>
            <th>Cuenta</th>
            <th>Nota</th>
            <th>Importe</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>

        @foreach($bloque as [$c,$p])
        <tr>
            {{-- COMPRAS --}}
            <td>{{ $c->fecha ?? '' }}</td>
            <td>{{ $c->guia ?? '' }}</td>
            <td>{{ $c->nro_motor ?? '' }}</td>
            <td class="text-left">{{ $c->modelo ?? '' }}</td>
            <td class="text-right">
                {{ isset($c) ? number_format($c->importe_venta,2) : '' }}
            </td>
            <td class="text-right">
                {{ isset($c) ? number_format($c->subtotal_guia,2) : '' }}
            </td>

            {{-- PAGOS --}}
            @php
                if($p){
                    $saldo -= $p->importe_cancelado;
                }
            @endphp

            <td>{{ $p->fecha_pago ?? '' }}</td>
            <td class="text-left">{{ $p->cuenta ?? '' }}</td>
            <td class="text-left">{{ $p->nota_pago ?? '' }}</td>
            <td class="text-right">
                {{ isset($p) ? number_format($p->importe_cancelado,2) : '' }}
            </td>
            <td class="text-right">
                {{ isset($p) ? number_format($saldo,2) : '' }}
            </td>
        </tr>
        @endforeach

    </tbody>
</table>

<div class="page-break"></div>

@endforeach

{{-- ================= TOTALES FINALES ================= --}}
<table style="margin-top:10px;">
    <tr class="totales">
        <td width="70%">TOTAL COMPRAS</td>
        <td class="text-right">{{ number_format($totales->total_compras, 2) }}</td>
    </tr>
    <tr class="totales">
        <td>TOTAL PAGOS</td>
        <td class="text-right">{{ number_format($totales->total_pagos, 2) }}</td>
    </tr>
    <tr class="totales">
        <td>SALDO FINAL</td>
        <td class="text-right">{{ number_format($totales->saldo_final, 2) }}</td>
    </tr>
</table>

<!-- <br><br> -->

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
