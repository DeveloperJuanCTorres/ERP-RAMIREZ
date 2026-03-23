<!DOCTYPE html>
<html>
<head>
    <title>Reporte Transferencias</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h2 {
            text-align: center;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th {
            background: #f2f2f2;
        }

        th, td {
            padding: 5px;
            text-align: center;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ asset('img/contratobg.jpeg') }}">
</div>

<h2>REPORTE DE TRANSFERENCIAS DE STOCK</h2>

@foreach($transfers as $transfer_id => $items)

    @php
        $first = $items->first();
    @endphp

    <p>
        <strong>Trabsferencia:</strong> {{$first->ref_no}} <br>
        <strong>Fecha:</strong> 
        {{ \Carbon\Carbon::parse($first->transaction_date)->format('d/m/Y') }} <br>

        <strong>Desde:</strong> {{ $first->location_from }} <br>

        <strong>Hacia:</strong> {{ $first->location_to }} <br>

        <strong>Transportista:</strong> {{ $first->transportista ?? '-' }} <br>
    </p>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Lote</th>
                <th>Color</th>
            </tr>
        </thead>
        <tbody>

        @foreach($items as $row)
            <tr>
                <td>{{ $row->product }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->unit }}</td>
                <td>{{ $row->lot_number ?? '-' }}</td>
                <td>{{ $row->color ?? '-' }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

    <hr>

@endforeach

</body>
</html>

<script>
    window.onload = function () {
        window.print();
    }
</script>