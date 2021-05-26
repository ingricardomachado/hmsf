<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
    
    <form action="" id="form_facility" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($facility->id)?$facility->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('facility_id', ($facility->id)?$facility->id:0, ['id'=>'facility_id']) !!}
        @if($facility->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-umbrella" aria-hidden="true"></i> {{ ($facility->id) ? "Modificar Instalación": "Registrar Instalación" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <!-- columna 1 -->
                <div class="col-sm-6">                            
                    <div class="form-group">
                        <label>Foto </label><small> (Sólo formatos jpg, png. Máx. 2Mb.)</small>
                        <input id="photo" name="photo" type="file">
                    </div>
                </div>
                <!-- columna 2 -->
                <div class="row">
                <div class="col-sm-6">
                    <div class="form-group col-sm-12">
                        <label>Nombre *</label>
                        {!! Form::text('name', $facility->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                    </div>
                    <div class="form-group col-sm-12">  
                      <label>Estado *</label>
                      {{ Form::select('status', ['O' => 'Operativo', 'M' => ' Mantenimiento', 'R' => 'Reparación', 'N' => 'No Operativo'], ($facility->id)?$facility->status:'O', ['id'=>'status', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                    </div>
                    <div class="form-group col-sm-12">  
                      <label>Disponible desde *</label>
                      {{ Form::select('start', ['01:00'=>'01:00 am', '02:00'=>'02:00 am', '03:00'=>'03:00 am', '04:00'=>'04:00 am', '05:00'=>'05:00 am', '06:00'=>'06:00 am', '07:00'=>'07:00 am', '08:00'=>'08:00 am', '09:00'=>'09:00 am', '10:00'=>'10:00 am', '11:00'=>'11:00 am', '12:00'=>'12:00 pm', '13:00'=>'01:00 pm', '14:00'=>'02:00 pm', '15:00'=>'03:00 pm', '16:00'=>'04:00 pm', '17:00'=>'05:00 pm', '18:00'=>'06:00 pm', '19:00'=>'07:00 pm', '20:00'=>'08:00 pm', '21:00'=>'09:00 pm', '22:00'=>'10:00 pm', '23:00'=>'11:00 pm', '24:00'=>'12:00 am'], ($facility->id)?$facility->start->format('H:i'):'08:00', ['id'=>'start', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                    </div>                
                    <div class="form-group col-sm-12">  
                      <label>Disponible hasta *</label>
                      {{ Form::select('end', ['01:00'=>'01:00 am', '02:00'=>'02:00 am', '03:00'=>'03:00 am', '04:00'=>'04:00 am', '05:00'=>'05:00 am', '06:00'=>'06:00 am', '07:00'=>'07:00 am', '08:00'=>'08:00 am', '09:00'=>'09:00 am', '10:00'=>'10:00 am', '11:00'=>'11:00 am', '12:00'=>'12:00 pm', '13:00'=>'01:00 pm', '14:00'=>'02:00 pm', '15:00'=>'03:00 pm', '16:00'=>'04:00 pm', '17:00'=>'05:00 pm', '18:00'=>'06:00 pm', '19:00'=>'07:00 pm', '20:00'=>'08:00 pm', '21:00'=>'09:00 pm', '22:00'=>'10:00 pm', '23:00'=>'11:00 pm', '00:00'=>'12:00 am'], ($facility->id)?$facility->end->format('H:i'):'16:00', ['id'=>'end', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                    </div>
                </div>
                </div>
                <div class="form-group col-sm-12">
                    <label>Normas de uso</label><small> Máx. 1000 caracteres</small>
                    {!! Form::textarea('rules', $facility->rules, ['id'=>'rules', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe las reglas de uso de la instalación ...', 'maxlength'=>'500']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <div class="i-checks">
                        {!! Form::checkbox('defaulters', null, ($facility->id)?$facility->defaulters:false, ['id'=>'defaulters', 'class'=>'i-checks']) !!} <label>Morosos pueden reservar.</label>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <div class="i-checks">
                        {!! Form::checkbox('rent', null, ($facility->id)?$facility->rent:false, ['id'=>'rent', 'class'=>'i-checks']) !!} <label>Se alquila.</label>
                    </div>
                </div>
                <div id='div_rent' style='display:none;'>                
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                      <small><i class="fa fa-info-circle" aria-hidden="true"></i> En caso de alquiler el sistema calcula y genera una cuota automatica al propietario en base los costos de alquiler colocados.</small>
                    </div>                  

                    <div class="form-group col-sm-6">
                      <label>Costo por día completo ({{ session('coin') }}) *</label>
                      {!! Form::text('day_cost', $facility->day_cost, ['id'=>'day_cost', 'class'=>'form-control decimal', 'type'=>'text', 'placeholder'=>'', 'min'=>'1', 'number', 'required']) !!}
                    </div>
                    <div class="form-group col-sm-6">                
                      <label>Costo por hora ({{ session('coin') }}) *</label>
                      {!! Form::text('hr_cost', $facility->hr_cost, ['id'=>'hr_cost', 'class'=>'form-control decimal', 'type'=>'text', 'placeholder'=>'', 'min'=>'1', 'number', 'required']) !!}
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="facility_CRUD({{ ($facility->id)?$facility->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<script>

var facility_id = "{{$facility->id}}";
if( facility_id == "" ){
    photo_preview = "<img style='height:150px' src='{{ url('img/no_image_available.png') }}'>";
}else{
    photo_preview = "<img style='height:150px' src= '{{ url('facility_photo/'.$facility->id) }}' >";
}
      
// Fileinput    
$('#photo').fileinput({
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
      photo_preview
    ]      
});            

$('#rent').on('ifChanged', function(event){
  (event.target.checked)?$('#div_rent').show():$('#div_rent').hide();  
});

$(document).ready(function() {
    $('#name').focus();

    ('{{ $facility->rent }}'=='1')?$('#div_rent').show():'';
    
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    $("#start").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalFacility .modal-content'),
        width: '100%'
    });
    
    $("#end").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalFacility .modal-content'),
        width: '100%'
    });

    $("#status").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalFacility .modal-content'),
        width: '100%'
    });

    $('#rules').maxlength({
    warningClass: "small text-muted",
    limitReachedClass: "small text-muted",
    placement: "top-right-inside"
    });  

});
</script>