<div class="col-sm-12 text-right" style="margin-bottom:2mm">
    <a href="#" class="btn btn-xs btn-primary" onclick="showModalActivity({{ $project->id }}, 0);">Nueva Actividad</a>
</div>
@if($activities->count()>0)
<div class="table-responsive col-sm-12">
<table class="table">
    <thead>
        <tr>
            <th></th>
            <th>Actividad</th>
            <th>Fecha</th>
            <th>Avance</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach($activities as $activity)
        <tr>
            <td>
              <a href="#" class="text-muted" style="color:inherit" onclick="showModalDeleteActivity('{{ $activity->id }}', '{{ $activity->name }}')" title="Eliminar"><i class="fa fa-trash-o"></i></a>
            </td>
            <td>
              <a href="#" onclick="showModalActivity({{ $project->id }}, {{ $activity->id }})" style="color:inherit"  title="Click para editar">{{ $activity->name }}</a>
            </td>
            <td>{{ $activity->date->format('d.m.Y') }}</td>
            <td>{{ $activity->advance }}%</td>
            <td><p class="small">{{ $activity->observation }}</p></td>
        </tr>
    @endforeach
    </tbody>
</table>
<br><br><br>
</div>
@endif

<!-- Modal para Actividades -->
<div class="modal inmodal" id="modalActivity" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div id="activity"></div>
    </div>
  </div>
</div>
<!-- /Modal para Actividades -->

<!-- Modal para eliminar -->
<div class="modal inmodal" id="modalDeleteActivity" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-trash-o"></i> <strong>Eliminar Actividad</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
        <input type="hidden" id="hdd_activity_id" value=""/>
          <p>Está seguro que desea eliminar la actividad <b><span id="span_activity_name"></span></b> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btn_delete_activity" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar-->

<script>

function showModalActivity(id, activity_id){
  url = '{{URL::to("project_activities.load")}}/'+id+'/'+activity_id;
  $('#activity').load(url);  
  $("#modalActivity").modal("show");
}    

function showModalDeleteActivity(activity_id, name){
  $('#hdd_activity_id').val(activity_id);
  $('#span_activity_name').html(name);
  $("#modalDeleteActivity").modal("show");    
};

$("#btn_delete_activity").on('click', function(event) {    
    activity_delete($('#hdd_activity_id').val());
});

function activity_delete(id){  
  $.ajax({
      url: `{{URL::to("project_activities")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modalDeleteActivity').modal('toggle');
      project_activities();
      project_progress();
      $('#total_activities').html(response.total_activities);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
      $('#modalDeleteActivity').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function activity_CRUD(id){
  var validator = $( "#form_activity" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    var project_id = '{{ $project->id }}';
    $.ajax({
        url:(id==0)?'{{URL::to("project_activities")}}':'{{URL::to("project_activities")}}/'+id,
        type: (id==0)?'POST':'PUT',          
        data: {
            _token: "{{ csrf_token() }}", 
            project_id: project_id,
            name: $('#name').val(),
            date: $('#date').val(),
            advance: $('#advance').val(),
            observation: $('#observation').val()
        },          
    })
    .done(function(response) {
        $('#modalActivity').modal('toggle');
        project_activities();
        project_progress();
        $('#total_activities').html(response.total_activities);
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
