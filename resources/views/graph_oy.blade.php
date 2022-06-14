<div class="ibox-content">    
    <div>
        <span class="pull-right text-right">Total Facturado Año: <b>{{ money_fmt($tot_incomes_year) }}</b></span>
        <h3 class="font-bold no-margins"> Facturación {{ $year }}</h3>
    </div>
    <div class="m-t-sm">
        <div class="row">
            <div class="col-md-4 col-xs-6 pull-right">
                <label>Año</label> 
                {{ Form::select('year_filter_oy', $years, $year, ['id'=>'year_filter_oy', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
            </div>
        </div>
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
<script>

$("#year_filter_oy").change( event => {
    var year=$('#year_filter_oy').val();
    load_graph_oy(year);
});

$(document).ready(function() {

    $("#year_filter_oy").select2({
      language: "es",
      placeholder: "Año",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
    });
    
    //Grafica Facturacion AÑO
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

});     
</script>