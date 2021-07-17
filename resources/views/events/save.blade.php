<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- Datetimepicker -->
<link href="{{ URL::asset('js/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
</style>
<style>
.fc-color-picker {
  list-style: none;
  margin: 0;
  padding: 0;
}
.fc-color-picker > li {
  float: left;
  font-size: 30px;
  margin-right: 5px;
  line-height: 30px;
}
.fc-color-picker > li .fa {
  -webkit-transition: -webkit-transform linear 0.3s;
  -moz-transition: -moz-transform linear 0.3s;
  -o-transition: -o-transform linear 0.3s;
  transition: transform linear 0.3s;
}
.fc-color-picker > li .fa:hover {
  -webkit-transform: rotate(30deg);
  -ms-transform: rotate(30deg);
  -o-transform: rotate(30deg);
  transform: rotate(30deg);
}
.text-red {
  color: #ed5565 !important;
}
.text-yellow {
  color: #f8ac59 !important;
}
.text-aqua {
  color: #00c0ef !important;
}
.text-blue {
  color: #1c84c6 !important;
}
.text-black {
  color: #111111 !important;
}
.text-light-blue {
  color: #3c8dbc !important;
}
.text-green {
  color: #00a65a !important;
}
.text-gray {
  color: #d2d6de !important;
}
.text-navy {
  color: #001f3f !important;
}
.text-teal {
  color: #39cccc !important;
}
.text-olive {
  color: #3d9970 !important;
}
.text-lime {
  color: #01ff70 !important;
}
.text-orange {
  color: #ff851b !important;
}
.text-fuchsia {
  color: #f012be !important;
}
.text-purple {
  color: #605ca8 !important;
}
.text-maroon {
  color: #d81b60 !important;
}    
</style>
    
    <form action="#" id="form_event" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />        
        {!! Form::hidden('condominium_id', ($event->id)?$event->condominium_id:session('condominium')->id, ['id'=>'condominium_id']) !!}
        {!! Form::hidden('event_id', ($event->id)?$event->id:0, ['id'=>'event_id']) !!}
        @if($event->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-calendar-o" aria-hidden="true"></i> {{ ($event->id) ? "Modificar Evento": "Registrar Evento" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                <div class="form-group col-sm-6">  
                    <label>Desde *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('start', ($event->id)?$event->start->format('d/m/Y H:i'):null, ['id'=>'start', 'class'=>'form-control', 'type'=>'admission', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>                
                <div class="form-group col-sm-6">  
                    <label>Desde *</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {{ Form::text ('end', ($event->id)?$event->end->format('d/m/Y H:i'):null, ['id'=>'end', 'class'=>'form-control', 'type'=>'admission', 'placeholder'=>'', 'required']) }}
                    </div>
                </div>
                <div class="form-group col-sm-12">
                    <label>Titulo *</label>
                    {!! Form::text('title', $event->title, ['id'=>'title', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Descripción</label><small> Máx. 150 caracteres</small>
                    {!! Form::textarea('description', $event->description, ['id'=>'description', 'class'=>'form-control', 'type'=>'text', 'rows'=>'2', 'style'=>'font-size:12px', 'placeholder'=>'Escribe aqui alguna descripción interés ...', 'maxlength'=>'150']) !!}
                </div>
                <div class="form-group col-sm-12">
                  <label>Seleccione un color</label>
                  <ul class="fc-color-picker" id="color-chooser">
                    <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                    <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                  </ul>
                </div>
                <div class="form-group col-sm-12">
                    <div class="i-checks">
                        {!! Form::checkbox('private', null, ($event->id)?$event->private:false, ['id'=>'private', 'class'=>'i-checks']) !!} <label>El evento es privado.</label> <small>Los eventos privados solo podrán ser visto por los administradores.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            @if($event->id)
              <button type="button" id="btn_delete" onclick="event_delete({{ $event->id }})" class="btn btn-sm btn-primary btn-outline">Eliminar</button>
            @endif
            <button type="button" id="btn_submit" onclick="event_CRUD({{ ($event->id)?$event->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Datetimepicker --> 
<script src="{{ URL::asset('js/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.es.js') }}"></script>
<!-- Maxlenght -->
<script src="{{ asset('js/plugins/bootstrap-character-counter/dist/bootstrap-maxlength.min.js') }}"></script>
<script>

$('#all_day').on('ifChanged', function(event){
  if(event.target.checked){
    $('#div_all_day').hide();
    $('#span_total_hrs').html('Todo el día');
  }else{
    $('#start').val(null).trigger('change');
    $('#end').val(null).trigger('change');
    $('#div_all_day').show();
    $('#span_total_hrs').html('');
    $('#span_total_cost').html('');
  }  
});

//Color chooser button
var currColor='{{ ($event->id)?$event->color:'' }}'; 
$("#color-chooser > li > a").click(function (e) {
  e.preventDefault();
  //Save color
  currColor = $(this).css("color");
  //Add color effect to button
  $('#btn_submit').css({"background-color": currColor, "border-color": currColor});
});

$(document).ready(function() {

    if('{{ $event->id }}'!=''){
      $('#btn_submit').css({"background-color": currColor, "border-color": currColor});      
    }

    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    //Datetimepicker 
    var d = new Date();
    d.setHours(0,0,0,0);    
    $("#start").datetimepicker({
        language: "es",
        startDate: d,
        format: "dd/mm/yyyy hh:ii",
        autoclose: true
    }).on("changeDate", function (e) {
      startDate = new Date(e.date.valueOf());
      startDate.setDate(startDate.getDate(new Date(e.date.valueOf())));
      $('#end').datetimepicker('setStartDate', startDate);
    });
    
    $("#end").datetimepicker({
        language: "es",
        startDate: d,
        format: "dd/mm/yyyy hh:ii",
        autoclose: true
    });
    
    $('#description').maxlength({
        warningClass: "small text-muted",
        limitReachedClass: "small text-muted",
        placement: "top-right-inside"
    });  
});

</script>