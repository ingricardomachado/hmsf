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
          <h5><i class="fa fa-users" aria-hidden="true"></i> Socios Comerciales</h5>
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
            <div class="col-sm-3 col-xs-12">
            </div>
            <div class="col-sm-9 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalPartner(0);"><i class="fa fa-plus-circle"></i> Nuevo Socio Comercial</a>
              <a href="{{ url('partners.rpt_partners') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a><br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="partners-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="20%">Nombre</th>
                    <th width="10%">Celular</th>
                    <th width="10%">Comisi??n</th>
                    <th width="10%">Clientes</th>
                    <th width="10%">Operaciones</th>
                    <th width="10%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Celular</th>
                    <th>Comisi??n</th>
                    <th>Clientes</th>
                    <th>Operaciones</th>                    
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
<div class="modal inmodal" id="modalPartner" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="partner"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar usuario -->
<div class="modal inmodal" id="modalDeletePartner" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Socio Comercial</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_partner_id" value=""/>
          <p>Esta seguro que desea eliminar el socio comercial <b><span id="partner_name"></span></b> ?</p>
          <p>Al eliminar el socio comercial, se eliminar??n todas las operaciones asociadas al ??l.</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_partner" class="btn btn-danger">Eliminar</button>
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
  
function showModalPartner(id){
  url = '{{URL::to("partners.load")}}/'+id;
  $('#partner').load(url);  
  $("#modalPartner").modal("show");
}

function change_status(id){
  $.ajax({
      url: `{{URL::to("partners.status")}}/${id}`,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#partners-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
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
  
function showModalDelete(id, name){
  $('#hdd_partner_id').val(id);
  $('#partner_name').html(name);
  $("#modalDeletePartner").modal("show");    
};
    
$("#btn_delete_partner").on('click', function(event) {    
    partner_delete($('#hdd_partner_id').val());
});

function partner_delete(id){  
  $.ajax({
      url: `{{URL::to("partners")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeletePartner').modal('toggle');
      $('#partners-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeletePartner').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function partner_CRUD(id){
        
    var validator = $("#form_partner").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_partner")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("partners")}}':'{{URL::to("partners")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalPartner').modal('toggle');
          $('#partners-table').DataTable().draw(false); 
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
    var table=$('#partners-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: '{!! route('partners.datatable') !!}',
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'users.full_name', orderable: true, searchable: true},
            { data: 'cell',   name: 'cell', orderable: true, searchable: false},
            { data: 'tax',   name: 'tax', orderable: true, searchable: false},
            { data: 'customers',   name: 'customers', orderable: false, searchable: false},
            { data: 'operations',   name: 'operations', orderable: false, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });

});

</script>
@endpush