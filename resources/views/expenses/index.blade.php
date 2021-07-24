@extends('layouts.app')

@push('stylesheets')
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
          <h5><i class="fa fa-folder-o" aria-hidden="true"></i> Egresos</h5>
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
            {{ Form::open(array('url' => '', 'id' => 'form_rpt', 'method' => 'get'), ['' ])}}
            {{ Form::close() }}
            <div class="col-sm-3 col-xs-12">
                {{ Form::select('expense_type_filter', $expense_types, null, ['id'=>'expense_type_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-3 col-xs-12">
                {{ Form::select('supplier_filter', $suppliers, null, ['id'=>'supplier_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-6 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalExpense(0);"><i class="fa fa-plus-circle"></i> Nuevo Egreso</a>
                <br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="expenses-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="10%">Fecha</th>
                    <th width="35%">Egreso</th>
                    <th width="30%">Cuenta</th>
                    <th width="10%">Monto</th>
                    <th width="10%">Soporte</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Fecha</th>
                    <th>Egreso</th>
                    <th>Cuenta</th>
                    <th>Monto</th>
                    <th>Soporte</th>
                  </tr>
                </tfoot>
              </table>
              <br><br>
            </div>
          </div>
        </div>
        <!-- /ibox-content- -->

      </div>
    </div>
  </div>
</div>
  
<!-- Modal para Datos -->
<div class="modal inmodal" id="modalExpense" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="expense"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteExpense" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Egreso</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_expense_id" value=""/>
          <p>Esta seguro que desea eliminar el egreso <b><span id="expense_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_expense" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar-->
@endsection

@push('scripts')
<!-- Magnific Popup -->
<script src="{{ URL::asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script>
  
function showModalExpense(id){
  url = '{{URL::to("expenses.load")}}/'+id;
  $('#expense').load(url);  
  $("#modalExpense").modal("show");
}
 
function showModalDelete(id, name){
  $('#hdd_expense_id').val(id);
  $('#expense_name').html(name);
  $("#modalDeleteExpense").modal("show");    
};
    
$("#btn_delete_expense").on('click', function(event) {    
    expense_delete($('#hdd_expense_id').val());
});

function expense_delete(id){  
  $.ajax({
      url: `{{URL::to("expenses")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteExpense').modal('toggle');
      $('#expenses-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteExpense').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function expense_CRUD(id){
        
    var validator = $("#form_expense").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_expense")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("expenses")}}':'{{URL::to("expenses")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalExpense').modal('toggle');
          $('#expenses-table').DataTable().draw(false); 
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

$("#expense_type_filter").change( event => {
  $('#expenses-table').DataTable().draw(false);
});

$("#supplier_filter").change( event => {
  $('#expenses-table').DataTable().draw(false);
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#expenses-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('expenses.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.expense_type_filter = $('#expense_type_filter').val();
                d.supplier_filter = $('#supplier_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'date',   name: 'date', orderable: true, searchable: true},
            { data: 'expense',   name: 'expense', orderable: false, searchable: true},
            { data: 'account',   name: 'account', orderable: false, searchable: true},
            { data: 'amount', name: 'amount', orderable: false, searchable: false },
            { data: 'file', name: 'file', orderable: false, searchable: false }
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

    $("#expense_type_filter").select2({
      language: "es",
      placeholder: "Tipos de egresos - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

    $("#supplier_filter").select2({
      language: "es",
      placeholder: "Proveedores - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

});
</script>
@endpush