<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background: #eaeaea; text-align: center; }
        .filtros td { border: none; padding: 2px 6px; }
        h3 { text-align: center; margin: 5px 0; }
    </style>
</head>
<body>

<img src="{{ public_path('img/contratobg.jpeg') }}" width="100%">

<h3>INFORME DE COMPRA DE PRODUCTOS</h3>

<table class="filtros" width="100%">
<tr>
    <td><strong>Fecha:</strong> {{ $filtros['fecha'] }}</td>
    <td><strong>Modelo:</strong> {{ $filtros['modelo'] }}</td>
    <td><strong>Proveedor:</strong> {{ $filtros['proveedor'] }}</td>
</tr>
<tr>
    <td><strong>Contenedor:</strong> {{ $filtros['contenedor'] }}</td>
    <td><strong>Guía:</strong> {{ $filtros['guia'] }}</td>
    <td><strong>Estado:</strong> {{ $filtros['estado'] }}</td>
</tr>
</table>

<br>

<table>
<thead>
<tr>
    <th>ITEM</th>
    <th>MODELO</th>
    <th>MOTOR</th>
    <th>CHASIS</th>
    <th>PROVEEDOR</th>
    <th>GUÍA</th>
    <th>PRECIO</th>
    <th>N. CONTENEDOR</th>
    <th>ESTADO</th>
</tr>
</thead>
<tbody>
@foreach($purchaseLines as $i => $line)
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ $line->product->name }}</td>
    <td>{{ $line->motor }}</td>
    <td>{{ $line->chasis }}</td>
    <td>{{ $line->transaction->contact->supplier_business_name ?? '--' }}</td>
    <td>{{ $line->guia }}</td>
    <td style="text-align:right">{{ number_format($line->purchase_price_inc_tax, 2) }}</td>
    <td>{{ $line->contenedor }}</td>
    <td style="text-align:center">{{ $line->estado }}</td>
</tr>
@endforeach
</tbody>
</table>

</body>
</html>
