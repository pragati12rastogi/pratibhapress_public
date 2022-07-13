<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\InternalOrder;
use App\Model\Party;
use App\Model\Tax_Invoice;
use App\Model\Delivery_challan;
use App\Model\POD_upload;
use App\Model\ItemCategory;
use App\Model\Tax;
use DB;
use Route;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use App\dkerp;
use Auth;use File;

class OrderToCollectionController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        return view('sections.dashboard', ['layout' => 'layouts.main']);
    }
    public function summary_old(Request $requset)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $partydata = InternalOrder::leftJoin('party', function($join) {
                            $join->on('internal_order.party_id', '=', 'party.id');
                        })->rightJoin('job_card',function($join){
                            $join->on('job_card.io_id','=','internal_order.id');
                        });
        if(!empty($serach_value))
        {
            $partydata->where('partyname','LIKE',"%".$serach_value."%")
                        ->orWhere('job_number','LIKE',"%".$serach_value."%")
                        ->orWhere('internal_order.other_item_name','LIKE',"%".$serach_value."%")
                        ->orWhere('internal_order.io_number','LIKE',"%".$serach_value."%")
                        ->orWhere('internal_order.created_time','LIKE',"%".$serach_value."%")
                        ->orWhere('tax_invoice.invoice_number','LIKE',"%".$serach_value."%")
                        ->orWhere('item_category.name','LIKE',"%".$serach_value."%")
                        ->orWhere('delivery_challan.challan_number','LIKE',"%".$serach_value."%");
        }
        $count = $partydata->count();

        $partydata=$partydata->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['a','b','pn','item_name','jc_num','dc_num','ti_num'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $partydata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else {
            $partydata->orderBy('tax_invoice.id', 'desc');
        }
        
            $partydata = $partydata->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $partydata; 
        return json_encode($array);

    }
  
    public function summry(Request $request)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $partydata1 = InternalOrder::leftjoin('challan_per_io','internal_order.id','challan_per_io.io')
        ->leftjoin('job_card','job_card.io_id','internal_order.id')
        ->leftjoin('job_details','job_details.id','internal_order.job_details_id')
        ->leftjoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','item_category.id','internal_order.item_category_id')
        ->leftjoin('delivery_challan','delivery_challan.id','challan_per_io.delivery_challan_id')
        ->leftJoin('tax','tax.delivery_challan_id','delivery_challan.id' )
        ->leftJoin('tax_invoice','tax.tax_invoice_id','tax_invoice.id' )
        ->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
        ->select(
            DB::raw('CONVERT(SUBSTRING_INDEX(io_number,"/",-1),UNSIGNED  INTEGER) as asd'),
            DB::raw('internal_order.io_number as a'),
            'internal_order.id as io_id',
            'party_reference.referencename as reference_name',
            DB::raw('(DATE_FORMAT(internal_order.created_time ,"%d-%m-%Y %r")) as b'),
            'item_category.name as item_name',
            'io_type.name as io_type',
            'internal_order.financial_year',
            'job_details.qty',
            'job_details.rate_per_qty',
            'internal_order.other_item_name as other_item_name',
            DB::raw('job_card.job_number as jc_num'),
            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as dc_num'),
            DB::raw('group_concat(DISTINCT(tax_invoice.invoice_number)) as ti_num')
        )->groupBy('internal_order.id','io_type.name','internal_order.io_number', 'party_reference.referencename','job_card.job_number','internal_order.other_item_name','internal_order.created_time','item_category.name');
        $partydata  =$partydata1;
        // 
            // $partydata  = Tax_Invoice::leftJoin('tax','tax.tax_invoice_id','tax_invoice.id' )
            // ->leftJoin('party','party.id','tax_invoice.party_id')
            // ->leftjoin('delivery_challan','delivery_challan.id','tax.delivery_challan_id')
            // ->leftjoin('challan_per_io','challan_per_io.delivery_challan_id','delivery_challan.id')
            // ->leftjoin('internal_order','internal_order.id','challan_per_io.io')
            // ->leftjoin('item_category','item_category.id','internal_order.item_category_id')
            // ->leftjoin('job_card','job_card.io_id','internal_order.id')
            // ->select(
            //     'internal_order.io_number as a',
            //     'internal_order.id as io_id',
                
            //     DB::raw('substring(internal_order.created_time,1,10) as b'),
            //     'party.partyname as pn',
            //     DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
            //     'job_card.job_number as jc_num',
            //     'delivery_challan.challan_number as dc_num',
            //     'tax_invoice.invoice_number as ti_num'
            // );
            //// $partydata = InternalOrder::leftJoin('party', function($join) {
            //                             $join->on('internal_order.party_id', '=', 'party.id');
            //                         })->rightJoin('job_card',function($join){
            //                             $join->on('job_card.io_id','=','internal_order.id');
            //                         });
        // 
        if(!empty($serach_value))
        {
            $partydata->where('internal_order.reference_name','LIKE',"%".$serach_value."%")
                ->orWhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                ->orWhere('job_number','LIKE',"%".$serach_value."%")
                ->orWhere('internal_order.other_item_name','LIKE',"%".$serach_value."%")
                ->orWhere('internal_order.io_number','LIKE',"%".$serach_value."%")
                ->orWhere('internal_order.created_time','LIKE',"%".$serach_value."%")
                ->orWhere('io_type.name','LIKE',"%".$serach_value."%")
                ->orWhere('tax_invoice.invoice_number','LIKE',"%".$serach_value."%")
                ->orWhere('item_category.name','LIKE',"%".$serach_value."%")
                ->orWhere('internal_order.other_item_name','LIKE',"%".$serach_value."%")
                ->orWhere('delivery_challan.challan_number','LIKE',"%".$serach_value."%");
        }
        $count = count($partydata->get()->toArray());
        
        $partydata=$partydata->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['internal_order.io_number','internal_order.created_time',
                'item_category.name','job_card.job_number','io_type.name','delivery_challan.challan_number','internal_order.other_item_name','tax_invoice.invoice_number','party_reference.referencename','job_details.qty','rate_per_qty'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $partydata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else {
            $partydata->orderBy('financial_year','desc')->orderBy('asd','desc');
        }
        
        $partydata = $partydata->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $partydata; 
        return json_encode($array);
 
    }
    public function pod(){
        $dcns = Delivery_challan::leftJoin('challan_per_io','challan_per_io.delivery_challan_id','delivery_challan.id')
        ->leftjoin('internal_order','internal_order.id','challan_per_io.io')
        ->select(
            DB::raw('group_concat(DISTINCT(delivery_challan.id)) as id'),
            DB::raw('group_concat(DISTINCT(challan_number)) as challan_number'),
            DB::raw('group_concat(DISTINCT((internal_order.status))) as st')
    )
    ->groupBy('delivery_challan.id')
    ->get();
    $dcn=[];
    foreach($dcns as $item){
        if($item['st']=="Open"){
            $dcn[$item['id']]['id']=$item['id'];
            $dcn[$item['id']]['challan_number']=$item['challan_number'];
            $dcn[$item['id']]['st']=$item['st'];
        }
    }
    // print_r($dcn);die;
    // return $dcn;
        $data=array('layout'=>'layouts.main','dcn'=>$dcn);
        return view('sections.pod', $data);
    }
    public function pod_db(Request $request){
        try {
            // print_r($request->input('is_rec'));die();
            $this->validate($request,
                [
                    'dcn' => 'required',
                    'is_rec' => 'required',
                    'upl_dc'=>'required_if:is_rec,==,Delivery Challan',
                    'upl_docket'=>'required_if:is_rec,==,Docket',
                    
                ],
                [
                    
                    'dcn.required' => 'This field is required',
                    'is_rec.required' => 'This field is required',
                    'upl_dc.required_if' => 'This field is required',
                    'upl_docket.required_if' => 'This field is required',
                ]
            );
            
            $timestamp = date('Y-m-d G:i:s');
                $dc_upload = $request->file('upl_dc');
                $doc_upload = $request->file('upl_docket');
                
                $dc ='';
                if(isset($dc_upload) || $dc_upload != null){
                    $destinationPath = public_path().'/upload/dc';
                    $filenameWithExt = $request->file('upl_dc')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('upl_dc')->getClientOriginalExtension();
                    $dc = $filename.'_'.time().'.'.$extension;
                    $path = $dc_upload->move($destinationPath, $dc);
                    
                }else{
                    $dc = '';
                }
                $doc ='';
                if(isset($doc_upload) || $doc_upload != null){
                    $destinationPath = public_path().'/upload/dc';
                    $filenameWithExt = $request->file('upl_docket')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('upl_docket')->getClientOriginalExtension();
                    $doc = $filename.'_'.time().'.'.$extension;
                    $path = $doc_upload->move($destinationPath, $doc);
                }else{
                    $doc = '';
                }

            $pod = POD_upload::insertGetId([
                "id"=>NULL,
                "dc_id"=>$request->input('dcn'),
                "pod_recieved"=>$request->input('is_rec'),
                "dc_upload"=>$dc,
                "docket_upload"=>$doc,
                "created_by"=>Auth::id(),
                "created_at"=>$timestamp
            ]);
            if($pod == Null){
                return redirect('/proof/of/delivery')->with('error','Some Error Occur');
            }else{
                return redirect('/proof/of/delivery')->with('success','Tax Invoice Dispatch created successfully.');
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/proof/of/delivery')->with('error',$ex->getMessage());
        }
    }
    public function pod_summary(){
        
        $data=array('layout'=>'layouts.main');
        return view('sections.pod_summary', $data);
    }
    public function pod_summary_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $pod_list = POD_upload::leftjoin('delivery_challan','delivery_challan.id','pod_upload.dc_id')
        ->leftjoin('challan_per_io','challan_per_io.delivery_challan_id','delivery_challan.id')
        ->leftjoin('internal_order','challan_per_io.io','internal_order.id')
        ->leftjoin('party','delivery_challan.party_id','party.id')
        ->leftJoin('item_category',function($join){
            $join->on('internal_order.item_category_id','=','item_category.id');
       })
        ->select('pod_upload.*',
        'delivery_challan.challan_number',
        DB::raw('(SUM(challan_per_io.good_qty))as qtys'),
        DB::raw('(DATE_FORMAT(delivery_challan.delivery_date ,"%d-%m-%Y")) as delivery_date'), 'party.partyname',
        DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as itemss')
    
    )->groupBy('delivery_challan.id');
        if(!empty($serach_value))
        {
            $pod_list->where(function($query) use ($serach_value){
                    $query->where('delivery_challan.challan_number','LIKE',"%".$serach_value."%")
                        ->orWhere('pod_recieved','LIKE',"%".$serach_value."%")
                        ->orwhere('party.partyname','LIKE',"%".$serach_value."%")
                        ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                        ->orwhere('internal_order.other_item_name','LIKE',"%".$serach_value."%")
                        ;
                }); 
        }
        
        if(isset($request->input('order')[0]['column'])){
            $data = ['pod_upload.id', 'delivery_challan.challan_number', 'party.partyname','delivery_challan.delivery_date','qty','dc_upload', 'docket_upload'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $pod_list->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $pod_list->orderBy('pod_upload.id','desc');
        }

        $count = $pod_list->count();
        $pod_list = $pod_list->offset($offset)->limit($limit)->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $pod_list;
        return json_encode($array);
    }
    public function not_uploaded_pod_list(){
        
        $data=array('layout'=>'layouts.main');
        return view('sections.not_uploaded_pod', $data);
    }
    public function not_uploaded_pod_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $pod_list = Delivery_challan::leftjoin('pod_upload','delivery_challan.id','pod_upload.dc_id')
        ->leftjoin('challan_per_io','challan_per_io.delivery_challan_id','delivery_challan.id')
        ->leftjoin('internal_order','challan_per_io.io','internal_order.id')
        ->leftJoin('item_category',function($join){
            $join->on('internal_order.item_category_id','=','item_category.id');
       })
        ->leftJoin('party_reference','delivery_challan.reference_name','party_reference.id')
        ->leftjoin('party','delivery_challan.party_id','party.id')
        ->whereNull('pod_upload.dc_id');
        // ->get()->toArray()
        ;
        // print_r($pod_list);die();
        if(!empty($serach_value))
        {
            $pod_list->where(function($query) use ($serach_value){
                    $query->where('delivery_challan.challan_number','LIKE',"%".$serach_value."%")
                        ->orWhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                        ->orWhere('party.partyname','LIKE',"%".$serach_value."%")
                        ->orwhere('party.partyname','LIKE',"%".$serach_value."%")
                        ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                        ->orwhere('internal_order.other_item_name','LIKE',"%".$serach_value."%")
                        
                        ;
                }); 
        }
        
        if(isset($request->input('order')[0]['column'])){
            $data = ['delivery_challan.id', 'delivery_challan.challan_number', 'party_reference.referencename', 'party.partyname','delivery_challan.delivery_date','delivery_challan.total_amount'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $pod_list->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $pod_list->orderBy('delivery_challan.id','desc');
        }

        $count = count(
            $pod_list->select(
                DB::raw('group_concat(DISTINCT(pod_upload.dc_id)) as dc_id'),
                DB::raw('group_concat(DISTINCT(delivery_challan.delivery_date)) as delivery_date'),
                DB::raw('group_concat(DISTINCT(delivery_challan.total_amount)) as total_amount'),
                DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number'),
                DB::raw('group_concat(DISTINCT(party_reference.referencename)) as referencename'),
                DB::raw('group_concat(DISTINCT(party.partyname)) as partyname'),
                DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as itemss'),
                DB::raw('(SUM(challan_per_io.good_qty))as qtys')
               )->groupBy('delivery_challan.id')->get()
        );
        $pod_list = $pod_list->select(
            DB::raw('group_concat(DISTINCT(pod_upload.dc_id)) as dc_id'),
            DB::raw('group_concat(DISTINCT(DATE_FORMAT(delivery_challan.delivery_date ,"%d-%m-%Y"))) as delivery_date'),
            DB::raw('group_concat(DISTINCT(delivery_challan.total_amount)) as total_amount'),
            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number'),
            DB::raw('group_concat(DISTINCT(party_reference.referencename)) as referencename'),
            DB::raw('group_concat(DISTINCT(party.partyname)) as partyname'),
            DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as itemss'),
            DB::raw('(SUM(challan_per_io.good_qty))as qtys')
           )->groupBy('delivery_challan.id')->offset($offset)->limit($limit)->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $pod_list;
        return json_encode($array);
    }
    public function summry_jobdet($data_id)
    {
        // $partydata = InternalOrder::leftJoin('party', function($join) {
        //                             $join->on('internal_order.party_id', '=', 'party.id');
        //                         })->leftJoin('job_details',function($join){
        //                             $join->on('internal_order.job_details_id','=','job_details.id');
        //                         })->leftJoin('item_category',function($join){
        //                             $join->on('internal_order.item_category_id','=','item_category.id');
        //                         })->leftJoin('job_card_joins',function($join){
        //                             $join->on('internal_order.id','=','job_card_joins.io_id');
        //                         });
                                
            // $partydata1 = $partydata->select(
            //     'internal_order.id',

            //     'item_category.name as Item Name',
            //     'item_category.elements as Element',

            //     'job_details.is_supplied_paper as Paper by Press',
            //     'job_details.is_supplied_plate as Plate by Press',

            //     'job_card_joins.plate_size as Plate Size',
            //     'job_card_joins.plate_size as Paper Size',
            //     'job_card_joins.paper_gsm as Paper GSM',
            //     'job_card_joins.paper_brand as Paper Brand',
            //     'job_card_joins.no_of_pages as No Of Pages',
            //     'job_card_joins.name as Paper Type',
            //     'job_card_joins.impression_per_plate as Impression Per Plate',
            //     'job_card_joins.front_color as Front Colour',
            //     'job_card_joins.back_color as Back Colour',
            //     'job_card_joins.job_sample_received as Sample Received',
            //     'job_card_joins.open_size as Open Size',
            //     'job_card_joins.close_size as Close Size',
            //     'job_card_joins.creative_name as Creative Name',
            //     'job_card_joins.io_id',
            //     'job_card_joins.plate_sets as Plate Set',
            //     'job_card_joins.name as Plate Type'
            // )->where('internal_order.id','=',$data_id)->get()->first()->toarray();
            // $partydata2 = $partydata->select(
            //     'job_card_joins.value as val',      
            //     'job_card_joins.remark as rem'
            //     )->where('internal_order.id',"=",$data_id)->get()->first()->toarray();

            $partydata=InternalOrder::where('internal_order.id',$data_id)
            ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
            ->leftJoin('master__marketing_person','master__marketing_person.id','=','job_details.marketing_user_id')
            ->leftJoin('advance_io','job_details.advance_io_id','=','advance_io.id')
            ->leftJoin('party','internal_order.party_id','=','party.id')
            ->leftJoin('countries','party.country_id','=','countries.id')
            ->leftJoin('states','party.state_id','=','states.id')
            ->leftJoin('cities','party.city_id','=','cities.id')
            ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
            ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
            ->leftJoin('io_type','job_details.io_type_id','=','io_type.id')
            ->leftJoin('unit_of_measurement','job_details.unit','=','unit_of_measurement.id')
            ->leftJoin('hsn','hsn.id','=','job_details.hsn_code');
            $partydata1= $partydata->select(
                'internal_order.id as io_id',
            'internal_order.io_number',
            'internal_order.status',
            'internal_order.closed_by',
            DB::raw('(DATE_FORMAT(internal_order.closed_date ,"%d-%m-%Y")) as closed_date'),
            'internal_order.party_id',
            'internal_order.item_category_id',
            'internal_order.job_details_id',
            DB::raw('(DATE_FORMAT(internal_order.created_time ,"%d-%m-%Y %r")) as created_time'),
            'internal_order.created_by',
            'internal_order.other_item_name',
            'party.address',//doubt
            'party.pincode',
            'payment_term.value',
            'party.reference_name',
            'cities.city as city',
            'states.name as states',
            'countries.name as country',
            'item_category.name as item_category',
            'io_type.name as io_type',
            'unit_of_measurement.uom_name',
            DB::raw('(DATE_FORMAT(job_details.job_date ,"%d-%m-%Y")) as job_date'),
            DB::raw('(DATE_FORMAT(job_details.delivery_date ,"%d-%m-%Y")) as delivery_date'),
            'job_details.qty',
            'job_details.job_size',
            'job_details.rate_per_qty',
            'job_details.marketing_user_id',
            'job_details.details',
            'job_details.dimension',
            'job_details.front_color',
            'job_details.back_color',
            'job_details.is_supplied_paper',
            'job_details.is_supplied_plate',
            'job_details.remarks',
            'job_details.transportation_charge',
            'job_details.other_charge',
            'job_details.advanced_received',
            'master__marketing_person.id as user_id',
            'master__marketing_person.name as marketing_name',
            'advance_io.amount',
            'advance_io.mode_of_receive',
            DB::raw('(DATE_FORMAT(advance_io.date ,"%d-%m-%Y")) as amount_received_date'),
            'hsn.hsn as hsn_name',
            'hsn.gst_rate as gst'
            )->get()->first()->toarray();
          
        // $data=array( 'io'=>'$io','data1'=>$partydata1,'data2'=>$partydata2);
        $data=array('internal'=>$partydata1);
        // return $partydata1;
        return view('sections.summary_jobdet', $data);
    }
    public function summary_list()
    {
        $data=array('layout'=>'layouts.main', 'io'=>'$io');
        //     return view('sections.summary', $data);
        return view('sections.summary_change', $data);
    }
    public function fms(){
        $item = ItemCategory::all();
        $data=array('layout'=>'layouts.main','item'=>$item);
        return view('sections.fms', $data);
    }

    public function fms_old(){
        $item = ItemCategory::all();
        $data=array('layout'=>'layouts.main','item'=>$item);
        return view('sections.fms_copy', $data);
    }

    public function fms_api(Request $request){
        
        // $response = array(
        //     'status' => 'success',
        // );
        $yr=$request->input('year');
        $month=$request->input('month');
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        DB::enableQueryLog();
       $partydata = InternalOrder::where('internal_order.status','Open')
        ->whereYear('internal_order.created_time', $yr)
        ->whereMonth('internal_order.created_time', $month)
        ;
    

        $partydata->where(function($query) use ($s_date){
            $query    ->leftjoin('job_details',function($join){
                $join->on('job_details.id','internal_order.job_details_id');
            })
    
            ->leftjoin('job_card',function($join){
                $join->on('job_card.io_id','internal_order.id');
            })
            ->leftjoin('item_category','item_category.id','internal_order.item_category_id')
            ->leftjoin('client_po','client_po.io','internal_order.id')
            ->leftJoin('challan_per_io','challan_per_io.io','internal_order.id' )
            ->leftJoin('delivery_challan','delivery_challan.id','challan_per_io.delivery_challan_id' )
            ->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
            ->leftjoin('io_type','job_details.io_type_id','io_type.id')
            ->leftJoin('tax','tax.io_id','internal_order.id')
            ->leftJoin('tax_invoice','tax_invoice.id','tax.tax_invoice_id')
            ->leftJoin('tax_dispatch','tax_dispatch.tax_invoice_id','tax_invoice.id')
            ->leftJoin('tax as dispatch_tax','tax_dispatch.tax_invoice_id','dispatch_tax.tax_invoice_id')
            ->leftJoin('waybill as way_challan','delivery_challan.id','way_challan.challan_id')
            ->leftJoin('waybill as way_tax','tax_invoice.id','way_tax.invoice_id')
            ->leftJoin('asn','asn.invoice_id','tax_invoice.id')
            ->leftJoin('grn','grn.invoice_id','tax_invoice.id')
            ->leftJoin('pur_purchase_req','pur_purchase_req.io','internal_order.id')
            ->leftJoin('employee__profile','employee__profile.id','pur_purchase_req.requested_by')
            ->leftJoin('department','department.id','employee__profile.department_id')
            ->where('internal_order.status','Open');
        });

      
        if(!empty($serach_value))
        {
            $partydata->where('job_number','LIKE',"%".$serach_value."%")
                        ->orWhere('internal_order.other_item_name','LIKE',"%".$serach_value."%")
                        ->orWhere('internal_order.io_number','LIKE',"%".$serach_value."%")
                        ->orWhere('internal_order.created_time','LIKE',"%".$serach_value."%")
                        ->orWhere('item_category.name','LIKE',"%".$serach_value."%")
                        ->orWhere('delivery_challan.challan_number','LIKE',"%".$serach_value."%")
                        ->orWhere('tax_invoice.invoice_number','LIKE',"%".$serach_value."%")
                        ->orWhere('client_po.po_number','LIKE',"%".$serach_value."%")
                        ->orWhere('io_type.name','LIKE',"%".$serach_value."%");
        }
        $count = count($partydata ->select(
            //step 1
            'internal_order.id as io_id',
            DB::raw('internal_order.io_number as io_number'),
             DB::raw('(DATE_FORMAT(internal_order.created_time ,"%d-%m-%Y %r")) as io_date'),
             DB::raw('job_details.qty as qty'),
             DB::raw('party_reference.referencename as reference_name'),
            DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_category'),
            DB::raw('io_type.name as io_type'),
            
            //step 2
            DB::raw('job_card.created_time as actual_job_date'),
            DB::raw('(ADDTIME(internal_order.created_time,"2:00:00")) as planned_job_date'),
            DB::raw('(DATE_FORMAT(job_details.delivery_date ,"%d-%m-%Y %r")) as delivery_date'),
            DB::raw('job_card.creative_name as creative_name'),
            DB::raw('job_card.job_qty as job_qty'),
            DB::raw( 'job_card.job_number as job_number'),
            DB::raw('job_card.created_time as job_created_time'),
            
            //step 3

            //step4
            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as delivery_challan_number'),
            DB::raw('job_details.delivery_date as planned_delivery_date'),
            DB::raw('delivery_challan.created_time  as actual_delivery_date'),
            DB::raw('sum(challan_per_io.good_qty) as sum_qty_dc'),
            DB::raw('group_concat(challan_per_io.good_desc) as delivery_challan_good_desc'),
            DB::raw('group_concat(challan_per_io.good_qty) as delivery_challan_good_qty'),

            //step 5
            DB::raw('client_po.po_number'),
            DB::raw('client_po.is_po_provided'),
            DB::raw('client_po.created_at as actual_cpo_date'),
            DB::raw('(SUBTIME(job_details.delivery_date ,"24:00:00")) as planned_cpo_date'),


            DB::raw('group_concat(DISTINCT(tax_invoice.invoice_number)) as invoice_number'),
            DB::raw('(ADDTIME(job_details.delivery_date,"0:30:00")) as planned_tax_date'),
            DB::raw('tax_invoice.created_at  as actual_tax_date'),
            DB::raw('sum(tax.qty) as sum_qty_tax'),
            DB::raw('sum(dispatch_tax.qty) as sum_qty_dispatch'),
            DB::raw('group_concat(tax_invoice.total_amount) as tax_amount'),
            DB::raw('group_concat(tax.qty) as tax_qty'),


           
            DB::raw('(group_concat(tax_invoice.waybill_status)) as tax_waybill'),
            DB::raw('(group_concat(delivery_challan.waybill_status)) as challan_waybill'),
            // DB::raw('group_concat(DISTINCT(way_tax.invoice_number)) as way_invoice_number'),
            DB::raw('group_concat(way_challan.waybill_number) as way_challan_number'),
            DB::raw('group_concat(way_tax.waybill_number) as way_tax_number'),

            DB::raw('group_concat(asn.asn_number) as asn_number'),
            DB::raw('group_concat(grn.grn_number) as grn_number'),
            DB::raw('asn.created_time as asn_actual_date'),
            DB::raw('grn.created_time as grn_actual_date'),


            DB::raw('group_concat(tax_dispatch.docket_number) as docket_number'),
            // DB::raw('group_concat(purchase_req_number) as purchase_req_number'),
            DB::raw("group_concat(DISTINCT(concat(purchase_req_number,' : ',department.department))) as pur_req"),
            DB::raw('(ADDTIME(job_card.created_time,"3:00:00")) as planned_pr_date'),
            // 'purchase_req_number',
            DB::raw( 'internal_order.status as io_status')
            )->groupBy(
                'internal_order.id'
            )->get());

        $partydata=$partydata->offset($offset)->limit($limit);
        
        if(isset($request->input('order')[0]['column'])){
            $data =
            [
                'internal_order.id',
                'internal_order.io_number',
                'internal_order.created_time',
                'internal_order.reference_name',
                'item_category.name',
                'job_card.creative_name',
                'job_card.job_qty',
                'io_type.name',
                'job_details.delivery_date',
                'job_card.job_number',
                'job_card.created_time',
                'client_po.po_number',
                'client_po.is_po_provided',
                'client_po.created_at',
                'internal_order.status',
                'delivery_challan.challan_number',
                'delivery_challan.created_time',
                'challan_per_io.good_desc',
                'challan_per_io.good_qty',
                // 'tax_invoice.invoice_number',
                // 'tax.qty) as invoice_qty',
                // 'tax_invoice.total_amount',
                // 'tax_invoice.waybill_status',
                // 'asn.asn_number',
                // 'grn.grn_number',
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $partydata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else {
            $partydata->orderBy('internal_order.id', 'desc');
        }
        
        $partydata = $partydata->get();

        print_r(DB::getQueryLog());die();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $partydata; 
        // print_r($partydata);die;
        return json_encode($array);
    }

    //try

    public function getdata(Request $request){
        // print_r($request->input('search'));die;
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $search_value=$request->input('search');
        parse_str($search_value, $data);
        // print_r($data);die;
        // $yr=$data['yr'];
        $x='';
        $y='';
        $z='';
        $where='';
        $where1='';
        $search['internal_order']="";
        $search['item_name']=" left join `item_category` on `item_category`.`id` = `internal_order`.`item_category_id` ";
        $search['delivery_challan']=" left join `challan_per_io` on `challan_per_io`.`io` = `internal_order`.`id` 
        left join `delivery_challan` on `delivery_challan`.`id` = `challan_per_io`.`delivery_challan_id` ";
        $search['reference_name']=" left join `party_reference` on `party_reference`.`id` = `internal_order`.`reference_name` " ;
                foreach($data['search'] as $key=>$value){
                    if($value){
                        if($key!='item_name'){ $x=$x.$search[$key];}else{$z=$z.$search[$key];}
               
                if($key=='internal_order')
                    $where=$where.' AND `io_number` LIKE "%'.$value.'%"';
                if($key=='item_name')
                    $where1=$where1.' AND `item_category`.`name` LIKE "%'.$value.'%"';
                    
                if($key=='reference_name')
                    $where=$where.' AND `party_reference`.`referencename` LIKE "%'.$value.'%"';
                if($key=='delivery_challan')
                    $where=$where.' AND `delivery_challan`.`challan_number` LIKE "%'.$value.'%"';
                // echo $search[$key];
            }
            else{
                $y=$y.$search[$key];
            }
        }
        $srch=$data['search'];
        $data1="select
        `internal_order`.`id` as `io_id`,
        internal_order.io_number as io_number, (DATE_FORMAT(internal_order.created_time ,'%d-%m-%Y %r')) as io_date, 
        job_details.qty as qty, party_reference.referencename as reference_name, 
        (concat(item_category.name,if(`item_category`.name = 'Other',' : ',''),internal_order.other_item_name)) as item_category,
        io_type.name as io_type, job_card.created_time as actual_job_date, 
        (ADDTIME(internal_order.created_time,'2:00:00')) as planned_job_date,
        (DATE_FORMAT(job_details.delivery_date ,'%d-%m-%Y %r')) as delivery_date, 
        job_card.creative_name as creative_name, job_card.job_qty as job_qty, job_card.job_number as job_number,
        job_card.created_time as job_created_time,

        group_concat(DISTINCT(delivery_challan.challan_number)) as delivery_challan_number,
        job_details.delivery_date as planned_delivery_date, 
        delivery_challan.created_time as actual_delivery_date,
        sum(challan_per_io.good_qty) as sum_qty_dc, 


        group_concat(DISTINCT(concat(delivery_challan.challan_number,' : ',challan_per_io.good_qty))) as delivery_challan_good_qty,

            client_po.po_number, client_po.is_po_provided,
            client_po.created_at as actual_cpo_date,
            (SUBTIME(job_details.delivery_date ,'24:00:00')) as planned_cpo_date,

            group_concat(DISTINCT(tax_invoice.invoice_number)) as invoice_number, 
            (ADDTIME(job_details.delivery_date,'0:30:00')) as planned_tax_date,
             tax_invoice.created_at as actual_tax_date, 
            sum(tax.qty) as sum_qty_tax, sum(dispatch_tax.qty) as sum_qty_dispatch,

            group_concat(DISTINCT(concat(tax_invoice.invoice_number,' : ',tax.qty))) as tax_qty,
            group_concat(DISTINCT(concat(tax_invoice.invoice_number,' : ',tax_invoice.total_amount))) as tax_amount,

            group_concat(DISTINCT(concat(tax_invoice.invoice_number,' : ',tax_invoice.waybill_status))) as tax_waybill,
            group_concat(DISTINCT(concat(delivery_challan.challan_number,' : ',delivery_challan.waybill_status))) as challan_waybill,
            group_concat(way_challan.waybill_number) as way_challan_number,

            group_concat(DISTINCT(concat(tax_invoice.invoice_number,' : ',way_tax.waybill_number))) as way_tax_number,
            group_concat(asn.asn_number) as asn_number, 
            group_concat(grn.grn_number) as grn_number, 
        asn.created_time as asn_actual_date,
        grn.created_time as grn_actual_date, 
        group_concat(DISTINCT(tax_dispatch.docket_number)) as docket_number, 
        group_concat(DISTINCT(concat(purchase_req_number,' : ',department.department))) as pur_req,
        pur_indent.created_time as pur_actual_time, 
        (ADDTIME(job_card.created_time,'3:00:00')) as planned_pr_date, internal_order.status as io_status 
        from 						
        (select internal_order.*,
        (concat(item_category.name,if(`item_category`.name = 'Other',' : ',''),internal_order.other_item_name)) as item_category from `internal_order` ".$z." where `internal_order`.`status` = 'Open' " .$where1;
        $data1=$data1." ) as  internal_order
        $y
        $x
        left join `item_category` on `item_category`.`id` = `internal_order`.`item_category_id` 
        left join `job_details` on `job_details`.`id` = `internal_order`.`job_details_id` 
        left join `job_card` on `job_card`.`io_id` = `internal_order`.`id` 
        left join `client_po` on `client_po`.`io` = `internal_order`.`id` 
        left join `io_type` on `job_details`.`io_type_id` = `io_type`.`id` 
        left join `tax` on `tax`.`io_id` = `internal_order`.`id`  AND `tax`.`is_cancelled` = '0'
        left join `tax_invoice` on `tax_invoice`.`id` = `tax`.`tax_invoice_id` 
        left join `tax_dispatch` on `tax_dispatch`.`tax_invoice_id` = `tax_invoice`.`id` 
        left join `tax` as `dispatch_tax` on `tax_dispatch`.`tax_invoice_id` = `dispatch_tax`.`tax_invoice_id` 
        left join `waybill` as `way_challan` on `delivery_challan`.`id` = `way_challan`.`challan_id` 
        left join `waybill` as `way_tax` on `tax_invoice`.`id` = `way_tax`.`invoice_id` 
        left join `asn` on `asn`.`invoice_id` = `tax_invoice`.`id` left join `grn` on `grn`.`invoice_id` = `tax_invoice`.`id` 
        left join `pur_indent` on `pur_indent`.`io` = `internal_order`.`id` 
        left join `employee__profile` on `employee__profile`.`id` = `pur_indent`.`requested_by`
        left join `department` on `department`.`id` = `employee__profile`.`department_id`";
        $data1=$data1."where `internal_order`.`status` = 'Open'  ".$where.' group by `internal_order`.`id`';

        print_r($data1);die;
        $partydata=DB::select($data1);
        DB::enableQueryLog();

        // $stdClass = json_decode(json_encode($partydata));
        // $count =count($stdClass);

        // $partydata = $partydata->offset($offset)->limit($limit);
            $pp=$data1="select
            `internal_order`.`id` as `io_id`,
            internal_order.io_number as io_number, (DATE_FORMAT(internal_order.created_time ,'%d-%m-%Y %r')) as io_date, 
            job_details.qty as qty, party_reference.referencename as reference_name, 
            (ADDTIME(job_card.created_time,'3:00:00')) as planned_pr_date, internal_order.status as io_status 
            from 						
            (select internal_order.*,
            (concat(item_category.name,if(`item_category`.name = 'Other',' : ',''),internal_order.other_item_name)) as item_category from `internal_order` ".$z." where `internal_order`.`status` = 'Open' " .$where1;
            $pp=$pp." ) as  internal_order
            $y
            $x
            left join `item_category` on `item_category`.`id` = `internal_order`.`item_category_id` 
            left join `tax` on `tax`.`io_id` = `internal_order`.`id`  AND `tax`.`is_cancelled` = '0'
            left join `tax_invoice` on `tax_invoice`.`id` = `tax`.`tax_invoice_id` ";
            $pp=$pp."where `internal_order`.`status` = 'Open'  ".$where.' group by `internal_order`.`id`';

            $pp=DB::select($pp);
            DB::enableQueryLog();
    
            $stdClasspp = json_decode(json_encode($pp));
            $count =count($stdClasspp);
        // print_r($partydata);die();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $partydata; 
        return json_encode($array);

    }
    public function try_api(Request $request){
        $search_value=$request->input('search');
        // parse_str($search_value, $data);
        // print_r($search_value);die;
        $yr=$request->input('year');
            // $search = $request->input('search');
            // $serach_value = $request->input('search');
            $start = $request->input('start');
            $limit = $request->input('length');
            $offset = empty($start) ? 0 : $start ;
            $limit =  empty($limit) ? 10 : $limit ;
            DB::enableQueryLog();
            if(!isset($search_value)){
                if(isset($request->input('order')[0]['column']))
                {
                    $data =
                [
                    'internal_order.id',
                    'internal_order.created_time',
                    'reference_name',
                    'item_category',
                    'job_details.qty',
                    'io_type.name',
                    'job_details.delivery_date',
                    ' job_details.delivery_date',
                    ' job_details.delivery_date',
                    'job_card.job_number',
                    'job_card.created_time',
                    'client_po.po_number',
                    'client_po.is_po_provided',
                    'client_po.created_at',
                    'internal_order.status',
                    'delivery_challan.challan_number',
                    'delivery_challan.created_time',
                    'challan_per_io.good_desc',
                    'challan_per_io.good_qty',
                    'internal_order.io_number',
                   
                    // 'tax_invoice.invoice_number',
                    // 'tax.qty) as invoice_qty',
                    // 'tax_invoice.total_amount',
                    // 'tax_invoice.waybill_status',
                    // 'asn.asn_number',
                    // 'grn.grn_number',
                ];
                    $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                    $order="ORDER BY" ." ". $data[$request->input('order')[0]['column']] . " ". $by;
                }
                else{
                    $order="ORDER BY internal_order.id desc";
                }
                $datas="select
                `internal_order`.`id` as `io_id`,
                internal_order.io_number as io_number, (DATE_FORMAT(internal_order.created_time ,'%d-%m-%Y %r')) as io_date, 
                job_details.qty as qty, party_reference.referencename as reference_name, 
                (concat(item_category.name,if(`item_category`.name = 'Other',' : ',''),internal_order.other_item_name)) as item_category,
                io_type.name as io_type, job_card.created_time as actual_job_date, 
                (ADDTIME(internal_order.created_time,'2:00:00')) as planned_job_date,
                (DATE_FORMAT(job_details.delivery_date ,'%d-%m-%Y %r')) as delivery_date, 
                job_card.creative_name as creative_name, job_card.job_qty as job_qty, job_card.job_number as job_number,
                job_card.created_time as job_created_time,
        
                group_concat(DISTINCT(delivery_challan.challan_number)) as delivery_challan_number,
                job_details.delivery_date as planned_delivery_date, 
                delivery_challan.created_time as actual_delivery_date,
                sum(challan_per_io.good_qty) as sum_qty_dc, 
        
        
                group_concat(DISTINCT(concat(delivery_challan.challan_number,' : ',challan_per_io.good_qty))) as delivery_challan_good_qty,
        
                client_po.po_number, client_po.is_po_provided,
                client_po.created_at as actual_cpo_date,
                (SUBTIME(job_details.delivery_date ,'24:00:00')) as planned_cpo_date,
        
                group_concat(DISTINCT(tax_invoice.invoice_number)) as invoice_number, 
                (ADDTIME(job_details.delivery_date,'0:30:00')) as planned_tax_date,
                 tax_invoice.created_at as actual_tax_date, 
                sum(tax.qty) as sum_qty_tax, sum(dispatch_tax.qty) as sum_qty_dispatch,
        
                group_concat(DISTINCT(concat(tax_invoice.invoice_number,' : ',tax.qty))) as tax_qty,
                group_concat(DISTINCT(concat(tax_invoice.invoice_number,' : ',tax_invoice.total_amount))) as tax_amount,
        
                group_concat(DISTINCT(concat(tax_invoice.invoice_number,' : ',tax_invoice.waybill_status))) as tax_waybill,
                group_concat(DISTINCT(concat(delivery_challan.challan_number,' : ',delivery_challan.waybill_status))) as challan_waybill,
                group_concat(way_challan.waybill_number) as way_challan_number,
        
                group_concat(DISTINCT(concat(tax_invoice.invoice_number,' : ',way_tax.waybill_number))) as way_tax_number,
                group_concat(asn.asn_number) as asn_number, 
                group_concat(grn.grn_number) as grn_number, 
                asn.created_time as asn_actual_date,
                grn.created_time as grn_actual_date, 
                group_concat(DISTINCT(tax_dispatch.docket_number)) as docket_number, 
                group_concat(DISTINCT(concat(purchase_req_number,' : ',department.department))) as pur_req,
                pur_indent.created_time as pur_actual_time, 
                (ADDTIME(job_card.created_time,'3:00:00')) as planned_pr_date, internal_order.status as io_status 
        
                from 						
        
                (select * from `internal_order` where `internal_order`.`status` = 'Open' 
                $order limit $limit  offset $offset
                ) as  internal_order
                left join `job_details` on `job_details`.`id` = `internal_order`.`job_details_id` 
                left join `job_card` on `job_card`.`io_id` = `internal_order`.`id` 
                left join `item_category` on `item_category`.`id` = `internal_order`.`item_category_id` 
                left join `client_po` on `client_po`.`io` = `internal_order`.`id` 
                left join `challan_per_io` on `challan_per_io`.`io` = `internal_order`.`id` 
                left join `delivery_challan` on `delivery_challan`.`id` = `challan_per_io`.`delivery_challan_id` 
                left join `party_reference` on `party_reference`.`id` = `internal_order`.`reference_name` 
                left join `io_type` on `job_details`.`io_type_id` = `io_type`.`id` 
                left join `tax` on `tax`.`io_id` = `internal_order`.`id` 
                left join `tax_invoice` on `tax_invoice`.`id` = `tax`.`tax_invoice_id` 
                left join `tax_dispatch` on `tax_dispatch`.`tax_invoice_id` = `tax_invoice`.`id` 
                left join `tax` as `dispatch_tax` on `tax_dispatch`.`tax_invoice_id` = `dispatch_tax`.`tax_invoice_id` 
                left join `waybill` as `way_challan` on `delivery_challan`.`id` = `way_challan`.`challan_id` 
                left join `waybill` as `way_tax` on `tax_invoice`.`id` = `way_tax`.`invoice_id` 
                left join `asn` on `asn`.`invoice_id` = `tax_invoice`.`id` left join `grn` on `grn`.`invoice_id` = `tax_invoice`.`id` 
                left join `pur_indent` on `pur_indent`.`io` = `internal_order`.`id` 
                left join `employee__profile` on `employee__profile`.`id` = `pur_indent`.`requested_by`
                left join `department` on `department`.`id` = `employee__profile`.`department_id`
                where 'tax_invoice.is_cancelled' = 0";
        
                $partydata=DB::select($datas.' group by `internal_order`.`id`');
        
                            $count = count(InternalOrder::where('internal_order.status','Open')
                            // ->whereYear('internal_order.created_time', $yr)
                            ->get(['id']));
                            // print_r($count);die;
                            // $partydata = $partydata->offset($offset)->limit($limit);
                           
                          
                            // print_r($partydata);die();
        
                            $array['recordsTotal'] = $count;
                            $array['recordsFiltered'] = $count ;
                            $array['data'] = $partydata; 
                            return json_encode($array);
            }

            else{
                parse_str($search_value, $datas);
        // print_r($datas);die;
        // $yr=$data['yr'];
        $x='';
        $y='';
        $z='';
        $m='';
        $where='';
        $where1='';
        $where2='';
        $off='';
        $search['internal_order']="";
        $search['item_name']="";
        $search['delivery_challan']=" left join `challan_per_io` as `ch`  on `ch`.`io` = `internal_order`.`id` 
        left join `delivery_challan` as `dc` on `dc`.`id` = `ch`.`delivery_challan_id` ";
        $search['reference_name']=" left join `party_reference` as `ref` on `ref`.`id` = `internal_order`.`reference_name` " ;
                foreach($datas['search'] as $key=>$value){
                    if($value){
                        
                if($key=='internal_order')
                    $where=$where.' AND `io_number` LIKE "%'.$value.'%"';
                if($key=='item_name'){
                    $where1=$where1.' AND `internal_order`.`item_category_id` = "'.$value.'"'  ;
                    $where2=$where2.' AND `internal_order`.`item_category_id` = "'.$value.'"' ;
                    $z=$z.$search[$key];
                    $off=$off.  " limit ". $limit ." offset " .$offset;
                    $m=$m."" ;
                }
                else{
                    $x=$x.$search[$key];
                }
               
                    
                if($key=='reference_name')
                    $where=$where.' AND `ref`.`referencename` LIKE "%'.$value.'%"';
                if($key=='delivery_challan')
                    $where=$where.' AND `dc`.`challan_number` LIKE "%'.$value.'%"';
                // echo $search[$key];
            }
            else{
                $y=$y.$search[$key];
            }
        }
        $srch=$datas['search'];
        if(isset($request->input('order')[0]['column']))
        {
            $data =
        [
            'internal_order.id',
            'internal_order.created_time',
            'reference_name',
            'item_category',
            'job_details.qty',
            'io_type.name',
            'job_details.delivery_date',
            ' job_details.delivery_date',
            ' job_details.delivery_date',
            'job_card.job_number',
            'job_card.created_time',
            'client_po.po_number',
            'client_po.is_po_provided',
            'client_po.created_at',
            'internal_order.status',
            'delivery_challan.challan_number',
            'delivery_challan.created_time',
            'challan_per_io.good_desc',
            'challan_per_io.good_qty',
            'internal_order.io_number',
           
            // 'tax_invoice.invoice_number',
            // 'tax.qty) as invoice_qty',
            // 'tax_invoice.total_amount',
            // 'tax_invoice.waybill_status',
            // 'asn.asn_number',
            // 'grn.grn_number',
        ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $order="ORDER BY" ." ". $data[$request->input('order')[0]['column']] . " ". $by;
        }
        else{
            $order="ORDER BY internal_order.id desc";
        }
        $data1="select
        `internal_order`.`id` as `io_id`,
        internal_order.io_number as io_number, (DATE_FORMAT(internal_order.created_time ,'%d-%m-%Y %r')) as io_date, 
        job_details.qty as qty, party_reference.referencename as reference_name, 
        (concat(item.name,if(`item`.name = 'Other',' : ',''),internal_order.other_item_name)) as item_category,
        io_type.name as io_type, job_card.created_time as actual_job_date, 
        (ADDTIME(internal_order.created_time,'2:00:00')) as planned_job_date,
        (DATE_FORMAT(job_details.delivery_date ,'%d-%m-%Y %r')) as delivery_date, 
        job_card.creative_name as creative_name, job_card.job_qty as job_qty, job_card.job_number as job_number,
        job_card.created_time as job_created_time,

        group_concat(DISTINCT(delivery_challan.challan_number)) as delivery_challan_number,
        job_details.delivery_date as planned_delivery_date, 
        delivery_challan.created_time as actual_delivery_date,
        sum(challan_per_io.good_qty) as sum_qty_dc, 


        group_concat(DISTINCT(concat(delivery_challan.challan_number,' : ',challan_per_io.good_qty))) as delivery_challan_good_qty,

            client_po.po_number, client_po.is_po_provided,
            client_po.created_at as actual_cpo_date,
            (SUBTIME(job_details.delivery_date ,'24:00:00')) as planned_cpo_date,

           
            (ADDTIME(job_details.delivery_date,'0:30:00')) as planned_tax_date,
            
           

           

           
            group_concat(DISTINCT(concat(delivery_challan.challan_number,' : ',delivery_challan.waybill_status))) as challan_waybill,
            group_concat(way_challan.waybill_number) as way_challan_number,

            
           
      
        
        group_concat(DISTINCT(concat(purchase_req_number,' : ',department.department))) as pur_req,
        pur_indent.created_time as pur_actual_time, 
        (ADDTIME(job_card.created_time,'3:00:00')) as planned_pr_date, internal_order.status as io_status 
        from 						
        (select internal_order.* ".$m." from `internal_order` ".$z." " . $x." where    `internal_order`.`status` = 'Open' ".$where1 ." ".$where ." ".$order.  " limit ". $limit ." offset " .$offset;
        $data1=$data1." ) as  internal_order
        left join `party_reference` on `party_reference`.`id` = `internal_order`.`reference_name`
        left join `challan_per_io`  on `challan_per_io`.`io` = `internal_order`.`id` 
        left join `delivery_challan` on `delivery_challan`.`id` = `challan_per_io`.`delivery_challan_id` 
        left join `item_category` as `item` on `item`.`id` = `internal_order`.`item_category_id` 
        left join `job_details` on `job_details`.`id` = `internal_order`.`job_details_id` 
        left join `job_card` on `job_card`.`io_id` = `internal_order`.`id` 
        left join `client_po` on `client_po`.`io` = `internal_order`.`id` 
        left join `io_type` on `job_details`.`io_type_id` = `io_type`.`id` 
      
        left join `waybill` as `way_challan` on `delivery_challan`.`id` = `way_challan`.`challan_id` 
      
       
        left join `pur_indent` on `pur_indent`.`io` = `internal_order`.`id` 
        left join `employee__profile` on `employee__profile`.`id` = `pur_indent`.`requested_by`
        left join `department` on `department`.`id` = `employee__profile`.`department_id`";
        $data1=$data1."where `internal_order`.`status` = 'Open'  ".' group by `internal_order`.`id`';

        // print_r($data1);die;
        $partydata=DB::select($data1);
        DB::enableQueryLog();
     
        $pp="select
            `internal_order`.`id` as `io_id`
            from 						
            (select internal_order.* ".$m." from `internal_order` ".$z." where `internal_order`.`status` = 'Open' " .$where1;
            $pp=$pp." ) as  internal_order
            $y
            $x ";
            $pp=$pp."where `internal_order`.`status` = 'Open'  ".$where.' group by `internal_order`.`id`';

           
            $pp=DB::select($pp);
            DB::enableQueryLog();
    //  print_r($pp);die;
            $stdClass= json_decode(json_encode($pp));
            $count =count($stdClass);

           
            $stdClass1= json_decode(json_encode($partydata),true);
            // print_r($stdClass1);die;
            foreach($stdClass1 as $key=>$value){
                // print_r($value);die;
                $arr['invoice_number']=NULL;
                $arr['actual_tax_date']=NULL;
                $arr['sum_qty_tax']=NULL;
                $arr['sum_qty_dispatch']=NULL;
                $arr['tax_amount']=NULL;
                $arr['tax_qty']=NULL;
                $arr['asn_number']=NULL;
                $arr['grn_number']=NULL;
                $arr['asn_actual_date']=NULL;
                $arr['grn_actual_date']=NULL;
                $arr['docket_number']=NULL;
                $tax_invoice=Tax::where('tax.io_id',$value['io_id'])
                ->leftJoin('tax_invoice','tax_invoice.id','tax.tax_invoice_id')
                ->leftJoin('tax_dispatch','tax_dispatch.tax_invoice_id','tax_invoice.id')
                ->leftJoin('tax as dispatch_tax','tax_dispatch.tax_invoice_id','dispatch_tax.tax_invoice_id')
                
                ->leftJoin('waybill as way_tax','tax_invoice.id','way_tax.invoice_id')
                ->leftJoin('asn','asn.invoice_id','tax_invoice.id')
                ->leftJoin('grn','grn.invoice_id','tax_invoice.id')
            ->select(
                DB::raw('group_concat(DISTINCT(tax_invoice.invoice_number)) as invoice_number'),
                DB::raw('tax_invoice.created_at as actual_tax_date'),
                DB::raw('sum(tax.qty) as sum_qty_tax'),
                DB::raw('sum(dispatch_tax.qty) as sum_qty_dispatch'),
                DB::raw('group_concat(DISTINCT(concat(tax_invoice.invoice_number," : ",tax_invoice.total_amount))) as tax_amount'),
                DB::raw('group_concat(DISTINCT(concat(tax_invoice.invoice_number," : ",tax.qty))) as tax_qty'),
                DB::raw('group_concat(asn.asn_number) as asn_number'),
                DB::raw('group_concat(grn.grn_number) as grn_number'),
                DB::raw('asn.created_time as asn_actual_date'),
                DB::raw('grn.created_time as grn_actual_date'),
    
    
                DB::raw('group_concat(DISTINCT(tax_dispatch.docket_number)) as docket_number'),
                DB::raw('group_concat(DISTINCT(concat(tax_invoice.invoice_number," : ",tax_invoice.waybill_status))) as tax_waybill'),
                DB::raw('group_concat(DISTINCT(concat(tax_invoice.invoice_number," : ",way_tax.waybill_number))) as way_tax_number')
               
            
                )
                ->get()->first();
                // print_r($tax_invoice);die;
                $arr['invoice_number']=$tax_invoice['invoice_number'];
                $arr['actual_tax_date']=$tax_invoice['actual_tax_date'];
                $arr['sum_qty_tax']=$tax_invoice['sum_qty_tax'];
                $arr['sum_qty_dispatch']=$tax_invoice['sum_qty_dispatch'];
                $arr['tax_amount']=$tax_invoice['tax_amount'];
                $arr['tax_qty']=$tax_invoice['tax_qty'];
                $arr['asn_number']=$tax_invoice['asn_number'];
                $arr['grn_number']=$tax_invoice['grn_number'];
                $arr['asn_actual_date']=$tax_invoice['asn_actual_date'];
                $arr['grn_actual_date']=$tax_invoice['grn_actual_date'];
                $arr['docket_number']=$tax_invoice['docket_number'];

               
                    // print_r($key);die;
               $stdClass1[$key]=array_merge($value,$arr);
                // print_r($stdClass1[0]);die;
            }
            //     print_r($stdClass1);
            // die;

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $stdClass1; 
        return json_encode($array);
            }
          
           
       
                    
      
    }
}
