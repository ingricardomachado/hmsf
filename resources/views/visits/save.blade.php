<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- Datetimepicker -->
<link href="{{ URL::asset('js/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="" id="form_visit" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($visit->id)?$visit->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        <input type="hidden" name="visit_id" id="visit_id" class="form-control" value="{{ ($visit->id)?$visit->id:0 }}">
        @if($visit->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> {{ ($visit->id) ? "Modificar Visita": "Registrar Visita" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">
                    <label>Fecha de entrada *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('checkin', ($visit->id)?$visit->checkin->format('d/m/Y'):$today->format('d/m/Y H:i'), ['id'=>'checkin', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-6">  
                  <label>Propiedad *</label>
                  {{ Form::select('property', $properties, $visit->property_id, ['id'=>'property', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Tipo de visita *</label>
                  {{ Form::select('visit_type', $visit_types, $visit->visit_type_id, ['id'=>'visit_type', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Nro Identificación del visitante *</label>
                    {!! Form::text('NIT', ($visit->visitor_id)?$visit->visitor->NIT:'', ['id'=>'NIT', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('name', ($visit->visitor_id)?$visit->visitor->name:'', ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
                
                <div class="form-group col-sm-6">
                    <div class="i-checks">
                        {!! Form::checkbox('car', null, false, ['id'=>'car', 'class'=>'i-checks']) !!} <label>Viene en vehículo</label>
                    </div>
                </div>
            <div id="div_car" style="display: none">
                <div class="form-group col-sm-6">
                    <label>Placa *</label>
                    {!! Form::text('plate', ($visit->visiting_car_id)?$visit->visiting_car->plate:'', ['id'=>'plate', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Marca *</label>
                    {!! Form::text('make', ($visit->visiting_car_id)?$visit->visiting_car->make:'', ['id'=>'make', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Modelo *</label>
                    {!! Form::text('model', ($visit->visiting_car_id)?$visit->visiting_car->model:'', ['id'=>'model', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
            </div>
                <div class="form-group col-sm-12">
                    <label>Notas</label><small> Máx. 150 caracteres</small>
                    {!! Form::textarea('notes', $visit->notes, ['id'=>'notes', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui alguna nota de interés ...', 'maxlength'=>'150']) !!}
                </div>
                @if(!$visit->file)
                    <div class="form-group col-sm-12">
                      <label>Soporte</label><small> (Sólo formatos jpg, jpeg, png, pdf. Máx. 2Mb.)</small>
                      <input id="file" name="file" type="file">
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="visit_CRUD({{ ($visit->id)?$visit->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- Datetimepicker --> 
<script src="{{ URL::asset('js/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<script>

var condominium_id={{ session('condominium')->id }};

$('#car').on('ifChanged', function(event){
  (event.target.checked)?$('#div_car').show():$('#div_car').hide();
});

$('#NIT').keypress(function (e) {
if (e.which == '13') {
    search_visitor($(this).val());
}});

function search_visitor(nit){  
    $.ajax({
      url: `{{URL::to("visitor_by_nit")}}/${condominium_id}/${nit}`,
      type: 'GET',
    })
    .done(function(response) {
        $('#name').val(response.visitor.name);
    })
    .fail(function(response) {
        $('#name').val('');
        $('#name').focus();
    });
}  

$('#plate').keypress(function (e) {
if (e.which == '13') {
    search_visiting_car($(this).val());
}});

function search_visiting_car(plate){  
    $.ajax({
      url: `{{URL::to("visiting_car_by_plate")}}/${condominium_id}/${plate}`,
      type: 'GET',
    })
    .done(function(response) {
        $('#make').val(response.visiting_car.make);
        $('#model').val(response.visiting_car.model);
        $('#color').val(response.visiting_car.color);
    })
    .fail(function(response) {
        $('#make').val('');
        $('#model').val('');
        $('#color').val('');
        $('#make').focus();
    });
}  

$(document).ready(function() {
    
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
            
    $("#visit_type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalVisit .modal-content'),
        width: '100%'
    });

    $("#property").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalVisit .modal-content'),
        width: '100%'
    });

    var d = new Date();
    //d.setHours(0,0,0,0);    
    $("#checkin").datetimepicker({
      language: "es",
      startDate: d,
      format: "dd/mm/yyyy hh:ii",
      autoclose: true
    });

    $('#notes').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  

    $('#file').fileinput({
        language: 'es',
        allowedFileExtensions : ['jpg', 'jpeg', 'png', 'bmp', 'pdf'],
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