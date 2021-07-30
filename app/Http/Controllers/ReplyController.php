<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ReplyRequest;
use App\Models\Project;
use App\Models\Reply;
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

class ReplyController extends Controller
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
        //
    }

    public function load($id)
    {
    }
    
    /**
     * Store a newly created reply in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $reply = new Reply();
            $reply->post_id=$request->post_id;
            $reply->user_id=$request->user_id;
            $reply->comment=$request->comment;
            $reply->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Respuesta registrada exitosamente',
                ], 200);

            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);            
        }
    }

    /**
     * Remove the specified reply from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $reply = Reply::find($id);
            $reply->delete();

            return response()->json([
                    'success' => true,
                    'message' => 'Respuesta eliminada exitosamente',
                ], 200);

        } catch (Exception $e) {
            
        }
    }
}
