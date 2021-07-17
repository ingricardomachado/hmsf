@extends('layouts.app')

@push('stylesheets')
<!-- Magnific Popup -->
<link rel="stylesheet" href="{{ URL::asset('js/plugins/magnific-popup/magnific-popup.css') }}">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<style type="text/css">
    
/* *********  media gallery  **************************** */

.thumbnail .image {
  height: 150px;
  overflow: hidden; }

.caption {
  padding: 9px 5px;
  background: #F7F7F7; }

.caption p {
  margin-bottom: 5px; }

.thumbnail {
  overflow: hidden; }

.view {
  overflow: hidden;
  position: relative;
  text-align: center;
  box-shadow: 1px 1px 2px #e6e6e6;
  cursor: default; }

.view .mask, .view .content {
  position: absolute;
  width: 100%;
  overflow: hidden;
  top: 0;
  left: 0; }

.view img {
  display: block;
  position: relative; }

.view .tools {
  text-transform: uppercase;
  color: #fff;
  text-align: center;
  position: relative;
  font-size: 17px;
  padding: 20px;
  background: rgba(0, 0, 0, 0.35);
  margin: 60px 0 0 0; }

.mask.no-caption .tools {
  margin: 90px 0 0 0; }

.view .tools a {
  display: inline-block;
  color: #FFF;
  font-size: 18px;
  font-weight: 400;
  padding: 0 4px; }

.view p {
  font-family: Georgia, serif;
  font-style: italic;
  font-size: 12px;
  position: relative;
  color: #fff;
  padding: 10px 20px 20px;
  text-align: center; }

.view a.info {
  display: inline-block;
  text-decoration: none;
  padding: 7px 14px;
  background: #000;
  color: #fff;
  text-transform: uppercase;
  box-shadow: 0 0 1px #000; }

.view-first img {
  transition: all 0.2s linear; }

.view-first .mask {
  opacity: 0;
  background-color: rgba(0, 0, 0, 0.5);
  transition: all 0.4s ease-in-out; }

.view-first .tools {
  transform: translateY(-100px);
  opacity: 0;
  transition: all 0.2s ease-in-out; }

.view-first p {
  transform: translateY(100px);
  opacity: 0;
  transition: all 0.2s linear; }

.view-first:hover img {
  transform: scale(1.1); }

.view-first:hover .mask {
  opacity: 1; }

.view-first:hover .tools, .view-first:hover p {
  opacity: 1;
  transform: translateY(0px); }

.view-first:hover p {
  transition-delay: 0.1s; }

/* *********  /media gallery  **************************** */
</style>
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
@endpush

@section('page-header')

@endsection

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox float-e-margins">        
        <!-- ibox-title -->
        <div class="ibox-title">
          <h5><i class="fa fa-picture-o" aria-hidden="true"></i> Fotos {{ $project->name }}</h5>
            <div class="ibox-tools">
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
              <ul class="dropdown-menu dropdown-user">
                <li><a href="#">Config option 1</a></li>
                <li><a href="#">Config option 2</a></li>
              </ul>
              <a class="close-link"><i class="fa fa-times"></i></a>
            </div>
        </div>
        <!-- /ibox-title -->
                    
        <div class="ibox-content">
          <div class="row">
          <form action="{{url('assets.add_photo/'.$project->id)}}" id="form" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
          <input type="hidden" name="hdd_project_id" value="{{ $project->id }}" />
          <div class="row">
          @if(Auth::user()->role!='VIS')
            <div class="form-group col-sm-4">
                <label>Foto </label><small> (Sólo formatos jpg, png. Máx. 2Mb.)</small>
                <input id="photo" name="photo" type="file" required>
            </div>
            <div class="form-group col-sm-4">
                <label>Título *</label><small> Max. 100 caracteres.</small>
                {!! Form::text('title', null, ['id'=>'title', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
            </div>
            <div class="form-group col-sm-2">
              <label>Momento *</label>
              {{ Form::select('stage', [1=>'Antes', 2=>'Durante', 3=>'Despues'], null, ['id'=>'stage', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
            </div>
            <div class="col-sm-2"> 
              <label><br></label> 
              <button type="button" id="btn_add" class="btn btn-primary btn-block"><i class="fa fa-plus-circle" aria-hidden="true"></i> Agregar</button>
            </div>
            @endif
            <div class="col-sm-12">
              <div id="photos"></div>
            </div>
          </div>
          </form>
          <!-- boton pie de formulario-->
          <div class="form-group pull-right">
              <a href="{{URL::previous()}}" class="btn btn-default"><i class="fa fa-hand-o-left"></i> Regresar</a>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para editar -->
<div class="modal inmodal" id="modalEdit" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <form action="#" id="form_update" method="POST">
    <input type="hidden" name="hdd_photo_id" id="hdd_photo_id" value=""/>
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-picture-o"></i> <strong>Editar Titulo</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
        <div class="row">
          <div class="form-group col-sm-8">
            <label>Título *</label><small> Max. 100 caracteres.</small>
            {!! Form::text('new_title', null, ['id'=>'new_title', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
          </div>
          <div class="form-group col-sm-4">
            <label>Momento *</label>
            {{ Form::select('new_stage', [1=>'Antes', 2=>'Durante', 3=>'Despues'], null, ['id'=>'new_stage', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btn_update" class="btn btn-primary">Guardar</button>
      </div>
    </div>
    </form>
  </div>
</div>
<!-- /Modal para editar-->

@endsection

@push('scripts')
<!-- Magnific Popup -->
<script src="{{ URL::asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- Jquery Validate -->
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/messages_es.js') }}"></script>
<script>

      // Fileinput    
      $('#photo').fileinput({
        language: 'es',
        allowedFileExtensions : ['jpg', 'jpeg', 'png'],
        showUpload: false,        
        maxFileSize: 2000,
        maxFilesNum: 1,
        overwriteInitial: true,
        showPreview: false,
        progressClass: true,
        progressCompleteClass: true,
      });            

    // Select2 
    $("#stage").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        width: '100%'
    });

    $("#new_stage").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        width: '100%'
    });

function load_photos(project_id){
  url = '{{URL::to("projects.load_photos")}}/'+project_id;
  $('#photos').load(url);  
}

$("#btn_add").on('click', function(event) {
  var validator = $("#form" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    //Uso de FormData para envio de archivos via Ajax
    var project_id='{{ $project->id }}';
    var form_data = new FormData($("#form")[0]);
    $.ajax({
      url: `{{URL::to("projects.add_photo")}}`,
      type: 'POST',
      cache:true,
      processData: false,
      contentType: false,      
      data: form_data
    })
    .done(function(response) {
      load_photos(project_id);      
      $('#photo').fileinput('reset');
      $('#title').val('');
      setTimeout(function() {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: 2000
      };
      toastr.success('Foto añadida exitosamente', '{{ Session::get('app_name') }}');
      }, 1000);
    })
    .fail(function() {
      console.log("error subiendo foto");
    });
  }
});
  
function remove_photo(photo_id){
  var project_id='{{ $project->id }}';
  $.ajax({
      url: `{{URL::to("projects.remove_photo")}}`,
      type: 'POST',
      cache:true,
      data: {
        _token: "{{ csrf_token() }}", 
        photo_id:photo_id
      },
  })
  .done(function(response) {
      load_photos(project_id);
      setTimeout(function() {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: 2000
      };
      toastr.success('Foto eliminada exitosamente', '{{ Session::get('app_name') }}');
      }, 1000);
  })
  .fail(function() {
      console.log("error eliminando estado");
  });
}  
  
function showModalEdit(photo_id, title, stage){
  $('#hdd_photo_id').val(photo_id);
  $('#new_title').val(title);
  $('#new_stage').val(stage).trigger('change');
  $("#modalEdit").modal("show");    
};

$("#btn_update").on('click', function(event) {
  var validator = $("#form_update" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    var project_id='{{ $project->id }}';
    $.ajax({
      url: `{{URL::to("projects.update_title")}}`,
      type: 'POST',
      cache:true,
      data: {
        _token: "{{ csrf_token() }}", 
        photo_id:$('#hdd_photo_id').val(),
        title:$('#new_title').val(),
        stage:$('#new_stage').val()
      },
    })
    .done(function(response) {
      $("#modalEdit").modal("toggle");
      load_photos(project_id); 
      setTimeout(function() {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: 2000
      };
      toastr.success('Titulo actualizado exitosamente', '{{ Session::get('app_name') }}');
      }, 1000);
    })
    .fail(function() {
      console.log("error actualizando titulo");
    });
  }
});
$(document).ready(function(){
  var project_id='{{ $project->id }}';
  load_photos(project_id);
});

</script>
@endpush