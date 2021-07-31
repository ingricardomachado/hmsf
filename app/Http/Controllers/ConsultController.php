<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ProductCategoryRequest;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Customer;
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
use Carbon\Carbon;
use Session;
//Export
use App\Exports\SalesTaxExport;
use App\Exports\PurchasesTaxExport;

class ConsultController extends Controller
{
       
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'create', 'edit']]);
    }    

}
