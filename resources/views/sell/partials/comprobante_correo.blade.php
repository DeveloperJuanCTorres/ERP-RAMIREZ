<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 11px;
        color: #000;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    .bordered {
        border: 1px solid #000;
        padding: 8px;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .tabla-detalle th, 
    .tabla-detalle td {
        border: 1px solid #000;
        padding: 4px;
        font-size: 11px;
    }

    .tabla-detalle th {
        background: #f2f2f2;
    }

    .totales td {
        padding: 3px;
        font-size: 12px;
    }

</style>
</head>
<body>

@php
    if($comprobante->business_id == 1 || $comprobante->business_id == 7){
        $imgPath = public_path('img/importaciones.jpeg');
        $empresa = "IMPORTACIONES RAMIREZ E.I.R.L.";
        $ruc = "20495764398";
    } else {
        $imgPath = public_path('img/a1ramirez.jpeg');
        $empresa = "A1 RAMIREZ SAC";
        $ruc = "20603437331";
    }

    $imgData = base64_encode(file_get_contents($imgPath));
    $imgType = pathinfo($imgPath, PATHINFO_EXTENSION);

    $tipo_doc = Str::startsWith($comprobante->invoice_no, 'F') ? 'RUC' : 'DNI';
@endphp

{{-- ================= HEADER ================= --}}
<table style="border-bottom:2px solid #000; margin-bottom:15px;">
    <tr>
        <td width="65%" valign="top">
            <img src="data:image/{{ $imgType }};base64,{{ $imgData }}" width="200">
            <h3 style="margin:5px 0;">{{ $empresa }}</h3>
            <p style="margin:2px 0;">RUC: {{ $ruc }}</p>
        </td>

        <td width="35%" valign="top">
            <table class="bordered">
                <tr>
                    <td class="text-center">
                        <strong>{{ $comprobante->type }}</strong><br>
                        {{ $comprobante->invoice_no }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ================= CLIENTE ================= --}}
<table style="margin-bottom:15px;">
    <tr>
        <td width="70%" valign="top">
            <table class="bordered">
                <tr><td><strong>CLIENTE:</strong> {{ $comprobante->name }}</td></tr>
                <tr><td><strong>{{ $tipo_doc }}:</strong> {{ $comprobante->numero_doc }}</td></tr>
                <tr><td><strong>DIRECCIÓN:</strong> {{ $comprobante->address }}</td></tr>
            </table>
        </td>

        <td width="30%" valign="top">
            <table class="bordered">
                <tr>
                    <td><strong>FECHA EMISIÓN:</strong><br>
                        {{ \Carbon\Carbon::parse($comprobante->fecha_emision)->format('d/m/Y') }}
                    </td>
                </tr>

                @if($comprobante->tipo_pago == 'credito')
                <tr>
                    <td><strong>FECHA VENC.:</strong><br>
                        {{ \Carbon\Carbon::parse($comprobante->fecha_vencimiento)->format('d/m/Y') }}
                    </td>
                </tr>
                @endif

                <tr>
                    <td>
                        <strong>MONEDA:</strong>
                        {{ $comprobante->moneda == 1 ? 'SOLES' : 'DÓLARES' }}
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>COND. PAGO:</strong>
                        {{ strtoupper($comprobante->tipo_pago) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ================= DETALLE ================= --}}
<table class="tabla-detalle" style="margin-bottom:10px;">
    <thead>
        <tr>
            <th width="8%">CANT.</th>
            <th width="8%">UM</th>
            <th width="10%">CÓD.</th>
            <th width="34%">DESCRIPCIÓN</th>
            <th width="12%">V/U</th>
            <th width="12%">P/U</th>
            <th width="16%">IMPORTE</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($productos as $item)
        <tr>
            <td class="text-center">{{ $item->cantidad }}</td>
            <td class="text-center">{{ $item->unidad_de_medida }}</td>
            <td class="text-center">{{ $item->codigo }}</td>
            <td>{{ $item->descripcion }}</td>
            <td class="text-right">{{ number_format($item->valor_unitario, 3) }}</td>
            <td class="text-right">{{ number_format($item->precio_unitario, 3) }}</td>
            <td class="text-right">{{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ================= TOTALES ================= --}}
@php
    $simbolo = $comprobante->moneda == 1 ? 'S/. ' : '$ ';
    $gravada = $comprobante->total / 1.18;
    $igv = $comprobante->total - $gravada;
@endphp

<table width="100%">
    <tr>
        <td width="60%"></td>
        <td width="40%">
            <table class="totales">
                <tr>
                    <td><strong>GRAVADA:</strong></td>
                    <td class="text-right">{{ $simbolo }} {{ number_format($gravada, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>IGV (18%):</strong></td>
                    <td class="text-right">{{ $simbolo }} {{ number_format($igv, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>{{ $simbolo }} {{ number_format($comprobante->total, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>IMPORTE EN LETRAS:</strong><br>
                        {{ $totalEnLetras }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ================= DETRACCIÓN ================= --}}
@if($comprobante->detraccion == 1)
<br>
<table class="bordered">
    <tr>
        <td>
            <strong>Información de la detracción</strong><br><br>
            Operación sujeta al Sistema de Pago de Obligaciones Tributarias.<br>
            Bien o Servicio: 019 Arrendamiento de bienes muebles<br>
            Medio de pago: 001 Depósito en cuenta<br>
            Nro. Cta. Banco de la Nación: 00274019956<br>
            Porcentaje: 10%<br>
            Monto: {{ $simbolo }} {{ number_format($comprobante->total * 0.10, 2) }}
        </td>
    </tr>
</table>
@endif

{{-- ================= FOOTER ================= --}}
<br>
<table>
    <tr>
        <td class="text-center" style="font-size:10px; border-top:1px solid #000; padding-top:8px;">
            Representación impresa de la FACTURA ELECTRÓNICA<br>
            Autorizado mediante Resolución de Intendencia No.034-005-0005315
        </td>
    </tr>
</table>

</body>
</html>
