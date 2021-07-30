@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Tipos de Visitas</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($visit_types as $visit_type)                    
            <tr>
                <td class="text-left">{{ $visit_type->name }}</td>
                <td class="text-left">{{ $visit_type->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

