@extends('layouts.app')

@push('stylesheets')
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- EasyAutocomplete -->
<link rel="stylesheet" type="text/css" href="{{ URL::asset('js/plugins/easyAutocomplete/dist/easy-autocomplete.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('js/plugins/easyAutocomplete/dist/easy-autocomplete.themes.min.css') }}">
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- CSS Datatables -->
<link href="{{ URL::asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@section('page-header')
@endsection

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox float-e-margins">
        
        <!-- ibox-title -->
        <div class="ibox-title">
          <h5><i class="fa fa-exchange" aria-hidden="true"></i> Movimiento de Puntos</h5>
            <div class="ibox-tools">
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
              <ul class="dropdown-menu dropdown-user">
                <li><a href="#">Config option 1</a></li>
                <li><a href="#">Config option 2</a></li>
              </ul>
              <a class="close-link"><i class="fa fa-times"></i></a>
            </div>
        </div>
        <!-- /ibox-title -->
                    
        <!-- ibox-content- -->
        <div class="ibox-content">
          <div class="row">
            {{ Form::open(array('url' => '', 'id' => 'form', 'method' => 'POST'), ['' ])}}
            {!! Form::hidden('hdd_customer_id', null, ['id'=>'hdd_customer_id']) !!}
              <div class="col-sm-1">
                <label>Buscar</label>
                {!! Form::checkbox('by_cell', null,  false, ['id'=>'by_cell', 'class'=>'i-checks']) !!}&nbsp;<i class="fa fa-phone" aria-hidden="true"></i>
              </div>
              <div class="form-group col-sm-6" id="div_customer_name">
                <label>Cliente</label>
                {!! Form::text('search_customer_name', null, ['id'=>'search_customer_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Buscar cliente por nombre...','required']) !!}
              </div>
              <div class="form-group col-sm-6" id="div_customer_cell" style="display: none">
                <label>Cliente</label>
                {!! Form::text('search_customer_cell', null, ['id'=>'search_customer_cell', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Buscar cliente por celular...','required']) !!}
              </div>
              <div class="form-group col-sm-2">
                <label>Desde</label>
                <div class="input-group date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {{ Form::text ('start_filter', $start, ['id'=>'start_filter', 'class'=>'form-control', 'placeholder'=>'', 'style'=>'font-size:13px', 'required']) }}
                </div>
              </div>
              <div class="form-group col-sm-2">
                <label>Hasta</label>
                <div class="input-group date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {{ Form::text ('end_filter', $end, ['id'=>'end_filter', 'class'=>'form-control', 'placeholder'=>'', 'style'=>'font-size:13px', 'required']) }}
                </div>
              </div>
              <div class="form-group col-sm-1 text-right">
                <label>&nbsp;</label>
                <div><button type="button" name="btn_print" id="btn_print" class="btn btn-default" title="Imprimir"><i class="fa fa-print" aria-hidden="true"></i></button></div>
              </div>
            
              <span id="point_movements_detail"></span>

            {{ Form::close() }}

          </div>
        </div>
        <!-- /ibox-content- -->

      </div>
    </div>
  </div>
</div>
  

@endsection

@push('scripts')
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<!-- EasyAutocomplete -->
<script type="text/javascript" src="{{ URL::asset('js/plugins/easyAutocomplete/dist/jquery.easy-autocomplete.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<script>

$('#by_cell').on('ifChanged', function(event){
  if(event.target.checked){
    $('#div_customer_cell').show();
    $('#div_customer_name').hide();
  }else{
    $('#div_customer_cell').hide();
    $('#div_customer_name').show();
  }
});  
  
    // *** FUNCION PARA BUSCAR CLIENTE POR NOMBRE***
    function customerNameAutocomplete(){
        var customer = $("#search_customer_name"),
        options = {
            url: function(key) {
                return "{{URL::to('get_customers_by_name')}}/"+key;
            },
            adjustWidth: false,
            requestDelay: 500,
            getValue: 'name',
            template: {
                type: "description",
                fields: {
                description: "cell"
                }
            },        
            list: {
                sort: {
                    enabled: true
                },                
                match: {
                    enabled: true
                },                
                onLoadEvent:function() {
                },
                onChooseEvent: function() {
                    var e = customer.getSelectedItemData();
                    $("#hdd_customer_id").val(e.id);
                    $("#search_customer_cell").val(e.cell);
                    load_point_movements();
                    set_start_date(e.created_at);
                }
            },
        };
        customer.easyAutocomplete(options);
    }
    
    // *** FUNCION PARA BUSCAR CLIENTE POR NIT***
    function customerCellAutocomplete(){
        var customer = $("#search_customer_cell"),
        options = {
            url: function(key) {
                return "{{URL::to('get_customers_by_cell')}}/"+key;
            },
            adjustWidth: false,
            requestDelay: 500,
            getValue: 'cell',
            template: {
                type: "description",
                fields: {
                description: "name"
                }
            },        
            list: {
                sort: {
                    enabled: true
                },
                match: {
                    enabled: true
                },                
                onLoadEvent:function() {
                },            
                onChooseEvent: function() {
                    var e = customer.getSelectedItemData();
                    $("#hdd_customer_id").val(e.id);
                    $("#search_customer_cell").val(e.cell);
                    load_point_movements();
                    set_start_date(e.created_at);
                }
            },
        };
        customer.easyAutocomplete(options);
    }
  
function set_start_date(date){
  var year=date.substring(0,4);
  var month=date.substring(5,7);
  var day=date.substring(8,10);
  start_date=new Date(year, month, day);
  $('#start_filter').datepicker('setStartDate', start_date);
}

function load_point_movements(){
  var validator = $("#form" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    $.ajax({
      url: `{{URL::to("consults.load_point_movements")}}`,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}", 
        customer_id:$('#hdd_customer_id').val(),
        start_filter:$('#start_filter').val(),
        end_filter:$('#end_filter').val(),
      },
    })
    .done(function(response) {
        $('#point_movements_detail').html(response);
    })
    .fail(function() {
        console.log("error cargando los movimientos del customero");
    });
  }
}
   
  $('#btn_print').click(function(event) {
    url = '{{URL::to("inventories.rpt_movements_customer")}}';
    $('#form_rpt').attr('action', url);
    $('#form_rpt').attr('target', '_blank');
    $('#form_rpt').submit();
  });
  
$(document).ready(function(){
                      
    customerCellAutocomplete();
    customerNameAutocomplete();
    
    // iCheck
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });

    //Datepicker 
    var date_input_1=$('#start_filter');
    date_input_1.datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
    }).on("changeDate", function (e) {
      load_point_movements();
    })

    var date_input_2=$('#end_filter');
    date_input_2.datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
    }).on("changeDate", function (e) {
      load_point_movements();
    })
});
  

  </script>
@endpush