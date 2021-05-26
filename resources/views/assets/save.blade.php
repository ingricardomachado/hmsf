<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet"
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
    
    <form action="{{url('assets/'.$asset->id)}}" id="form_asset" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('condominium_id', ($asset->id)?$asset->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('asset_id', ($asset->id)?$asset->id:0, ['id'=>'asset_id']) !!}
        @if($asset->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-wrench" aria-hidden="true"></i> {{ ($asset->id) ? "Modificar Activo": "Registrar Activo" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">
                    <label>Nombre *</label>
                    {!! Form::text('name', $asset->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Descripción</label><small> Máx. 1000 caracteres</small>
                    {!! Form::textarea('description', $asset->description, ['id'=>'description', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe alguna descripción del artículo...', 'maxlength'=>'1000']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Cantidad *</label>
                    {!! Form::number('quantity', ($asset->id)?$asset->quantity:1, ['id'=>'quantity', 'class'=>'form-control', 'placeholder'=>'', 'step'=>'1', 'min' => '0', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Costo estimado {{ Session::get('coin') }} *</label>
                    {!! Form::number('cost', $asset->cost, ['id'=>'cost', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-4">  
                  <label>Estado *</label>
                  {{ Form::select('status', ['OP'=>'Operativo', 'NO'=>'No operativo', 'RE'=>'Reparación'], ($asset->id)?$asset->status:'OP', ['id'=>'status', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="asset_CRUD({{ ($asset->id)?$asset->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<script>


$(document).ready(function() {
    $('#name').focus();

    $("#status").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalAsset .modal-content'),
        width: '100%'
    });

    $('#description').maxlength({
    warningClass: "small text-muted",
    limitReachedClass: "small text-muted",
    placement: "top-right-inside"
    });  

});

</script>