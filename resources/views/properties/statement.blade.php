@extends('layouts.app')

@push('stylesheets')
@endpush
@section('page-header')
@endsection

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox float-e-margins">
        
        <!-- ibox-title -->
        <div class="ibox-title">
          <h5><i class="fa fa-file-text-o" aria-hidden="true"></i> Estado de cuenta</h5>
            <div class="ibox-tools">
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
              <ul class="dropdown-menu dropdown-user">
                <li><a href="#">Config option 1</a></li>
                <li><a href="#">Config option 2</a></li>
              </ul>
              <a class="close-link"><i class="fa fa-times"></i></a>
            </div>
        </div>
        <!-- /ibox-title -->
                    
        <!-- ibox-content- -->
        <div class="ibox-content">
          <div class="row">
            <div class="col-sm-6">
              <b>Propiedad:</b> {{ $property->number }}<br>
              <b>Propietario:</b> {{ ($property->user_id)?$property->user->name:'' }}<br>
            </div>
          @if($fees->count()>0)
            <div class="col-sm-6 col-xs-12 text-right">
                <a href="{{ url('properties.xls_statement', Crypt::encrypt($property->id)) }}" class="btn btn-sm btn-primary btn-outline" target="_blank" title="Exportar Excel">XLS</a>
                <a href="{{ url('properties.rpt_statement', Crypt::encrypt($property->id)) }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
                <a href="{{URL::to('properties')}}" class="btn btn-sm btn-default">Regresar</a>
            </div>
            <div class="col-sm-12" style="margin-top: 2mm">
              <b>Vencido:</b> {{ session('coin') }} {{ money_fmt($property->due_debt) }}<br>
              <b>Pendiente:</b> {{ session('coin') }} {{ money_fmt($property->debt) }}<br>
              <span style="font-size:14px"><b>DEUDA TOTAL:</b> {{ session('coin') }} {{ money_fmt($property->total_debt) }}</span>
            </div>
                                                
            <div class="table-responsive col-sm-12">
              <table class="table table-striped table-hover" id="fees-table">
                <thead>
                  <tr>
                    <th width="30%">Cuota</th>
                    <th width="10%">Monto</th>
                    <th width="10%">Pagado</th>
                    <th width="10%">Por pagar</th>
                    <th width="10%">Aplicación</th>
                    <th width="10%">Vencimiento</th>
                    <th width="10%">Estado</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($fees as $fee)
                    @php($paid=$fee->payments()->where('status','A')->sum('payment_fee.amount'))
                    <tr>
                        <td>
                            <a href="#" onclick="showModalFeeInfo({{ $fee->id }})" style="color:inherit" title="Click para ver detalle">{{ $fee->concept }}<br><small><i>{{ $fee->income_type->name }}</small></i></a>
                        </td>
                        <td>{{ money_fmt($fee->amount) }}</td>
                        <td>{{ ($paid>0)?money_fmt($paid):'' }}</td>
                        <td>{{ ($fee->balance>0)?money_fmt($fee->balance):'' }}</td>
                        <td>{{ $fee->date->format('d/m/Y') }}</td>
                        <td>
                          {{ $fee->due_date->format('d/m/Y')}}<br>
                          <small><i>{{ $fee->remainig_days_description }}</i></small>
                        </td>
                        <td>{!! $fee->status_label !!}</td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="col-sm-6 col-xs-12 text-right">
              <a href="{{URL::to('properties')}}" class="btn btn-sm btn-default">Regresar</a>
            </div>
            <div class="col-sm-12 col-xs-12">
              <div class="alert alert-info" style="margin-top:4mm">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Atención!</strong> Esta propiedad no tiene cuotas pendientes
              </div>
            </div>
          @endif
          </div>
        </div>
        <!-- /ibox-content- -->
      </div>
    </div>
  </div>
</div>
  
<!-- Modal para mostrar -->
<div class="modal inmodal" id="modalFeeInfo" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="fee_info"></div>
    </div>
  </div>
</div>
<!-- /Modal para mostrar -->
@endsection

@push('scripts')
<script>
  
function showModalFeeInfo(id){
  url = '{{URL::to("fees.info")}}/'+id;
  $('#fee_info').load(url);  
  $("#modalFeeInfo").modal("show");
}
 
$(document).ready(function(){
                      
});
</script>
@endpush