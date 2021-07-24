@extends('layouts.app')

@push('stylesheets')
<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- International Phones -->
<link href="{{ URL::asset('js/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que input del cell sea 100% width-->
<style type="text/css">
    .iti { width: 100%; }
</style>

@endpush

@section('page-header')
@endsection

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="ibox float-e-margins">            
            <div class="ibox-title">
                <h5>Perfil de Usuario <small>Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
                <div class="ibox-tools">
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
                    <a class="close-link"><i class="fa fa-times"></i></a>
                </div>
            </div>
            
            <!-- ibox-content -->
            <div class="ibox-content">
                <div class="row">
                    <form action="" id="form_profile" method="POST">
                        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                        {!! Form::hidden('cell', ($user->id)?$user->cell:null, ['id'=>'cell']) !!}
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Avatar </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) Recomendación Máx. 200px por 200px</small>
                                <input id="avatar" name="avatar" class="file" type="file">
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group col-sm-6">
                                <label>Nombre *</label>
                                {!! Form::text('name', $user->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Correo *</label><small> Será su nombre de usuario</small>
                                {!! Form::email('email', $user->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Celular</label>
                                {!! Form::tel('national_cell', $user->cell, ['id'=>'national_cell', 'class'=>'form-control', 'placeholder'=>'']) !!}
                                <span id="error-msg" style="color:#cc5965;font-weight:bold"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Teléfono</label>
                                {!! Form::text('phone', $user->phone, ['id'=>'phone', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'30']) !!}
                            </div>
                            <div class="form-group col-sm-12" style="display:{{ ($user->id)?'solid':'none' }}">
                                <div class="i-checks">
                                    <label>{!! Form::checkbox('change_password', null,  false, ['id'=>'change_password']) !!} Cambiar la contraseña</label>
                                </div>
                            </div>
                            <div class="form-group col-sm-6" id='div_password' style='display:{{ ($user->id)?'none':'solid' }};'>
                                <label>Contraseña *</label><small> De 6 a 15 caracteres.</small>
                                <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="6" maxlength="15" required>
                            </div>
                            <div class="form-group col-sm-6" id='div_password_confirmation' style='display:{{ ($user->id)?'none':'solid' }};'>
                                <label>Confirmar Contraseña *</label>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="" minlength="6" maxlength="15" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                        </div>
                        <div class="form-group col-sm-12 text-right">
                            <button type="button" id="btn_submit" class="btn btn-sm btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /ibox-content -->
        
        </div>
    </div>
</div>
@endsection

@push('scripts')    
<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- International Phones --> 
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/intlTelInput.js') }}"></script>
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/utils.js') }}"></script>
<script>
      
var user_id = "{{$user->id}}";
if( user_id == "" )
{        
    avatar_preview = "<img style='height:150px' src='{{ url('img/avatar_default.png') }}'>";
}else{
    avatar_preview = "<img style='height:150px' src= '{{ url('user_avatar/'.$user->id) }}' >";
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

$("#btn_submit").on('click', function(event) {    
    var validator = $("#form_profile" ).validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $("#btn_submit").attr('disabled',true);
        var form_data = new FormData($("#form_profile")[0]);
        $.ajax({
          url: '{{URL::to("profile.update")}}',
          type: 'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
            $("#btn_submit").attr('disabled',false);
            toastr_msg('success', '{{ config('app.name') }}', response.message, 1000);
        })
        .fail(function(response) {
          $("#btn_submit").attr('disabled',false);
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

});
</script>
@endpush