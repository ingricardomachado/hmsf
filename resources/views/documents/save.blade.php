<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
    
    <form action="#" id="form_document" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($document->id)?$document->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('document_id', ($document->id)?$document->id:0, ['id'=>'document_id']) !!}
        @if($document->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-file-text-o" aria-hidden="true"></i> {{ ($document->id) ? "Modificar Documento": "Registrar Documento" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">  
                  <label>Categoría *</label>
                  {{ Form::select('document_type', $document_types, $document->document_type_id, ['id'=>'document_type', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>                
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('name', $document->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Descripción</label><small> Máx. 500 caracteres</small>
                    {!! Form::textarea('description', $document->description, ['id'=>'description', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe una breve descripción ...', 'maxlength'=>'500']) !!}
                </div>
                @if(!$document->id)
                    <div class="form-group col-sm-12">
                      <label>Archivo</label><small> (Sólo formatos jpg, jpeg, png, bmp, pdf, xls, xlsx, doc, docx, ods, odt. Máx. 2Mb.)</small>
                      <input id="file" name="file" type="file">
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="document_CRUD({{ ($document->id)?$document->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<script>


$(document).ready(function() {
    $('#name').focus();

    $("#document_type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalDocument .modal-content'),
        width: '100%'
    });

    $('#description').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  
    
    $('#file').fileinput({
        language: 'es',
        allowedFileExtensions : ['jpg', 'jpeg', 'png', 'bmp', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'ods', 'odt'],
        previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
        showUpload: false,        
        maxFileSize: 2000,
        maxFilesNum: 1,
        progressClass: true,
        progressCompleteClass: true,
        showPreview: false
    });
});

</script>