<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Operation;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
//Export
use Carbon\Carbon;
use DB;
use Storage;
//Import
use App\Imports\PartnersImport;
use App\Imports\CustomersImport;
use App\Imports\CompaniesImport;
use App\Imports\OperationsImport;

class FixController extends Controller
{
           
   public function upload_partners(Request $request){      
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Partner::truncate();
        User::where('role', 'SOC')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Excel::import(new PartnersImport(), storage_path().'/app/SOCIOS.xlsx');
   }

   public function upload_customers(Request $request){      
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Customer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Excel::import(new CustomersImport(), storage_path().'/app/CLIENTES.xlsx');
   }

   public function upload_companies(Request $request){      
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Company::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Excel::import(new CompaniesImport(), storage_path().'/app/EMPRESAS.xlsx');
   }

   public function upload_operations(Request $request){      
        //DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        //Operation::truncate();
        //DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Excel::import(new OperationsImport(), storage_path().'/app/OPERACIONES.xlsx');
   }

}
