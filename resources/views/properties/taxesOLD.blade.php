@extends('layouts.app')

@push('stylesheets')
@endpush

@section('page-header')
@endsection

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox float-e-margins">
        
        <!-- ibox-title -->
        <div class="ibox-title">
          <h5><i class="fa fa-home" aria-hidden="true"></i> Alícuotas</h5>
            <div class="ibox-tools">
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
              <ul class="dropdown-menu dropdown-user">
                <li><a href="#">Config option 1</a></li>
                <li><a href="#">Config option 2</a></li>
              </ul>
              <a class="close-link"><i class="fa fa-times"></i></a>
            </div>
        </div>
        <!-- /ibox-title -->
                    
        <!-- ibox-content- -->
        <div class="ibox-content">
          <div class="row">
            {{ Form::open(array('url' => '', 'id' => 'form_rpt', 'method' => 'get'), ['' ])}}
            {{ Form::close() }}
            <div class="col-sm-3 col-xs-12">
            </div>
            <div class="col-sm-9 col-xs-12 text-right">
            </div>
                                                
            <div class="col-sm-12">
                  <div class="col-sm-2">
                    <b>Propiedad</b> 
                  </div>
                  <div class="col-sm-3">
                    <b>Propietario</b>
                  </div>
                  <div class="col-sm-7">
                    <b>Alicutota</b>
                  </div>
              <div class="col-sm-12" id="scroll-info">
                @foreach($properties as $property)
                      <div class="col-sm-2" style="padding-top: 2mm">
                        <b>{{ $property->number }}</b> 
                      </div>
                      <div class="col-sm-3" style="padding-top: 2mm">
                        {{ ($property->user_id)?$property->user->name:'' }}
                      </div>
                      <div class="col-sm-7" style="padding-top: 1mm">
                        <input type="text" class="form-control decimal" value="{{ $property->tax }}" style="width: 100px" />
                      </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <!-- /ibox-content- -->

      </div>
    </div>
  </div>
</div>  
@endsection

@push('scripts')
<script>

$(document).ready(function(){
                      
});

</script>
@endpush