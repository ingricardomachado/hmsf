<!-- Fileinput -->
<link href="{{ URL::asset('js/plugins/kartik-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet" type="text/css" />
<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Switchery -->
<link href="{{ URL::asset('js/plugins/switchery/dist/switchery.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>

    <form action="{{url('users/'.$user->id)}}" id="form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        @if($user->id)
            {{ Form::hidden ('_method', 'PUT') }}
            {!! Form::hidden('hdd_user_id', $user->id) !!}
        @endif        
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($user->id) ? "Modificar Usuario" : "Registrar Usuario" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <!-- columna 1 -->
                <div class="col-sm-6">                            
                    <div class="form-group">
                        <label>Avatar </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) Recomendación Máx. 200px por 200px</small>
                        <input id="avatar" name="avatar" type="file">
                    </div>
                </div>
                <!-- columna 2 -->
                <div class="col-sm-6">              
                    @if(Session::get('role')=='SAM')
                        <div class="form-group">
                            <label>Rol *</label>
                            {{ Form::select('role', ['SAM'=>'Super Administrador', 'ADM'=>'Administrador', 'CAJ'=>'Cajero'], ($user->id)?$user->role:'', ['id'=>'role', 'class'=>'select2_single form-control', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="form-group" id="div_center" style="display:{{ ($user->role=='SAM')?'none':'solid' }}">
                            <label>Centro *</label>
                            {{ Form::select('center', $centers, $user->center_id, ['id'=>'center', 'class'=>'select2_single form-control', 'placeholder'=>'', 'required'])}}
                        </div>
                    @else
                        <div class="form-group">
                            <label>Rol *</label>
                            {{ Form::select('role', ['ADM'=>'Administrador', 'USR'=>'Usuario'], ($user->id)?$user->role:'ADM', ['id'=>'role', 'class'=>'select2_single form-control', 'placeholder'=>'', 'required'])}}
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Nombre *</label>
                        {!! Form::text('name', $user->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                    </div>
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo electrónico *</label>
                    {!! Form::email('email', $user->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'Ej. correo@dominio.com', 'minlength'=>'3', 'maxlength'=>'50', 'required']) !!}
                    <span id="msj_email" style="color:#cc5965"></span>
                </div>
                @if($user->id)
                    <div class="form-group col-sm-12">
                        <div class="i-checks">
                        <label>{!! Form::checkbox('change_password', null,  false, ['id'=>'change_password']) !!} Cambiar el password.</label>
                        </div>
                    </div>      
                    <div id='div_password' style='display:none;'>
                        <div class="form-group col-sm-6">
                            <label>Contraseña *</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" minlength="6" required="">
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Confirmar Contraseña *</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirme su contraseña" minlength="6" required="">
                        </div>
                    </div>
                @else
                    <div>
                        <div class="form-group col-sm-6">
                            <label>Contraseña *</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" minlength="6" required="">
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Confirmar Contraseña *</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirme su contraseña" minlength="6" required="">
                        </div>
                    </div>
                @endif
                <div class="form-group col-sm-12">
                  <p>{!! Form::checkbox('email_notification', null,  $user->email_notification, ['id'=>'email_notification', 'class'=>'js-switch']) !!}&nbsp;&nbsp;<b>Recibir notificaciones</b>. <small> Recibir correos de notificación.</small></p>
                </div>


                </div>
            </div>
        <div class="modal-footer">
            <button type="submit" id="btn_submit" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>


<!-- Fileinput -->
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/kartik-fileinput/js/fileinput_locale_es.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Switchery -->
<script src="{{ URL::asset('js/plugins/switchery/dist/switchery.js') }}"></script>
<!-- Jquery Validate -->
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/messages_es.js') }}"></script>
<script>
                  
    var user_id = "{{$user->id}}";
      if( user_id == "" )
      {        
        avatar_preview = "<img style='height:150px' src='{{ url('img/avatar_default.png') }}'>";
      }else{
        avatar_preview = "<img style='height:150px' src= '{{ url('user_avatar/'.$user->id) }}' >";
      }
      
      // Fileinput    
      $('#avatar').fileinput({
        language: 'es',
        allowedFileExtensions : ['jpg', 'jpeg', 'png'],
        previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
        showUpload: false,        
        maxFileSize: 2000,
        maxFilesNum: 1,
        overwriteInitial: true,
        progressClass: true,
        progressCompleteClass: true,
        initialPreview: [
          avatar_preview
        ]      
      });            
        
if('{{ $user->id }}'==''){
    $('#email').blur(function(event) {
      verify_email($(this).val());
    });
}

$('#email').focus(function(event) {
   $('#msj_email').html('');
   $('#btn_submit').attr('disabled', false);
});

function verify_email(email){
    $.ajax({
      url: `{{URL::to("verify_email")}}`,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}", 
        email:email
      },
    })
    .done(function(response) {
      if(response.exists){
        $('#msj_email').html('Correo ya existe, intente con otro.');
        $('#btn_submit').attr('disabled', true);
      }
    })
    .fail(function() {
      console.log("error verificando email");
    });
}
    
$("#role").change( event => {
    if(event.target.value=='SAM'){
        $('#div_center').hide();
    }else{
        $('#div_center').show();
    }
});

$(document).ready(function() {

        // Switchery
        var elem = document.querySelector('#email_notification');
        var init = new Switchery(elem, { size: 'small', color: '#1AB394' });
        
        // Select2         
        $("#center").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: 10,
          allowClear: false,
          width: '100%'
        });

        $("#role").select2({
          language: "es",
          placeholder: "Seleccione",
          minimumResultsForSearch: 10,
          allowClear: false,
          width: '100%'
        });

        // iCheck
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('#change_password').on('ifChecked', function(event){ 
          $('#div_password').show();
        });       

        $('#change_password').on('ifUnchecked', function(event){ 
          $('#div_password').hide();
        });       
        
        $("#form").validate({            
            submitHandler: function(form) {
                $("#btn_submit").attr("disabled",true);
                form.submit();
            }        
        });

});

</script>

