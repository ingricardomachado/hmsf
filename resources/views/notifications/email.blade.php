@extends('layouts.app')

@push('stylesheets')
<!-- CSS Datatables -->
<link href="{{ URL::asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- bootstrap wysihtml5 - text editor -->
<link href="{{ URL::asset('js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet">
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
          <h5><i class="fa fa-envelope-o" aria-hidden="true"></i> Correo <small>Seleccione las propiedades y complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
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
            <form action="#" id="form_email" method="POST">
              <div class="table-responsive col-sm-12">
                <table class="table" id="datatable-properties" width="100%" style="font-size: 12px">
                  <thead>
                    <tr>
                      <th title="Seleccionar todas">
                        {!! Form::checkbox('check-all', null, false, ['id'=>'check-all', 'class'=>'i-checks']) !!}
                      </th>
                      <th>Propiedad</th>
                      <th>Propietario</th>
                      <th>Correo</th>
                      <th>Deuda</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($properties as $property)
                    <tr>
                      <td>
                        {!! Form::checkbox('properties', $property->id, null, ['class'=>'i-checks', 'id'=>'properties[]', 'required']) !!}
                      </td>
                      <td><b>{{ $property->number }}</b></td>
                      <td>{{ $property->user }}</td>
                      <td>{{ $property->email }}</td>
                      <td>{{ money_fmt($property->total_debt) }}</td>
                      <td>{!! $property->status_label !!}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <div class="form-group col-sm-12">
                {!! Form::text('subject', null, ['id'=>'subject', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Asunto del correo...', 'maxlength'=>'100', 'required']) !!}
              </div>
              <div class="form-group col-sm-12">
                {!! Form::textarea('body', null, ['id'=>'body', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Escribe aquí cuerpo del correo ...', 'maxlength'=>'1000']) !!}
              </div>
              <div class="form-group col-sm-12 text-right">
                <button type="button" id="btn_send" class="btn btn-sm btn-primary"><i class="fa fa-paper-plane-o" aria-hidden="true" disabled></i> Enviar</button>
              </div>
            </form>
          </div>
        </div>
        <!-- /ibox-content- -->

      </div>
    </div>
  </div>
</div>
  
@endsection

@push('scripts')
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset('js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<script src="{{ asset('js/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.es-ES.js') }}"></script>
<script>
  
$('#check-all').on('ifChanged', function(event){
  (event.target.checked)?$('input:checkbox').iCheck('check'):$('input:checkbox').iCheck('uncheck');
});

$("input[id^='properties']").on('ifChanged', function(event){       
  $('#btn_send').attr('disabled', $('input:checkbox:checked').length==0);
});       

$("#btn_send").on('click', function(event) {    
  var validator = $( "#form_email" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    $('#btn_submit').attr('disabled', true);
    var array_properties=[];
    $("input[name='properties']:checked").each(function (){
      array_properties.push($(this).val());
    });
    $.ajax({
        url: '{{URL::to("notifications.send_email")}}',
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          subject: $('#subject').val(),
          body: $('#body').val(),
          array_properties: array_properties
        },
    })
    .done(function(response) {
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
    })
    .fail(function(response) {
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
    });
  }
});

$(document).ready(function(){

  // iCheck
  $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
  });
  
  $('#body').wysihtml5({
    locale: 'es-ES',
    toolbar: {
      "font-styles": true, // Font styling, e.g. h1, h2, etc.
      "emphasis": true, // Italics, bold, etc.
      "lists": true, // (Un)ordered lists, e.g. Bullets, Numbers.
      "html": false, // Button which allows you to edit the generated HTML.
      "link": false, // Button to insert a link.
      "image": false, // Button to insert an image.
      "color": false, // Button to change color of font
      "blockquote": true, // Blockquote
    }    
  });

  $('#datatable-properties').DataTable( {
    "ordering": false,
    "searching": false,
    "lengthChange": false,
    "scrollX": true,
    "scrollY": '200px',
    "scrollCollapse": true,
    "paging":false,
    "info" : false,
  } );

});
</script>
@endpush