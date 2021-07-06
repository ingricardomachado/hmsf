@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Estado de Cuenta</h2>
    @php($initial_balance=$account->balance_at($start))
    <div>
        <b>Cuenta:</b> {{ $account->aliase }}<br>
        @if($account->type=='B')
        <b>Banco:</b> {{ $account->bank }}<br>
        <b>Nro:</b> {{ $account->number }}<br>
        @endif
        <b>Saldo al {{ $start->format('d/m/Y') }}:</b> {{ session('coin') }} {{ money_fmt($initial_balance) }}<br><br>     
    </div>    
    @if($movements->count()>0)
    <div>
        <table class="table" width="100%">
            <thead>
                <tr>
                    <th align="left">Fecha</th>
                    <th align="left">Concepto</th>
                    <th align="left">Referencia</th>
                    <th align="left">Débito/Crédito</th>
                    <th align="right">Monto {{ session('coin') }}</th>
                    <th align="right">Saldo {{ session('coin') }}</th>
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
@endsection

