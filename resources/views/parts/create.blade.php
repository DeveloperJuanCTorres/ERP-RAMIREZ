<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => route('parts.store'), 'method' => 'post', 'id' => 'part_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Registro de contrato</h4>
    </div>

    <div class="modal-body">
      <!-- <input type="hidden" class="payment_row_index" value="row_index"> -->
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
          {!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'name' => 'business_locations', 'id' => 'business_locations', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
        </div>

        <div class="form-group">
            {!! Form::label('name', 'Proveedor:*') !!}
            <select class="form-control" name="proveedor_id" id="proveedor_id">
                <option value="0">Seleccionar</option>
                @foreach($proveedores as $proveedor)
                <option value="{{$proveedor->id}}">{{$proveedor->supplier_business_name . $proveedor->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            {!! Form::label('name', 'Cliente:*') !!}
            <select class="form-control" name="cliente_id" id="cliente_id">
                <option value="0">Seleccionar</option>
                @foreach($clientes as $cliente)
                <option value="{{$cliente->id}}">{{$cliente->supplier_business_name . $cliente->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            {!! Form::label('name', 'Producto:*') !!}
            <select class="form-control" name="product_id" id="product_id">
                <option value="0">Seleccionar</option>
                @foreach($products as $product)
                <option value="{{$product->id}}">{{$product->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('name', 'Horas mínimas:*') !!}
              <input class="form-control" name="horas_minimas" id="horas_minimas"></input>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('name', 'Dias M. Por semana:*') !!}
              <input class="form-control" name="dias_minimas" id="dias_minimas"></input>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('name', 'Importe por hora:*') !!}
              <input class="form-control" name="importe" id="importe" rows="3"></input>
            </div>
          </div>
        </div>
               
        <div class="form-group">
            {!! Form::label('name', 'Observación:*') !!}
            <textarea class="form-control" name="observations" id="observations" rows="3"></textarea>
        </div>       
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
