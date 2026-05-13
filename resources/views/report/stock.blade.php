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
                <select id="filter_product" class="form-control select2" name="product_id">
                    <option value="">Seleccionar producto</option>
                    @foreach($products as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
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
                    <a id="btnExcel" href="{{ route('reportes.stock.excel') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel-o"></i> Exportar Excel
                    </a>

                    <a id="btnPdf" href="{{ route('reportes.stock.pdf') }}" class="btn btn-danger btn-sm">
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
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th>Ubicación</th>
                        <th>Stock</th>
                        <th>Serie</th>
                        <th>Color</th>
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
                                        <th>Color</th>
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
    $(document).ready(function () {

        /*
        |--------------------------------------------------------------------------
        | CARGAR STOCK
        |--------------------------------------------------------------------------
        */
        function cargarStock() {

            let data = {
                product_id: $('#filter_product').val(),
                category_id: $('#filter_category').val(),
                brand_id: $('#filter_brand').val(),
                location_id: $('#filter_location').val()
            };

            // 🔥 actualizar links exportación
            actualizarLinksExportacion();

            $.ajax({
                url: "{{ route('reportes.stock.data') }}",
                method: "GET",
                data: data,

                success: function (response) {
                    renderTabla(response);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | RENDER TABLA
        |--------------------------------------------------------------------------
        */
        function renderTabla(data) {

            let html = '';

            if (data.length === 0) {

                html = `
                    <tr>
                        <td colspan="7" class="text-center">
                            No se encontraron registros
                        </td>
                    </tr>
                `;

            } else {

                data.forEach(row => {

                    html += `
                        <tr>
                            <td>${row.producto}</td>

                            <td>${row.categoria ?? '-'}</td>

                            <td>${row.marca ?? '-'}</td>

                            <td>${row.ubicacion ?? '-'}</td>

                            <td>
                                <strong>${row.stock}</strong>
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    ${row.serie}
                                </span>
                            </td>

                            <td>
                                ${row.color ?? '-'}
                            </td>
                        </tr>
                    `;
                });
            }

            $('#tablaStock').html(html);
        }

        /*
        |--------------------------------------------------------------------------
        | EXPORTACIONES
        |--------------------------------------------------------------------------
        */
        function actualizarLinksExportacion() {

            let params = $.param({
                product_id: $('#filter_product').val(),
                category_id: $('#filter_category').val(),
                brand_id: $('#filter_brand').val(),
                location_id: $('#filter_location').val()
            });

            $('#btnExcel').attr(
                'href',
                "{{ route('reportes.stock.excel') }}?" + params
            );

            $('#btnPdf').attr(
                'href',
                "{{ route('reportes.stock.pdf') }}?" + params
            );
        }

        /*
        |--------------------------------------------------------------------------
        | EVENTOS
        |--------------------------------------------------------------------------
        */
        $('#filter_product, #filter_category, #filter_brand, #filter_location')
            .on('change', function () {

                cargarStock();
            });

        /*
        |--------------------------------------------------------------------------
        | CARGA INICIAL
        |--------------------------------------------------------------------------
        */
        cargarStock();

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
                    <td>${l.color ?? '-'}</td>
                </tr>
            `;
        });

        $('#tablaLotes').html(html);
        $('#modalLotes').modal('show');
    });
</script>

@endsection