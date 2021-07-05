<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span></button>
    <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> Detalle del Pago</h5>
</div>
<div class="modal-body">
    <div class="row">            
        <div class="form-group col-sm-6">
            <b>Fecha:</b> {{ $payment->date->format('d/m/Y') }}<br>
            <b>Cuenta:</b> {{ $payment->account->aliase }}<br>
            <b>Metodo de Pago:</b> {{ $payment->payment_method_description }}<br>
            <b>REF:</b> {{ $payment->reference }}<br>
        </div>
        <div class="form-group col-sm-6">
            <div style="font-size: 16px;margin-bottom: 2mm;"><b>MONTO:</b> {{ session('coin') }} {{ money_fmt($payment->amount) }}</div>
            <b>Propiedad:</b> {{ $payment->property->number }}<br>
            <b>Propietario:</b> {{ ($payment->property->user_id)?$payment->property->user->name:'' }}
        </div>
        <div class="form-group col-sm-12">
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th></th>
                        <th width="70%">Cuota pagada</th>
                        <th width="30%">Monto pagado</th>
                    </tr>
                </thead>
                <tbody>
                    @php($i=1)
                    @foreach($payment->fees()->get() as $fee)
                        <tr>
                            <td><b>{{ $i++ }}</b></td>
                            <td>
                              {{ $fee->concept }} <b>{{ session('coin') }} {{ money_fmt($fee->amount) }}</b>
                            </td>
                            <td>{{ session('coin') }} {{ money_fmt($fee->pivot->amount) }}</td>
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