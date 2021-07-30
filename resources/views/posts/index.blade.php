@if($posts->count()>0)
<div id="scroll-info">
    <div class="feed-activity-list" style="margin-right: 3mm;">
        @foreach($posts as $post)
            <div class="feed-element">
                <span class="pull-left">
                    <img alt="image" class="img-circle" src="{{ url('user_avatar/'.$post->user_id) }}">
                </span>
                <div class="media-body ">
                    <span class="pull-right text-muted"><small><time class="timeago" datetime="{{$post->created_at->format(DateTime::ATOM)}}">{{gmdate($post->created_at)}}</time></small></span>
                    <strong>{{ $post->user->name }}</strong><br>
                    <small class="text-muted">{{ Carbon\Carbon::parse($post->created_at)->isoFormat('LLLL') }}</small>
                    <a href="#" class="pull-right text-muted" style="color:inherit" onclick="post_delete('{{ $post->id }}')" title="Eliminar mensaje"><i class="fa fa-trash-o"></i></a>
                    <a href="#" class="pull-right text-muted" style="color:inherit;margin-right: 1mm" onclick="showModalReply({{ $post->id }})" title="Responder mensaje"><i class="fa fa-reply"></i></a>
                    <div class="well">
                        {{ $post->comment }}
                    </div>
                    @if($post->replies()->count()>0)
                        @foreach($post->replies()->get() as $reply)
                            <div class="text-muted" style="margin-bottom: 1mm; font-size:11px">
                                <i class="fa fa-angle-double-right" aria-hidden="true"></i> <b>{{ $reply->user->name }}</b> {{ $reply->comment }} {{ $reply->created_at->format('d.m.y H:i') }}
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
    <div class="alert alert-info" style="font-size:12px">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        No hay mensajes para mostrar ...
    </div>
@endif
<script>

$(document).ready(function() {        

    $('#scroll-info').slimScroll({
      height: '600px'
    });
  
    $("time.timeago").timeago();
});
</script>
