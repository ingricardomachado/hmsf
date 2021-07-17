@extends('layouts.app')

@push('stylesheets')
<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Switchery -->
<link href="{{ URL::asset('js/plugins/switchery/dist/switchery.css') }}" rel="stylesheet">
@endpush

@section('page-header')
@endsection

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="ibox float-e-margins">
                        
            <!-- ibox-title -->
            <div class="ibox-title">
                <h5><i class="fa fa-cogs" aria-hidden="true"></i> Configuraciones del Condominio<small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
                <div class="ibox-tools">
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
                    <a class="close-link"><i class="fa fa-times"></i></a>
                </div>
            </div>
            <!-- /ibox-title -->
            
            @include('partials.errors')

            <!-- ibox-content -->
            <div class="ibox-content">
                <div class="row">
                    <form action="{{url('settings.update_condominium',$condominium->id)}}" id="form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                        <!-- Columna 1 -->
                        <div class="col-sm-4">                              
                            <div class="form-group">
                                <label>Logo </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) Recomendación Máx. 200px por 200px</small>
                                <input id="logo" name="logo" class="file" type="file">
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group col-sm-6">
                                <label>Nombre del Condominio *</label>
                                {!! Form::text('name', $condominium->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-6" style="padding-top: 1mm">
                                <label>Tipo de propiedades *</label>
                                {{ Form::select('property_type', $property_types, $condominium->property_type_id, ['id' => 'property_type', 'class'=>'select2_single  form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Persona contacto *</label>
                                {!! Form::text('contact', $condominium->contact, ['id'=>'contact', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Celular contacto *</label>
                                {!! Form::text('cell', $condominium->cell, ['id'=>'cell', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'15']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Teléfono contacto</label>
                                {!! Form::text('phone', $condominium->phone, ['id'=>'phone', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'15']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Correo contacto *</label>
                                {!! Form::email('email', $condominium->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'empresa@dominio.com', 'maxlength'=>'50', 'required']) !!}
                            </div>

                            <div class="form-group col-sm-6">
                                <label>País *</label>
                                {{ Form::select('country', $countries, $condominium->country_id, ['id'=>'country', 'class'=>'select2_single form-control', 'placeholder'=>'', 'required'])}}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Estado *</label>
                                {{ Form::select('state', $states, $condominium->state_id, ['id'=>'state', 'class'=>'select2_single form-control', 'placeholder'=>'', 'required'])}}
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Ciudad *</label>
                                {!! Form::text('city', $condominium->city, ['id'=>'city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-8">
                                <label>Dirección *</label>
                                {!! Form::text('address', $condominium->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'200', 'required']) !!}
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Símbolo de moneda *</label>
                                {!! Form::text('coin', $condominium->coin, ['id'=>'coin', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Ej. MXN', 'maxlength'=>'10', 'required']) !!}
                            </div>                            
                            <div class="form-group col-sm-6">
                                <label>Formato de moneda *</label>
                                {!! Form::select('money_format', ['PC2'=>'1.000,00', 'CP2' => '1,000.00'], $condominium->money_format, ['id'=>'money_format', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required']) !!}
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
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Switchery -->
<script src="{{ URL::asset('js/plugins/switchery/dist/switchery.js') }}"></script>

<!-- Page-Level Scripts -->
<script>
          
if('{{ $condominium->logo }}'=='')
{        
    logo_preview = "<img style='height:150px' src='{{ url('img/company_logo.png') }}'>";
}else{
    logo_preview = "<img style='height:150px' src= '{{ url('condominium_logo/'.$condominium->id) }}' >";
}

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
        logo_preview
    ]      
});            
    
//ECMAScript 6 Metodo para combos anidados
$("#country").change( event => {
    if(event.target.value!=''){
        url = `{{URL::to('get_states/')}}/${event.target.value}`;                    
        $.get(url, function(response){
            $("#state").empty();
            response.forEach(element => {
                $("#state").append(`<option value=${element.id}> ${element.name} </option>`);
            });
            $('#state').val(null).trigger('change');
        });
    }
});

$("#btn_submit").on('click', function(event) {    
    var validator = $("#form" ).validate();
    formulario_validado = validator.form();
        
    if(formulario_validado){
        var form_data = new FormData($("#form")[0]);
        $.ajax({
          url: '{{URL::to("settings.update_condominium")}}',
          type: 'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
            toastr_msg('success', '{{ config('app.name') }}', 'Datos del condominio actualizados exitosamente', 1000);
        })
        .fail(function() {
          toastr_msg('error', '{{ config('app.name') }}', 'Ocurrio un error actualizando los datos del condoniminio', 1000);
        });
      }
});

$(document).ready(function() {
                        
    // Select2 
    $(".select2_single").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        width: '100%'
    });
    
});
</script>
@endpush