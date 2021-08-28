@extends('layouts.app')

@push('stylesheets')
<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
@endpush

@section('page-header')
@endsection

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Configuración General<small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
                <div class="ibox-tools">
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
                    <a class="close-link"><i class="fa fa-times"></i></a>
                </div>
            </div>
            <!-- ibox-content -->
            <div class="ibox-content">
                <div class="row">
                    <form action="" id="form" method="POST">
                        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                        <!-- Columna 1 -->
                        <div class="col-sm-5">                              
                            <div class="form-group">
                                <label>Logo </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) Recomendación Máx. 200px por 200px</small>
                                <input id="logo" name="logo" class="file" type="file">
                            </div>
                        </div>
                        <!-- Columna 2 -->
                        <div class="col-sm-7">                            
                            <div class="form-group col-sm-12">
                                <label>Nombre de la Organización *</label> <small>Razón Social</small>
                                {!! Form::text('company', $setting->company, ['id'=>'company', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                            </div>                            
                            <div class="form-group col-sm-6">
                                <label>NIT *</label>
                                {!! Form::text('NIT', $setting->NIT, ['id'=>'NIT', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Teléfono</label>
                                {!! Form::text('phone', $setting->phone, ['id'=>'phone', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'25', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Celular</label>
                                {!! Form::text('cell', $setting->cell, ['id'=>'cell', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'25', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Correo electrónico *</label>
                                {!! Form::email('email', $setting->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'empresa@dominio.com', 'maxlength'=>'50', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Dirección *</label>
                                {!! Form::text('address', $setting->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'200', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Símbolo de moneda *</label>
                                {!! Form::text('coin', $setting->coin, ['id'=>'coin', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Ej. MXN', 'maxlength'=>'10', 'required']) !!}
                            </div>                            
                            <div class="form-group col-sm-4">
                                <label>Formato de moneda *</label>
                                {!! Form::select('money_format', ['PC2'=>'1.000,00', 'CP2' => '1,000.00'], $setting->money_format, ['id'=>'money_format', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Comisión % *</label>
                                <input type="number" name="tax" id="tax" value="{{ $setting->tax }}" class="form-control" min="1" max=100 step="0.01" placeholder="" required/>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 text-right">
                            <button type="button" id="btn_submit" class="btn btn-sm btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')    
<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<script>
          
// Fileinput    
$('#logo').fileinput({
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
      "<img style='height:150px' src= '{{ url('company_logo/'.$setting->id) }}' >"
    ]      
});            
    

$("#btn_submit").on('click', function(event) {    
    var validator = $("#form" ).validate();
    formulario_validado = validator.form();
        
    if(formulario_validado){
        $("#btn_submit").attr('disabled',true);
        var form_data = new FormData($("#form")[0]);
        $.ajax({
          url: '{{URL::to("settings.update")}}',
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
        
    // Select2 
    $("#money_format").select2({
      language: "es",
      placeholder: "Seleccione un formato numérico",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
    });
 
});
</script>
@endpush