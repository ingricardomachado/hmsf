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
          <h5><i class="fa fa-users" aria-hidden="true"></i> Usuarios</h5>
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
            <div class="col-sm-4 col-xs-12">
            </div>                            
            <div class="col-sm-8 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalUser(0);"><i class="fa fa-plus-circle"></i> Nuevo Usuario</a>
              <a href="{{ url('users.rpt_users') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a><br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="users-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Creado</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Creado</th>
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
  
<!-- Modal para Datos Personales -->
<div class="modal inmodal" id="modalUser" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="user"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos Personales -->

<!-- Modal para eliminar -->
<div class="modal inmodal" id="modalDelete" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    {{ Form::open(array('url' => '', 'id' => 'form_delete', 'method' => 'GET'), ['' ])}}
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> <strong>Eliminar Usuario</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
        <input type="hidden" id="hdd_user_id" value=""/>
          <p>Está seguro que desea eliminar el usuario <b><span id="span_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete" class="btn btn-danger" data-dismiss="modal">Eliminar</button>
      </div>
    </div>
    {{ Form::close() }}
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

function showModalUser(user_id){
  url = '{{URL::to("users.load_user")}}/'+user_id;
  $('#user').load(url);  
  $("#modalUser").modal("show");
}

function showModalDelete(user_id, name){
  $('#hdd_user_id').val(user_id);
  $('#span_name').html(name);
  $("#modalDelete").modal("show");    
};

$('#btn_delete').on("click", function (e) { 
  var user_id = $('#hdd_user_id').val();
  url = `{{URL::to('users.delete/')}}/${user_id}`;
  $('#form_delete').attr('method', 'GET');
  $('#form_delete').attr('action', url);
  $('#form_delete').submit();
});
  
function change_status(user_id){
  $.ajax({
      url: `{{URL::to("users.status")}}`,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}", 
        user_id:user_id,
      },
  })
  .done(function(response) {
      $('#users-table').DataTable().draw(); 
      setTimeout(function() {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: 2000
      };
      toastr.success('Estado cambiado exitosamente', '{{ Session::get('app_name') }}');
      }, 1000);
  })
  .fail(function() {
      console.log("error cambiando estado");
  });
}  

$(document).ready(function(){
    
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#users-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('users.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'name'},
            { data: 'role', name: 'role', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
                

     //Notifications
    setTimeout(function() {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            timeOut: 2000
        };
        if('{{ Session::get('notify') }}'=='create' &&  '{{ Session::get('create_notification') }}'=='1'){
          toastr.success('Usuario añadido exitosamente', '{{ Session::get('app_name') }}');
        }
        if('{{ Session::get('notify') }}'=='update' &&  '{{ Session::get('update_notification') }}'=='1'){
          toastr.success('Usuario actualizado exitosamente', '{{ Session::get('app_name') }}');
        }
        if('{{ Session::get('notify') }}'=='delete' &&  '{{ Session::get('delete_notification') }}'=='1'){
          toastr.success('Usuario eliminado exitosamente', '{{ Session::get('app_name') }}');
        }
    }, 1300);  
 
  });
  </script>
@endpush