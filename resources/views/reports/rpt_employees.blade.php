@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Empleados</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Celular</th>
                <th class="text-left">Telefono</th>
                <th class="text-left">Correo</th>
                <th class="text-left">Dirección</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($employees as $employee)                    
            <tr>
                <td class="text-left">
                    <b>{{ $employee->name }}</b><br>
                    <small>{{ $employee->position }}</small>
                </td>
                <td class="text-left">{{ $employee->cell }}</td>
                <td class="text-left">{{ $employee->phone }}</td>
                <td class="text-left">{{ $employee->email }}</td>
                <td class="text-left"><small>{{ $employee->address }}</small></td>
                <td class="text-left">{{ $employee->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

