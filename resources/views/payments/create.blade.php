<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="" id="form_payment" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> {{ ($payment->id) ? "Modificar Egreso": "Registrar Egreso" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-5 b-r">
                    <div class="row">
                        <div class="form-group col-sm-12">  
                          <label>Propiedad *</label>
                          {{ Form::select('property', $properties, null, ['id'=>'property', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="form-group col-sm-12">  
                          <label>Cuenta origen *</label>
                          {{ Form::select('account', $accounts, null, ['id'=>'account', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Fecha *</label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {{ Form::text ('date', ($payment->id)?$payment->date->format('d/m/Y'):$today->format('d/m/Y'), ['id'=>'date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                            </div>
                        </div>
                        <div class="form-group col-sm-6">  
                          <label>Método de Pago *</label>
                          {{ Form::select('payment_method', ['EF' => 'Efectivo', 'CH'=>'Cheque', 'TA'=>'Transferencia'], $payment->payment_method, ['id'=>'payment_method', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Referencia</label><small> Nro de transacción</small>
                            {!! Form::text('reference', $payment->reference, ['id'=>'reference', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Concepto *</label>
                            {!! Form::text('concept', $payment->concept, ['id'=>'concept', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'150', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-12">
                          <label>Soporte</label><small> (Sólo formatos jpg, jpeg, png, pdf. Máx. 2Mb.)</small>
                          <input id="file" name="file" type="file">
                        </div>
                    </div>    
                </div>
                <div class="col-sm-7">
                    <div><label>Cuotas pendientes  *</label> <small>Seleccione las cuotas a pagar. <b>Puede abonar</b> colocando un monto diferente a la deuda total de la cuota.</small></div>
                    <span id="pending_fees"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" class="btn btn-sm btn-primary" disabled>Guardar</button>
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
<!-- Jquery Validate -->
<script src="{{ URL::asset('js/plugins/jquery-validation-1.19.1/dist/jquery.validate.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-validation-1.19.1/dist/localization/messages_es.js') }}"></script>
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
    });
});

$("#property").change( event => {
  url = `{{URL::to("payments.load_pending_fees")}}/${event.target.value}`;
  $('#pending_fees').load(url);
});

$("#btn_submit").on('click', function(event){        
    var validator = $("#form_payment").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $(this).attr('disabled', true);
        var array_fees=[];
        var array_amounts=[];
        $("input[name='fees']:checked").each(function (){
            array_fees.push($(this).val());
            var index = $("input[id^='fees']").index(this);
            var input_amount=$("input[name^='amounts']").eq(index);
            array_amounts.push(input_amount.val());
        });
        var form_data = new FormData($("#form_payment")[0]);
        form_data.append('array_fees', JSON.stringify(array_fees));
        form_data.append('array_amounts', JSON.stringify(array_amounts));        
        $.ajax({
          url:'{{URL::to("payments")}}',
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $(this).attr('disabled', false);
          $('#modalPayment').modal('toggle');
          $('#payments-table').DataTable().draw(); 
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $(this).attr('disabled', false);
            var errorsHtml='';
            $.each(response.responseJSON.errors, function (key, value) {
              errorsHtml += '<li>' + value[0] + '</li>'; 
            });          
            toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 3000);
          }else{
            toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
          }
        });
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
    
    $("#property").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalPayment .modal-content'),
        width: '100%'
    });
    
    $("#account").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalPayment .modal-content'),
        width: '100%'
    });
    
    $("#payment_method").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalPayment .modal-content'),
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