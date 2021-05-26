@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Propiedades</h2>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Número</th>
                <th class="text-left">Propietario</th>
                <th class="text-right">Vencido</th>
                <th class="text-right">Pendiente</th>
                <th class="text-right">Total</th>
                <th class="text-left">Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($properties as $property)                    
            <tr>
                <td class="text-left"><b>{{ $property->number }}</b></td>
                <td class="text-left">{{ ($property->user_id)?$property->user->name:'' }}</td>
                <td class="text-right">{{ money_fmt($property->due_debt) }}</td>
                <td class="text-right">{{ money_fmt($property->debt) }}</td>
                <td class="text-right">{{ money_fmt($property->total_debt) }}</td>
                <td class="text-left">{{ $property->status_description }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-left" colspan="2">TOTALES</th>
                <th class="text-right">{{ money_fmt($properties->sum('due_debt')) }}</th>
                <th class="text-right">{{ money_fmt($properties->sum('debt')) }}</th>
                <th class="text-right">{{ money_fmt($properties->sum('total_debt')) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
@endsection

