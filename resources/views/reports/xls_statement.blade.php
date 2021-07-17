<div>
    Propiedad: {{ $property->number }}<br>
    Propietario: {{ ($property->user_id)?$property->user->name:'' }}<br>
    Vencido: {{ session('coin') }} {{ money_fmt($property->due_debt) }}<br>
    Pendiente: {{ session('coin') }} {{ money_fmt($property->debt) }}<br>
    DEUDA TOTAL: {{ session('coin') }} {{ money_fmt($property->total_debt) }}<br>
</div>    
<div>
  <table class="table">
    <thead>
      <tr>
        <th>Cuota</th>
        <th class="text-right">Monto</th>
        <th class="text-right">Pagado</th>
        <th class="text-right">Por pagar</th>
        <th>Aplicación</th>
        <th>Vencimiento</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
        @foreach($fees as $fee)
        @php($paid=$fee->payments()->where('status','A')->sum('payment_fee.amount'))
        <tr>
            <td>{{ $fee->concept }}</td>
            <td>{{ $fee->amount }}</td>
            <td>{{ ($paid>0)?$paid:'' }}</td>
            <td>{{ ($fee->balance>0)?$fee->balance:'' }}</td>
            <td>{{ $fee->date->format('d/m/Y') }}</td>
            <td>{{ $fee->due_date->format('d/m/Y')}}</td>
            <td>{!! $fee->status_description !!}</td>
        </tr>
        @endforeach
    </tbody>
  </table>
</div>