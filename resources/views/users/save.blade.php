<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
    
    <form action="{{url('users/'.$user->id)}}" id="form_user" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('user_id', ($user->id)?$user->id:0, ['id'=>'user_id']) !!}
        {!! Form::hidden('cell', ($user->id)?$user->cell:null, ['id'=>'cell']) !!}
        @if($user->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($user->id) ? "Modificar Usuario": "Registrar Usuario" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('first_name', $user->first_name, ['id'=>'first_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Apellido *</label>
                    {!! Form::text('last_name', $user->last_name, ['id'=>'last_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo *</label><small> Será su nombre de usuario</small>
                    {!! Form::email('email', $user->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Rol *</label>
                  {{ Form::select('role', ['ADM'=>'Administrador', 'MEN'=>'Mensajero', 'SUP'=>'Supervisor'], $user->role, ['id'=>'role', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                
                <div class="form-group col-sm-12" style="display:{{ ($user->id)?'solid':'none' }}">
                    <div class="i-checks">
                        <label>{!! Form::checkbox('change_password', null,  false, ['id'=>'change_password']) !!} Cambiar la contraseña</label>
                    </div>
                </div>
                <div class="form-group col-sm-6" id='div_password' style='display:{{ ($user->id)?'none':'solid' }};'>
                    <label>Contraseña *</label><small> De 6 a 15 caracteres.</small>
                    <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="6" maxlength="15" required>
                </div>
                <div class="form-group col-sm-6" id='div_password_confirmation' style='display:{{ ($user->id)?'none':'solid' }};'>
                    <label>Confirmar Contraseña *</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="" minlength="6" maxlength="15" required>
                </div>
                <div class="form-group col-sm-6" id="div_notification" style="display:{{ ($user->id)?'none':'solid' }}">
                    <div class="i-checks">
                        <label>{!! Form::checkbox('notification', null,  false, ['id'=>'notification']) !!} <span id="label_notification">Notificar por correo al usuario</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="user_CRUD({{ ($user->id)?$user->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/utils.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<script>

$('#change_password').on('ifChanged', function(event){
  if(event.target.checked){
    $('#div_password').show();
    $('#div_password_confirmation').show();
    $('#label_notification').html('Notificar el cambio de contraseña'); 
    $('#div_notification').show();    
  }else{
    $('#div_password').hide();
    $('#div_password_confirmation').hide();
    $('#label_notification').html('Notificar por correo al usuario');
    $('#div_notification').hide();    
  }  
});

$(document).ready(function() {
    
    $("#role").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalUser .modal-content'),
        width: '100%'
    });

    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

});
</script>