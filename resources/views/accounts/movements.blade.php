@php($initial_balance=$account->balance_at($start))
<div style="margin-bottom: 4mm">
    <b>Cuenta:</b> {{ $account->aliase }}<br>
    @if($account->type=='B')
    <b>Banco:</b> {{ $account->bank }}<br>
    <b>Nro:</b> {{ $account->number }}<br>
    @endif
    <b>Saldo al {{ $start->format('d/m/Y') }}:</b> {{ money_fmt($initial_balance) }}     
</div>
@if($movements->count()>0)
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Referencia</th>
                <th>Débito/Crédito</th>
                <th class="text-right">Monto {{ session('coin') }}</th>
                <th class="text-right">Saldo {{ session('coin') }}</th>
            </tr>
        </thead>
        <tbody>
            @php($balance=$initial_balance)
            @foreach($movements as $movement)
            @php(($movement->type=='C')?($balance+=$movement->amount):($balance-=$movement->amount))
            <tr>
                <td>{{ $movement->date->format('d/m/Y') }}</td>
                <td>{!! $movement->concept !!}</td>
                <td>{{ $movement->reference }}</td>
                <td>{{ $movement->type_description }}</td>
                <td class="text-right">{{ ($movement->type=='D')?'-':'' }}{{ money_fmt($movement->amount) }}</td>
                <td class="text-right">{{ money_fmt($balance) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
<div class="form-group col-sm-12">    
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Atención!</strong> No hay movimientos en este rango de fechas
    </div>
</div>
@endif