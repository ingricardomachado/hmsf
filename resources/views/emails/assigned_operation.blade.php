@component('mail::message')
# Operación asignada!

<p>    
    <b>Operación Nro:</b> {{ $operation->number }}<br>
    <b>Fecha:</b> {{ $operation->date->format('d/m/Y') }}<br>
    <b>Cliente:</b> {{ $operation->customer->full_name }}<br>
    <b>Monto a Retornar:</b> {{ money_fmt($operation->return_amount) }}<br>

</p>
@endcomponent
