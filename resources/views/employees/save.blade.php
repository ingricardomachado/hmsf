<!-- International Phones -->
<link href="{{ URL::asset('js/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que input del cell sea 100% width-->
<style type="text/css">
    .iti { width: 100%; }
</style>
<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
    <form action="" id="form_employee" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($employee->id)?$employee->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('employee_id', ($employee->id)?$employee->id:0, ['id'=>'employee_id']) !!}
        {!! Form::hidden('cell', ($employee->id)?$employee->cell:null, ['id'=>'cell']) !!}
        @if($employee->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($employee->id) ? "Modificar Empleado" : "Registrar Empleado" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <!-- columna 1 -->
                <div class="col-sm-6">                            
                    <div class="form-group">
                        <label>Avatar </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) Recomendación Máx. 200px por 200px</small>
                        <input id="avatar" name="avatar" type="file">
                    </div>
                </div>
                <!-- columna 2 -->
                <div class="row">
                    <div class="col-sm-6">              
                        <div class="form-group col-sm-12">
                            <label>Nombre *</label>
                            {!! Form::text('name', $employee->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Cargo *</label>
                            {!! Form::text('position', $employee->position, ['id'=>'position', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Celular *</label>
                            {!! Form::tel('national_cell', $employee->cell, ['id'=>'national_cell', 'class'=>'form-control', 'placeholder'=>'', 'required']) !!}
                            <span id="error-msg" style="color:#cc5965;font-weight:bold"></span>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Nro Identificación</label>
                            {!! Form::text('NIT', $employee->NIT, ['id'=>'NIT', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20']) !!}
                        </div>
                    </div>
                </div>
                    <div class="form-group col-sm-6">
                        <label>Teléfono</label>
                        {!! Form::text('phone', $employee->phone, ['id'=>'phone', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'10']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Correo electrónico</label>
                        {!! Form::email('email', $employee->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'Ej. correo@dominio.com', 'minlength'=>'3', 'maxlength'=>'50']) !!}
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Dirección</label>
                        {!! Form::text('address', $employee->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'200']) !!}
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Notas</label><small> Máx. 1000 caracteres</small>
                        {!! Form::textarea('notes', $employee->notes, ['id'=>'notes', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe algunas notas de interés...', 'maxlength'=>'1000']) !!}
                    </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="employee_CRUD({{ ($employee->id)?$employee->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>

<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- International Phones --> 
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/intlTelInput.js') }}"></script>
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/utils.js') }}"></script>
<script>
                  
var employee_id = "{{$employee->id}}";
if( employee_id == "" ){
    avatar_preview = "<img style='height:150px' src='{{ url('img/avatar_default.png') }}'>";
}else{
    avatar_preview = "<img style='height:150px' src= '{{ url('employee_avatar/'.$employee->id) }}' >";
}
      
// Fileinput    
$('#avatar').fileinput({
    language: 'es',
    allowedFileExtensions : ['jpg', 'jpeg', 'png'],
    previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
    showUpload: false,        
    maxFileSize: 2000,
    maxFilesNum: 1,
    overwriteInitial: true,
    progressClass: true,
    progressCompleteClass: true,
    initialPreview: [
      avatar_preview
    ]      
});            
        
var input = document.querySelector("#national_cell"),
output = document.querySelector("#error-msg");

var iti = window.intlTelInput(input, {
  initialCountry:'{{ session('condominium')->country->iso }}',
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

$(document).ready(function() {

    $('#notes').maxlength({
    warningClass: "small text-muted",
    limitReachedClass: "small text-muted",
    placement: "top-right-inside"
    });  

});
</script>

