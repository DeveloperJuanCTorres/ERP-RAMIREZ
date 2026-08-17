<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Stock de Producción</title>

    <style>

        @page {
            margin: 25px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 4px 0;
            color: #666;
        }

        .filtros {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ddd;
            background: #f5f5f5;
        }

        .filtros strong {
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #343a40;
            color: white;
            padding: 7px 5px;
            border: 1px solid #222;
            text-align: center;
            font-size: 8px;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #ccc;
            font-size: 8px;
        }

        td.center {
            text-align: center;
        }

        .estado {
            background: #28a745;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 7px;
        }

        .etapa {
            background: #17a2b8;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 7px;
        }

        .total {
            margin-top: 12px;
            text-align: right;
            font-weight: bold;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            color: #777;
        }

    </style>

</head>

<body>

    {{-- HEADER --}}

    <div class="header">

        <h1>
            STOCK DE PRODUCCIÓN
        </h1>

        <p>
            Motos disponibles según su última etapa de producción
        </p>

        <p>
            Fecha de generación:
            {{ date('d/m/Y H:i') }}
        </p>

    </div>


    {{-- FILTROS --}}

    @if(
        request('lot_number') ||
        request('location_id') ||
        request('product_id') ||
        request('fecha_desde') ||
        request('fecha_hasta')
    )

        <div class="filtros">

            <strong>Filtros aplicados:</strong>

            @if(request('lot_number'))
                Motor:
                {{ request('lot_number') }}
            @endif

            @if(request('fecha_desde'))
                &nbsp; | &nbsp;
                Desde:
                {{ \Carbon\Carbon::parse(request('fecha_desde'))->format('d/m/Y') }}
            @endif

            @if(request('fecha_hasta'))
                &nbsp; | &nbsp;
                Hasta:
                {{ \Carbon\Carbon::parse(request('fecha_hasta'))->format('d/m/Y') }}
            @endif

        </div>

    @endif


    {{-- TABLA --}}

    <table>

        <thead>

            <tr>

                <th style="width: 5%;">
                    #
                </th>

                <th style="width: 17%;">
                    Número de motor
                </th>

                <th style="width: 18%;">
                    Ubicación
                </th>

                <th style="width: 20%;">
                    Última etapa
                </th>

                <th style="width: 18%;">
                    Fecha último proceso
                </th>

                <th style="width: 8%;">
                    Cantidad
                </th>

                <th style="width: 14%;">
                    Estado
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($stocks as $index => $stock)

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        <strong>
                            {{ $stock->lot_number }}
                        </strong>
                    </td>

                    <td>
                        {{ $stock->ubicacion ?? 'Sin ubicación' }}
                    </td>

                    <td class="center">

                        <span class="etapa">
                            {{ $stock->producto }}
                        </span>

                    </td>

                    <td class="center">

                        {{ \Carbon\Carbon::parse(
                            $stock->transaction_date
                        )->format('d/m/Y H:i') }}

                    </td>

                    <td class="center">

                        {{ number_format($stock->quantity, 0) }}

                    </td>

                    <td class="center">

                        <span class="estado">
                            ✓ Disponible
                        </span>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="7"
                        style="text-align:center; padding:15px;"
                    >

                        No se encontraron motos disponibles.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- TOTAL --}}

    <div class="total">

        Total de motos:
        {{ number_format($stocks->count(), 0) }}

    </div>


    {{-- FOOTER --}}

    <div class="footer">

        Stock de Producción -
        Generado el {{ date('d/m/Y H:i:s') }}

    </div>

</body>

</html>