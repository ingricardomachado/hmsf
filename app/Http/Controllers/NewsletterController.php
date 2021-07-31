<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\NewsletterRequest;
use App\Models\Condominium;
use App\Models\Newsletter;
use App\Models\Property;
use App\Models\Setting;
use App\User;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
//Export
use Storage;
use Carbon\Carbon;
use App\Exports\PropertiesExport;
use Image;
use File;
use DB;
use PDF;
use Auth;

class NewsletterController extends Controller
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
     * Display a listing of the newsletter.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');                
        $users=$this->condominium->users()->where('role', 'WAM')->orderBy('name')->pluck('name','id');
        return view('newsletters.index')->with('start', $start->format('d/m/Y'))
                        ->with('end', $end->format('d/m/Y'))
                        ->with('users', $users);
    }

    public function datatable(Request $request)
    {        
        $newsletters=$this->get_newsletters_collection($request);        
        
        return Datatables::of($newsletters)
            ->addColumn('action', function ($newsletter) {
                $newsletter_id = Crypt::encrypt($newsletter->id);
                $url_edit = route('newsletters.edit', $newsletter_id);
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalNewsletter('.$newsletter->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$newsletter->id.'`, `'.$newsletter->title.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                })           
            ->addColumn('user', function ($newsletter) {                    
                    return $newsletter->user->name.'<br>'.$newsletter->user->cell;
                })
            ->editColumn('title', function ($newsletter) {                    
                    return '<a href="#"  onclick="showModalNewsletter('.$newsletter->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><span class="text-muted"><small>'.Carbon::parse($newsletter->date)->isoFormat('LLLL').'</small></span><br><b>'.$newsletter->title.'</b><br><small>'.nl2br($newsletter->description).'</small></a>';
                })
            ->editColumn('level', function ($newsletter) {                    
                    return $newsletter->level_label;
                })
            ->addColumn('file', function ($newsletter) {
                    if($newsletter->file_name){                    
                        $ext=$newsletter->file_type;
                        if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='bmp'){
                            $url_show_file = url('newsletter_image', $newsletter->id);
                            return '<div class="text-center"><a class="popup-link" href="'.$url_show_file.'" title="'.$newsletter->file_name.'"><i class="fa fa-picture-o"></i></a></div>';
                        }else{
                            $url_download_file = route('newsletters.download', $newsletter->id);
                            return '<div class="text-center"><a href="'.$url_download_file.'" title="'.$newsletter->file_name.'"><i class="fa fa-cloud-download"></i></a></div>';
                        }
                    }
                })
            ->rawColumns(['action', 'title', 'user', 'file', 'level'])
            ->make(true);
    }
    
    /**
     * Display the specified newsletter.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $today=Carbon::now();
        if($id==0){
            $newsletter = new Newsletter();
        }else{
            $newsletter = Newsletter::find($id);
        }
        
        return view('newsletters.save')->with('newsletter', $newsletter)
                        ->with('today', $today);
    }

    /**
     * Store a newly created newsletter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsletterRequest $request)
    {
        try {
            $user=User::findOrFail($request->user_id);
            $newsletter = new Newsletter();
            $newsletter->condominium_id=$user->condominium_id;
            $newsletter->user_id=$user->id;
            $newsletter->date=Carbon::createFromFormat('d/m/Y H:i', $request->date);
            $newsletter->level=$request->level;
            $newsletter->title=$request->title;
            $newsletter->description=$request->description;
            $newsletter->created_by=$user->name;
            $file = $request->file;
            if (File::exists($file)){
                $newsletter->file_name = $file->getClientOriginalName();
                $newsletter->file_type = $file->getClientOriginalExtension();
                $newsletter->file_size = $file->getSize();
                $newsletter->file=$this->upload_file($newsletter->condominium_id.'/newsletters/', $file);
            }
            $newsletter->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Novedad registrada exitosamente',
                    'newsletter' => $newsletter->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified newsletter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewsletterRequest $request, $id)
    {
        try {
            $newsletter = Newsletter::find($id);
            $newsletter->date=Carbon::createFromFormat('d/m/Y H:i', $request->date);
            $newsletter->level=$request->level;
            $newsletter->title=$request->title;
            $newsletter->description=$request->description;
            $file = $request->file;
            if (File::exists($file)){
                if($newsletter->file){
                    Storage::delete($newsletter->condominium_id.'/newsletters/'.$newsletter->file);
                    Storage::delete($newsletter->condominium_id.'/newsletters/thumbs/'.$newsletter->file);
                }
                $newsletter->file_name = $file->getClientOriginalName();
                $newsletter->file_type = $file->getClientOriginalExtension();
                $newsletter->file_size = $file->getSize();
                $newsletter->file=$this->upload_file($newsletter->condominium_id.'/newsletters/', $file);
            }
            $newsletter->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Novedad actualizada exitosamente',
                    'newsletter' => $newsletter
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified newsletter from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $newsletter = Newsletter::find($id);
            if($newsletter->file){
                Storage::delete($newsletter->condominium_id.'/newsletters/'.$newsletter->file);
                Storage::delete($newsletter->condominium_id.'/newsletters/thumbs/'.$newsletter->file);
            }
            $newsletter->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Novedad eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /*
     * Download file from DB  
    */ 
    public function download_file($id)
    {
        $newsletter = Newsletter::find($id);
        
        return Storage::download($newsletter->condominium_id.'/newsletters/'.$newsletter->file, $newsletter->file_name);
    }


    public function get_newsletters_collection(Request $request){
        
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        $user_filter=$request->user_filter;
        $level_filter=$request->level_filter;

        if($user_filter!=''){
            if($level_filter!=''){
                $newsletters = $this->condominium->newsletters()
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('user_id', $user_filter)
                            ->where('level', $level_filter);
            }else{
                $newsletters = $this->condominium->newsletters()
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('user_id', $user_filter);
            }
        }else{
            if($level_filter!=''){
                $newsletters = $this->condominium->newsletters()
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('level', $level_filter);
            }else{
                $newsletters = $this->condominium->newsletters()
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter);
            }
        }
        return $newsletters;
    }

    public function rpt_newsletters(Request $request){
        
        $logo=($this->condominium->logo)?'data:image/png;base64, '.base64_encode(Storage::get($this->condominium->id.'/'.$this->condominium->logo)):'';
        $company=$this->condominium->name;
        
        $newsletters=$this->get_newsletters_collection($request)->get();

        if($request->level_filter!=''){
            switch ($request->level_filter) {
                case '1':
                    $level_name='Alto';
                    break;
                case '2':
                    $level_name='Medio';
                    break;
                case '3':
                    $level_name='Bajo';
                    break;
            }
        }else{
            $level_name='Todos';
        }

        if($request->user_filter!=''){
            $user=User::find($request->user_filter);
            $user_name=$user->name;
        }else{
            $user_name='Todos';
        }

        $data=[
            'company' => $this->condominium->name,
            'logo' => $logo,            
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'user_name' => $user_name,
            'level_name' => $level_name,
            'newsletters' => $newsletters            
        ];

        $pdf = PDF::loadView('reports/rpt_newsletters', $data);
        
        return $pdf->stream('Visitas.pdf');        
    }
}
