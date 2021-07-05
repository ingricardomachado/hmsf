<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\User;
use App\Models\Setting;
use App\Models\Condominium;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\Contact;
use App\Models\Document;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Payment;
use File;
use Image;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Storage;




class ImgController extends Controller
{

   public function resize_image($img, $ext, $width, $height)
   {
      
        $img_resize = new Image();      
        //Paso 1: TAMAÑO. Si la imagen es muy grande se hace un resize de ancho y  alto segun parametros como maximo manteniendo su relacion de aspecto.        
        //$width      = 450;
        //$height     = 350;
        $img->resize($width, $height, function ($c) {
          $c->aspectRatio();
          $c->upsize();
        });
        //Paso 2: PESO. Una vez redimensionada si el archivo pesa mas de 500 Kb se baja la calidad al 90%
        if ($img->filesize()>500000)
        {
          $img = $img->encode($ext,95);
        } 
        
        $img_resize = $img;
        
        return $img_resize;
   }

    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showUserAvatar($id)
    {
        $user = User::findOrFail($id);
        if($user->avatar!=null){
            $picture = Image::make(storage_path('app/users/'.$user->avatar));
        }else{
            $picture = Image::make(public_path().'/img/avatar_default.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }
    
    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showContactAvatar($id)
    {
        $contact = Contact::findOrFail($id);
        if($contact->avatar!=null){
            $picture = Image::make(storage_path('app/'.$contact->condominium_id.'/contacts/thumbs/'.$contact->avatar));
        }else{
            $picture = Image::make(public_path().'/img/avatar_default.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showEmployeeAvatar($id)
    {
        $employee = Employee::findOrFail($id);
        if($employee->avatar!=null){
            $picture = Image::make(storage_path('app/'.$employee->condominium_id.'/employees/thumbs/'.$employee->avatar));
        }else{
            $picture = Image::make(public_path().'/img/avatar_default.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }
    
    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showFacilityPhoto($id)
    {
        $facility = Facility::findOrFail($id);
        if($facility->photo!=null){
            $picture = Image::make(storage_path('app/'.$facility->condominium_id.'/facilities/thumbs/'.$facility->photo));
        }else{
            $picture = Image::make(public_path().'/img/no_image_available.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showCondominiumLogo($id)
    {
        $condominium=Condominium::findOrFail($id);
        if($condominium->logo!=null){
            $picture=Image::make(storage_path('app/'.$condominium->id.'/'.$condominium->logo));
        }else{
            $picture = Image::make(public_path().'/img/company_logo.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showCompanyLogo()
    {
        $setting = Setting::first();
        $picture = Image::make($setting->logo);
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showDocumentImage($id)
    {
        $document = Document::findOrFail($id);
        $picture = Image::make(storage_path('app/'.$document->condominium_id.'/documents/'.$document->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showIncomeImage($id)
    {
        $income = Income::findOrFail($id);
        $picture = Image::make(storage_path('app/'.$income->condominium_id.'/incomes/'.$income->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showExpenseImage($id)
    {
        $expense = Expense::findOrFail($id);
        $picture = Image::make(storage_path('app/'.$expense->condominium_id.'/expenses/'.$expense->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showPaymentImage($id)
    {
        $payment = Payment::findOrFail($id);
        $picture = Image::make(storage_path('app/'.$payment->condominium_id.'/payments/'.$payment->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

}

?>