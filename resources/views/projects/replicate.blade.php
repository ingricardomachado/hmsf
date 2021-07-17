<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>

    <form action="{{ url('projects.replicate') }}" id="form_replicate" method="POST">
      <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-clone" aria-hidden="true"></i> Replicar proyecto </h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-8">
                  <label>Proyecto *</label>
                  {!! Form::text('name', $project->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'200', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Tipo *</label>
                    {{ Form::select('type', ['PR'=>'Proyecto', 'GA'=>'Gasto', 'MA'=>'Mantenimiento', 'IN'=>'Inversion', 'OT'=>'Otro'], $project->type, ['id'=>'type', 'class'=>'select2_single form-control', 'placeholder'=>'', 'required'])}}
                </div>                
                <div class="form-group col-sm-12">
                  <label>Descripción</label><small> Máx. 400 caracteres</small>
                  {!! Form::textarea('description', $project->description, ['id'=>'description', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'', 'maxlength'=>'400']) !!}
                </div>
                <div class="form-group col-sm-6">
                  <label>Responsable *</label>
                  {{ Form::select('user', $users, $project->user_id, ['id'=>'user', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                  <div class="i-checks">
                  {!! Form::checkbox('maintenance_group', null, $project->maintenance_group, ['id'=>'maintenance_group']) !!} <label>Mantenimiento en Grupo</label><br><small> Mantenimiento a grupo de activos.</small>
                    </div>
                </div>
                <div class="form-group col-sm-12" id="div_asset">
                  <label>Activo *</label>
                  {{ Form::select('asset', $assets, ($project->asset_id)?$project->asset_id:'', ['id'=>'asset', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-12" id="div_group" style="display:none">
                  <label>Grupo *</label>
                  {{ Form::select('group', $groups, ($project->group_id)?$project->group_id:'', ['id'=>'group', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                  <label>Fecha de Inicio</label><small> Estimada</small>
                  <div class="input-group date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {{ Form::text ('start_date', null, ['id'=>'start_date', 'class'=>'form-control', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-6">
                  <label>Replicaciones *</label><small> Veces a replicar el proyecto</small>
                  {{ Form::select('replications', ['1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'10', '11'=>'11', '12'=>'12', '13'=>'13', '14'=>'14', '15'=>'15', '16'=>'16', '17'=>'17', '18'=>'18', '19'=>'19', '20'=>'20', '21'=>'21', '22'=>'22', '23'=>'23', '24'=>'24'], 1, ['id'=>'replications', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                  <label>Provedor</label>
                  {{ Form::select('supplier', $suppliers, $project->supplier_id, ['id'=>'supplier', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                  <label>Presupuesto estimado {{ Session::get('coin') }} *</label>
                  {!! Form::number('budget', $project->budget, ['id'=>'budget', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-12">
                  <small><b>ATENCION:</b> El sistema replicará el proyecto de forma mensual. Ej. Si selecciona fecha de inicio 15/01/2019 el sistema replicará todos los 15 de cada mes tantas veces como replicaciones seleccione.</small>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btn_replicate" class="btn btn-sm btn-primary">Replicar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>

<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Jquery Validate -->
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/messages_es.js') }}"></script>
<script>
                  
    $(document).ready(function() {

        if('{{ $project->maintenance_group }}'==1){
          $('#div_asset').hide();
          $('#div_group').show();
        }

        var project_type='{{ $project->type }}';

        if(project_type=='GA' || project_type=='IN' || project_type=='OT'){
          $('#asset').attr('required', false);
          $('#group').attr('required', false);
        }

        // Select2         
        $("#company").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: 10,
          allowClear: false,
          width: '100%'
        });
        
        $("#type").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: 10,
          allowClear: false,
          width: '100%'
        });

        $("#user").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: 10,
          allowClear: false,
          width: '100%'
        });

        $("#asset").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: 10,
          allowClear: true,
          width: '100%'
        });

        $("#group").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: 10,
          allowClear: true,
          width: '100%'
        });

        $("#supplier").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: 10,
          allowClear: false,
          width: '100%'
        });

        $("#replications").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: -1,
          allowClear: false,
          width: '100%'
        });
        
        // iCheck
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
        
        //Datepicker 
        var date_input_1=$('#start_date');
        date_input_1.datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true,
            language: 'es',
        })
        $("#start_date").datepicker("setDate", new Date());

        $("#form_replicate").validate({            
            submitHandler: function(form) {
                $("#btn_replicate").attr("disabled",true);
                form.submit();
            }        
        });

    });

  //ECMAScript 6 Metodo para combos anidados
  $("#company").change( event => {
    //LLena los activos
    url = `{{URL::to('get_assets/')}}/${event.target.value}`;                    
    $.get(url, function(response){
      $("#asset").empty();
      response.forEach(element => {
        $("#asset").append(`<option value=${element.id}> ${element.name} </option>`);
      });
      $('#asset').val(null).trigger('change');
    });
    //LLena los grupos
    url = `{{URL::to('get_groups/')}}/${event.target.value}`;                    
    $.get(url, function(response){
      $("#group").empty();
      response.forEach(element => {
        $("#group").append(`<option value=${element.id}> ${element.name} </option>`);
      });
      $('#group').val(null).trigger('change');
    });
  });

  $('#maintenance_group').on('ifChecked', function(event){ 
    $('#div_group').show();
    $('#div_asset').hide();
  });       

  $('#maintenance_group').on('ifUnchecked', function(event){ 
    $('#div_group').hide();
    $('#div_asset').show();
  });       

  $('#type').on("select2:select", function(e) { 
      if($(this).val()=='GA' || $(this).val()=='IN' || $(this).val()=='OT'){
        $('#asset').attr('required', false);
        $('#group').attr('required', false);
      }else{
        $('#asset').attr('required', true);
        $('#group').attr('required', true);
      }
  });

</script>

