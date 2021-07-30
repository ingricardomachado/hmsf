@extends('layouts.app')

@push('stylesheets')
<!-- Magnific Popup -->
<link rel="stylesheet" href="{{ URL::asset('js/plugins/magnific-popup/magnific-popup.css') }}">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Datatables -->
<link href="{{ URL::asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@section('page-header')
@endsection

@section('content')
<div class="wrapper wrapper-content">
	<div class="row">
        <div class="col-lg-3">
            <div class="widget style1 navy-bg">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa fa-users fa-4x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span> Solventes <b>{{ $solventes }}</b></span>
                        <h3 class="font-bold"></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="widget style1 yellow-bg">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa fa-users fa-4x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span> Pendientes <b>{{ $pendientes }}</b></span>
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt($debt) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="widget style1 red-bg">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa fa-users fa-4x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span> Morosos <b>{{ $morosos }}</b></span>
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt($due_debt) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="widget style1 style1 lazur-bg">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa fa-money fa-4x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span> Total Cta x Cobrar </span>
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt($tot_debt) }}</h3>
                    </div>
                </div>
            </div>
        </div>
	</div>

    <div class="row">
        <div class="col-lg-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Ingresos vs Egresos <b>{{ $today->year }}</b></h5> <span class="label label-primary">IN+</span>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                
                <div class="ibox-content">
                    <div class="m-t-sm">
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <canvas id="lineChartMovements" height="80"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-md">
                        <small>
                            <i class="fa fa-circle text-navy" aria-hidden="true"></i> Ingresos 
                            <i class="fa fa-circle text-muted" aria-hidden="true"></i>  Egresos 
                        </small>
                        <small class="pull-right">
                            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><a href="{{ url('payments') }}" style="color:inherit"  title="Click para ir a pagos">Pagos por confirmar</a></h5>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                    <div class="table-responsive col-sm-12">
                      <table class="table table-striped table-hover" id="payments-table" width="100%" style="font-size:12px">
                        <thead>
                          <tr>
                            <th width="10%">Fecha</th>
                            <th width="35%">Pago</th>
                            <th width="20%">Propiedad</th>
                            <th width="10%">Monto</th>
                            <th width="10%">Soporte</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                    </div>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><a href="{{ url('reservations') }}" style="color:inherit"  title="Click para ir a reservaciones">Reservaciones por confirmar</a></h5>
                    <div class="ibox-tools">
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="table-responsive col-sm-12">
                          <table class="table table-striped table-hover" id="reservations-table">
                            <thead>
                              <tr>
                                <th>Instalación</th>
                                <th>Propiedad</th>
                                <th width="20%">Notas</th>
                                <th width="20%">Observaciones</th>
                                <th>Costo</th>
                              </tr>
                            </thead>
                          </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Caja y Banco</h5>
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
                    <div>
                        <table width="100%">
                            <tbody>
                                @foreach($accounts as $account)
                                    <tr>
                                        <td>
                                            <a href="{{ url('accounts.statement', Crypt::encrypt($account->id)) }}" style="color:inherit"  title="Click para estado de cuenta">{{ $account->aliase }}</a>
                                        </td>
                                        <td class="text-right">
                                            {{ session('coin') }}{{ money_fmt($account->balance) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>TOTAL..:</th>
                                    <th class="text-right">
                                        {{ session('coin') }}{{ money_fmt($accounts->sum('balance')) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><a href="{{ url('events') }}" style="color:inherit"  title="Click para ir a calendario">Eventos próximos</a></h5>
                    <div class="ibox-tools">
                        <span class="label label-warning-light pull-right">{{ $events->count() }} Evento</span>
                       </div>
                </div>
                <div class="ibox-content">
                    <div>
                        @if($events->count()>0)
                        <div class="feed-activity-list">
                            @foreach($events as $event)
                                <div class="feed-element">
                                    <div class="media-body ">
                                        <b>{{ $event->title }}</b><br>
                                        @if($event->description)
                                            {{ $event->description }}<br>
                                        @endif
                                        <small class="text-muted">
                                           {{ Carbon\Carbon::parse($event->start)->isoFormat('LLLL') }} {!! ($event->private)?'<i class="fa fa-lock" aria-hidden="true"></i>':'' !!} 
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @else
                            <div class="alert alert-info" style="font-size:12px">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                No hay eventos para mostrar ...
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Ultimos mensajes</h5>
                    <div class="ibox-tools">
                        <button type="button" class="btn btn-xs btn-primary pull-right" onclick="showModalPost()">Postear</button>
                       </div>
                </div>
                <div class="ibox-content">
                    <div>
                            <span id="posts"></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal para Post -->
<div class="modal inmodal" id="modalPost" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
        <form action="#" id="form_post" method="POST">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><i class="fa fa-comments-o" aria-hidden="true"></i> Postear Mensaje</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
            </div>
            <div class="modal-body">
                <div class="row">            
                    <div class="form-group col-sm-12">
                        <label>Mensaje *</label><small> Máx. 140 caracteres</small>
                        {!! Form::textarea('comment', null,['id'=>'comment', 'class'=>'form-control', 'type'=>'text', 'rows'=>'1', 'style'=>'font-size:12px', 'placeholder'=>'', 'maxlength'=>'140']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_post" class="btn btn-sm btn-primary">Guardar</button>
                <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </form>

    </div>
  </div>
</div>
<!-- /Modal para Comentarios -->

<!-- Modal para Reply -->
<div class="modal inmodal" id="modalReply" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
        <form action="#" id="form_post" method="POST">
            <input type="hidden" name="hdd_post_id" id="hdd_post_id" class="form-control" value="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><i class="fa fa-comments-o" aria-hidden="true"></i> Responder Mensaje</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
            </div>
            <div class="modal-body">
                <div class="row">            
                    <div class="form-group col-sm-12">
                        <label>Respuesta *</label><small> Máx. 140 caracteres</small>
                        {!! Form::textarea('comment_reply', null,['id'=>'comment_reply', 'class'=>'form-control', 'type'=>'text', 'rows'=>'1', 'style'=>'font-size:12px', 'placeholder'=>'', 'maxlength'=>'140']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_reply" class="btn btn-sm btn-primary">Responder</button>
                <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </form>

    </div>
  </div>
</div>
<!-- /Modal para Comentarios -->

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

@endsection
@push('scripts')    
<!-- Magnific Popup -->
<script src="{{ URL::asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<!-- ChartJS-->
<script src="{{ asset("js/plugins/chartJs/Chart.min.js") }}"></script>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- Timeago -->
<script src="{{ URL::asset('js/plugins/jquery-timeago/jquery.timeago.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-timeago/locales/jquery.timeago.es.js') }}"></script>
<!-- Scroll -->
<script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script>

var condominium_id={{ session('condominium')->id }};
var user_id={{ Auth::user()->id }};

function showModalPost(){
  $('#comment').val('');
  $("#modalPost").modal("show");
}    

function showModalReply(id){
  $('#hdd_post_id').val(id);
  $('#comment_reply').val('');
  $("#modalReply").modal("show");
}    

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

function posts(id){
  url = '{{URL::to("posts.index")}}/'+id;
  $('#posts').load(url);  
}

$("#btn_post").on('click', function(event) {    
  $.ajax({
      url: `{{URL::to("posts")}}`,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}",
        condominium_id: condominium_id,
        user_id: user_id,
        comment: $('#comment').val(), 
      },
  })
  .done(function(response) {
      $('#modalPost').modal('toggle');
      posts(condominium_id);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalPost').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
});

$("#btn_reply").on('click', function(event) {    
  var post_id=$('#hdd_post_id').val();
  $.ajax({
      url: `{{URL::to("replies")}}`,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}",
        post_id: post_id,
        user_id: user_id,
        comment: $('#comment_reply').val(), 
      },
  })
  .done(function(response) {
      $('#modalReply').modal('toggle');
      posts(condominium_id);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalReply').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
});

function post_delete(id){  
  $.ajax({
      url: `{{URL::to("posts")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      posts(condominium_id);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modalDeleteCar').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

$(document).ready(function() {
    
    var lineDataMovements = {
        labels: {!! $labels !!},
        datasets: [
            {
                label: "Ingresos",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.7)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: {!! $array_incomes !!}
            },
            {
                label: "Egresos",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: {!! $array_expenses !!}
            }
        ]
    };

    var lineOptionsPurchases = {
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        bezierCurve: true,
        bezierCurveTension: 0.4,
        pointDot: true,
        pointDotRadius: 4,
        pointDotStrokeWidth: 1,
        pointHitDetectionRadius: 20,
        datasetStroke: true,
        datasetStrokeWidth: 2,
        datasetFill: false,
        responsive: true,
    };

    var ctx_movements = document.getElementById("lineChartMovements").getContext("2d");
    var myNewChartPurchases = new Chart(ctx_movements).Line(lineDataMovements, lineOptionsPurchases);


    path_str_language = "{{URL::asset('js/plugins/dataTables/es_ES.txt')}}";          
    var payments_table=$('#payments-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 3,
        bLengthChange: false,
        ajax: {
            url: '{!! route('payments.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.property_filter = '';
                d.status_filter = 'P';
            }
        },        
        columns: [
            { data: 'date',   name: 'date', orderable: false, searchable: false},
            { data: 'payment',   name: 'payment', orderable: false, searchable: true},
            { data: 'property',   name: 'property', orderable: false, searchable: false},
            { data: 'amount', name: 'amount', orderable: false, searchable: false },
            { data: 'file', name: 'file', orderable: false, searchable: false },
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

    var reservations_table=$('#reservations-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 3,
        bLengthChange: false,        
        ajax: {
            url: '{!! route('reservations.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.property_filter = '';
                d.status_filter = 'P';
            }
        },        
        columns: [
            { data: 'facility',   name: 'facility', orderable: false, searchable: false},
            { data: 'property',   name: 'property', orderable: false, searchable: true},
            { data: 'notes', name: 'notes', orderable: false, searchable: false },
            { data: 'observations', name: 'observations', orderable: false, searchable: false },
            { data: 'cost', name: 'cost', orderable: false, searchable: false },
        ]
    });

    $('#comment').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  

    posts({{ session('condominium')->id }});
});
</script>
@endpush