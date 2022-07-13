<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Tax_Invoice;
use App\Model\Tax_Dispatch;
use App\Model\InternalOrder;
use App\Model\Delivery_challan;
use App\Model\Utilities\Material_inwarding;
use App\Model\Utilities\Internal_DC;
use App\Model\Production\DailyPlateProcess;
use App\Model\Production\PressDailyProcess;
use App\Model\Payment;
use App\Model\Payment_Received;
use App\Model\Payment_Date;
use App\Model\tax_invoice_payment_date;
use App\Model\FinancialYear;
use App\Model\Reference;
use App\Model\Party;
use App\Model\Purchase\PurchaseOrder;
use App\Model\Design\DesignOrder;
use App\Model\PaymentStatus;
use App\Model\Follow_up_Status;
use DB;
use \Carbon\Carbon;
use Route;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use App\dkerp;
use Auth;use File;

class CollectionController extends Controller
{
	public function tax_inv_dispatch(){
		$payment_term = Payment::all();
		$data=array('layout'=>'layouts.main','payment'=>$payment_term);
        return view('collection.tax_invoice_dispatch', $data);
	}
	public function tax_inv_dispatch_api(Request $request){
		$search = $request->input('search');
	    $serach_value = $search['value'];
	    $start = $request->input('start');
	    $limit = $request->input('length');
	    $offset = empty($start) ? 0 : $start ;
	    $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
	    $client_po = Tax_Dispatch::leftJoin('tax_invoice', function($join) { $join->on('tax_invoice.id', '=', 'tax_dispatch.tax_invoice_id');})
	    ->leftJoin('party', function($join) { $join->on('tax_invoice.party_id', '=', 'party.id');})
	    ->leftJoin('consignee', function($join) { $join->on('tax_invoice.consignee_id', '=', 'consignee.id');})
	    ->leftJoin('employee__profile', function($join) { $join->on('tax_dispatch.person', '=', 'employee__profile.id');})
	    ->leftJoin('goods_dispatch', function($join) { $join->on('tax_dispatch.courier_company', '=', 'goods_dispatch.id');})
        ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','tax_invoice.id')
        ->where('coll__ti_payment_date.tax_invoice_id','=',NULL)
	    // ->where('tax_dispatch.is_active',1)
	    ->where('tax_invoice.is_cancelled',0);
        if(!empty($serach_value))
        {
            $client_po->where(function($query) use ($serach_value){
	            $query->where('party.partyname','LIKE',"%".$serach_value."%")
	            ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
	            ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%")
	            ->orwhere('tax_dispatch.dispatch_date','like',"%".$serach_value."%")
	            ->orwhere('goods_dispatch.courier_name','like',"%".$serach_value."%")
	            ->orwhere('tax_dispatch.docket_number','like',"%".$serach_value."%")
	            ->orwhere('tax_dispatch.created_time','like',"%".$serach_value."%");
            });
                        
        }
        $count = count($client_po->select(
            'tax_dispatch.id as id',
            'tax_dispatch.dispatch_date',
            'tax_dispatch.dispatch_mode',
            'goods_dispatch.courier_name as courier_company',
            'tax_dispatch.docket_number',
            'employee__profile.name as person',
            DB::raw('DATE_FORMAT(tax_dispatch.created_time,"%d-%m-%Y") as created_time'),
            DB::raw('DATE_FORMAT(tax_dispatch.docket_date,"%d-%m-%Y") as docket_date'),
            'tax_invoice.invoice_number as ch_no',
            'tax_invoice.id as tax_id',
            DB::raw('DATE_FORMAT(tax_invoice.date,"%d-%m-%Y") as tax_date'),
            'tax_dispatch.status',
            'tax_dispatch.docket_file',
            'party.partyname as pname',
            'tax_dispatch.byhand_invoice',
            'tax_dispatch.party_invoice',
            'consignee.consignee_name as cname','coll__ti_payment_date.id as ti_payment_date_id')->get());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['ch_no','party.partyname','tax_dispatch.docket_file','tax_dispatch.byhand_invoice',
            'tax_dispatch.party_invoice','tax_dispatch.docket_date','consignee.consignee_name','tax_date','tax_invoice.id as tax_id','tax_dispatch.id','tax_dispatch.dispatch_date','goods_dispatch.courier_name','employee__profile.name as person','tax_dispatch.docket_number','tax_dispatch.created_time'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        $client_po->orderBy('tax_invoice.id','desc');
        $client_po= $client_po->select(
            'tax_dispatch.id as id',
            'tax_dispatch.dispatch_date',
            'tax_dispatch.dispatch_mode',
            'goods_dispatch.courier_name as courier_company',
            'tax_dispatch.docket_number',
            DB::raw('DATE_FORMAT(tax_dispatch.docket_date,"%d-%m-%Y") as docket_date'),
            'employee__profile.name as person',
            DB::raw('DATE_FORMAT(tax_dispatch.created_time,"%d-%m-%Y %r") as created_time'),
            'tax_invoice.invoice_number as ch_no',
            'tax_invoice.id as tax_id',
            'tax_dispatch.docket_file',
            DB::raw('DATE_FORMAT(tax_invoice.date,"%d-%m-%Y") as tax_date'),
            'party.partyname as pname',
            'party.payment_term_id',
            'tax_dispatch.status',
            'tax_dispatch.byhand_invoice',
            'tax_dispatch.party_invoice',
            'consignee.consignee_name as cname','coll__ti_payment_date.id as ti_payment_date_id')->get();
            // print_r( DB::getQueryLog());die('@ggh');
       
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        return json_encode($array);
	}
	public function tax_invoice_receipt_date(Request $request){
		try {

			$error =[];
            if(empty($request->input('tir_date'))){
                $error = array_merge($error,array('Tax Invoice Receipt Date is Required'));
            }
            
            if(count($error)>0){
                $data = [
                'error'=>$error];
                return response()->json($data);
            }

            $payment_date = "";
            if($request->input('pay_term') == null || $request->input('pay_term') == 0 || $request->input('pay_term') == ""){
                $payment_date = date('Y-m-d',strtotime($request->input('tir_date')));
            }else{
                $payment_term = Payment::where('id',$request->input('pay_term'))->get()->first()['value'];
                if($payment_term == "Against delivery"){
                    $payment_date = date('Y-m-d',strtotime($request->input('tir_date')));
                }else if($payment_term == "First week of every month"){
                    $payment_date = date('Y-m-d',strtotime("30 Days",$request->input('tir_date')));
                }else{
                    $payment_date = date('Y-m-d',strtotime($payment_term, strtotime($request->input('tir_date'))));
                }

            }
            // print_r($payment_date);
            // print_r('<br>');
            // print_r($payment_term);die();
            $pp=Tax_Invoice::where('id',$request->input('id'))->select('party_id')->get()->first();
            $timestamp = date('Y-m-d G:i:s');
            $reciept = tax_invoice_payment_date::InsertGetId([
<<<<<<< HEAD
                    'tax_invoice_id'=>$request->input('id'), 		
=======
                    'id'=>null,
                    'tax_invoice_id'=>$request->input('id'), 	
                    'party_id'=>$pp['party_id'],	
>>>>>>> 6e254f2a84f09a2aecc6a64a2b90565ef11ed767
                    'tax_reciept_date'=>date('Y-m-d',strtotime($request->input('tir_date'))),
                    'payment_date' =>$payment_date,
                    'created_by'=>Auth::id(),
                    'created_at'=>$timestamp
                ]);
            // print_r($reciept);die();
                if($reciept == null){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));
                }else{
                    $msg =['Successfully Tax Invoice Receipt Date Created.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
                return response()->json($data);
		} catch (\Illuminate\Database\QueryException $ex) {
			 return redirect('/collection/taxinvoice/dispatch')->with('error','some error occurred'.$ex->getMessage());
		}
	}
	public function payment_date_summary(){
        $payment_term = Payment::all();
		$data=array('layout'=>'layouts.main','payment'=>$payment_term);
        return view('collection.payment_date_summary', $data);
	}
	public function payment_date_summary_api(Request $request){
		$search = $request->input('search');
	    $serach_value = $search['value'];
	    $start = $request->input('start');
	    $limit = $request->input('length');
	    $offset = empty($start) ? 0 : $start ;
	    $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
	    $client_po = tax_invoice_payment_date::leftjoin('tax_invoice','tax_invoice.id','coll__ti_payment_date.tax_invoice_id')
	    ->leftjoin('tax','tax.tax_invoice_id','coll__ti_payment_date.tax_invoice_id')
	    ->leftjoin('party','tax_invoice.party_id','party.id')
	    ->leftjoin('internal_order','internal_order.id','tax.io_id')
	    ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('payment_term as p1','p1.id','party.payment_term_id')
        ->leftJoin('payment_term as p2','p2.id','coll__ti_payment_date.payment_term')

        ->select('coll__ti_payment_date.id','tax_invoice.invoice_number',
        DB::raw('DATE_FORMAT(tax_invoice.date,"%d-%m-%Y") as tax_date'),
        'party.partyname',
        DB::raw('group_concat(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        'tax.qty',
        'tax.rate',
        'tax.amount','tax_invoice.total_amount',

        DB::raw('DATE_FORMAT(coll__ti_payment_date.tax_reciept_date,"%d-%m-%Y") as tax_reciept_date'),
        DB::raw('DATE_FORMAT(coll__ti_payment_date.payment_date,"%d-%m-%Y") as payment_date'),
        'party.payment_term_id','p1.value as pay_name','coll__ti_payment_date.payment_term as cp','p2.value as col_name','tax.payment as tax_payment')
        ->groupby('coll__ti_payment_date.id')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value))
        {
            $client_po->where(function($query) use ($serach_value){
	            $query->where('party.partyname','LIKE',"%".$serach_value."%")
	            ->orwhere('item_category.name','like',"%".$serach_value."%")
	            ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%")
	            ;
            });
                        
        }
        $count = count($client_po->get());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['coll__ti_payment_date.id','tax_invoice.invoice_number','tax_invoice.date as tax_date','party.partyname','item_name','tax.qty','tax.rate','tax.amount','tax_invoice.total_amount','coll__ti_payment_date.tax_reciept_date','coll__ti_payment_date.payment_date'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        $client_po->orderBy('coll__ti_payment_date.id','desc');
        $client_po= $client_po->get();

       
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        return json_encode($array);
	}
    public function update_payment_date(Request $request){
        try {
            $error =[];
            if(empty($request->input('pay_date'))){
                $error = array_merge($error,array('Payment Date is Required'));
            }
            if(count($error)>0){
                $data = [
                'error'=>$error];
                return response()->json($data);
            }
            $tax_reciept =tax_invoice_payment_date::where('id',$request->input('id'))->get()->first();
            $payment_date = date('Y-m-d',strtotime($request->input('pay_date')));
            
            // $payment_term = Payment::where('id',$request->input('pay_term'))->get()->first()['value'];
            // if($payment_term == "Against delivery"){
            //     $payment_date = date('Y-m-d',strtotime($tax_reciept['payment_date']));
            // }else if($payment_term == "First week of every month"){
            //     $payment_date = date('Y-m-d',strtotime($tax_reciept['payment_date']));
            // }else{
            //     $payment_date = date('Y-m-d',strtotime($payment_term, strtotime($tax_reciept['tax_reciept_date'])));
            // }

            $timestamp = date('Y-m-d G:i:s');
            $reciept = tax_invoice_payment_date::where('id',$request->input('id'))->update([        
                    // 'payment_term'=>$request->input('pay_term'),
                    'payment_date'=>$payment_date,
                    'updated_at'=>$timestamp
                ]);
                if($reciept == null){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));
                }else{
                    $msg =['Successfully Tax Invoice Payment Date Updated.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
                return response()->json($data);
        } catch (\Illuminate\Database\QueryException $ex) {
             return redirect('/collection/taxinvoice/dispatch')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function bill_recievable(){
        $mop = DB::table('mode_of_payment')->get();
        $Reference = Reference::get()->toArray();
        $data=array('layout'=>'layouts.main','mop'=>$mop,'reference'=>$Reference);
        return view('collection.bill_recievable_summary', $data);
    }
    public function bill_recievable_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $party = $request->input('party');
        $ref = $request->input('ref');
        // print_r($party);
        // print_r("x");
        // print_r($ref);
        // die();
        // DB::enableQueryLog();
        $bill = Payment_Date::leftJoin('tax_invoice', function($join) { $join->on('tax_invoice.id', '=', 'coll__ti_payment_date.tax_invoice_id');})
        ->leftjoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
            ->leftJoin('party','tax_invoice.party_id', 'party.id')
            ->leftjoin('tax','tax_invoice.id','tax.tax_invoice_id')
            ->leftjoin('internal_order','internal_order.id','tax.io_id')
            ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
            // ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','tax_invoice.id')
            // ->where('coll__ti_payment_date.tax_invoice_id','!=',NULL)
            ->where('coll__payment_recieved.status',"!=",'closed')
            ->orwhere('coll__payment_recieved.id',"=",NULL)
            ->select('tax_invoice.id','tax_invoice.invoice_number',
            DB::raw('DATE_FORMAT(tax_invoice.date,"%d-%m-%Y") as tax_date'),
            'party.partyname',
            DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as item_name'),
            'tax.qty','tax.rate','tax_invoice.total_amount',DB::raw('(IFNULL(
            (SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
            WHERE p.tax_invoice_id = tax_invoice.id
                GROUP BY p.tax_invoice_id) ,0 ) 
            ) AS amt_recieved'),
            DB::raw('(
                (SELECT DISTINCT(p.status) FROM coll__payment_recieved p 
                WHERE p.tax_invoice_id = tax_invoice.id
                    GROUP BY p.tax_invoice_id ) 
                ) AS status'),
            DB::raw('tax_invoice.total_amount - (
                IFNULL((
            SELECT
                SUM(p.pr_amount)
            FROM
                coll__payment_recieved p
            WHERE
                p.tax_invoice_id = tax_invoice.id
            GROUP BY
                p.tax_invoice_id
            ) ,
                    0
                ) 
            ) AS balance_amt'),'coll__ti_payment_date.payment_date')->HavingRaw('(tax_invoice.total_amount - (
                IFNULL((
            SELECT
                SUM(p.pr_amount)
            FROM
                coll__payment_recieved p
            WHERE
                p.tax_invoice_id = tax_invoice.id
            GROUP BY
                p.tax_invoice_id
            ) ,
                        0
                    ) 
                )) > 0')
            ->groupby('tax_invoice.id')
            // ->get()
        ;
            // print_r( DB::getQueryLog());die();

        if(!empty($party) && empty($ref))
        {
            $bill->where(function($query) use ($party){
                $query->where('tax_invoice.party_id',$party);
            });                
        }else if(!empty($ref) && empty($party)){
            $bill->where(function($query) use ($ref){
                $query->where('party.reference_name',$ref);
            });   
        }
        if(!empty($serach_value))
        {
            $bill->where(function($query) use ($serach_value){
                $query->where('party.partyname','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%")
                ->orwhere('internal_order.other_item_name','like',"%".$serach_value."%")
                ;
            });
                        
        }
        $count = count($bill->get());
        $bill = $bill->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['tax_invoice.id','tax_invoice.invoice_number','tax_date','party.partyname','item_name','tax.qty','tax.rate','tax_invoice.total_amount','amt_recieved','balance_amt'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $bill->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        $bill->orderBy('tax_invoice.id','desc');
        $bill= $bill->get();

       
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $bill; 
        return json_encode($array);
    }
    public function bill_recievable_ondate_db(Request $request){
        try {
            $error =[];
            if(empty($request->input('recieve_date'))){
                $error = array_merge($error,array('Payment received on date is Required'));
            }
            if(empty($request->input('amt_recieve'))){
                $error = array_merge($error,array('Amount received is Required'));
            }
            if(empty($request->input('pay_term'))){
                $error = array_merge($error,array('Mode of payment is Required'));
            }
            
            if(count($error)>0){
                $data = [
                'error'=>$error];
                return response()->json($data);
            }
            $tax=Tax_Invoice::where('tax_invoice.id',$request->input('tax_id'))
            ->leftJoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
            ->select(DB::raw('DISTINCT(total_amount) as total_amount'),DB::raw('SUM(IFNULL(coll__payment_recieved.pr_amount,"0")) as amt'))->groupBy('tax_invoice.id')->get()->first();
            // print_r($tax);die;
            $tax_left=$tax['total_amount']-$tax['amt'];
            $left=$tax['total_amount']-$tax['amt'];
            if($request->input('amt_recieve')>$tax_left){
              
                $error = array_merge($error,array('Amount Received Cannot be more than left amount'));
                $data = [

                    'error'=>$error];
                    return response()->json($data);
            }
            $rep=Payment_Received::where('tax_invoice_id', $request->input('tax_id'))
            ->select(DB::raw('DISTINCT(status) as status'))->get()->first();
            if($rep['status']=="closed"){
                $error = array_merge($error,array('Payment Status For this Invoice is already closed.'));
                $data = [

                    'error'=>$error];
                    return response()->json($data);
            }
            $file = $request->file('upload_adv');
            $job_image ='';
            if(isset($file) || $file != null){
                $destinationPath = public_path().'/upload/Recievable/';
                $filenameWithExt = $request->file('upload_adv')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('upload_adv')->getClientOriginalExtension();
                $job_image = $filename.'_'.time().'.'.$extension;
                $path = $file->move($destinationPath, $job_image);
            }else{
                $job_image = '';
            }
            
            $timestamp = date('Y-m-d G:i:s');
            $reciept = Payment_Received::InsertGetId([        
                    'id'=>null,
                    'tax_invoice_id'=> $request->input('tax_id'),
                    'pr_date'=>date("Y-m-d",strtotime($request->input('recieve_date'))),
                    'pr_amount'=>$request->input('amt_recieve'),
                    'mop_id'=>$request->input('pay_term'),
                    'advice_upload'=>$job_image,
                    'deduction'=>$request->input('deduct'),
                    'reason_for_deduction'=>$request->input('ded_reason'),
                    'created_by'=>Auth::id(),
                    'created_at'=>$timestamp
                ]);
            
                if($reciept == null){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));
                }else{
                    $msg =['Successfully Recieved Amount Updated.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
                return response()->json($data);
        } catch (\Illuminate\Database\QueryException $ex) {
             return redirect('/collection/billrecieve/summary')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function payment_recieved_summary(){
        $data=array('layout'=>'layouts.main');
        return view('collection.payment_recieved_summary', $data);
    }
    public function paymentrecieved_status(Request $request){
        try {
             $this->validate($request,
            [
                'status'=>'required'
            ],
            [
                'status.required'=>'This field is required'
            ]
            );
            $up_hr=Payment_Received::where('tax_invoice_id',$request->input('id'))->update([
                'status'=>$request->input('status')
            ]);
            if($up_hr==NULL){
                return redirect('/collection/paymentrecieved/summary')->with('error','some error occurred')->withInput();
            }else{
                return redirect('/collection/paymentrecieved/summary')->with('success','Successfully Status updated.');
            }
        } catch (Exception $e) {
             return redirect('/collection/paymentrecieved/summary')->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }
    public function payment_recieved_summary_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $status=$request->input('status');
        
        // DB::enableQueryLog();
        $bill = Tax_Invoice::leftjoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
            ->leftJoin('party','tax_invoice.party_id', 'party.id')
            ->leftjoin('tax','coll__payment_recieved.tax_invoice_id','tax.tax_invoice_id')
            ->leftjoin('internal_order','internal_order.id','tax.io_id')
            ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
            ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','coll__payment_recieved.tax_invoice_id')
            ->where('coll__payment_recieved.status',$status)
            ->select('tax_invoice.id',
            'tax_invoice.invoice_number',DB::raw('DATE_FORMAT(tax_invoice.date,"%d-%m-%Y") as tax_date'),'party.partyname',DB::raw('group_concat(Distinct(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as item_name'),'tax.qty','tax.rate','tax_invoice.total_amount',DB::raw('(IFNULL(
            (SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
            WHERE p.tax_invoice_id = tax_invoice.id
                GROUP BY p.tax_invoice_id) ,0 )
            ) AS amt_recieved'),
            DB::raw('tax_invoice.total_amount - (
                IFNULL((
            SELECT
                SUM(p.pr_amount)
            FROM
                coll__payment_recieved p
            WHERE
                p.tax_invoice_id = tax_invoice.id
            GROUP BY
                p.tax_invoice_id
            ) ,
                    0
                )
            ) AS balance_amt'),'coll__ti_payment_date.payment_date','coll__payment_recieved.status')->HavingRaw('Sum(pr_amount) > 0')
            ->groupby('coll__payment_recieved.tax_invoice_id')
            // ->get()
        ;
            // print_r( DB::getQueryLog());die();

        if(!empty($serach_value))
        {
            $bill->where(function($query) use ($serach_value){
                $query->where('party.partyname','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%")
                ->orwhere('internal_order.other_item_name','like',"%".$serach_value."%")
                ;
            });
                        
        }
        $count = count($bill->get());
        $bill = $bill->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['tax_invoice.id','tax_invoice.invoice_number','coll__payment_recieved.status','tax_date','party.partyname','item_name','tax.qty','tax.rate','tax_invoice.total_amount','amt_recieved','balance_amt'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $bill->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        $bill->orderBy('tax_invoice.id','desc');
        $bill= $bill->get();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $bill; 
        return json_encode($array);
    }

    public function bytax_details($tax_id){
        $data=array('layout'=>'layouts.main','tax_id'=>$tax_id);
        return view('collection.bytax_details_recievable', $data);
    }
    public function bytax_details_api($tax_id,Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        // DB::enableQueryLog();
        $bill = Payment_Received::where('coll__payment_recieved.tax_invoice_id',$tax_id)->leftjoin('tax_invoice','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
            ->leftjoin('mode_of_payment','mode_of_payment.id','coll__payment_recieved.mop_id')
            ->select('coll__payment_recieved.id','coll__payment_recieved.pr_date','coll__payment_recieved.pr_amount','mode_of_payment.value','coll__payment_recieved.advice_upload','coll__payment_recieved.deduction','coll__payment_recieved.reason_for_deduction','tax_invoice.invoice_number')
            // ->get()
        ;
            // print_r( DB::getQueryLog());die();

        if(!empty($serach_value))
        {
            $bill->where(function($query) use ($serach_value){
                $query->where('pr_date','LIKE',"%".$serach_value."%")
                ->orwhere('pr_amount','like',"%".$serach_value."%")
                ->orwhere('mode_of_payment.value','like',"%".$serach_value."%")
                ->orwhere('coll__payment_recieved.deduction','like',"%".$serach_value."%")
                ->orwhere('coll__payment_recieved.reason_for_deduction','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%")
                ;
            });
                        
        }
        $count = count($bill->get());
        $bill = $bill->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['coll__payment_recieved.id','coll__payment_recieved.pr_date','coll__payment_recieved.pr_amount','mode_of_payment.value','coll__payment_recieved.advice_upload','coll__payment_recieved.deduction','coll__payment_recieved.reason_for_deduction','tax_invoice.invoice_number'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $bill->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        $bill->orderBy('coll__payment_recieved.id','desc');
        $bill= $bill->get();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $bill; 
        return json_encode($array);
    }
    public function collection_engine(){
        $options = PaymentStatus::all();
        $data=array('layout'=>'layouts.main','payment_status'=>$options);
        return view('collection.collection_engine', $data);
    }
    public function collection_engine_api(Request $request){
       $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $bill = Payment_Date::leftJoin('tax_invoice','tax_invoice.id','coll__ti_payment_date.tax_invoice_id')
        ->leftjoin('party','party.id','tax_invoice.party_id')
        ->leftjoin('party_reference','party_reference.id','party.reference_name')
        ->leftjoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
        // ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','coll__payment_recieved.tax_invoice_id')
        // ->where('party.id',1)
        ->where('coll__payment_recieved.status',"!=",'closed')
        ->orwhere('coll__payment_recieved.id',"=",NULL)
        ->select('party.id','party_reference.referencename','party.partyname','party.contact_person','party.contact','party.email',
        DB::raw('group_concat(DISTINCT(invoice_number)) as invoice_number'),
        DB::raw('SUM((total_amount)) as total_amount'),
        DB::raw('IFNULL(SUM((coll__payment_recieved.pr_amount)),"0") as payment_received'),

                DB::raw('group_concat(DISTINCT(IFNULL(
                    (SELECT (p.payment_date) FROM coll__ti_payment_date p 
                    WHERE p.tax_invoice_id = tax_invoice.id
                        GROUP BY p.payment_date) ,"-" )) 
                    ) AS payment_date'),DB::raw('(
                        (SELECT DISTINCT(p.status) FROM coll__payment_recieved p 
                        WHERE p.tax_invoice_id = tax_invoice.id
                            GROUP BY p.tax_invoice_id ) 
                        ) AS status'),
                    DB::raw('(IFNULL(SUM((total_amount)),"0")  - IFNULL(SUM((coll__payment_recieved.pr_amount)),"0")) AS left_amt')
       
        )->WhereRaw('(total_amount - (
            IFNULL((
        SELECT
            SUM(p.pr_amount)
        FROM
            coll__payment_recieved p
        WHERE
            p.tax_invoice_id = tax_invoice.id
        GROUP BY
            p.tax_invoice_id
        ) ,
                    0
                ) 
            )) > 0')

        ->groupBy('coll__ti_payment_date.party_id')

        // ->groupBy('party.reference_name')
            // ->get()
        ;
            // print_r( DB::getQueryLog());die();

        if(!empty($serach_value))
        {
            $bill->where(function($query) use ($serach_value){
                $query->where('party_reference.referencename','LIKE',"%".$serach_value."%")
                ->orwhere('party.partyname','like',"%".$serach_value."%")
                ->orwhere('party.contact_person','like',"%".$serach_value."%")
                ->orwhere('party.contact','like',"%".$serach_value."%")
                ->orwhere('party.email','like',"%".$serach_value."%")
                ->orwhere('invoice_number','like',"%".$serach_value."%")
                ;
            });
                        
        }
        $count = count($bill->get());
        $bill = $bill->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['party.id','party_reference.referencename','party.partyname','party.contact_person','party.contact','party.email','invoice_number','total_amount','left_amt','payment_date'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $bill->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $bill->orderBy('party.id','desc');
        $bill= $bill->get();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $bill; 
        return json_encode($array); 
    }
    public function submit_collection_status(Request $request){
        try {
            $error =[];
            if(empty($request->input('pr_status'))){
                $error = array_merge($error,array('Payment received status is Required'));
            }
            $options = PaymentStatus::where('id',$request->input('pr_status'))->get()->first()['name'];
            if($options == 'Call Back'){
                if(empty($request->input('cb_date'))){
                    $error = array_merge($error,array('Date is Required'));
                }
                if(empty($request->input('cb_time'))){
                    $error = array_merge($error,array('Time is Required'));
                }
            }else if($options == 'Dispute' || $options == "Not required" || $options == "Did not call"){
                if(empty($request->input('dis_reason'))){
                    $error = array_merge($error,array('Reason is Required'));
                }
            }else if($options == 'Ringing/Not Contactable'){
                if(empty($request->file('upload_call_logs'))){
                    $error = array_merge($error,array('Screenshot is Required'));
                }
            }else if($options == 'Promise To Pay'){
                if(empty($request->input('ptp_date'))){
                    $error = array_merge($error,array('Promise to Pay is Required'));
                }
            }else if($options == 'Follow up Done'){
                if(empty($request->input('f_remark'))){
                    $error = array_merge($error,array('Follow up Remark is Required'));
                }
            }
            
            if(count($error)>0){
                $data = [
                'error'=>$error];
                return response()->json($data);
            }

            $file = $request->file('upload_call_logs');
            $job_image ='';
            if(isset($file) || $file != null){
                $destinationPath = public_path().'/upload/Recievable/';
                $filenameWithExt = $request->file('upload_call_logs')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('upload_call_logs')->getClientOriginalExtension();
                $job_image = $filename.'_'.time().'.'.$extension;
                $path = $file->move($destinationPath, $job_image);
            }else{
                $job_image = NULL;
            }

            $timestamp = date('Y-m-d G:i:s');
            if($request->input('cb_date')){
                $dt=date("Y-m-d",strtotime($request->input('cb_date')));
            }
            else{
                $dt=NULL;
            }
            $reciept = Follow_up_Status::InsertGetId([        
                    'id'=>null,
                    'party_id'=> $request->input('party_id'),
                    'payment_status_id'=>$request->input('pr_status'),
                    'cb_date'=>$dt,
                    'cb_time'=>$request->input('cb_time'),
                    'reason'=>$request->input('dis_reason'),
                    'call_log_upload'=>$job_image,
                    'ptp_date'=>$request->input('ptp_date'),
                    'remark'=>$request->input('f_remark'),
                    'created_by'=>Auth::id(),
                    'created_at'=>$timestamp
                ]);
            
                if($reciept == null){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));
                }else{
                    $msg =['Successfully Follow Up Status Updated.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
                return response()->json($data);
        } catch (\Illuminate\Database\QueryException $ex) {
             return redirect('/collection/engine/followupsheet')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function report(){
       
        $data=array('layout'=>'layouts.main');
        return view('collection.report', $data);
    }
    public function report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $party = $request->input('party');
        $ref = $request->input('ref');
        // print_r($party);
        // print_r("x");
        // print_r($ref);
        // die();
        // DB::enableQueryLog();

        $bill=Payment_Received::leftjoin('tax_invoice','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
            ->leftJoin('party','tax_invoice.party_id', 'party.id')
            ->leftJoin('party_reference','party.reference_name','party_reference.id')
            ->leftjoin('tax','tax_invoice.id','tax.tax_invoice_id')
            ->leftjoin('internal_order','internal_order.id','tax.io_id')
            ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
            ->leftJoin('io_type','job_details.io_type_id','io_type.id')
            ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
            ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','tax_invoice.id')
            // ->where('coll__payment_recieved.status',"!=",'closed')
            ->select('tax_invoice.id',
            'coll__payment_recieved.status',
            'tax_invoice.invoice_number',
            'party_reference.referencename',
            'party.partyname',
            DB::raw('group_concat(DISTINCT(io_number)) as io_number'),
            DB::raw('group_concat(DISTINCT(job_details.job_date)) as job_date'),
            DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as item_name'),
            'tax_invoice.total_amount',
            DB::raw('(IFNULL(
            (SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
            WHERE p.tax_invoice_id = tax_invoice.id
                GROUP BY p.tax_invoice_id) ,0 ) 
            ) AS amt_recieved'),
            DB::raw('tax_invoice.total_amount - (
                IFNULL((
            SELECT
                SUM(p.pr_amount)
            FROM
                coll__payment_recieved p
            WHERE
                p.tax_invoice_id = tax_invoice.id
            GROUP BY
                p.tax_invoice_id
            ) ,
                    0
                ) 
            ) AS balance_amt'),'coll__ti_payment_date.payment_date')
            ->havingRaw('(coll__payment_recieved.status = "closed") OR 
            tax_invoice.total_amount - (
                IFNULL((
            SELECT
                SUM(p.pr_amount)
            FROM
                coll__payment_recieved p
            WHERE
                p.tax_invoice_id = tax_invoice.id
            GROUP BY
                p.tax_invoice_id
            ) ,0) 
            )=0')
            ->groupBy('tax_invoice.id')
            // ->get()
        ;
            // print_r( DB::getQueryLog());die();

    
        if(!empty($serach_value))
        {
            $bill->where(function($query) use ($serach_value){
                $query->where('party_reference.referencename','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','like',"%".$serach_value."%")
                ->orwhere('internal_order.io_number','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%")
                ->orwhere('internal_order.other_item_name','like',"%".$serach_value."%")
                ;
            });
                        
        }
        $count = count($bill->get());
        $bill = $bill->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['tax_invoice.id','tax_invoice.invoice_number', 'party_reference.referencename','io_number','party.partyname','item_name','tax_invoice.total_amount','amt_recieved','balance_amt'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $bill->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        $bill->orderBy('tax_invoice.id','desc');
        $bill= $bill->get();

       
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $bill; 
        return json_encode($array);
    }

    public function dailyreport(){
       
        $mop = DB::table('mode_of_payment')->get();
        $Reference = Reference::get()->toArray();
        $data=array('layout'=>'layouts.main','mop'=>$mop,'reference'=>$Reference);
        return view('email.dailyreport', $data);
    }
    public function dailyreport_api(Request $request){
        $date=$request->input('date');
        $ref=$request->input('ref');
        $party=$request->input('party');
        //IO engaged today
        $io_today=InternalOrder::leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->select('io_number','party_reference.referencename',
        DB::raw('concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name) as item_name'),
        'job_details.qty','rate_per_qty','job_size',DB::raw('(job_details.qty * rate_per_qty) as amount')
        );

        //Party Billed Today
       $tax_today=Tax_Invoice::leftJoin('party','tax_invoice.party_id', 'party.id')
       ->leftJoin('party_reference','party.reference_name','party_reference.id')
       ->leftjoin('tax','tax_invoice.id','tax.tax_invoice_id')
       ->leftjoin('internal_order','internal_order.id','tax.io_id')
       ->leftjoin('waybill','waybill.invoice_id','tax_invoice.id')
       ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
       ->select(DB::raw('SUM(tax.qty) as tot_qty'),DB::raw('SUM(tax.rate) as tot_rate'),
       DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other",":",""),internal_order.other_item_name))) as item_name'),'waybill_status','waybill.waybill_number',
       'tax_invoice.total_amount','invoice_number','party.partyname as pname',DB::raw('(DATE_FORMAT(tax_invoice.date ,"%d-%m-%Y")) as date'),DB::raw('(select SUM(tt.total_amount) from tax_invoice tt 
                            LEFT JOIN party p ON p.`id` = tt.`party_id`
                    where tt.date ="'.date('Y-m-d',strtotime($date)).'" and tt.party_id =`tax_invoice`.`party_id` GROUP by p.gst )AS wa_s'),'waybill.id as waybill_id')
       ->groupBy('tax_invoice.id');

       //Payment Received Today

       $pay_today=Tax_Invoice::leftjoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
       ->leftJoin('party','tax_invoice.party_id', 'party.id')
       ->leftJoin('party_reference','party.reference_name','party_reference.id')
       ->leftjoin('mode_of_payment','coll__payment_recieved.mop_id','mode_of_payment.id')
       ->select('party.partyname',
      
        'deduction','advice_upload','mode_of_payment.value as mop','reason_for_deduction',
       DB::raw('(IFNULL(
       (SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
       WHERE p.tax_invoice_id = tax_invoice.id
           GROUP BY p.tax_invoice_id) ,0 )
       ) AS amt_recieved'))
       ->groupby('coll__payment_recieved.tax_invoice_id')
       // ->get()
            ;
            //MIR today
        $mir_today= Material_inwarding::leftjoin('pur_grn','pur_grn.material_inward_id','material_inward.id')->select(
            'material_inward_number',
            'company',
            'item_name',
            'qty',
            'date',
            'driver_name',
                DB::raw('Group_Concat(DISTINCT(pur_grn.grn_number)) as pur_grn_no'),
                DB::raw('Group_Concat(DISTINCT(pur_grn.received_by)) as received_by')
        )->groupBy('material_inward.id');

             //Material Dispatch Today

            $mat_dis_today=Delivery_challan::leftJoin('challan_per_io','challan_per_io.delivery_challan_id','delivery_challan.id')
            ->leftJoin('party','delivery_challan.party_id', 'party.id')
            //    ->leftJoin('gatepass','gatepass.challan_id','delivery_challan.id')
            ->leftJoin('gatepass', function($join) {
                $join->on('delivery_challan.id', '=', 'gatepass.challan_id');
                $join->where('gatepass.challan_type', '=', 'PPML/DCN/');
                })
                ->leftJoin('internal_order','internal_order.id','challan_per_io.io')
                ->leftJoin('item_category',function($join){
                    $join->on('internal_order.item_category_id','=','item_category.id');
                })
            ->leftJoin('material_outward','material_outward.gatepass','gatepass.id')
            ->leftJoin('waybill','waybill.challan_id','delivery_challan.id')
            ->select(DB::raw('group_concat(DISTINCT(challan_number)) as challan_number'),DB::raw('group_concat(DISTINCT(partyname)) as partyname'),
            DB::raw('group_concat((concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as good_desc'),
            DB::raw('SUM(challan_per_io.good_qty) as good_qty'),'waybill_number','waybill_status as waybill_status',
            DB::raw('group_concat(DISTINCT(gatepass.gatepass_number)) as gatepass_number'),
            DB::raw('group_concat(DISTINCT(material_outward.material_outward_number)) as material_outward_number'),
            DB::raw('(select SUM(dc.total_amount) from delivery_challan dc 
                                LEFT JOIN party p ON p.`id` = dc.`party_id`
                                where dc.date ="'.date('Y-m-d',strtotime($date)).'" and dc.party_id =`delivery_challan`.`party_id` GROUP by p.gst )AS wa_s'),
                        'waybill.id as waybill_id'
                )->groupBy('delivery_challan.id');

            $mat_idc_today=Internal_DC::leftJoin('gatepass', function($join) {
                $join->on('internal_dc.id', '=', 'gatepass.challan_id');
                $join->where('gatepass.challan_type', '=', 'PPML/IDC/');
                })
            ->leftJoin('material_outward','material_outward.gatepass','gatepass.id')
            
            ->select('idc_number as challan_number','internal_dc.dispatch_to as partyname',DB::raw('group_concat(DISTINCT(internal_dc.item_desc)) as good_desc'),
            DB::raw('SUM(internal_dc.item_qty) as good_qty'),
            DB::raw('group_concat(DISTINCT(gatepass.gatepass_number)) as gatepass_number'),
            DB::raw('group_concat(DISTINCT(material_outward.material_outward_number)) as material_outward_number'))->groupBy('internal_dc.id');

            // $prod_today=DailyPlateProcess::where('job_details.is_supplied_plate','Press')
            //     ->where('is_plate_size','<>',0)
            //     ->leftjoin('job_card','prod__dailyprocess_planning.jc_id','job_card.id')
            //     ->leftjoin('internal_order','internal_order.id','job_card.io_id')
            //     ->leftjoin('prod__dailyplate_report','prod__dailyplate_report.plan_id','prod__dailyprocess_planning.id')
            //     ->leftjoin('job_details','job_details.id','internal_order.job_details_id')
            //     ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
            //     ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
            //     ->leftJoin('prod__machine_name','prod__machine_name.id','prod__dailyprocess_planning.machine_id')

            //     ->select(
            //     'party_reference.referencename','job_card.creative_name','prod__machine_name.name as machine',
            //     DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
            //     DB::raw('SUM(CASE WHEN prod__dailyplate_report.actual Is NULL THEN "0" ELSE prod__dailyplate_report.actual END) AS actual')
            //     )
            //      ->groupBy('prod__dailyplate_report.plan_id')
            //     ;
            
            //production done today
                    $dailyprepresslog =DailyPlateProcess::where('job_details.is_supplied_plate','Press')
                            ->where('is_plate_size','<>',0)
                            ->leftjoin('job_card','prod__dailyprocess_planning.jc_id','job_card.id')
                            ->leftjoin('internal_order','internal_order.id','job_card.io_id')
                            ->leftjoin('prod__dailyplate_report','prod__dailyplate_report.plan_id','prod__dailyprocess_planning.id')
                            ->leftjoin('job_details','job_details.id','internal_order.job_details_id')
                            ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
                            ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
                            ->leftJoin('element_feeder','element_feeder.job_card_id','prod__dailyprocess_planning.jc_id')
                            ->leftjoin('element_type','prod__dailyprocess_planning.element_id','element_type.id')
                            ->select('job_card.id','job_card.job_number',
                            'internal_order.reference_name',
                            'internal_order.item_category_id',
                            'party_reference.referencename','job_card.creative_name',
                            'element_type.name as element_name','element_type.id as element_id',
                            DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
                            'element_feeder.plate_size as e_plate_size',
                            DB::raw('(element_feeder.plate_sets * element_feeder.front_color) + (element_feeder.plate_sets * element_feeder.back_color) as total_plates'),
                            'prod__dailyprocess_planning.no_of_plates as planned_plates',
                            DB::raw('SUM(CASE WHEN prod__dailyplate_report.actual Is NULL THEN "0" ELSE prod__dailyplate_report.actual END) AS actual'),
                            DB::raw('SUM(CASE WHEN prod__dailyplate_report.wastage Is NULL THEN "0" ELSE prod__dailyplate_report.wastage END) AS wastage'),
                            DB::raw('group_concat(DISTINCT(CASE WHEN prod__dailyplate_report.reason Is NULL THEN "" ELSE prod__dailyplate_report.reason END)) AS reason')
                            )
                            ->groupBy('prod__dailyplate_report.plan_id')
                            ;
                    $dailyprocesslog =PressDailyProcess::where('job_details.is_supplied_plate','Press')
                            ->leftjoin('job_card','prod__press_dailyplanning.jc_id','job_card.id')
                            ->leftjoin('internal_order','internal_order.id','job_card.io_id')
                            ->leftJoin('prod__machine_name','prod__machine_name.id','prod__press_dailyplanning.machine_id')
                            ->leftjoin('prod__press_dailyplate_report','prod__press_dailyplate_report.plan_id','prod__press_dailyplanning.id')
                            ->leftjoin('job_details','job_details.id','internal_order.job_details_id')
                            ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
                            ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
                            ->leftJoin('element_feeder','element_feeder.job_card_id','prod__press_dailyplanning.jc_id')
                            ->leftjoin('element_type','prod__press_dailyplanning.element_id','element_type.id')
                            ->select('job_card.id','job_card.job_number',
                            'internal_order.reference_name',
                            'internal_order.item_category_id',
                            'party_reference.referencename','job_card.creative_name',
                            'element_type.name as element_name','element_type.id as element_id',
                            DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
                            'element_feeder.plate_size as e_plate_size',
                            'prod__press_dailyplanning.left_imp',
                            'prod__press_dailyplanning.id as plan_id',
                            'prod__press_dailyplanning.planned_date',
                            DB::raw('DATE_FORMAT(prod__press_dailyplanning.planned_date, "%d-%m-%Y") as planneddate'),
                            'element_feeder.plate_sets as e_plate_set',
                            'element_feeder.front_color as e_front_color',
                            'element_feeder.back_color as e_back_color',
                            DB::raw('(element_feeder.plate_sets * element_feeder.impression_per_plate) as total_plates'),
                            'prod__press_dailyplanning.no_of_imp as planned_plates',
                            DB::raw('SUM(CASE WHEN prod__press_dailyplate_report.actual_11am Is NULL THEN "0" ELSE prod__press_dailyplate_report.actual_11am END) AS actual_11am'),
                            DB::raw('SUM(CASE WHEN prod__press_dailyplate_report.actual_2pm Is NULL THEN "0" ELSE prod__press_dailyplate_report.actual_2pm END) AS actual_2pm'),
                            DB::raw('SUM(CASE WHEN prod__press_dailyplate_report.actual_6pm Is NULL THEN "0" ELSE prod__press_dailyplate_report.actual_6pm END) AS actual_6pm'),
                            'prod__machine_name.name as machine',
                            DB::raw('SUM(CASE WHEN prod__press_dailyplate_report.actual Is NULL THEN "0" ELSE prod__press_dailyplate_report.actual END) AS actual'),
                            DB::raw('group_concat(DISTINCT(CASE WHEN prod__press_dailyplate_report.reason_11am Is NULL THEN "-" ELSE prod__press_dailyplate_report.reason_11am END)) AS reason_11am'),
                            DB::raw('group_concat(DISTINCT(CASE WHEN prod__press_dailyplate_report.reason_2pm Is NULL THEN "-" ELSE prod__press_dailyplate_report.reason_2pm END)) AS reason_2pm'),
                            DB::raw('group_concat(DISTINCT(CASE WHEN prod__press_dailyplate_report.reason_6pm Is NULL THEN "-" ELSE prod__press_dailyplate_report.reason_6pm END)) AS reason_6pm')
                            )
                            ->groupBy('prod__press_dailyplanning.id')
                            ;
                    $design_order =DesignOrder::leftjoin('party_reference','design__order.reference_name','party_reference.id')
                        ->leftJoin('internal_order','internal_order.id','design__order.io')
                        ->leftJoin('item_category','item_category.id','design__order.item')
                        ->leftJoin('design__work_allotment','design__work_allotment.design_id','design__order.id')
                        ->leftJoin('employee__profile','employee__profile.id','design__work_allotment.work_emp_id')
                        ->select('design__order.do_number','party_reference.referencename','internal_order.io_number',
                        DB::raw('(CASE WHEN item_category.name = "Other" THEN CONCAT(item_category.name,":",IFNULL(design__order.other_item_desc,"")) ELSE item_category.name END) as itemname'),'design__order.no_pages','design__order.left_pages','design__order.other_item_desc','design__order.creative','employee__profile.name as alloted');
                    $purchase = PurchaseOrder::leftjoin('vendor','vendor.id','pr_purchase_order.vendor_id')
                        ->leftjoin('pur_purchase_req','pur_purchase_req.id','pr_purchase_order.indent_num_id')
                        ->leftjoin('pr_purchase_order_details','pr_purchase_order.id','pr_purchase_order_details.pr_po_id')
                        ->leftjoin('stock','stock.id','pr_purchase_order_details.item_name_id')
                        ->leftjoin('tax_per_applicable','tax_per_applicable.id','pr_purchase_order_details.tax_percent_id')
                        
                        ->select('pr_purchase_order.po_num as po_number','pur_purchase_req.indent_num as pr_no','vendor.name as vendor',DB::raw('Group_Concat(stock.item_name) as item_name'),DB::raw('Sum(pr_purchase_order_details.item_qty) as qty'),DB::raw('sum(pr_purchase_order_details.item_rate) as rate'),DB::raw('(sum(pr_purchase_order_details.item_qty) * sum(pr_purchase_order_details.item_rate))+ (sum(pr_purchase_order_details.item_qty) * sum(pr_purchase_order_details.item_rate) * sum(tax_per_applicable.value/100)) as amount'))->groupBy('pr_purchase_order.id');

            $fromDate= date('Y-m',strtotime($date));
            $dates=date_create($fromDate);

            $finan=FinancialYear::where('from', '<=', $fromDate)
            ->where('to', '>=', $fromDate)->first();
            
            $without_tax=Tax_Invoice::leftjoin('tax','tax_invoice.id','tax.tax_invoice_id')->where('financial_year', $finan['financial_year'])
            ->select( DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'))
            ->get();
            $with_tax=Tax_Invoice::where('financial_year', $finan['financial_year'])
            ->select( DB::raw('SUM(total_amount) as amount'))
            ->get();

            //monthly 
            $mon=date('m',strtotime($date));
            $yr=date('Y',strtotime($date));
            $monthly_without_tax=Tax_Invoice::leftjoin('tax','tax_invoice.id','tax.tax_invoice_id')->whereMonth('tax_invoice.date', $mon)
            ->whereYear('tax_invoice.date', $yr)
            ->select( DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'))
            ->get();
        $monthly_with_tax=Tax_Invoice::whereMonth('tax_invoice.date', $mon)
        ->select( DB::raw('SUM(total_amount) as amount'))
        ->get();  

        if(!empty($date))
            {
                $tax_today->where(function($query) use ($date){
                    $query->where('tax_invoice.date','=',date('Y-m-d',strtotime($date)));
                }); 

                $io_today->where(function($query) use ($date){
                    $query->where('job_details.job_date','=',date('Y-m-d',strtotime($date)));
                }); 

                $pay_today->where(function($query) use ($date){
                    $query->where('coll__payment_recieved.pr_date','=',date('Y-m-d',strtotime($date)));
                }); 
                $mir_today->where(function($query) use ($date){
                    $query->where('date','=',date('Y-m-d',strtotime($date)));
                }); 
                $mat_dis_today->where(function($query) use ($date){
                    $query->where('delivery_date','=',date('Y-m-d',strtotime($date)));
                }); 
                $mat_idc_today->where(function($query) use ($date){
                    $query->where('internal_dc.date','=',date('Y-m-d',strtotime($date)));
                }); 
                // $prod_today->where(function($query) use ($date){
                //     $query->where('prod__dailyprocess_planning.planned_date','=',date('Y-m-d',strtotime($date)));
                // }); 
                $dailyprepresslog->where(function($query) use ($date){
                    $query->where('prod__dailyprocess_planning.planned_date','=',date('Y-m-d',strtotime($date)));
                });
                $dailyprocesslog->where(function($query) use ($date){
                    $query->where('prod__press_dailyplanning.planned_date','=',date('Y-m-d',strtotime($date)));
                });
                $design_order->where(function($query) use ($date){
                    $query->whereRaw('DATE_FORMAT(design__order.created_at,"%Y-%m-%d") = "'.date('Y-m-d',strtotime($date)).'"');
                });
                $purchase->where(function($query) use ($date){
                    $query->whereRaw('pr_purchase_order.po_date = "'.date('Y-m-d',strtotime($date)).'"');
                });

            }
            if($ref!=0)
            {
                $tax_today->where(function($query) use ($ref){
                    $query->where('party.reference_name','=',$ref);
                });    
                $io_today->where(function($query) use ($ref){
                    $query->where('internal_order.reference_name','=',$ref);
                });      
                $pay_today->where(function($query) use ($ref){
                    $query->where('party_reference.id','=',$ref);
                }); 

                $mat_dis_today->where(function($query) use ($ref){
                    $query->where('delivery_challan.reference_name','=',$ref);
                });
                // $prod_today->where(function($query) use ($ref){
                //     $query->where('reference_name','=',$ref);
                // });  
                $dailyprepresslog->where(function($query) use ($ref){
                    $query->where('party_reference.id','=',$ref);
                });
                $dailyprocesslog->where(function($query) use ($ref){
                    $query->where('party_reference.id','=',$ref);
                });
                $design_order->where(function($query) use ($ref){
                    $query->where('design__order.reference_name','=',$ref);
                });

            }
            if($party!=0)
            {
                $tax_today->where(function($query) use ($party){
                    $query->where('tax_invoice.party_id','=',$party);
                }); 
                $pay_today->where(function($query) use ($party){
                    $query->where('tax_invoice.party_id','=',$party);
                });  
                $mat_dis_today->where(function($query) use ($party){
                    $query->where('party_id','=',$party);
                });              
            }
            $tax_today=$tax_today->get();
            $io_today=$io_today->get();
            $pay_today=$pay_today->get();
            $mir_today=$mir_today->get();
            $mat_dis_today=$mat_dis_today->get();
            $mat_idc_today=$mat_idc_today->get();
            // $prod_today=$prod_today->get();
            $dailyprepresslog = $dailyprepresslog->get();
            $dailyprocesslog = $dailyprocesslog->get();
            $design_order = $design_order->get();
            $purchase =$purchase->get();
     //    print_r($mat_dis_today);die;
            $data=[
                'tax_today'=>$tax_today,
                'io_today'=>$io_today,
                'pay_today'=>$pay_today,
                'mir_today'=>$mir_today,

                'mat_dis_today'=>$mat_dis_today,
                'mat_idc_today'=>$mat_idc_today,
                // 'prod_today'=>$prod_today,
                'dailyprepresslog'=>$dailyprepresslog,
                'dailyprocesslog'=>$dailyprocesslog,
                'design_order'=>$design_order,
                'purchase'=>$purchase,

                'without_tax'=>$without_tax,
                'with_tax'=>$with_tax,
                'monthly_without_tax'=>$monthly_without_tax,
                'monthly_with_tax'=>$monthly_with_tax
            ];

            return $data;

    }
    public function fms(){
        $users= Party::select('id','partyname as name')->get();
        $data=array('users' => $users,'layout'=>'layouts.main');
        return view('collection.fms', $data);
    }
    public function fms_api(Request $request){
        $search = $request->input('search');
        $user_name = $request->input('user_name');
        $month = $request->input('month');
        $year = $request->input('year');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $user = Follow_up_Status::select('party_id')->get()->toArray();
        $a_date = date('Y-m-d');
        $date = new DateTime($a_date);
        $date->modify('last day of this month');
        $last_day = $date->format('d'); 
        if ($month) {
            $month=date('m',strtotime($month));
            $month_year = date('Y-'.$month);
            $date = new DateTime($month_year);
            $date->modify('last day of this month');
            $last_day = $date->format('d'); 
        }else{
            $month_year = date('Y-m');
            $date = new DateTime($month_year);
            $date->modify('last day of this month');
            $last_day = $date->format('d'); 
        }
        
       
        DB::enableQueryLog();
        $sort_data_query = array();
        // print_r($last_day);die;
       for ($j = 1; $j <= $last_day ; $j++) {
            // $emp_id = $user[$j]['party_id'];
            $mdate = '"'.$month_year.'-'.$j.'"';
            $d='"'.$j.'/'.$month_year.'"';
            $query[$j] = "IFNULL((SELECT coll__payment_status.short as status FROM coll__followup_status att 
            left join coll__payment_status on coll__payment_status.id=att.payment_status_id
            WHERE att.party_id = coll__followup_status.party_id AND DATE(att.created_at) = ".$mdate."),'') as d".$j." ";

        }
        $query = join(",",$query);

        $date = Carbon::now();
        if ($month) {
            $month_name = $date->format($month);
        }else{
            $month_name = date('m');  
        }
        $api_data =  Payment_Date::leftJoin('tax_invoice','tax_invoice.id','coll__ti_payment_date.tax_invoice_id')
        ->leftjoin('party','party.id','tax_invoice.party_id')
        ->leftjoin('party_reference','party_reference.id','party.reference_name')
        ->leftjoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
        // ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','coll__payment_recieved.tax_invoice_id')
        // ->where('party.id',1)
        ->where('coll__payment_recieved.status',"!=",'closed')
        ->orwhere('coll__payment_recieved.id',"=",NULL)
        ->leftJoin('coll__followup_status','coll__followup_status.party_id','party.id')
        ->leftJoin('coll__payment_status','coll__payment_status.id','coll__followup_status.payment_status_id')
            // ->leftjoin('party_reference','party_reference.id','party.reference_name')
        // ->leftJoin('tax_invoice','tax_invoice.party_id','party.id')
        // ->leftjoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
        // ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','coll__payment_recieved.tax_invoice_id')
            // ->whereMonth('coll__followup_status.created_at', date($month_name))
            // ->whereMonth('coll__payment_recieved.pr_date', date($month_name))
            // ->where('tax_invoice.party_id','!=',NULL)
            ->select(
                'party.partyname as name',
                'coll__followup_status.party_id',
                'coll__followup_status.payment_status_id',
                DB::raw('IFNULL(DATE_FORMAT(coll__followup_status.created_at,"%Y-%m"),'.$month_year.') as date'),
                'coll__payment_status.short as status',
                // DB::raw('concat(store.name,"-",store.store_type) as store_name'),
                'party_reference.referencename','party.contact_person','party.contact','party.email',
        DB::raw('group_concat(DISTINCT(invoice_number)) as invoice_number'),
        DB::raw('SUM((total_amount)) as total_amount'),
        DB::raw('IFNULL(SUM((coll__payment_recieved.pr_amount)),"0") as payment_received'),
DB::raw('(IFNULL(SUM((total_amount)),"0")  - IFNULL(SUM((coll__payment_recieved.pr_amount)),"0")) AS left_amt'),

                DB::raw('group_concat(DISTINCT(IFNULL(
                    (SELECT (p.payment_date) FROM coll__ti_payment_date p 
                    WHERE p.tax_invoice_id = tax_invoice.id
                        GROUP BY p.payment_date) ,"-" )) 
                    ) AS payment_date'),
        // DB::raw("group_concat(CASE WHEN coll__ti_payment_date.payment_date Is NULL THEN '-' ELSE coll__ti_payment_date.payment_date END) as payment_date"),
        // DB::raw("group_concat(DATEDIFF(coll__ti_payment_date.payment_date,coll__payment_recieved.pr_date)) as overdue_by"),
       
        
                DB::raw($query)
            )->groupBy('party.id');

             if(!empty($serach_value)) {
                $api_data->where(function($query) use ($serach_value){
                    $query->where('party.partyname','like',"%".$serach_value."%")
                    ->orwhere('coll__payment_status.name','like',"%".$serach_value."%")
                    ->orwhere('coll__payment_status.short','like',"%".$serach_value."%")
                   ;
                });
            }
            if(isset($user_name) && !isset($month)) {
               $api_data->where(function($query) use ($user_name){
                        $query->where('coll__followup_status.party_id',$user_name);
                    });               
            }

            if(isset($user_name) && isset($month)) {
                $api_data->where(function($query) use ($user_name,$month){
                         $query->where('coll__followup_status.party_id',$user_name)->whereMonth('coll__followup_status.created_at', date($month));
                     });               
             }
            
            if(isset($month) && !isset($user_name)) {
               $api_data->where(function($query) use ($month){
                        $query->whereMonth('coll__followup_status.created_at', date($month))->orWhere('coll__followup_status.party_id','=',NULL);
                    });
            }

            if(isset($request->input('order')[0]['column'])) {

                $data = [
                    'coll__followup_status.id',
                    'coll__followup_status.party_id',
                    'party.name',
                    'coll__followup_status.payment_status_id',
                    'coll__followup_status.created_at'
                    // 'store_name'
                ];

                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $api_data->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else
                $api_data->orderBy('coll__followup_status.id','desc');
                $count = count( $api_data->get()->toArray());
                $api_data = $api_data->offset($offset)->limit($limit)->get()->toArray(); 
                $array['recordsTotal'] = $count;
                $array['recordsFiltered'] = $count;
                $array['month_year']=$month_year;
                $array['data'] = $api_data; 
                
                return json_encode($array);
    }
    public function details($party){
        $users= Party::where('id',$party)->select('id','partyname as name')->get()->first();
        $api_data = Party::leftJoin('coll__followup_status','coll__followup_status.party_id','party.id')
        ->leftJoin('coll__payment_status','coll__payment_status.id','coll__followup_status.payment_status_id')
        ->leftjoin('party_reference','party_reference.id','party.reference_name')
    ->leftJoin('tax_invoice','tax_invoice.party_id','party.id')
    ->leftjoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
    ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','coll__payment_recieved.tax_invoice_id')
        // ->whereMonth('coll__followup_status.created_at', date($month_name))
        ->where('coll__followup_status.party_id','=',$party)
        ->select(
            'party.partyname as name',
            'coll__followup_status.party_id',
            // 'coll__followup_status.payment_status_id',
            DB::raw('IFNULL(coll__followup_status.reason,"-") as reason'),
            DB::raw('IFNULL(coll__followup_status.cb_time,"-") as cb_time'),
            DB::raw('IFNULL(coll__followup_status.remark,"-") as remark'),
            DB::raw('IFNULL(coll__followup_status.call_log_upload,"-") as call_log_upload'),
            DB::raw('IFNULL(DATE_FORMAT(coll__followup_status.cb_date,"%d-%m-%Y"),"-") as cb_date'),
            DB::raw('IFNULL(DATE_FORMAT(coll__followup_status.ptp_date,"%d-%m-%Y"),"-") as ptp_date'),

            DB::raw('DATE_FORMAT(coll__followup_status.created_at,"%m-%Y") as date'),
            DB::raw('DATE_FORMAT(coll__followup_status.created_at,"%d %M %Y") as dates'),
            'coll__payment_status.short as status'
        )->groupBy('party.id','coll__followup_status.created_at')->get();
        $data_new=[];
        $i=0;
        foreach($api_data as $key){
            $key['dates']=$key['dates'].'@'.$i;
            $data_new[$key['date']][$key['dates']]['status']=$key['status'];
            $data_new[$key['date']][$key['dates']]['reason']=$key['reason'];
            $data_new[$key['date']][$key['dates']]['cb_time']=$key['cb_time'];
            $data_new[$key['date']][$key['dates']]['remark']=$key['remark'];
            $data_new[$key['date']][$key['dates']]['call_log_upload']=$key['call_log_upload'];
            $data_new[$key['date']][$key['dates']]['cb_date']=$key['cb_date'];
            $data_new[$key['date']][$key['dates']]['ptp_date']=$key['ptp_date'];
                $i++;
        }
        $data=array('users' => $users,'layout'=>'layouts.main','data_new'=>$data_new);
        return view('collection.details', $data);
    }
    public function details_api(Request $request , $party){
        $api_data = Party::leftJoin('coll__followup_status','coll__followup_status.party_id','party.id')
        ->leftJoin('coll__payment_status','coll__payment_status.id','coll__followup_status.payment_status_id')
        ->leftjoin('party_reference','party_reference.id','party.reference_name')
    ->leftJoin('tax_invoice','tax_invoice.party_id','party.id')
    ->leftjoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax_invoice.id')
    ->leftjoin('coll__ti_payment_date','coll__ti_payment_date.tax_invoice_id','coll__payment_recieved.tax_invoice_id')
        // ->whereMonth('coll__followup_status.created_at', date($month_name))
        ->where('coll__followup_status.party_id','=',$party)
        ->select(
            'party.partyname as name',
            'coll__followup_status.party_id',
            // 'coll__followup_status.payment_status_id',
            DB::raw('IFNULL(coll__followup_status.reason,"-") as reason'),
            DB::raw('IFNULL(coll__followup_status.cb_time,"-") as cb_time'),
            DB::raw('IFNULL(coll__followup_status.remark,"-") as remark'),
            DB::raw('IFNULL(coll__followup_status.call_log_upload,"-") as call_log_upload'),
            DB::raw('IFNULL(DATE_FORMAT(coll__followup_status.cb_date,"%d-%m-%Y"),"-") as cb_date'),
            DB::raw('IFNULL(DATE_FORMAT(coll__followup_status.ptp_date,"%d-%m-%Y"),"-") as ptp_date'),

            DB::raw('DATE_FORMAT(coll__followup_status.created_at,"%m-%Y") as date'),
            DB::raw('DATE_FORMAT(coll__followup_status.created_at,"%d %M %Y") as dates'),
            'coll__payment_status.name as status'
        )->groupBy('party.id','coll__followup_status.created_at')->get();
        $data_new=[];
        $i=0;
        foreach($api_data as $key){
            $key['dates']=$key['dates'].'@'.$i;
            $data_new[$key['date']][$key['dates']]['status']=$key['status'];
                $i++;
        }
            return ($data_new);
    }
}