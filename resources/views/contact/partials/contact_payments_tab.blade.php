<table id="contact_payments_shadow" style="display:none;">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Referencia</th>
            <th>Monto</th>
            <th>Método</th>
            <th>Para</th>
            <th>ID</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
            <tr>
                <td>{{ @format_datetime($payment->paid_on) }}</td>
                <td>{{ $payment->payment_ref_no }}</td>
                <td>{{ $payment->amount }}</td>
                <td>{{ $payment_types[$payment->method] ?? '' }}</td>
                <td>{{ $payment->transaction_type }}</td>
                <td>{{ $payment->id }}</td>
            </tr>
            @foreach($payment->child_payments as $child)
                <tr>
                    <td>{{ @format_datetime($payment->paid_on) }}</td> {{-- usamos fecha del padre --}}
                    <td>{{ $child->payment_ref_no }}</td>
                    <td>{{ $child->amount }}</td>
                    <td>{{ $payment_types[$child->method] ?? '' }}</td>
                    <td>{{ $child->transaction_type }}</td>
                    <td>{{ $child->id }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>



<table class="table table-bordered" 
id="contact_payments_table">
    <thead>
        <tr>
            <th>@lang('lang_v1.paid_on')</th>
            <th>@lang('purchase.ref_no')</th>
            <th>@lang('sale.amount')</th>
            <th>@lang('lang_v1.payment_method')</th>
            <th>@lang('account.payment_for')</th>
            <th>@lang('messages.action')</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $payment)
            @php
                $count_child_payments = count($payment->child_payments);
            @endphp
            @include('contact.partials.payment_row', compact('payment', 'count_child_payments', 'payment_types'))

            @if($count_child_payments > 0)
                @foreach($payment->child_payments as $child_payment)
                    @include('contact.partials.payment_row', ['payment' => $child_payment, 'count_child_payments' => 0, 'payment_types' => $payment_types, 'parent_payment_ref_no' => $payment->payment_ref_no])
                @endforeach
            @endif
        @empty
            <tr>
                <td colspan="6" class="text-center">@lang('purchase.no_records_found')</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="text-right" style="width: 100%;" id="contact_payments_pagination">{{ $payments->links() }}</div>



<script>
$(document).ready(function () {
    let shadowTable = $('#contact_payments_shadow').DataTable({
        language: {
            decimal: ",",
            thousands: ".",
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros en total)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún dato disponible en esta tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            }
        },
        paging: true,
        searching: true
    });

    // 🔹 Mover controles de DataTables al DOM de la tabla original
    let dtContainer = $(shadowTable.table().container());

    // Buscador arriba de la tabla original
    $('#contact_payments_table').before(dtContainer.find('.dataTables_filter'));

    // Paginación e info abajo de la tabla original
    $('#contact_payments_table').after(dtContainer.find('.dataTables_paginate'));
    $('#contact_payments_table').after(dtContainer.find('.dataTables_info'));

    // 🔹 Sincronizar búsqueda
    shadowTable.on('search.dt', function () {
        let search = shadowTable.search().toLowerCase();
        $('#contact_payments_table tbody tr').each(function () {
            let texto = $(this).text().toLowerCase();
            $(this).toggle(texto.indexOf(search) > -1);
        });
    });

    // 🔹 Sincronizar paginación
    shadowTable.on('draw.dt', function () {
        let pageInfo = shadowTable.page.info();
        $('#contact_payments_table tbody tr').hide()
            .slice(pageInfo.start, pageInfo.end).show();
    }).trigger('draw.dt'); // primera vez
});
</script>

<!-- <script>
$(document).ready(function () {
    $('#contact_payments_table').DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });
});
</script> -->