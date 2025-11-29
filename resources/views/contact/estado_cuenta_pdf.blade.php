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
            margin-bottom: 10px;
            page-break-inside: auto; 
        }

        tr { 
            page-break-inside: avoid; 
            page-break-after: auto; 
        }

        th, td { 
            border: 1px solid #000; 
            padding: 4px; 
            text-align: center; 
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
            margin-bottom: 10px;
        }

        .subtitulo p { 
            margin: 2px 0; 
            font-size: 13px;
        }

        .totales td { 
            font-weight: bold; 
            background: #e8e8e8; 
        }

        .sin-borde td {
            border: none;
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

<br>

{{-- ================== CONTENEDOR DOS COLUMNAS COMPATIBLE DOMPDF ================== --}}
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" valign="top">

{{-- ================= TABLA IZQUIERDA : COMPRAS ================= --}}
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
        @foreach($compras as $c)
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
            <td colspan="5">TOTAL COMPRAS</td>
            <td colspan="2" class="text-right">
                {{ number_format($totales->total_compras, 2) }}
            </td>
        </tr>
    </tbody>
</table>

</td>
<td width="50%" valign="top">

{{-- ================= TABLA DERECHA : PAGOS ================= --}}
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
        @php 
            $saldo = $totales->total_compras; 
        @endphp

        @foreach($pagos as $p)
            @php 
                $saldo -= $p->importe_cancelado; 
            @endphp
            <tr>
                <td>{{ $p->fecha_pago }}</td>
                <td class="text-left">{{ $p->cuenta }}</td>
                <td>{{ $p->nota_pago }}</td>
                <td class="text-right">{{ number_format($p->importe_cancelado, 2) }}</td>
                <td class="text-right">{{ number_format($saldo, 2) }}</td>
            </tr>
        @endforeach

        <tr class="totales">
            <td colspan="3">TOTAL PAGADO</td>
            <td colspan="2" class="text-right">
                {{ number_format($totales->total_pagos, 2) }}
            </td>
        </tr>

        <tr class="totales">
            <td colspan="3">SALDO FINAL</td>
            <td colspan="2" class="text-right">
                {{ number_format($totales->saldo_final, 2) }}
            </td>
        </tr>
    </tbody>
</table>

</td>
</tr>
</table>

<br><br>

{{-- ================== FIRMAS ================== --}}
<table class="sin-borde" style="margin-top: 30px;">
    <tr>
        <td width="50%" class="text-center">
            __________________________
        </td>
        <td width="50%" class="text-center">
            __________________________
        </td>
    </tr>
    <tr>
        <td class="text-center">
            <strong>Firma del Cliente</strong>
        </td>
        <td class="text-center">
            <strong>Firma de la Empresa</strong>
        </td>
    </tr>
</table>

</body>
</html>
