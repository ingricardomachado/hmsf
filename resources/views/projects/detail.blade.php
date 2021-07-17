@extends('layouts.app')

@push('stylesheets')
@endpush

@section('page-header')

@endsection

@section('content')
<div class="row">
    <div class="col-lg-9">
        <div class="wrapper wrapper-content animated fadeInUp">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="m-b-md">
                                <h3><i class="fa fa-wrench" aria-hidden="true"></i> {{ $project->name }}</h3>
                                <div class="form-group pull-right">
                                    <a href="{{ route('projects.gallery', $project->id) }}" class="btn btn-sm btn-default" title="Galeria de Fotos"><i class="fa fa-picture-o"></i></a>
                                    <a href="{{ route('projects.rpt_project', Crypt::encrypt($project->id)) }}" target="_blank" class="btn btn-sm btn-default" title="Imprimir detalle"><i class="fa fa-print"></i></a>
                                    <a href="{{URL::to('projects')}}" class="btn btn-sm btn-default">Regresar</a>
                                </div>

                            </div>
                            <dl class="dl-horizontal">
                              <span id="project_btn_status"></span>
                            </dl>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <dl class="dl-horizontal">
                                <dt>Creado por:</dt> <dd>{{ $project->created_by }}</dd>
                            </dl>
                        </div>
                        <div class="col-lg-6" id="cluster_info">
                            <dl class="dl-horizontal" >
                                <dt>Inicio estimado:</dt> <dd>{{ $project->planned->format('d.m.Y') }}</dd>
                                <dt>Fin estimado:</dt> <dd>{{ $project->planned_end->format('d.m.Y') }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <dl class="dl-horizontal">
                                <dt>% de Ejecución:</dt>
                                <dd>
                                    <div id="progress"></div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="row m-t-sm">
                        <div class="col-lg-12">
                        <div class="panel blank-panel">
                        <div class="panel-heading">
                            <div class="panel-options">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab-1" data-toggle="tab">Actividades</a></li>
                                    <li><a href="#tab-2" data-toggle="tab">Cotizaciones</a></li>
                                    @if(Auth::user()->role!='OPE')
                                      <li class=""><a href="#tab-3" data-toggle="tab">Facturas</a></li>
                                      <li class=""><a href="#tab-4" data-toggle="tab">Pagos</a></li>
                                    @endif
                                    <li class=""><a href="#tab-5" data-toggle="tab">Comentarios</a></li>
                                </ul>
                            </div>
                        </div>

                            <div class="panel-body">
                              <div class="tab-content">
                                <div class="tab-pane active" id="tab-1">
                                    <div id="activities"></div>
                                    <br><br>
                                </div>
                                <div class="tab-pane" id="tab-2">
                                    <div id="budgets"></div>
                                </div>                                
                                <div class="tab-pane" id="tab-3">
                                    <div id="invoices"></div>
                                </div>
                                <div class="tab-pane" id="tab-4">
                                    <div id="payments"></div>
                                </div>
                                <div class="tab-pane" id="tab-5">
                                    <div id="comments"></div>
                                </div>
                              </div>
                            </div>
                            
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="wrapper wrapper-content project-manager">
            <h4>Descripción del Proyecto</h4>
            @if($project->photos()->where('stage', 3)->count()>0)
                @php($photo=$project->photos()->where('stage', 3)->first())
                <img src="{{ url('project_photo/'.$photo->id) }}" class="img-responsive thumbnail" style='max-height:150px;max-width:200px'>
            @endif
            <p class="small">{{ $project->description }}</p>
            <p class="small font-bold">
                <span><i class="fa fa-circle text-warning"></i> High priority</span>
            </p>
            <h5>Archivos asociados al proyecto</h5>
            <div id="documents"></div>            
        </div>
    </div>
</div>

<!-- Modal para mas finalizar proyecto -->
<div class="modal inmodal" id="modalFinish" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content animated fadeIn">
      <div id="finish"></div>
    </div>
  </div>
</div>
<!-- /Modal para finalizar proyecto -->

@endsection
@push('scripts')
<script>
    
var id={{ $project->id }};

function showModalProject(){
  url = '{{URL::to("projects.load")}}/'+id;
  $('#project').load(url);  
  $("#modalProject").modal("show");
}

function showModalFinish(){
  url = '{{URL::to("projects.load_finish")}}/'+id;
  $('#finish').load(url);  
  $("#modalFinish").modal("show");
}

function project_btn_status(){
  url = '{{URL::to("projects.load_btn_status")}}/'+id;
  $('#project_btn_status').load(url);  
}

function project_progress(){
  url = '{{URL::to("projects.load_progress")}}/'+id;
  $('#progress').load(url);  
}

function project_activities(){
  url = '{{URL::to("project_activities.index")}}/'+id;
  $('#activities').load(url);  
}

function project_comments(){
  url = '{{URL::to("project_comments.index")}}/'+id;
  $('#comments').load(url);  
}

function project_payments(){
  url = '{{URL::to("payments.project_payments")}}/'+id;
  $('#payments').load(url);  
}

function project_documents(){
  url = '{{URL::to("documents.project_documents")}}/'+id;
  $('#documents').load(url);  
}

$(document).ready(function(){
  project_btn_status();
  project_progress();
  project_activities();
  //project_payments();
  project_comments();
  //project_documents();
});

</script>
@endpush