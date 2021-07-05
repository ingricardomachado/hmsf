<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="" id="form_income" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($income->id)?$income->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('income_id', ($income->id)?$income->id:0, ['id'=>'income_id']) !!}
        @if($income->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> {{ ($income->id) ? "Modificar Ingreso Extraordinario": "Registrar Ingreso Extraordinario" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">
                    <div class="i-checks">
                        {!! Form::checkbox('to_project', null, false, ['id'=>'to_project', 'class'=>'i-checks']) !!} <label>Asociarlo a un proyecto</label>
                    </div>
                </div>
                <div class="form-group col-sm-12" id="div_projects" style="display:none">  
                  <label>Proyectos *</label>
                  {{ Form::select('project', $projects, $income->project_id, ['id'=>'project', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Fecha *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('date', ($income->id)?$income->date->format('d/m/Y'):$today->format('d/m/Y'), ['id'=>'date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-6">  
                  <label>Cuenta destino *</label>
                  {{ Form::select('account', $accounts, $income->account_id, ['id'=>'account', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Tipo de Ingreso *</label>
                  {{ Form::select('income_type', $income_types, $income->income_type_id, ['id'=>'income_type', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Método de Pago *</label>
                  {{ Form::select('payment_method', ['EF' => 'Efectivo', 'CH'=>'Cheque', 'TA'=>'Transferencia'], $income->payment_method, ['id'=>'payment_method', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Referencia</label><small> Nro de transacción</small>
                    {!! Form::text('reference', $income->reference, ['id'=>'reference', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Monto {{ session()->get('coin') }} *</label>
                    <input type="number" name="amount" id="amount" value="{{ $income->amount }}" class="form-control decimal" min="1" step="0.01" placeholder="" required/>
                </div>
                <div class="form-group col-sm-12">
                    <label>Concepto *</label>
                    {!! Form::text('concept', $income->concept, ['id'=>'concept', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'150', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Notas</label><small> Máx. 150 caracteres</small>
                    {!! Form::textarea('notes', $income->notes, ['id'=>'notes', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui alguna nota de interés ...', 'maxlength'=>'150']) !!}
                </div>
                <div class="form-group col-sm-12">
                  <label>Soporte</label><small> (Sólo formatos jpg, jpeg, png, pdf. Máx. 2Mb.)</small>
                  <input id="file" name="file" type="file">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="income_CRUD({{ ($income->id)?$income->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<script>

//Validar fecha minima segun la cuenta seleccionada
$("#account").change( event => {
    url = `{{URL::to('accounts')}}/${event.target.value}`;                    
    $.get(url, function(response){
        $('#date').datepicker('setStartDate', new Date(response.account.date_initial_balance));
    });
});

$('#to_project').on('ifChanged', function(event){
  (event.target.checked)?$('#div_projects').show():$('#div_projects').hide();
});

$(document).ready(function() {
    
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    $(':input[type=number]').on('mousewheel', function(e){
        e.preventDefault();
    });    
    
    $('#name').focus();
    
    $("#project").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalIncome .modal-content'),
        width: '100%'
    });

    $("#account").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalIncome .modal-content'),
        width: '100%'
    });
    
    $("#payment_method").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalIncome .modal-content'),
        width: '100%'
    });

    $("#income_type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalIncome .modal-content'),
        width: '100%'
    });

    $('#date').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
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