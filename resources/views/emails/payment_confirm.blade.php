@component('mail::layout')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ $payment->condominium->name }}
    @endcomponent
@endslot

# Hola {{ $payment->property->user->name }}

<p>Tu pago ha sido {{ ($payment->status=='A')?'aprobado':'rechazado' }}</p>

<p>
    <b>Propiedad:</b> {{ $payment->property->number }}<br>
    <b>Fecha:</b> {{ $payment->date->format('d/m/Y') }}<br>
    <b>Cuenta:</b> {{ $payment->account->aliase }}<br>
    <b>Metodo de Pago:</b> {{ $payment->payment_method_description }}<br>
    <b>REF:</b> {{ $payment->reference }}<br>
    <span style="font-size: 16px;margin-bottom: 2mm;"><b>MONTO:</b> {{ session('coin') }} {{ money_fmt($payment->amount) }}</span>
</p>

@if($payment->observations)
<p>
	<b>Observaciones del Administrador:</b> {{ $payment->observations }}
</p>
@endif

<p>Si tienes alguna duda puedes comunicarte con la administración del condominio y con gusto te atenderemos.</p>

Saludos!

@slot('footer')
        @component('mail::footer')
            <b>{{ $payment->condominium->name }}</b><br>
			{{ $payment->condominium->address }}, {{ ($payment->state_id)?$payment->subscriber->state->name:'' }} {{ $payment->condominium->city }}
        @endcomponent
@endslot
@endcomponent
