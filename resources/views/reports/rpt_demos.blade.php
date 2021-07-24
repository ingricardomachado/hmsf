@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Condominios Demos</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left">Condominio</th>
                        <th class="text-left">Tipo</th>
                        <th class="text-left">Max. Propiedades</th>
                        <th class="text-left">Contacto</th>
                        <th class="text-left">Dias restantes</th>
                        <th class="text-left">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($condominiums as $condominium)                    
                    <tr>
                        <td class="text-left">
                            <b>{{ $condominium->name }}</b><br>
                            <small><i>{{ $condominium->country->name }}</i></small>
                        </td>
                        <td class="text-left">{{ $condominium->type_description }}</td>
                        <td class="text-left">{{ $condominium->max_properties }}</td>
                        <td class="text-left">{{ $condominium->contact }}<br>{{ $condominium->cell }}</td>
                        <td class="text-left">{{ $condominium->remainig_days }}</td>
                        <td class="text-left">{!! $condominium->status_label !!}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

