<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\Property;
use App\Models\Setting;
use App\Models\Facility;
use App\Models\Fee;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ImgController;
//Export
use App\Exports\PropertiesExport;
use Carbon\Carbon;
use Image;
use File;
use DB;
use PDF;
use Auth;
use Mail;
use App\Mail\ReservationConfirm;


class ReservationController extends Controller
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
     * Display a listing of the reservation.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                        
        $facilities=$this->condominium->facilities()->where('status', 'O')
                                ->orderBy('name')->pluck('name','id');
                                
        if(session('role')=='OWN'){
            $properties=Auth::user()->properties()->orderBy('number')->pluck('number','id');
        }else{
            $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        }
        
        return view('reservations.index')->with('properties', $properties)
                                        ->with('facilities', $facilities);
    }

    public function datatable(Request $request)
    {        
        $property_filter=$request->property_filter;
        $status_filter=$request->status_filter;

        if($property_filter!=''){
            if($status_filter!=''){
                $reservations = $this->condominium->reservations()
                            ->where('property_id', $property_filter)
                            ->where('status', $status_filter);
            }else{
                $reservations = $this->condominium->reservations()
                            ->where('property_id', $property_filter);
            }
        }else{
            if(session('role')=='OWN'){
                if($status_filter!=''){
                    $reservations = $this->condominium->reservations()
                            ->whereIn('property_id', Auth::user()->properties()->pluck('id'))
                            ->where('status', $status_filter);
                }else{
                    $reservations = $this->condominium->reservations()
                            ->whereIn('property_id', Auth::user()->properties()->pluck('id'));
                }
            }else{
                if($status_filter!=''){
                    $reservations = $this->condominium->reservations()
                            ->where('status', $status_filter);
                }else{
                    $reservations = $this->condominium->reservations();
                }
            }
        }
                
        return Datatables::of($reservations)
            ->addColumn('action', function ($reservation) {
                $opt_confirm=(session('role')=='ADM')?
                    '<li>
                        <a href="#" class="modal-class" onclick="showModalConfirmReservation('.$reservation->id.')"><i class="fa fa-check-square-o"></i> Confirmar</a>
                    </li><li class="divider"></li>':'';
                    if($reservation->status=='P'){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                '.$opt_confirm.'
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$reservation->id.'`, `'.$reservation->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return "";
                    }    
                })           
            ->addColumn('facility', function ($reservation) {
                    if($reservation->all_day){
                        $label='<b>'.$reservation->facility->name.'</b><br><small>'.day_letter($reservation->start->dayOfWeek, 'lg').' '.$reservation->start->format('d.m.Y').'<br> Todo el día</small>';
                    }else{
                        $label='<b>'.$reservation->facility->name.'</b><br><small>'.day_letter($reservation->start->dayOfWeek, 'lg').' '.$reservation->start->format('d.m.Y').'<br>De '.$reservation->start->format('g:i a').' a '.$reservation->end->format('g:i a').'</small>';
                    }
                    if($reservation->status=='P'){
                        return '<a href="#" onclick="showModalConfirmReservation('.$reservation->id.')" class="modal-class" style="color:inherit"  title="Click para editar">'.$label.'</a>';                        
                    }else{
                        return $label;
                    }
                })
            ->addColumn('property', function ($reservation) {                    
                    if($reservation->property->user_id){
                        return $reservation->property->number.'<br><small>'.$reservation->property->user->name.'<br>'.$reservation->property->user->cell.'<small>';
                    }else{
                        return $reservation->property->number;
                    }
                })
            ->editColumn('notes', function ($reservation) {                    
                    return '<small>'.$reservation->notes.'</small>';
                })
            ->editColumn('observations', function ($reservation) {                    
                    return '<small>'.$reservation->observations.'</small>';
                })
            ->editColumn('cost', function ($reservation) {                    
                    return ($reservation->amount)?session('coin').$reservation->amount:'';
                })

            ->editColumn('status', function ($reservation) {                    
                    return $reservation->status_label;
                })
            ->rawColumns(['action', 'facility', 'property', 'notes', 'observations', 'cost', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified reservation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id, $facility_id)
    {
        if(session('role')=='OWN'){
            $properties=Auth::user()->properties()->orderBy('number')->pluck('number','id');
        }else{
            $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        }
        
        $facility=Facility::find($facility_id);
        
        if($id==0){
            $reservation = new Reservation();
        }else{
            $reservation = Reservation::find($id);
        }
        
        return view('reservations.save')->with('reservation', $reservation)
                                    ->with('facility', $facility)
                                    ->with('properties', $properties);
    }

    public function load_confirm($id)
    {
        $reservation=Reservation::find($id);
        
        return view('reservations.confirm')->with('reservation', $reservation);
    }

    /**
     * Store a newly created reservation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReservationRequest $request)
    {
        try {
            $facility=Facility::find($request->facility_id);
            $reservation = new Reservation();
            $reservation->condominium_id = $facility->condominium_id;
            $reservation->property_id=$request->property;
            $reservation->facility_id=$facility->id;
            $reservation->title= 'Reservación '.$facility->name;
            $reservation->notes= $request->notes;
            $reservation->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $reservation->rent=$facility->rent;
            $reservation->all_day=($request->all_day)?true:false;
            $reservation->start=($reservation->all_day)?Carbon::createFromFormat('d/m/Y', $request->date):Carbon::createFromFormat('d/m/Y H', $request->date.' '.$request->start);
            $reservation->end=($reservation->all_day)?null:Carbon::createFromFormat('d/m/Y H', $request->date.' '.$request->end);
            if($reservation->rent){
                if($reservation->all_day){
                    $reservation->day_cost= $facility->day_cost;
                    $reservation->amount=$reservation->day_cost;
                }else{
                    $reservation->hour_cost=$facility->hour_cost;
                    $reservation->tot_hours=$reservation->end->floatDiffInHours($reservation->start);
                    $reservation->amount=$reservation->tot_hours*$reservation->hour_cost;
                }
            }
            $reservation->status= 'P';
            $reservation->save();            
            
            return response()->json([
                    'success' => true,
                    'message' => 'Reservación registrada exitosamente',
                    'reservation' => $reservation->toArray()
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }
    
   /**
     * Update the specified reservation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request, $id)
    {
        try {
            $reservation = Reservation::find($id);
            if($request->resp=='A'){
                //se registra la cuota de alquiler
                $fee = new Fee();
                $fee->created_by=Auth::user()->name;
                $fee->condominium_id=$reservation->condominium_id;
                $fee->income_type_id=9; //9=Alquiler
                $fee->property_id=$reservation->property_id;
                $fee->date=$reservation->start;
                $fee->due_date=Carbon::createFromFormat('d/m/Y', $request->due_date);
                $fee->concept=$request->concept;
                $fee->amount=$reservation->amount;
                $fee->balance=$fee->amount;
                $fee->save();
                //se actualiza la reservacion y se le asigna la cuota
                $reservation->fee_id=$fee->id;
                $reservation->status='A';
                $reservation->observations=$request->observations;
                $reservation->save();
            }else{
                $reservation->status='R';
                $reservation->observations=$request->observations;
                $reservation->save();
            }
            //se envia la notificacion al propietario            
            ($reservation->property->user_id)?Mail::to($reservation->property->user->email)->send(new ReservationConfirm($reservation)):'';


            return response()->json([
                    'success' => true,
                    'message' => 'Reservación confirmada exitosamente',
                    'reservation' => $reservation->toArray()
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified reservation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $reservation = Reservation::find($id);
            $reservation->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Reservación eliminada exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified facility.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reserve($id)
    {        
        $facility = Facility::find($id);
        
        return view('reservations.reserve')->with('facility', $facility);
    }    
}
