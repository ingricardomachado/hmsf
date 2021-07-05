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
          <h5><i class="fa fa-folder-o" aria-hidden="true"></i> Pagos de cuotas</h5>
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
                {{ Form::select('property_filter', $properties, null, ['id'=>'property_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-3 col-xs-12">
                {{ Form::select('status_filter', ['A' => 'Aprobados', 'P' => 'Por confirmar', 'R' => 'Rechazados'], null, ['id'=>'status_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-6 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalPayment(0);"><i class="fa fa-plus-circle"></i> Nuevo Pago</a>
                <br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="payments-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="10%">Fecha</th>
                    <th width="35%">Pago</th>
                    <th width="20%">Propiedad</th>
                    <th width="10%">Monto</th>
                    <th width="10%">Soporte</th>
                    <th width="10%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Fecha</th>
                    <th>Pago</th>
                    <th>Propiedad</th>
                    <th>Monto</th>
                    <th>Soporte</th>
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

<!-- Modal para confirmar pago -->
<div class="modal inmodal" id="modalConfirmPayment" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="confirm_payment"></div>
    </div>
  </div>
</div>
<!-- /Modal para confirmar pago -->

<!-- Modal para mostrar -->
<div class="modal inmodal" id="modalPaymentInfo" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="payment_info"></div>
    </div>
  </div>
</div>
<!-- /Modal para mostrar -->

<!-- Modal para Datos -->
<div class="modal inmodal" id="modalPayment" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content animated fadeIn">
      <div id="payment"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeletePayment" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Pago</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_payment_id" value=""/>
          <p>Esta seguro que desea eliminar el pago <b><span id="payment_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_payment" class="btn btn-danger">Eliminar</button>
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
  
function showModalConfirmPayment(id){
  url = '{{URL::to("payments.load_confirm")}}/'+id;
  $('#confirm_payment').load(url);  
  $("#modalConfirmPayment").modal("show");
}

function showModalPaymentInfo(id){
  url = '{{URL::to("payments.info")}}/'+id;
  $('#payment_info').load(url);  
  $("#modalPaymentInfo").modal("show");
}

function showModalPayment(id){
  url = '{{URL::to("payments.load")}}/'+id;
  $('#payment').load(url);  
  $("#modalPayment").modal("show");
}
 
function showModalDelete(id, name){
  $('#hdd_payment_id').val(id);
  $('#payment_name').html(name);
  $("#modalDeletePayment").modal("show");    
};
    
$("#btn_delete_payment").on('click', function(event) {    
    payment_delete($('#hdd_payment_id').val());
});

function payment_delete(id){  
  $.ajax({
      url: `{{URL::to("payments")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeletePayment').modal('toggle');
      $('#payments-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeletePayment').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function payment_CRUD(id){
        
    var validator = $("#form_payment").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_payment")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("payments")}}':'{{URL::to("payments")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalPayment').modal('toggle');
          $('#payments-table').DataTable().draw(false); 
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

$("#property_filter").change( event => {
  $('#payments-table').DataTable().draw(false);
});

$("#status_filter").change( event => {
  $('#payments-table').DataTable().draw(false);
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#payments-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('payments.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.property_filter = $('#property_filter').val();
                d.status_filter = $('#status_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'date',   name: 'date', orderable: false, searchable: false},
            { data: 'payment',   name: 'payment', orderable: false, searchable: true},
            { data: 'property',   name: 'property', orderable: false, searchable: false},
            { data: 'amount', name: 'amount', orderable: false, searchable: false },
            { data: 'file', name: 'file', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false }
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

    $("#property_filter").select2({
      language: "es",
      placeholder: "Propiedad - Todas",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

    $("#income_type_filter").select2({
      language: "es",
      placeholder: "Tipo de Ingreso - Todas",
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