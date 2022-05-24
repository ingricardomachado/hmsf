<div class="ibox-content">
    <div>
        <span class="pull-right text-right">
            <label>Mes</label> 
            {{ Form::select('month_filter_mm', ['1'=>'01','2'=>'02', '3'=>'03', '4'=>'04', '5'=>'05', '6'=>'06', '7'=>'07', '8'=>'08', '9'=>'09', '10'=>'10', '11'=>'11', '12'=>'12'], $month, ['id'=>'month_filter_mm', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
        </span>
        <span class="pull-right text-right" style="margin-right:2mm">
            <label>Año</label> 
            {{ Form::select('year_filter_mm', $years, $year, ['id'=>'year_filter_mm', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
        </span>
        <span class="pull-right text-right">
            Total Margenes Mes: <b>{{ money_fmt($tot_margin_sc_month) }}</b>
        </span>
        <h3 class="font-bold no-margins"> Margenes {{ month_letter($month, 'lg') }} {{ $year }}</h3>
    </div>
    <div class="m-t-sm">
        <div class="row">
            <div class="col-md-12">
                <div><canvas id="lineChartMarginsMonth" height="40"></canvas></div>
            </div>
        </div>
    </div>
    <div class="m-t-md">
        <small class="pull-right">
            <i class="fa fa-clock-o"></i> Actualizado al {{ $today->format('d.m.Y') }}
        </small>
    </div>
</div>
<script>
    
$("#year_filter_mm").change( event => {
    var year=$('#year_filter_mm').val();
    var month=$('#month_filter_mm').val();
    load_graph_mm(year, month);
});

$("#month_filter_mm").change( event => {
    var year=$('#year_filter_mm').val();
    var month=$('#month_filter_mm').val();
    load_graph_mm(year, month);
});

$(document).ready(function() {

    $("#year_filter_mm").select2({
      language: "es",
      placeholder: "Año",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
    });
    
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
                label: "Margen SC",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,1)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: {!! $array_margin_sc_month !!}
            }
        ]
    };

    var lineOptionsMarginsMonth = {
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

    var ctx_margins_month = document.getElementById("lineChartMarginsMonth").getContext("2d");
    var myNewChartMarginsMonth = new Chart(ctx_margins_month).Line(lineDataMarginsMonth, lineOptionsMarginsMonth);

});
</script>
