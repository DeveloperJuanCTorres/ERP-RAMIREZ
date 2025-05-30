<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\LoanController::class, 'update'], [$loan->id]), 'method' => 'PUT', 'id' => 'loan_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Editar préstamo</h4>
    </div>

    <div class="modal-body">
        <div class="form-group">
            {!! Form::label('name', 'Usuario:*') !!}
            <select class="form-control" name="user_id" id="user_id">
                <option value="0">Seleccionar</option>
                @foreach($users as $user)
                <option value="{{$user->id}}" {{ (isset($loan) && $loan->user_id == $user->id) ? 'selected' : '' }}>{{$user->first_name . ' ' . $user->last_name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            {!! Form::label('amount', 'Monto del préstamo:*') !!}
            {!! Form::number('amount', $loan->amount, ['class' => 'form-control','placeholder' => 'Monto']); !!}
        </div>

        <div class="form-group">
            {!! Form::label('description', 'Tipo de pago:*') !!}
            <select class="form-control" name="type" id="type">
                <option value="0" selected>Seleccionar</option>
                <option value="dia" {{ (isset($loan) && $loan->type == 'dia') ? 'selected' : '' }}>Diario</option>
                <option value="semana" {{ (isset($loan) && $loan->type == 'semana') ? 'selected' : '' }}>Semanal</option>
                <option value="mes" {{ (isset($loan) && $loan->type == 'mes') ? 'selected' : '' }}>Mensual</option>
            </select>
        </div>
        <div class="form-group">
            {!! Form::label('time', 'Tiempo:*') !!}
            {!! Form::number('time', $loan->time, ['class' => 'form-control','placeholder' => 'Tiempo de pago']); !!}
        </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->