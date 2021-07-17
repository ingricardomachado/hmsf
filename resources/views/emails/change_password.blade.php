@component('mail::layout')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ $user->condominium->name }}
    @endcomponent
@endslot

    # Hola {{ $user->name }}

    <p>
        Su contraseña ha sido cambiada por el administrador.<br>
        
        <b>Usuario:</b> {{ $user->email }}<br>
        <b>Contraseña:</b> {{ $password }}<br>
    </p>

    <p>Si olvidate tu contraseña puedes hacer click en el siguiente botón</p>

    @component('mail::button', ['url' => url('/password/reset')])
        Olvidé mi contraseña
    @endcomponent

Gracias,<br>
@slot('footer')
        @component('mail::footer')
            <b>{{ $user->condominium->name }}</b><br>
			{{ $user->condominium->address }}, {{ ($user->state_id)?$user->subscriber->state->name:'' }} {{ $user->condominium->city }}
        @endcomponent
@endslot
@endcomponent
