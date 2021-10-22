<div class="ibox-content">
    <div>
        <span class="pull-right text-right">
            <label>Mes</label> 
            {{ Form::select('month_filter_mm', ['01'=>'01','02'=>'02', '03'=>'03', '04'=>'04', '05'=>'05', '06'=>'06', '07'=>'07', '08'=>'08', '09'=>'09', '10'=>'10', '11'=>'11', '12'=>'12'], $month, ['id'=>'month_filter_mm', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
        </span>
        <span class="pull-right text-right">Totales Mes:
            <small>
            <i class="fa fa-circle text-warning" aria-hidden="true"></i> {{ money_fmt($tot_margin_total_month) }} <i class="fa fa-circle text-navy" aria-hidden="true"></i>  {{ money_fmt($tot_margin_sc_month) }} 
            <i class="fa fa-circle text-danger" aria-hidden="true"></i> {{ money_fmt($tot_margin_hm_month) }}
            </small>
        </span>
        <h3 class="font-bold no-margins"> Margenes {{ month_letter($month, 'lg') }}</h3>
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
<script>
    
$("#month_filter_mm").change( event => {
    var month=$('#month_filter_mm').val();
    load_graph_mm(month);
});

$(document).ready(function() {

    $("#month_filter_mm").select2({
      language: "es",
      placeholder: "Año",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
    });
    
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

});
</script>