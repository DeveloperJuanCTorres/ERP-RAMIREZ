<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Pagos</title>
    <style>
        body { font-family: Arial; font-size: 14px; }
        .header { text-align:center; }
        .logo { height:80px; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        table, th, td { border:1px solid #000; }
        th, td { padding:8px; text-align:center; }
        .total { font-weight:bold; }
        .signatures { margin-top:60px; display:flex; justify-content:space-between; }
        .sign { text-align:center; width:40%; }
    </style>
</head>
<body onload="window.print()">

<div class="header">
    <img src="{{ asset('img/importaciones.jpeg') }}" class="logo">
    <h2>REPORTE DE PAGOS POR SERVICIO DE FABRICACIÓN</h2>
</div>

@if($user)
<p><strong>Usuario:</strong> {{ $user->first_name . ' ' .  $user->last_name}}</p>
@endif

@if(request('filter_date'))
<p><strong>Fecha de pago:</strong> {{ request('filter_date') }}</p>
@endif

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>N° Motor</th>
            <th>Monto Pagado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->product }}</td>
            <td>{{ $item->lot_number }}</td>
            <td>S/ {{ number_format($item->monto,2) }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="2">TOTAL PAGADO</td>
            <td>S/ {{ number_format($total,2) }}</td>
        </tr>
    </tbody>
</table>

<div class="signatures">
    <div class="sign">
        ___________________________<br>
        Importaciones Ramirez
    </div>

    <div class="sign">
        ___________________________<br>
        {{ $user->first_name . ' ' .  $user->last_name}}
    </div>
</div>

</body>
</html>
