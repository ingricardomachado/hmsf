@extends('layouts.app')

@push('stylesheets')
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
          <h5><i class="fa fa-truck" aria-hidden="true"></i> Proveedores</h5>
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
            <div class="col-sm-4 col-xs-12">
                {{ Form::select('supplier_category_filter', $supplier_categories, null, ['id'=>'supplier_category_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-8 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalSupplier(0);"><i class="fa fa-plus-circle"></i> Nuevo Proveedor</a>
                <a href="{{ url('suppliers.rpt_suppliers') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
                <br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="suppliers-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="10%">Nombre</th>
                    <th width="10%">Teléfono</th>
                    <th width="10%">Correo</th>
                    <th width="10%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Estado</th>
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
<div class="modal inmodal" id="modalSupplier" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="supplier"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteSupplier" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Proveedor</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_supplier_id" value=""/>
          <p>Esta seguro que desea eliminar el proveedor <b><span id="supplier_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_supplier" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar-->
@endsection

@push('scripts')
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script>
  
function showModalSupplier(id){
  url = '{{URL::to("suppliers.load")}}/'+id;
  $('#supplier').load(url);  
  $("#modalSupplier").modal("show");
}
 
function change_status(id){
  $.ajax({
      url: `{{URL::to("suppliers.status")}}/${id}`,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#suppliers-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
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

function showModalDelete(id, name){
  $('#hdd_supplier_id').val(id);
  $('#supplier_name').html(name);
  $("#modalDeleteSupplier").modal("show");    
};
    
$("#btn_delete_supplier").on('click', function(event) {    
    supplier_delete($('#hdd_supplier_id').val());
});

function supplier_delete(id){  
  $.ajax({
      url: `{{URL::to("suppliers")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteSupplier').modal('toggle');
      $('#suppliers-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteSupplier').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function supplier_CRUD(id){
        
    var validator = $("#form_supplier").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_supplier")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("suppliers")}}':'{{URL::to("suppliers")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalSupplier').modal('toggle');
          $('#suppliers-table').DataTable().draw(); 
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

$("#supplier_category_filter").change( event => {
  $('#suppliers-table').DataTable().draw();
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#suppliers-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('suppliers.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.supplier_category_filter = $('#supplier_category_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'name', orderable: false, searchable: true},
            { data: 'phone',   name: 'phone', orderable: false, searchable: false},
            { data: 'email', name: 'email', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });

    $("#supplier_category_filter").select2({
      language: "es",
      placeholder: "Categorías - Todas",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

});

</script>
@endpush