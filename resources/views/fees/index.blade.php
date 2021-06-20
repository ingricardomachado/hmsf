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
          <h5><i class="fa fa-file-text-o" aria-hidden="true"></i> Cuotas por Cobrar</h5>
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
                {{ Form::select('income_type_filter', $income_types, null, ['id'=>'income_type_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-6 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalFee(0);" title="Registrar cuota a una sola propiedad"><i class="fa fa-plus-circle"></i> Una cuota</a>
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalMultipleFee();" title="Registrar cuota a multiples propiedades"><i class="fa fa-plus-circle"></i> Multiples cuotas</a>
                <br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="fees-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="30%">Cuota</th>
                    <th width="10%">Propiedad</th>
                    <th width="15%">Monto</th>
                    <th width="10%">Aplicación</th>
                    <th width="10%">Vencimiento</th>
                    <th width="10%">Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Cuota</th>
                    <th>Propiedad</th>
                    <th>Monto</th>
                    <th>Aplicación</th>
                    <th>Vencimiento</th>
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
<div class="modal inmodal" id="modalFee" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="fee"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para crear multiples cuotas -->
<div class="modal inmodal" id="modalMultipleFee" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content animated fadeIn">
      <div id="multiple_fee"></div>
    </div>
  </div>
</div>
<!-- /Modal para crear multilples cuotas -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteFee" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Cuota</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_fee_id" value=""/>
          <p>
            Esta seguro que desea eliminar la cuota?
            <div><b>Propiedad</b> <span id="fee_property"></span></div>
            <div><b>Concepto</b> <span id="fee_concept"></span></div>
            <div><b>Monto</b> <span id="fee_amount"></span></div>
          </p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_fee" class="btn btn-danger">Eliminar</button>
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
  
function showModalFee(id){
  url = '{{URL::to("fees.load")}}/'+id;
  $('#fee').load(url);  
  $("#modalFee").modal("show");
}
 
function showModalMultipleFee(){
  url = '{{URL::to("fees.create_multiple")}}';
  $('#multiple_fee').load(url);  
  $("#modalMultipleFee").modal("show");
}

function showModalDelete(id, concept, property, amount){
  $('#hdd_fee_id').val(id);
  $('#fee_concept').html(concept);
  $('#fee_property').html(property);
  $('#fee_amount').html(amount);
  $("#modalDeleteFee").modal("show");    
};
    
$("#btn_delete_fee").on('click', function(event) {    
    fee_delete($('#hdd_fee_id').val());
});

function fee_delete(id){  
  $.ajax({
      url: `{{URL::to("fees")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteFee').modal('toggle');
      $('#fees-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteFee').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function fee_CRUD(id){
        
    var validator = $("#form_fee").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_fee")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("fees")}}':'{{URL::to("fees")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalFee').modal('toggle');
          $('#fees-table').DataTable().draw(); 
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.visivility == 422){
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
  $('#fees-table').DataTable().draw();
});

$("#income_type_filter").change( event => {
  $('#fees-table').DataTable().draw();
});

$(fee).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#fees-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('fees.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.property_filter = $('#property_filter').val();
                d.income_type_filter = $('#income_type_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'fee', name: 'concept', orderable: false, searchable: true },
            { data: 'property',   name: 'property', orderable: false, searchable: false},
            { data: 'amount', name: 'amount', orderable: false, searchable: false },
            { data: 'date',   name: 'date', orderable: false, searchable: false},
            { data: 'due_date', name: 'due_date', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ],
    });

    $("#property_filter").select2({
      language: "es",
      placeholder: "Propiedades - Todas",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

    $("#income_type_filter").select2({
      language: "es",
      placeholder: "Tipo de cuota - Todas",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

});

</script>
@endpush