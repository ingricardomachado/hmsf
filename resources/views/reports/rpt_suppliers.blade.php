@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Proveedores</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Teléfono</th>
                <th class="text-left">Correo</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($suppliers as $supplier)                    
            <tr>
                <td class="text-left">
                    <b>{{ $supplier->name }}</b><br>
                    <small><i>{{ $supplier->supplier_category->name }}</i></small>
                </td>
                <td class="text-left">
                    {{ $supplier->phone }}<br>
                    <small><i>{{ $supplier->contact }}</i></small>
                </td>
                <td class="text-left">{{ $supplier->email }}</td>
                <td class="text-left">{{ $supplier->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

