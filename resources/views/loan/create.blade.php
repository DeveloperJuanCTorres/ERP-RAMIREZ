<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\LoanController::class, 'store']), 'method' => 'post', 'id' => $quick_add ? 'quick_add_loan_form' : 'loan_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Agregar préstamo interno</h4>
    </div>

    <div class="modal-body">
      <input type="hidden" class="payment_row_index" value="{{ $row_index}}">
      @php
        $col_class = 'col-md-6';
        if(!empty($accounts)){
          $col_class = 'col-md-4';
        }
        $readonly = $payment_line['method'] == 'advance' ? true : false;
      @endphp

        @if(count($business_locations) == 1)
					@php 
						$default_location = current(array_keys($business_locations->toArray())) 
					@endphp
				@else
					@php $default_location = null; @endphp
				@endif
        <div class="form-group">
          {!! Form::label('location_id', __('purchase.business_location').':*') !!}
          {!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
        </div>

        <div class="form-group">
            {!! Form::label('name', 'Usuario:*') !!}
            <select class="form-control" name="user_id" id="user_id">
                <option value="0">Seleccionar</option>
                @foreach($users as $user)
                <option value="{{$user->id}}">{{$user->first_name . $user->last_name}}</option>
                @endforeach
            </select>
        </div>

        <div class="row">
          <div class="col-md-6 ms-auto">
            <div class="form-group">
              {!! Form::label('amount', 'Monto del préstamo:*') !!}
              {!! Form::number('amount', null, ['class' => 'form-control','placeholder' => 'Monto']); !!}
          </div>
          </div>
          <div class="col-md-6 ms-auto">
            <div class="form-group">
              {!! Form::label('description', 'Tipo de pago:*') !!}
              <select class="form-control" name="type" id="type">
                  <option value="0" selected>Seleccionar</option>
                  <option value="dia">Diario</option>
                  <option value="semana">Semanal</option>
                  <option value="mes">Mensual</option>
              </select>
            </div>
          </div>
        </div>
                
        <div class="row">
          <div class="col-md-6 ms-auto">
            <div class="form-group">
              {!! Form::label('time', 'Tiempo:*') !!}
              {!! Form::number('time', null, ['class' => 'form-control','placeholder' => 'Tiempo de pago']); !!}
            </div>
          </div>
          <div class="col-md-6 ms-auto">
            <div class="form-group">
              {!! Form::label('tax', 'Interés:') !!}
              {!! Form::number('tax', null, ['class' => 'form-control','placeholder' => 'Interés en %']); !!}
            </div>
          </div>
        </div>
        
        @php
          $row_index = 0;
        @endphp
        <div class="form-group">
          {!! Form::label("method_$row_index" , __('lang_v1.payment_method') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fas fa-money-bill-alt"></i>
            </span>
            @php
              $_payment_method = empty($payment_line['method']) && array_key_exists('cash', $payment_types) ? 'cash' : $payment_line['method'];
            @endphp
            {!! Form::select("payment[$row_index][method]", $payment_types, $_payment_method, ['class' => 'form-control col-md-12 payment_types_dropdown', 'required', 'id' => !$readonly ? "method_$row_index" : "method_advance_$row_index", 'style' => 'width:100%;', 'disabled' => $readonly]); !!}

            @if($readonly)
              {!! Form::hidden("payment[$row_index][method]", $payment_line['method'], ['class' => 'payment_types_dropdown', 'required', 'id' => "method_$row_index"]); !!}
            @endif
          </div>
        </div>
        <div class="form-group @if($readonly) hide @endif">
          {!! Form::label("account_$row_index" , __('lang_v1.payment_account') . ':') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fas fa-money-bill-alt"></i>
            </span>
            {!! Form::select("payment[$row_index][account_id]", $accounts, !empty($payment_line['account_id']) ? $payment_line['account_id'] : '' , ['class' => 'form-control select2 account-dropdown', 'id' => !$readonly ? "account_$row_index" : "account_advance_$row_index", 'style' => 'width:100%;', 'disabled' => $readonly]); !!}
          </div>
        </div>
        
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
