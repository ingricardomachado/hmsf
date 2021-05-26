    <form action="" id="form_income_type" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($income_type->id)?$income_type->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('income_type_id', ($income_type->id)?$income_type->id:0, ['id'=>'income_type_id']) !!}
        @if($income_type->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-folder-o" aria-hidden="true"></i> {{ ($income_type->id) ? "Modificar Tipo de Ingreso": "Registrar Tipo de Ingreso" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">
                    <label>Nombre *</label>
                    {!! Form::text('name', $income_type->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="income_type_CRUD({{ ($income_type->id)?$income_type->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>


$(document).ready(function() {
    $('#name').focus();
});
</script>