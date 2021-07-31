@extends('layouts.app')

@push('stylesheets')
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
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
          <h5><i class="fa fa-folder-o" aria-hidden="true"></i> Visitas</h5>
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
            {{ Form::open(array('url' => '', 'id' => 'form_rpt', 'method' => 'post'), ['' ])}}
            <div class="form-group col-sm-2">
                <label>Desde *</label>
                <div class="input-group date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {{ Form::text ('start_filter', $start, ['id'=>'start_filter', 'class'=>'form-control', 'placeholder'=>'', 'style'=>'font-size:13px', 'required']) }}
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label>Hasta *</label>
                <div class="input-group date">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {{ Form::text ('end_filter', $end, ['id'=>'end_filter', 'class'=>'form-control', 'placeholder'=>'', 'style'=>'font-size:13px', 'required']) }}
                </div>
            </div>
            <div class="form-group col-sm-3">
              <label>Vigilante</label>
              {{ Form::select('user_filter', $users, null, ['id'=>'user_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="form-group col-sm-3">
              <label>Propiedad</label>
              {{ Form::select('property_filter', $properties, null, ['id'=>'property_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
            </div>
            <div class="col-sm-2 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalVisit(0);"><i class="fa fa-plus-circle"></i> Nueva Visita</a>
                <button type="button" name="btn_print" id="btn_print" class="btn btn-default" title="Imprimir"><i class="fa fa-print" aria-hidden="true"></i></button>
                <br>
            </div>
            {{ Form::close() }}
            <div class="col-sm-12">
              @include('partials.errors')
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="visits-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th width="25%">Visitante</th>
                    <th width="30%">Visita</th>
                    <th width="20%">Propiedad</th>
                    <th width="15%">Registrada por</th>
                    <th width="10%">Soporte</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Visitante</th>
                    <th>Visita</th>
                    <th>Propiedad</th>
                    <th>Registrada por</th>
                    <th>Soporte</th>
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
<div class="modal inmodal" id="modalVisit" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="visit"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteVisit" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Ingreso</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_visit_id" value=""/>
          <p>Esta seguro que desea eliminar la visita <b><span id="visit_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_visit" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar-->
@endsection

@push('scripts')
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Magnific Popup -->
<script src="{{ URL::asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script>
  
function showModalVisit(id){
  url = '{{URL::to("visits.load")}}/'+id;
  $('#visit').load(url);  
  $("#modalVisit").modal("show");
}
 
function showModalDelete(id, name){
  $('#hdd_visit_id').val(id);
  $('#visit_name').html(name);
  $("#modalDeleteVisit").modal("show");    
};
    
$("#btn_delete_visit").on('click', function(event) {    
    visit_delete($('#hdd_visit_id').val());
});

function visit_delete(id){  
  $.ajax({
      url: `{{URL::to("visits")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteVisit').modal('toggle');
      $('#visits-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteVisit').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function visit_CRUD(id){
        
    var validator = $("#form_visit").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_visit")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("visits")}}':'{{URL::to("visits")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalVisit').modal('toggle');
          $('#visits-table').DataTable().draw(false); 
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

$("#user_filter").change( event => {
  $('#visits-table').DataTable().draw(false);
});

$("#property_filter").change( event => {
  $('#visits-table').DataTable().draw(false);
});

$('#btn_print').click(function(event) {
  var validator = $("#form_rpt" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    url = '{{URL::to("visits.rpt_visits")}}';
    $('#form_rpt').attr('action', url);
    $('#form_rpt').attr('target', '_blank');
    $('#form_rpt').submit();
  }
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#visits-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('visits.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.start_filter = $('#start_filter').val();
                d.end_filter = $('#end_filter').val();
                d.user_filter = $('#user_filter').val();
                d.property_filter = $('#property_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'visitor',   name: 'visitor', orderable: false, searchable: true},
            { data: 'visit',   name: 'visit', orderable: false, searchable: true},
            { data: 'property',   name: 'property', orderable: false, searchable: true},
            { data: 'user', name: 'user', orderable: false, searchable: false },
            { data: 'file', name: 'file', orderable: false, searchable: false }
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

  $('#start_filter').datepicker({
      format: 'dd/mm/yyyy',
      todayHighlight: true,
      autoclose: true,
      language: 'es',
  }).on("changeDate", function (e) {
      $('#visits-table').DataTable().draw();
  });

  $('#end_filter').datepicker({
      format: 'dd/mm/yyyy',
      todayHighlight: true,
      autoclose: true,
      language: 'es',
  }).on("changeDate", function (e) {
      $('#visits-table').DataTable().draw();
  });

  $("#user_filter").select2({
    language: "es",
    placeholder: "Todos",
    minimumResultsForSearch: 10,
    allowClear: true,
    width: '100%'
  });

  $("#property_filter").select2({
    language: "es",
    placeholder: "Todas",
    minimumResultsForSearch: 10,
    allowClear: true,
    width: '100%'
  });
});
</script>
@endpush