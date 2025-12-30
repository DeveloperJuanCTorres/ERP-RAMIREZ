<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrato de Venta</title>

    <style>
        @page {
            margin: 40px;
        }

        body {
            font-family: DejaVu Sans;
            font-size: 11px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
        }

        .no-border td {
            border: none;
        }

        /* 🔥 CLAVE: rellena la hoja */
        .relleno-pagina {
            height: 180px; /* AJUSTA si tu contenido crece */
        }

        .firmas {
            margin-top: 10px;
        }
    </style>
</head>

<body>

{{-- CABECERA --}}
<table class="no-border">
    <tr>
        <td width="20%">
            @if(!empty($sell->business->logo))
                <!-- <img src="{{ asset('uploads/business_logos/'.$sell->business->logo) }}" width="90"> -->
            @endif
        </td>
        <td class="text-center">
            <strong style="font-size:14px">{{ $sell->business->name }}</strong><br>
            {{ $sell->business->address }}<br>
            Tel: {{ $sell->business->mobile }}
        </td>
    </tr>
</table>

<hr>

<p class="text-center bold" style="font-size:13px">
    CONTRATO DE VENTA N° {{ $sell->invoice_no }} <br>
    Fecha: {{ @format_date($sell->transaction_date) }}
</p>

<hr>

<p class="bold">DATOS DEL CLIENTE</p>
<p>
    Nombre: {{ $sell->contact->name }}<br>
    Documento: {{ $sell->contact->tax_number ?? '--' }}<br>
    Dirección: {!! $sell->contact->contact_address !!}<br>
    Teléfono: {{ $sell->contact->mobile ?? '--' }}
</p>

<p class="bold">DETALLE DE LOS PRODUCTOS</p>

<table>
    <thead>
        <tr>
            <th>Modelo</th>
            <th>Marca</th>
            <th>Motor</th>            
            <th>Color</th>
            <th>Chasis</th>
            <th>Año</th>
            <th>Póliza</th>
            <th>Precio</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sell->sell_lines as $line)
            @php $lot = $line->lot_details; @endphp
            <tr>
                <td>{{ $line->product->name }}</td>
                <td>{{ $line->product->brand->name ?? '--' }}</td>
                <td>{{ $lot->motor ?? '--' }}</td>                
                <td>{{ $lot->color ?? '--' }}</td>
                <td>{{ $lot->chasis ?? '--' }}</td>
                <td>{{ $lot->anio ?? '--' }}</td>
                <td>{{ $lot->poliza ?? '--' }}</td>
                <td class="text-right">{{ number_format($line->unit_price_inc_tax, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="bold">CERTIFICADO DE GARANTÍA</p>
<p style="text-align: justify">
     La empresa se responsabiliza sólo por falla de fábrica del motor (6000 km recorrido) y de la carrocería por 1 año y se
compromete a extraer las piezas dañadas y colocar una original de la misma marca, mas no del daño producido por el mal manejo,
falta de mantenimiento y desgaste por uso. No se acepta cambios ni devoluciones
</p>

{{-- 🔥 RELLENO PARA EMPUJAR AL FINAL --}}
<div class="relleno-pagina"></div>

{{-- FIRMAS SIEMPRE ABAJO --}}
<table class="no-border firmas">
    <tr>
        <td width="50%" class="text-center">
            ___________________________<br>
            <strong>Firma Empresa</strong>
        </td>
        <td width="50%" class="text-center">
            ___________________________<br>
            <strong>Firma Cliente</strong>
        </td>
    </tr>
</table>

</body>
</html>
