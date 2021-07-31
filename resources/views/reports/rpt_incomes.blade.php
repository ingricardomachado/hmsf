@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Ingresos extraordinarios</h2>
    <div style="margin-bottom:2mm">
        <b>Desde:</b> {{ $start }} <b>Hasta:</b> {{ $end }}<br>
    </div>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Fecha</th>
                <th class="text-left">Ingreso</th>
                <th class="text-left">Cuenta</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
        @foreach($incomes as $income)                    
            <tr>
                <td class="text-left">{{ $income->date->format('d/m/Y') }}</td>
                <td class="text-left">
                    {{ $income->concept }}<br>
                    <small>{{ $income->income_type->name }}</small>
                </td>
                <td class="text-left">{{ $income->account->aliase }}<br>
                    <small>{{ $income->payment_method_description }}</small>
                </td>
                <td class="text-right">{{ session('coin') }} {{ money_fmt($income->amount) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3"></th>
                <th class="text-right">TOTAL.: {{ session('coin') }} {{ money_fmt($incomes->sum('amount')) }}</th>
            </tr>
        </tfoot>
    </table>
@endsection

