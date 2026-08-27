<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans;
    font-size:10px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    border:1px solid #000;
    padding:5px;
}

th{
    background:#eee;
}

h3{
    text-align:center;
}

</style>

</head>
<body>

<h3>Reporte de Transferencias de Stock</h3>

<table>

<thead>

<tr>
    <th>Fecha</th>
    <th>Referencia</th>
    <th>Desde</th>
    <th>Hasta</th>
    <th>Producto</th>
    <th>N° Motor</th>
    <th>Cant.</th>
    <th>Estado</th>
</tr>

</thead>

<tbody>

@foreach($transfers as $t)

<tr>
    <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y') }}</td>
    <td>{{ $t->ref_no }}</td>
    <td>{{ $t->location_from }}</td>
    <td>{{ $t->location_to }}</td>
    <td>{{ $t->product }}</td>
    <td>{{ $t->lot_number }}</td>
    <td>{{ $t->quantity }}</td>
    <td>{{ ucfirst($t->status) }}</td>
</tr>

@endforeach

</tbody>

</table>

</body>
</html>