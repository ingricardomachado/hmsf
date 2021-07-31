@extends('layouts.blank_report_header')
@push('stylesheets')
@endpush
@section('content')
    <h2 class="text-center">Novedades</h2>
    <div style="margin-bottom:2mm">
        <b>Desde:</b> {{ $start }} <b>Hasta:</b> {{ $end }}<br>
        <b>Vigilante:</b> {{ $user_name }}<br>
        <b>Importancia:</b> {{ $level_name }}
    </div>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left">Novedad</th>
                <th class="text-left">Vigilante</th>
                <th class="text-left">Importancia</th>
            </tr>
        </thead>
        <tbody>
        @foreach($newsletters as $newsletter)                    
            <tr>
                <td class="text-left">
                    <small>{{ Carbon\Carbon::parse($newsletter->date)->isoFormat('LLLL') }}</small></span><br><b>{{ $newsletter->title }}</b><br>
                    <small>{{ nl2br($newsletter->description) }}
                </td>
                <td class="text-left">{{ $newsletter->user->name }}</td>
                <td class="text-left">{{ $newsletter->level_description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

