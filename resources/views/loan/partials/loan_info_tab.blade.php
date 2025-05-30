
<h3 class="profile-username">
    <i class="fas fa-user-tie"></i>
    {{ $user->first_name . ' ' . $user->last_name }}
    <small>
        Usuario interno
    </small>
</h3><br>
<strong><i class="fa fa-envelope margin-r-5"></i> Correo electrónico</strong>
<p class="text-muted">
    {{ $user->email }}
</p>
<br>
<h3 class="profile-username">
    <i class="fas fa-money-bill"></i>
    S/. {{ $loan->amount + $loan->amount*$loan->tax/100 }}
    <small>
        Total de préstamo a pagar
    </small>
</h3>
