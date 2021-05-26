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
          <h5><i class="fa fa-user" aria-hidden="true"></i> Contactos</h5>
            <div class="ibox-tools">
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
              <ul class="dropdown-menu dropdown-contact">
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
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalContact(0);"><i class="fa fa-plus-circle"></i> Nuevo Contacto</a>
              <a href="{{ url('contacts.rpt_contacts') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a><br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="contacts-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="20%">Nombre</th>
                    <th>Celular</th>
                    <th>Teléfono</th>
                    <th>Correo</th>                    
                    <th>Dirección</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Celular</th>
                    <th>Teléfono</th>
                    <th>Correo</th>                    
                    <th>Dirección</th>
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
  
<!-- Modal para mostrar -->
<div class="modal inmodal" id="modalShowContact" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content animated fadeIn">
      <div id="show_contact"></div>
    </div>
  </div>
</div>
<!-- /Modal para mostrar -->

<!-- Modal para Datos -->
<div class="modal inmodal" id="modalContact" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="contact"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteContact" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Contacto</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_contact_id" value=""/>
          <p>Esta seguro que desea eliminar el empleado <b><span id="contact_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_contact" class="btn btn-danger">Eliminar</button>
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
    
function showModalShowContact(id){
  url = '{{URL::to("contacts")}}/'+id;
  $('#show_contact').load(url);  
  $("#modalShowContact").modal("show");
}

function showModalContact(id){
  url = '{{URL::to("contacts.load")}}/'+id;
  $('#contact').load(url);  
  $("#modalContact").modal("show");
}
 
function change_status(id){
  $.ajax({
      url: `{{URL::to("contacts.status")}}/${id}`,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#contacts-table').DataTable().draw();
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
  $('#hdd_contact_id').val(id);
  $('#contact_name').html(name);
  $("#modalDeleteContact").modal("show");    
};
    
$("#btn_delete_contact").on('click', function(event) {    
    contact_delete($('#hdd_contact_id').val());
});

function contact_delete(id){  
  $.ajax({
      url: `{{URL::to("contacts")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteContact').modal('toggle');
      $('#contacts-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteContact').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function contact_CRUD(id){
        
    var validator = $("#form_contact").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_contact")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("contacts")}}':'{{URL::to("contacts")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalContact').modal('toggle');
          $('#contacts-table').DataTable().draw(); 
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
    var table=$('#contacts-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('contacts.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'name', orderable: false, searchable: false},
            { data: 'cell',   name: 'cell', orderable: false, searchable: false},
            { data: 'phone',   name: 'phone', orderable: false, searchable: false},
            { data: 'email',   name: 'email', orderable: false, searchable: false},
            { data: 'address',   name: 'address', orderable: false, searchable: false},
        ]
    });
});
</script>
@endpush