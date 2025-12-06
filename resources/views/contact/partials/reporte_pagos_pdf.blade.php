<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<h3 class="text-center">REPORTE DE PAGOS</h3>

<p>
    <strong>Cliente:</strong> {{ $cliente->name . $cliente->supplier_business_name}} <br>
    <strong>Desde:</strong> {{ $inicio }} <br>
    <strong>Hasta:</strong> {{ $fin }}
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha Pago</th>
            <th>Cuenta</th>
            <th>Nota</th>
            <th>Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagos as $i => $p)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td class="text-center">{{ $p->fecha_pago }}</td>
            <td>{{ $p->cuenta }}</td>
            <td>{{ $p->nota_pago }}</td>
            <td class="text-right">{{ number_format($p->importe, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-right">TOTAL PAGOS</th>
            <th class="text-right">{{ number_format($total->total_pagos,2) }}</th>
        </tr>
    </tfoot>
</table>

</body>
</html>
