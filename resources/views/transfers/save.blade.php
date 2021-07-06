<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="" id="form_transfer" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($transfer->id)?$transfer->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('transfer_id', ($transfer->id)?$transfer->id:0, ['id'=>'transfer_id']) !!}
        @if($transfer->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> {{ ($transfer->id) ? "Modificar Transferencia": "Registrar Transferencia" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">
                    <label>Fecha *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('date', ($transfer->id)?$transfer->date->format('d/m/Y'):$today->format('d/m/Y'), ['id'=>'date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-6">  
                  <label>Método de Pago *</label>
                  {{ Form::select('payment_method', ['EF' => 'Efectivo', 'CH'=>'Cheque', 'TA'=>'Transferencia'], $transfer->payment_method, ['id'=>'payment_method', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-12">  
                  <label>Cuenta origen *</label> <small id="info_balance"></small>
                  {{ Form::select('from_account', $accounts, $transfer->from_account_id, ['id'=>'from_account', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required', ($transfer->id)?'disabled':''])}}
                </div>

                <div class="form-group col-sm-12">  
                  <label>Cuenta destino *</label>
                  {{ Form::select('to_account', $accounts, $transfer->to_account_id, ['id'=>'to_account', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required', ($transfer->id)?'disabled':''])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Referencia</label><small> Nro de transacción</small>
                    {!! Form::text('reference', $transfer->reference, ['id'=>'reference', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Monto {{ session()->get('coin') }} *</label>
                    <input type="number" name="amount" id="amount" value="{{ $transfer->amount }}" class="form-control decimal" min="1" step="0.01" placeholder="" required/>
                </div>
                <div class="form-group col-sm-12">
                    <label>Concepto *</label>
                    {!! Form::text('concept', $transfer->concept, ['id'=>'concept', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'150', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                  <label>Soporte</label><small> (Sólo formatos jpg, jpeg, png, pdf. Máx. 2Mb.)</small>
                  <input id="file" name="file" type="file">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="transfer_CRUD({{ ($transfer->id)?$transfer->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
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
$("#from_account").change( event => {
    url = `{{URL::to('accounts')}}/${event.target.value}`;                    
    $.get(url, function(response){
        $('#date').datepicker('setStartDate', new Date(response.account.date_initial_balance));
        $('#amount').attr('max', response.account.balance);
        $('#info_balance').html('El disponible de esta cuenta es '+money_fmt(response.account.balance)+' {{ session('coin') }}');
    });
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
    
    $("#from_account").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalTransfer .modal-content'),
        width: '100%'
    });
    
    $("#to_account").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalTransfer .modal-content'),
        width: '100%'
    });

    $("#payment_method").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalTransfer .modal-content'),
        width: '100%'
    });

    $('#date').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
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