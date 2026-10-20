<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Guía de Remisión {{ $guia->serie }}-{{ str_pad($guia->numero, 6, '0', STR_PAD_LEFT) }}</title>
<style>
/* GUÍA DE REMISIÓN - DISEÑO COMPACTO A4 */
@page { size: A4 portrait; margin: 9mm 11mm; }
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body { color: #263443; font: 9.5px/1.23 Arial, Helvetica, sans-serif; }
.sheet { width: 100%; }
table { width: 100%; border-collapse: collapse; }
.header { margin-bottom: 7px; }
.header td { vertical-align: middle; }
.logo { max-width: 100%; max-height: 90px; width: auto; height: auto; object-fit: contain; }
.number-box { border: 1px solid #8d9eac; border-radius: 5px; text-align: center; padding: 7px 6px; }
.number-box .ruc { font-size: 11px; font-weight: bold; }
.number-box .kind { font-size: 10px; font-weight: bold; margin: 4px 0; line-height: 1.2; }
.number-box .number { font-size: 15px; font-weight: bold; letter-spacing: .2px; }
.section { border: 1px solid #d8e0e7; border-radius: 4px; margin: 5px 0; page-break-inside: avoid; overflow: hidden; }
/* Tonalidad sutil: azul grisáceo pastel, sin barras oscuras */
.section-title { background: #e9f0f5 !important; color: #344b60 !important; border-bottom: 1px solid #d5e0e8; font-size: 9px; font-weight: 700; letter-spacing: .25px; padding: 4px 8px !important; margin: 0 !important; border-radius: 0; text-transform: uppercase; }
.section-content { padding: 5px 8px; }
.detail td { padding: 1.5px 5px 1.5px 0; vertical-align: top; line-height: 1.22; }
.key { color: #596b79; font-size: 8.5px; text-transform: uppercase; font-weight: bold; }
.value { font-weight: 600; overflow-wrap: anywhere; }
.route td { width: 50%; vertical-align: top; padding: 2px 9px 2px 0; line-height: 1.22; }
.route-title { font-weight: bold; font-size: 9px; margin-bottom: 2px; color: #405b70; }
.route-name { font-weight: bold; margin: 2px 0; }
.muted { color: #596775; }
.items { margin-top: 6px; }
.items th { background: #edf2f6 !important; color: #344b60; border-top: 1px solid #d6dee5; border-bottom: 1px solid #d6dee5; font-size: 8.5px; text-align: left; padding: 5px 5px; }
.items td { border-bottom: 1px solid #e0e5e9; padding: 4px 5px; vertical-align: top; line-height: 1.25; overflow-wrap: anywhere; }
.items tbody tr { page-break-inside: avoid; }
.center { text-align: center !important; }
.right { text-align: right !important; }
.small { font-size: 8.5px; }
.notes { white-space: pre-wrap; overflow-wrap: anywhere; line-height: 1.25; }
.footer { border-top: 1px solid #9ca9b6; margin-top: 9px; padding-top: 6px; page-break-inside: avoid; }
.footer td { vertical-align: top; }
.qr-box { border: 1px solid #d6dee5; border-radius: 4px; text-align: center; padding: 4px; min-height: 90px; }
.qr-box svg, .qr-box img { width: 82px; height: 82px; max-width: 100%; }
.test { font-size: 8.5px; font-weight: bold; color: #7a3d1e; margin-top: 4px; }
.no-print { margin: 6px 0; }
@media print {
    html, body, .section-title, .items th { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .no-print { display: none !important; }
}



/* PIE DE PÁGINA - EMPRESA DESARROLLADORA */
.developer-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 5px 0;
    border-top: 1px solid #e5e9ed;
    background: #ffffff;
}

.developer-footer img {
    max-height: 75px;
    max-width: 180px;
    width: auto;
    height: auto;
    object-fit: contain;
}

.developer-footer p {
    margin: 0;
    font-size: 10px;
    font-weight: 600;
    color: #667788;
    line-height: 1.2;
}

@media print {
    .developer-footer {
        position: fixed;
        bottom: 0;
    }

    .sheet {
        padding-bottom: 65px;
    }
}



</style>
</head>
<body>
@php
    $businessId = (int) ($guia->business_id ?? session('user.business_id'));
    $logoPath = public_path(in_array($businessId, [1, 7], true)
        ? 'img/facturacion1-bg.png'
        : 'img/a1ramirez-facturacion.png');
    $logoSrc = null;
    if (is_file($logoPath)) {
        $extension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $mime = $extension === 'png' ? 'image/png' : ($extension === 'jpg' || $extension === 'jpeg' ? 'image/jpeg' : null);
        if ($mime) { $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath)); }
    }
    $motivo = str_pad((string) ($envio['motivo_de_traslado'] ?? ''), 2, '0', STR_PAD_LEFT);
    $esInterno = $motivo === '04';
    $empresaNombre = session('business.name', '');
    $empresaRuc = session('business.ruc', '');
    $destinatarioNombre = $esInterno ? $empresaNombre : ($envio['cliente_denominacion'] ?? '');
    $destinatarioDoc = $esInterno ? $empresaRuc : ($envio['cliente_numero_de_documento'] ?? '');
    $destinatarioDireccion = $esInterno ? ($envio['punto_de_llegada_direccion'] ?? '') : ($envio['cliente_direccion'] ?? '');
    $fecha = function ($valor) {
        if (!$valor) return '—';
        try { return \Carbon\Carbon::parse($valor)->format('d/m/Y'); }
        catch (\Throwable $e) { return $valor; }
    };
    $formatoMotivo = $esInterno ? 'TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA' : ($motivo === '01' ? 'VENTA' : $motivo);
    $transporte = (string) ($envio['tipo_de_transporte'] ?? '');
    $modalidad = $transporte === '02' ? 'TRANSPORTE PRIVADO' : ($transporte === '01' ? 'TRANSPORTE PÚBLICO' : $transporte);
    $partida = $ubigeoPartida ?? ($envio['punto_de_partida_ubigeo'] ?? '');
    $llegada = $ubigeoLlegada ?? ($envio['punto_de_llegada_ubigeo'] ?? '');
    $items = $envio['items'] ?? [];
    $documentos = $envio['documento_relacionado'] ?? [];
    $aceptada = (bool) ($guia->aceptada ?? false);
@endphp
<div class="sheet">
    <table class="header">
        <tr>
            <td style="width:59%; padding-right:18px;">
                @if($logoSrc)
                    <img class="logo" src="{{ $logoSrc }}" alt="Logo empresarial">
                @else
                    <strong>{{ $empresaNombre }}</strong>
                @endif
            </td>
            <td style="width:41%;">
                <div class="number-box">
                    <div class="ruc">RUC {{ $empresaRuc }}</div>
                    <div class="kind">GUÍA DE REMISIÓN<br>REMITENTE ELECTRÓNICA</div>
                    <div class="number">{{ $guia->serie }}-{{ str_pad($guia->numero, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">01 · DATOS DEL DESTINATARIO</div>
        <div class="section-content">
            <table class="detail">
                <tr><td style="width:20%" class="key">Razón social</td><td class="value">{{ $destinatarioNombre }}</td></tr>
                <tr><td class="key">RUC / DNI</td><td class="value">{{ $destinatarioDoc }}</td></tr>
                @if($destinatarioDireccion)
                <tr><td class="key">Dirección</td><td>{{ $destinatarioDireccion }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">02 · DATOS DEL TRASLADO</div>
        <div class="section-content">
            <table class="detail">
                <tr>
                    <td style="width:22%" class="key">Fecha de emisión</td><td style="width:28%" class="value">{{ $fecha($envio['fecha_de_emision'] ?? null) }}</td>
                    <td style="width:24%" class="key">Inicio de traslado</td><td class="value">{{ $fecha($envio['fecha_de_inicio_de_traslado'] ?? null) }}</td>
                </tr>
                <tr><td class="key">Motivo</td><td colspan="3" class="value">{{ $formatoMotivo }}</td></tr>
                <tr><td class="key">Modalidad</td><td colspan="3">{{ $modalidad }}</td></tr>
                <tr>
                    <td class="key">Peso bruto total</td><td class="value">{{ number_format((float) ($envio['peso_bruto_total'] ?? 0), 2) }} KGM</td>
                    <td class="key">Número de bultos</td><td class="value">{{ $envio['numero_de_bultos'] ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">03 · DATOS DEL TRANSPORTE</div>
        <div class="section-content">
            <table class="detail">
                <tr><td style="width:24%" class="key">Vehículo principal</td><td class="value">{{ $envio['transportista_placa_numero'] ?? '—' }}</td></tr>
                @if(!empty($envio['transportista_denominacion']))
                <tr><td class="key">Transportista</td><td>{{ $envio['transportista_denominacion'] }} — {{ $envio['transportista_documento_numero'] ?? '' }}</td></tr>
                @endif
                <tr><td class="key">Conductor principal</td><td>{{ trim(($envio['conductor_nombre'] ?? '') . ' ' . ($envio['conductor_apellidos'] ?? '')) }} — {{ $envio['conductor_documento_numero'] ?? '' }}</td></tr>
                <tr><td class="key">Licencia</td><td>{{ $envio['conductor_numero_licencia'] ?? '—' }}</td></tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">04 · PUNTOS DE PARTIDA Y LLEGADA</div>
        <div class="section-content">
            <table class="route">
                <tr>
                    <td>
                        <div class="route-title">PUNTO DE PARTIDA</div>
                        <div class="route-name">{{ $partida }}</div>
                        <div class="muted">{{ $envio['punto_de_partida_direccion'] ?? '' }}</div>
                    </td>
                    <td>
                        <div class="route-title">PUNTO DE LLEGADA</div>
                        <div class="route-name">{{ $llegada }}</div>
                        <div class="muted">{{ $envio['punto_de_llegada_direccion'] ?? '' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="items">
        <div class="section-title" style="border:1px solid #d6dee5;">05 · DETALLE DE LOS BIENES TRANSPORTADOS</div>
        <table>
            <thead>
                <tr>
                    <th style="width:6%" class="center">N.º</th>
                    <th style="width:15%">CÓDIGO</th>
                    <th>DESCRIPCIÓN</th>
                    <th style="width:8%" class="center">U/M</th>
                    <th style="width:10%" class="right">CANT.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item['codigo'] ?? '' }}</td>
                    <td>{{ $item['descripcion'] ?? '' }}</td>
                    <td class="center">{{ $item['unidad_de_medida'] ?? 'NIU' }}</td>
                    <td class="right">{{ $item['cantidad'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!empty($documentos))
    <div class="section">
        <div class="section-title">DOCUMENTOS RELACIONADOS</div>
        <div class="section-content">
            @foreach($documentos as $doc)
                <div style="padding:3px 0;">{{ ($doc['tipo'] ?? '') === '01' ? 'FACTURA' : (($doc['tipo'] ?? '') === '03' ? 'BOLETA' : ($doc['tipo'] ?? 'DOCUMENTO')) }}: {{ $doc['serie'] ?? '' }}-{{ $doc['numero'] ?? '' }}</div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($envio['observaciones']))
    <div class="section">
        <div class="section-title">OBSERVACIONES</div>
        <div class="section-content notes">{{ $envio['observaciones'] }}</div>
    </div>
    @endif

    @if(!empty($envio['vehiculos_secundarios']) || !empty($envio['conductores_secundarios']))
    <div class="section">
        <div class="section-title">INFORMACIÓN ADICIONAL DEL TRANSPORTE</div>
        <div class="section-content">
            @foreach(($envio['vehiculos_secundarios'] ?? []) as $v)
                <div>Vehículo secundario: {{ $v['placa_numero'] ?? '' }}</div>
            @endforeach
            @foreach(($envio['conductores_secundarios'] ?? []) as $c)
                <div>Conductor secundario: {{ trim(($c['nombre'] ?? '') . ' ' . ($c['apellidos'] ?? '')) }} — {{ $c['documento_numero'] ?? '' }} — Licencia: {{ $c['numero_licencia'] ?? '' }}</div>
            @endforeach
        </div>
    </div>
    @endif

    <table class="footer">
        <tr>
            <td style="width:76%; padding-right:10px; line-height:1.3;">
                <strong>REPRESENTACIÓN IMPRESA DE LA GUÍA DE REMISIÓN REMITENTE ELECTRÓNICA</strong><br>
                {{ $empresaNombre }} @if($empresaRuc) — RUC {{ $empresaRuc }} @endif<br>
                Documento: <strong>{{ $guia->serie }}-{{ str_pad($guia->numero, 6, '0', STR_PAD_LEFT) }}</strong><br>
                @if(!empty($guia->hash))Hash: {{ $guia->hash }}<br>@endif
                @if(!$aceptada)
                    <div class="test">DOCUMENTO DE PRUEBA — NO ENVIADO / NO ACEPTADO POR SUNAT</div>
                @endif
                @if(!empty($guia->sunat_description) && $aceptada)
                    <span class="small">{{ $guia->sunat_description }}</span>
                @endif
            </td>
            <td style="width:24%;">
                <div class="qr-box">
                    @if(!empty($qrSvg) && $aceptada)
                        {!! $qrSvg !!}
                    @else
                        <div class="muted" style="padding-top:22px; font-size:8.5px;">QR de verificación<br>disponible tras la<br>emisión real</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

   
</div>



<!-- PIE DE PÁGINA: EMPRESA DESARROLLADORA -->
<div class="developer-footer">

    <!-- LOGO IZQUIERDA -->
    <img
        src="{{ asset('img/logo-desarrollador.png') }}"
        alt="Grupo TYG - Desarrollo de Software"
    >

    <!-- TEXTO DERECHA -->
    <p>
        Desarrollo de Software |
        www.grupotyg.pe
    </p>

</div>



<script>
(function () {
    let cerrado = false;

    function cerrarVistaImpresion() {
        if (cerrado) return;

        cerrado = true;

        // Intenta cerrar la pestaña de impresión
        window.close();
    }

    // Se ejecuta al cerrar el diálogo de impresión,
    // tanto al imprimir como al cancelar.
    window.addEventListener('afterprint', function () {
        setTimeout(cerrarVistaImpresion, 300);
    });

    window.addEventListener('load', function () {
        setTimeout(function () {
            window.focus();
            window.print();
        }, 500);
    });
})();
</script>


</body>
</html>
