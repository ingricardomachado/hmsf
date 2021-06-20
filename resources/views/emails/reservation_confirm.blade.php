@component('mail::layout')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ $reservation->condominium->name }}
    @endcomponent
@endslot

# Hola {{ $reservation->property->user->name }}

<p>Tu reservación ha sido {{ ($reservation->status=='A')?'aprobada':'rechazada' }}</p>

<p>
    <b>Propiedad:</b> {{ $reservation->property->number }}<br>
    <b>Instalación:</b> {{ $reservation->facility->name }}<br>
    @if($reservation->all_day)
        <b>Fecha: </b>{{ $reservation->facility->start->format('d/m/Y') }} Todo el día.
    @else
        <b>Fecha: </b>{{ $reservation->facility->start->format('d/m/Y') }} desde {{ $reservation->start->format('g:i a') }} hasta {{ $reservation->end->format('g:i a') }} ({{ $reservation->tot_hours }} horas).
    @endif
</p>

@if($reservation->observations)
<p>
	<b>Observaciones del Administrador:</b> {{ $reservation->observations }}
</p>
@endif

@if($reservation->status=='A' && $reservation->fee_id)
<p>
	Se ha generado una cuota ({{ $reservation->fee->concept }}) por <b>{{ session('coin') }}{{ money_fmt($reservation->fee->amount) }}</b> con fecha de vencimiento <b>{{ $reservation->fee->due_date->format('d/m/Y') }}</b>.	
</p>
@endif

<p>Si tienes alguna duda puedes comunicarte con la administración del condominio y con gusto te atenderemos.</p>

Saludos!

@slot('footer')
        @component('mail::footer')
            <b>{{ $reservation->condominium->name }}</b><br>
			{{ $reservation->condominium->address }}, {{ ($reservation->state_id)?$reservation->subscriber->state->name:'' }} {{ $reservation->condominium->city }}
        @endcomponent
@endslot
@endcomponent
