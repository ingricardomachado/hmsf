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
                        <span> Tot Clientes<br>a la fecha</span>
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
                        <span> Tot Operaciones<br>a la fecha</span>
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
                        <span> Facturado Ultimo Mes {{ $last_day->format('m.Y') }}</span>
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
                        <span> Tot Margen Ultimo Mes {{ $last_day->format('m.Y') }}</span>
                        <h3 class="font-bold">{{ session('coin') }}{{ money_fmt($tot_margin_sc_month) }}</h3>
                    </div>
                </div>
            </div>
        </div>
	</div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <span id="span_graph_om"></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <span id="span_graph_oy"></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <span id="span_graph_my"></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <span id="span_graph_mm"></span>
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

function load_graph_om(year, month){
  url = '{{URL::to("load_graph_om")}}/'+year+'/'+month;
  $('#span_graph_om').load(url);  
}

function load_graph_mm(year, month){
  url = '{{URL::to("load_graph_mm")}}/'+year+'/'+month;
  $('#span_graph_mm').load(url);  
}

function load_graph_oy(year){
  url = '{{URL::to("load_graph_oy")}}/'+year;
  $('#span_graph_oy').load(url);  
}

function load_graph_my(year){
  url = '{{URL::to("load_graph_my")}}/'+year;
  $('#span_graph_my').load(url);  
}

$(document).ready(function() {
    
    var year={{ $last_day->year }};
    var month={{ $last_day->month }};
    
    load_graph_om(year, month);
    load_graph_mm(year, month);
    
    load_graph_oy(year);
    load_graph_my(year);    

});
</script>
@endpush