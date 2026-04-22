@extends('layouts.app')
@section('title', __( 'lang_v1.all_sales'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <!-- <h1>lang( 'sale.sells') -->
    <h1>Reporte de stock
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <form method="GET">
            <div class="col-md-3">
                <label for="product">Buscar producto</label>
                <input class="form-control" type="text" name="product" placeholder="Buscar producto">
            </div>

            <div class="col-md-3">
                <label for="category_id">Categoría</label>
                <select class="form-control select2" name="category_id">
                    <option value="">Seleccionar</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="brand_id">Marca</label>
                <select class="form-control select2" name="brand_id">
                    <option value="">Seleccionar</option>
                    @foreach($brands as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="location_id">Ubicación</label>
                <select class="form-control select2" name="location_id">
                    <option value="">Seleccionar</option>
                    @foreach($locations as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="" class="w-100"></label>
                <button class="btn btn-primary" style="width: 100%;">Filtrar</button>
            </div>            
        </form>
    @endcomponent
    <!-- component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_sales')]) -->
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Stock de productos'])
        @can('direct_sell.access')
            @slot('tool')             
                <div class="pull-right">
                    <a href="{{ route('reportes.stock.excel') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel-o"></i> Exportar Excel
                    </a>

                    <a href="{{ route('reportes.stock.pdf') }}" class="btn btn-danger btn-sm">
                        <i class="fa fa-file-pdf-o"></i> Exportar PDF
                    </a>
                </div>                
            @endslot
        @endcan
        @if(auth()->user()->can('direct_sell.view') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
        @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
         @endphp
            <table class="table table-bordered table-striped">
                <thead >
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Variación</th>
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th>Ubicación</th>
                        <th>Stock</th>
                        <th>Stock Min</th>
                        <th>Valor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report as $row)
                        <tr>
                            <td>{{ $row['producto'] }}</td>
                            <td>{{ $row['sku'] }}</td>
                            <td>{{ $row['variacion'] }}</td>
                            <td>{{ $row['categoria'] }}</td>
                            <td>{{ $row['marca'] }}</td>
                            <td>{{ $row['ubicacion'] }}</td>
                            <td>{{ $row['stock'] }}</td>
                            <td>{{ $row['stock_minimo'] }}</td>
                            <td>S/ {{ number_format($row['valor_stock'], 2) }}</td>
                            <td>
                                <span class="badge 
                                    @if($row['estado'] == 'CRITICO') bg-error
                                    @elseif($row['estado'] == 'BAJO') bg-warning
                                    @elseif($row['estado'] == 'SIN STOCK') btn-danger
                                    @else btn-success
                                    @endif">
                                    {{ $row['estado'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endcomponent


</section>


@stop

@section('javascript')



@endsection