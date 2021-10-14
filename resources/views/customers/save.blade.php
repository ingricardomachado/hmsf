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
    
    <form action="#" id="form_customer" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('customer_id', ($customer->id)?$customer->id:0, ['id'=>'customer_id']) !!}
        {!! Form::hidden('cell', ($customer->id)?$customer->cell:null, ['id'=>'cell']) !!}
        @if($customer->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($customer->id) ? "Modificar Cliente": "Registrar Cliente" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">  
                  <label>Socio Comercial *</label>
                  {{ Form::select('partner', $partners, $customer->partner_id, ['id'=>'partner', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('name', $customer->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo *</label>
                    {!! Form::email('email', $customer->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Celular *</label>
                    {!! Form::tel('national_cell', $customer->cell, ['id'=>'national_cell', 'class'=>'form-control', 'placeholder'=>'', 'required']) !!}
                    <span id="error-msg" style="color:#cc5965;font-weight:bold"></span>
                </div>
                <div class="form-group col-sm-6">
                    <label>Comisión % *</label>
                    <input type="number" name="tax" id="tax" value="{{ $customer->tax }}" class="form-control" min="1" max=100 step="0.01" placeholder="" required/>
                </div>
                <div class="form-group col-sm-6" style="margin-top:7mm">
                    <div class="i-checks">
                        <label>{!! Form::checkbox('has_contract', null,  false, ['id'=>'has_contract']) !!} Tiene contrato</label>
                    </div>
                </div>
                <div class="form-group col-sm-6" id="div_contract" style="display:{{ ($customer->contract)?'solid':'none' }}">
                    <label>Contrato *</label><small>Max. 20 caracteres</small>
                    {!! Form::text('contract', $customer->contract, ['id'=>'contract', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="customer_CRUD({{ ($customer->id)?$customer->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
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


$('#has_contract').on('ifChanged', function(event){
  (event.target.checked)?$('#div_contract').show():$('#div_contract').hide();
});

$(document).ready(function() {

    ('{{ $customer->id && $customer->contract }}'!='')?$('#has_contract').iCheck('check'):'';

    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    $("#partner").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalCustomer .modal-content'),
        width: '100%'
    });
});
</script>