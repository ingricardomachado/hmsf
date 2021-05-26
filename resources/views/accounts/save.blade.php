<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
    
    <form action="" id="form_account" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($account->id)?$account->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('account_id', ($account->id)?$account->id:0, ['id'=>'account_id']) !!}
        @if($account->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-university" aria-hidden="true"></i> {{ ($account->id) ? "Modificar Cuenta": "Registrar Cuenta" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">  
                  <label>Caja o Banco *</label>
                  {{ Form::select('type', ['C'=>'Caja', 'B'=>'Banco'], ($account->id)?$account->type:'C', ['id'=>'type', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-12">
                    <label>Nombre o alias *</label>
                    {!! Form::text('aliase', $account->aliase, ['id'=>'aliase', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div id="div_bank" style="display:none">
                    <div class="form-group col-sm-12">
                        <label>Banco *</label>
                        {!! Form::text('bank', $account->bank, ['id'=>'bank', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Número de cuenta *</label>
                        {!! Form::text('number', $account->number, ['id'=>'number', 'class'=>'form-control', 'placeholder'=>'', 'minlength'=>'20', 'maxlength'=>'20', 'required']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Titular *</label>
                        {!! Form::text('holder', $account->holder, ['id'=>'holder', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                    </div>                
                    <div class="form-group col-sm-6">
                        <label>Nro de Identificación *</label> <small>CI, NIT, DNI, RIF</small>
                        {!! Form::text('PIN', $account->PIN, ['id'=>'PIN', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20', 'required']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Correo electrónico del titular</label>
                        {!! Form::email('email', $account->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'Ej. correo@dominio.com', 'minlength'=>'3', 'maxlength'=>'50']) !!}
                    </div>
                </div>
                @if(!$account->id)
                    <div class="form-group col-sm-6">
                        <label>Saldo Inicial {{ Session::get('coin') }} *</label>
                        {!! Form::number('initial_balance', $account->initial_balance, ['id'=>'initial_balance', 'class'=>'form-control', 'placeholder'=>'', 'min' => '1', 'required', 'lang'=>'en-150']) !!}
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Fecha de Saldo Inicial</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            {{ Form::text ('date_initial_balance', null, ['id'=>'date_initial_balance', 'class'=>'form-control', 'type'=>'admission', 'placeholder'=>'', 'required']) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="account_CRUD({{ ($account->id)?$account->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<script>

$("#type").change( event => {
  (event.target.value=='B')?$('#div_bank').show():$('#div_bank').hide();
});

$(document).ready(function() {
    ('{{ $account->type }}'=='B')?$('#div_bank').show():'';

    $("#type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalAccount .modal-content'),
        width: '100%'
    });

    $("#account_type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: true,
        dropdownParent: $('#modalAccount .modal-content'),
        width: '100%'
    });

    //Datepicker 
    $('#date_initial_balance').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
    })
});

</script>