<!-- International Phones -->
<link href="{{ URL::asset('js/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que input del cell sea 100% width-->
<style type="text/css">
    .iti { width: 100%; }
</style>
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
    
    <form action="" id="form_condominium" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($condominium->id)?$condominium->id:0, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('cell', ($condominium->id)?$condominium->cell:null, ['id'=>'cell']) !!}
        @if($condominium->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-building-o" aria-hidden="true"></i> {{ ($condominium->id) ? "Modificar Condominio": "Registrar Condominio" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-12">
                    <label>Nombre *</label>
                    {!! Form::text('name', $condominium->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>País *</label>
                    {{ Form::select('country', $countries, $condominium->country_id, ['id' => 'country', 'class'=>'form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Estado *</label>
                    {{ Form::select('state', $states, $condominium->state_id, ['id' => 'state', 'class'=>'form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Tipo de condominio *</label>
                    {{ Form::select('type', ['C' =>'Comercial', 'R' => 'Residencial'], ($condominium->id)?$condominium->type:'R', ['id' => 'type', 'class'=>'form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Tipo de propiedades *</label>
                    {{ Form::select('property_type', $property_types, $condominium->property_type_id, ['id' => 'property_type', 'class'=>'form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                  <label>Max. Nro de propiedades *</label>
                  <input id="max_properties" class="form-control" name="max_properties" value="{{ $condominium->max_properties }}" placeholder="" min="1" max="500" required="required" type="number">
                </div>
                <div class="form-group col-sm-6">
                    <label>Contacto *</label>
                    {!! Form::text('contact', $condominium->contact, ['id'=>'contact', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo electrónico *</label>
                    {!! Form::email('email', $condominium->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'Ej. correo@dominio.com', 'minlength'=>'3', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Celular *</label>
                    {!! Form::tel('national_cell', $condominium->cell, ['id'=>'national_cell', 'class'=>'form-control', 'placeholder'=>'', 'required']) !!}
                    <span id="error-msg" style="color:#cc5965;font-weight:bold"></span>
                </div>
                <div class="form-group col-sm-6">
                    <label>Teléfono</label>
                    {!! Form::text('phone', $condominium->phone, ['id'=>'phone', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'10']) !!}
                </div>
                <div class="form-group col-sm-6" style="display:{{ ($condominium->demo)?'solid':'none' }}">
                    <label>Días restantes *</label> <small>Max 30 días</small>
                    {!! Form::number('remaining_days', $condominium->remaining_days, ['id'=>'remaining_days', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'min'=>'0', 'max'=>'30', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="condominium_CRUD({{ ($condominium->id)?$condominium->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- International Phones --> 
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/intlTelInput.js') }}"></script>
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/utils.js') }}"></script>
<script>

var input = document.querySelector("#national_cell"),
output = document.querySelector("#error-msg");

var iti = window.intlTelInput(input, {
  initialCountry:'{{ ($condominium->id)?$condominium->country->iso:'gt' }}',
  onlyCountries: ['ar', 'bo', 'br', 'cl', 'co', 'cr', 'cu', 'sv', 'ec', 'es', 'gt', 'hn', 'mx', 'ni', 'pa', 'py', 'pe', 'pr', 'do', 'uy', 've'],
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

$("#country").change( event => {
  url = `{{URL::to('get_states/')}}/${event.target.value}`;                    
  $.get(url, function( response){
    $("#state").empty();
    response.forEach(element => {
      $("#state").append(`<option value=${element.id}> ${element.name} </option>`);
    });
    $('#state').val(null).trigger('change');
  });
  url = `{{URL::to('countries')}}/${event.target.value}`;                    
  $.get(url, function(response){
    iti.setCountry(response.data.iso);  
  });  
});

$(document).ready(function() {
    $('#plate').focus();

    $("#country").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modalCondominium .modal-content'),
        width: '100%'
    });

    $("#state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modalCondominium .modal-content'),
        width: '100%'
    });

    $("#property_type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modalCondominium .modal-content'),
        width: '100%'
    });

    $("#type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modalCondominium .modal-content'),
        width: '100%'
    });

    $('#notes').maxlength({
    warningClass: "small text-muted",
    limitReachedClass: "small text-muted",
    placement: "top-right-inside"
    });  

});

</script>