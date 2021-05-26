<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\ReservationCategory;
use App\Models\Property;
use App\Models\Setting;
use App\Models\Facility;
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
        $facilities=$this->condominium->facilities()->orderBy('name')->pluck('name','id');
        
        return view('reservations.index')->with('facilities', $facilities);
    }

    public function datatable(Request $request)
    {        
        $status_filter=$request->status_filter;

        if($status_filter!=''){
            $reservations = $this->condominium->reservations()->where('status', $status_filter);
        }else{
            $reservations = $this->condominium->reservations();
        }
                
        return Datatables::of($reservations)
            ->addColumn('action', function ($reservation) {
                $reservation_id = Crypt::encrypt($reservation->id);
                $url_edit = route('reservations.edit', $reservation_id);
                    if($reservation->active){
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_cancel" class="modal-class" onclick="showModalReservation('.$reservation->id.')"><i class="fa fa-pencil-square-o"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$reservation->id.')"><i class="fa fa-ban"></i> Deshabilitar</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="showModalDelete(`'.$reservation->id.'`, `'.$reservation->name.'`)"><i class="fa fa-trash-o"></i> Eliminiar</a>                                
                                </li>
                            </ul>
                        </div>';
                    }else{
                        return '<div class="input-group-btn text-center">
                            <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" type="button" title="Acciones"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" name="href_status" class="modal-class" onclick="change_status('.$reservation->id.')"><i class="fa fa-check"></i> Activar</a>
                                </li>
                            </ul>
                        </div>';

                    }    
                })           
            ->addColumn('facility', function ($reservation) {                    
                    if($reservation->all_day){
                        return '<a href="#"  onclick="showModalReservation('.$reservation->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$reservation->facility->name.'</b><br><small>'.day_letter($reservation->start->dayOfWeek, 'lg').' '.$reservation->start->format('d.m.Y').'<br> Todo el día</small></a>';
                    }else{
                        return '<a href="#"  onclick="showModalReservation('.$reservation->id.')" class="modal-class" style="color:inherit"  title="Click para editar"><b>'.$reservation->facility->name.'</b><br><small>'.day_letter($reservation->start->dayOfWeek, 'lg').' '.$reservation->start->format('d.m.Y').'<br>De '.$reservation->start->format('g:i a').' a '.$reservation->end->format('g:i a').'</small></a>';
                    }
                })
            ->addColumn('property', function ($reservation) {                    
                    if($reservation->property->user_id){
                        return $reservation->property->number.'<br><small>'.$reservation->property->user->name.'<br>'.$reservation->property->user->cell.'<small>';
                    }else{
                        return $reservation->property->number;
                    }
                })
            ->addColumn('cost', function ($reservation) {                    
                    return "0000";
                })
            ->editColumn('status', function ($reservation) {                    
                    return $reservation->status_label;
                })
            ->rawColumns(['action', 'facility', 'property', 'status'])
            ->make(true);
    }
    
    /**
     * Display the specified reservation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function load($id)
    {
        $properties=$this->condominium->properties()->orderBy('number')->pluck('number','id');
        
        if($id==0){
            $reservation = new Reservation();
        }else{
            $reservation = Reservation::find($id);
        }
        
        return view('reservations.save')->with('reservation', $reservation)
                                    ->with('properties', $properties);
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
            $reservation->date=Carbon::createFromFormat('d/m/Y', $request->date);
            $reservation->all_day=($request->all_day)?true:false;
            $reservation->hour_cost=$facility->hr_cost;                    
            $reservation->day_cost= $facility->day_cost;
            $reservation->amount=$reservation->hr_cost*$reservation->tot_hrs;
            if ($reservation->all_day){
                $reservation->start=Carbon::createFromFormat('d/m/Y', $request->date);
            }else{
                $reservation->start=Carbon::createFromFormat('d/m/Y H', $request->date.' '.$request->start);
                $reservation->end=Carbon::createFromFormat('d/m/Y H', $request->date.' '.$request->end);
                $reservation->tot_hours=$reservation->end->floatDiffInHours($reservation->start);
            }
            if(session('role')=='ADM'){
                $reservation->confirm_date=Carbon::now();
                $reservation->status= 'A';
            }else{
                $reservation->status= 'P';
            }
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
    public function update(ReservationRequest $request, $id)
    {
        try {
            $reservation = Reservation::find($id);
            $reservation->save();

            return response()->json([
                    'success' => true,
                    'message' => 'Reservación actualizada exitosamente',
                    'reservation' => $reservation
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
    
    public function rpt_reservations()
    {        
        $logo=($this->condominium->logo)?realpath(storage_path()).'/app/'.$this->condominium->id.'/'.$this->condominium->logo:public_path().'/img/company_logo.png';
        $company=$this->condominium->name;
        
        $data=[
            'company' => $this->condominium->name,
            'reservations' => $this->condominium->reservations()->get(),
            'logo' => $logo
        ];

        $pdf = PDF::loadView('reports/rpt_reservations', $data);
        
        return $pdf->stream('Reservaciones.pdf');

    }
}
