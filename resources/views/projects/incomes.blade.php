<div class="table-responsive col-sm-12">
  <table class="table" id="datatable-properties" width="100%" style="font-size: 12px">
    <thead>
      <tr>
        <th width="10%">Fecha</th>
        <th width="65%">Ingreso</th>
        <th width="15%">Monto</th>
        <th width="10%">Soporte</th>
      </tr>
    </thead>
    <tbody>
      @foreach($incomes as $income)
      <tr>
        <td>{{ $income->date->format('d/m/Y') }}</td>
        <td>
          {{ ($income->property)?$income->property.' - '.$income->concept:$income->concept }}
        </td>
        <td>{{ session('coin') }} {{ money_fmt($income->amount) }}</td>
        <td>{!! $income->url !!}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
  $('.popup-link').magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    closeBtnInside: false,
    fixedContentPos: true,
    mainClass: 'my-custom-class'
  });
</script>
