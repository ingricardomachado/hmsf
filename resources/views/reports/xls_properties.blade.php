<table class="table">
  <thead>
    <tr>
      <th>Número</th>
      <th>Propietario</th>
      <th class="text-right">Vencido</th>
      <th class="text-right">Pendiente</th>
      <th class="text-right">Total</th>
      <th class="text-right">Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($properties as $property)
      <tr>
        <td>{{ $property->number }}</td>
        <td>{{ ($property->user_id)?$property->user->name:'' }}</td>
        <td class="text-right">{{ $property->due_debt }}</td>
        <td class="text-right">{{ $property->debt }}</td>
        <td class="text-right">{{ $property->total_debt }}</td>
        <td class="text-right">{{ $property->status_description }}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <th colspan="2">TOTALES</th>
      <th>{{ $properties->sum('due_debt') }}</th>
      <th>{{ $properties->sum('debt') }}</th>
      <th>{{ $properties->sum('total_debt') }}</th>
      <th></th>
    </tr>
  </tfoot>
</table>