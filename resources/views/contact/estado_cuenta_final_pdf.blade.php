<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th colspan="6">COMPRAS</th>
            <th colspan="4">PAGOS</th>
            <th>SALDO</th>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Pedido</th>
            <th>Motor</th>
            <th>Modelo</th>
            <th>Importe</th>
            <th>SubTotal</th>

            <th>Fecha P.</th>
            <th>Cuenta</th>
            <th>Nota</th>
            <th>Importe Pago</th>

            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
    @foreach($movimientos as $m)
        <tr>
            <td>{{ ($m['tipo']=='COMPRA' && $m['es_primero']) ? $m['fecha'] : '' }}</td>
            <td>{{ ($m['tipo']=='COMPRA' && $m['es_primero']) ? $m['invoice_no'] : '' }}</td>

            <td>{{ $m['motor'] }}</td>
            <td>{{ $m['modelo'] }}</td>

            <td align="right">
                {{ $m['tipo']=='COMPRA' ? number_format($m['importe'],2) : '' }}
            </td>

            <td align="right">
                {{ ($m['tipo']=='COMPRA' && $m['es_ultimo']) ? number_format($m['subtotal'],2) : '' }}
            </td>

            <td>
                {{ $m['tipo']=='PAGO' ? \Carbon\Carbon::parse($m['fecha'])->format('d/m/Y') : '' }}
            </td>

            <td>{{ $m['cuenta'] }}</td>

            <td style="font-size:10px">{{ $m['nota_pago'] }}</td>

            <td align="right">
                {{ $m['tipo']=='PAGO' ? number_format($m['importe_pago'],2) : '' }}
            </td>

            <td align="right">
                {{ number_format($m['saldo'],2) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
