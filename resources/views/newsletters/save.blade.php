<!-- Datetimepicker -->
<link href="{{ URL::asset('js/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
    
    <form action="#" id="form_newsletter" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('user_id', ($newsletter->id)?$newsletter->user_id:Auth::user()->id, ['id'=>'user_id']) !!}
        {!! Form::hidden('newsletter_id', ($newsletter->id)?$newsletter->id:0, ['id'=>'newsletter_id']) !!}
        @if($newsletter->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-newspaper-o" aria-hidden="true"></i> {{ ($newsletter->id) ? "Modificar Novedad": "Registrar Novedad" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">
                  <label>Fecha *</label>
                  <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {{ Form::text ('date', ($newsletter->id)?$newsletter->date->format('d/m/Y H:i'):$today->format('d/m/Y H:i'), ['id'=>'date', 'class'=>'form-control', 'placeholder'=>'', 'required']) }}
                  </div>
                </div>
                <div class="form-group col-sm-6">  
                  <label>Importancia *</label>
                  {{ Form::select('level', ['1'=>'Alta', '2'=>'Media', '3'=>'Baja'], ($newsletter->id)?$newsletter->level:3, ['id'=>'level', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>                

                <div class="form-group col-sm-12">
                    <label>Título *</label>
                    {!! Form::text('title', $newsletter->title, ['id'=>'title', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Descripción</label><small> Máx. 1000 caracteres</small>
                    {!! Form::textarea('description', $newsletter->description, ['id'=>'description', 'class'=>'form-control', 'type'=>'text', 'rows'=>'4', 'style'=>'font-size:12px', 'placeholder'=>'Escribe una breve descripción ...', 'maxlength'=>'1000']) !!}
                </div>
                @if(!$newsletter->file)
                    <div class="form-group col-sm-12">
                      <label>Archivo</label><small> (Sólo formatos jpg, jpeg, png, bmp, pdf. Máx. 2Mb.)</small>
                      <input id="file" name="file" type="file">
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="newsletter_CRUD({{ ($newsletter->id)?$newsletter->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Datetimepicker --> 
<script src="{{ URL::asset('js/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<!-- Maxlenght -->
<script src="{{ URL::asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<script>


$(document).ready(function() {
    
    $("#level").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalNewsletter .modal-content'),
        width: '100%'
    });

    var d = new Date();
    //d.setHours(0,0,0,0);    
    $("#date").datetimepicker({
      language: "es",
      startDate: d,
      format: "dd/mm/yyyy hh:ii",
      autoclose: true
    });
    
    $('#title').focus();

    $('#description').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  
    
    $('#file').fileinput({
        language: 'es',
        allowedFileExtensions : ['jpg', 'jpeg', 'png', 'bmp', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'ods', 'odt'],
        previewFileIcon: "<i class='fa fa-exclamation-triangle'></i>",
        showUpload: false,        
        maxFileSize: 2000,
        maxFilesNum: 1,
        progressClass: true,
        progressCompleteClass: true,
        showPreview: false
    });
});
</script>