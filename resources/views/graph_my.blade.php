<div class="ibox-content">
    <div>
        <span class="pull-right text-right">{{ (session('role')=='ADM')?'Totales':'Total' }} Año:
            <small>
            @if(session('role')=='ADM')
                <i class="fa fa-circle text-warning" aria-hidden="true"></i> {{ money_fmt($tot_margin_total_year) }}
            @endif 
            <i class="fa fa-circle text-navy" aria-hidden="true"></i>  {{ money_fmt($tot_margin_sc_year) }} 
            @if(session('role')=='ADM')
                <i class="fa fa-circle text-danger" aria-hidden="true"></i> {{ money_fmt($tot_margin_hm_year) }}
            @endif
            </small>
        </span>
        <h3 class="font-bold no-margins"> Margenes {{ $year }}</h3>
    </div>
    <div class="m-t-sm">
        <div class="row">
            <div class="col-md-4 col-xs-6 pull-right">
            <label>Año</label> 
            {{ Form::select('year_filter_my', $years, $year, ['id'=>'year_filter_my', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
            </div>
        </div>
    </div>
    <div class="m-t-sm">
        <div class="row">
            <div class="col-md-12">
                <div><canvas id="lineChartMarginsYear" height="120"></canvas></div>
            </div>
        </div>
    </div>
    <div class="m-t-md">
        @if(session('role')=='ADM')
            <small>
                <i class="fa fa-circle text-warning" aria-hidden="true"></i> Margen Total <i class="fa fa-circle text-navy" aria-hidden="true"></i>  Margen SC 
                <i class="fa fa-circle text-danger" aria-hidden="true"></i> Margen HM
            </small>
        @endif
        <small class="pull-right">
            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
        </small>
    </div>
</div>
<script>

$("#year_filter_my").change( event => {
    var year=$('#year_filter_my').val();
    load_graph_my(year);
});

$(document).ready(function() {

    $("#year_filter_my").select2({
      language: "es",
      placeholder: "Año",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
    });
    
    if('{{ session('role') }}'=='ADM'){
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
    
    }else{
        //Grafica Margenes AÑO Total SC HM
        var lineDataMarginsYear = {
            labels: {!! $labels_months !!},
            datasets: [
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
            ]
        };

        var lineOptionsMarginsYear = {
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
    }

    var ctx_margins_year = document.getElementById("lineChartMarginsYear").getContext("2d");
    var myNewChartMarginsYear = new Chart(ctx_margins_year).Line(lineDataMarginsYear, lineOptionsMarginsYear);

});    
</script>
