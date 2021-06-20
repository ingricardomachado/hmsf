<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="#" id="form_confirm_reservation" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('reservation_id', $reservation->id, ['id'=>'reservation_id']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Confirmar Reservación</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">  
                    <div><b>Propiedad:</b> {{ $reservation->property->number }} {{ ($reservation->property->user_id)?$reservation->property->user->name:'' }}</div>
                    <div><b>Instalación:</b> {{ $reservation->facility->name }}</div>
                    @if($reservation->all_day)
                        <div><b>Fecha: </b>{{ $reservation->facility->start->format('d/m/Y') }} Todo el día</div>
                    @else
                        <div><b>Fecha: </b>{{ $reservation->facility->start->format('d/m/Y') }} Desde {{ $reservation->start->format('g:i a') }} Hasta {{ $reservation->end->format('g:i a') }} ({{ $reservation->tot_hours }} horas)</div>
                    @endif
                </div>
                <div class="form-group col-sm-6">
                    @if($reservation->rent)
                        <div><b>Costo total por alquiler:</b> {{ session('coin') }} {{ money_fmt($reservation->amount) }}</div>
                    @endif
                    <div><b>Notas:</b> {{ $reservation->notes }}</div>
                </div>
                <div class="form-group col-sm-12">
                    <div class="i-checks">
                      {!! Form::radio('resp', 'A',  false) !!} Aprobada
                      {!! Form::radio('resp', 'R',  false) !!} Rechazada
                    </div>
                </div>
                <div class="form-group col-sm-12">
                    <label>Observaciones</label><small> Máx. 150 caracteres</small>
                    {!! Form::textarea('observations', null, ['id'=>'observations', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui alguna observación de interés ...', 'maxlength'=>'150']) !!}
                </div>
                <div class="form-group col-sm-12" id="div_create_fee" style="display: none">
                    <div class="i-checks">
                        {!! Form::checkbox('create_fee', null, false, ['id'=>'create_fee', 'class'=>'i-checks']) !!} <label>Generar cuota</label><small> Haga click si desea generar una cuota por {{ session('coin') }} {{ money_fmt($reservation->amount) }}.</small>
                    </div>
                </div>
                <div id="div_fee" style="display: none">
                    <div class="form-group col-sm-4">
                        <label>Fecha de vecimiento *</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            {{ Form::text ('due_date', null, ['id'=>'due_date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                        </div>
                    </div>
                    <div class="form-group col-sm-8">
                        <label>Concepto</label><small> Máx. 150 caracteres</small>
                        {!! Form::text('concept', 'Alquiler de '.$reservation->facility->name, ['id'=>'concept', 'class'=>'form-control', 'type'=>'text', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui un concepto que describa la cuota a cobrar ...', 'maxlength'=>'150']) !!}
                    </div>                    
                </div>
                @if($reservation->property->user_id)
                    <div class="form-group col-sm-12">
                        <b>Atención:</b> Se le enviará una notificación a {{ $reservation->property->user->name }} a su dirección de correo {{ $reservation->property->user->email }}.
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" class="btn btn-sm btn-primary">Aceptar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<script>

$("[name='resp']").on('ifChecked', function(event){
    if(event.target.value=='A'){
        $('#div_create_fee').show();
    }else{
        $('#create_fee').iCheck('uncheck');
        $('#div_create_fee').hide();
    }
});

$('#create_fee').on('ifChanged', function(event){
  if(event.target.checked){
    $('#div_fee').show();
  }else{
    $('#div_fee').hide();
  }  
});

$("#btn_submit").on('click', function(event) {    
    var validator = $("#form_confirm_reservation").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $(this).attr('disabled', true);
        var id={{ $reservation->id }};
        $.ajax({
          url: `{{URL::to("reservations.confirm")}}/${id}`,
          type: 'POST',
          data: {
            _token: "{{ csrf_token() }}",
            resp:$('input[name="resp"]:checked').val(),
            observations: $('#observations').val(),
            create_fee:$('#create_fee').is(":checked")?1:0,
            due_date:$('#due_date').val(),
            concept:$('#concept').val()
          },
        })
        .done(function(response) {
          $(this).attr('disabled', false);
          $('#modalConfirmReservation').modal('toggle');
          $('#reservations-table').DataTable().draw();
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

        })
        .fail(function(response) {
          $('#modalConfirmReservation').modal('toggle');
          toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
        });
    }
});

$(document).ready(function() {
    
    // iCheck
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });

    $('#observations').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  

    var timestamp = Date.parse("{{ $reservation->start->addDay(1) }}");
    var date = new Date(timestamp);
    var lastDayOfMonth = new Date(date.getFullYear(), date.getMonth()+1, 0);    
    $('#due_date').datepicker({
        startDate: date,
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
    });
    $("#due_date").datepicker("setDate", lastDayOfMonth);    


});

</script>