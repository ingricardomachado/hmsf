@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Tipos de Egresos</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($expense_types as $expense_type)                    
            <tr>
                <td class="text-left">{{ $expense_type->name }}</td>
                <td class="text-left">{{ $expense_type->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

