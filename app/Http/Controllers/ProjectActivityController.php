<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Carbon\Carbon;
use Image;
use File;
use DB;
use PDF;
use Auth;

class ProjectActivityController extends Controller
{
               
    public function index($id)
    {
        $project = Project::find($id);
        $activities=$project->activities()->get();
        
        return view('project_activities.index')
                                ->with('project', $project)
                                ->with('activities', $activities);
    }

    public function load($id, $activity_id)
    {
        $project=Project::find($id);

        if($activity_id==0){
            $activity = new ProjectActivity();
        }else{
            $activity = ProjectActivity::find($activity_id);
        }
        
        return view('project_activities.save')->with('project', $project)
                                ->with('activity', $activity);
    }
    
    /**
     * Store a newly created activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $activity = new ProjectActivity();
            $activity->project_id=$request->project_id;
            $activity->name= $request->name;
            $activity->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $activity->observation=$request->observation;
            $activity->advance=$request->advance;
            $activity->save();
            //Actualizo el % del Proyecto
            $project=Project::find($request->project_id);
            $project->advance=$project->activities()->sum('advance');
            $project->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Actividad registrada exitosamente',
                    'total_activities' => $project->activities()->count(),
                ], 200);

            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);            
        }        
    }

    /**
     * Update the specified activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $activity = ProjectActivity::find($id);
            $activity->name= $request->name;
            $activity->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $activity->observation=$request->observation;
            $activity->advance=$request->advance;
            $activity->save();
            //Actualizo el % del Proyecto
            $project=Project::find($request->project_id);
            $project->advance=$project->activities()->sum('advance');
            $project->save();        

            return response()->json([
                    'success' => true,
                    'message' => 'Actividad actualizada exitosamente'
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $activity = ProjectActivity::find($id);
            $project=Project::find($activity->project_id);
            $activity->delete();
            //Actualizo el % del Proyecto
            $project->advance=$project->activities()->sum('advance');
            $project->save();        

            return response()->json([
                    'success' => true,
                    'message' => 'Comentario eliminado exitosamente',
                    'total_activities' => $project->activities()->count(),
                ], 200);

        } catch (Exception $e) {
            
        }
    }
}
