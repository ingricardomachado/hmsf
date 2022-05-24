<div class="ibox-content">
    <div>
        <span class="pull-right text-right">
            <label>Mes</label> 
            {{ Form::select('month_filter_om', ['1'=>'01','2'=>'02', '3'=>'03', '4'=>'04', '5'=>'05', '6'=>'06', '7'=>'07', '8'=>'08', '9'=>'09', '10'=>'10', '11'=>'11', '12'=>'12'], $month, ['id'=>'month_filter_om', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
        </span>
        <span class="pull-right text-right" style="margin-right:2mm">
            <label>Año</label> 
            {{ Form::select('year_filter_om', $years, $year, ['id'=>'year_filter_om', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
        </span>        
        <span class="pull-right text-right" style="margin-right: 3mm;">
            Total Facturado Mes: <b>{{ money_fmt($tot_incomes_month) }}</b>
        </span>
        <h3 class="font-bold no-margins"> Facturación {{ month_letter($month, 'lg') }} {{ $year }}</h3>
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
            <strong>Facturación diaria {{ session('coin') }}</strong>.
        </small>
    </div>
</div>
<script>

$("#year_filter_om").change( event => {
    var year=$('#year_filter_om').val();
    var month=$('#month_filter_om').val();
    load_graph_om(year, month);
});

$("#month_filter_om").change( event => {
    var year=$('#year_filter_om').val();
    var month=$('#month_filter_om').val();
    load_graph_om(year, month);
});

$(document).ready(function() {

    $("#year_filter_om").select2({
      language: "es",
      placeholder: "Año",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
    });

    $("#month_filter_om").select2({
      language: "es",
      placeholder: "Mes",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
    });
    
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

});    
</script>