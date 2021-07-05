@extends('layouts.app')

@push('stylesheets')
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
                        <span> Sooventes </span>
                        <h2 class="font-bold">99</h2>
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
                        <span> Pendientes </span>
                        <h2 class="font-bold">99</h2>
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
                        <span> Morosos </span>
                        <h2 class="font-bold">99</h2>
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
                        <span> Cta x Cobrar </span>
                        <h2 class="font-bold">999 {{ session('coin') }}</h2>
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
                        <span class="pull-right text-right">Total ingresos : 999</span>
                        <h3 class="font-bold no-margins"><i class="fa fa-money" aria-hidden="true"></i> Ingresos en el {{ $today->year }}</h3>
                    </div>
                    <div class="m-t-sm">
                        <div class="row">
                            <div class="col-md-12">
                                <div><canvas id="lineChartSales" height="114"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-md">
                        <small class="pull-right">
                            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
                        </small>
                        <small>
                            <strong>Ingresos semestrales</strong>.
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div>
                        <span class="pull-right text-right">Total egresos: 9999</span>
                        <h3 class="font-bold no-margins"><i class="fa fa-money" aria-hidden="true"></i> Egresos en el {{ $today->year }}</h3>
                    </div>
                    <div class="m-t-sm">
                        <div class="row">
                            <div class="col-md-12">
                                <div><canvas id="lineChartPurchases" height="114"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-md">
                        <small class="pull-right">
                            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
                        </small>
                        <small>
                            <strong>Egresos semestrales</strong>.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')    
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Datatables -->
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<!-- Flot -->
<script src="{{ asset("js/plugins/flot/jquery.flot.js") }}"></script>
<script src="{{ asset("js/plugins/flot/jquery.flot.tooltip.min.js") }}"></script>
<script src="{{ asset("js/plugins/flot/jquery.flot.spline.js") }}"></script>
<script src="{{ asset("js/plugins/flot/jquery.flot.resize.js") }}"></script>
<script src="{{ asset("js/plugins/flot/jquery.flot.pie.js") }}"></script>    
<!-- ChartJS-->
<script src="{{ asset("js/plugins/chartJs/Chart.min.js") }}"></script>
<!-- Peity -->
<script src="{{ asset("js/plugins/peity/jquery.peity.min.js") }}"></script>
<script src="{{ asset("js/demo/peity-demo.js") }}"></script>

    <script>
        $(document).ready(function() {

            var lineDataSales = {
                labels: {!! $labels !!},
                datasets: [
                    {
                        label: "Example dataset",
                        fillColor: "rgba(26,179,148,0.5)",
                        strokeColor: "rgba(26,179,148,0.7)",
                        pointColor: "rgba(26,179,148,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(26,179,148,1)",
                        data: {!! $array_total_credit_points !!}
                    }
                ]
            };

            var lineOptionsSales = {
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
                datasetFill: true,
                responsive: true,
            };

            var ctx_sales = document.getElementById("lineChartSales").getContext("2d");
            var myNewChartSales = new Chart(ctx_sales).Line(lineDataSales, lineOptionsSales);


            var lineDataPurchases = {
                labels: {!! $labels !!},
                datasets: [
                    {
                        label: "Example dataset",
                        fillColor: "rgba(220,220,220,0.5)",
                        strokeColor: "rgba(220,220,220,1)",
                        pointColor: "rgba(220,220,220,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data: {!! $array_total_debit_points !!}
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
                datasetFill: true,
                responsive: true,
            };

            var ctx_purchases = document.getElementById("lineChartPurchases").getContext("2d");
            var myNewChartPurchases = new Chart(ctx_purchases).Line(lineDataPurchases, lineOptionsPurchases);


        });
    </script>

@endpush