<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Model\Waybill;
use App\Model\Tax_Invoice;
use App\Model\Tax_dispatch;
use App\Model\Tax;
use App\Model\Hsn;
use App\Model\Party;
use App\Model\Unit_of_measurement;
use App\Model\Payment;
use App\Model\Itemcategory;
use App\Model\JobDetails;
use App\Model\Reference;
use App\Model\InternalOrder;
use App\Model\Client_po;
use App\Model\Delivery_challan;
use App\Model\coll__payment_recieved;
use App\Model\Challan_per_io;
use Illuminate\Validation\Rule;
use App\Model\Settings;
use Auth;
use App\dkerp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Hash;
 
class ReportController extends Controller
{
    public function waybill_not_generated_list()
    {
        $data=array('layout'=>'layouts.main');
        return view('sections.waybill_not_generated', $data);   
    }
 
    public function waybill_not_generated_api(Request $request,$type)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        if($type=="challan")
        {
           
            $api_data= Delivery_challan::where('delivery_challan.is_active',1)
            ->leftJoin('party','delivery_challan.party_id','party.id')
            ->leftJoin('waybill','waybill.challan_id','delivery_challan.id')
            ->where('delivery_challan.waybill_status','=',1)
            ->where('waybill.id',NULL)
            ->select(
                DB::raw('group_concat(distinct(delivery_challan.id)) as challan_id'),
                DB::raw('SUM(delivery_challan.total_amount) as total_amount'),
             
                DB::raw('group_concat(distinct(DATE_FORMAT(delivery_challan.date ,"%d-%m-%Y"))) as date'),
                DB::raw('group_concat(delivery_challan.challan_number) as challan_number') ,
                DB::raw('group_concat(distinct(delivery_challan.party_id)) as party_id'),
                DB::raw("group_concat(delivery_challan.id SEPARATOR ':') as 'a'"),
                DB::raw('group_concat(distinct(party.partyname)) as partyname'),
                DB::raw('group_concat(distinct(party.gst)) as gst'),
                DB::raw('group_concat(distinct(party.reference_name)) as reference_name'),
                DB::raw('group_concat(distinct(party.gst_pointer)) as gst_pointer')
            )->havingRaw('SUM(delivery_challan.total_amount) > 50000')
            ->groupBy('party.gst')
            ->groupBy('delivery_challan.date');
            if(!empty($serach_value))
            {
                $api_data->where(function($query) use ($serach_value){
                    $query->where('challan_number','like',"%".$serach_value."%")
                    ->orwhere('partyname','like',"%".$serach_value."%")
                    ->orwhere('gst','like',"%".$serach_value."%");
                });
            }
            if(isset($request->input('order')[0]['column']))
            {
                $data = ['date','party_id','partyname'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $api_data->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else
                $api_data->orderBy('party_id','desc');      
        }
        else if($type=="invoice")
        {
            $api_data = Tax_Invoice::leftJoin('party','tax_invoice.party_id','party.id')
            ->where('tax_invoice.waybill_status','=','1')
            ->leftJoin('waybill','waybill.invoice_id','tax_invoice.id')
            ->where('waybill.id',NULL)
            ->select(
                DB::raw('group_concat(distinct(tax_invoice.id)) as invoice_id'),
                DB::raw('group_concat(distinct(tax_invoice.invoice_number)) as invoice_number'),
                DB::raw('sum(tax_invoice.total_amount) as total_amount'),
                DB::raw('group_concat(distinct(party.partyname)) as partyname'),
                DB::raw('group_concat(distinct(tax_invoice.party_id)) as party_id'),
                DB::raw("group_concat(tax_invoice.id SEPARATOR ':') as 'a'"),
                DB::raw('group_concat(distinct(tax_invoice.date)) as date'),
                DB::raw('group_concat(distinct(party.gst)) as gst_number'),
                DB::raw('group_concat(distinct(party.reference_name)) as reference_name'),
                DB::raw('group_concat(distinct(party.gst_pointer)) as gst_pointer')
                )->havingRaw('SUM(tax_invoice.total_amount) > 50000')
                ->groupBy('tax_invoice.party_id')
                ->groupBy('tax_invoice.date');
         
            if(!empty($serach_value))
            {
                $api_data->where(function($query) use ($serach_value){
                    $query->where('invoice_number','LIKE',"%".$serach_value."%")
                        ->orwhere('partyname','like',"%".$serach_value."%")
                        ->orwhere('gst','like',"%".$serach_value."%");
                });
            }
            if(isset($request->input('order')[0]['column']))
            {
                $data = ['date','invoice_number','challan_number','partyname','a'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $api_data->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else
                $api_data->orderBy('tax_invoice.party_id','desc');
        
        }
                       
        $count = count( $api_data->get()->toArray());
        $api_data = $api_data->offset($offset)->limit($limit)->get()->toArray();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $api_data; 
        return json_encode($array);
    }
    
    public function pending_po_list() {
        $data = array('layout'=>'layouts.main');
        return view('sections.pending_po',$data);  
    }
 
    public function pending_po_list_api(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $userlog = InternalOrder::leftJoin('client_po','client_po.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->where('io_type.name','<>','Scrap Sale')
        ->where('io_type.name','<>','K Sampling')
        ->where('io_type.name','<>','FOC')
        ->where('internal_order.status','Open')
        ->where('client_po.io', null)
        ->select('internal_order.id',
        'internal_order.io_number',
        'io_type.name as ioType',
        'party_reference.referencename',
        'internal_order.created_time',
        DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
        'job_details.qty'
        );
 
        if(!empty($serach_value))
        {
            $userlog->where(function($query) use ($serach_value){
                $query->where('internal_order.io_number','LIKE',"%".$serach_value."%")
                ->orwhere('io_type.name','LIKE',"%".$serach_value."%")
                ->orwhere('internal_order.created_time','LIKE',"%".$serach_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                ->orwhere('job_details.qty','LIKE',"%".$serach_value."%");
            });                
        }
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                        'internal_order.id',
                        'io_type.name',
                        'party_reference.referencename',
                        'internal_order.io_number','internal_order.created_time',
                        'item_category.name',
                        'job_details.qty'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
        $userlogdata = $userlog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
 
    public function pending_jobcard_list() 
    {
        $data = array('layout'=>'layouts.main');
        return view('sections.pending_jobcard',$data); 
    }
 
 
    public function pending_jobcard_list_api(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $userlog = InternalOrder::leftJoin('job_card','job_card.io_id','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->where('job_card.io_id', null)
        ->where('internal_order.status','Open')
        ->whereNotIn('job_details.io_type_id',array('2','3','8','9'))
        ->select('internal_order.id',
        'internal_order.io_number',
        'io_type.name',
        'job_details.io_type_id',
        'party_reference.referencename',
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),
        DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
        'job_details.qty'
        );
     
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('internal_order.io_number','LIKE',"%".$serach_value."%")
                        ->orwhere('io_type.name','LIKE',"%".$serach_value."%")
                        ->orwhere('job_details.job_date','LIKE',"%".$serach_value."%")
                        ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                        ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                        ->orwhere('job_details.qty','LIKE',"%".$serach_value."%")
                        ;
                    });
         }
       
 
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                        'internal_order.id',
                        'io_type.name',
                        'party_reference.referencename',
                        'internal_order.io_number','job_details.job_date',
                        'item_category.name',
                        'job_details.qty'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
        $userlogdata = $userlog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
 
    public function pending_dispatchorder_list() {
       $data = array('layout'=>'layouts.main');
        return view('sections.pending_dispatchorder',$data); 
    }
 
    public function pending_dispatchorder_list_api(Request $request) {
        // DB::enableQueryLog();
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $userlog = InternalOrder::where('internal_order.status','Open')
        ->where('job_details.io_type_id','<>',8)
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('tax','tax.io_id','internal_order.id')
        ->leftjoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('party','internal_order.party_id','party.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->select('internal_order.id',
        'internal_order.io_number',
        'job_details.qty as io_qty',
        'party_reference.referencename',
        'party.partyname','io_type.name',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),
        // DB::raw('SUM(challan_per_io.good_qty) as dispatch_qty'),
        DB::raw('SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END) as dispatch_qty'),
        DB::raw('(job_details.qty - SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END))as remaining_qty')
        )
        ->HavingRaw('(job_details.qty - SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END)) > 0')->groupBy('internal_order.id')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value))
        {
            $userlog = $userlog->where('internal_order.io_number','LIKE',"%".$serach_value."%")
               ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
               ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
               ->orwhere('party.partyname','LIKE',"%".$serach_value."%")
               ->orwhere('job_details.qty','LIKE',"%".$serach_value."%")
               ->orwhere('io_type.name','LIKE',"%".$serach_value."%")
                ;
        }
 
        $count= count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                'internal_order.id',
                'party.partyname',
                'job_details.qty','io_type.name',
                'job_details.job_date',
                'party_reference.referencename',
                'internal_order.io_number',
                'dispatch_qty',
                'item_name',
                'remaining_qty'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
 
        $userlogdata = $userlog->get();
 
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
 
    public function pending_orders_list() {
        $data = array('layout'=>'layouts.main');
        return view('sections.pending_order_list',$data);  
    }
    public function pending_orders_list_api(Request $request){
        $search = $request->input('search');
        $search_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
           // DB::enableQueryLog();
        $orderlist = InternalOrder::leftjoin('job_details','job_details.id','internal_order.job_details_id')
        ->leftjoin('tax',function($join){
            $join->on('tax.io_id', '=','internal_order.id');
            $join->where('tax.is_cancelled',0);
        })
        
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        // ->where('tax.is_cancelled',0)
        ->select('internal_order.id as IO_id',
            'job_details.qty as IO_qty',
            'io_type.name',
        'party_reference.referencename',
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),
        DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
            DB::raw('SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END) as taxqty'),
            
            DB::raw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END)) as diffqty'),
            // 'job_details.left_qty as diffqty',
            'internal_order.io_number'
        )
        ->where('job_details.io_type_id',8)
        // ->where('tax.io_id',NULL)
        ->HavingRaw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END))  > 0')->groupBy('internal_order.id')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($search_value))
        {
            $orderlist->where(function($query) use ($search_value){
                $query->where('internal_order.io_number','LIKE',"%".$search_value."%")
                ->orwhere('item_category.name','LIKE',"%".$search_value."%")
                ->orwhere('internal_order.other_item_name','LIKE',"%".$search_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$search_value."%")
                ->orwhere('io_type.name','LIKE',"%".$search_value."%");
            });
        }
        if(isset($request->input('order')[0]['column']))
        {
            $data = ['internal_order.io_number',
            'job_details.qty',
            'taxqty',
            'item_name',
            'job_details.job_date',
            'referencename','io_type.name',
            'job_details.left_qty'
        ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $orderlist->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $orderlist->orderBy('internal_order.id','desc');      
    
        $count = count($orderlist->get()->toArray());
        $orderlist = $orderlist->offset($offset)->limit($limit)->get()->toArray();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $orderlist; 
        
        return json_encode($array); 
       
    }
    public function dispatch_vs_billing_report() {
        $data = array('layout'=>'layouts.main');
        return view('sections.dispatch_and_billing_report',$data);  
    }
    public function dispatch_vs_billing_report_api(Request $request){
        $search = $request->input('search');
        $search_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
 
        $orderlist =InternalOrder::where('internal_order.status','Open')
        ->where('job_details.io_type_id','<>',8)
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftjoin('tax',function($join){
            $join->on('tax.io_id', '=','internal_order.id');
            $join->where('tax.is_cancelled',0);
        })
        ->leftjoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->select('internal_order.id',
        'internal_order.io_number',
        'job_details.qty as io_qty',
        'io_type.name',
        DB::raw('group_concat(DISTINCT(tax.id)) as taxid'),
        DB::raw('group_concat(DISTINCT(challan_per_io.id)) as challanid'),
        'party_reference.referencename',
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),
        DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
        DB::raw('group_concat(DISTINCT(concat(tax.id,":",tax.qty))) as taxqty'),
        DB::raw('group_concat(DISTINCT(concat(challan_per_io.id,":",challan_per_io.good_qty))) as dispatch_qty')
        ) 
        // ->HavingRaw('(SUM(IFNULL(challan_per_io.good_qty,0))-SUM(IFNULL(tax.qty,0))) <> 0  ')
        ->groupBy('tax.io_id','challan_per_io.io');
        
        if(!empty($search_value))
        {
            $orderlist->where(function($query) use ($search_value){
                $query->where('io_number','LIKE',"%".$search_value."%")
                ->orwhere('item_category.name','LIKE',"%".$search_value."%")
                ->orwhere('internal_order.other_item_name','LIKE',"%".$search_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$search_value."%")
                ->orwhere('io_type.name','LIKE',"%".$search_value."%");
            });
        }
        $count = count($orderlist->get());
        $orderlist = $orderlist->offset($offset)->limit($limit);
        
        if(isset($request->input('order')[0]['column']))
        {
            $data = ['internal_order.id','io_number',
            'job_details.qty','item_name','dispatch_qty','referencename',
            'taxqty','io_type.name','job_details.job_date'
        ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $orderlist->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $orderlist->orderBy('internal_order.id','desc');      
    
            
        $orderlistdata = $orderlist->get();
        foreach($orderlistdata as $value){
            $dispatch_qty=0;
            $dispatch=$value['dispatch_qty'];
            $dispatch_ext=explode(',',$dispatch);

            $tax_qty=0;
            $tax=$value['taxqty'];
            $tax_ext=explode(',',$tax);

            if($dispatch_ext[0]!=''){
                foreach($dispatch_ext as $item){
                    $dis=explode(':',$item);
                    if(isset($dis[1])){
                        $dispatch_qty=$dispatch_qty+$dis[1];
                        }
                    
                }
            }
            
            if($tax_ext[0]!=''){
                foreach($tax_ext as $item){
                    
                    $tx=explode(':',$item);
                    if(isset($tx[1])){
                    $tax_qty=$tax_qty+$tx[1];
                    }
                    
                    // print_r($tx[1]);
                }
            }

            $value['dispatch_qty']=$dispatch_qty;
            $value['taxqty']=$tax_qty;
            $value['unbilled_qty']=$value['dispatch_qty']-$value['taxqty'];
            $value['unbilled_order_qty']=$value['io_qty']-$value['taxqty'];
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $orderlistdata; 
        
        return json_encode($array); 
    }
    public function sale_tracker_list() {
        $data = array('layout'=>'layouts.main');
        return view('sections.sale_tracker',$data); 
    }
   
    public function sale_tracker_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $client_po = Tax_Invoice::leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
                        ->leftJoin('party','tax_invoice.party_id', 'party.id')
                        ->rightJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                        ->leftJoin('hsn','hsn.id','=','tax.hsn')
                        ->rightJoin('internal_order','internal_order.id',  'tax.io_id')
                        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
                        ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
                        ->where('tax_invoice.is_active',1)
                        ->where('tax_invoice.is_cancelled',0)
                        ->select(
                            'tax_invoice.invoice_number as ch_no',
                            'party.partyname as pname',
                            'consignee.consignee_name as cname',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.id',
                            DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
                            DB::raw('group_concat(tax.qty) as tax_qty'),
                            DB::raw('group_concat(tax.rate) as tax_rate'),
                            DB::raw('group_concat(hsn.gst_rate) as hsn_gst'),
                            DB::raw('group_concat(tax.discount) as tax_dis'),
                            DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'),
                            'tax_invoice.total_amount',
                            DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io_number'),
                            DB::raw('group_concat(DISTINCT(tax_invoice.date)) as created_at'),
                            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number')
                        )->groupBy('tax_invoice.id',
                        'tax_invoice.invoice_number',
                            'party.partyname',
                            'consignee.consignee_name',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.total_amount')
                       ;
                      
        if(!empty($serach_value))
        {
            $client_po->where(function($query) use ($serach_value){
                $query->where('party.partyname','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
                ->orwhere('delivery_challan.challan_number','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.terms_of_delivery','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%");
            });                
        }
 
        // $cp_add_data= $client_po->get()->toArray();
        // foreach($cp_add_data as &$cl){
        //     if(isset($cl['tax_qty'])){
        //         $ar_da = explode(',',$cl['tax_qty']);
        //         $total= 0;
        //         for($i=0;$i<count($ar_da);$i++){
        //             $r= explode(',',$cl['tax_rate']);
        //             $rate=$r[$i];
        //             $q = explode(',',$cl['tax_qty']);
        //             $qty=$q[$i];
        //             $d = explode(',',$cl['tax_dis']);
        //             $discount=$d[$i];
        //             $g = explode(',',$cl['hsn_gst']);
        //             $gst=$g[$i];
        //             $amount=$rate*$qty;
        //             $discount_amt=$amount-(($amount*$discount)/100);
        //             $total=$total+round($discount_amt, 2);
        //         }
        //         $cl['amount']= $total;
        //     }
        // }
        $cp_add_data= $client_po->get()->toArray();
        $sum_without_tax=0;
        $sum_with_tax=0;
        foreach($cp_add_data as &$cl){
            $sum_without_tax=$sum_without_tax+$cl['amount'];
            $sum_with_tax=$sum_with_tax+$cl['total_amount'];
        }
        $count = count($client_po->get()->toArray());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['ch_no','party.partyname','consignee.consignee_name','item_name','created_at','internal_order.io_number','tax_invoice.id','tax_invoice.terms_of_delivery','internal_order.io_number','tax_invoice.total_amount','delivery_challan.challan_number'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $client_po->orderBy('invoice_number','desc');
 
        $client_po= $client_po->get();
        foreach($client_po as $po){
            $po['sum_without_tax']=$sum_without_tax;
            $po['sum_with_tax']=$sum_with_tax;
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        return json_encode($array);
    }
 
    public function sale_tracker_datewise_list_api(Request $request,$s_date,$e_date){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
       
        $client_po = Tax_Invoice::leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
                        ->leftJoin('party','tax_invoice.party_id', 'party.id')
                        ->rightJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                        ->rightJoin('internal_order','internal_order.id',  'tax.io_id')
                        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
                        ->leftJoin('hsn','hsn.id','=','tax.hsn')
                        ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
                        ->where('tax_invoice.is_active',1)
                        ->where('tax_invoice.is_cancelled',0)
                        ->whereBetween('tax_invoice.date',array($s_date,$e_date))
                        ->select(
                            'tax_invoice.invoice_number as ch_no',
                            'party.partyname as pname',
                            'consignee.consignee_name as cname',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.id',
                            DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
                            DB::raw('group_concat(tax.qty) as tax_qty'),
                            DB::raw('group_concat(tax.rate) as tax_rate'),
                            DB::raw('group_concat(hsn.gst_rate) as hsn_gst'),
                            DB::raw('group_concat(tax.discount) as tax_dis'),
                            DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'),
                            'tax_invoice.total_amount',
                            DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io_number'),
                            DB::raw('group_concat(DISTINCT(tax_invoice.date)) as created_at'),
                            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number')
                        )->groupBy('tax_invoice.id',
                        'tax_invoice.invoice_number',
                            'party.partyname',
                            'consignee.consignee_name',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.total_amount')
                       ;
                       
        if(!empty($serach_value))
        {
            $client_po->where(function($query) use ($serach_value){
                $query->where('party.partyname','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
                ->orwhere('delivery_challan.challan_number','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.terms_of_delivery','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%");
            });                
        }
        $cp_add_data= $client_po->get()->toArray();
        $sum_without_tax=0;
        $sum_with_tax=0;
        foreach($cp_add_data as &$cl){
            $sum_without_tax=$sum_without_tax+$cl['amount'];
            $sum_with_tax=$sum_with_tax+$cl['total_amount'];
        }
        $count = count($client_po->get()->toArray());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['ch_no','party.partyname','consignee.consignee_name','item_name','created_at','internal_order.io_number','tax_invoice.id','tax_invoice.terms_of_delivery','internal_order.io_number','tax_invoice.total_amount','delivery_challan.challan_number'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $client_po->orderBy('invoice_number','desc');
 
        $client_po= $client_po->get();
        foreach($client_po as $po){
            $po['sum_without_tax']=$sum_without_tax;
            $po['sum_with_tax']=$sum_with_tax;
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        return json_encode($array);
    }
 
    public function client_po_tracker(){
        $Reference = Reference::get()->toArray();
        $data = array('layout'=>'layouts.main',
        'reference'=>$Reference);
        return view('sections.client_po_tracker',$data); 
    }
    public function fetch_client_po($ref_id){
        $client_po = Client_po::where('reference_name',$ref_id)
        ->where('po_number','<>',null)
        ->select(DB::raw('distinct(po_number)'),'id')
        ->groupby('po_number')
        ->get()->toArray();
        return response()->json($client_po);
    }
    public function client_po_tracker_api(Request $request,$ref,$po){
        
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $po_number = Client_po::where('id',$po)->select('po_number')->get()->first();
        
        $userlog = Client_po::leftJoin('internal_order','client_po.io','internal_order.id')
        ->leftJoin('party_reference','client_po.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->where('internal_order.status','Open')
        ->where('client_po.reference_name',$ref)
        ->where('client_po.po_number','like',$po_number['po_number'])
        ->select('internal_order.id',
        'internal_order.io_number',
        'io_type.name',
        'client_po.po_number',
        'party_reference.referencename',
        'internal_order.created_time',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        'job_details.qty',
        'client_po.qty as client_po_qty',
        DB::raw('(job_details.qty - client_po.qty) as balance')
        );
 
        if(!empty($serach_value))
        {
            $userlog->where(function($query) use ($serach_value){
                $query->where('internal_order.io_number','LIKE',"%".$serach_value."%")
                        ->orwhere('io_type.name','LIKE',"%".$serach_value."%")
                        ->orwhere('internal_order.created_time','LIKE',"%".$serach_value."%")
                        ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                        ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                        ->orwhere('job_details.qty','LIKE',"%".$serach_value."%");
            });
        }
 
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                        'internal_order.id',
                        'io_type.name',
                        'client_po.po_number',
                        'party_reference.referencename',
                        'internal_order.io_number','internal_order.created_time',
                        'item_category.name',
                        'job_details.qty'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
        $userlogdata = $userlog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function client_po_ref_tracker_api(Request $request,$ref){
        
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = Client_po::leftJoin('internal_order','client_po.io','internal_order.id')
        ->leftJoin('party_reference','client_po.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->where('internal_order.status','Open')
        ->where('client_po.reference_name',$ref)
        ->select('internal_order.id',
        'internal_order.io_number',
        'io_type.name',
        'party_reference.referencename',
        'internal_order.created_time',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        'job_details.qty',
        'client_po.qty as client_po_qty',
        'client_po.po_number',
        DB::raw('(job_details.qty - client_po.qty) as balance')
        );
 
        if(!empty($serach_value))
        {
            $userlog->where(function($query) use ($serach_value){
                $query->where('internal_order.io_number','LIKE',"%".$serach_value."%")
                        ->orwhere('io_type.name','LIKE',"%".$serach_value."%")
                        ->orwhere('internal_order.created_time','LIKE',"%".$serach_value."%")
                        ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                        ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                        ->orwhere('job_details.qty','LIKE',"%".$serach_value."%");
            });
        }
 
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                        'internal_order.id',
                        'io_type.name',
                        'party_reference.referencename',
                        'internal_order.io_number','internal_order.created_time',
                        'item_category.name',
                        'client_po.po_number',
                        'job_details.qty'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
        $userlogdata = $userlog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function Order_vs_billing() {
        $data = array('layout'=>'layouts.main');
        return view('sections.order_vs_bill_report',$data);  
    }
    public function Order_vs_billing_api(Request $request) {
        $search = $request->input('search');
        $search_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
 
        $userlog =InternalOrder::where('internal_order.status','Open')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftjoin('tax',function($join){
            $join->on('tax.io_id', '=','internal_order.id');
            $join->where('tax.is_cancelled',0);
        })
        ->leftjoin('tax_invoice','tax.tax_invoice_id','tax_invoice.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->whereNotIn('job_details.io_type_id',array('5','6'))
        ->select('internal_order.id',
        'internal_order.io_number',
        'job_details.qty as io_qty','io_type.name',
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),
        'party_reference.referencename',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
         DB::raw('SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END) as taxqty'),
        DB::raw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END))as unbilled_qty'),
        DB::raw('group_concat(tax_invoice.invoice_number) as tax_invoice_no') 
        )
        ->HavingRaw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END)) > 0')->groupBy('internal_order.id');
 
        if(!empty($search_value))
        {
            $userlog->where(function($query) use ($search_value){
                $query->where('internal_order.other_item_name','LIKE',"%".$search_value."%")
                ->orwhere('internal_order.io_number','LIKE',"%".$search_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$search_value."%")
                ->orwhere('item_category.name','LIKE',"%".$search_value."%")
                ->orwhere('tax_invoice.invoice_number','LIKE',"%".$search_value."%")
                ->orwhere('io_type.name','LIKE',"%".$search_value."%")
                ->orwhere('job_details.qty','LIKE',"%".$search_value."%");
                        
            });
        }
 
        $count = count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                        'internal_order.id',
                        'party_reference.referencename',
                        'internal_order.io_number',
                        'item_name','io_type.name',
                        'job_details.qty',
                        'unbilled_qty',
                        'job_details.job_date',
                        'tax_invoice_no'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
 
        $userlogdata = $userlog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function business_tracker(){
        $Reference = Reference::get()->toArray();
        $data = array('layout'=>'layouts.main',
        'reference'=>$Reference);
        return view('sections.bussiness_tracker',$data); 
    }
    public function fetch_client($ref_id){
        $party = Party::where('reference_name',$ref_id)
        ->select('partyname','id')
        ->get()->toArray();
        return response()->json($party);
    }
    public function business_tracker_api(Request $request,$party_id){
       
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $s_date = $request->input('startDate');
        $e_date = $request->input('endDate');
 
        $client_po = Tax_Invoice::leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
                        ->leftJoin('party','tax_invoice.party_id', 'party.id')
                        ->rightJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                        ->leftJoin('hsn','hsn.id','=','tax.hsn')
                        ->rightJoin('internal_order','internal_order.id',  'tax.io_id')
                        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')

                        ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
                        ->where('tax_invoice.is_active',1)
                        ->where('tax_invoice.is_cancelled',0)
                        ->where('tax_invoice.party_id',$party_id)
                        ->select(
                            'tax_invoice.invoice_number as ch_no',
                            'party.partyname as pname',
                            'consignee.consignee_name as cname',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.id',
                            DB::raw('group_concat(tax.qty) as tax_qty'),
                            DB::raw('group_concat(tax.rate) as tax_rate'),
                            DB::raw('group_concat(hsn.gst_rate) as hsn_gst'),
                            DB::raw('group_concat(tax.discount) as tax_dis'),
                            DB::raw('group_concat(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
                            DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'),
                            'tax_invoice.total_amount',
                            DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io_number'),
                            DB::raw('group_concat(DISTINCT(tax_invoice.date)) as created_at'),
                            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number')
                        )->groupBy('tax_invoice.id',
                        'tax_invoice.invoice_number',
                            'party.partyname',
                            'consignee.consignee_name',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.total_amount')
                       ;
        if(!empty($s_date) && empty($e_date))
        {
            $client_po->where(function($query) use ($s_date){
                $query->where('tax_invoice.date','>=',$s_date);
            });                
        }else if(!empty($e_date) && empty($s_date)){
            $client_po->where(function($query) use ($e_date){
                $query->where('tax_invoice.date','<=',$e_date);
            });   
        }else if(!empty($e_date) && !empty($s_date)){
            $client_po->where(function($query) use ($s_date,$e_date){
                $query->whereBetween('tax_invoice.date',array($s_date,$e_date));
            });   
        }
        if(!empty($serach_value))
        {
            $client_po->where(function($query) use ($serach_value){
                $query->where('party.partyname','LIKE',"%".$serach_value."%")
                ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
                ->orwhere('delivery_challan.challan_number','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.terms_of_delivery','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%");
            });                
        }
        $cp_add_data= $client_po->get()->toArray();
        $sum_without_tax=0;
        $sum_with_tax=0;
        foreach($cp_add_data as &$cl){
            $sum_without_tax=$sum_without_tax+$cl['amount'];
            $sum_with_tax=$sum_with_tax+$cl['total_amount'];
        }
        // print_r($sum_with_tax);die;
        $count = count($client_po->get()->toArray());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['ch_no','item_name','party.partyname','consignee.consignee_name','created_at','internal_order.io_number','tax_invoice.id','tax_invoice.terms_of_delivery','internal_order.io_number','tax_invoice.total_amount','delivery_challan.challan_number'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $client_po->orderBy('invoice_number','desc');
 
        $client_po= $client_po->get();
        foreach($client_po as $po){
            $po['sum_without_tax']=$sum_without_tax;
            $po['sum_with_tax']=$sum_with_tax;
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        $array['sum_without_tax']=$sum_without_tax;
        return json_encode($array);
    }

    public function business_tracker_ref_api(Request $request,$party_id){
       
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $s_date = $request->input('startDate');
        $e_date = $request->input('endDate');
 
        $client_po = Tax_Invoice::leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
                        ->leftJoin('party','tax_invoice.party_id', 'party.id')
                        ->leftJoin('party_reference','party_reference.id','party.reference_name')
                        ->rightJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                        ->leftJoin('hsn','hsn.id','=','tax.hsn')
                        ->rightJoin('internal_order','internal_order.id',  'tax.io_id')
                        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')

                        ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
                        ->where('tax_invoice.is_active',1)
                        ->where('tax_invoice.is_cancelled',0)
                        ->where('party.reference_name',$party_id)
                        ->select(
                            'tax_invoice.invoice_number as ch_no',
                            'party.partyname as pname',
                            'party_reference.referencename as referencename',
                            'consignee.consignee_name as cname',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.id',
                            DB::raw('group_concat(tax.qty) as tax_qty'),
                            DB::raw('group_concat(tax.rate) as tax_rate'),
                            DB::raw('group_concat(hsn.gst_rate) as hsn_gst'),
                            DB::raw('group_concat(tax.discount) as tax_dis'),
                            DB::raw('group_concat(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
                            DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'),
                            'tax_invoice.total_amount',
                            DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io_number'),
                            DB::raw('group_concat(DISTINCT(tax_invoice.date)) as created_at'),
                            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number')
                        )->groupBy('tax_invoice.id',
                        'tax_invoice.invoice_number',
                            'party.partyname',
                            'consignee.consignee_name',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.total_amount')
                       ;
        if(!empty($s_date) && empty($e_date))
        {
            $client_po->where(function($query) use ($s_date){
                $query->where('tax_invoice.date','>=',$s_date);
            });                
        }else if(!empty($e_date) && empty($s_date)){
            $client_po->where(function($query) use ($e_date){
                $query->where('tax_invoice.date','<=',$e_date);
            });   
        }else if(!empty($e_date) && !empty($s_date)){
            $client_po->where(function($query) use ($s_date,$e_date){
                $query->whereBetween('tax_invoice.date',array($s_date,$e_date));
            });   
        }
        if(!empty($serach_value))
        {
            $client_po->where(function($query) use ($serach_value){
                $query->where('party.partyname','LIKE',"%".$serach_value."%")
                ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
                ->orwhere('delivery_challan.challan_number','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.terms_of_delivery','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%");
            });                
        }
        $cp_add_data= $client_po->get()->toArray();
        $sum_without_tax=0;
        $sum_with_tax=0;
        foreach($cp_add_data as &$cl){
            $sum_without_tax=$sum_without_tax+$cl['amount'];
            $sum_with_tax=$sum_with_tax+$cl['total_amount'];
        }
       
        $count = count($client_po->get()->toArray());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['ch_no','item_name','party.partyname','consignee.consignee_name','created_at','internal_order.io_number','tax_invoice.id','tax_invoice.terms_of_delivery','internal_order.io_number','tax_invoice.total_amount','delivery_challan.challan_number'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $client_po->orderBy('invoice_number','desc');
 
        $client_po= $client_po->get();
        foreach($client_po as $po){
            $po['sum_without_tax']=$sum_without_tax;
            $po['sum_with_tax']=$sum_with_tax;
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['sum_without_tax']=$sum_without_tax;
        $array['data'] = $client_po; 
        return json_encode($array);
    }

    public function Ksamp_and_foc_report(){
        $data = array('layout'=>'layouts.main');
        return view('sections.Ksamp_and_foc_report',$data);
    }
    public function Ksamp_and_foc_report_api(Request $request){
        $search = $request->input('search');
        $search_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
         // DB::enableQueryLog();
        $userlog =InternalOrder::where('internal_order.status','Open')
        ->whereIn('job_details.io_type_id', [5, 6])
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('advance_io','advance_io.id','job_details.advance_io_id')
        // ->leftjoin('tax_invoice','tax.tax_invoice_id','tax_invoice.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->select('internal_order.id',
        'internal_order.io_number',
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),'io_type.name as io_type',
        'party_reference.referencename',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        'job_details.qty as io_qty','job_details.rate_per_qty as io_rate',
        DB::raw('(job_details.qty)*(job_details.rate_per_qty) as amount'),
        DB::raw('IFNULL(advance_io.amount, 0) AS advance_amt'),
        DB::raw('CASE WHEN advance_io.mode_of_receive = 0 THEN "Cash"
            WHEN advance_io.mode_of_receive = 1 THEN "Cheque" ELSE "RTGS" END as advance_mode'),
        DB::raw('(((job_details.qty)*(job_details.rate_per_qty)) - IFNULL(advance_io.amount, 0))as balance'))
         // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($search_value))
        {
            $userlog->where(function($query) use ($search_value){
                $query->where('internal_order.other_item_name','LIKE',"%".$search_value."%")
                ->orwhere('internal_order.io_number','LIKE',"%".$search_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$search_value."%")
                ->orwhere('item_category.name','LIKE',"%".$search_value."%")
                ->orwhere('io_type.name','LIKE',"%".$search_value."%")
                ->orwhere('job_details.qty','LIKE',"%".$search_value."%")
                ->orwhere('job_details.rate_per_qty','LIKE',"%".$search_value."%")
                ;
                        
            });
        }
 
        $count = count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                        'internal_order.id','job_details.job_date','item_name','io_rate',
                        'party_reference.referencename',
                        'internal_order.io_number','amount',
                        'item_name','io_type.name','advance_mode',
                        'io_qty','balance','advance_amt'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
 
        $userlogdata = $userlog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function noworkdone_io_report(){
        $data = array('layout'=>'layouts.main');
        return view('sections.noworkdone_io_report',$data);
    }
    public function noworkdone_io_report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $userlog = InternalOrder::leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('tax','tax.io_id','internal_order.id')
        ->leftJoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->where('internal_order.status','Open')
        ->whereNull('tax.id')
        ->whereNull('challan_per_io.id')
        ->select('internal_order.id',
        'internal_order.io_number',
        'io_type.name',
        'party_reference.referencename',
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),'io_type.name as io_type',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        'job_details.qty as io_qty','tax.id as tax_id','challan_per_io.id as challan_id'
        )
        // ->get()
        ;
         // print_r( DB::getQueryLog());die();
        if(!empty($serach_value))
        {
            $userlog = $userlog->where('internal_order.io_number','LIKE',"%".$serach_value."%")
                ->orwhere('io_type.name','LIKE',"%".$serach_value."%")
                ->orwhere('internal_order.created_time','LIKE',"%".$serach_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                ->orwhere('job_details.qty','LIKE',"%".$serach_value."%")
                ;
        }
 
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                        'internal_order.id',
                        'io_type.name',
                        'party_reference.referencename',
                        'internal_order.io_number','job_details.job_date',
                        'item_name',
                        'io_qty'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
        $userlogdata = $userlog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function noworkdone_financial_report(){
        $data = array('layout'=>'layouts.main');
        return view('sections.noworkdone_financial_report',$data);
    }
    public function noworkdone_financial_report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $userlog = InternalOrder::leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('tax','tax.io_id','internal_order.id')
		->leftJoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->where('internal_order.status','Open')
        ->whereNull('tax.id')
        ->whereNull('challan_per_io.id')
        ->select('internal_order.id',
        'internal_order.io_number',
        'io_type.name',
        'party_reference.referencename',
        DB::raw('DATE_FORMAT(internal_order.created_time,"%d-%m-%Y") as created_time'),
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        'job_details.qty as io_qty','tax.id as tax_id','job_details.rate_per_qty as io_rate',
        DB::raw('(job_details.qty)*(job_details.rate_per_qty) as amount')
        )
        // ->get()
        ;
         // print_r( DB::getQueryLog());die();
        if(!empty($serach_value))
        {
            $userlog = $userlog->where('internal_order.io_number','LIKE',"%".$serach_value."%")
                ->orwhere('io_type.name','LIKE',"%".$serach_value."%")
                ->orwhere('internal_order.created_time','LIKE',"%".$serach_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                ->orwhere('job_details.qty','LIKE',"%".$serach_value."%")
                ;
        }
 
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                        'internal_order.id',
                        'io_type.name',
                        'party_reference.referencename',
                        'internal_order.io_number','internal_order.created_time',
                        'item_name',
                        'io_qty','io_rate','amount'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
        $userlogdata = $userlog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function pending_dispatchorder_financial(){
        $data = array('layout'=>'layouts.main');
        return view('sections.pendingdispatch_financial_report',$data);
    }
    public function pending_dispatchorder_financial_api(Request $request) {
        // DB::enableQueryLog();
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $userlog = InternalOrder::where('internal_order.status','Open')
        ->where('job_details.io_type_id','<>',8)
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('tax','tax.io_id','internal_order.id')
        ->leftjoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('party','internal_order.party_id','party.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->select('internal_order.id',
        'internal_order.io_number',
        'job_details.qty as io_qty',
        'party_reference.referencename',
        'party.partyname','io_type.name',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),
        // DB::raw('SUM(challan_per_io.good_qty) as dispatch_qty'),
        DB::raw('SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END) as dispatch_qty'),
        DB::raw('(job_details.qty - SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END))as remaining_qty'),'job_details.rate_per_qty as io_rate',
        DB::raw('(job_details.qty)*(job_details.rate_per_qty) as amount')
        )
        ->HavingRaw('(job_details.qty - SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END)) > 0')->groupBy('internal_order.id')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value))
        {
            $userlog = $userlog->where('internal_order.io_number','LIKE',"%".$serach_value."%")
               ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
               ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
               ->orwhere('party.partyname','LIKE',"%".$serach_value."%")
               ->orwhere('job_details.qty','LIKE',"%".$serach_value."%")
               ->orwhere('io_type.name','LIKE',"%".$serach_value."%")
                ;
        }
 
        $count= count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [
                'internal_order.id',
                'party.partyname',
                'job_details.qty','io_type.name',
                'job_details.job_date',
                'party_reference.referencename',
                'internal_order.io_number',
                'dispatch_qty',
                'item_name',
                'remaining_qty','io_rate','amount'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('internal_order.id','desc');
        }
 
        $userlogdata = $userlog->get();
 
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }

    //to be closed ios
    public function to_be_closed_ios_report() {
        $data = array('layout'=>'layouts.main');
        return view('collection.report',$data);  
    }
    public function to_be_closed_ios_report_api(Request $request){
        $search = $request->input('search');
        $search_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
           // DB::enableQueryLog();
        //    $orderlist=coll__payment_recieved::leftJoin('tax_invoice','tax_invoice.id','coll__payment_recieved.tax_invoice_id')
        //     ->leftjoin('tax',function($join){
        //     $join->on('tax.tax_invoice_id', '=','tax_invoice.id');
        //     $join->where('tax.is_cancelled',0);
        //     })
        //     ->leftJoin('')
        $orderlist = InternalOrder::leftjoin('job_details','job_details.id','internal_order.job_details_id')
        ->leftjoin('tax',function($join){
            $join->on('tax.io_id', '=','internal_order.id');
            $join->where('tax.is_cancelled',0);
        })
        ->leftJoin('tax_invoice','tax_invoice.id','tax.tax_invoice_id')
        ->leftJoin('tax as tx','tax_invoice.id','tx.tax_invoice_id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftJoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax.tax_invoice_id')
        // ->where('tax.is_cancelled',0)
        ->select('internal_order.id as IO_id',
            'job_details.qty as io_qty',
            'io_number',
           
            DB::raw('group_concat(DISTINCT(invoice_number)) as invoice_number'),
        'party_reference.referencename',
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as job_date'),
        DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
        
 
            DB::raw('(IFNULL(
                (SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
                WHERE p.tax_invoice_id = tax_invoice.id
                    GROUP BY p.tax_invoice_id) ,0 ) 
                ) as amt_received'),
                DB::raw('(IFNULL(
                    (SELECT (p.status) FROM coll__payment_recieved p 
                    WHERE p.tax_invoice_id = tax_invoice.id
                        GROUP BY p.tax_invoice_id) ,"pending") 
                    ) as status'),
            DB::raw('(job_details.qty - 
            (IFNULL(
                (SELECT SUM(m.qty) FROM tax m 
                WHERE m.io_id=internal_order.id
                    GROUP BY internal_order.id) ,0 ) 
                )
            )  as binding_qty_left'),
            DB::raw('(IFNULL(
                (SELECT SUM(m.qty) FROM tax m 
                WHERE m.io_id=internal_order.id
                    GROUP BY internal_order.id) ,0 ) 
                ) AS tax_qty'),
                DB::raw('(IFNULL(
                    (SELECT SUM(m.qty) FROM tax m 
                    WHERE m.tax_invoice_id=tax_invoice.id
                        GROUP BY tax_invoice.id) ,0 ) 
                    ) AS total_tax_qty'),

                DB::raw('(IFNULL(
                    (SELECT SUM(m.total_amount) FROM tax_invoice m 
                    WHERE m.id=tax.tax_invoice_id
                        GROUP BY tax_invoice.id) ,0 ) 
                    ) AS total_amount'),
            DB::raw('(IFNULL(
                (SELECT SUM(m.total_amount) FROM tax_invoice m 
                WHERE m.id=coll__payment_recieved.tax_invoice_id
                    GROUP BY coll__payment_recieved.tax_invoice_id) ,0 ) 
                )-(IFNULL(
                    (SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
                    WHERE p.tax_invoice_id = tax_invoice.id
                        GROUP BY p.tax_invoice_id) ,0 ) 
                    ) AS amt_left'),

                    

            DB::raw('(SUM(tax_invoice.total_amount)-(IFNULL(SUM(coll__payment_recieved.pr_amount),"0")))as leftamt'),
            DB::raw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END)) as diffqty'),
            // 'job_details.left_qty as diffqty',
            'internal_order.io_number'
        )
        ->where('coll__payment_recieved.tax_invoice_id','!=',NULL)
        // ->orwhere('coll__payment_recieved.status',"=",'closed')
        ->WhereRaw('(((IFNULL(
            (SELECT SUM(m.total_amount) FROM tax_invoice m 
            WHERE m.id=coll__payment_recieved.tax_invoice_id
                GROUP BY coll__payment_recieved.tax_invoice_id) ,0 ) 
            )-(IFNULL(
                (SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
                WHERE p.tax_invoice_id = tax_invoice.id
                    GROUP BY p.tax_invoice_id) ,0 ) 
                ) < 1) AND (job_details.qty - 
                (IFNULL(
                    (SELECT SUM(m.qty) FROM tax m 
                    WHERE m.io_id=internal_order.id
                        GROUP BY internal_order.id) ,0 ) 
                    )
                ) = 0) OR (IFNULL(
                    (SELECT p.status FROM coll__payment_recieved p 
                    WHERE p.tax_invoice_id = tax_invoice.id
                        GROUP BY p.tax_invoice_id) ,"pending" ) 
                    ) = "closed"')
        ->groupBy('internal_order.id','coll__payment_recieved.tax_invoice_id')
        // ->get()
        ;
        // // print_r( DB::getQueryLog());die();
        if(!empty($search_value))
        {
            $orderlist->where(function($query) use ($search_value){
                $query->where('internal_order.io_number','LIKE',"%".$search_value."%")
                ->orwhere('item_category.name','LIKE',"%".$search_value."%")
                ->orwhere('internal_order.other_item_name','LIKE',"%".$search_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$search_value."%")
                ->orwhere('io_type.name','LIKE',"%".$search_value."%");
            });
        }
        if(isset($request->input('order')[0]['column']))
        {
            $data = ['internal_order.io_number',
            'job_details.qty',
            'taxqty',
            'item_name',
            'job_details.job_date',
            'referencename','io_type.name',
            'job_details.left_qty'
        ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $orderlist->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $orderlist->orderBy('internal_order.id','desc');      
    
        $count = count($orderlist->get()->toArray());
        $orderlist = $orderlist->offset($offset)->limit($limit)->get()->toArray();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $orderlist; 
        
        return json_encode($array); 
       
    }
}
 

