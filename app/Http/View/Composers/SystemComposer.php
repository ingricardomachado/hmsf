<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Session;
use Auth;
use App\Models\Condominium;

class SystemComposer
{
    /**
     * Create a movie composer.
     *
     * @return void
     */
    public function __construct()
    {
        /*
        $this->global_center ='';

        if(!Auth::guest() && Auth::user()->role == 'ADM'){
            $this->global_center = Center::find(Auth::user()->center_id);
        }
        */

        $this->global_condominium ='';

        if(!Auth::guest() && Auth::user()->role == 'SAM' && Session::has('condominium')){
            $this->global_condominium=Condominium::find(Session::get('condominium')->id);
        }elseif(!Auth::guest()){
            $this->global_condominium=Condominium::find(Auth::user()->condominium_id);
        }

    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if(!Auth::guest()){
            $view->with('global_condominium', $this->global_condominium);
        }
    }
}