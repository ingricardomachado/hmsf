@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Usuarios</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-center" width="5%">#</th>
                        <th class="text-left">Nombre</th>
                        <th class="text-left">Rol</th>
                        <th class="text-left">Creado</th>
                        <th class="text-left">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($users as $user)                    
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td class="text-left">
                            {{ $user->name }}<br>
                            <small><i>{{ $user->email }}</i></small>
                        </td>
                        <td class="text-left">{{ $user->role_description }}</td>
                        <td class="text-left">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $user->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

