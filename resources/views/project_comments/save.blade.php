<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    
    <form action="{{url('comments/'.$comment->id)}}" id="form_comment" method="POST">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-comment-o" aria-hidden="true"></i> {{ ($comment->id) ? "Modificar Comentario" : "Registrar Comentario" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-12">
                    <label>Comentario</label><small> Máx. 400 caracteres</small>
                    {!! Form::textarea('comment_txt', $comment->comment, ['id'=>'comment_txt', 'class'=>'form-control', 'type'=>'text', 'rows'=>'3', 'style'=>'font-size:12px', 'placeholder'=>'', 'maxlength'=>'400']) !!}
                </div>
                @if(Auth::user()->role!='CLI')
                    <div class="form-group col-sm-12">
                        <div class="i-checks">
                            <label>Visibilidad</label>&nbsp;&nbsp;
                            {!! Form::radio('visibility', 0,  ($comment->id)?(!$comment->public):true, ['id'=>'male']) !!}<small>&nbsp;&nbsp;<i class='fa fa-lock' aria-hidden='true'></i> Privado</small>&nbsp;&nbsp;
                            {!! Form::radio('visibility', 1,  ($comment->id)?($comment->public):false, ['id'=>'female']) !!}<small>&nbsp;&nbsp;<i class='fa fa-unlock' aria-hidden='true'></i> Público</small>              
                    </div>
                </div>  
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="comment_CRUD({{ ($comment->id)?$comment->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>

<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Jquery Validate -->
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/messages_es.js') }}"></script>
<script>
                  

$(document).ready(function() {
        
    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

});

</script>

