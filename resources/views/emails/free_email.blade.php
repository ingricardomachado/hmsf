@component('mail::layout')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ $property->condominium->name }}
    @endcomponent
@endslot

    {!! $body !!}

@slot('footer')
        @component('mail::footer')
            <b>{{ $property->condominium->name }}</b><br>
			{{ $property->condominium->address }}, {{ ($property->state_id)?$property->subscriber->state->name:'' }} {{ $property->condominium->city }}
        @endcomponent
@endslot
@endcomponent
