@extends('layouts.app')

@push('stylesheets')
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
          <h5><i class="fa fa-folder-o" aria-hidden="true"></i> Clasificación de Documentos</h5>
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
            </div>
            <div class="col-sm-9 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalDocumentType(0);"><i class="fa fa-plus-circle"></i> Nueva Clasificación</a>
                <a href="{{ url('document_types.rpt_document_types') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
                <br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="document_types-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="80%">Nombre</th>
                    <th width="20%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Nombre</th>
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
<div class="modal inmodal" id="modalDocumentType" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="document_type"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteDocumentType" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Clasificación</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_document_type_id" value=""/>
          <p>Esta seguro que desea eliminar la clasificación <b><span id="document_type_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_document_type" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar-->
@endsection

@push('scripts')
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script>
  
function showModalDocumentType(id){
  url = '{{URL::to("document_types.load")}}/'+id;
  $('#document_type').load(url);  
  $("#modalDocumentType").modal("show");
}
 
function change_status(id){
  $.ajax({
      url: `{{URL::to("document_types.status")}}/${id}`,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#document_types-table').DataTable().draw();
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
  $('#hdd_document_type_id').val(id);
  $('#document_type_name').html(name);
  $("#modalDeleteDocumentType").modal("show");    
};
    
$("#btn_delete_document_type").on('click', function(event) {    
    document_type_delete($('#hdd_document_type_id').val());
});

function document_type_delete(id){  
  $.ajax({
      url: `{{URL::to("document_types")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteDocumentType').modal('toggle');
      $('#document_types-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteDocumentType').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function document_type_CRUD(id){
        
    var validator = $("#form_document_type").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_document_type")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("document_types")}}':'{{URL::to("document_types")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalDocumentType').modal('toggle');
          $('#document_types-table').DataTable().draw(); 
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

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#document_types-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: '{!! route('document_types.datatable') !!}',
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'name', orderable: true, searchable: true},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });

});

</script>
@endpush