@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Operaciones</h2>
    <div style="margin-bottom:2mm">
        <b>Desde:</b> {{ $start }} <b>Hasta:</b> {{ $end }}<br>
        @if($partner)
            <b>Socio Comercial:</b> {{ $partner->user->full_name }}<br>
        @endif        
        @if($customer)
            <b>Cliente:</b> {{ $customer->full_name }}<br>
        @endif        
        @if($user)
            <b>Mensajero:</b> {{ $user->full_name }}<br>
        @endif        
        @if($status)
            <b>Estado:</b> {{ $status }}<br>
        @endif        
    </div>
    <table class="table" width="100%" style="font-size:10px">
        <thead>
            <tr>
                <th class="text-center">Nro</th>
                <th class="text-center">Fecha</th>
                <th class="text-left">Socio</th>
                <th class="text-left">Cliente</th>
                <th class="text-left">Folio</th>
                <th class="text-right">Retorno</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($operations as $operation)                    
            <tr>
                <td class="text-center" valign="top">{{ $operation->number }}</td>
                <td class="text-center" valign="top">{{ $operation->date->format('d/m/Y') }}</td>
                <td class="text-left" valign="top">{{ $operation->partner->user->full_name }}</td>
                <td class="text-left" valign="top">
                    @if($operation->customer->contract)
                        {{ $operation->customer->full_name }}<br>
                        {{ $operation->customer->code }}<br>
                        Contrato {{ $operation->customer->contract }}
                    @else
                        {{ $operation->customer->full_name }}
                    @endif
                </td>
                <td class="text-left" valign="top">{{ $operation->folio }}</td>
                <td class="text-right" valign="top">{{ session('coin') }}{{ money_fmt($operation->return_amount) }}</td>
                <td class="text-center" valign="top">{{ $operation->status_description }}</td>                
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th class="text-right">
                    {{ session('coin') }}{{ money_fmt($operations->sum('return_amount'))}}
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>
@endsection

