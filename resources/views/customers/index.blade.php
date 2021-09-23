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
          <h5><i class="fa fa-wrench" aria-hidden="true"></i> Clientes</h5>
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
                {{ Form::select('partner_filter', $partners, null, ['id'=>'partner_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-8 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalCustomer(0);"><i class="fa fa-plus-circle"></i> Nuevo Cliente</a>
                <a href="{{ url('customers.rpt_customers') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
                <br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="customers-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="25%">Nombre</th>
                    <th width="10%">Contrato</th>
                    <th width="15%">Socio Comercial</th>
                    <th width="10%">Celular</th>
                    <th width="10%">Comisión</th>
                    <th width="10%">Operaciones</th>
                    <th width="10%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Contrato</th>
                    <th>Socio Comercial</th>
                    <th>Celular</th>
                    <th>Comisión</th>
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
<div class="modal inmodal" id="modalCustomer" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="customer"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteCustomer" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Cliente</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_customer_id" value=""/>
          <p>Esta seguro que desea eliminar el cliente <b><span id="customer_name"></span></b> ?</p>
          <p>Al eliminar el cliente, se elliminarán todas las operaciones asociadas al él.</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_customer" class="btn btn-danger">Eliminar</button>
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
  
function showModalCustomer(id){
  url = '{{URL::to("customers.load")}}/'+id;
  $('#customer').load(url);  
  $("#modalCustomer").modal("show");
}
 
function change_status(id){
  $.ajax({
      url: `{{URL::to("customers.status")}}/${id}`,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#customers-table').DataTable().draw(false);
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
  $('#hdd_customer_id').val(id);
  $('#customer_name').html(name);
  $("#modalDeleteCustomer").modal("show");    
};
    
$("#btn_delete_customer").on('click', function(event) {    
    customer_delete($('#hdd_customer_id').val());
});

function customer_delete(id){  
  $.ajax({
      url: `{{URL::to("customers")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteCustomer').modal('toggle');
      $('#customers-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteCustomer').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function customer_CRUD(id){
        
    var validator = $("#form_customer").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_customer")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("customers")}}':'{{URL::to("customers")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalCustomer').modal('toggle');
          $('#customers-table').DataTable().draw(false); 
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
  $('#customers-table').DataTable().draw(false);
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#customers-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('customers.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.partner_filter = $('#partner_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'customers.full_name', orderable: false, searchable: true},
            { data: 'contract',   name: 'contract', orderable: false, searchable: false},
            { data: 'partner',   name: 'partner', orderable: false, searchable: false},
            { data: 'cell',   name: 'cell', orderable: false, searchable: false},
            { data: 'tax',   name: 'tax', orderable: false, searchable: false},
            { data: 'operations', name: 'operations', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });

    $("#partner_filter").select2({
      language: "es",
      placeholder: "Socio Comercial - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

});
</script>
@endpush