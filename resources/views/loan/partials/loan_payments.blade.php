<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\LoanPaymentsController::class, 'update'], $loanPayments->id), 'method' => 'post', 'id' => 'loan_payments_form' ]) !!}


    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Agregar pago de cuota</h4>
    </div>

    <div class="modal-body">        
        <div class="row">
            <div class="col-md-6 ms-auto">
                <div class="form-group">
                {!! Form::label('date', 'Fecha de pago:*') !!}
                {!! Form::date('date_pay', $loanPayments->date_pay, ['class' => 'form-control']); !!}
                </div>
            </div>
            <div class="col-md-6 ms-auto">
                <div class="form-group">
                {!! Form::label('amount_pay', 'Monto a pagar:') !!}
                {!! Form::number('amount_pay', $loanPayments->amount_pay, ['class' => 'form-control']); !!}
                </div>
            </div>
        </div>
        
        <div class="form-group">
            {!! Form::label('method_pay', 'Método de pago:*') !!}
            <select class="form-control" name="method_pay" id="method_pay">
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="yape">Yape</option>
                <option value="plin">Plin</option>
            </select>
        </div>
        <div class="form-group">
            {!! Form::label('observation', 'Observacion:') !!}
            {!! Form::textarea('observation', $loanPayments->observation, ['class' => 'form-control', 'rows' => 3]); !!}
        </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->