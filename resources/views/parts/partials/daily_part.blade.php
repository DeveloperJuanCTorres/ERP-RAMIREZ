<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\DiaryPartController::class, 'storeDailyPart'], ['id' => $part_id]), 'method' => 'post', 'part_id' => 'daily_part_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Agregar parte diario</h4>
    </div>

    <div class="modal-body">
      <!-- <input type="hidden" class="payment_row_index" value=""> -->
                     

        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
                {!! Form::label('conductor', 'Conductor:*') !!}
                {!! Form::text('conductor', null, ['class' => 'form-control','placeholder' => 'Nombre del conductor']); !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('dni', 'DNI:*') !!}
                {!! Form::number('dni', null, ['class' => 'form-control','placeholder' => '# DNI']); !!}
            </div>
          </div>
        </div>           

        <div class="row">
          <div class="col-md-8 ms-auto">
            <div class="form-group">
              {!! Form::label('zona_trabajo', 'Zona de trabajo:*') !!}
              {!! Form::text('zona_trabajo', null, ['class' => 'form-control','placeholder' => 'zona de trabajo']); !!}
          </div>
          </div>
          <div class="col-md-4 ms-auto">
            <div class="form-group">
              {!! Form::label('Combustible', 'Combustible:*') !!}
              {!! Form::number('combustible', null, ['class' => 'form-control','placeholder' => 'En galones']); !!}
            </div>
          </div>
        </div> 

        <div class="row">
          <div class="col-md-6 ms-auto">
            <div class="form-group">
              {!! Form::label('h_inicio', 'Horometro inicial:*') !!}
              {!! Form::number('h_inicio', null, ['class' => 'form-control','placeholder' => 'horometro inicial']); !!}
          </div>
          </div>
          <div class="col-md-6 ms-auto">
            <div class="form-group">
              {!! Form::label('h_final', 'Horometro final:*') !!}
              {!! Form::number('h_final', null, ['class' => 'form-control','placeholder' => 'horometro final']); !!}
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>