@extends('layouts.blank_report')
@push('stylesheets')
@endpush
@section('content')
<table class="table" width="100%">
    <tbody>
        <tr>
            <td class="text-left">
                <div><img alt="image" style="max-height:auto; width:100px;" src="{{ $logo }}"/></div>
            </td>
            <td class="text-right">
                <span style="font-size:16px"><b>{{ $payment->condominium->name }}</b></span>
                {{ $payment->condominium->url }}<br>
                <b>Dirección:</b> {{ $payment->condominium->address }}<br>
                <b>Teléfono:</b> {{ $payment->condominium->phone }}
            </td>
        </tr>
    </tbody>
</table>
<h2><strong>Recibo de Pago</strong></h2>
<table class="table" width="100%">
    <tbody>
        <tr>
            <td width="50%">
                <b>Fecha:</b> {{ $payment->date->format('d/m/Y') }}<br>
                <b>Cuenta:</b> {{ $payment->account->aliase }}<br>
                <b>Metodo de Pago:</b> {{ $payment->payment_method_description }}<br>
                <b>REF:</b> {{ $payment->reference }}<br>

            </td>
            <td width="50%" class="text-right">
                <div style="font-size: 16px;margin-bottom: 2mm;"><b>MONTO:</b> {{ session('coin') }} {{ money_fmt($payment->amount) }}</div>
                <b>Propiedad:</b> {{ $payment->property->number }}<br>
                <b>Propietario:</b> {{ ($payment->property->user_id)?$payment->property->user->name:'' }}
            </td>
        </tr>
    </tbody>
</table>
<br><br>        
<span style="font-size:11px">
    <b>CUOTAS PAGADAS</b>
</span>
<table class="table" width="100%">
    <thead>
        <tr>
            <th class="text-left"></th>
            <th class="text-left">Cuota pagada</th>
            <th class="text-right">Monto pagado</th>
        </tr>
    </thead>
    <tbody>
        @php($i=1)
        @foreach($payment->fees()->get() as $fee)
            <tr>
                <td class="text-left"><b>{{ $i++ }}</b></td>
                <td class="text-left">
                  {{ $fee->concept }} <b>{{ session('coin') }} {{ money_fmt($fee->amount) }}</b>
                </td>
                <td class="text-right">
                    {{ session('coin') }} {{ money_fmt($fee->pivot->amount) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection

