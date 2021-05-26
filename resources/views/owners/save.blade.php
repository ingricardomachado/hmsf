<!-- International Phones -->
<link href="{{ URL::asset('js/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet">
<!-- Magicsuggest -->
<link rel="stylesheet" type="text/css" href="{{ URL::asset('js/plugins/magicsuggest/magicsuggest-min.css') }}">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
    
    <form action="{{url('owners/'.$owner->id)}}" id="form_owner" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('owner_id', ($owner->id)?$owner->id:0, ['id'=>'owner_id']) !!}
        {!! Form::hidden('hdd_cell', ($owner->id)?$owner->cell:null, ['id'=>'hdd_cell']) !!}
        @if($owner->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($owner->id) ? "Modificar Propietario": "Registrar Propietario" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('name', $owner->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo *</label><small> Será su nombre de usuario</small>
                    {!! Form::email('email', $owner->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Celular</label>
                    {!! Form::tel('cell', $owner->cell, ['id'=>'cell', 'class'=>'form-control', 'placeholder'=>'']) !!}
                    <span id="error-msg" style="color:#cc5965;font-weight:bold"></span>
                </div>

                <div class="form-group col-sm-6">
                    <label>Teléfono</label>
                    {!! Form::text('phone', $owner->phone, ['id'=>'phone', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'30']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Propiedades *</label><small> Puede seleccionar varias propiedades. Sólo se muestran las disponibles</small>
                    {!! Form::text('properties', null, ['id'=>'properties', 'class'=>'form-control', 'type'=>'text', 'required']) !!}
                    <span id="msj_error_properties" style="color:#cc5965;font-weight:bold"></span>
                </div>
                <div class="form-group col-sm-12">
                    <div class="i-checks">
                        <label>{!! Form::checkbox('committee', null,  $owner->committee, ['id'=>'committee']) !!} Pertenece al comité</label>
                    </div>
                </div>      
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="owner_CRUD({{ ($owner->id)?$owner->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- International Phones --> 
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/intlTelInput.js') }}"></script>
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/utils.js') }}"></script>
<!-- Magicsuggest -->
<script type="text/javascript" src="{{ URL::asset('js/plugins/magicsuggest/magicsuggest.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- Jquery Validate -->
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/messages_es.js') }}"></script>
<script src="{{ URL::asset('js/plugins/nit_validation/calcularDigitoVerificacion.js') }}"></script>
<script>

// Magicsuggest
var ms_properties=$('#properties').magicSuggest({
    placeholder: "Seleccione las propiedades",
    required:true,
    maxSelection: null,
    allowFreeEntries: false,
    //Todos los valores del select
    method:'get',
    data: {!! json_encode($properties) !!},
    //Valores iniciales del select
    value: {!! json_encode($owner_properties) !!},
    valueField: 'id',
    displayField: 'number',
});

var input = document.querySelector("#cell"),
output = document.querySelector("#error-msg");

var iti = window.intlTelInput(input, {
  initialCountry:"{{ session()->get('condominium')->country->iso }}",
  onlyCountries: ['ar', 'bo', 'br', 'cl', 'co', 'cr', 'cu', 'sl', 'ec', 'es', 'gt', 'hn', 'mx', 'ni', 'pa', 'py', 'pe', 'pr', 'do', 'uy', 've'],
  nationalMode: true,
  utilsScript: "../../build/js/utils.js?1590403638580" // just for formatting/placeholders etc
});

var handleChange = function() {
    var text="";
    (iti.isValidNumber()) ? $('#hdd_cell').val(iti.getNumber()) : text="Introduzca un número válido";
    var textNode = document.createTextNode(text);
    output.innerHTML = "";
    output.appendChild(textNode);
};

// listen to "keyup", but also "change" to update when the user selects a country
input.addEventListener('change', handleChange);
input.addEventListener('keyup', handleChange);


$(document).ready(function() {
    $('#name').focus();
    
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

});

</script>