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
use App\Models\Transfer;
use File;
use Image;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Storage;




class ImgController extends Controller
{
    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showCompanyLogo($id)
    {
        $setting=Setting::first();
        if($setting->logo!=null){
            $picture=Image::make(Storage::get('global/'.$setting->logo));
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
    public function showUserAvatar($id)
    {
        $user = User::findOrFail($id);
        if($user->avatar!=null){
            if($user->condominium_id){
                $picture = Image::make(Storage::get($user->condominium_id.'/users/'.$user->avatar));
            }else{
                $picture = Image::make(Storage::get('global/'.$user->avatar));
            }
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
            $picture = Image::make(Storage::get($contact->condominium_id.'/contacts/thumbs/'.$contact->avatar));
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
            $picture = Image::make(Storage::get($employee->condominium_id.'/employees/thumbs/'.$employee->avatar));
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
            $picture = Image::make(Storage::get($facility->condominium_id.'/facilities/thumbs/'.$facility->photo));
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
            $picture=Image::make(Storage::get($condominium->id.'/'.$condominium->logo));
        }else{
            $picture = Image::make(public_path().'/img/company_logo.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showDocumentImage($id)
    {
        $document = Document::findOrFail($id);
        $picture = Image::make(Storage::get($document->condominium_id.'/documents/'.$document->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showIncomeImage($id)
    {
        $income = Income::findOrFail($id);
        $picture = Image::make(Storage::get($income->condominium_id.'/incomes/'.$income->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showExpenseImage($id)
    {
        $expense = Expense::findOrFail($id);
        $picture = Image::make(Storage::get($expense->condominium_id.'/expenses/'.$expense->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showTransferImage($id)
    {
        $transfer = Transfer::findOrFail($id);
        $picture = Image::make(Storage::get($transfer->condominium_id.'/transfers/'.$transfer->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    public function showPaymentImage($id)
    {
        $payment = Payment::findOrFail($id);
        $picture = Image::make(Storage::get($payment->condominium_id.'/payments/'.$payment->file));
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }
}

?>