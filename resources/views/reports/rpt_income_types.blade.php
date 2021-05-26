@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Tipos de Ingresos</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($income_types as $income_type)                    
            <tr>
                <td class="text-left">{{ $income_type->name }}</td>
                <td class="text-left">{{ $income_type->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

