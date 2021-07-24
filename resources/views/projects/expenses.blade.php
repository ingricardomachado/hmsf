
<div class="table-responsive col-sm-12">
  <table class="table" id="datatable-properties" width="100%" style="font-size: 12px">
    <thead>
      <tr>
        <th width="10%">Fecha</th>
        <th width="65%">Egreso</th>
        <th width="15%">Monto</th>
        <th width="10%">Soporte</th>
      </tr>
    </thead>
    <tbody>
      @foreach($expenses as $expense)
      <tr>
        <td>{{ $expense->date->format('d/m/Y') }}</td>
        <td>
          {!! ($expense->supplier_id)?$expense->supplier->name.' - '.$expense->concept:$expense->concept !!}
        </td>
        <td>{{ session('coin') }} {{ money_fmt($expense->amount) }}</td>
        <td>{!! $expense->download_file !!}</td>
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


