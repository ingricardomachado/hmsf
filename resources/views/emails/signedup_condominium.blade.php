@component('mail::message')
# Condominio registrado!

{{ $condominium->name }}, {{ $condominium->country->name }}<br>
<b>Tipo:</b> {{ $condominium->type_description }}<br>
<b>Propiedades:</b> {{ $condominium->property_type->name }} ({{ $condominium->max_properties }})<br>
<b>Contacto:</b> {{ $condominium->contact }}<br>
<b>Celular:</b> {{ $condominium->cell }}<br>
<b>Correo:</b> {{ $condominium->email }}<br>

{{ config('app.name') }}
@endcomponent
