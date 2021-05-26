<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
    
    <form action="#" id="form_supplier" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('supplier_id', ($supplier->id)?$supplier->id:0, ['id'=>'supplier_id']) !!}
        @if($supplier->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-truck" aria-hidden="true"></i> {{ ($supplier->id) ? "Modificar Proveedor": "Registrar Proveedor" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">  
                  <label>Categoría *</label>
                  {{ Form::select('supplier_category', $supplier_categories, $supplier->supplier_category_id, ['id'=>'supplier_category', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>                
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('name', $supplier->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Nro Identificación</label> <small>CI, RIF, NIT, DNI, ID</small>
                    {!! Form::text('NIT', $supplier->name, ['id'=>'NIT', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'15']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Contacto</label>
                    {!! Form::text('contact', $supplier->contact, ['id'=>'contact', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Teléfono *</label>
                    {!! Form::text('phone', $supplier->phone, ['id'=>'phone', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'30', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo</label>
                    {!! Form::email('email', $supplier->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Sitio Web</label>
                    {!! Form::text('url', null, ['id'=>'url', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Dirección</label><small>Max 500 caracteres.</small>
                    {!! Form::text('address', $supplier->addres, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'500']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="supplier_CRUD({{ ($supplier->id)?$supplier->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<script>


$(document).ready(function() {
    $('#name').focus();

    $("#supplier_category").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalSupplier .modal-content'),
        width: '100%'
    });

});

</script>