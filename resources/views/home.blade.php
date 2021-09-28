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
            <div class="widget style1 style1 lazur-bg">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa fa-users fa-4x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span> Tot Clientes </span>
                        <h3 class="font-bold">{{ $tot_customers }}</h3>
                    </div>
                </div>
            </div>
        </div>        
        <div class="col-lg-3">
            <div class="widget style1 yellow-bg">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa fa-truck fa-4x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span> Tot Operaciones </span>
                        <h3 class="font-bold">{{ $tot_operations }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="widget style1 navy-bg">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa fa-money fa-4x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span> Tot Facturado Mes</span>
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt($tot_incomes_month) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="widget style1 red-bg">
                <div class="row">
                    <div class="col-xs-4">
                        <i class="fa fa-money fa-4x"></i>
                    </div>
                    <div class="col-xs-8 text-right">
                        <span> Tot Egresos Mes</span>
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt($tot_expenses_month) }}</h3>
                    </div>
                </div>
            </div>
        </div>
	</div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div>
                        <span class="pull-right text-right">Total Facturado Mes: <b>{{ money_fmt($tot_incomes_month) }}</b></span>
                        <h3 class="font-bold no-margins"> Facturación {{ month_letter($today->month, 'lg') }}</h3>
                    </div>
                    <div class="m-t-sm">
                        <div class="row">
                            <div class="col-md-12">
                                <div><canvas id="lineChartIncomesMonth" height="40"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-md">
                        <small class="pull-right">
                            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
                        </small>
                        <small>
                            <strong>Facturación diaria {{ Session::get('coin') }}</strong>.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div>
                        <span class="pull-right text-right">Total Facturado Año: <b>{{ money_fmt($tot_incomes_year) }}</b></span>
                        <h3 class="font-bold no-margins"> Facturación {{ $today->year }}</h3>
                    </div>
                    <div class="m-t-sm">
                        <div class="row">
                            <div class="col-md-12">
                                <div><canvas id="lineChartIncomesYear" height="120"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-md">
                        <small class="pull-right">
                            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
                        </small>
                        <small>
                            <strong>Facturación mensual {{ Session::get('coin') }}</strong>.
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div>
                        <span class="pull-right text-right">Totales Año:
                            <i class="fa fa-circle text-warning" aria-hidden="true"></i> {{ money_fmt($tot_margin_total_year) }} <i class="fa fa-circle text-navy" aria-hidden="true"></i>  {{ money_fmt($tot_margin_sc_year) }} 
                            <i class="fa fa-circle text-danger" aria-hidden="true"></i> {{ money_fmt($tot_margin_hm_year) }}
                        </span>
                        <h3 class="font-bold no-margins"> Margenes {{ $today->year }}</h3>
                    </div>
                    <div class="m-t-sm">
                        <div class="row">
                            <div class="col-md-12">
                                <div><canvas id="lineChartMarginsYear" height="120"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-md">
                        <small>
                            <i class="fa fa-circle text-warning" aria-hidden="true"></i> Margen Total <i class="fa fa-circle text-navy" aria-hidden="true"></i>  Margen SC 
                            <i class="fa fa-circle text-danger" aria-hidden="true"></i> Margen HM
                        </small>
                        <small class="pull-right">
                            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div>
                        <span class="pull-right text-right">Totales Mes:
                            <i class="fa fa-circle text-warning" aria-hidden="true"></i> {{ money_fmt($tot_margin_total_month) }} <i class="fa fa-circle text-navy" aria-hidden="true"></i>  {{ money_fmt($tot_margin_sc_month) }} 
                            <i class="fa fa-circle text-danger" aria-hidden="true"></i> {{ money_fmt($tot_margin_hm_month) }}
                        </span>
                        <h3 class="font-bold no-margins"> Margenes {{ month_letter($today->month, 'lg') }}</h3>
                    </div>
                    <div class="m-t-sm">
                        <div class="row">
                            <div class="col-md-12">
                                <div><canvas id="lineChartMarginsMonth" height="40"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-md">
                        <small>
                            <i class="fa fa-circle text-warning" aria-hidden="true"></i> Margen Total <i class="fa fa-circle text-navy" aria-hidden="true"></i>  Margen SC <i class="fa fa-circle text-danger" aria-hidden="true"></i> Margen HM
                        </small>
                        <small class="pull-right">
                            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
                        </small>
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
<!-- JQuery number-format -->
<script src="{{ URL::asset('js/plugins/jquery-number-format/jquery.number.min.js') }}"></script>
<script>

function money_fmt(num){        
  if('{{ session('money_format') }}' == 'PC'){
      num_fmt = $.number(num, 0, ',', '.');        
  }else if('{{ session('money_format') }}' == 'PC2'){
      num_fmt = $.number(num, 2, ',', '.');          
  }else if('{{ session('money_format') }}' == 'CP2'){
      num_fmt = $.number(num, 2, '.', ',');
  }
  return num_fmt;        
}

$(document).ready(function() {
    
    //Grafica MES
    var lineDataIncomesMonth = {
        labels: {!! $labels_days !!},
        datasets: [
            {
                label: "Example dataset",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.7)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: {!! $array_incomes_month !!}
            }
        ]
    };

    var lineOptionsIncomesMonth = {
        scaleLabel:"<%= money_fmt(value) %>",
        tooltipTemplate: "<%= money_fmt(value) %>",
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

    var ctx_incomes_month = document.getElementById("lineChartIncomesMonth").getContext("2d");
    var myNewChartIncomesMonth = new Chart(ctx_incomes_month).Line(lineDataIncomesMonth, lineOptionsIncomesMonth);


    //Grafica AÑO
    var lineDataIncomesYear = {
        labels: {!! $labels_months !!},
        datasets: [
            {
                label: "Example dataset",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.7)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: {!! $array_incomes_year !!}
            }
        ]
    };

    var lineOptionsIncomesYear = {
        scaleLabel:"<%= money_fmt(value) %>",
        tooltipTemplate: "<%= money_fmt(value) %>",        
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

    var ctx_incomes_year = document.getElementById("lineChartIncomesYear").getContext("2d");
    var myNewIncomesYear = new Chart(ctx_incomes_year).Line(lineDataIncomesYear, lineOptionsIncomesYear);


    //Grafica Margenes MES Total SC HM
    var lineDataMarginsMonth = {
        labels: {!! $labels_days !!},
        datasets: [
            {
                label: "Margen Total",
                fillColor: "rgba(248,172, 89,0.5)",
                strokeColor: "rgba(248,172,89,1)",
                pointColor: "rgba(248,172,89,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(248,172,89,1)",
                data: {!! $array_margin_total_month !!}
            },
            {
                label: "Margen SC",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,1)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: {!! $array_margin_sc_month !!}
            },
            {
                label: "Margen HM",
                fillColor: "rgba(237,85,101,0.5)",
                strokeColor: "rgba(237,85,101,1)",
                pointColor: "rgba(237,85,101,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(237,85,101,1)",
                data: {!! $array_margin_hm_month !!}
            }
        ]
    };

    var lineOptionsMarginsMonth = {
        scaleLabel:"<%= money_fmt(value) %>",
        multiTooltipTemplate: "<%= money_fmt(value) %>",
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

    var ctx_margins_month = document.getElementById("lineChartMarginsMonth").getContext("2d");
    var myNewChartMarginsMonth = new Chart(ctx_margins_month).Line(lineDataMarginsMonth, lineOptionsMarginsMonth);



    //Grafica Margenes AÑO Total SC HM
    var lineDataMarginsYear = {
        labels: {!! $labels_months !!},
        datasets: [
            {
                label: "Margen Total",
                fillColor: "rgba(248,172, 89,0.5)",
                strokeColor: "rgba(248,172,89,1)",
                pointColor: "rgba(248,172,89,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(248,172,89,1)",
                data: {!! $array_margin_total_year !!},
            },
            {
                label: "Margen SC",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,1)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: {!! $array_margin_sc_year !!}
            },
            {
                label: "Margen HM",
                fillColor: "rgba(237,85,101,0.5)",
                strokeColor: "rgba(237,85,101,1)",
                pointColor: "rgba(237,85,101,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(237,85,101,1)",
                data: {!! $array_margin_hm_year !!}
            }
        ]
    };

    var lineOptionsMarginsYear = {
        scaleLabel:"<%= money_fmt(value) %>",
        multiTooltipTemplate: "<%= money_fmt(value) %>",
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

    var ctx_margins_year = document.getElementById("lineChartMarginsYear").getContext("2d");
    var myNewChartMarginsYear = new Chart(ctx_margins_year).Line(lineDataMarginsYear, lineOptionsMarginsYear);

});
</script>
@endpush