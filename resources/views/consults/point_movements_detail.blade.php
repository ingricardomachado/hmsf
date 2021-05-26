<small style="font-size: 11pt">
  <div class="col-sm-6">
    <b>Cliente:</b> {{ $customer->name }} <i class="fa fa-phone" aria-hidden="true"></i> {{ $customer->cell }}
  </div>
  <div class="col-sm-6 text-right">
    <b>Total Compras:</b> {{ $customer->purchases()->count() }}
  </div>
  <div class="col-sm-6">
    <b>Puntos al {{ $date->format('d/m/Y') }}:</b>  {{ $balance_at }}
  </div>
  <div class="col-sm-6 text-right">
    <b>Total Importe:</b> {{ money_fmt($customer->purchases()->sum('total')) }} {{ Session::get('coin') }}
  </div>
</small>
<br><br>
<div class="table-responsive col-sm-12">  
  <table id="point_statement-table" class="table" width="100%">
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Tipo</th>
        <th>Movimiento</th>
        <th>Puntos</th>
        <th>Saldo</th>
      </tr>
    </thead>
    @php($balance=$balance_at)
    <tbody>
      @foreach($point_movements as $movement)
        @php($balance=($movement->type=='C')?$balance+$movement->points:$balance-$movement->points)
        <tr class="gradeX">
          <td>{{ $movement->date->format('d/m/Y') }}</td>
          <td>{!! $movement->type_description !!}</td>
          <td>{!! $movement->movement_type_description !!}</td>
          <td>{!! $movement->points_badget !!}</td>
          <td>{{ $balance }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>