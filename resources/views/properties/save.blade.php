<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
    
    <form action="" id="form_property" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($property->id)?$property->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('property_id', ($property->id)?$property->id:0, ['id'=>'property_id']) !!}
        @if($property->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-home" aria-hidden="true"></i> {{ ($property->id) ? "Modificar Propiedad": "Registrar Propiedad" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-4">
                    <label>Número *</label>
                    {!! Form::text('number', $property->number, ['id'=>'number', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-8">
                    <label>Propietario</label><small>{{(!$property->id)?' Puede asignarlo luego':'' }}</small>
                    {{ Form::select('user', $users, $property->user_id, ['id'=>'user', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="property_CRUD({{ ($property->id)?$property->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- Jquery Validate -->
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/messages_es.js') }}"></script>
<script src="{{ URL::asset('js/plugins/nit_validation/calcularDigitoVerificacion.js') }}"></script>
<script>


$(document).ready(function() {
    $('#number').focus();

    $("#user").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: true,
        dropdownParent: $('#modalProperty .modal-content'),
        width: '100%'
    });

});

</script>