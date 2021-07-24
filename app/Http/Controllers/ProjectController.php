<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ProjectRequest;
use App\User;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Supplier;
use App\Models\PhotoProject;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Location;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Carbon\Carbon;
use Image;
use Storage;
use File;
use DB;
use PDF;
use Auth;

class ProjectController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
        $this->middleware(function ($request, $next) {
            $this->condominium=session()->get('condominium');
            return $next($request);
        });    

    }    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('projects.index');
    }

    public function datatable(Request $request)
    {        
        $status_filter=$request->status_filter;

        if($status_filter!=''){
            $projects=$this->condominium->projects()->where('status', $status_filter);
        }else{
            $projects=$this->condominium->projects();
        }

        return Datatables::of($projects)
            ->addColumn('action', function ($project) {
                $project_id = Crypt::encrypt($project->id);
                    return '<div class="input-group-btn text-center">
                        <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" name="href_cancel" class="modal-class" onclick="showModalProject('.$project->id.')"><i class="fa fa-pencil-square-o"></i> Editar datos generales</a>
                            </li>
                            <li>
                                <a href="'.route('projects.detail', $project_id).'" name="href_cancel" class="modal-class"><i class="fa fa-list-ul"></i> Ir a detalle</a>
                            </li>
                            <li>
                                <a href="'.route('projects.gallery', $project->id).'" name="href_photos" class="modal-class"><i class="fa fa-picture-o"></i> Fotos</a>
                            </li>
                            <li>
                                <a href="'.route('projects.rpt_project', $project_id).'" name="href_print" class="modal-class" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="showModalDelete(`'.$project->id.'`, `'.$project->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                            </li>
                        </ul>
                    </div>';
                })           
            ->editColumn('name', function ($project) {                    
                    return '<b><a href="'.route('projects.detail', Crypt::encrypt($project->id)).'" name="href_detail" style="color:inherit"  title="Ir al detalle">'.$project->name.'</a></b><br><small>'.$project->planned->format('d.m.Y').' - '.$project->planned_end->format('d.m.Y').'</small>';
                })
            ->editColumn('description', function ($project) {                    
                    return '<small>'.$project->description.'</small>';
                })
            ->editColumn('advance', function ($project) {                    
                    return '<td class="project-completion"><small>Porcentaje: '.$project->advance.'%</small><div class="progress progress-mini"><div style="width: '.$project->advance.'%;" class="progress-bar"></div></div></td>';
                })
            ->editColumn('budget', function ($project) {                    
                    return session('coin').''.money_fmt($project->budget);
                })
            ->editColumn('cost', function ($project) {                    
                    return session('coin').''.money_fmt($project->cost);
                })
            ->editColumn('status', function ($project) {                    
                    return $project->status_label;
                })
            ->rawColumns(['action', 'name', 'description', 'planned', 'advance', 'status'])
            ->make(true);
    }
    
    public function detail($id)
    {
        $project = Project::find(Crypt::decrypt($id));

        $tot_incomes=$project->incomes()->sum('amount')+$project->payments()->sum('payment_fee.amount');
        $tot_expenses=$project->expenses()->sum('amount');
        $balance=$tot_incomes-$tot_expenses;
        
        return view('projects.detail')->with('project', $project)
                            ->with('tot_incomes', $tot_incomes)
                            ->with('tot_expenses', $tot_expenses)
                            ->with('balance', $balance);
    }
        
    /**
     * Display the specified lot.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $project = new Project();
        }else{
            $project = Project::find($id);
        }
        
        return view('projects.save')->with('project', $project);
    }
    
    /**
     * Display the specified location.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function gallery($project_id)
    {
        $project = Project::find($project_id);
        
        return view('projects.gallery')->with('project', $project);
    }
    
    public function load_photos($project_id)
    {
        $project = Project::find($project_id);        
        $photos=$project->photos()->orderBy('stage')->get();
        
        return view('projects.photos')->with('photos', $photos);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectRequest $request)
    {
        try {
            $project = new Project();
            $project->condominium_id=$request->condominium_id;
            $project->name= $request->name;
            $project->description= $request->description;
            $project->planned=Carbon::createFromFormat('d/m/Y', $request->planned);
            $project->planned_end=Carbon::createFromFormat('d/m/Y', $request->planned_end);
            ($request->budget)?$project->budget=$request->budget:'';
            $project->status= 'P';
            $project->created_by= Auth::user()->name;
            $project->save();
            $project->hash=hash('ripemd128', $project->id);
            
            return response()->json([
                    'success' => true,
                    'message' => 'Proyecto registrado exitosamente',
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRequest $request, $id)
    {
        try {
            $project = Project::find($id);
            $project->name= $request->name;
            $project->description= $request->description;
            $project->planned=Carbon::createFromFormat('d/m/Y', $request->planned);
            $project->planned_end=Carbon::createFromFormat('d/m/Y', $request->planned_end);
            ($request->budget)?$project->budget=$request->budget:'';
            $project->status= 'P';
            $project->created_by= Auth::user()->name;
            $project->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Proyecto registrado exitosamente',
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
    /**
     * Remove the specified project from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $project = Project::find($id);
            //se eliminan las fotos asociadas
            if($project->photos()->count()>0){
                foreach($project->photos()->get() as $photo){
                    Storage::delete($project->condominium_id.'/projects/'.$photo->file);
                    Storage::delete($project->condominium_id.'/projects/thumbs/'.$photo->file);
                }
            }
            //se eliminan los documentos asociados
            if($project->documents()->count()>0){
                foreach($project->documents()->get() as $document){
                    Storage::delete($project->condominium_id.'/projects/'.$document->file);
                    Storage::delete($project->condominium_id.'/projects/thumbs/'.$document->file);
                }
            }
            $project->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Proyecto eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function rpt_projects()
    {        
        $setting = Setting::first();
        
        $company=Company::find(Auth::user()->company_id);
        $projects=$company->projects()->orderBy('created_at', 'desc')->get();
        
        $data=[
            'setting' => $setting,
            'projects' => $projects,
            'company_name' => $company->name,
            'logo' => 'data:image/png;base64, '.$setting->logo
        ];
        $pdf = PDF::loadView('reports/rpt_projects', $data);
        
        return $pdf->stream('Activos.pdf');

    }

    public function rpt_project($id)
    {        
        $setting = Setting::first();
        
        $project = Project::find(Crypt::decrypt($id));
        $photos=$project->photos()->orderBy('stage')->get();
        
        $data=[
            'setting' => $setting,
            'project' => $project,
            'photos' => $photos,
            'root' => realpath(storage_path()),
            'logo' => 'data:image/png;base64, '.$setting->logo
        ];
        $pdf = PDF::loadView('reports/rpt_project', $data);
        
        return $pdf->stream($project->name.'.pdf');

    }

    public function load_btn_status($id)
    {
        $project=Project::find($id);        
        return view('projects.btn_status')->with('project', $project);
    }

    /**
     * Update the status to specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        try {
            $project=Project::find($id);
            $project->status=$request->status;
            ($project->status=='E' && $project->started==null)?$project->started=Carbon::now():'';

            $project->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Estado cambiado existosamente'
                ], 200);
                        
        } catch (Exception $e) {
            //
        }

    }

    public function load_progress($id)
    {
        $project = Project::find($id);        
        return view('projects.progress')->with('project', $project);
    }

    public function load_expenses($id)
    {
        $project = Project::find($id);
        $expenses=$project->expenses()->get();        
        return view('projects.expenses')->with('expenses', $expenses);
    }

    public function load_incomes($id)
    {
        $project = Project::find($id);
        $incomes=$project->incomes()->select('id', 'date', 'concept', 'amount', 'file', 'file_type')->get();
        
        foreach($incomes as $income){
            $income['url']=$income->download_file;
        } 

        $payments=$project->payments()->get();

        foreach($payments as $payment){
            $payment['url']=$payment->download_file;
        } 

        $incomes=$incomes->concat($payments)->sortBy('date');

        return view('projects.incomes')->with('incomes', $incomes);
    }

}
