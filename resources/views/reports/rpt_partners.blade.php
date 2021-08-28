@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Socios de Negocio</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Celular</th>
                <th class="text-left">Comisión</th>
                <th class="text-left">Clientes</th>
                <th class="text-left">Operaciones</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($partners as $partner)                    
            <tr>
                <td class="text-left">
                    <b>{{ $partner->full_name }}</b><br>
                    <small>{{ $partner->email }}</small>
                </td>
                <td class="text-left">{{ $partner->cell }}</td>
                <td class="text-left">{{ $partner->tax }}</td>
                <td class="text-left"></td>
                <td class="text-left"></td>
                <td class="text-left">{{ $partner->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

