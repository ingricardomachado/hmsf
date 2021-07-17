@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Estado de Cuenta</h2>
    <div>
        <b>Propiedad:</b> {{ $property->number }}<br>
        <b>Propietario:</b> {{ ($property->user_id)?$property->user->name:'' }}
    </div>    
    <div style="margin-top:2mm;margin-bottom: 2mm;">
        <b>Vencido:</b> {{ session('coin') }} {{ money_fmt($property->due_debt) }}<br>
        <b>Pendiente:</b> {{ session('coin') }} {{ money_fmt($property->debt) }}<br>
        <span style="font-size:12px"><b>DEUDA TOTAL:</b> {{ session('coin') }} {{ money_fmt($property->total_debt) }}</span>
    </div>    
    <div>
      <table class="table" width="100%">
        <thead>
          <tr>
            <th class="text-left" width="30%">Cuota</th>
            <th class="text-left">Monto</th>
            <th class="text-left">Pagado</th>
            <th class="text-left">Por pagar</th>
            <th class="text-left">Aplicación</th>
            <th class="text-left">Vencimiento</th>
            <th class="text-left">Estado</th>
          </tr>
        </thead>
        <tbody>
            @foreach($fees as $fee)
            @php($paid=$fee->payments()->where('status','A')->sum('payment_fee.amount'))
            <tr>
                <td>
                    {{ $fee->concept }}<br>
                    <small><i>{{ $fee->income_type->name }}</small>
                </td>
                <td>{{ money_fmt($fee->amount) }}</td>
                <td>{{ ($paid>0)?money_fmt($paid):'' }}</td>
                <td>{{ ($fee->balance>0)?money_fmt($fee->balance):'' }}</td>
                <td>{{ $fee->date->format('d/m/Y') }}</td>
                <td>
                  {{ $fee->due_date->format('d/m/Y')}}<br>
                  <small><i>{{ $fee->remainig_days_description }}</i></small>
                </td>
                <td>{!! $fee->status_label !!}</td>
            </tr>
            @endforeach
        </tbody>
      </table>
    </div>    
@endsection

