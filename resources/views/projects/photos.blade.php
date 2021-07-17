@foreach($photos as $photo)
<div class="col-md-3">
    <div class="thumbnail">
        <div class="image view view-first">
            <img style="max-height:150px;max-width:220px" src="{{ url('project_photo/'.$photo->id) }}" alt="image" />
            <div class="mask">
                <p></p>
                <div class="tools tools-bottom">
                    <a class="popup-link" href="{{ url('project_photo/'.$photo->id) }}" title="{{ $photo->title }}"><i class="fa fa-search"></i></a>
                    @if(Auth::user()->role!='VIS')
                        <a href="#" onclick="showModalEdit('{{ $photo->id }}', '{{ $photo->title }}', '{{ $photo->stage }}')"><i class="fa fa-pencil"></i></a>
                        <a href="#" onclick="remove_photo({{ $photo->id }})"><i class="fa fa-trash"></i></a>
                    @endif
                </div>
            </div>
        </div>
        <div class="caption text-center">
            <p><small>{{ $photo->title }}<br><b>{{ strtoupper($photo->stage_description)}}</b></small></p>
        </div>
    </div>
  </div>
@endforeach

<script type="text/javascript">
$('.popup-link').magnificPopup({
  type: 'image'
  // other options
});    
</script>