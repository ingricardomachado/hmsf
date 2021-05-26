@component('mail::message')
# Bienvenido(a) {{ $user->name }}

<p>Acabas de registrar tu condominio <b>{{ $user->condominium->name }}</b> en SmartCond tu condominio inteligente. Desde yá podrás disfrutar de <b>15 días completamente gratis</b>.<p>

<p> Si tienes alguna duda puedes comunicarte con nosotros a través de nuestro número Whastapp +585439974 o de nuestro correo electrónico atencion.cliente@smartcond.com.ve y con gusto te atenderemos.</p>

<p>Si olvidate tu contraseña puedes hacer click en el siguiente botón</p>

@component('mail::button', ['url' => url('/password/reset')])
Olvidé mi contraseña
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
