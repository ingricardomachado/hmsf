@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Propietarios</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Propiedades</th>
                <th class="text-left">Celular</th>
                <th class="text-left">Telefono</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($owners as $owner)                    
            <tr>
                <td class="text-left">{{ $owner->name }}</td>
                <td class="text-left">{!! $owner->properties_label !!}</td>
                <td class="text-left">{{ $owner->cell }}</td>
                <td class="text-left">{{ $owner->phone }}</td>
                <td class="text-left">{{ $owner->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

