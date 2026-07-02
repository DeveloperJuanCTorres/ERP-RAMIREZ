<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {

            background: #343a40;

            color: white;

            border: 1px solid #000;

            padding: 6px;

        }

        table td {

            border: 1px solid #000;

            padding: 5px;

        }

        h2 {

            text-align: center;

            margin-bottom: 20px;

        }
    </style>

</head>

<body>

    <h2>

        Historial de Importaciones Masivas

    </h2>

    <table>

        <thead>

            <tr>

                <th>Fecha</th>

                <th>Producto</th>

                <th>Motor</th>

                <th>Chasis</th>

                <th>Color</th>

                <th>Año</th>

                <th>Póliza</th>

                <th>Guía</th>

                <th>Contenedor</th>

            </tr>

        </thead>

        <tbody>

            @foreach($datos as $r)

            <tr>

                <td>{{ \Carbon\Carbon::parse($r->transaction_date)->format('d/m/Y') }}</td>

                <td>{{ $r->producto }}</td>

                <td>{{ $r->motor }}</td>

                <td>{{ $r->chasis }}</td>

                <td>{{ $r->color }}</td>

                <td>{{ $r->anio }}</td>

                <td>{{ $r->poliza }}</td>

                <td>{{ $r->guia }}</td>

                <td>{{ $r->contenedor }}</td>

            </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>