@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Vehículos</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Placa</th>
                <th class="text-left">Marca</th>
                <th class="text-left">Modelo</th>
                <th class="text-left">Año</th>
                <th class="text-left">Color</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($cars as $car)                    
            <tr>
                <td class="text-left">
                    <b>{{ $car->plate }}</b><br>
                    <small><i>{{ $car->property->number }}</i></small>
                </td>
                <td class="text-left">{{ $car->make }}</td>
                <td class="text-left">{{ $car->model }}</td>
                <td class="text-left">{{ $car->year }}</td>
                <td class="text-left">{{ $car->color }}</td>
                <td class="text-left">{{ $car->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

