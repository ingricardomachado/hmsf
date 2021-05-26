@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Activos</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Cantidad</th>
                <th class="text-left">Costo</th>
                <th class="text-left">Total</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($assets as $asset)                    
            <tr>
                <td class="text-left">
                    <b>{{ $asset->name }}</b><br>
                    <small>{{ $asset->description }}</small>
                </td>
                <td class="text-left">{{ $asset->quantity }}</td>
                <td class="text-left">{{ session('coin') }} {{ money_fmt($asset->cost) }}</td>
                <td class="text-left">{{ session('coin') }} {{ money_fmt($asset->total) }}</td>
                <td class="text-left">{{ $asset->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

