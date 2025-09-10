<div style="margin-bottom: 20px;">
    <label for="filtro_fecha">Filtrar por fecha:</label>
    <input type="date" id="filtro_fecha" class="form-control" style="width: 200px; display:inline-block;">
</div>

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
document.getElementById('filtro_fecha').addEventListener('change', function () {
    let fechaFiltro = this.value; // yyyy-mm-dd
    let filas = document.querySelectorAll('#contact_payments_table tbody tr');

    filas.forEach(fila => {
        let celdaFecha = fila.querySelector('td[data-fecha]');

        if (!celdaFecha) {
            // si no hay celda de fecha en esta fila (ej: child_payment),
            // mantenemos visible SOLO si la fila padre ya está visible
            if (fechaFiltro === '') {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
            return;
        }

        let fechaCelda = celdaFecha.getAttribute('data-fecha');

        if (!fechaFiltro || fechaFiltro === fechaCelda) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
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