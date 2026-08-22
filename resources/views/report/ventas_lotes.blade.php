@extends('layouts.app')

@section('title', 'Reporte de Ventas por Lote')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">

                <i class="fas fa-file-invoice-dollar me-2"></i>

                Reporte de Ventas por Lote

            </h3>

            <p class="text-muted mb-0">

                Detalle de productos con número de lote vendidos

            </p>

        </div>

    </div>


    <!-- Main content -->

    <section class="content">

        <form
            method="GET"
            action="{{ action([\App\Http\Controllers\ReporteVentasLotesController::class, 'index']) }}"
            id="formReporteVentasLotes"
        >

            @component('components.filters', [
                'title' => __('report.filters')
            ])

                {{-- UBICACIÓN --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label(
                            'location_id',
                            'Ubicación:'
                        ) !!}

                        {!! Form::select(
                            'location_id',
                            $locations,
                            request('location_id'),
                            [
                                'class' => 'form-control select2',
                                'id' => 'location_id',
                                'placeholder' => 'Todas las ubicaciones',
                                'style' => 'width:100%'
                            ]
                        ) !!}

                    </div>

                </div>


                {{-- CLIENTE --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label(
                            'contact_id',
                            'Cliente:'
                        ) !!}

                        {!! Form::select(
                            'contact_id',
                            $clientes,
                            request('contact_id'),
                            [
                                'class' => 'form-control select2',
                                'id' => 'contact_id',
                                'placeholder' => 'Todos los clientes',
                                'style' => 'width:100%'
                            ]
                        ) !!}

                    </div>

                </div>


                {{-- PRODUCTO --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label(
                            'product_id',
                            'Producto:'
                        ) !!}

                        {!! Form::select(
                            'product_id',
                            $products,
                            request('product_id'),
                            [
                                'class' => 'form-control select2',
                                'id' => 'product_id',
                                'placeholder' => 'Todos los productos',
                                'style' => 'width:100%'
                            ]
                        ) !!}

                    </div>

                </div>


                {{-- FECHA DESDE --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label(
                            'fecha_desde',
                            'Fecha desde:'
                        ) !!}

                        {!! Form::date(
                            'fecha_desde',
                            $fecha_desde,
                            [
                                'class' => 'form-control',
                                'id' => 'fecha_desde'
                            ]
                        ) !!}

                    </div>

                </div>


                {{-- FECHA HASTA --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label(
                            'fecha_hasta',
                            'Fecha hasta:'
                        ) !!}

                        {!! Form::date(
                            'fecha_hasta',
                            $fecha_hasta,
                            [
                                'class' => 'form-control',
                                'id' => 'fecha_hasta'
                            ]
                        ) !!}

                    </div>

                </div>


                {{-- BOTONES --}}
                <div class="col-md-6">

                    <div class="form-group">

                        <label>&nbsp;</label>

                        <div>

                            {{-- FILTRAR --}}

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i class="fa fa-search"></i>

                                Filtrar

                            </button>


                            {{-- PDF --}}

                            <a href="{{ route('reportes.ventas.lotes.pdf', request()->query()) }}"
                                class="btn btn-danger"
                                target="_blank"
                            >

                                <i class="fas fa-file-pdf"></i>

                                Exportar PDF

                            </a>

                            <a href="{{ route('reportes.ventas.lotes.excel', request()->query()) }}"
                                class="btn btn-success">

                                <i class="fas fa-file-excel"></i>
                                Exportar Excel

                            </a>


                            {{-- LIMPIAR --}}

                            <a
                                href="{{ action([
                                    \App\Http\Controllers\ReporteVentasLotesController::class,
                                    'index'
                                ]) }}"
                                class="btn btn-default"
                            >

                                <i class="fa fa-refresh"></i>

                                Limpiar

                            </a>

                        </div>

                    </div>

                </div>

            @endcomponent

        </form>


        {{-- RESUMEN --}}

        <div class="row">

            {{-- TOTAL ITEMS --}}

            <div class="col-md-4">

                @component('components.widget', [
                    'class' => 'box-primary',
                    'title' => 'Items vendidos'
                ])

                    <h3 class="text-center">

                        {{ number_format($total_items, 0) }}

                    </h3>

                @endcomponent

            </div>


            {{-- CANTIDAD --}}

            <div class="col-md-4">

                @component('components.widget', [
                    'class' => 'box-success',
                    'title' => 'Cantidad total'
                ])

                    <h3 class="text-center">

                        {{ number_format($total_cantidad, 0) }}

                    </h3>

                @endcomponent

            </div>


            {{-- TOTAL VENTA --}}

            <div class="col-md-4">

                @component('components.widget', [
                    'class' => 'box-warning',
                    'title' => 'Total venta'
                ])

                    <h3 class="text-center">

                        S/
                        {{ number_format(
                            $total_venta,
                            2
                        ) }}

                    </h3>

                @endcomponent

            </div>

        </div>


        {{-- TABLA --}}

        @component('components.widget', [

            'class' => 'box-primary',

            'title' => 'Detalle de ventas por lote'

        ])

            <div class="table-responsive">

                <table
                    class="table table-bordered table-striped"
                    id="ventas_lotes_table"
                    width="100%"
                >

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>
                                Fecha
                            </th>

                            <th>
                                Producto
                            </th>

                            <th>
                                Lot Number
                            </th>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Ubicación
                            </th>

                            <th>
                                Cantidad
                            </th>

                            <th>
                                Precio venta
                            </th>

                            <th>
                                Invoice No.
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($ventas as $index => $venta)

                            <tr>

                                <td>

                                    {{ $index + 1 }}

                                </td>


                                <td>

                                    {{ \Carbon\Carbon::parse(
                                        $venta->transaction_date
                                    )->format('d/m/Y H:i') }}

                                </td>


                                <td>

                                    {{ $venta->producto }}

                                </td>


                                <td>

                                    <strong>

                                        {{ $venta->lot_number }}

                                    </strong>

                                </td>


                                <td>

                                    {{ $venta->cliente ?? 'Cliente genérico' }}

                                </td>


                                <td>

                                    {{ $venta->ubicacion ?? 'Sin ubicación' }}

                                </td>


                                <td class="text-center">

                                    {{ number_format(
                                        $venta->cantidad,
                                        0
                                    ) }}

                                </td>


                                <td class="text-right">

                                    S/
                                    {{ number_format(
                                        $venta->unit_price_inc_tax,
                                        2
                                    ) }}

                                </td>


                                <td>

                                    <strong>

                                        {{ $venta->invoice_no }}

                                    </strong>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center"
                                >

                                    No se encontraron ventas
                                    de productos con lote.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>


                    @if($ventas->count() > 0)

                        <tfoot>

                            <tr>

                                <th
                                    colspan="6"
                                    class="text-right"
                                >

                                    TOTAL:

                                </th>

                                <th class="text-center">

                                    {{ number_format(
                                        $total_cantidad,
                                        0
                                    ) }}

                                </th>

                                <th class="text-right">

                                    S/
                                    {{ number_format(
                                        $total_venta,
                                        2
                                    ) }}

                                </th>

                                <th></th>

                            </tr>

                        </tfoot>

                    @endif

                </table>

            </div>

        @endcomponent

    </section>

</div>

@endsection