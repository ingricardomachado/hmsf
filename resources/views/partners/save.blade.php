<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- International Phones -->
<link href="{{ URL::asset('js/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que input del cell sea 100% width-->
<style type="text/css">
    .iti { width: 100%; }
</style>
    
    <form action="#" id="form_partner" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('partner_id', ($partner->id)?$partner->id:0, ['id'=>'partner_id']) !!}
        {!! Form::hidden('cell', ($partner->id)?$partner->cell:null, ['id'=>'cell']) !!}
        @if($partner->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($partner->id) ? "Modificar Socio Comercial": "Registrar Socio Comercial" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('first_name', ($partner->id)?$partner->user->first_name:null, ['id'=>'first_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Apellido *</label>
                    {!! Form::text('last_name', ($partner->id)?$partner->user->last_name:null, ['id'=>'last_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo *</label><small> Será su nombre de usuario</small>
                    {!! Form::email('email', ($partner->id)?$partner->user->email:null, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Celular</label>
                    {!! Form::tel('national_cell', $partner->cell, ['id'=>'national_cell', 'class'=>'form-control', 'placeholder'=>'']) !!}
                    <span id="error-msg" style="color:#cc5965;font-weight:bold"></span>
                </div>
                <div class="form-group col-sm-6">
                    <label>Comisión % *</label>
                    <input type="number" name="tax" id="tax" value="{{ $partner->tax }}" class="form-control" min="0" max=100 step="0.01" placeholder="" required/>
                </div>
                <div class="form-group col-sm-6">  
                  <label>Estado *</label>
                  {{ Form::select('state', $states, ($partner->id)?$partner->state_id:null, ['id'=>'state', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-12" style="display:{{ ($partner->id)?'solid':'none' }}">
                    <div class="i-checks">
                        <label>{!! Form::checkbox('change_password', null,  false, ['id'=>'change_password']) !!} Cambiar la contraseña</label>
                    </div>
                </div>
                <div class="form-group col-sm-6" id='div_password' style='display:{{ ($partner->id)?'none':'solid' }};'>
                    <label>Contraseña *</label><small> De 6 a 15 caracteres.</small>
                    <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="6" maxlength="15" required>
                </div>
                <div class="form-group col-sm-6" id='div_password_confirmation' style='display:{{ ($partner->id)?'none':'solid' }};'>
                    <label>Confirmar Contraseña *</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="" minlength="6" maxlength="15" required>
                </div>
                <div class="form-group col-sm-6" id="div_notification" style="display:{{ ($partner->id)?'none':'solid' }}">
                    <div class="i-checks">
                        <label>{!! Form::checkbox('notification', null,  false, ['id'=>'notification']) !!} <span id="label_notification">Notificar por correo al socio.</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="partner_CRUD({{ ($partner->id)?$partner->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- International Phones --> 
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/intlTelInput.js') }}"></script>
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/utils.js') }}"></script>
<script>

$('#change_password').on('ifChanged', function(event){
  if(event.target.checked){
    $('#div_password').show();
    $('#div_password_confirmation').show();
    $('#label_notification').html('Notificar el cambio de contraseña'); 
    $('#div_notification').show();    
  }else{
    $('#div_password').hide();
    $('#div_password_confirmation').hide();
    $('#label_notification').html('Notificar por correo al propietario');
    $('#div_notification').hide();    
  }  
});

var input = document.querySelector("#national_cell"),
output = document.querySelector("#error-msg");

var iti = window.intlTelInput(input, {
  initialCountry:'mx',
  onlyCountries: ['mx'],
  nationalMode: true,
  utilsScript: "../../build/js/utils.js?1590403638580" // just for formatting/placeholders etc
});

var handleChange = function() {
    var text="";
    (iti.isValidNumber()) ? $('#cell').val(iti.getNumber()) : text="Introduzca un número válido";
    var textNode = document.createTextNode(text);
    output.innerHTML = "";
    output.appendChild(textNode);
};

// listen to "keyup", but also "change" to update when the user selects a country
input.addEventListener('change', handleChange);
input.addEventListener('keyup', handleChange);

$(document).ready(function() {
    
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    $("#state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalPartner .modal-content'),
        width: '100%'
    });
});

</script>