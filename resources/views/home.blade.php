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
                        <span> Solventes <b>000</b></span>
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
                        <span> Pendientes <b>000</b></span>
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt(0) }}</h3>
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
                        <span> Morosos <b>000</b></span>
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt(0) }}</h3>
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
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt(0) }}</h3>
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
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>TOTAL..:</th>
                                    <th class="text-right">
                                        {{ session('coin') }}{{ money_fmt(0) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><a href="#" style="color:inherit"  title="Click para ir a calendario">Eventos próximos</a></h5>
                    <div class="ibox-tools">
                        <span class="label label-warning-light pull-right"> Evento</span>
                       </div>
                </div>
                <div class="ibox-content">
                    <div>
                            <div class="alert alert-info" style="font-size:12px">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                No hay eventos para mostrar ...
                            </div>
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

});
</script>
@endpush