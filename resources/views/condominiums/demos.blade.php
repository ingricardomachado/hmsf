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
          <h5><i class="fa fa-building-o" aria-hidden="true"></i> Condominios Demos</h5>
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
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalCondominium(0);"><i class="fa fa-plus-circle"></i> Nuevo Condominio</a>
                <a href="{{ url('condominiums.rpt_demos') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
                <br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="condominiums-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="10%">Condominio</th>
                    <th width="10%">Tipo</th>
                    <th width="10%">Max. Propiedades</th>
                    <th width="15%">Contacto</th>
                    <th width="10%">Días restantes</th>
                    <th width="10%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Condominio</th>
                    <th>Tipo</th>
                    <th>Max Propiedades</th>
                    <th>Contacto</th>
                    <th>Dias restantes</th>
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
<div class="modal inmodal" id="modalCondominium" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="condominium"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteCondominium" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Condominio</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_condominium_id" value=""/>
          <p>Esta seguro que desea eliminar el condominio <b><span id="condominium_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_condominium" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar-->

<!-- Modal para pasar a permanente -->
<div class="modal inmodal" id="modalPermanent" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-tag" aria-hidden="true"></i> <strong>Pasar a Permanente</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_permanent_id" value=""/>
          <p>Esta seguro que desea pasar a permanente el condominio <b><span id="condominium_permanent_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_permanent" class="btn btn-primary">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para pasar a permanente -->

@endsection

@push('scripts')
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script>
  
function showModalCondominium(id){
  url = '{{URL::to("condominiums.load")}}/'+id;
  $('#condominium').load(url);  
  $("#modalCondominium").modal("show");
}
 
function showModalPermanent(id, name){
  $('#hdd_permanent_id').val(id);
  $('#condominium_permanent_name').html(name);
  $("#modalPermanent").modal("show");    
};

$("#btn_permanent").on('click', function(event) {    
  var id=$('#hdd_permanent_id').val();
  $.ajax({
      url: `{{URL::to("condominiums.permanent")}}/${id}`,
  })
  .done(function(response) {
      $('#modalPermanent').modal('toggle');
      $('#condominiums-table').DataTable().draw(false);
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
});

function showModalDelete(id, name){
  $('#hdd_condominium_id').val(id);
  $('#condominium_name').html(name);
  $("#modalDeleteCondominium").modal("show");    
};
    
$("#btn_delete_condominium").on('click', function(event) {    
    condominium_delete($('#hdd_condominium_id').val());
});

function condominium_delete(id){  
  $.ajax({
      url: `{{URL::to("condominiums")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteCondominium').modal('toggle');
      $('#condominiums-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteCondominium').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function change_status(id){
  $.ajax({
      url: `{{URL::to("condominiums.status")}}/${id}`,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#condominiums-table').DataTable().draw(false);
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

function condominium_CRUD(id){
        
    var validator = $("#form_condominium").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_condominium")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("condominiums")}}':'{{URL::to("condominiums")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalCondominium').modal('toggle');
          $('#condominiums-table').DataTable().draw(false); 
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
    var table=$('#condominiums-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('condominiums.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.demo = 1;
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'name', orderable: false, searchable: true},
            { data: 'type',   name: 'type', orderable: false, searchable: false},
            { data: 'max_properties',   name: 'max_properties', orderable: false, searchable: false},
            { data: 'contact', name: 'contact', orderable: false, searchable: false },
            { data: 'remaining_days', name: 'remaining_days', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });

});

</script>
@endpush