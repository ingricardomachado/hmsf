<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="" id="form_operation" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('operation_id', ($operation->id)?$operation->id:0, ['id'=>'operation_id']) !!}
        @if($operation->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-truck" aria-hidden="true"></i> {{ ($operation->id) ? "Modificar Operación": "Registrar Operación" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-4">
                    <label>Fecha *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('date', ($operation->id)?$operation->date->format('d/m/Y'):$today->format('d/m/Y'), ['id'=>'date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-8">  
                  <label>Cliente *</label>
                  {{ Form::select('customer', $customers, $operation->customer_id, ['id'=>'customer', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Empresa emisora *</label>
                  {{ Form::select('company', $companies, $operation->company_id, ['id'=>'company', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Socio comercial *</label>
                  {{ Form::select('partner', $partners, $operation->partner_id, ['id'=>'partner', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Folio</label>
                    {!! Form::text('folio', $operation->folio, ['id'=>'folio', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'15']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Total Facturado {{ session()->get('coin') }} *</label>
                    <input type="number" name="amount" id="amount" value="{{ $operation->amount }}" class="form-control" min="1" step="0.01" placeholder="" required/>
                </div>
                <div class="form-group col-sm-4">
                    <label>Comisión CLI % *</label>
                    <input type="number" name="customer_tax" id="customer_tax" value="{{ $operation->customer_tax }}" class="form-control" style="margin-bottom:2mm" min="0" max="100" step="0.01" placeholder="" required/>
                    <div class="text-muted" id="customer_profit"></div>
                </div>
                <div class="form-group col-sm-4">
                    <label>Comisión SC % *</label>
                    <input type="number" name="partner_tax" id="partner_tax" value="{{ $operation->partner_tax }}" class="form-control" style="margin-bottom:2mm" min="0" max="100" step="0.01" placeholder="" required/>
                    <span class="text-muted" id="partner_profit"></span>
                </div>
                <div class="form-group col-sm-4">
                    <label>Comisión HM % *</label>
                    <input type="number" name="hm_tax" id="hm_tax" value="{{ ($operation->id)?$operation->hm_tax:'' }}" class="form-control" style="margin-bottom:2mm" min="0" max="100" step="0.01" placeholder="" required/>
                    <span class="text-muted" id="hm_profit"></span>
                </div>
                <div class="form-group col-sm-12">  
                  <label>Mensajero</label>
                  {{ Form::select('user', $users, $operation->user_id, ['id'=>'user', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                @if(!$operation->id)
                    <div class="form-group col-sm-12" id="div_notification" style="display:none">
                        <div class="i-checks">
                            <label>{!! Form::checkbox('notification', null,  false, ['id'=>'notification']) !!} <span id="label_notification">Notificar al mensajero</label>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="operation_CRUD({{ ($operation->id)?$operation->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
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

var coin='{{ session('coin') }}';

function money_fmt(num){        
    if('{{ session('money_format') }}' == 'PC'){
        num_fmt = $.number(num, 0, ',', '.');        
    }else if('{{ session('money_format') }}' == 'PC2'){
        num_fmt = $.number(num, 2, ',', '.');          
    }else if('{{ session('money_format') }}' == 'CP2'){
        num_fmt = $.number(num, 2, '.', ',');
    }
    return num_fmt;        
}

$("#customer").change( event => {
  if(event.target.value!=''){
      url=`{{URL::to('customers/')}}/${event.target.value}`;                    
      $.get(url, function(response){
        $('#customer_tax').val(response.customer.tax);
        $('#partner').val(response.customer.partner_id).trigger('change');
      });
  }
});

$("#partner").change( event => {
  if(event.target.value!=''){
      url=`{{URL::to('partners/')}}/${event.target.value}`;                    
      $.get(url, function(response){
        partner_tax=parseFloat(response.partner.tax);
        $('#partner_tax').val(partner_tax);
        $('#hm_tax').val(100-partner_tax);
      });
      calculate_profits();
  }
});

$("#amount").keyup(function () {
    calculate_profits();
});

$("#hm_tax").keyup(function () {
    calculate_profits();
});

$("#partner_tax").keyup(function () {
    calculate_profits();
});

$("#customer_tax").keyup(function () {
    calculate_profits();
});

function calculate_profits(){
    var amount=($('#amount').val()!='')?parseFloat($('#amount').val()):0;
    var hm_tax=($('#hm_tax').val()!='')?parseFloat($('#hm_tax').val()):0;
    var customer_tax=($('#customer_tax').val()!='')?parseFloat($('#customer_tax').val()):0;
    var partner_tax=($('#partner_tax').val()!='')?parseFloat($('#partner_tax').val()):0;
    var customer_profit=amount*(customer_tax/100);
    var partner_profit=customer_profit*(partner_tax/100);
    var hm_profit=customer_profit*(hm_tax/100);
    $('#customer_profit').html("Margen "+money_fmt(customer_profit)+coin);
    $('#partner_profit').html("Margen "+money_fmt(partner_profit)+coin);
    $('#hm_profit').html("Margen "+money_fmt(hm_profit)+coin);
}

$('#user').on('select2:select', function (e) {
  $('#div_notification').show();
});

$('#user').on('select2:unselect', function (e) {
  $('#div_notification').hide();
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
        
    $("#partner").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalOperation .modal-content'),
        width: '100%'
    });

    $("#customer").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalOperation .modal-content'),
        width: '100%'
    });

    $("#company").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalOperation .modal-content'),
        width: '100%'
    });

    $("#user").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: true,
        dropdownParent: $('#modalOperation .modal-content'),
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
});
</script>