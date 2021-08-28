@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Oficinas</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left">Oficina</th>
                        <th class="text-left">Direccion</th>
                        <th class="text-left">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($centers as $center)                    
                    <tr>
                        <td class="text-left">
                            <b>{{ $center->name }}</b><br>
                            <small><i>{{ $center->state->name }}</i></small>
                        </td>
                        <td class="text-left">{{ $center->address }}</td>
                        <td class="text-left">{!! $center->status_label !!}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

