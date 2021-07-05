@extends('layouts.app')

@push('stylesheets')
<!-- CSS Datatables -->
<link href="{{ URL::asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
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
          <h5><i class="fa fa-th-list" aria-hidden="true"></i> Estado de Cuenta</h5>
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
            <input type="hidden" name="account" id="acount" class="form-control" value="{{ $account->id }}">
            <div class="form-group col-sm-2">
                <label>Desde *</label>
                <div class="input-group date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {{ Form::text ('start', $start, ['id'=>'start', 'class'=>'form-control', 'placeholder'=>'', 'style'=>'font-size:13px', 'required']) }}
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label>Hasta *</label>
                <div class="input-group date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {{ Form::text ('end', $end, ['id'=>'end', 'class'=>'form-control', 'placeholder'=>'', 'style'=>'font-size:13px', 'required']) }}
                </div>
            </div>
            {{ Form::close() }}
            <div class="col-sm-8 col-xs-12 text-right">
                <button type="button" id="btn_xls" class="btn btn-sm btn-primary btn-outline" title="Exportar Excel">XLS</button>                 
                <a href="{{ url('accounts.rpt_movements') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
                <a href="{{URL::to('accounts')}}" class="btn btn-sm btn-default">Regresar</a>
                <br><br>
            </div>
            <div class="col-sm-12">
              <span id="movements"></span>
            </div>
          </div>
        </div>
        <!-- /ibox-content- -->

      </div>
    </div>
  </div>
</div>
  
@endsection

@push('scripts')
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<script>
  
$('#btn_xls').on("click", function (e) { 
  url = `{{URL::to('accounts.xls_movements')}}`;
  $('#form').attr('method', 'POST');
  $('#form').attr('target', '_self');
  $('#form').attr('action', url);
  $('#form').submit();
});

function load_movements(){
  $.ajax({
      url: `{{URL::to("accounts.movements")}}`,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}",
        account: {{ $account->id }},
        start: $('#start').val(),
        end: $('#end').val() 
      },
  })
  .done(function(response) {
    $('#movements').html(response);
  })
  .fail(function(response) {
    if(response.status == 422){
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

$(document).ready(function(){
                      
    load_movements();

    //Datetimepicker 
    var d = new Date('{{ $account->date_initial_balance }}');
    d.setHours(0,0,0,0);    
    $("#start").datepicker({
        language: "es",
        startDate: d,
        format: "dd/mm/yyyy",
        autoclose: true
    }).on("changeDate", function (e) {
      startDate = new Date(e.date.valueOf());
      startDate.setDate(startDate.getDate(new Date(e.date.valueOf())));
      $('#end').datepicker('setStartDate', startDate);
      load_movements();
    });
    
    $("#end").datepicker({
        language: "es",
        startDate: d,
        format: "dd/mm/yyyy hh:ii",
        autoclose: true
    }).on("changeDate", function (e) {
      load_movements();
    });

});

</script>
@endpush