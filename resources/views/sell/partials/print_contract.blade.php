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
            /* border: 1px solid #000; */
            padding: 2px;
        }

        .no-border td {
            border: none;
        }

        /* 🔥 CLAVE: rellena la hoja */
        .relleno-pagina {
            height: 50px; /* AJUSTA si tu contenido crece */
        }

        .firmas {
            margin-top: 10px;
        }
    </style>
</head>

<body>

{{-- CABECERA --}}
<div class="row">
    <img src="img/contratobg.jpeg" width="100%">
</div>

<hr>

<p class="text-center bold" style="font-size:13px">
    CONTRATO DE VENTA N° {{ $sell->invoice_no }} <br>
</p>

<hr>
<p class="bold">Fecha: {{ @format_date($sell->transaction_date) }}</p>
<table width="100%">
    <tr>
        {{-- IZQUIERDA --}}
        <td width="50%" valign="top">
            <p>
                <strong>Nombre:</strong>
                {{ $sell->contact->name . $sell->contact->supplier_business_name }}<br>

                <strong>Dirección:</strong>
                {!! $sell->contact->address_line_1 ?? '--' !!}
            </p>
        </td>

        {{-- DERECHA --}}
        <td width="50%" valign="top">
            <p>
                <strong>Documento:</strong>
                {{ $sell->contact->contact_id ?? '--' }}<br>

                <strong>Teléfono:</strong>
                {{ $sell->contact->mobile ?? '--' }}
            </p>
        </td>
    </tr>
</table>

@if($salesOrder)
<p class="bold">DETALLE DE LA ORDEN DE VENTA : {{$salesOrder->invoice_no}}</p>

<table>
    <thead>
        <tr>
            <th>Cantidad</th>
            <th>Modelo</th>
            <th>Descripción</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp

        @foreach($salesOrder->sell_lines as $line)
            @php
                $subtotal = $line->quantity * $line->unit_price_inc_tax;
                $total += $subtotal;
            @endphp
            <tr>
                <td class="text-center">{{ number_format($line->quantity, 0) }}</td>
                <td>{{ $line->product->name }}</td>
                <td>{{ $line->product->product_description ?? '--' }}</td>
                <td class="text-right">{{ number_format($line->unit_price_inc_tax, 2) }}</td>
                <td class="text-right">{{ number_format($subtotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4" class="text-right">TOTAL</th>
            <th class="text-right">{{ number_format($total, 2) }}</th>
        </tr>
    </tfoot>
</table>

<hr>
@endif

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


<!-- <hr>

<p class="bold">RESUMEN DE PAGOS</p>

@php
    $totalVenta   = $sell->final_total;
    $totalPagado  = $sell->payment_lines->sum('amount');
    $saldoPendiente = $totalVenta - $totalPagado;
@endphp

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Método de Pago</th>
            <th>Nota</th>
            <th class="text-right">Monto</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sell->payment_lines as $payment)
            <tr>
                <td class="text-center">
                    {{ @format_date($payment->paid_on) }}
                </td>
                @php
                    $paymentMethods = [
                        'advance' => 'Anticipo',
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'bank_transfer' => 'Transferencia',
                        'cheque' => 'Cheque',
                        'custom_pay_1' => 'Depósito'
                    ];
                @endphp

                <td class="text-center">
                    {{ $paymentMethods[$payment->method] ?? ucfirst($payment->method ?? '--') }}
                </td>
                <td class="text-center">
                    {{ $payment->note ?? '--' }}
                </td>
                <td class="text-right">
                    {{ number_format($payment->amount, 2) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">
                    No se registran pagos
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<br>

<table>
    <tr>
        <th class="text-right">TOTAL PAGADO</th>
        <th class="text-right">{{ number_format($totalPagado, 2) }}</th>
    </tr>
    <tr>
        <th class="text-right">SALDO PENDIENTE</th>
        <th class="text-right">
            {{ number_format($saldoPendiente, 2) }}
        </th>
    </tr>
</table> -->

<hr>

<p class="bold">RESUMEN DE PAGOS</p>

@php
    $totalVenta = $sell->final_total;
    $aCuenta = $sell->payment_lines->sum('amount');
    $saldoPendiente = $totalVenta - $aCuenta;

    // 👉 Tomamos UN pago (el primero)
    $pago = $sell->payment_lines->first();
@endphp

{{-- CABECERA --}}
<table>
    <thead>
        <tr>
            <th class="text-center">FECHA</th>
            <th class="text-center">CUENTA DE PAGO</th>
            <th class="text-center">NOTA</th>
            <th class="text-center">IMPORTE</th>
            <th class="text-center">SALDO</th>
        </tr>
    </thead>
    <tbody>
        @if($pago)
            @php
                $paymentMethods = [
                    'advance' => 'Anticipo',
                    'cash' => 'Efectivo',
                    'card' => 'Tarjeta',
                    'bank_transfer' => 'Transferencia',
                    'cheque' => 'Cheque',
                    'custom_pay_1' => 'Depósito'
                ];
            @endphp
            <tr>
                <td class="text-center">
                    {{ @format_date($pago->paid_on) }}
                </td>
                <td class="text-center">
                    {{ $paymentMethods[$pago->method] ?? ucfirst($pago->method) }}
                </td>
                <td class="text-center">
                    {{ $pago->note ?? '--' }}
                </td>
                <td class="text-right">
                    {{ number_format($pago->amount, 2) }}
                </td>
                <td class="text-right">
                    {{ number_format($saldoPendiente, 2) }}
                </td>
            </tr>
        @else
            <tr>
                <td colspan="5" class="text-center">
                    No se registran pagos
                </td>
            </tr>
        @endif
    </tbody>
</table>

<br>

{{-- RESUMEN FINAL --}}
<table>
    <tr>
        <th class="text-center">
            A Cta.: {{ number_format($aCuenta, 2) }}
        </th>
        <th class="text-center">
            Saldo: {{ number_format($saldoPendiente, 2) }}
        </th>
        <th class="text-center">
            Total Compra: {{ number_format($totalVenta, 2) }}
        </th>
    </tr>
</table>





{{-- 🔥 RELLENO PARA EMPUJAR AL FINAL --}}
<div class="relleno-pagina"></div>

{{-- FIRMAS SIEMPRE ABAJO --}}
<table class="no-border firmas">
    <tr>
        <td width="50%" class="text-center">
            ___________________________<br>
            <strong>Firma Empresa</strong><br>
            <strong>Imp. Ramirez</strong>
        </td>
        <td width="50%" class="text-center">
            ___________________________<br>
            <strong>Firma Cliente</strong><br>
            {{ $sell->contact->name . $sell->contact->supplier_business_name }}
        </td>
    </tr>
</table>

</body>
</html>
