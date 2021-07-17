<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- Touchspin -->
<link href="{{ URL::asset('css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet">
    
    <form action="" id="form_activity" method="POST">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-tasks" aria-hidden="true"></i> {{ ($activity->id) ? "Modificar Actividad" : "Registrar Actividad" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">
                    <label>Actividad *</label>
                    {!! Form::text('name', $activity->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                  <label>Fecha *</label>
                  <div class="input-group date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {{ Form::text ('date', ($activity->id)?$activity->date->format('d/m/Y'):'', ['id'=>'date', 'class'=>'form-control', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-6">
                  <label>Avance de la actividad *</label>
                  {!! Form::text('advance', $activity->advance, ['id'=>'advance', 'class'=>'form-control', 'placeholder'=>'', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Observaciones</label><small> Máx. 250 caracteres</small>
                    {!! Form::textarea('observation', $activity->observation, ['id'=>'observation', 'class'=>'form-control', 'type'=>'text', 'rows'=>'3', 'style'=>'font-size:12px', 'placeholder'=>'', 'maxlength'=>'250']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="activity_CRUD({{ ($activity->id)?$activity->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>

<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- TouchSpin -->
<script src="{{ URL::asset('js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
<script>

$(document).ready(function() {
        
    //Datepicker 
    var date_input_1=$('#date');
    date_input_1.datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
    })
        
    $("#advance").TouchSpin({
        min: 0,
        max: 100 - {{ $project->advance }},
        step: 1,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '%',
        buttondown_class: 'btn btn-white',
        buttonup_class: 'btn btn-white'
    });

});
</script>

