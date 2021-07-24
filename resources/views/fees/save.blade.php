<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="#" id="form_fee" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($fee->id)?$fee->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('fee_id', ($fee->id)?$fee->id:0, ['id'=>'fee_id']) !!}
        @if($fee->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-file-text-o" aria-hidden="true"></i> {{ ($fee->id) ? "Modificar Cuota": "Registrar Cuota" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">
                    <div class="i-checks">
                        {!! Form::checkbox('to_project', null, ($fee->id)?($fee->project_id)?true:false:false, ['id'=>'to_project', 'class'=>'i-checks']) !!} <label>Asociarla a un proyecto</label>
                    </div>
                </div>
                <div class="form-group col-sm-12" id="div_projects" style="display:{{ ($fee->id)?($fee->project_id)?'solid':'none':'none' }}">  
                  <label>Proyectos *</label>
                  {{ Form::select('project', $projects, $fee->project_id, ['id'=>'project', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Propiedad *</label>
                  {{ Form::select('property', $properties, $fee->property_id, ['id'=>'property', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>                
                <div class="form-group col-sm-6">
                    <label>Fecha</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('date', ($fee->date)?$fee->date->format('d/m/Y'):$today, ['id'=>'date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-12">  
                  <label>Tipo de cuota *</label>
                  {{ Form::select('income_type', $income_types, $fee->income_type_id, ['id'=>'income_type', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Monto {{ session()->get('coin') }} *</label>
                    <input type="number" name="amount" id="amount" value="{{ $fee->amount }}" class="form-control decimal" min="1" placeholder="" required/>
                </div>
                <div class="form-group col-sm-6">
                    <label>Vecimiento *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('due_date', ($fee->due_date)?$fee->due_date->format('d/m/Y'):$last_day_of_month, ['id'=>'due_date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>

                <div class="form-group col-sm-12">
                    <label>Concepto *</label><small> Máx. 150 caracteres</small>
                    {!! Form::text('concept', $fee->concept, ['id'=>'concept', 'class'=>'form-control', 'type'=>'text', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui un concepto que describa la cuota a cobrar ...', 'maxlength'=>'150', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="fee_CRUD({{ ($fee->id)?$fee->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Inputmask -->
<script src="{{ asset('js/plugins/inputmask/dist/jquery.inputmask.min.js') }}"></script>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<script>

$('#to_project').on('ifChanged', function(event){
  (event.target.checked)?$('#div_projects').show():$('#div_projects').hide();
});

$(document).ready(function() {
    
   /*$("#masked-amount").inputmask("decimal", {
        positionCaretOnClick: "radixFocus",
        groupSeparator: ".",
        radixPoint: ",",
        _radixDance: true,
        numericInput: true,
        placeholder: "0",
        definitions: {
            "0": {
                validator: "[0-9\uFF11-\uFF19]"
            }
        }
   });*/
    
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    //Datepicker 
    var d = new Date();
    $("#date").datepicker({
      language: "es",
      startDate: d,
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true
    }).on("changeDate", function (e) {
        startDate = new Date(e.date.valueOf());
        startDate.setDate(startDate.getDate(new Date(e.date.valueOf())));
        $('#due_date').datepicker('setStartDate', startDate);
    });
    
    $('#due_date').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
        startDate: d
    })

    $("#project").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalFee .modal-content'),
        width: '100%'
    });

    $("#property").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalFee .modal-content'),
        width: '100%'
    });

    $("#income_type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalFee .modal-content'),
        width: '100%'
    });

    $('#concept').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  

});
</script>