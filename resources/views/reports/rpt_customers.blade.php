@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Clientes</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Contrato</th>
                <th class="text-left">Socio Comercial</th>
                <th class="text-left">Celular</th>
                <th class="text-left">Comisión</th>
                <th class="text-center">Operaciones</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($customers as $customer)                    
            <tr>
                <td class="text-left">
                    <b>{{ $customer->full_name }}</b><br>
                    {{ $customer->code }}<br>
                    <small>{{ $customer->email }}</small>
                </td>
                <td class="text-left">{{ $customer->contract }}</td>
                <td class="text-left">{{ $customer->partner->user->full_name }}</td>
                <td class="text-left">{{ $customer->cell }}</td>
                <td class="text-left">{{ $customer->tax }}%</td>
                <td class="text-center">{{ $customer->operations()->count() }}</td>
                <td class="text-left">{{ $customer->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

