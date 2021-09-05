@if($comments->count()>0)
<div id="scroll-info">
    <div class="feed-activity-list" style="margin-right: 5mm;">
        @foreach($comments as $comment)
            <div class="feed-element">
                    <b>{{ $comment->user->full_name }}</b>
                    <small class="text-muted"><time class="timeago" datetime="{{$comment->created_at->format(DateTime::ATOM)}}">{{gmdate($comment->created_at)}}</time></small>
                    <a href="#" class="pull-right text-muted" style="color:inherit" onclick="comment_delete('{{ $comment->id }}')" title="Eliminar comentario"><i class="fa fa-trash-o"></i></a>
                    <div>
                        <small>{{ $comment->comment }}</small>
                    </div>
            </div>
        @endforeach
    </div>
</div>
@endif
<script>

$(document).ready(function() {        

    $('#scroll-info').slimScroll({
      height: '250px'
    });
  
    $("time.timeago").timeago();
});
</script>

