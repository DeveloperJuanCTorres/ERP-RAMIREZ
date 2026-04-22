<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Stock</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>Reporte de Stock</h2>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Stock</th>
            <th>Ubicación</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $row)
            <tr>
                <td>{{ $row['producto'] }}</td>
                <td>{{ $row['stock'] }}</td>
                <td>{{ $row['ubicacion'] }}</td>
                <td>{{ $row['estado'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>