@extends('layouts.app')

@push('stylesheets')
<!-- Full Calendar -->
<link href="{{ URL::asset('js/plugins/fullcalendar-3.2.0/fullcalendar.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("js/plugins/jquery-qtip2-3.0.3/css/jquery.qtip.min.css") }}">
@endpush

@section('page-header')
@endsection

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row animated fadeInRight">
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $facility->name }}</h5>
                </div>
                <div class="ibox-content no-padding border-left-right">
                    <img alt="image" class="img-responsive" src="{{ url('facility_photo/'.$facility->id) }}" style="width: 320px;height: auto">
                </div>
                <div class="ibox-content profile-content">
                    <div>
                        <b>Disponible</b>
                        De {{ $facility->start->format('g:i a') }} a {{ $facility->end->format('g:i a') }} 
                    </div>
                    @if($facility->rent)
                        <div>
                            <b>Costos</b>
                            Hora {{ money_fmt($facility->hr_cost) }} Día completo {{ money_fmt($facility->day_cost) }} 
                        </div>
                    @endif
                    <div>
                        <b>Normas de Uso</b><br>
                        <small>Estas son algunas normas de uso de la....</small>
                    </div>
                    <br>
                    <div class="user-button">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" onclick="showModalReservation(0)" class="btn btn-block btn-primary btn-sm btn-block"> Reservar</button>
                                <a href="{{url('reservations')}}" class="btn btn-block btn-sm btn-default"> Regresar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Consultar reservaciones <b>{{ $facility->name }}</b></h5>
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
                  <div id="calendar"></div>
                  <div style="padding-top: 2mm">
                    <small>
                    <i class="fa fa-circle text-warning" aria-hidden="true"></i> Pendientes&ensp;
                    <i class="fa fa-circle text-navy" aria-hidden="true"></i>  Aprobadas&ensp;
                    <i class="fa fa-circle text-danger" aria-hidden="true"></i> Rechazadas
                    </small>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
  
<!-- Modal para Datos -->
<div class="modal inmodal" id="modalReservation" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="reservation"></div>
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
    url: '{{ url("/facilities/{$facility->id}/reservations") }}',
    type: 'POST',
    data: function(){
        return {
            _token: "{{ csrf_token() }}", 
            //status_filter: $('#status_filter').val(),
        };
    },
    error: function() {
        alert('there was an error while fetching events!');
    },
  },
  timeFormat: 'h(:mm)t' // uppercase H for 24-hour clock
});

function showModalReservation(id){
  url = '{{URL::to("reservations.load")}}/'+id;
  $('#reservation').load(url);  
  $("#modalReservation").modal("show");
}

function reservation_CRUD(id){
        
    var validator = $("#form_reservation").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_reservation")[0]);
        var facility_id={{ $facility->id }};
        form_data.append('facility_id', facility_id);
        $.ajax({
          url:(id==0)?'{{URL::to("reservations")}}':'{{URL::to("reservations")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modalReservation').modal('toggle');
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