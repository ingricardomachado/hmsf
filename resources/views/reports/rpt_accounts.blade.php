@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Cuentas</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Banco</th>
                <th class="text-left">Saldo</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($accounts as $account)                    
            <tr>
                <td class="text-left">
                    <b>{{ $account->aliase }}</b><br>
                    <small>
                        {{ $account->date_initial_balance->format('d/m/Y') }}<br>
                        {{ $account->type_description }}
                    </small>
                </td>
                <td class="text-left">
                    {{ $account->bank }}<br>
                    <small>
                        {{ $account->number }}<br>
                        {{ $account->holder }}
                    </small>
                </td>
                <td class="text-left">{{ session('coin') }} {{ money_fmt($account->balance) }}</td>
                <td class="text-left">{{ $account->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

