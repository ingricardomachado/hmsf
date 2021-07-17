<div class="col-sm-12 text-right" style="margin-bottom:2mm">
  <a href="#" class="btn btn-xs btn-primary" onclick="showModalComment({{ $project->id }}, 0);">Postear comentario</a>
</div>

<div class="col-sm-12">
<div class="feed-activity-list">
  @foreach($comments as $comment)
    <div class="feed-element">
        <a href="#" class="pull-left">
            <img alt="image" class="img-circle" src="{{ url('user_avatar/'.$comment->user_id) }}">
        </a>
        <div class="media-body ">
            <span class="pull-right text-muted"><small><time class="timeago" datetime="{{$comment->created_at->format(DateTime::ATOM)}}">{{gmdate($comment->created_at)}}</time></small></span>
            <strong><small>{{ $comment->user->name }}</small></strong><br>
            <small class="text-muted">{{ $comment->created_at->format('h:i a') }} - {{ $comment->created_at->format('d.m.Y') }}</small>
            <div class="pull-right">
              @if($comment->user_id==Auth::user()->id || Auth::user()->role=='ADM')
                <a href="#" style="color:inherit" onclick="showModalComment({{ $project->id }}, {{ $comment->id }})"><i class="fa fa-pencil-square-o"></i></a>            
                <a href="#" style="color:inherit" onclick="showModalDeleteComment('{{ $comment->id }}', '{{ $comment->user->name }}')"><i class="fa fa-trash-o"></i></a>
              @endif              
              {!! $comment->visibility_label !!}
            </div>
            <div class="well" style="font-size: 10pt">{{ $comment->comment }}</div>
        </div>
    </div>
  @endforeach
</div>
</div>

<!-- Modal para Comentarios -->
<div class="modal inmodal" id="modalComment" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="comment"></div>
    </div>
  </div>
</div>
<!-- /Modal para Comentarios -->

<!-- Modal para eliminar -->
<div class="modal inmodal" id="modalDeleteComment" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> <strong>Eliminar Comentario</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
        <input type="hidden" id="hdd_comment_id" value=""/>
          <p>Está seguro que desea eliminar el comentario de <b><span id="span_comment_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btn_delete_comment" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar-->

<!-- Timeago -->
<script src="{{ URL::asset('js/plugins/jquery-timeago/jquery.timeago.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-timeago/locales/jquery.timeago.es.js') }}"></script>
<script>

$(document).ready(function() {
        
  $("time.timeago").timeago();

});

function showModalComment(project_id, comment_id){
  url = '{{URL::to("project_comments.load")}}/'+project_id+'/'+comment_id;
  $('#comment').load(url);  
  $("#modalComment").modal("show");
}    

function showModalDeleteComment(comment_id, name){
  $('#hdd_comment_id').val(comment_id);
  $('#span_comment_name').html(name);
  $("#modalDeleteComment").modal("show");
};

$("#btn_delete_comment").on('click', function(event) {    
    comment_delete($('#hdd_comment_id').val());
});

function comment_delete(id){  
  $.ajax({
      url: `{{URL::to("project_comments")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteComment').modal('toggle');
      project_comments();
      $('#total_comments').html(response.total_comments);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
      $('#modalDeleteComment').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function comment_CRUD(id){
  var validator = $( "#form_comment" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    var project_id = '{{ $project->id }}';
    $.ajax({
        url:(id==0)?'{{URL::to("project_comments")}}':'{{URL::to("project_comments")}}/'+id,
        type: (id==0)?'POST':'PUT',          
        data: {
            _token: "{{ csrf_token() }}", 
            project_id: project_id,
            comment: $('#comment_txt').val(),
            visibility: $("#form_comment input[type='radio']:checked").val()
        },          
    })
    .done(function(response) {
        $('#modalComment').modal('toggle');
        project_comments();
        $('#total_comments').html(response.total_comments);
        toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);            
    })
    .fail(function(reponse) {
      if(response.status == 422){
        $('#btn_submit').attr('disabled', false);
        var errorsHtml='';
        $.each(response.responseJSON.errors, function (key, value) {
          errorsHtml += '<li>' + value[0] + '</li>'; 
        });          
        toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 3000);
      }else{
        toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
      }
    });
  }
}

</script>
