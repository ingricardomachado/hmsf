@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Empresas Emisoras</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($companies as $empresa)                    
            <tr>
                <td class="text-left">{{ $empresa->name }}</td>
                <td class="text-left">{{ $empresa->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

