@extends('layouts.app')

@push('stylesheets')
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- Magnific Popup -->
<link rel="stylesheet" href="{{ URL::asset('js/plugins/magnific-popup/magnific-popup.css') }}">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
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
          <h5><i class="fa fa-truck" aria-hidden="true"></i> Operaciones</h5>
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
            {{ Form::open(array('url' => '', 'id' => 'form_rpt', 'method' => 'post'), ['' ])}}
            <div class="form-group col-sm-12">
              <div class="col-sm-2">
                  <label>Desde *</label>
                  <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {{ Form::text ('start_filter', $start, ['id'=>'start_filter', 'class'=>'form-control', 'placeholder'=>'', 'style'=>'font-size:12px', 'required']) }}
                  </div>
              </div>
              <div class="col-sm-2">
                  <label>Hasta *</label>
                  <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    {{ Form::text ('end_filter', $end, ['id'=>'end_filter', 'class'=>'form-control', 'placeholder'=>'', 'style'=>'font-size:12px', 'required']) }}
                  </div>
              </div>
              <div class="col-sm-68col-xs-12 text-right">
                  <button type="button" name="btn_print" id="btn_print" class="btn btn-sm btn-default" title="Imprimir"><i class="fa fa-print" aria-hidden="true"></i></button>
                  <br>
              </div>
            </div>
            <div class="form-group col-sm-12">
              <div class="col-sm-3 col-xs-12">
                  {{ Form::select('partner_filter', $partners, null, ['id'=>'partner_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
              </div>
              <div class="col-sm-3 col-xs-12">
                  {{ Form::select('customer_filter', $customers, null, ['id'=>'customer_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
              </div>
              <div class="col-sm-3 col-xs-12">
                  {{ Form::select('user_filter', $users, null, ['id'=>'user_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
              </div>
              <div class="col-sm-3 col-xs-12">
                  {{ Form::select('status_filter', ['1'=>'Proceso', '2'=>'Pendiente', '3'=>'Entregado'], null, ['id'=>'status_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
              </div>
            </div>
            {{ Form::close() }}
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12" style="font-size:12px">
              <table class="table table-striped table-hover" id="operations-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="5%">Nro</th>
                    <th width="10%">Fecha</th>
                    <th width="15%">Socio</th>
                    <th width="15%">Cliente</th>
                    <th width="15%">Mensajero</th>
                    <th width="5%">Folio</th>
                    <th width="10%" class="text-right">Retorno</th>
                    <th width="5%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                </tfoot>
              </table>
              <br><br><br><br>
            </div>
          </div>
        </div>
        <!-- /ibox-content- -->

      </div>
    </div>
  </div>
</div>
  
<!-- Modal para Datos -->
<div class="modal inmodal" id="modalOperation" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="operation"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteOperation" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Operación</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_operation_id" value=""/>
          <p>Esta seguro que desea eliminar la operación <b><span id="operation_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_operation" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar-->

<!-- Modal para pasar a pendiente -->
<div class="modal inmodal" id="modalStatus" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="modal_status"></div>
    </div>
  </div>
</div>
<!-- /Modal para pasar a pendiente -->

<!-- Modal para CRUD comentarios -->
<div class="modal inmodal" id="modalComments" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="modal_comments"></div>
    </div>
  </div>
</div>
<!-- /Modal para CRUD commentarios -->

@endsection

@push('scripts')
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Magnific Popup -->
<script src="{{ URL::asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script>
  
function showModalComments(id){
  url = '{{URL::to("operations.load_comments")}}/'+id;
  $('#modal_comments').load(url);  
  $("#modalComments").modal("show");
}

function showModalStatus(id){
  url = '{{URL::to("operations.load_status")}}/'+id;
  $('#modal_status').load(url);  
  $("#modalStatus").modal("show");
}

function showModalOperation(id){
  url = '{{URL::to("operations.load")}}/'+id;
  $('#operation').load(url);  
  $("#modalOperation").modal("show");
}
 
function showModalDelete(id, name){
  $('#hdd_operation_id').val(id);
  $('#operation_name').html(name);
  $("#modalDeleteOperation").modal("show");    
};
    
$("#btn_delete_operation").on('click', function(event) {    
    operation_delete($('#hdd_operation_id').val());
});

function operation_delete(id){  
  $.ajax({
      url: `{{URL::to("operations")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteOperation').modal('toggle');
      $('#operations-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteOperation').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function operation_CRUD(id){
        
    var validator = $("#form_operation").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_operation")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("operations")}}':'{{URL::to("operations")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalOperation').modal('toggle');
          $('#operations-table').DataTable().draw(false); 
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_submit').attr('disabled', false);
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
}

$("#partner_filter").change( event => {
  $('#operations-table').DataTable().draw(false);
});

$("#customer_filter").change( event => {
  $('#operations-table').DataTable().draw(false);
});

$("#user_filter").change( event => {
  $('#operations-table').DataTable().draw(false);
});

$("#status_filter").change( event => {
  $('#operations-table').DataTable().draw(false);
});

$('#btn_print').click(function(event) {
  var validator = $("#form_rpt" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    url = '{{URL::to("operations.rpt_operations")}}';
    $('#form_rpt').attr('action', url);
    $('#form_rpt').attr('target', '_blank');
    $('#form_rpt').submit();
  }
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#operations-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('operations.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.start_filter = $('#start_filter').val();
                d.end_filter = $('#end_filter').val();
                d.partner_filter = $('#partner_filter').val();
                d.customer_filter = $('#customer_filter').val();
                d.user_filter = $('#user_filter').val();
                d.status_filter = $('#status_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'number', orderable: true, searchable: true},
            { data: 'date',   name: 'date', orderable: true, searchable: false},
            { data: 'partner',   name: 'partner', orderable: true, searchable: false},
            { data: 'customer',   name: 'customer', orderable: true, searchable: false},
            { data: 'user',   name: 'user', orderable: true, searchable: false},
            { data: 'folio',   name: 'folio', orderable: false, searchable: false},
            { data: 'return_amount',   name: 'return_amount', orderable: false, searchable: false},
            { data: 'status',   name: 'status', orderable: false, searchable: false}
        ],
        "fnDrawCallback": function () {
            $('.popup-link').magnificPopup({
              type: 'image',
              closeOnContentClick: true,
              closeBtnInside: false,
              fixedContentPos: true,
              mainClass: 'my-custom-class'
            });
        }
    });

    $('#start_filter').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
    }).on("changeDate", function (e) {
        $('#operations-table').DataTable().draw();
    });

    $('#end_filter').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
    }).on("changeDate", function (e) {
        $('#operations-table').DataTable().draw();
    });
    
    $("#partner_filter").select2({
      language: "es",
      placeholder: "Socio - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

    $("#customer_filter").select2({
      language: "es",
      placeholder: "Cliente - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

    $("#user_filter").select2({
      language: "es",
      placeholder: "Mensajero - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

    $("#status_filter").select2({
      language: "es",
      placeholder: "Estado - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

});
</script>
@endpush