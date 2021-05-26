<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Company;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Crypt;
use File;
use PDF;
use Session;


class PDFController extends Controller
{
 
    /*
     * Download file from DB  
    */ 
    public function invoice_pdf($id)
    {
        $company = Company::first();
        $invoice = Invoice::find(Crypt::decrypt($id));
        $data=[
            'company' => $company,
            'invoice' => $invoice,
        ];
        $pdf = PDF::loadView('reports/invoice', $data);
        return $pdf->download('Invoice.pdf');

    }

    /*
     * Download file from DB  
    */ 
    public function invoices_pdf($year, $month)
    {
        //return "Todos los recibos";
        $company = Company::first();
        $invoices = Invoice::where('year', Crypt::decrypt($year))
                            ->where('month', Crypt::decrypt($month))->get();
        $data=[
            'company' => $company,
            'invoices' => $invoices,
        ];
        $pdf = PDF::loadView('reports/invoice_all', $data);
        return $pdf->download('InvoicesAll.pdf');

    }

    /*
     * Download file from DB  
    */ 
    public function print_voucher($id)
    {
        $company = Company::first();
        $payment = Payment::find(Crypt::decrypt($id));
        $data=[
            'company' => $company,
            'payment' => $payment,
        ];
        $pdf = PDF::loadView('reports/voucher', $data);
        return $pdf->download('Voucher.pdf');

    }


}
