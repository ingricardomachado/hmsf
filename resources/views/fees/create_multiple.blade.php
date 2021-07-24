<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">

<form action="" id="form_fees" method="POST">
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title"><i class="fa fa-money" aria-hidden="true"></i> Registrar cuotas multiples</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-5 b-r">    
                <div class="form-group col-sm-12">
                    <div class="i-checks">
                        {!! Form::checkbox('to_project_multiple', null, false, ['id'=>'to_project_multiple', 'class'=>'i-checks']) !!} <label>Asociarla a un proyecto</label>
                    </div>
                </div>
                <div class="form-group col-sm-12" id="div_projects_multiple" style="display:{{ ($fee->id)?($fee->project_id)?'solid':'none':'none' }}">  
                  <label>Proyectos *</label>
                  {{ Form::select('project_multiple', $projects, $fee->project_id, ['id'=>'project_multiple', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Fecha</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('date_multiple', ($fee->date)?$fee->date->format('d/m/Y'):$today, ['id'=>'date_multiple', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'style'=>'font-size:12px', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label>Vecimiento *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('due_date_multiple', ($fee->due_date)?$fee->due_date->format('d/m/Y'):$last_day_of_month, ['id'=>'due_date_multiple', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'style'=>'font-size:12px', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-12">  
                  <label>Tipo de cuota *</label>
                  {{ Form::select('income_type_multiple', $income_types, null, ['id'=>'income_type_multiple', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-12">  
                  <label>Tipo de distribución *</label>
                    {{ Form::select('distribution_type', [
                        '1' => 'Monto igual para cada propiedad seleccionada', 
                        '2' => 'Monto global distribuido equitativamente entre las propiedades seleccionadas',
                        '3' => 'Monto global distribuido de acuerdo al % de alicuota definido (aplica para todas las propiedades)', 
                        '4' => 'Montos libres a cada propiedad seleccionada'],  null, ['id'=>'distribution_type', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-12" id="div-amount">
                    <label>Monto {{ session()->get('coin') }} *</label>
                    <input type="number" name="amount_multiple" id="amount_multiple" value="{{ $fee->amount }}" class="form-control input-number–noSpinners" style="font-size: 16px" min="1" placeholder="" required/>
                </div>
                <div class="form-group col-sm-12">
                    <label>Concepto *</label><small> Máx. 150 caracteres</small>
                    {!! Form::text('concept_multiple', $fee->concept, ['id'=>'concept_multiple', 'class'=>'form-control', 'type'=>'text', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui un concepto que describa la cuota a cobrar ...', 'maxlength'=>'150', 'required']) !!}
                </div>
            </div>
            <div class="col-sm-7">
              <label>Seleccione las propiedades *</label>
              <div class="table-responsive">                                        
                <table class="table" id="datatable-properties" width="100%" style="font-size: 12px">
                  <thead>
                    <tr>
                      <th>
                        {!! Form::checkbox('check-all', null, false, ['id'=>'check-all', 'class'=>'i-checks i-fees']) !!}
                      </th>
                      <th>Nro</th>
                      <th>Propietario</th>
                      <th class="col-aliquot" style="display: none">Alicuota</th>
                      <th class="text-right">Monto {{ session('coin') }}</th>
                    </tr>
                  </thead>
                  <tbody>                  
                  @php($i=0)
                  @foreach($properties as $property)
                    <tr>
                      <td>
                        {!! Form::checkbox('properties', $property->id, null, ['class'=>'i-checks i-fees', 'id'=>'properties[]', 'data-tax' => $property->tax, 'required']) !!}
                      </td>
                      <td><b>{{ $property->number }}</b></td>
                      <td>{{ $property->user }}</td>
                      <td class="col-aliquot" style="display: none">
                        {{ money_fmt($property->tax) }}%
                      </td>
                      <td class="text-right">
                        {!! Form::number('amounts', null, ['id'=>'amounts[]', 'class'=>'form-control form-control-sm', 'min'=>'1', 'step'=>'0.01', 'required', 'style'=>'display: none']) !!}
                        <span id="amount_label[{{ $i++ }}]"></span>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
                <small><span id="properties-selected">Propiedades seleccionadas 0</span></small>
              </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="btn_submit_multiple" class="btn btn-sm btn-primary">Guardar</button>
        <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
    </div>
</form>

<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
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

$("#distribution_type").change( event => {
  if(event.target.value==3){
    $('.col-aliquot').show();
    $('.i-fees').iCheck('check');
    $('.i-fees').iCheck('disable');
    distribuye(event.target.value);
  }else{
    $('.col-aliquot').hide();
    $('.i-fees').iCheck('uncheck');
    $('.i-fees').iCheck('enable');
  }
  if(event.target.value==4){
    $('#amount_multiple').val('');
    $('#div-amount').hide();
  }else{
    $('#div-amount').show();
  }
});

$('#check-all').on('ifChanged', function(event){
  (event.target.checked)?$('.i-fees').iCheck('check'):$('.i-fees').iCheck('uncheck');  
});

$("#amount_multiple").keyup(function () {
  var distribution_type=$('#distribution_type').val();
  (distribution_type<4)?distribuye(distribution_type):'';
});


$("input[id^='properties']").on('ifChanged', function(event){       
  var index = $("input[id^='properties']").index(this);
  var amount = $('#amount_multiple').val();
  var distribution_type=$('#distribution_type').val();
  if(event.target.checked){
    if(distribution_type==1){
      distribuye(distribution_type);
    }else if(distribution_type==2 || distribution_type==3){
      distribuye(distribution_type);
    }else if(distribution_type==4){
      $("input[name^='amounts']").eq(index).show();
      $("input[name^='amounts']").eq(index).focus();      
    }
  }else{
      $("input[name^='amounts']").eq(index).val('');
      $("input[name^='amounts']").eq(index).hide();
      document.getElementById('amount_label['+index+']').innerHTML = '';
      distribuye(distribution_type);
  }
});       

function distribuye(distribution_type){
  tot_amount=$('#amount_multiple').val();
  tot_checked=$("input[name='properties']:checked").length;
  $('#properties-selected').html('Proiedades seleccionadas '+tot_checked);
  if(distribution_type == 1){
    $("input[name='properties']:checked").each(function (){
      var index = $("input[id^='properties']").index(this);
      document.getElementById('amount_label['+index+']').innerHTML = money_fmt(tot_amount);
    });
  }else if (distribution_type == 2){
    amount=tot_amount/tot_checked;
    amount=Math.round(amount*100)/100;
    $("input[name='properties']:checked").each(function (){
      var index = $("input[id^='properties']").index(this);
      document.getElementById('amount_label['+index+']').innerHTML = money_fmt(amount);
    });
  }else if(distribution_type == 3){
    $("input[name='properties']:checked").each(function (){
      var index = $("input[id^='properties']").index(this);
      tax=$(this).attr("data-tax");
      amount=Math.round(tot_amount*tax)/100;
      document.getElementById('amount_label['+index+']').innerHTML = money_fmt(amount);
    });    
  }
}

$("#btn_submit_multiple").on('click', function(event) {
  var validator = $( "#form_fees" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    $('#btn_submit_multiple').attr('disabled', true);
    var array_properties=[];
    var array_amounts=[];
    $("input[name='properties']:checked").each(function (){
      array_properties.push($(this).val());
    });

    $("input[name='amounts']").each(function (){
      ($(this).val())?array_amounts.push($(this).val()):'';
    });

    $.ajax({
        url:'{{URL::to("fees.store_multiple")}}',
        type: 'POST',          
        data: {
            _token: "{{ csrf_token() }}",
            condominium_id: {{ session('condominium')->id }},
            project: $('#project_multiple').val(),
            distribution_type: $('#distribution_type').val(),
            income_type: $('#income_type_multiple').val(),
            amount: $('#amount_multiple').val(),
            date: $('#date_multiple').val(),
            due_date: $('#due_date_multiple').val(),
            concept: $('#concept_multiple').val(),
            array_properties: array_properties,
            array_amounts: array_amounts
        },          
    })
    .done(function(response) {
        $('#btn_submit_multiple').attr('disabled', false);
        $('#modalMultipleFee').modal('toggle');
        $('#fees-table').DataTable().draw(); 
        toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
    })
    .fail(function(reponse) {
      if(response.status == 422){
        $('#btn_submit_multiple').attr('disabled', false);
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

$('#to_project_multiple').on('ifChanged', function(event){
  (event.target.checked)?$('#div_projects_multiple').show():$('#div_projects_multiple').hide();
});

$(document).ready(function() {

    $(':input[type=number]').on('mousewheel', function(e){
        e.preventDefault();
    });    

    $('.i-fees').iCheck('disable');

    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    //Datepicker 
    var d = new Date();
    $("#date_multiple").datepicker({
      language: "es",
      startDate: d,
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true
    }).on("changeDate", function (e) {
        startDate = new Date(e.date.valueOf());
        startDate.setDate(startDate.getDate(new Date(e.date.valueOf())));
        $('#due_date_multiple').datepicker('setStartDate', startDate);
    });
    
    $('#due_date_multiple').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
        startDate: d
    })

    $("#project_multiple").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalMultipleFee .modal-content'),
        width: '100%'
    });

    $("#income_type_multiple").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalMultipleFee .modal-content'),
        width: '100%'
    });

    $("#distribution_type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalMultipleFee .modal-content'),
        width: '100%'
    });

    $('#concept_multiple').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  
          
    $('#datatable-properties').DataTable( {
      "ordering": false,
      "searching": false,
      "lengthChange": false,
      "scrollX": true,
      "scrollY": '300px',
      "scrollCollapse": true,
      "paging":false,
      "info" : false,
    } );

});
</script>

