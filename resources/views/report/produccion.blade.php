@extends('layouts.app')

@section('title', 'Stock de Producción')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                <i class="fas fa-industry me-2"></i>
                Stock de Producción
            </h3>

            <p class="text-muted mb-0">
                Motos disponibles según su última etapa de producción
            </p>
        </div>

    </div>

   <!-- Main content -->
    <section class="content">

        <form
            method="GET"
            action="{{ action([\App\Http\Controllers\StockProduccionController::class, 'index']) }}"
            id="formStockProduccion"
        >

            @component('components.filters', ['title' => __('report.filters')])

                {{-- NÚMERO DE MOTOR --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label('lot_number', 'Número de motor:') !!}

                        {!! Form::text(
                            'lot_number',
                            request('lot_number'),
                            [
                                'class' => 'form-control',
                                'id' => 'lot_number',
                                'placeholder' => 'Número de motor'
                            ]
                        ) !!}

                    </div>

                </div>


                {{-- UBICACIÓN --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label('location_id', 'Ubicación:') !!}

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


                {{-- PRODUCTO / ETAPA --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label('product_id', 'Etapa / Producto:') !!}

                        {!! Form::select(
                            'product_id',
                            $products,
                            request('product_id'),
                            [
                                'class' => 'form-control select2',
                                'id' => 'product_id',
                                'placeholder' => 'Todas las etapas',
                                'style' => 'width:100%'
                            ]
                        ) !!}

                    </div>

                </div>


                {{-- FECHA DESDE --}}
                <div class="col-md-3">

                    <div class="form-group">

                        {!! Form::label('fecha_desde', 'Fecha desde:') !!}

                        {!! Form::date(
                            'fecha_desde',
                            request('fecha_desde'),
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

                        {!! Form::label('fecha_hasta', 'Fecha hasta:') !!}

                        {!! Form::date(
                            'fecha_hasta',
                            request('fecha_hasta'),
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

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="fa fa-search"></i>
                                Filtrar
                            </button>

                            <a
                                href="{{ route('stock.produccion.pdf', request()->query()) }}"
                                class="btn btn-danger"
                                target="_blank"
                            >
                                <i class="fas fa-file-pdf"></i>
                                Exportar PDF
                            </a>

                            <a
                                href="{{ action([\App\Http\Controllers\StockProduccionController::class, 'index']) }}"
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

        {{-- TABLA --}}

        @component('components.widget', [
            'class' => 'box-primary',
            'title' => 'Motos disponibles'
        ])

            <div class="table-responsive">

                <table
                    class="table table-bordered table-striped"
                    id="stock_produccion_table"
                    width="100%"
                >

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>
                                Número de motor
                            </th>

                            <th>
                                Ubicación
                            </th>

                            <th>
                                Última etapa
                            </th>

                            <th>
                                Fecha último proceso
                            </th>

                            <th>
                                Cantidad
                            </th>

                            <th>
                                Estado
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($stocks as $index => $stock)

                            <tr>

                                <td>
                                    {{ $stocks->firstItem() + $index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $stock->lot_number }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $stock->ubicacion ?? 'Sin ubicación' }}
                                </td>

                                <td>

                                    <span class="label label-info">

                                        {{ $stock->producto }}

                                    </span>

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse(
                                        $stock->transaction_date
                                    )->format('d/m/Y H:i') }}

                                </td>

                                <td>
                                    {{ number_format($stock->quantity, 0) }}
                                </td>

                                <td>

                                    <span class="label label-success">

                                        <i class="fa fa-check"></i>

                                        Disponible

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center"
                                >

                                    No se encontraron motos disponibles.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if($stocks->hasPages())

                <div class="text-center">

                    {{ $stocks->links() }}

                </div>

            @endif

        @endcomponent

    </section>

</div>

@endsection