<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
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

        /* ================= SOLUCIÓN DEFINITIVA ================= */

        /* Altura fija que acepta MÁXIMO 2 líneas */
        tbody tr {
            height: 22px;
        }

        .celda-dos-lineas {
            height: 22px;
            line-height: 11px;     /* 2 líneas exactas */
            overflow: hidden;
            word-wrap: break-word;
            white-space: normal;
        }

    </style>
</head>
<body>

{{-- ================== ENCABEZADO ================== --}}
<div class="titulo">ESTADO DE CUENTA POR CLIENTE</div>

<div class="subtitulo">
    <p>Cliente: <strong>{{ $cliente->name }} {{ $cliente->supplier_business_name ?? '' }}</strong></p>
    <p>RUC / DNI: <strong>{{ $cliente->contact_id }}</strong></p>
    <p>Desde: <strong>{{ $inicio }}</strong> - Hasta: <strong>{{ $fin }}</strong></p>
</div>

@php
    $filasPorPagina = 20;

    $comprasChunks = array_chunk($compras, $filasPorPagina);
    $pagosChunks   = array_chunk($pagos, $filasPorPagina);

    $maxPaginas = max(count($comprasChunks), count($pagosChunks));
@endphp

{{-- ================== CICLO DE PAGINAS ================== --}}
@for($pagina = 0; $pagina < $maxPaginas; $pagina++)

<table width="100%" style="margin-top:10px;">
<tr>
<td width="50%" valign="top">

{{-- ================= COMPRAS ================= --}}
<table>
    <thead>
        <tr><th colspan="6">COMPRAS</th></tr>
        <tr>
            <th>Fecha</th>
            <th>Guía</th>
            <th>Motor</th>
            <th>Modelo</th>
            <th>Importe</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($comprasChunks[$pagina] ?? [] as $c)
        <tr>
            <td class="celda-dos-lineas">{{ $c->fecha }}</td>
            <td class="celda-dos-lineas">{{ $c->guia }}</td>
            <td class="celda-dos-lineas">{{ $c->nro_motor }}</td>
            <td class="celda-dos-lineas text-left">{{ $c->modelo }}</td>
            <td class="celda-dos-lineas text-right">{{ number_format($c->importe_venta, 2) }}</td>
            <td class="celda-dos-lineas text-right">{{ number_format($c->subtotal_guia, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</td>
<td width="50%" valign="top">

{{-- ================= PAGOS ================= --}}
@if(!empty($pagosChunks[$pagina]))
<table>
    <thead>
        <tr><th colspan="5">PAGOS</th></tr>
        <tr>
            <th>Fecha</th>
            <th>Cuenta</th>
            <th>Nota</th>
            <th>Importe</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>

        @php
            if ($pagina == 0) {
                $saldo = $totales->total_compras;
            }
        @endphp

        @foreach($pagosChunks[$pagina] as $p)
            @php $saldo -= $p->importe_cancelado; @endphp
            <tr>
                <td class="celda-dos-lineas">{{ $p->fecha_pago }}</td>
                <td class="celda-dos-lineas text-left">
                    {{ str_replace(["\r","\n","\t"], ' ', $p->cuenta) }}
                </td>
                <td class="celda-dos-lineas text-left">
                    {{ str_replace(["\r","\n","\t"], ' ', $p->nota_pago) }}
                </td>
                <td class="celda-dos-lineas text-right">{{ number_format($p->importe_cancelado, 2) }}</td>
                <td class="celda-dos-lineas text-right">{{ number_format($saldo, 2) }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
@endif

</td>
</tr>
</table>

@if($pagina < $maxPaginas - 1)
<div class="page-break"></div>
@endif

@endfor

{{-- ================= TOTALES FINALES ================= --}}
<table style="margin-top:15px;">
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

<br><br>

{{-- ================= FIRMAS ================= --}}
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
