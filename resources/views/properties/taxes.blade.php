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
          <h5><i class="fa fa-percent" aria-hidden="true"></i> Alícuotas</h5>
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
            <div class="col-sm-8 col-sm-offset-2">
                <small>La suma de los porcentajes de alicuotas deben totalizar <b>100%</b> para poder guardar los cambios.</small>
              <div class="progress">
                <div id="dynamic" class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <span id="current-progress"></span>
                </div>
              </div>
              <button class="btn btn-sm btn-block btn-primary" id="btn_save_taxes" disabled>Guardar</button>
            </div>
            <div class="table-responsive col-sm-8 col-sm-offset-2">
              <table class="table table-striped table-hover table-condensed" id="properties-table">
                <thead>
                  <tr>
                    <th width="20%">Propiedad</th>
                    <th width="50%">Propietario</th>
                    <th width="30%">Porcentaje (%)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($properties as $property)
                  <tr>
                    <td><b>{{ $property->number }}</b></td>
                    <td>
                      {{ ($property->user_id)?$property->user->name:'' }}
                    </td>
                    <td>
                      <input type="text" name="tax[]" data-id="{{ $property->id }}" class="form-control decimal" value="{{ $property->tax }}" style="width: 100px;height: 25px"/>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- /ibox-content- -->

      </div>
    </div>
  </div>
</div>  
@endsection

@push('scripts')
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/jquery-circle-progress/dist/circle-progress.js') }}"></script>
<script>


$("input[name='tax[]']").keyup(function () {
  var interval = setInterval(function() {
    calculate_total_tax();
  }, 1000);  
});

function calculate_total_tax(){
  total_tax=0;
  $("input[name='tax[]']").each(function(index, element) {
      tax=($(element).val()!='')?$(element).val():0;
      total_tax+=parseFloat(tax);
  });  
  //console.log('El total es '+total_tax);
  progressbar(total_tax);
  (total_tax==100)?$('#btn_save_taxes').attr('disabled',false):$('#btn_save_taxes').attr('disabled', true);
}

function progressbar(progress) {
  if(progress<=100){
    $("#dynamic")
    .css("width", progress + "%")
    .attr("aria-valuenow", progress)
    .text(progress + "% Completado");    
  }else{

  }
}

$("#btn_save_taxes").on('click', function(event) {
  $(this).attr('disabled',true);
  var array_properties=[];
  var array_taxes=[];
  $("input[name='tax[]']").each(function(index, element) {
      tax=($(element).val()!='')?$(element).val():0;
      array_properties.push($(this).data("id"));
      array_taxes.push(tax);
  });
  $.ajax({
    url: `{{URL::to("properties.update_taxes")}}`,
    type: 'POST',
    data: {
      _token: "{{ csrf_token() }}", 
      array_properties:array_properties,
      array_taxes:array_taxes
    },
  })
  .done(function(response) {
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var table=$('#properties-table').DataTable({
      "oLanguage":{"sUrl":path_str_language},
      "ordering": false,
      "searching": false,
      "lengthChange": false,
      "scrollY": '350px',
      "scrollCollapse": true,
      "paging":false,
      "info" : false,
      "autoWidth": false // Disable the auto width calculation
    });
});

</script>
@endpush