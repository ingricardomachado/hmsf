@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Visitas</h2>
    <div style="margin-bottom:2mm">
        <b>Desde:</b> {{ $start }} <b>Hasta:</b> {{ $end }}<br>
        <b>Vigilante:</b> {{ $user_name }}<br>
        <b>Propiedad:</b> {{ $property_number }}
    </div>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Visitante</th>
                <th class="text-left">Visita</th>
                <th class="text-left">Propiedad</th>
                <th class="text-left">Registrado por</th>
            </tr>
        </thead>
        <tbody>
        @foreach($visits as $visit)                    
            <tr>
                <td class="text-left">
                    @if($visit->visiting_car_id)
                        <b>{{ $visit->visitor->name }}</b><br>{{ $visit->visitor->NIT }}<br>
                        {{ $visit->visiting_car->plate }} {{ $visit->visiting_car->make }} {{ $visit->visiting_car->model }}<br><span class="text-muted"><small>{{ Carbon\Carbon::parse($visit->checkin)->isoFormat('LLLL') }}</small></span>
                    @else
                        <b>{{ $visit->visitor->name }}</b><br>
                        {{ $visit->visitor->NIT }}                    
                    @endif
                </td>
                <td class="text-left">
                    {{ $visit->notes }}<br>
                    <small>{{ $visit->visit_type->name }}</small>
                </td>
                <td class="text-left">
                    @if($visit->user_id)
                        <b>{{ $visit->property->number }}</b>{{ ($visit->property->user_id)?$visit->property->user->name:'' }}<br>
                        {{ $visit->property->user->cell }}                    
                    @else
                        <b>{{ $visit->property->number }}</b>
                    @endif
                </td>
                <td class="text-left">{{ $visit->user->name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

