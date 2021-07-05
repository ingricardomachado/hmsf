<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Account;
use App\Models\Movement;
use App\User;
use Session;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HaciendaImport;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Input;
use XmlParser;
use SimpleXMLElement;

class FixController extends Controller
{    
    function update_balance_accounts(){

        $accounts=Account::all();
        
        foreach ($accounts as $account) {
           $account->update_balance();
        }

        return "Saldos actualizados";
    }
}
