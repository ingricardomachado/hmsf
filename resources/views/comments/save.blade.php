    <form action="" id="form_comment" method="POST">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-comments-o" aria-hidden="true"></i> Comentarios de la Operación</h5>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <label>Comentar *</label><small> Max 200 caracteres.</small>
                    {!! Form::textarea('comment', null, ['id'=>'comment', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'rows'=>'2', 'maxlength'=>'200', 'required']) !!}
                </div>
                <div class="col-sm-12 text-right" style="margin-top: 2mm;margin-bottom: 2mm;">
                    <button type="button" id="btn_comment" onclick="" class="btn btn-sm btn-primary">Postear</button>
                </div>

                <div class="form-group col-sm-12">
                    <span id="comments"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<!-- Timeago -->
<script src="{{ URL::asset('js/plugins/jquery-timeago/jquery.timeago.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-timeago/locales/jquery.timeago.es.js') }}"></script>
<!-- Scroll -->
<script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

<script>

function posts(){
  var id = {{ $operation->id }};
  url = '{{URL::to("comments.index")}}/'+id;
  $('#comments').load(url);  
}

function comment_delete(id){  
  $.ajax({
      url: `{{URL::to("comments")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      posts();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
      $('#modalDeleteCar').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

$("#btn_comment").on('click', function(event) {    
    var validator = $("#form_comment").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_comment').attr('disabled', true);
            var id = {{ $operation->id }};
            $.ajax({
              url: `{{URL::to("comments")}}`,
              type: 'POST',
              data: {
                _token: "{{ csrf_token() }}",
                operation:id,
                comment:$('#comment').val(), 
              },
            })
            .done(function(response) {
                $('#comment').val('');
                $('#btn_comment').attr('disabled', false);
                posts();
                toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
            })
            .fail(function(response) {
            if(response.status == 422){
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
});


$(document).ready(function() {
    posts();

    $('#comment').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  


});
</script>