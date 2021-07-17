<dt>Estado:</dt> 
<dd>
      @if($project->status=='P')
        <div class="btn-group">
          <button id="btn_status_project" data-toggle="dropdown" class="btn btn-sm btn-xs {{ $project->status_btn_class }} dropdown-toggle">{{ $project->status_description }} <span class="caret"></span></button>
            <ul class="dropdown-menu">
              <li><a href="#" onclick="change_status('E')">Ejecución</a></li>
              <li><a href="#" onclick="showModalFinish()">Finalizado</a></li>
            </ul>
        </div>
      @elseif($project->status=='E')
        <div class="btn-group">
          <button id="btn_status_project" data-toggle="dropdown" class="btn btn-sm btn-xs {{ $project->status_btn_class }} dropdown-toggle">{{ $project->status_description }} <span class="caret"></span></button>
            <ul class="dropdown-menu">
              <li><a href="#" onclick="showModalFinish()">Finalizado</a></li>
            </ul>
        </div>
      @else
        {!! $project->status_label !!}
      @endif
</dd>

<script>
function change_status(status){
  var project_id={{ $project->id }};
  $.ajax({
      url: '{{URL::to("projects.status")}}/'+project_id,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}", 
        status:status
      },
  })
  .done(function(response) {
    project_btn_status();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000); 
  })
  .fail(function(response) {
    console.log("error cambiando estado");
  });
}  
</script>