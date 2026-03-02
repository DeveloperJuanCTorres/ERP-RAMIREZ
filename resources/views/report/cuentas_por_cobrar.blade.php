<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Cuentas por Cobrar</title>

    <style>
        @page {
            margin: 50px;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .banner {
            width: 100%;
            height: auto;
            display: block;
        }

        .content {
            padding: 25px;
        }

        .filters {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 6px;
        }

        table th {
            border: 1px solid #000;
            padding: 6px;
            background: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .footer-total {
            font-weight: bold;
        }

        .header h2 {
            text-align: center;
            margin-top: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="no-print" style="margin-bottom:15px;">
    <button onclick="window.print()">🖨 Imprimir</button>
</div>

<!-- CABECERA -->
<div class="header">
    <img class="banner" src="{{ asset('img/facturacion-bg.jpeg') }}" alt="Logo">
    <h2>REPORTE DE CUENTAS POR COBRAR</h2>
    <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p>
</div>

<!-- FILTROS -->
<div class="filters">
    <strong>Filtros Aplicados:</strong><br>

    Cliente:
    @if($clienteId)
        {{ \App\Contact::find($clienteId)->name ?? '-' }}
    @else
        Todos
    @endif
    <br>

    Fecha Inicio:
    {{ $fechaInicio ?? 'No especificado' }} <br>

    Fecha Fin:
    {{ $fechaFin ?? 'No especificado' }}
</div>

<!-- TABLA -->
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Total Compras</th>
            <th>Total Pagos</th>
            <th>Saldo</th>
            <th>Último Pago</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalCompras = 0;
            $totalPagos = 0;
            $totalSaldo = 0;
        @endphp

        @forelse($data as $row)
            @php
                $totalCompras += $row->total_compras;
                $totalPagos += $row->total_pagos;
                $totalSaldo += $row->saldo;
            @endphp
            <tr>
                <td>{{ $row->cliente }}</td>
                <td class="text-right">{{ number_format($row->total_compras, 2) }}</td>
                <td class="text-right">{{ number_format($row->total_pagos, 2) }}</td>
                <td class="text-right">{{ number_format($row->saldo, 2) }}</td>
                <td>
                    {{ $row->ultimo_pago 
                        ? \Carbon\Carbon::parse($row->ultimo_pago)->format('d/m/Y')
                        : '-' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" align="center">No se encontraron registros</td>
            </tr>
        @endforelse

        <tr class="footer-total">
            <td>TOTAL GENERAL</td>
            <td class="text-right">{{ number_format($totalCompras, 2) }}</td>
            <td class="text-right">{{ number_format($totalPagos, 2) }}</td>
            <td class="text-right">{{ number_format($totalSaldo, 2) }}</td>
            <td></td>
        </tr>

    </tbody>
</table>

</body>
</html>