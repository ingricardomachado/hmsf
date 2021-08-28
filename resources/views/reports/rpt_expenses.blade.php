@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Gastos</h2>
    <div style="margin-bottom:2mm">
        <b>Desde:</b> {{ $start }} <b>Hasta:</b> {{ $end }}<br>
        <b>Proveedor:</b> {{ $supplier_name }}<br>
        <b>Tipo de Gasto:</b> {{ $expense_type_name }}<br>
        <b>Oficina:</b> {{ $center_name }}
    </div>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Fecha</th>
                <th class="text-left">Egreso</th>
                <th class="text-left">Oficina</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
        @foreach($expenses as $expense)                    
            <tr>
                <td class="text-left">{{ $expense->date->format('d/m/Y') }}</td>
                <td class="text-left">
                        {{ $expense->concept }}<br>
                        <small>{{ $expense->expense_type->name }}</small>
                </td>
                <td class="text-left">
                    {{ ($expense->center_id)?$expense->center->name:'' }}
                </td>
                <td class="text-right">{{ session('coin') }} {{ money_fmt($expense->amount) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3"></th>
                <th class="text-right">TOTAL.: {{ session('coin') }} {{ money_fmt($expenses->sum('amount')) }}</th>
            </tr>
        </tfoot>
    </table>
@endsection

