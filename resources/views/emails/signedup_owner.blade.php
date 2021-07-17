@component('mail::layout')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ $owner->condominium->name }}
    @endcomponent
@endslot

    # Hola {{ $owner->name }}

    <p>
        Has sido registrado en el sistema de administración de condominios.<br>
        
        <b>Usuario:</b> {{ $owner->email }}<br>
        <b>Contraseña:</b> {{ $password }}<br>
    </p>

    <p>
        @component('mail::button', ['url' => url(config('app.url'))])
            Entrar al sistema
        @endcomponent        
    </p>

    <p>Si olvidate tu contraseña puedes hacer click en el siguiente botón</p>

    @component('mail::button', ['url' => url('/password/reset')])
        Olvidé mi contraseña
    @endcomponent

Gracias,<br>
@slot('footer')
        @component('mail::footer')
            <b>{{ $owner->condominium->name }}</b><br>
			{{ $owner->condominium->address }}, {{ ($owner->state_id)?$owner->subscriber->state->name:'' }} {{ $owner->condominium->city }}
        @endcomponent
@endslot
@endcomponent
