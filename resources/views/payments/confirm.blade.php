<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
<form action="#" id="form_confirm_payment" method="POST">
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    {!! Form::hidden('payment_id', $payment->id, ['id'=>'payment_id']) !!}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Confirmar Pago</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-sm-6">
                <b>Fecha:</b> {{ $payment->date->format('d/m/Y') }}<br>
                <b>Cuenta:</b> {{ $payment->account->aliase }}<br>
                <b>Metodo de Pago:</b> {{ $payment->payment_method_description }}<br>
                <b>REF:</b> {{ $payment->reference }}
            </div>
            <div class="form-group col-sm-6">
                <div style="font-size: 16px;margin-bottom: 2mm;"><b>MONTO:</b> {{ session('coin') }} {{ money_fmt($payment->amount) }}</div>
                <b>Propiedad:</b> {{ $payment->property->number }}<br>
                <b>Propietario:</b> {{ ($payment->property->user_id)?$payment->property->user->name:'' }}
            </div>
            <div class="form-group col-sm-12">
                <b>Concepto:</b> {{ $payment->concept }}                
            </div>
            <div class="form-group col-sm-12">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th width="70%">Cuotas pagadas</th>
                            <th width="30%">Monto pagado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payment->fees()->get() as $fee)
                            <tr>
                                <td>
                                  {{ $fee->concept }} <b>{{ session('coin') }} {{ money_fmt($fee->amount) }}</b>
                                </td>
                                <td>{{ session('coin') }} {{ money_fmt($fee->pivot->amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="form-group col-sm-12">
                <div class="i-checks">
                  {!! Form::radio('resp', 'A',  false) !!} Aprobado
                  {!! Form::radio('resp', 'R',  false) !!} Rechazado
                </div>
            </div>
            <div class="form-group col-sm-12">
                <label>Observaciones</label><small> Máx. 150 caracteres</small>
                {!! Form::textarea('observations', null, ['id'=>'observations', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui alguna observación de interés ...', 'maxlength'=>'150']) !!}
            </div>
            @if($payment->property->user_id)
                <div class="form-group col-sm-12">
                    <b>Atención:</b> Se le enviará una notificación a {{ $payment->property->user->name }} a su dirección de correo {{ $payment->property->user->email }}.
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
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<script>

$("#btn_submit").on('click', function(event) {    
    var validator = $("#form_confirm_payment").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $(this).attr('disabled', true);
        var id={{ $payment->id }};
        $.ajax({
          url: `{{URL::to("payments.confirm")}}/${id}`,
          type: 'POST',
          data: {
            _token: "{{ csrf_token() }}",
            resp:$('input[name="resp"]:checked').val(),
            observations: $('#observations').val()
          },
        })
        .done(function(response) {
          $(this).attr('disabled', false);
          $('#modalConfirmPayment').modal('toggle');
          $('#payments-table').DataTable().draw();
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

        })
        .fail(function(response) {
          $('#modalConfirmPayment').modal('toggle');
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

    $('#observation').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });
});

</script>