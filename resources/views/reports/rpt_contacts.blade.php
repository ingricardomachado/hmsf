@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Contactos</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Celular</th>
                <th class="text-left">Telefono</th>
                <th class="text-left">Correo</th>
                <th class="text-left">Dirección</th>
            </tr>
        </thead>
        <tbody>
        @foreach($contacts as $contact)                    
            <tr>
                <td class="text-left">
                    @if($contact->position)
                        <b>{{ $contact->name }}</b><br>
                        <small>{{ $contact->position }}<br>
                        <b>{{ $contact->company }}</b></small>
                    @else
                        <b>{{ $contact->name }}</b>
                    @endif
                </td>
                <td class="text-left">{{ $contact->cell }}</td>
                <td class="text-left">{{ $contact->phone }}</td>
                <td class="text-left">{{ $contact->email }}</td>
                <td class="text-left"><small>{{ $contact->address }}</small></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

