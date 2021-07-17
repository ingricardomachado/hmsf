<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
use Carbon\Carbon;

class EventController extends Controller
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
     * Display a listing of the event.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('events.index')->with('condominium', $this->condominium);
    }
    
    /**
     * Display the specified car.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        if($id==0){
            $event = new Event();
        }else{
            $event = Event::find($id);
        }
        
        return view('events.save')->with('event', $event);
    }
    
    /**
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        try {
            $event = new Event();
            $event->condominium_id=$request->condominium_id;
            $event->start=Carbon::createFromFormat('d/m/Y H:i', $request->start);
            $event->end=Carbon::createFromFormat('d/m/Y H:i', $request->end);            
            $event->title=$request->title;
            $event->description=$request->description;
            $event->color=$request->color;
            $event->private=($request->private)?true:false;
            $event->description=$request->description;
            $event->save();
            
            return response()->json([
                    'success' => true,
                    'message' => 'Evento registrado exitosamente',
                    'event' => $event->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, $id)
    {
        try {
            $event = Event::find($id);
            $event->start=Carbon::createFromFormat('d/m/Y H:i', $request->start);
            $event->end=Carbon::createFromFormat('d/m/Y H:i', $request->end);            
            $event->title=$request->title;
            $event->description=$request->description;
            $event->color=$request->color;
            $event->private=($request->private)?true:false;
            $event->description=$request->description;
            $event->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Evento actualizado exitosamente',
                    'event' => $event
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    public function drop(EventRequest $request, $id)
    {
        try {
            $event = Event::find($id);
            $event->start=Carbon::createFromFormat('d/m/Y H:i', $request->start);
            $event->end=Carbon::createFromFormat('d/m/Y H:i', $request->end);            
            $event->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Evento actualizado exitosamente',
                    'event' => $event
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified event from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $event = Event::find($id);
            $event->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Evento eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function rpt_events()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'events' => $this->condominium->events()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_events', $data);
        
        return $pdf->stream('Eventos.pdf');

    }
}
