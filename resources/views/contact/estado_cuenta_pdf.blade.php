@php use Illuminate\Support\Str; @endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta</title>

    <style>
        body { font-family: DejaVu Sans; font-size: 8px; }

        table { width:100%; border-collapse:collapse; table-layout: fixed; }
        th, td { border:1px solid #000; padding:4px; text-align:center; vertical-align: middle; }
        th { background:#f0f0f0; font-weight:600; }

        .text-right { text-align: right; }
        .text-left  { text-align: left; }

        .titulo { font-size:16px; font-weight:bold; text-align:center; margin-bottom:5px; }
        .subtitulo p { margin:2px 0; font-size:13px; }

        .totales td { font-weight:bold; background:#e8e8e8; }

        /* --- estabilidad: filas permitidas hasta 2 líneas --- */
        tbody tr { height: 26px; }             /* fila preparada para hasta 2 líneas */
        .dos-lineas {
            height:26px;
            line-height:13px;                 /* 2 * 13 = 26 */
            overflow: hidden;
            word-wrap: break-word;
            white-space: normal;
        }

        /* columnas anchas/estrechas: ajustar para que entren 2 líneas */
        .c-fecha   { width: 10%; }
        .c-guia    { width: 12%; }
        .c-motor   { width: 12%; }
        .c-modelo  { width: 22%; }
        .c-importe { width: 12%; }
        .c-subtot  { width: 12%; }

        .p-fecha   { width: 10%; }
        .p-cuenta  { width: 18%; }
        .p-nota    { width: 30%; }
        .p-importe { width: 12%; }
        .p-saldo   { width: 20%; }

        /* evitar que una fila se rompa entre páginas */
        tr { page-break-inside: avoid; }

        .page-break { page-break-after: always; }
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
    // filas por "slot" en la columna (este número ya lo usabas)
    $filasPorPagina = 20;

    // limpiamos notas y cuentas para evitar saltos \r \n \t
    foreach ($pagos as $idx => $pp) {
        $clean = trim(preg_replace('/[\r\n\t]+/', ' ', $pp->nota_pago ?? ''));
        $pagos[$idx]->nota_pago = $clean;
        $cleanCuenta = trim(preg_replace('/[\r\n\t]+/', ' ', $pp->cuenta ?? ''));
        $pagos[$idx]->cuenta = $cleanCuenta;
    }

    // aseguramos arrays indexados
    $comprasArr = array_values($compras);
    $pagosArr   = array_values($pagos);

    // cantidad máxima de páginas en función de "slots" por columna
    $comprasChunks = array_chunk($comprasArr, $filasPorPagina);
    $pagosChunks   = array_chunk($pagosArr, $filasPorPagina);

    $maxPaginas = max(count($comprasChunks), count($pagosChunks));

    // Si alguna página tiene menos filas en una columna, la rellenamos con nulls
    for ($i=0; $i<$maxPaginas; $i++) {
        $comprasChunks[$i] = $comprasChunks[$i] ?? [];
        $pagosChunks[$i]   = $pagosChunks[$i] ?? [];

        while (count($comprasChunks[$i]) < $filasPorPagina) {
            $comprasChunks[$i][] = null;
        }
        while (count($pagosChunks[$i]) < $filasPorPagina) {
            $pagosChunks[$i][] = null;
        }
    }

    // saldo inicial
    $saldo = $totales->total_compras;
@endphp

@for($pagina = 0; $pagina < $maxPaginas; $pagina++)

    {{-- Una sola tabla por página — dos sub-secciones lado a lado --}}
    <table style="margin-top:10px;">
        <thead>
            <tr>
                <th colspan="6">COMPRAS</th>
                <th colspan="5">PAGOS</th>
            </tr>
            <tr>
                <th class="c-fecha">Fecha</th>
                <th class="c-guia">Guía</th>
                <th class="c-motor">Motor</th>
                <th class="c-modelo">Modelo</th>
                <th class="c-importe">Importe</th>
                <th class="c-subtot">Subtotal</th>

                <th class="p-fecha">Fecha</th>
                <th class="p-cuenta">Cuenta</th>
                <th class="p-nota">Nota</th>
                <th class="p-importe">Importe</th>
                <th class="p-saldo">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @for($r = 0; $r < $filasPorPagina; $r++)
                @php
                    $c = $comprasChunks[$pagina][$r] ?? null;
                    $p = $pagosChunks[$pagina][$r] ?? null;
                @endphp
                <tr>
                    {{-- COMPRAS --}}
                    <td class="dos-lineas c-fecha">{{ $c->fecha ?? '' }}</td>
                    <td class="dos-lineas c-guia">{{ $c->guia ?? '' }}</td>
                    <td class="dos-lineas c-motor">{{ $c->nro_motor ?? '' }}</td>
                    <td class="dos-lineas c-modelo text-left">{{ $c->modelo ?? '' }}</td>
                    <td class="dos-lineas c-importe text-right">
                        {{ isset($c) ? number_format($c->importe_venta, 2) : '' }}
                    </td>
                    <td class="dos-lineas c-subtot text-right">
                        {{ isset($c) ? number_format($c->subtotal_guia, 2) : '' }}
                    </td>

                    {{-- PAGOS --}}
                    @php
                        if ($p) {
                            $saldo -= $p->importe_cancelado;
                        }
                    @endphp

                    <td class="dos-lineas p-fecha">{{ $p->fecha_pago ?? '' }}</td>
                    <td class="dos-lineas p-cuenta text-left">{{ $p->cuenta ?? '' }}</td>
                    <td class="dos-lineas p-nota text-left">{{ $p->nota_pago ?? '' }}</td>
                    <td class="dos-lineas p-importe text-right">
                        {{ isset($p) ? number_format($p->importe_cancelado, 2) : '' }}
                    </td>
                    <td class="dos-lineas p-saldo text-right">
                        {{ isset($p) ? number_format($saldo, 2) : '' }}
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    @if($pagina < $maxPaginas - 1)
        <div class="page-break"></div>
    @endif

@endfor

{{-- TOTALES --}}
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

<br><br>

{{-- FIRMAS --}}
<div style="page-break-inside: avoid;">
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
</div>

</body>
</html>
