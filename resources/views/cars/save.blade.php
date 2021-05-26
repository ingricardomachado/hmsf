<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
    
    <form action="" id="form_car" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('car_id', ($car->id)?$car->id:0, ['id'=>'car_id']) !!}
        @if($car->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-car" aria-hidden="true"></i> {{ ($car->id) ? "Modificar Vehículo": "Registrar Vehículo" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">
                    <label>Placa *</label>
                    {!! Form::text('plate', $car->plate, ['id'=>'plate', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'10', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Propiedad *</label>
                  {{ Form::select('property', $properties, $car->property_id, ['id'=>'property', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>

                <div class="form-group col-sm-6">
                    <label>Marca *</label>
                    {!! Form::text('make', $car->make, ['id'=>'make', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Modelo *</label>
                    {!! Form::text('model', $car->model, ['id'=>'model', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Color *</label>
                    {!! Form::text('color', $car->color, ['id'=>'color', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                  <label>Año *</label>
                  <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {{ Form::text ('year', ($car->id)?$car->year:null, ['id'=>'year', 'class'=>'form-control', 'placeholder'=>'', 'required']) }}
                  </div>
                </div>
                <div class="form-group col-sm-12">
                    <label>Notas</label><small> Máx. 1000 caracteres</small>
                    {!! Form::textarea('notes', $car->notes, ['id'=>'notes', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe algunas notas de interés...', 'maxlength'=>'1000']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="car_CRUD({{ ($car->id)?$car->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<script>


$(document).ready(function() {
    $('#plate').focus();

    $("#property").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: true,
        dropdownParent: $('#modalCar .modal-content'),
        width: '100%'
    });

    $('#year').datepicker({
        format: 'yyyy',
        viewMode: 'years', 
        minViewMode: 'years',        
        todayHighlight: true,
        autoclose: true,
        language: 'es',
        startDate: '1970',
        endDate: '+1y'
    });

    $('#notes').maxlength({
    warningClass: "small text-muted",
    limitReachedClass: "small text-muted",
    placement: "top-right-inside"
    });  

});

</script>