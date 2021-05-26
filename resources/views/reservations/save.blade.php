<!-- iCheck -->
<link href="{{ URL::asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
<!-- DatePicker -->
<link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
<!-- Esta instruccion es para que Select2 funcione dentro del Modal-->
<style type="text/css">
  .select2-dropdown{
    z-index: 3051;
} 
</style>
    
    <form action="#" id="form_reservation" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />        
        {!! Form::hidden('reservation_id', ($reservation->id)?$reservation->id:0, ['id'=>'reservation_id']) !!}
        @if($reservation->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title"><i class="fa fa-calendar-o" aria-hidden="true"></i> {{ ($reservation->id) ? "Modificar Reservación": "Reservar" }}</h5><small>Complete el formulario <b>(*) Campos obligatorios.</b></small>
        </div>
        <div class="modal-body">
            <div class="row">            
                    <div class="form-group col-sm-12">
                        <div class="i-checks">
                            {!! Form::checkbox('all_day', null, false, ['id'=>'all_day', 'class'=>'i-checks']) !!} <label>Todo el día</label>
                        </div>
                    </div>
                
                <div class="row">                                        
                    <div class="col-sm-12">
                        <div class="form-group col-sm-6">  
                          <label>Propiedad *</label>
                          {{ Form::select('property', $properties, $reservation->property_id, ['id'=>'property', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Fecha de la reservación</label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {{ Form::text ('date', null, ['id'=>'date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div id="div_all_day" style="display: solid">
                        <div class="form-group col-sm-6">  
                          <label>Desde *</label>
                          {{ Form::select('start', ['1'=>'01:00 am', '2'=>'02:00 am', '3'=>'03:00 am', '4'=>'04:00 am', '5'=>'05:00 am', '6'=>'06:00 am', '7'=>'07:00 am', '8'=>'08:00 am', '9'=>'09:00 am', '10'=>'10:00 am', '11'=>'11:00 am', '12'=>'12:00 pm', '13'=>'01:00 pm', '14'=>'02:00 pm', '15'=>'03:00 pm', '16'=>'04:00 pm', '17'=>'05:00 pm', '18'=>'06:00 pm', '19'=>'07:00 pm', '20'=>'08:00 pm', '21'=>'09:00 pm', '22'=>'10:00 pm', '23'=>'11:00 pm', '24'=>'12:00 am'], '08:00', ['id'=>'start', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>                
                        <div class="form-group col-sm-6">  
                          <label>Hasta *</label>
                          {{ Form::select('end', ['1'=>'01:00 am', '2'=>'02:00 am', '3'=>'03:00 am', '4'=>'04:00 am', '5'=>'05:00 am', '6'=>'06:00 am', '7'=>'07:00 am', '8'=>'08:00 am', '9'=>'09:00 am', '10'=>'10:00 am', '11'=>'11:00 am', '12'=>'12:00 pm', '13'=>'01:00 pm', '14'=>'02:00 pm', '15'=>'03:00 pm', '16'=>'04:00 pm', '17'=>'05:00 pm', '18'=>'06:00 pm', '19'=>'07:00 pm', '20'=>'08:00 pm', '21'=>'09:00 pm', '22'=>'10:00 pm', '23'=>'11:00 pm', '24'=>'12:00 am'], '16:00', ['id'=>'end', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                </div>
                @if($facility->rent)    
                    <div class="form-group col-sm-6">
                        <label>Fecha limite de pago</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            {{ Form::text ('due_date', null, ['id'=>'due_date', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="reservation_CRUD({{ ($reservation->id)?$reservation->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- DatePicker --> 
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<script>

$('#all_day').on('ifChanged', function(event){
  (event.target.checked)?$('#div_all_day').hide():$('#div_all_day').show();  
});

function getLastDateOfMonth(Year,Month){
    var last_day_month = new Date((new Date(Year, Month,1))-1);
    var day = last_day_month.getDate();
    var month = last_day_month.getMonth()+1;
    if (month < 10){
      month='0'+month;
    }
    var year = last_day_month.getFullYear();
    return(day+'/'+month+'/'+year);
}

$(document).ready(function() {
    $('#name').focus();

    // iCheck
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    //Datepicker 
    var d = new Date();
    d.setHours(0,0,0,0);    
    $("#date").datepicker({
        startDate: d,
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
        language: "es"
    }).on("changeDate", function (e) {
        var date = $(this).datepicker('getDate');
        $('#due_date').datepicker('setStartDate', date);
        $("#due_date").datepicker("setDate", getLastDateOfMonth(date.getFullYear(), date.getMonth() + 1));
    });
    
    $('#due_date').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
        language: 'es',
    })

    $("#property").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalReservation .modal-content'),
        width: '100%'
    });

    $("#start").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalReservation .modal-content'),
        width: '100%'
    });

    $("#end").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        allowClear: false,
        dropdownParent: $('#modalReservation .modal-content'),
        width: '100%'
    });

});

</script>