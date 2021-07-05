<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="" id="form_expense" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($expense->id)?$expense->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('expense_id', ($expense->id)?$expense->id:0, ['id'=>'expense_id']) !!}
        @if($expense->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> {{ ($expense->id) ? "Modificar Egreso": "Registrar Egreso" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">
                    <div class="i-checks">
                        {!! Form::checkbox('to_project', null, ($expense->id)?($expense->project_id)?true:false:false, ['id'=>'to_project', 'class'=>'i-checks']) !!} <label>Asociarlo a un proyecto</label>
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <div class="i-checks">
                        {!! Form::checkbox('to_supplier', null, ($expense->id)?($expense->supplier_id)?true:false:true, ['id'=>'to_supplier', 'class'=>'i-checks']) !!} <label>Es un pago a proveedor</label>
                    </div>
                </div>
                <div class="form-group col-sm-12" id="div_projects" style="display:{{ ($expense->id)?($expense->project_id)?'solid':'none':'none' }}">  
                  <label>Proyectos *</label>
                  {{ Form::select('project', $projects, $expense->project_id, ['id'=>'project', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-12" id="div_suppliers" style="display:{{ ($expense->id)?($expense->supplier_id)?'solid':'none':'solid' }}">  
                  <label>Proveedor *</label>
                  {{ Form::select('supplier', $suppliers, $expense->supplier_id, ['id'=>'supplier', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-12">  
                  <label>Cuenta origen *</label> <small id="info_balance"></small>
                  {{ Form::select('account', $accounts, $expense->account_id, ['id'=>'account', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Fecha *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('date', ($expense->id)?$expense->date->format('d/m/Y'):$today->format('d/m/Y'), ['id'=>'date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-6">  
                  <label>Tipo de Egreso *</label>
                  {{ Form::select('expense_type', $expense_types, $expense->expense_type_id, ['id'=>'expense_type', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Método de Pago *</label>
                  {{ Form::select('payment_method', ['EF' => 'Efectivo', 'CH'=>'Cheque', 'TA'=>'Transferencia'], $expense->payment_method, ['id'=>'payment_method', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Referencia</label><small> Nro de transacción</small>
                    {!! Form::text('reference', $expense->reference, ['id'=>'reference', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Monto {{ session()->get('coin') }} *</label>
                    <input type="number" name="amount" id="amount" value="{{ $expense->amount }}" class="form-control" min="1" max={{ ($expense->id)?$expense->account->balance: ''}} step="0.01" placeholder="" required/>
                </div>
                <div class="form-group col-sm-12">
                    <label>Concepto *</label>
                    {!! Form::text('concept', $expense->concept, ['id'=>'concept', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'150', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Notas</label><small> Máx. 150 caracteres</small>
                    {!! Form::textarea('notes', $expense->notes, ['id'=>'notes', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui alguna nota de interés ...', 'maxlength'=>'150']) !!}
                </div>
                <div class="form-group col-sm-12">
                  <label>Soporte</label><small> (Sólo formatos jpg, jpeg, png, pdf. Máx. 2Mb.)</small>
                  <input id="file" name="file" type="file">
                </div>
                <div class="form-group col-sm-6" id="div_notification" style="display:{{ ($expense->id)?($expense->supplier_id)?'solid':'none':'solid' }}">
                    <div class="i-checks">
                        {!! Form::checkbox('notification', null, true, ['id'=>'notification', 'class'=>'i-checks']) !!} <label>Notificar al proveedor por correo</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="expense_CRUD({{ ($expense->id)?$expense->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
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
<!-- JQuery number-format -->
<script src="{{ URL::asset('js/plugins/jquery-number-format/jquery.number.min.js') }}"></script>
<script>

function money_fmt(num){        
  if('{{ session('money_format') }}' == 'PC'){
      num_fmt = $.number(num, 0, ',', '.');        
  }else if('{{ session('money_format')  }}' == 'PC2'){
      num_fmt = $.number(num, 2, ',', '.');          
  }else if('{{ session('money_format')  }}' == 'CP2'){
      num_fmt = $.number(num, 2, '.', ',');
  }
  return num_fmt;        
}

//Validar fecha minima segun la cuenta seleccionada
$("#account").change( event => {
    url = `{{URL::to('accounts')}}/${event.target.value}`;                    
    $.get(url, function(response){
        $('#date').datepicker('setStartDate', new Date(response.account.date_initial_balance));
        $('#amount').attr('max', response.account.balance);
        $('#info_balance').html('El disponible de esta cuenta es '+money_fmt(response.account.balance)+' {{ session('coin') }}');
    });
});

$('#to_project').on('ifChanged', function(event){
  (event.target.checked)?$('#div_projects').show():$('#div_projects').hide();
});

$('#to_supplier').on('ifChanged', function(event){
  if(event.target.checked){
    $('#div_suppliers').show();
    $('#div_notification').show();
  }else{
    $('#div_suppliers').hide();
    $('#div_notification').hide();
  }
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
        dropdownParent: $('#modalExpense .modal-content'),
        width: '100%'
    });
    
    $("#supplier").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalExpense .modal-content'),
        width: '100%'
    });

    $("#account").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalExpense .modal-content'),
        width: '100%'
    });
    
    $("#payment_method").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalExpense .modal-content'),
        width: '100%'
    });

    $("#expense_type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalExpense .modal-content'),
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