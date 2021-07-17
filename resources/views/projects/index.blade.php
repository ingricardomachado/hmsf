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
          <h5><i class="fa fa-wrench" aria-hidden="true"></i> Proyectos</h5>
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
              {{ Form::select('status_filter', ['P'=>'Pendiente', 'A'=>'En curso', 'F'=>'Finalizado', 'C'=>'Cancelado'], null, ['id'=>'status_filter', 'class'=>'select2 form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}            
            </div>
            <div class="col-sm-9 col-xs-12 text-right">
                <a href="#" class="btn btn-sm btn-primary" onclick="showModalProject(0);"><i class="fa fa-plus-circle"></i> Nuevo Proyecto</a>
              <button type="button" id="btn_print" class="btn btn-sm btn-default" title="Imprimir PDF"><i class="fa fa-print" aria-hidden="true"></i></button><br><br>
            </div>
            <div class="col-sm-12">
              @include('partials.errors')
            </div>                              
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="projects-table">
                <thead>
                  <tr>
                    <th text-align="center" width="5%"></th>
                    <th>Proyecto</th>
                    <th>Descripción</th>
                    <th>Costo estimado</th>
                    <th>Costo real</th>
                    <th>Avance</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Proyecto</th>
                    <th>Descripción</th>
                    <th>Costo estimado</th>
                    <th>Costo real</th>
                    <th>Avance</th>
                    <th>Estado</th>
                  </tr>
                </tfoot>
              </table>
              <br><br><br><br>
            </div>
          </div>
        </div>
        <!-- /ibox-content- -->

      </div>
    </div>
  </div>
</div>
  
<!-- Modal para Datos -->
<div class="modal inmodal" id="modalProject" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="project"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar-->
<div class="modal inmodal" id="modalDeleteProject" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <strong>Eliminar Proyecto</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
          <input type="hidden" id="hdd_project_id" value=""/>
          <p>Esta seguro que desea eliminar el proyecto <b><span id="project_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
        <button type="button" id="btn_delete_project" class="btn btn-danger">Eliminar</button>
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
  
function showModalProject(id){
  url = '{{URL::to("projects.load")}}/'+id;
  $('#project').load(url);  
  $("#modalProject").modal("show");
}

function showModalDelete(project_id, name){
  $('#hdd_project_id').val(project_id);
  $('#project_name').html(name);
  $("#modalDeleteProject").modal("show");    
};
  
$("#btn_delete_project").on('click', function(event) {    
    project_delete($('#hdd_project_id').val());
});

function project_delete(id){  
  $.ajax({
      url: `{{URL::to("projects")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteProject').modal('toggle');
      $('#projects-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteProject').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  
  
$("#status_filter").change( event => {
  $('#projects-table').DataTable().draw(false);
});

function project_CRUD(id){
  var validator = $("#form_project").validate();
  formulario_validado = validator.form();
  if(formulario_validado){
      $('#btn_submit').attr('disabled', true);
      var form_data = new FormData($("#form_project")[0]);
      $.ajax({
        url:(id==0)?'{{URL::to("projects")}}':'{{URL::to("projects")}}/'+id,
        type:'POST',
        cache:true,
        processData: false,
        contentType: false,      
        data: form_data
      })
      .done(function(response) {
        $('#btn_submit').attr('disabled', false);
        $('#modalProject').modal('toggle');
        $('#projects-table').DataTable().draw(false); 
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
                          
    $("#status_filter").select2({
      language: "es",
      placeholder: "Estado - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
    });
        
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#projects-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[2, "desc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('projects.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.status_filter = $('#status_filter').val();
                d.type_filter = $('#type_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'name',   name: 'name'},
            { data: 'description',   name: 'description'},
            { data: 'budget',   name: 'budget'},
            { data: 'cost',   name: 'cost'},
            { data: 'advance',   name: 'advance', orderable: false, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});  
</script>
@endpush