@component('mail::layout')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ $expense->condominium->name }}
    @endcomponent
@endslot

# Estimado {{ $expense->supplier->name }}

<p>Hemos efectuado un pago a su nombre</p>

<p>
    <b>Fecha:</b> {{ $expense->date->format('d/m/Y') }}<br>
    <b>Concepto:</b> {{ $expense->concept }}<br>
    <b>Método de Pago:</b> {{ $expense->payment_method_description }}<br>
    @if($expense->reference)
        <b>Nro Referencia:</b> {{ $expense->reference }}<br>
    @endif
    <b>Monto:</b> {{ money_fmt($expense->amount) }}<br>
</p>

<p>Si tienes alguna duda puedes comunicarte con la administración del condominio y con gusto te atenderemos.</p>

Saludos!

@slot('footer')
        @component('mail::footer')
            <b>{{ $expense->condominium->name }}</b><br>
			{{ $expense->condominium->address }}, {{ ($expense->state_id)?$expense->subscriber->state->name:'' }} {{ $expense->condominium->city }}
        @endcomponent
@endslot
@endcomponent
