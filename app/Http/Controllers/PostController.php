<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\PostRequest;
use App\Models\Project;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Company;
use App\Models\Condominium;
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

class PostController extends Controller
{
               
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
        $this->middleware(function ($request, $next) {
            $this->condominium=session()->get('condominium');
            return $next($request);
        });    

    }    
    
    public function index()
    {
        $posts=$this->condominium->posts()->orderBy('id', 'desc')->get();
        return view('posts.index')->with('posts', $posts);
    }

    public function load($id)
    {
    }
    
    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $post = new Post();
            $post->condominium_id=$request->condominium_id;
            $post->user_id=$request->user_id;
            $post->comment=$request->comment;
            $post->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Mensaje posteado exitosamente',
                ], 200);

            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);            
        }
    }

    /**
     * Update the specified post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $post = Post::find($id);
            $post->delete();

            return response()->json([
                    'success' => true,
                    'message' => 'Mensaje eliminado exitosamente',
                ], 200);

        } catch (Exception $e) {
            
        }
    }
}
