<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">

    <form action="" id="form_project" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($project->id)?$project->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('facility_id', ($project->id)?$project->id:0, ['id'=>'facility_id']) !!}
        @if($project->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-wrench" aria-hidden="true"></i> {{ ($project->id) ? "Modificar Proyecto" : "Registrar Proyecto" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">
                    <label>Proyecto *</label>
                    {!! Form::text('name', $project->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'','required']) !!}
                </div>
                <div class="form-group col-sm-12">
                  <label>Descripción del proyecto *</label><small> Máx. 500 caracteres</small>
                  {!! Form::textarea('description', $project->description, ['id'=>'description', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Describe el proyecto a realizar...', 'maxlength'=>'500', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                  <label>Inicio estimado *</label>
                  <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {{ Form::text ('planned', ($project->id)?$project->planned->format('d/m/Y'):'', ['id'=>'planned', 'class'=>'form-control', 'placeholder'=>'', 'required']) }}
                  </div>
                </div>
                <div class="form-group col-sm-6">
                  <label>Culminación estimado *</label>
                  <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {{ Form::text ('planned_end', ($project->id)?$project->planned_end->format('d/m/Y'):'', ['id'=>'planned_end', 'class'=>'form-control', 'placeholder'=>'', 'required']) }}
                  </div>
                </div>
                <div class="form-group col-sm-6">
                  <label>Presupuesto estimado {{ session('coin') }} *</label>
                  {!! Form::number('budget', $project->budget, ['id'=>'budget', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step' => '0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="project_CRUD({{ ($project->id)?$project->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>

<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<script>
                  
$(document).ready(function() {
        
  // iCheck
  $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
  });
  
  //Datetimepicker 
  var d = new Date();
  d.setHours(0,0,0,0);    
  $("#planned").datepicker({
      language: "es",
      startDate: d,
      format: "dd/mm/yyyy",
      autoclose: true
  }).on("changeDate", function (e) {
    startDate = new Date(e.date.valueOf());
    startDate.setDate(startDate.getDate(new Date(e.date.valueOf())));
    $('#planned_end').datepicker('setStartDate', startDate);
  });
  
  $("#planned_end").datepicker({
      language: "es",
      startDate: d,
      format: "dd/mm/yyyy",
      autoclose: true
  });

});
</script>

