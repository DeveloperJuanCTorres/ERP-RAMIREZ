<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos - Proveedor</title>

    <style>
        body { 
            font-family: sans-serif; 
            font-size: 12px; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }

        th, td { 
            border: 1px solid #000; 
            padding: 5px; 
        }

        th { 
            background: #f2f2f2; 
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<h3 class="text-center">REPORTE DE PAGOS - PROVEEDOR</h3>

<p>
    <strong>Proveedor:</strong> 
    {{ $proveedor->name }} {{ $proveedor->supplier_business_name ?? '' }} <br>

    <strong>Desde:</strong> {{ $inicio }} <br>
    <strong>Hasta:</strong> {{ $fin }}
</p>

<table>
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="15%">Fecha Pago</th>
            <th width="15%">Monto</th>
            <th width="30%">Cuenta</th>
            <th width="35%">Nota</th>
        </tr>
    </thead>

    <tbody>
        @forelse($pagos as $i => $p)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td class="text-center">{{ $p->fecha_pago }}</td>
            <td class="text-right">{{ number_format($p->importe, 2) }}</td>
            <td>{{ $p->cuenta ?? '-' }}</td>
            <td>{{ $p->observacion ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No se registran pagos en este rango de fechas</td>
        </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <th colspan="2" class="text-right">TOTAL PAGOS</th>
            <th class="text-right">{{ number_format($total, 2) }}</th>
            <th colspan="2"></th>
        </tr>
    </tfoot>
</table>

</body>
</html>