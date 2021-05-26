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
    
    /**
     * Display a listing of the category.
     *
     * @return \Illuminate\Http\Response
     */
    public function point_movements()
    {                
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        return view('consults.point_movements')->with('start', $start->format('d/m/Y'))
                                    ->with('end', $end->format('d/m/Y'));
    }


    public function load_point_movements(Request $request)
    {        
        $customer=Customer::find($request->customer_id);
        $start_filter=Carbon::createFromFormat('d/m/Y', $request->start_filter)->format('Y-m-d');
        $end_filter=Carbon::createFromFormat('d/m/Y', $request->end_filter)->format('Y-m-d');

        $date=Carbon::createFromFormat('d/m/Y', $request->start_filter);
        $date=$date->subDays(1);

        $balance_at=$customer->balance_at($date);

        $point_movements = $customer->movements()
                                ->whereDate('date','>=', $start_filter)
                                ->whereDate('date','<=', $end_filter)->get();

        $credits=$point_movements->where('type', 'C')->sum('points');
        $debits=$point_movements->where('type', 'D')->sum('points');
        
        return view('consults.point_movements_detail')->with('customer', $customer)
                                                        ->with('point_movements', $point_movements)
                                                        ->with('date', $date)
                                                        ->with('end_date', $request->end_filter)
                                                        ->with('balance_at', $balance_at)
                                                        ->with('credits', $credits)
                                                        ->with('debits', $debits);
    }
    








    public function datatable_sales(Request $request)
    {        

        $customer_filter=$request->customer_filter;
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        
        if($customer_filter!=''){
            $sales = Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->where('sales.center_id', $this->center->id)
                            ->where('sales.customer_id', $customer_filter)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('sales.status', 'C')
                            ->select(['sales.*', 'customers.name as customer']);
        }else{
            $sales = Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->where('sales.center_id', $this->center->id)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('sales.status', 'C')
                            ->select(['sales.*', 'customers.name as customer']);
        }
        
        return Datatables::of($sales)
            ->editColumn('date', function ($sale) {                    
                    return $sale->date->format('d/m/Y');
                })
            ->editColumn('total', function ($sale) {                    
                    return $sale->total;
                })
            ->make(true);
    }
    
    public function rpt_sales(Request $request)
    {                
        $customer_filter=$request->hdd_customer_id;

        if($customer_filter!=''){
            $customer=Customer::find($customer_filter);
            $customer_name=$customer->name;
        }else{
            $customer_name='Todos';
        }
        
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        
        if($customer_filter!=''){
            $sales = Sale::where('customer_id', $customer_filter)
                            ->where('center_id', $this->center->id)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('sales.status', 'C')->get();
        }else{
            $sales = Sale::where('center_id', $this->center->id)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('sales.status', 'C')->get();
        }
        
        $logo=($this->center->logo)?realpath(storage_path()).'/app/'.$this->center->id.'/'.$this->center->logo:'';
        $company=$this->center->name;
        
        $data=[
            'company' => $company,
            'sales' => $sales,
            'customer_name' => $customer_name, 
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'logo' => $logo
        ];
        $pdf = PDF::loadView('reports/rpt_sales', $data);
        
        return $pdf->stream('Ventas.pdf');

    }



    public function product_sales()
    {                
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        return view('consults.product_sales')->with('start', $start->format('d/m/Y'))
                                    ->with('end', $end->format('d/m/Y'));
    }

    
    public function datatable_product_sales(Request $request)
    {        

        $product_filter=$request->product_filter;
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        
        if($product_filter!=''){
            $product_sales = ProductSale::join('sales', 'product_sale.sale_id', '=', 'sales.id')
                            ->join('products', 'product_sale.product_id', '=', 'products.id')
                            ->join('product_catalog', 'products.product_catalog_id', '=', 'product_catalog.id')
                            ->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->where('product_sale.product_id', $product_filter)
                            ->where('sales.center_id', $this->center->id)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('sales.status', 'C')
                            ->select(['sales.date as date', 'sales.number as number', 'customers.name as customer', 'product_catalog.name as product', 'product_sale.quantity as quantity', 'product_sale.unit_price as unit_price', 'product_sale.total as total']);
        }else{
            $product_sales = ProductSale::join('sales', 'product_sale.sale_id', '=', 'sales.id')
                            ->join('products', 'product_sale.product_id', '=', 'products.id')
                            ->join('product_catalog', 'products.product_catalog_id', '=', 'product_catalog.id')
                            ->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->where('sales.center_id', $this->center->id)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('sales.status', 'C')
                            ->select(['sales.date as date', 'sales.number as number', 'customers.name as customer', 'product_catalog.name as product', 'product_sale.quantity as quantity', 'product_sale.unit_price as unit_price', 'product_sale.total as total']);
        }
        
        return Datatables::of($product_sales)
            ->editColumn('number', function ($product_sale) {                    
                    return '<b>'.$product_sale->number.'</b>';
                })
            ->editColumn('date', function ($product_sale) {                    
                    return Carbon::parse($product_sale->date)->format('d/m/Y');
                })
            ->editColumn('unit_price', function ($product_sale) {                    
                    return $product_sale->unit_price;
                })
            ->editColumn('total', function ($product_sale) {                    
                    return $product_sale->total;
                })
            ->rawColumns(['number', 'unit_price', 'total'])
            ->make(true);
    }

    public function rpt_product_sales(Request $request)
    {        
        $product_filter=$request->hdd_product_id;

        if($product_filter!=''){
            $product=Product::find($product_filter);
            $product_name=$product->product_catalog->name;
        }else{
            $product_name='Todos';
        }
        
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        
        if($product_filter!=''){
            $product_sales = ProductSale::join('sales', 'product_sale.sale_id', '=', 'sales.id')
                            ->join('products', 'product_sale.product_id', '=', 'products.id')
                            ->join('product_catalog', 'products.product_catalog_id', '=', 'product_catalog.id')
                            ->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->where('product_sale.product_id', $product_filter)
                            ->where('sales.center_id', $this->center->id)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('sales.status', 'C')
                            ->select(['sales.date as date', 'sales.number as number', 'customers.name as customer', 'product_catalog.name as product', 'product_sale.quantity as quantity', 'product_sale.unit_price as unit_price', 'product_sale.total as total'])->get();
        }else{
            $product_sales = ProductSale::join('sales', 'product_sale.sale_id', '=', 'sales.id')
                            ->join('products', 'product_sale.product_id', '=', 'products.id')
                            ->join('product_catalog', 'products.product_catalog_id', '=', 'product_catalog.id')
                            ->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->where('sales.center_id', $this->center->id)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('sales.status', 'C')
                            ->select(['sales.number as number', 'customers.name as customer', 'product_catalog.name as product', 'product_sale.quantity as quantity', 'product_sale.unit_price as unit_price', 'product_sale.total as total'])->get();
        }
        
        $logo=($this->center->logo)?realpath(storage_path()).'/app/'.$this->center->id.'/'.$this->center->logo:'';
        $company=$this->center->name;
        
        $data=[
            'company' => $company,
            'product_sales' => $product_sales,
            'product_name' => $product_name, 
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'logo' => $logo
        ];
        $pdf = PDF::loadView('reports/rpt_product_sales', $data);
        
        return $pdf->stream('Ventas por Producto.pdf');

    }

    /**
     * Display a listing of the category.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchases()
    {                
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        return view('consults.purchases')->with('start', $start->format('d/m/Y'))
                                    ->with('end', $end->format('d/m/Y'));
    }

    public function datatable_purchases(Request $request)
    {        

        $supplier_filter=$request->supplier_filter;
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        
        if($supplier_filter!=''){
            $purchases = Purchase::join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                            ->join('payment_methods', 'purchases.payment_method_id', '=', 'payment_methods.id')
                            ->where('purchases.supplier_id', $supplier_filter)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('purchases.status', 'C')
                            ->select(['purchases.*', 'suppliers.name as supplier', 'payment_methods.name as payment_method']);
        }else{
            $purchases = Purchase::join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                            ->join('payment_methods', 'purchases.payment_method_id', '=', 'payment_methods.id')
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('purchases.status', 'C')
                            ->select(['purchases.*', 'suppliers.name as supplier', 'payment_methods.name as payment_method']);
        }
        
        return Datatables::of($purchases)
            ->editColumn('date', function ($purchase) {                    
                    return $purchase->date->format('d/m/Y');
                })
            ->editColumn('total', function ($purchase) {                    
                    return '<div class="text-right">'.Session::get('coin').' '.money_fmt($purchase->total).'</div>';
                })
            ->make(true);
    }
    
    public function get_total_purchases(Request $request){
        
        $supplier_filter=$request->supplier_filter;
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        
        if($supplier_filter!=''){
            $purchases = Purchase::join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                            ->join('payment_methods', 'purchases.payment_method_id', '=', 'payment_methods.id')
                            ->where('purchases.supplier_id', $supplier_filter)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('purchases.status', 'C')
                            ->select(['purchases.*', 'suppliers.name as customer', 'payment_methods.name as payment_method']);
        }else{
            $purchases = Purchase::join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                            ->join('payment_methods', 'purchases.payment_method_id', '=', 'payment_methods.id')
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('purchases.status', 'C')
                            ->select(['purchases.*', 'suppliers.name as customer', 'payment_methods.name as payment_method']);
        }

        return response()->json(['total_purchases' => $purchases->sum('total')]);

    }

    public function rpt_purchases(Request $request)
    {        
        $supplier_filter=$request->hdd_supplier_id;

        if($supplier_filter!=''){
            $supplier=Supplier::find($supplier_filter);
            $supplier_name=$supplier->name;
        }else{
            $supplier_name='Todos';
        }
        
        $start_filter=(new Carbon((new ToolController)->format_ymd($request->start_filter)))->format('Y-m-d');
        $end_filter=(new Carbon((new ToolController)->format_ymd($request->end_filter)))->format('Y-m-d');
        
        if($supplier_filter!=''){
            $purchases = Purchase::where('supplier_id', $supplier_filter)
                            ->whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('purchases.status', 'C')->get();
        }else{
            $purchases = Purchase::whereDate('date','>=', $start_filter)
                            ->whereDate('date','<=', $end_filter)
                            ->where('purchases.status', 'C')->get();
        }
        
        $logo=($this->center->logo)?realpath(storage_path()).'/app/'.$this->center->id.'/'.$this->center->logo:'';
        $company=$this->center->name;
        
        $data=[
            'company' => $company,
            'purchases' => $purchases,
            'supplier_name' => $supplier_name, 
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'logo' => $logo
        ];
        $pdf = PDF::loadView('reports/rpt_purchases', $data);
        
        return $pdf->stream('Compras.pdf');

    }


    /**
     * Display a listing of the category.
     *
     * @return \Illuminate\Http\Response
     */
    public function sales_tax()
    {                
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        return view('consults.sales_tax')->with('start', $start->format('d/m/Y'))
                                        ->with('end', $end->format('d/m/Y'));
    }

    public function get_sales_taxes(Request $request){
        
        $start_filter=Carbon::createFromFormat('d/m/Y', $request->start_filter)->format('Y-m-d');
        $end_filter=Carbon::createFromFormat('d/m/Y', $request->end_filter)->format('Y-m-d');
        
        //agrupa los tipos de impuestos de ventas en el rango solicitado
        $taxes_sales=ProductSale::join('sales', 'product_sale.sale_id', '=', 'sales.id')
                            ->whereDate('sales.date','>=', $start_filter)
                            ->whereDate('sales.date','<=', $end_filter)
                            ->select('product_sale.tax as tax')
                            ->groupBy('tax')
                            ->orderBy('tax', 'desc')->get();

        //agrupa los tipos de impuestos de notas de credito en el rango solicitado
        $taxes_cn=CreditNoteProduct::
                            join('credit_notes', 'credit_note_product.credit_note_id', '=', 'credit_notes.id')
                            ->whereDate('credit_notes.date','>=', $request->start_filter)
                            ->whereDate('credit_notes.date','<=', $request->end_filter)
                            ->select('credit_note_product.tax as tax')
                            ->groupBy('tax')
                            ->orderBy('tax', 'desc')->get();

        //una ambos arreglos para obtener la coleccion de impuestos
        $taxes=$taxes_sales->union($taxes_cn);
        return $taxes;
    }
    
    public function get_sales_tax_collection(Request $request){
        
        $start_filter=Carbon::createFromFormat('d/m/Y', $request->start_filter)->format('Y-m-d');
        $end_filter=Carbon::createFromFormat('d/m/Y', $request->end_filter)->format('Y-m-d');

        $sales=ProductSale::join('sales', 'product_sale.sale_id', '=', 'sales.id')
                            ->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->where('sales.type','F')
                            ->whereDate('sales.date','>=', $start_filter)
                            ->whereDate('sales.date','<=', $end_filter)
                            ->select(
                                    'sales.id as id',
                                    'sales.date as date', 
                                    'sales.number as number',
                                    'customers.name as customer', 
                                    'product_sale.tax as tax',
                                    DB::raw("'FAC' as doc"), 
                                    DB::raw("sum(product_sale.tax_amount) as tax_amount"),
                                    DB::raw("sum(product_sale.discount_amount) as discount_amount"),
                                    DB::raw("sum(product_sale.total) as total")
                                )
                            ->groupBy('id', 'date', 'number', 'customer', 'tax')
                            ->orderBy('id')->get();        
        

        $credit_notes=CreditNoteProduct::
                            join('credit_notes', 'credit_note_product.credit_note_id', '=', 'credit_notes.id')
                            ->join('customers', 'credit_notes.customer_id', '=', 'customers.id')
                            ->whereDate('credit_notes.date','>=', $start_filter)
                            ->whereDate('credit_notes.date','<=', $end_filter)
                            ->select(
                                    'credit_notes.id as id',
                                    'credit_notes.date as date', 
                                    'credit_notes.number as number',
                                    'customers.name as customer', 
                                    'credit_note_product.tax as tax',
                                    DB::raw("'NCR' as doc"), 
                                    DB::raw("sum(credit_note_product.tax_amount) as tax_amount"),
                                    DB::raw("sum(credit_note_product.discount_amount) as discount_amount"),
                                    DB::raw("sum(credit_note_product.total) as total")
                                )
                            ->groupBy('id', 'date', 'number', 'customer', 'tax')
                            ->orderBy('id')->get();        

        //$sales_cns=$sales;
        $sales_cns=$sales->concat($credit_notes);
        return $sales_cns->sortBy('id')->sortBy('date');        
    }
    
    public function sales_tax_matrix(Request $request)
    {                
        $taxes=$this->get_sales_taxes($request);
        $sales_cns=$this->get_sales_tax_collection($request);

        return view('consults.sales_tax_matrix')->with('taxes', $taxes)
                                                ->with('sales_cns', $sales_cns);
    }


    public function rpt_sales_tax(Request $request)
    {                
        $taxes=$this->get_sales_taxes($request);
        $sales_cns=$this->get_sales_tax_collection($request);
        
        $logo=($this->center->logo)?realpath(storage_path()).'/app/'.$this->center->id.'/'.$this->center->logo:'';
        $company=$this->center->name;
        
        $data=[
            'company' => $company,
            'taxes' => $taxes,
            'sales_cns' => $sales_cns, 
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'logo' => $logo
        ];
        $pdf = PDF::loadView('reports/rpt_sales_tax', $data);
        
        return $pdf->stream('Reporte de Ventas por Impuesto.pdf');

    }

    public function xls_sales_tax(Request $request)
    {        
        $taxes=$this->get_sales_taxes($request);
        $sales_cns=$this->get_sales_tax_collection($request);
        
        $start=$request->start_filter;
        $end=$request->end_filter;

        return Excel::download(new SalesTaxExport($taxes,$sales_cns), 'Ventas por Impuesto.xlsx');        
    }


    /**
     * Display a listing of the category.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchases_tax()
    {                
        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        return view('consults.purchases_tax')->with('start', $start->format('d/m/Y'))
                                        ->with('end', $end->format('d/m/Y'));
    }
    
    public function get_purchases_taxes(Request $request){
        
        $start_filter=Carbon::createFromFormat('d/m/Y', $request->start_filter)->format('Y-m-d');
        $end_filter=Carbon::createFromFormat('d/m/Y', $request->end_filter)->format('Y-m-d');
        
        //agrupa los tipos de impuestos de compras en el rango solicitado
        $taxes=ProductPurchase::join('purchases', 'product_purchase.purchase_id', '=', 'purchases.id')
                            ->whereDate('purchases.date','>=', $start_filter)
                            ->whereDate('purchases.date','<=', $end_filter)
                            ->select('product_purchase.tax as tax')
                            ->groupBy('tax')
                            ->orderBy('tax', 'desc')->get();


        return $taxes;
    }
    
    public function get_purchases_tax_collection(Request $request){
        
        $start_filter=Carbon::createFromFormat('d/m/Y', $request->start_filter)->format('Y-m-d');
        $end_filter=Carbon::createFromFormat('d/m/Y', $request->end_filter)->format('Y-m-d');

        $purchases=ProductPurchase::join('purchases', 'product_purchase.purchase_id', '=', 'purchases.id')
                            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                            ->where('purchases.type','C')
                            ->whereDate('purchases.date','>=', $start_filter)
                            ->whereDate('purchases.date','<=', $end_filter)
                            ->select(
                                    'purchases.id as id',
                                    'purchases.date as date', 
                                    'purchases.number as number',
                                    'suppliers.name as supplier', 
                                    'product_purchase.tax as tax',
                                    DB::raw("'CO' as doc"), 
                                    DB::raw("sum(product_purchase.tax_amount) as tax_amount"),
                                    DB::raw("sum(product_purchase.discount_amount) as discount_amount"),
                                    DB::raw("sum(product_purchase.total) as total")
                                )
                            ->groupBy('id', 'date', 'number', 'supplier', 'tax')
                            ->orderBy('id')->get();        
        
        return $purchases->sortBy('id')->sortBy('date');        
    }
    
    public function purchases_tax_matrix(Request $request)
    {                
        $taxes=$this->get_purchases_taxes($request);
        $purchases=$this->get_purchases_tax_collection($request);

        return view('consults.purchases_tax_matrix')->with('taxes', $taxes)
                                                ->with('purchases', $purchases);
    }


    public function rpt_purchases_tax(Request $request)
    {                
        $taxes=$this->get_purchases_taxes($request);
        $purchases=$this->get_purchases_tax_collection($request);
        
        $logo=($this->center->logo)?realpath(storage_path()).'/app/'.$this->center->id.'/'.$this->center->logo:'';
        $company=$this->center->name;
        
        $data=[
            'company' => $company,
            'taxes' => $taxes,
            'purchases' => $purchases, 
            'start' => $request->start_filter,
            'end' => $request->end_filter,
            'logo' => $logo
        ];
        $pdf = PDF::loadView('reports/rpt_purchases_tax', $data);
        
        return $pdf->stream('Reporte de Compras por Impuesto.pdf');

    }

    public function xls_purchases_tax(Request $request)
    {        
        $taxes=$this->get_purchases_taxes($request);
        $purchases=$this->get_purchases_tax_collection($request);
        
        $start=$request->start_filter;
        $end=$request->end_filter;

        return Excel::download(new PurchasesTaxExport($taxes,$purchases), 'Compras por Impuesto.xlsx');        
    }


}
