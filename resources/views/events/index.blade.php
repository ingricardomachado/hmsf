@extends('layouts.app')

@push('stylesheets')
<!-- Full Calendar -->
<link href="{{ URL::asset('js/plugins/fullcalendar-3.2.0/fullcalendar.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("js/plugins/jquery-qtip2-3.0.3/css/jquery.qtip.min.css") }}">
@endpush

@section('page-header')
@endsection

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row animated fadeInRight">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><i class="fa fa-calendar-o" aria-hidden="true"></i> Eventos</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#">Config option 1</a>
                            </li>
                            <li><a href="#">Config option 2</a>
                            </li>
                        </ul>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <i class="fa fa-info-circle" aria-hidden="true"></i> Puede arrastrar y soltar un evento para moverlo de día.                            
                        </div>
                        <div class="col-sm-6 col-xs-12 text-right">
                            <a href="#" class="btn btn-sm btn-primary" onclick="showModalEvent(0);"><i class="fa fa-plus-circle"></i> Nuevo Evento</a>
                        </div>
                        <div class="col-sm-12 col-xs-12" style="margin-top:2mm">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
  
<!-- Modal para Datos -->
<div class="modal inmodal" id="modalEvent" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="event"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

@endsection

@push('scripts')
<!-- Full Calendar -->
<script src="{{ asset('js/plugins/fullcalendar-3.2.0/moment.min.js') }}"></script>
<script src="{{ asset('js/plugins/fullcalendar-3.2.0/fullcalendar.min.js') }}"></script>
<script src="{{ asset("js/plugins/fullcalendar-3.2.0/locale/es.js") }}"></script>
<script src="{{ asset("js/plugins/jquery-qtip2-3.0.3/js/jquery.qtip.min.js") }}"></script>
<script>

$('#calendar').fullCalendar({
    locale: 'es',
    displayEventEnd:true, 
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listMonth'
    },
    buttonText: {
        today: 'Hoy',
        month: 'Mes',
        week: 'Semana',
        day: 'Día',
        list:'Lista'
    },      
    events: {
        url: '{{ url("/condominiums/{$condominium->id}/events") }}',
        type: 'POST',
        data: function(){
            return {
                _token: "{{ csrf_token() }}", 
            };
        },
        error: function() {
            alert('there was an error while fetching events!');
        },
    },
    timeFormat: 'h(:mm)t',
    //Modificar eventos al arrastrarlos a otro dia (Drop)
    eventDrop: function(event, delta){
        var start = event.start.format("DD/MM/YYYY HH:mm");
        var end = event.end.format("DD/MM/YYYY HH:mm");
        $.ajax({
            url: 'events.drop/'+event.id,
            type: "POST",
            data: {
              _token: "{{ csrf_token() }}", 
              start: start,
              end: end
            },
            success: function(json){
                console.log('evento actualizado');
            },
            error: function(json){
                console.log('error al actualizar el evento');
            }
        });
    },
    // Actualizar/Eliminar evento
    eventClick: function(event, jsEvent, view){
        showModalEvent(event._id);
    },
    eventRender: function(event, element) {
        (event.private)?element.find('.fc-title').prepend("<i class='fa fa-lock' aria-hidden='true'></i> "):'';
    }
});

function showModalEvent(id){
  url = '{{URL::to("events.load")}}/'+id;
  $('#event').load(url);  
  $("#modalEvent").modal("show");
}


function event_delete(id){  
  $.ajax({
      url: `{{URL::to("events")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalEvent').modal('toggle');
      $('#calendar').fullCalendar('refetchEvents');
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
      $('#modalEvent').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function event_CRUD(id){
        
    var validator = $("#form_event").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_event")[0]);
        form_data.append('color', currColor);        
        $.ajax({
          url:(id==0)?'{{URL::to("events")}}':'{{URL::to("events")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalEvent').modal('toggle');
          $('#calendar').fullCalendar('refetchEvents');
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
</script>
@endpush