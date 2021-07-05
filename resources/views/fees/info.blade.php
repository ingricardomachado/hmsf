<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span></button>
    <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> Detalle de la Cuota</h5>
</div>
<div class="modal-body">
    <div class="row">            
        <div class="form-group col-sm-6">
            <b>Fecha:</b> {{ $fee->date->format('d/m/Y') }}<br>
            <b>Cuota:</b> {{ $fee->concept }}<br>
            <b>Tipo de Ingreso:</b> {{ $fee->income_type->name }}<br>
            <b>Monto:</b> {{ session('coin') }} {{ money_fmt($fee->amount) }}
        </div>
        <div class="form-group col-sm-6">
            <b>Propiedad:</b> {{ $fee->property->number }}<br>
            <b>Propietario:</b> {{ ($fee->property->user_id)?$fee->property->user->name:'' }}<br>
            <div style="margin-top:2mm">
                <b>POR PAGAR:</b> {{ session('coin') }} {{ money_fmt($fee->balance) }}<br>
                <b>ESTADO:</b> {!! $fee->status_label !!}
            </div>
        </div>
        <div class="form-group col-sm-12">
            <table class="table table-hover table-condensed" width="100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>Fecha</th>
                        <th>Pago</th>
                        <th>Pagado</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @php($i=1)
                    @foreach($fee->payments()->get() as $payment)
                        <tr>
                            <td><b>{{ $i++ }}</b></td>
                            <td>{{ $payment->date->format('d/m/Y') }}</td>
                            <td>
                                <small>{{ $payment->concept }}<br>
                                {{ $payment->account->aliase }} {{ ($payment->reference)?'REF '.$payment->reference:'' }}</small>
                            </td>
                            <td>{{ session('coin') }} {{ money_fmt($payment->amount) }}</td>
                            <td>{!! $payment->status_label !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>