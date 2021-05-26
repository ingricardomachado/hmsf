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
          <h5><i class="fa fa-users" aria-hidden="true"></i> Propietarios</h5>
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
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalOwner(0);"><i class="fa fa-plus-circle"></i> Nuevo Propietario</a>
              <a href="{{ url('owners.rpt_owners') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a><br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="owners-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="10%">Nombre</th>
                    <th width="25%">Propiedades</th>
                    <th width="10%">Celular</th>
                    <th width="10%">Teléfono</th>
                    <th width="10%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Propiedades</th>
                    <th>Celular</th>
                    <th>Teléfono</th>
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
<div class="modal inmodal" id="modalOwner" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="owner"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar usuario -->
<div class="modal inmodal" id="modalDeleteOwner" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Propiedad</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_owner_id" value=""/>
          <p>Esta seguro que desea eliminar el cliente <b><span id="owner_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_owner" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar usuario-->
@endsection

@push('scripts')
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script>
  
function showModalOwner(id){
  url = '{{URL::to("owners.load")}}/'+id;
  $('#owner').load(url);  
  $("#modalOwner").modal("show");
}

function change_status(id){
  $.ajax({
      url: `{{URL::to("owners.status")}}/${id}`,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#owners-table').DataTable().draw();
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
  $('#hdd_owner_id').val(id);
  $('#owner_name').html(name);
  $("#modalDeleteOwner").modal("show");    
};
    
$("#btn_delete_owner").on('click', function(event) {    
    owner_delete($('#hdd_owner_id').val());
});

function owner_delete(id){  
  $.ajax({
      url: `{{URL::to("owners")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteOwner').modal('toggle');
      $('#owners-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteOwner').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function owner_CRUD(id){
        
    var validator = $("#form_owner").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_owner")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("owners")}}':'{{URL::to("owners")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalOwner').modal('toggle');
          $('#owners-table').DataTable().draw(); 
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
    var table=$('#owners-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: '{!! route('owners.datatable') !!}',
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'users.name', orderable: true, searchable: true},
            { data: 'properties',   name: 'properties', orderable: false, searchable: true},
            { data: 'cell',   name: 'cell', orderable: true, searchable: false},
            { data: 'phone',   name: 'phone', orderable: true, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });

});

</script>
@endpush