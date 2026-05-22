<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Reporte de Stock</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        h2 {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f3f3f3;
            font-size: 11px;
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
        }

        td {
            padding: 7px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        .badge {
            background: #2d89ef;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
    </style>
</head>

<body>

    <h2>Reporte de Stock</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Modelo</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Ubicación</th>
                <th>Stock</th>
                <th>Serie</th>
                <th>Color</th>
            </tr>
        </thead>

        <tbody>
            @foreach($report as $row)
                <tr>
                    <td>{{ $row['nro'] }}</td>
                    
                    <td>{{ $row['producto'] }}</td>

                    <td>
                        {{ $row['categoria'] ?? '-' }}
                    </td>

                    <td>
                        {{ $row['marca'] ?? '-' }}
                    </td>

                    <td>
                        {{ $row['ubicacion'] ?? '-' }}
                    </td>

                    <td>
                        {{ $row['stock'] }}
                    </td>

                    <td>
                        {{ $row['serie'] }}
                    </td>

                    <td>
                        {{ $row['color'] ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

</body>
</html>