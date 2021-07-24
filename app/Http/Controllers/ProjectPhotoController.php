<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ProjectPhotoRequest;
use App\Models\Project;
use App\Models\ProjectPhoto;
use App\Models\Setting;
use App\Models\Customer;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
//Image
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Storage;

class ProjectPhotoController extends Controller
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
     * Display a listing of the project_photo.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {                
        $project_photo = ProjectPhoto::findOrFail($id);
        $picture = Image::make(Storage::get($project_photo->project->center_id.'/projects/'.$project_photo->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function thumbnail($id)
    {                
        $project_photo = ProjectPhoto::findOrFail($id);
        $picture = Image::make(Storage::get($project_photo->project->center_id.'/projects/thumbs/'.$project_photo->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function load($id)
    {
        $project = Project::find($id);
        $photos=$project->photos()->get();

        return view('projects.photos')->with('photos', $photos);
    }
    
    /**
     * Store a newly created project_photo in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $project_photo = new ProjectPhoto();        
            $project_photo->project_id=$request->hdd_project_id;
            $project_photo->title=$request->title;
            $project_photo->stage=$request->stage;
            $file = Input::file('photo');        
            $project_photo->file_name = $file->getClientOriginalName();
            $project_photo->file_type = $file->getClientOriginalExtension();
            $project_photo->file_size = $file->getSize();
            $project_photo->file=$this->upload_file($this->center->id.'/projects/', $file);
            $project_photo->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Foto registrada exitosamente',
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified project_photo in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $project_photo = ProjectPhoto::find($id);
            $project_photo->title=$request->title;
            $project_photo->stage=$request->stage;
            $project_photo->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Foto actualizada exitosamente',
                    'project_photo' => $project_photo
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified project_photo from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $project_photo = ProjectPhoto::find($id);
            Storage::delete($this->center->id.'/projects/thumbs/'.$project_photo->file);
            Storage::delete($this->center->id.'/projects/'.$project_photo->file);        
            $project_photo->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
