<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="" id="form_status" method="POST">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> Pasar Operación a {{ ($operation->status==1)?'Pendiente':'Entregado' }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-7">
                    <b>Operación Nro:</b> {{ $operation->number }}<br>
                    <b>Fecha:</b> {{ $operation->date->format('d/m/Y') }}<br>
                    <b>Cliente:</b> {{ $operation->customer->full_name }}<br>
                    <b>Empresa emisora:</b> {{ $operation->company }}<br>
                    <b>Socio Comercial:</b> {{ $operation->partner->user->full_name }}<br>
                </div>
                <div class="form-group col-sm-5">
                    <b>Folio:</b> {{ $operation->folio }}<br>
                    <b>Total Facturado:</b> {{ session('coin') }}{{ money_fmt($operation->amount) }}<br>
                    <b>Margen Total:</b> {{ session('coin') }}{{ money_fmt($operation->customer_profit) }}<br>
                    <b>Margen Socio:</b> {{ session('coin') }}{{ money_fmt($operation->partner_profit) }}<br>
                    <b>Margen HM:</b> {{ session('coin') }}{{ money_fmt($operation->hm_profit) }}<br>
                </div>
                @if($operation->notes)
                    <div class="form-group col-sm-12">
                        <b>Notas:</b> {{ $operation->notes }}
                    </div>
                @endif
                <div class="form-group col-sm-6">  
                  <label>Mensajero *</label>
                  {{ Form::select('user', $users, $operation->user_id, ['id'=>'user', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <div class="i-checks">
                        {!! Form::checkbox('notification', null, false, ['id'=>'notification', 'class'=>'i-checks']) !!} <label>Enviar notificacion al mensajero</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_status" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<script>
    
$("#btn_status").on('click', function(event) {    
    var validator = $("#form_status").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_status').attr('disabled', true);
            var id = {{ $operation->id }};
            $.ajax({
              url: `{{URL::to("operations.status")}}/${id}`,
              type: 'POST',
              data: {
                _token: "{{ csrf_token() }}",
                user:$('#user').val(),
                notification:$('#notification').is(":checked")?1:0, 
              },
            })
            .done(function(response) {
                $('#btn_status').attr('disabled', false);
                $('#modalStatus').modal('toggle');
                $('#operations-table').DataTable().draw(false);
                toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
            })
            .fail(function(response) {
            if(response.status == 422){
              var errorsHtml='';
              $.each(response.responseJSON.errors, function (key, value) {
                errorsHtml += '<li>' + value[0] + '</li>'; 
              });          
              toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 3000);
            }else{
              toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
            }  
        });
    }
});


$(document).ready(function() {
    
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
            
    $("#user").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalStatus .modal-content'),
        width: '100%'
    });
});
</script>