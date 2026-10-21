
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Pagos - Proveedor</title>

<style>
    @page {
        margin: 18px 20px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 9px;
        color: #263443;
    }

    .titulo {
        text-align: center;
        font-size: 15px;
        font-weight: bold;
        color: #344b60;
        margin-bottom: 12px;
    }

    .info {
        background: #f2f6f9;
        padding: 10px;
        margin-bottom: 12px;
        line-height: 1.7;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    th {
        background: #e9f0f5;
        color: #344b60;
        font-size: 8px;
        padding: 7px 4px;
        border: 1px solid #d5e0e8;
    }

    td {
        border: 1px solid #e0e5e9;
        padding: 5px 4px;
        vertical-align: top;
        overflow-wrap: break-word;
    }

    tr {
        page-break-inside: avoid;
    }

    .center { text-align: center; }
    .right { text-align: right; }

    .total {
        background: #e9f0f5;
        font-weight: bold;
    }

    .nota {
        margin-top: 10px;
        color: #9a561c;
        font-size: 8px;
    }
</style>
</head>

<body>

<div class="titulo">
    REPORTE DE PAGOS A PROVEEDORES
</div>

<div class="info">
    <strong>Proveedor:</strong>
    {{ $proveedor->supplier_business_name ?: $proveedor->name }}

    <br>

    <strong>Período:</strong>
    {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }}
    al
    {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }}
</div>

<table>
    <thead>
        <tr>
            <th width="4%">N.º</th>
            <th width="10%">FECHA</th>
            <th width="14%">CUENTA</th>
            <th width="12%">N.º OPERACIÓN</th>
            <th width="10%">MONTO S/</th>
            <th width="8%">T.C.</th>
            <th width="11%">MONTO US$</th>
            <th width="31%">OBSERVACIÓN</th>
    </thead>

    <tbody>
        @forelse($pagos as $i => $p)
        <tr>
            <td class="center">{{ $i + 1 }}</td>

            <td class="center">
                {{ $p->fecha_pago }}
            </td>

            <td>{{ $p->cuenta }}</td>

            <td class="center">
                {{ $p->numero_operacion }}
            </td>

            <td class="right">
                {{ number_format($p->importe_soles, 2) }}
            </td>

            <td class="center">
                {{ $p->tipo_cambio > 0
                    ? number_format($p->tipo_cambio, 3)
                    : '-' }}
            </td>

            <td class="right">
                {{ $p->importe_dolares !== null
                    ? number_format($p->importe_dolares, 2)
                    : '-' }}
            </td>

            <td>
                {{ $p->observacion ?: '-' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="center">
                No se registran pagos en este período.
            </td>
        </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr class="total">
            <td colspan="4" class="right">TOTAL PAGOS</td>

            <td class="right">
                S/ {{ number_format($totalSoles, 2) }}
            </td>

            <td></td>

            <td class="right">
                US$ {{ number_format($totalDolares, 2) }}
            </td>

            <td></td>
        </tr>
    </tfoot>
</table>

@if($pagosSinConversion > 0)
<div class="nota">
    Atención: {{ $pagosSinConversion }}
    pago(s) no tienen importe en soles ni tipo de cambio válido.
    El total en soles mostrado es parcial.
</div>
@endif

</body>
</html>
