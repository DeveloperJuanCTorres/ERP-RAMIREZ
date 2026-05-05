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
        <form id="filtrosForm">
            <div class="col-md-3">
                <label for="product">Buscar producto</label>
                <input id="filter_product" class="form-control" type="text" name="product" placeholder="Buscar producto">
            </div>

            <div class="col-md-3">
                <label for="category_id">Categoría</label>
                <select id="filter_category" class="form-control select2" name="category_id">
                    <option value="">Seleccionar</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="brand_id">Marca</label>
                <select id="filter_brand" class="form-control select2" name="brand_id">
                    <option value="">Seleccionar</option>
                    @foreach($brands as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="location_id">Ubicación</label>
                <select id="filter_location" class="form-control select2" name="location_id">
                    <option value="">Seleccionar</option>
                    @foreach($locations as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
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
                        <th>Lotes</th>
                    </tr>
                </thead>
                <tbody id="tablaStock"></tbody>
            </table>

            <div class="modal fade" id="modalLotes">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Lotes del producto</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Lote</th>
                                        <th>Stock</th>
                                        <th>Vencimiento</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaLotes"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcomponent


</section>


@stop

@section('javascript')

<script>
    $(document).ready(function() {

        function cargarStock() {
            let data = {
                product: $('#filter_product').val(),
                category_id: $('#filter_category').val(),
                brand_id: $('#filter_brand').val(),
                location_id: $('#filter_location').val()
            };

            $.ajax({
                url: "{{ route('reportes.stock.data') }}", // nuevo endpoint
                method: "GET",
                data: data,
                success: function(response) {
                    renderTabla(response);
                }
            });
        }

        function renderTabla(data) {
            let html = '';

            data.forEach(row => {

                let badgeClass = 'btn-success';
                if(row.estado === 'CRITICO') badgeClass = 'bg-error';
                if(row.estado === 'BAJO') badgeClass = 'bg-warning';
                if(row.estado === 'SIN STOCK') badgeClass = 'btn-danger';

                // 🔥 LOTES BONITOS (PRO)
                let lotesHtml = '-';

                if (row.lotes && row.lotes.length > 0) {

                    // solo mostrar el primero + contador
                    let first = row.lotes[0];

                    lotesHtml = `
                        <div>
                            <span class="badge bg-info">
                                ${first.lot_number}
                            </span>

                            ${row.lotes.length > 1 
                                ? `<span class="badge bg-secondary">+${row.lotes.length - 1}</span>` 
                                : ''
                            }

                            <button class="btn btn-xs btn-default ver-lotes"
                                data-lotes='${JSON.stringify(row.lotes)}'>
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    `;
                }

                html += `
                    <tr>
                        <td>${row.producto}</td>
                        <td>${row.sku}</td>
                        <td>${row.variacion}</td>
                        <td>${row.categoria ?? ''}</td>
                        <td>${row.marca ?? ''}</td>
                        <td>${row.ubicacion ?? ''}</td>
                        <td><strong>${row.stock}</strong></td>
                        <td>${row.stock_minimo}</td>
                        <td>S/ ${parseFloat(row.valor_stock).toFixed(2)}</td>
                        <td><span class="badge ${badgeClass}">${row.estado}</span></td>
                        <td>${lotesHtml}</td>
                    </tr>
                `;
            });

            $('#tablaStock').html(html);
        }

        // 🔥 eventos dinámicos
        $('#filter_product').on('keyup', debounce(cargarStock, 400));
        $('#filter_category, #filter_brand, #filter_location').on('change', cargarStock);

        // carga inicial
        cargarStock();

        // debounce para no saturar
        function debounce(func, wait) {
            let timeout;
            return function() {
                clearTimeout(timeout);
                timeout = setTimeout(func, wait);
            };
        }

    });
</script>

<script>
    $(document).on('click', '.ver-lotes', function() {

        let lotes = $(this).data('lotes');
        let html = '';

        lotes.forEach(l => {
            html += `
                <tr>
                    <td>${l.lot_number}</td>
                    <td>${l.qty}</td>
                    <td>${l.exp_date ?? '-'}</td>
                </tr>
            `;
        });

        $('#tablaLotes').html(html);
        $('#modalLotes').modal('show');
    });
</script>

@endsection