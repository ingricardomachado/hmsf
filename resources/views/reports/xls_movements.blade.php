@php($initial_balance=$account->balance_at($start))
<div>
    Cuenta: {{ $account->aliase }}<br>
    @if($account->type=='B')
    Banco: {{ $account->bank }}<br>
    Nro: {{ $account->number }}<br>
    @endif
    Saldo al {{ $start->format('d/m/Y') }}: {{ money_fmt($initial_balance) }}<br><br>     
</div>    
@if($movements->count()>0)
<div>
    <table class="table">
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
                <td class="text-right">{{ ($movement->type=='D')?'-':'' }}{{ $movement->amount }}</td>
                <td class="text-right">{{ $balance }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>    
</div>    
@endif