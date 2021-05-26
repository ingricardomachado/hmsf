@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Categorías de Proveedores</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Nombre</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($supplier_categories as $supplier_category)                    
            <tr>
                <td class="text-left">{{ $supplier_category->name }}</td>
                <td class="text-left">{{ $supplier_category->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

