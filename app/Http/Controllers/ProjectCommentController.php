<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ProjectCommentRequest;
use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\Setting;
use App\Models\Company;
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

class ProjectCommentController extends Controller
{
           
    public function index($id)
    {
        $project = Project::find($id);
        if(session()->get('role')=='ADM'){
            $comments=$project->comments()->get();
        }elseif(session()->get('role')=='CLI'){
            $comments=$project->comments()->where('public', true)->get();            
        }
        
        return view('project_comments.index')
                                ->with('project', $project)
                                ->with('comments', $comments);
    }

    public function load($id, $comment_id)
    {
        $project=Project::find($id);

        if($comment_id==0){
            $comment = new ProjectComment();
        }else{
            $comment = ProjectComment::find($comment_id);
        }
        
        return view('project_comments.save')->with('project', $project)
                                    ->with('comment', $comment);
    }
    
    /**
     * Store a newly created comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $project=Project::find($request->project_id);
            $comment = new ProjectComment();
            $comment->project_id=$project->id;
            $comment->comment= $request->comment;
            $comment->public=(Auth::user()->role=='CLI')?1:$request->visibility;
            $comment->user_id=Auth::user()->id;        
            $comment->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Comentario registrado exitosamente',
                    'total_comments' => $project->comments()->count(),
                ], 200);

            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);            
        }
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $comment = ProjectComment::find($id);
            $comment->comment= $request->comment;
            $comment->public=(Auth::user()->role=='CLI')?1:$request->visibility;
            $comment->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Comentario actualizado exitosamente',
                    'comment' => $comment
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
            $comment = ProjectComment::find($id);
            $project=Project::find($comment->project_id);
            $comment->delete();

            return response()->json([
                    'success' => true,
                    'message' => 'Comentario eliminado exitosamente',
                    'total_comments' => $project->comments()->count(),
                ], 200);

        } catch (Exception $e) {
            
        }
    }
}
