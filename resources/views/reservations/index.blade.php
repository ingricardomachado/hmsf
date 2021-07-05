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
          <h5><i class="fa fa-calendar-o" aria-hidden="true"></i> Reservaciones</h5>
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
                {{ Form::select('status_filter', ['P'=>'Pendientes', 'A'=>'Aprobadas', 'R'=>'Rechazadas'], null, ['id'=>'status_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-6 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalSelectFacility();"><i class="fa fa-plus-circle"></i> Reservar</a>
                <br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="reservations-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th>Instalación</th>
                    <th>Propiedad</th>
                    <th width="20%">Notas</th>
                    <th width="20%">Observaciones</th>
                    <th>Costo</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Instalación</th>
                    <th>Propiedad</th>
                    <th>Notas</th>
                    <th>Observaciones</th>
                    <th>Costo</th>
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
  
<!-- Modal para confirmar reservacion -->
<div class="modal inmodal" id="modalConfirmReservation" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="confirm_reservation"></div>
    </div>
  </div>
</div>
<!-- /Modal para confirmar reservacion -->

<!-- Modal para selecionar instalacion -->
<div class="modal inmodal" id="modalSelectFacility" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <form action="" id="form_select_facility" method="POST" role="form">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-umbrella" aria-hidden="true"></i> Instalación</h5>
        </div>      
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-sm-12">  
              <label>Instalación *</label> <small>Selecione la instalación a reservar.</small>
              {{ Form::select('facility', $facilities, null, ['id'=>'facility', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
            </div>          
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>        
          <button type="button" id="btn_next" class="btn btn-sm btn-primary">Siguiente</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /Modal para seleccionar instalacion-->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteReservation" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Reservación</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_reservation_id" value=""/>
          <p>Esta seguro que desea eliminar el reservación <b><span id="reservation_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_reservation" class="btn btn-danger">Eliminar</button>
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
  
function showModalSelectFacility(){
  $("#modalSelectFacility").modal("show");
}

$('#btn_next').on("click", function (e) { 
  var facility_id = $('#facility').val();
  url = `{{URL::to('reserve/')}}/${facility_id}`;
  $('#form_select_facility').attr('action', url);
  $('#form_select_facility').submit();
});
 
function showModalConfirmReservation(id){
  url = '{{URL::to("reservations.load_confirm")}}/'+id;
  $('#confirm_reservation').load(url);  
  $("#modalConfirmReservation").modal("show");
}

function change_status(id){
  $.ajax({
      url: `{{URL::to("reservations.status")}}/${id}`,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#reservations-table').DataTable().draw(false);
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
  $('#hdd_reservation_id').val(id);
  $('#reservation_name').html(name);
  $("#modalDeleteReservation").modal("show");    
};
    
$("#btn_delete_reservation").on('click', function(event) {    
    reservation_delete($('#hdd_reservation_id').val());
});

function reservation_delete(id){  
  $.ajax({
      url: `{{URL::to("reservations")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteReservation').modal('toggle');
      $('#reservations-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteReservation').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

$("#property_filter").change( event => {
  $('#reservations-table').DataTable().draw(false);
});

$("#status_filter").change( event => {
  $('#reservations-table').DataTable().draw(false);
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#reservations-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('reservations.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.status_filter = $('#status_filter').val();
                d.property_filter = $('#property_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'facility',   name: 'facility', orderable: false, searchable: false},
            { data: 'property',   name: 'property', orderable: false, searchable: true},
            { data: 'notes', name: 'notes', orderable: false, searchable: false },
            { data: 'observations', name: 'observations', orderable: false, searchable: false },
            { data: 'cost', name: 'cost', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });

    $("#property_filter").select2({
      language: "es",
      placeholder: "Propiedades - Todas",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

    $("#status_filter").select2({
      language: "es",
      placeholder: "Estado - Todas",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });

    $("#facility").select2({
      language: "es",
      placeholder: "Seleccione",
      minimumResultsForSearch: 10,
      allowClear: false,
      dropdownParent: $('#modalSelectFacility .modal-content'),
      width: '100%'
    });

});
</script>
@endpush