@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Usuarios</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Rol</th>
                <th class="text-left">Celular</th>
                <th class="text-left">Telefono</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)                    
            <tr>
                <td class="text-left">{{ $user->name }}</td>
                <td class="text-left">{{ $user->role_description }}</td>
                <td class="text-left">{{ $user->cell }}</td>
                <td class="text-left">{{ $user->phone }}</td>
                <td class="text-left">{{ $user->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection