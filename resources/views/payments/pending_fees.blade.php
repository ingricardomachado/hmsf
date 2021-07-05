@if($pending_fees->count()>0)
  <style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        /* display: none; <- Crashes Chrome on hover */
        -webkit-appearance: none;
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }

    input[type=number] {
        -moz-appearance:textfield; /* Firefox */
    }  
  </style>

  <div class="col-sm-6" style="font-size: 14px;margin-top: 2mm;" align="left">
    <b>{{ $property->number }}</b> {{ ($property->user_id)?$property->user->name:'' }}
  </div>
  <div class="col-sm-6" style="font-size: 14px;margin-top: 2mm;" align="right">
    TOTAL A PAGAR.: <b>{{ session('coin') }} <span id="tot_amount">0,00</span></b>
  </div>
  <table class="table" id="datatable-fees" width="100%" style="font-size: 11px">
    <thead>
      <tr>
        <th width="5%">
          {!! Form::checkbox('check-all', null, false, ['id'=>'check-all', 'class'=>'i-checks']) !!}
        </th>
        <th width="10%">Fecha</th>
        <th width="30%">Cuota</th>
        <th width="10%">Vence</th>
        <th width="25%">Monto a pagar</th>
      </tr>
    </thead>
    <tbody>                  
    @php($i=0)
    @foreach($pending_fees as $fee)
      <tr>
        <td>
          {!! Form::checkbox('fees', $fee->id, null, ['class'=>'i-checks', 'id'=>'fees[]', 'required']) !!}
        </td>
        <td>{{ $fee->date->format('d/m/Y') }}</td>
        <td>
          {{ $fee->concept }}<br>
          <b>{{ session('coin') }} {{ money_fmt($fee->amount) }}</b>
        </td>
        <td class="text-center">
          {{ $fee->due_date->format('d/m/Y') }}<br>
          {!! $fee->bullet !!}          
        </td>
        <td class="text-right">
          {!! Form::number('amounts', $fee->balance, ['id'=>'amounts[]', 'data-amount' => $fee->amount, 'class'=>'form-control form-control-sm', 'min'=>'0.01', 'max'=>$fee->balance, 'step'=>'0.01', 'style'=>'text-align: right; max-width: 120px', 'required', 'disabled']) !!}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <div class="col-sm-12">
    <small>
      <i class="fa fa-circle text-warning" aria-hidden="true"></i> Pendiente
      <i class="fa fa-circle text-danger" aria-hidden="true"></i> Morosa
    </small>
  </div>
@else

  <div class="alert alert-info" style="margin-top: 5mm">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>Atención</strong> Esta propiedad no tiene cuotas pendientes.
  </div>

@endif
<script>

$('#check-all').on('ifChanged', function(event){
  (event.target.checked)?$('input:checkbox').iCheck('check'):$('input:checkbox').iCheck('uncheck');
});

function calc_total_amount(){
  var tot_amount=0;
  $("input[name='fees']:checked").each(function (){
    var index = $("input[id^='fees']").index(this);
    var input_amount=$("input[name^='amounts']").eq(index);
    tot_amount+=parseFloat(input_amount.val());
  });
  $('#tot_amount').html(money_fmt(tot_amount));
  $('#btn_submit').attr('disabled',tot_amount==0);
}


$("input[id^='fees']").on('ifChanged', function(event){
  var index = $("input[id^='fees']").index(this);
  var input_amount=$("input[name^='amounts']").eq(index);
  if(event.target.checked){
    input_amount.attr('disabled',false);
  }else{
    input_amount.val(input_amount.attr('data-amount'));
    input_amount.attr('disabled',true);
  }
  calc_total_amount();
});       

$("input[name^='amounts']").keyup(function () {
  calc_total_amount();
});

$(document).ready(function() {

    $(':input[type=number]').on('mousewheel', function(e){
        e.preventDefault();
    });    
    
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });    

    $('#datatable-fees').DataTable( {
      "ordering": false,
      "searching": false,
      "lengthChange": false,
      "scrollX": false,
      "scrollY": '300px',
      "scrollCollapse": true,
      "paging":false,
      "info" : false,
    } );

});  
</script>
