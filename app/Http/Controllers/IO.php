<?php

namespace App;
namespace App\Http\Controllers;
use App\Model\ItemCategory;
use App\Model\jobDetails;
use Route;
use App\Model\SectionRight;
use App\Model\UserSectionRight;
use DateTime;
use App\Imports\Import;
use Illuminate\Validation\Rule;
use App\Model\IoType;
use App\Model\Vehicle;
use App\Model\PoNumber;
use App\Model\Client_po_party;
use App\Model\FinancialYear;
use App\Model\Printing;
use App\Model\Tax_Invoice;
use App\Model\Tax_Dispatch;
use App\Model\DispatchPlan;
use App\Model\Dispatch_mode;
use App\Model\RequestPermission;
use App\Model\Payment;
use App\Model\Tax;
use App\Model\IORequestEdit;
use App\Model\Tax_Print;
use App\Model\Unit_of_measurement;
use App\Model\Challan_per_io;
use App\Model\Delivery_challan;
use App\Model\Goods_Dispatch;
use App\Model\Settings;
use App\Model\ElementFeeder;
use App\Model\Hsn;
use App\Model\Client_po;
use App\Model\advanceIO;
use App\Model\Client_po_data;
use App\Model\Client_po_consignee;
use App\Model\Consignee;
use App\Model\Binding_detail;
use App\Model\Raw_Material;
use App\Model\PaperType;
use App\Model\ElementType;
use App\Model\InternalOrder;
use App\Model\Binding_form_labels;
use App\Model\Binding_item;
use App\Model\JobCard;
use App\Model\Party;
use App\Model\State;
use App\Model\Country;
use App\Model\Employee\EmployeeProfile;
use App\Model\Waybill;
use App\Model\City;
use App\Model\Userlog;
use App\Model\JobDetailsView;
use App\Model\Users;
use App\Model\MasterMarketingPerson;
use App\Model\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use File;
use Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\dkerp;
use App\Model\Reference;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Custom\CustomHelpers;
use \Carbon\Carbon;
use App\Model\TaxPercentageApplicable;
class IO extends Controller
{
//--------------------------Internal Order Form--------------------------------------------------
    public function internal_order_form()
    {
        $item = ItemCategory::all();
        $hsn = Hsn::get(['hsn.gst_rate','hsn.hsn','hsn.id','hsn.item_id as name']);
        $users=MasterMarketingPerson::all();
        $settings = Settings::where('name','Old_IO_Prefix')->first();
        // $users=EmployeeProfile::where('department_id','=',10)->get();
        // $party=City::join('party', 'cities.id', '=', 'party.city_id')
        //     ->where('party.is_active',1)
        //     ->get(['party.id','party.partyname','party.city_id','cities.city']);
        $party = Reference::all();
        $io_type=IoType::all();
        $uom=Unit_of_measurement::all();
        $po_num=PoNumber::all();
        $data=array(
            'layout' => 'layouts.main',
            'item' => $item,
            'party' => $party,
            'po_num' => $po_num,
            'io_type' => $io_type,
            'uom' => $uom,
            'hsn' => $hsn,
            'settings'=>$settings->value,
            'users'=>$users
        );
 
        return view('sections.internalOrder',$data);
    }

    //----------------------- Internal Order Integration ----------------------------------------
    public function internal_insert(Request $request){

        $validation_array = [
            // 'party_name' => 'required|integer|exists:party,id',
            'reference_name'=>'required|string|exists:party,reference_name',
            'item' => 'required|integer',
            'io_type' => 'required|integer',
            'job_date' => 'required|date',
            'hsn' => 'required',
            'delivery_date' => 'required|date',
            'job_qty' => 'required|numeric',
            'job_qty_unit' => 'required|numeric',
            'job_size' => 'required',
            'job_rate' => 'required|numeric',
            'market' => 'required|integer',
            'details' => 'required',
            'front_color' => 'required|numeric',
            'back_color' => 'required|numeric',
            'paper' => ['required', Rule::in(['Party', 'Press', 'NA'])],
            'plates' => ['required', Rule::in(['Party', 'Press', 'OldPlates', 'NA'])],
            'transportaion_charges' => 'required|numeric',
            'other_charges' => 'required|numeric',
            'remark' => 'required',
            'adv_received' => 'required|boolean',
        ];
        $posted_date = $request->input('challan_number_status')=="New" ? date('Y-m-d G:i:s') : date('Y-m-d',strtotime($request->input('del_date_old')));
       
        $settings = Settings::where('name','internal_order_prefix')->first();
       
        $fromDate= date('Y-m',strtotime($request->input('job_date')));
        $date=date_create($fromDate);
        $finan=FinancialYear::where('from', '<=', $fromDate)
        ->where('to', '>=', $fromDate)->first();
       
        if($finan){
            $year=$finan->financial_year;
            $financial_year=$finan['financial_year'];
        }
        else{
            return redirect('/internalorder')->with('error','Enter Document Date According to Financial Year.')->withInput();
        }
        

        if($request->input('io_number_status')=='Old'){
           
            $io_number = $request->input('old_io');

                // if(strpos($io_number, $financial_year) !== false){
                    $prefix = $io_number;
                // }
                // else{
                //     return redirect('/internalorder')->with('error','Enter Old  Series According to Job Date Financial Year.')->withInput();
                // }
            $ar=[
              
                'old_io'=>'required|unique:delivery_challan,challan_number'
            ];
            $validation_array = array_merge($validation_array,$ar);  
        }
        else if($request->input('io_number_status')=='New'){
          
                $old_io=InternalOrder::where('io_number_status','New')
                ->where('financial_year',$year)
                ->get('io_number')->last();
              
            if($old_io){

                $dc=explode('/',$old_io['io_number']);
                $v = (int)$dc[count($dc)-1];
                $dc_id=$v+1;
               
            }
            else{
                $dc_id=1;
            }
         
            $io_number = $settings->value;
            $io_number = $io_number ."/".$year.'/'.$dc_id;
            $prefix = $io_number;
         
        }
        else{
           
             $io_number = $settings->value;
             $prefix = $io_number;
        }
        $this->validate($request,$validation_array);
        // print_r($prefix);die;

        if($request->input('adv_received')){
            // $validation_array['amount'] = 'required|numeric';
            // $validation_array['amt_received_date'] = 'required|date';
            $validation_array['mode_received'] = ['required', Rule::in(['0', '1', '2'])];
        }
        $request->validate($validation_array);
        try 
        {
            $adv_received=$request->input('adv_received');
            // job Date
            $date2 = strtotime($request->input('job_date'));
            $newDate2 = date("Y-m-d", $date2);
            // Amount Date
            $date3 = strtotime($request->input('amt_received_date'));
            $newDate3 = date("Y-m-d", $date3);
            // delivery Date
            $date4 = strtotime($request->input('delivery_date'));
            $newDate4 = date("Y-m-d", $date4);
            $timestamp = date('Y-m-d G:i:s');
            $other_name = $request->input('other_item_name');
            $other_name = $other_name!=""?$other_name:'';

            if ($adv_received==1)
            {
                $amount_id= advanceIO::insertGetId([
                    'id' => NULL,
                    'amount' => $request->input('amount'),
                    'mode_of_receive' =>$request->input('mode_received'),
                    'date' => $newDate3,
                ]);
            }
            else
            {
                $amount_id=NULL;
            }
            $jobdetail_id= jobDetails::insertGetId([
                'id' => NULL,
                'io_type_id' => $request->input('io_type'),
                'job_date' =>$newDate2,
                'hsn_code' => $request->input('hsn'),
                'delivery_date' => $newDate4,
                'qty' =>$request->input('job_qty'),
                'left_qty' =>$request->input('job_qty'),
                'unit' => $request->input('job_qty_unit'),
                'job_size' => $request->input('job_size'),
                'dimension' => $request->input('dimension'),
                'rate_per_qty' =>$request->input('job_rate'),
                'marketing_user_id' =>$request->input('market'),
                'details' => $request->input('details'),
                'front_color' =>$request->input('front_color'),
                'back_color' => $request->input('back_color'),
                'is_supplied_paper' =>$request->input('paper'),
                'is_supplied_plate' =>$request->input('plates'),
                'remarks' => $request->input('remark'),
                'transportation_charge' =>$request->input('transportaion_charges'),
                'other_charge' => $request->input('other_charges'),
                'advanced_received' => $request->input('adv_received'),
                'advance_io_id' =>$amount_id
            ]);
                if ( !empty($jobdetail_id))
                {
                    
                    $io_id= InternalOrder::insertGetId(
                        [
                            'id' => NULL,
                            'io_number'=> $io_number,
                            'io_number_status'=>$request->input('io_number_status'),
                            'financial_year'=>$request->input('io_number_status') =='New' ? $year : $financial_year,
                            'party_id' =>0,
                            'reference_name' => $request->input('reference_name'),
                            'item_category_id' =>$request->input('item'),
                            'other_item_name' =>$other_name,
                            'job_details_id' => $jobdetail_id,
                            'created_by' => Auth::id(),
                            'is_active' =>1,
                            'created_time' => date('Y-m-d G:i:s') ,
                        ]
                    );
                    
                }
                else
                {
                    return redirect('/internalorder')->with('error','Some error occurred')->withInput();
                }
                 /*** USER LOG ***/
            $hsn = Hsn::where('id',$request->input('hsn'))->selectRaw("CONCAT(hsn.item_id,' - ',hsn.hsn,' - ',hsn.gst_rate) as name")->get()->first();
            $hsn = isset($hsn->name)?$hsn->name:'';
            $changes_array['hsn'] = $hsn;

            $reference=Reference::where('id',$request->input('reference_name'))->select('referencename')->get()->first();
            $reference_name = isset($reference->referencename)?$reference->referencename:'';
            $changes_array['reference_name'] = $reference_name;

            $item=ItemCategory::where('id',$request->input('item'))->select('name')->get()->first();
            $item = isset($item->name)?$item->name:'';
            $changes_array['item'] = $item;

            $job_qty_unit=Unit_of_measurement::where('id',$request->input('job_qty_unit'))->select('uom_name')->get()->first();
            $job_qty_unit = isset($job_qty_unit->uom_name)?$job_qty_unit->uom_name:'';
            $changes_array['job_qty_unit'] = $job_qty_unit;

          if($request->input('io_type')!=null){
            $io_type=IoType::where('id',$request->input('io_type'))->select('name')->get()->first();
            $io_type = isset($io_type->name)?$io_type->name:'';
            $changes_array['io_type'] = $io_type;
          }
          $changes_array['job_date'] =$request->input('job_date');
          $changes_array['delivery_date'] =$request->input('delivery_date');
          $changes_array['job_qty'] =$request->input('job_qty');
          $changes_array['job_size'] =$request->input('job_size');
          $changes_array['dimension'] =$request->input('dimension');

          $changes_array['job_rate'] =$request->input('job_rate');
          $changes_array['details'] =$request->input('details');
          $changes_array['front_color'] =$request->input('front_color');
          $changes_array['back_color'] =$request->input('back_color');

          $changes_array['paper'] =$request->input('paper');
          $changes_array['plates'] =$request->input('plates');
          $changes_array['transportaion_charges'] =$request->input('transportaion_charges');
          $changes_array['other_charges'] =$request->input('other_charges');
          $changes_array['remark'] =$request->input('remark');

          $changes_array['adv_received'] =$request->input('adv_received');
          $changes_array['mode_received'] =$request->input('mode_received');
          $changes_array['amt_received_date'] =$request->input('amt_received_date');
          $changes_array['amount'] =$request->input('amount');
          

            CustomHelpers::userActionLog('Internal Order Created',$io_id,'Internal Order Created',
            $log_array=array(
                'hsn'=>'HSN Code',
                'reference_name'=>'Reference Name',
                'item'=>'Item Category',
                'io_type'=>'IO Type',
                'job_date'=>'Job Date',

                'job_date'=>'Job Date',
                'delivery_date'=>'Delivery Date',
                'job_qty'=>'Job Qty',
                'job_qty_unit'=>'Job Qty Unit',
                'job_size'=>'Job Size',
                'dimension'=>'Dimension',
                'job_rate'=>'Job Rate',
                'details'=>'Job Details',
                'front_color'=>'Front Color',
                'back_color'=>'Back Color',
                'paper'=>'Paper',
                'plates'=>'Plates',
                'transportaion_charges'=>'Transportation Charges',
                'other_charges'=>'Other Charges',
                'remark'=>'Remark',
                'adv_received'=>'Advanced Received',
                'mode_received'=>'Mode Received',
                'amt_received_date'=>'Amount Received Date',
                'amount'=>'Amount'
                // ''=>'',
            
            ),
            $changes_array,$removekeys=array('io_type_old','old_job_qty','old_leftqty'));
            /***  END USER LOG ***/
                return redirect('/internalorder')->with(['internal'=>'successfull','io_id'=>$io_id,'prefix'=>$prefix]);
            } catch(\Illuminate\Database\QueryException $ex) {
                // print_r($ex->getMessage());die;
                return redirect('/internalorder')->with('error','some error occurred'.$ex->getMessage())->withInput();
            }
    }


/**
 * Client po creation form
 */
    public function get_io_details_by_po_io($id){
        $io = InternalOrder::leftjoin('job_details','internal_order.job_details_id','=','job_details.id')
                ->where('internal_order.id',$id)->first();
        return $io;
    }

    public function get_po_details_by_po($id){
        $io = Client_po::leftJoin('internal_order','client_po.io','internal_order.id')
        ->where('po_number','=',$id)
        ->select(
            'po_date',
            'delivery_date',
            'discount',
            'client_po.id',
            'internal_order.party_id'
        )
        ->get()
        ->first();
            //doubt
        $io_list = InternalOrder::leftJoin('client_po','internal_order.id','client_po.io')
        ->whereRaw( 'internal_order.party_id = ? and( po_number <> ? or po_number is null )',[$io['party_id'],$id])->get()->toArray();
      
        // if($io->is_consignee==1)
        // {
        //     $consignee = Client_po_consignee::where('client_po_consignee.client_po_id',$io->id)
        //     ->select(
        //         'consignee_id',
        //         'qty'
        //     )->get()->toArray();
        // }
        $data=array(
            'io'=>$io,
            // 'consignee'=>$consignee,
            'io_list'=>$io_list
            
        );
        return $data;
    }
    public function client_po_details_by_refname($id){//doubt
       
        // $party_det = Party::where('reference_name','like',$id)->where('party.is_active',1)->get('id')->toArray();
        $party = Party::where('reference_name','=',$id)->where('party.is_active',1)->get(['id','partyname'])->toArray();
        $cliet_po_ios = Client_po::where('reference_name',$id)->get('io')->toArray();
        $details=InternalOrder::where('reference_name', $id)
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->whereNotIn('internal_order.id',$cliet_po_ios)
        ->whereNotIn('job_details.io_type_id',array('9'))
        ->where('internal_order.is_active',1)
        ->select('internal_order.id as ioid','io_number','item_category_id','other_item_name','reference_name','job_details.*')->get();
        $po_number = Client_po::where('reference_name',$id)
        ->leftJoin('client_po_data','client_po_data.client_po_id','client_po.id')->selectRaw('DISTINCT(client_po_data.po_number)')->get();
        $arr = array('io_list' => $details,  'party' => $party,
            'po_number'=>$po_number);
        return $arr;
    }
    public function client_po_details_by_partyname($id)
    {
        $consignee = Consignee::where('party_id',$id)->where('consignee.is_active',1)->get();
        $party = Party::where('id',$id)->where('party.is_active',1)->first(['payment_term_id']);
        $arr = array(
            'consg_list' => $consignee,
            'party' => $party
        );
        return $arr;
    }
    public function client_po_form(Request $request){
       
        $feed['reference'] = Reference::all();
        $feed['prefix_io'] = Settings::where('name', '=', 'internal_order_prefix')
                        ->first()->value;
        $feed['hsn'] = Hsn::get(['hsn.gst_rate','hsn.hsn','hsn.id','hsn.item_id as name']);
        $feed['pay_term'] = Payment::all();
        $feed['tax_per'] = TaxPercentageApplicable::all();
        $feed['uom'] = Unit_of_measurement::all();
        if ($request->old('party_name') != null) {
            $feed['io_feed'] = InternalOrder::where('party_id', '=', $request->old('party_name'))
                                    ->get();
        }
        $data = array('layout'=>'layouts.main','feed' => $feed);
        return view('sections.client_po', $data);
    }

/**
 * Post submission of client po create form
 */
public function clientPoInsert(Request $request){
    // print_r($request->input());die;
    $data = $request->input();
    unset($data['pay']);
    unset($data['is_con']);
    unset($data['cons']);
    unset($data['quan']);
   
    $val_arr = [
        'reference_name' => 'required|exists:party,reference_name',
        'io' => 'required|exists:internal_order,id|unique:client_po,io',
        'is_po_provided' => 'required|boolean',
        'created_by' => 'required|exists:users,id'
    ];

    if(isset($data['is_po_provided']) && $data['is_po_provided'] == 1){
        $val_arr2 = [
            'party_name.*' => 'required|exists:party,id',
            'po_number.*' => 'required_if:po_type,0',
            'po_number1.*' => 'required_if:po_type,1',
            'hsn' => 'required|exists:hsn,id',
            'payment_terms' => 'required|exists:payment_term,id',
            'item_desc' => 'required',
            'delivery_date' => 'required|date',
            'qty' => 'required|integer',
            'unit_of_measure' => 'required|exists:unit_of_measurement,id',
            'per_unit_price' => 'required|numeric',
            'discount' => 'required|numeric',
            'is_consignee.*' => 'required|boolean',
            'po_files.*'=>'required',
            'poqty.*'=>'required',
            'po_dates.*'=>'required'
        ];
        $val_arr = array_merge($val_arr, $val_arr2);
        
    }
    else
    {
        try
        {

     
            $validator = $request->validate($val_arr);
            $remove = ['_token','userAlloweds','party_name','po_type','po_number','po_number1', 'po_date',
                    'hsn', 'payment_terms', 'item_desc', 'delivery_date','qty', 'unit_of_measure', 'per_unit_price',
                    'discount','po_file', 'tax_perc_applicable', 'is_consignee'];
            $data = array_diff_key($data, array_flip($remove)); 
            // print_r($data);die; 
            $client_po = Client_po::insertGetId([
                'reference_name'=>$request->input('reference_name'),
                'io'=>$request->input('io'),
                'is_po_provided'=>0,
                'created_by'=>Auth::id()
            ]);
             /*** USER LOG ***/

      $io = InternalOrder::where('id',$request->input('io'))->select('io_number')->get()->first();
      $io = isset($io->io_number)?$io->io_number:'';
      $changes_array['io'] = $io;

      $reference=Reference::where('id',$request->input('reference_name'))->select('referencename')->get()->first();
      $reference_name = isset($reference->referencename)?$reference->referencename:'';
      $changes_array['reference_name'] = $reference_name;

      CustomHelpers::userActionLog('Client PO Created',$client_po,'Client PO Created',
      $log_array=array(
          
          'reference_name'=>'Reference Name',
          'io'=>'Internal Order'
      ),
      $changes_array,$removekeys=array('consignee_qty',
      'old_consignee',
      'old_quantity',
      'consignee_name','con_id','is_consignee','payment_terms','client_po_party_id','old_party'));
      /***  END USER LOG ***/
            return redirect('/clientpo')->with('success','Client Purchase Order has been created!')->withInput();
        }
        catch(Exception $ex){
            return redirect('/clientpo')->with('error','Something went wrong!'.$ex->getMessage())->withInput();
        }

    }
    // extracting excel and applying validation
    $validator = $request->validate($val_arr);
    if(isset($data['is_po_provided']) && $data['is_po_provided'] == 1)
    {    
        $party = $data['party_name'];
        foreach($party as $pid)
        {
            if($request->file('excel.'.$pid)){
                $val_arr['excel.'.$pid] = 'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx';
                $path[$pid] = $request->file('excel.'.$pid);
            }
        }
    }
    // if($request->hasFile('po_file'))
    // {
    //     $file = $request->file('po_file');
    //     $destinationPath = public_path().'/upload/clientpo';
    //     $filenameWithExt = $file->getClientOriginalName();
    //     $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);      
    //     $extension = $file->getClientOriginalExtension();
    //     $fileNameToStore = $filename.'_'.time().'.'.$extension;  
    //     $path1 = $file->move($destinationPath, $fileNameToStore);
    //     $data['po_file_name'] = $fileNameToStore;
    // }
    // unset($data['po_file']);
    unset($data['excel']);

    // performing server sided validation
    // extracting data for `Client_po_consignee`
    if (isset($data['consignee_name']) && (!empty($data['consignee_name']))) {
        $consg_name = $data['consignee_name'];
        $consg_qty = $data['consignee_qty'];
    }
    $reference = $data['reference_name'];
    $party = $data['party_name'];
    $payment = $data['payment_terms'];
    $is_consignee = $data['is_consignee'];
    $po_types=$data['po_type'];
    foreach($po_types as $key=>$value){
        if($value==1 )
        {
            $data['po_number'][$key] = $data['po_number1'][$key];
        }
    }
    // print_r($data['po_number']);die;
    // removing unnessary data from `client_po`

    unset($data['po_type']);
    unset($data['po_number1']);   
    unset($data['userAlloweds']);
    unset($data['_token']);
    unset($data['consignee_name']);
    unset($data['consignee_qty']);

    // converting time format
    if (isset($data['is_po_provided']) && $data['is_po_provided'] == 1) {
        $data['delivery_date'] = date('Y-m-d G:i:s', strtotime($data['delivery_date']));
        // $data['po_date'] = date('Y-m-d', strtotime($data['po_date']));
    }
    
    $new_data = array();
    $po_data = array();
    foreach($party as $pid)
        $new_data=array_merge($new_data,array($data));

    for($i=0;$i<count($new_data);$i++)
    {
        $new_data[$i]['party_name'] =$party[$i]; 
        $new_data[$i]['payment_terms'] =$new_data[$i]['payment_terms'][$party[$i]]; 
        $new_data[$i]['is_consignee'] =$new_data[$i]['is_consignee'][$party[$i]]; 
    }
    DB::beginTransaction(); 
    // inserting client_po and getting it's id
    try 
    {
        $po_numbers=$data['po_number'];
        $po_qtys=$data['poqty'];
        $po_dates=$data['po_dates'];
        unset($data['po_number']);
        unset($data['party_name']);
        unset($data['is_consignee']);
        unset($data['payment_terms']);
        unset($data['poqty']);
        unset($data['po_files']);
        unset($data['po_dates']);
       
        $data['po_number']=implode(',',$po_numbers); // remove this line ....
        $sum_qty=array_sum($po_qtys);
        $io = InternalOrder::where('internal_order.id',$request->input('io'))->leftJoin('job_details','job_details.id','internal_order.job_details_id')->select('io_number','qty')->get()->first();
        // print_r($io['qty']);die;
        if($sum_qty>$io['qty']){
            DB::rollback();
            return redirect('/clientpo')->with('error', 'PO Quantity Not Equal To IO Quantity.')->withInput();
           }
        //    print_r($data);die;
        $client_po = Client_po::insertGetId($data);
        $cc=count($po_numbers);
        //    print_r($po_numbers);die;
       
        foreach($po_types as $key=>$value){
           
                $file=$request->file('po_files'); 
                if(isset($file[$key])){
                    $destinationPath = public_path().'/upload/clientpo';
                    $filenameWithExt = $file[$key]->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);      
                    $extension = $file[$key]->getClientOriginalExtension();
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;  
                    $path1 = $file[$key]->move($destinationPath, $fileNameToStore);
                }
                else{
                    $fileNameToStore=NULL;
                } 
            
            $client_po_data=Client_po_data::insertGetId([
                'client_po_id'=>$client_po,
                'po_number'=>$po_numbers[$key],
                'po_qty'=>$po_qtys[$key],
                'po_date'=>date('Y-m-d',strtotime($po_dates[$key])),
                'po_upload'=>$fileNameToStore

            ]);
        }
         /*** USER LOG ***/

      $io = InternalOrder::where('id',$request->input('io'))->select('io_number')->get()->first();
      $io = isset($io->io_number)?$io->io_number:'';
      $changes_array['io'] = $io;


      $hsn = Hsn::where('id',$request->input('hsn'))->selectRaw("CONCAT(hsn.item_id,' - ',hsn.hsn,' - ',hsn.gst_rate) as name")->get()->first();
      $hsn = isset($hsn->name)?$hsn->name:'';
      $changes_array['hsn'] = $hsn;

      $reference=Reference::where('id',$request->input('reference_name'))->select('referencename')->get()->first();
      $reference_name = isset($reference->referencename)?$reference->referencename:'';
      $changes_array['reference_name'] = $reference_name;

 
      $unit_of_measure=Unit_of_measurement::where('id',$request->input('unit_of_measure'))->select('uom_name')->get()->first();
      $unit_of_measure = isset($unit_of_measure->uom_name)?$unit_of_measure->uom_name:'';
      $changes_array['unit_of_measure'] = $unit_of_measure;

      $party_name=Party::where('id',$request->input('party_name'))->select('partyname')->get()->first();
      $party_name = isset($party_name->partyname)?$party_name->partyname:'';
      $changes_array['party_name'] = $party_name;

   
    // $changes_array['po_number'] =$request->input('po_number');
    // $changes_array['po_date'] =$request->input('po_date');
    // $changes_array['item_desc'] =$request->input('item_desc');
    // $changes_array['delivery_date'] =$request->input('delivery_date');

    // $changes_array['qty'] =$request->input('qty');
    // $changes_array['per_unit_price'] =$request->input('per_unit_price');
    // $changes_array['discount'] =$request->input('discount');
  
    CustomHelpers::userActionLog('Client PO Created',$client_po,'Client PO Created',
      $log_array=array(
          
          'reference_name'=>'Reference Name',
          'io'=>'Internal Order',
          'party_name'=>'Party',
          'po_number'=>'PO Order',
          'po_date'=>'PO Date',
          'hsn'=>'HSN Code',
          'unit_of_measure'=>'Qty Unit',
          'item_desc'=>'Item Description',
          'delivery_date'=>'Delivery Date',
          'qty'=>'Quantity',
          'per_unit_price'=>'Per Unit Price',
          'discount'=>'Discount',
      ),
      $changes_array,$removekeys=array('consignee_qty',
      'old_consignee',
      'old_quantity',
      'consignee_name','con_id','is_consignee','payment_terms','client_po_party_id','old_party'));
      /***  END USER LOG ***/
        $po_data['client_po_id']=$client_po;
        if($client_po)
        {
            for($k=0;$k<count($party);$k++) 
            {    
                if(!isset($path[$party[$k]]))
                {
                    $client_party[$party[$k]]=Client_po_party::insertGetId([
                        'client_po_id'=>$client_po,
                        'party_name'=>$party[$k],
                        'payment_terms'=>$payment[$party[$k]],
                        'is_consignee'=>$is_consignee[$party[$k]],
                    ]);
                }
            }
        }
    
        // using id from above to multiple enter `Client_po_consignee`
        $qty_sum=0; 
        $count = count($party);
        for ($i = 0; $i < $count; $i++) 
        {
            if(!isset($path[$party[$i]]))
            {
                if (isset($consg_name) && is_array($consg_name) && count($consg_name)>0 && $new_data[$i]['is_consignee'] == 1 && isset($client_po)) 
                {
                    $count1 = count($consg_name[$party[$i]]);
                    for ($j = 0; $j < $count1; $j++) 
                    {
                        $qty_sum = $qty_sum+ $consg_qty[$party[$i]][$j];
                        Client_po_consignee::insert([
                            'consignee_id' => $consg_name[$party[$i]][$j],
                            'client_po_party_id'=>$client_party[$party[$i]],
                            'qty' => $consg_qty[$party[$i]][$j],
                            'client_po_id' => $client_po,
                            'party_id'=>$party[$i]
                        ]);
                    }
                }
            }
        }
    }
    catch (\Illuminate\Database\QueryException $ex) {
        DB::rollback();
        return redirect('/clientpo')->with('error','Something went wrong!'.$ex->getMessage())->withInput();
    }
    $total_error = 0;
    $column_name_err=0;
    for($party_acc=0;$party_acc<count($party);$party_acc++)
    {
        $pid = $party[$party_acc];
        //storing list from excel
        if(isset($path[$pid]) && $new_data[$party_acc]['is_consignee'] == 1 && isset($client_po))
        {
            $party_id = $pid;
            $client_party[$party_id]=Client_po_party::insertGetId([
                'client_po_id'=>$client_po ,
                'party_name'=>$party_id,
                'payment_terms'=>$payment[$party_id],
                'is_consignee'=>$is_consignee[$party_id]
            ]);

            $data = Excel::toArray(new Import(),$path[$pid]);
            $error = "";
            if($data){    
                $v = $data[0];
            }
            $column_name_format = array('consignee_name','gst','pan','address','pincode','country','state','city', 'qty');
            $index = 0;
      
            $char = '65';
            foreach($v[0] as $p=>$q)
            {

                if($q!=$column_name_format[$index]){
                    $column_name_err++;
                    $error=$error."Column Name not in provided format. Error At ".chr($char)."1.";
                }
                $index++;
                $char++;
            }

            $total_error = $total_error + $column_name_err;
            if($column_name_err == 0)
            {
                for($i = 1; $i < count($v); $i++)   
                {
                    $char = '65';
                    $fl=0;
                    if($v[$i][8]=="")
                        $v[$i][8]=0;
                    foreach ($v[$i] as $k=>$v1)
                    {
                        if($v1 == "")
                        {
                            $error=$error."Empty Cell at ".chr($char).($i).". "; 
                            $fl++;
                        }
                        $char++; 
                    }
                    $total_error = $total_error + $fl;
                    if($fl==0)
                    {
                        $data_err=0;
                        $country = Country::where('name','like',$v[$i]['5'])
                        ->get('id')->first();
                        if(!$country)
                        {
                            $error = $error."Country at F".($i)." Not found.<br/>";
                            $data_err++;
                        }
                        else
                            $state = State::where('country_id','=',$country->id)->
                            where('name','like',$v[$i]['6'])
                            ->get('id')->first();
                        if(!$country || !$state)
                        {
                            $error = $error."State at G".($i)." Not found.<br/>";
                            $data_err++;
                        }
                        else
                            $city = City::where('state_id','=',$state->id)
                                        ->where('city','like',$v[$i]['7'])
                                        ->get('id')->first();
                        if(!$country || !$state || !$city )
                        {
                            $error = $error."City at H".($i)." Not found.<br/>";
                            $data_err++;
                        }
                        $total_error = $total_error + $data_err;
                    
                        if($data_err == 0)
                        {
                            try{                    
                                $consg_name_id = Consignee::insertGetId(
                                    [
                                        'id' => NULL,
                                        'consignee_name' => $v[$i][0],
                                        'party_id' =>$party_id,
                                        'gst' =>$v[$i][1],
                                        'pan' =>$v[$i][2],
                                        'address' =>$v[$i][3],
                                        'city' =>$city->id,
                                        'pincode' =>$v[$i][4],
                                        'state' =>$state->id,
                                        'country' =>$country->id,
                                        'created_by'=>Auth::id(),
                                        'is_active'=>'1',
                                        'created_time'=>date('Y-m-d G:i:s'),
                                    ]);
                                $qty_sum = $qty_sum + $v[$i][8];
                               
                                Client_po_consignee::insert([

                                    'client_po_party_id'=>$client_party[$party_id],
                                    'party_id'=>$pid,
                                    'consignee_id' => $consg_name_id,
                                    'qty' => $v[$i][8],
                                    'client_po_id' => $client_po
                                ]);
                            
                            }catch (\Illuminate\Database\QueryException $ex) {
                                DB::rollback();
                                return redirect('/clientpo')->with('error', 'Something went wrong!')->withInput();
                            }
                        }
                    }
                    else{
                        $error=$error."<br/>";
                        DB::rollback();
                    }
                }
                
            }
            else
            {
                DB::rollback();
                $error = $error." No data is inserted.";
            }
        }
    }

    $is_consignee_counter=0;
    foreach($new_data as $nd)
        $is_consignee_counter = $is_consignee_counter + $nd['is_consignee'];
    if($total_error==0)
    {
        if($qty_sum!=$new_data[0]['qty'] && $is_consignee_counter!=0)
        {
           
            DB::rollback();
            return redirect('/clientpo')->with('error', 'Consignee Quantity Not Equal To Purchase Order Quantity.')->withInput();
        }
        DB::commit();
       
        $request->session()->flash('importerrors',"");
        
        return redirect('/clientpo')->with('success', 'Client Purchase Order has been created');
    }
    else if($column_name_err!=0)
    {
        $request->session()->flash('importerrors', $error);
        return redirect('/clientpo')->with('error', 'Errors Found1.')->withInput();        
    }
    else
    {
        $request->session()->flash('importerrors', $error."Rest Data is Inserted.");
        return redirect('/clientpo')->with('error', $error.'Errors Found2.')->withInput();        
    }
    
}
/**
 * Intenal Orders list page
 */
public function internal_list(Request $request,$status){
    $prefix = Settings::where('name', '=', 'internal_order_prefix')->first()->value;
    $data=array('layout'=>'layouts.main', 'prefix'=> $prefix);
    if($status=='*')
        $status='open';
    $data['status'] = $status;
    return view('sections.internal_list', $data);
}

/**
 * api made to serve IO List
 */ 
public function internal_all(Request $request){
    $search = $request->input('search');
    $serach_value = $search['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $offset = empty($start) ? 0 : $start ;
    $limit =  empty($limit) ? 10 : $limit ;
    $status = $request->input('status');
    $partydata = InternalOrder::leftJoin('party', function($join) {
                                    $join->on('internal_order.party_id', '=', 'party.id');
                                })->leftJoin('job_details',function($join){
                                    $join->on('internal_order.job_details_id','=','job_details.id');
                                })->leftJoin('item_category',function($join){
                                    $join->on('internal_order.item_category_id','=','item_category.id');
                                })
                                ->leftJoin('io_type','io_type.id','job_details.io_type_id')
                                ->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
                                ->leftJoin('request_permission',function($join){
                                    $join->on('request_permission.data_id','=','internal_order.id');
                                    $join->where('request_permission.status','=','pending');
                                    $join->where('request_permission.data_for','=','internalorder');
                                })
                               ;
    $partydata->where('internal_order.status',$status);
    if(!empty($serach_value))
    {
        $partydata->where(function($query) use ($serach_value){
                $query->where('party_reference.referencename','LIKE',"%".$serach_value."%")
                    ->orWhere('internal_order.io_number','LIKE',"%".$serach_value."%")
                    ->orWhere('io_type.name','LIKE',"%".$serach_value."%")
                    ->orWhere('job_details.qty','LIKE',"%".$serach_value."%")
                    ->orWhere('item_category.name','LIKE',"%".$serach_value."%");
            }); 
    }
    $partydata->select(
        
        DB::raw('CONVERT(SUBSTRING_INDEX(io_number,"/",-1),UNSIGNED  INTEGER) as asd'),
        'internal_order.id',
        'internal_order.io_number',
        'party_reference.referencename as reference_name',
        'job_details.qty',
        'financial_year',
        'request_permission.status as st',
        'io_type.name as io_type',
        'job_details.details',
        'io_number_status',
       
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as job_date'),
        // 'internal_order.created_time as date',
        DB::raw('(DATE_FORMAT(internal_order.created_time ,"%d-%m-%Y %r")) as date'),
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as name')
        )
        ->where('internal_order.is_active', '=', '1');
    if(isset($request->input('order')[0]['column'])){
        $data = ['id', 'date', 'party.reference_name', 'name', 'qty','job_details.job_date'];
        $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
        $partydata->orderBy($data[$request->input('order')[0]['column']], $by);
    }
    else
    {
        $partydata->orderBy('financial_year','desc')->orderBy('asd','desc');
    }

    $count = $partydata->count();
    $partydata = $partydata->offset($offset)->limit($limit)->get();
    
    $array['recordsTotal'] = $count;
    $array['recordsFiltered'] = $count;
    $array['data'] = $partydata;
    return json_encode($array);
}
public function internal_statusupdate(Request $request,$status,$id)
{
    $urlparams = $status=="close"?"open":'close';
    
    if(!empty($id))
    {
        $iodata = InternalOrder::select('id','status','io_number')->where("id",$id)->first();
        
        if(!empty($iodata->id))
        {
            $io_number = $iodata->io_number;
            $date = Carbon::now();
            $closed_by = Auth::id();
            $status = $status=="close"?"Closed":'Open';

            InternalOrder::where("id",$id)->update(
                ['status'=>$status,'closed_by'=>$closed_by,'closed_date'=>$date]
            );
            JobCard::where("io_id",$id)->update(
                ['status'=>$status,'closed_by'=>$closed_by,'closed_date'=>$date]
            );
            $status = strtolower($status);
            return redirect('/internal/list/open')->with('success','Internal Order ['.$io_number.'] has been '.$status.' successfully.');
        }else
        {
            return redirect('/internal/list/open')->with('error','Internal Order not available.');
        }
    }
    else
    {
        return redirect('/internal/list/open')->with('error','Status not updated.');
    }
    
}
public function internal_view($id)
{
    $internal=InternalOrder::where('internal_order.id',$id)
        ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
        ->leftJoin('master__marketing_person','master__marketing_person.id','=','job_details.marketing_user_id')
        ->leftJoin('advance_io','job_details.advance_io_id','=','advance_io.id')
        ->leftJoin('party','internal_order.party_id','=','party.id')
        ->leftJoin('countries','party.country_id','=','countries.id')
        ->leftJoin('states','party.state_id','=','states.id')
        ->leftJoin('cities','party.city_id','=','cities.id')
        ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
        ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
        ->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
        ->leftJoin('io_type','job_details.io_type_id','=','io_type.id')
        ->leftJoin('unit_of_measurement','job_details.unit','=','unit_of_measurement.id')
        ->leftJoin('hsn','hsn.id','=','job_details.hsn_code')
        ->get([
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
            'party_reference.referencename as reference_name',
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
            'hsn.gst_rate as gst',
            

        ])->first();
        $created=Users::where('id',$internal['created_by'])->get('name as created_name')->first();
        $closed_by= Users::where('id','=',$internal['closed_by'])->get('name as closed_by_name')->first();
        $data=array(
        'layout' => 'layouts.main',
        'internal'=>$internal,
        'created'=>$created,
        'closed_by'=>$closed_by
        );
    return view('sections.internal_order_view', $data);

    }

    public function internal_update($id)
{
    $flag=0;
    $usertype=Auth::user()->user_type;
    $io_dc=tax::where('tax.io_id',$id)->get('tax.io_id')->first();
    // $req=RequestPermission::where('data_id',$id)->where('data_for','internalorder')->where('requested_by',Auth::id())->where('status','allowed')->first();
    // print_r($req);die;
    if($io_dc && $usertype=="superadmin"){
        $flag=1;
        $msg="Tax Invoice is already Raised for this Internal Order.";
    }
    else if(!($io_dc) && $usertype=="superadmin"){
        $dc=Challan_per_io::where('io',$id)->get('challan_per_io.id')->first();
        $jc=JobCard::where('io_id',$id)->get('job_card.id')->first();
        if($dc){
            $flag=1;
            $msg="Delivery Challan Has Already Raised for this Internal Order";
        }
        else if($jc){
            $flag=0;
            $msg="Job Card Has Already Raised for this Internal Order";
        }
        else{
            $flag=0; 
            $msg="";
        }
    }
    else{
        $flag=0; 
        $msg="";
    }
    $item = ItemCategory::all();
    $hsn = Hsn::get(['hsn.gst_rate','hsn.hsn','hsn.id','hsn.item_id as name']);
    // $users=EmployeeProfile::where('department_id','=',10)->get();
    $users=MasterMarketingPerson::all();

    // $party=City::join('party', 'cities.id', '=', 'party.city_id')
    //             ->get(['party.id','party.partyname','party.city_id','cities.city']);
    $party = Reference::all();

    $io_type=IoType::all();
    $unitof = Unit_of_measurement::all();
    $po_num=PoNumber::all();
    $partydata = InternalOrder::leftJoin('job_details',function($join){
        $join->on('internal_order.job_details_id','=','job_details.id');
    })->leftJoin('item_category',function($join){
        $join->on('internal_order.item_category_id','=','item_category.id');
    })->leftJoin('advance_io',function($join){
        $join->on('job_details.advance_io_id','=','advance_io.id');
    })->where('internal_order.id','=',$id);

    //$internal_order_data = InternalOrder::where('id',1)->first();
    //$advance_io_id = JobDetailsView::where('id', $internal_order_data['party_id'])->value('advance_io_id');

    $partydata = $partydata->select(
        'internal_order.id',
        'internal_order.other_item_name',
        'internal_order.reference_name',

        'item_category.id as item_category_id',

        'job_details.io_type_id',
        'job_details.job_date',
        'job_details.hsn_code',
        'job_details.delivery_date',
        'job_details.qty',
        'job_details.left_qty',
        'job_details.unit',
        'job_details.job_size',
        'job_details.rate_per_qty',
        'job_details.details',
        'job_details.front_color',
        'job_details.back_color',
        'job_details.is_supplied_paper',
        'job_details.dimension',
        'job_details.is_supplied_plate',
        'job_details.transportation_charge',
        'job_details.other_charge',
        'job_details.remarks',
        'job_details.advanced_received',

            'advance_io.amount',
            'advance_io.mode_of_receive',
            'advance_io.date',
            'job_details.marketing_user_id'
        )->get();
        $data=array(
            'layout' => 'layouts.main',
            'item' => $item,
            'party' => $party,
            'po_num' => $po_num,
            'io_type' => $io_type,
            'flag' => $flag,
            'msg' => $msg,
            'hsn' => $hsn,
            'data'=>$partydata,
            'job_qty_unit'=>$unitof,
            'users'=>$users
        );
    return view('sections.internal_order_update', $data);

}
public function update_rate($id,$rate){
    $dc=Challan_per_io::where('challan_per_io.io',$id)
    ->leftJoin('delivery_challan','challan_per_io.delivery_challan_id','delivery_challan.id')
    ->leftJoin('challan_per_io as all_io','all_io.delivery_challan_id','delivery_challan.id')
    ->leftJoin('internal_order','internal_order.id','all_io.io')
    ->leftjoin('job_details','internal_order.job_details_id','=','job_details.id')
    ->leftJoin('hsn','hsn.id','=','job_details.hsn_code')
    ->select('all_io.io','delivery_challan.id as dc_id','all_io.id as challan_per_io_id','all_io.good_qty','all_io.amount','hsn.gst_rate as gst')->get();
    // print($dc);
    if(count($dc)!=0){
    $dc_id=$dc[0]['dc_id'];
    foreach($dc as $key){
        $total_amount[$key['dc_id']]=0;
    }
    foreach($dc as $key){
        if($key['io']==$id){
            if(!$key['gst'])
                $key['gst']=0;
            $total=0;
            $challan=$key['challan_per_io_id'];
            $amount=($rate * $key['good_qty']);
            $gst=($amount*$key['gst'])/100;
            $total=$amount+$gst;
            $total_amount[$key['dc_id']]=$total_amount[$key['dc_id']]+$total;
            Challan_per_io::where('id',$challan)->where('io',$id)->where('delivery_challan_id',$key['dc_id'])->update([
                    'rate'=>$rate,
                    'amount'=>$total
            ]);
        }
        else{
            $total_amount[$key['dc_id']]=$total_amount[$key['dc_id']]+$key['amount'];
        }
    }
    foreach($total_amount as $key=>$item){
        $value=Delivery_challan::where('id',$key)->update(['total_amount'=>$item]);
    }
    }
    
    $tax=Tax::where('tax.io_id',$id)
    ->leftJoin('tax_invoice','tax.tax_invoice_id','tax_invoice.id')
    ->leftJoin('tax as all_tax','all_tax.tax_invoice_id','tax_invoice.id')
    ->leftJoin('hsn','hsn.id','=','tax.hsn')
    ->select('all_tax.io_id as io','tax_invoice.id as tax_invoice_id','all_tax.id as tax_id','all_tax.qty','all_tax.amount','all_tax.discount','hsn.gst_rate as gst','tax_invoice.transportation_charge','tax_invoice.other_charge')->get();
    if(count($tax)!=0){
        // print($tax);
        $tax_id=$tax[0]['tax_invoice_id'];
        foreach($tax as $key){
            $tot_amnt[$key['tax_invoice_id']]=0;
        }
        foreach($tax as $key){
           
            if($key['io']==$id){
              
                if(!$key['gst'])
                    $key['gst']=0;
                $total=0;
                $challan=$key['tax_id'];
                $amount=($rate * $key['qty']);
                $gst=($amount*$key['gst'])/100;
                $total=$amount+$gst;
                $discount_amt=$total-(($total*$key['discount'])/100);
                $total_amount=$discount_amt;
                $tot_amnt[$key['tax_invoice_id']]= $tot_amnt[$key['tax_invoice_id']]+$total_amount;
                Tax::where('id',$challan)->where('io_id',$id)->where('tax_invoice_id',$key['tax_invoice_id'])->update([
                        'rate'=>$rate,
                        'amount'=>$total_amount
                ]);
                
            }
            else{
                $tot_amnt[$key['tax_invoice_id']]= $tot_amnt[$key['tax_invoice_id']]+$key['amount'];
            }
           
        }
     
        foreach($tot_amnt as $key=>$item){
            $value=Tax_Invoice::where('id',$key)->update(['total_amount'=>DB::raw('transportation_charge +'.'other_charge +'.$item)]);
        }
    }
   return 1;

 

}
public function internal_order_update_db(Request $request,$id)
{
    $usertype=Auth::user()->user_type;
    // print_r($request->input());die;
    if($request->input('flag')==0 && $usertype!="superadmin"){
        return redirect('/internalorder/update/'.$id)->with('error','You Have no rights to edit Internal Order IO Type')->withInput();
    }
    $usertype=Auth::user()->user_type;
    $io_dc=tax::where('tax.io_id',$id)->get('tax.io_id')->first();
    $req=IORequestEdit::where('io_id',$id)->where('status','pending')->first();
    if(($io_dc && $usertype=="superadmin")){
     
    }
    
    else if(!($io_dc) && $usertype=="superadmin"){

    }
    else{
        return redirect('/internalorder/update/'.$id)->with('error','You Have no rights to edit Internal Order IO Type')->withInput();
    }
    
    $validation_array = [
        // 'party_name' => 'required|integer|exists:party,id'  ,
        'reference_name' => 'required|string|exists:party,reference_name',
        'update_reason'=> 'required',
        'item' => 'required|integer',
        'other_item_name'=>'required_if:item,15',
        // 'io_type' => 'required|integer',
        'job_date' => 'required|date',
        'hsn' => 'required',
        'delivery_date' => 'required|date',
        'job_qty' => 'required|numeric',
        'job_qty_unit' => 'required|numeric',
        'job_size' => 'required',
        'job_rate' => 'required|numeric',
        'dimension' => 'required',
        'details' => 'required',
        'front_color' => 'required|numeric',
        'back_color' => 'required|numeric',
        'paper' => ['required', Rule::in(['Party', 'Press', 'NA'])],
        'plates' => ['required', Rule::in(['Party', 'Press', 'OldPlates', 'NA'])],
        'transportaion_charges' => 'required|numeric',
        'other_charges' => 'required|numeric',
        'remark' => 'required',
        'adv_received' => 'required|boolean',
    ];
   
    if($request->input('adv_received')){
        // $validation_array['amount'] = 'required|numeric';
        // $validation_array['amt_received_date'] = 'required|date';
        $validation_array['mode_received'] = ['required', Rule::in(['0', '1', '2'])];
    }

    $validation_message = $request->validate($validation_array);

        try {
            
            /*** USER LOG ***/
            $hsn = Hsn::where('id',$request->input('hsn'))->selectRaw("CONCAT(hsn.item_id,' - ',hsn.hsn,' - ',hsn.gst_rate) as name")->get()->first();
            $hsn = isset($hsn->name)?$hsn->name:'';
            $changes_array['hsn'] = $hsn;

            $reference=Reference::where('id',$request->input('reference_name'))->select('referencename')->get()->first();
            $reference_name = isset($reference->referencename)?$reference->referencename:'';
            $changes_array['reference_name'] = $reference_name;

            $item=ItemCategory::where('id',$request->input('item'))->select('name')->get()->first();
            $item = isset($item->name)?$item->name:'';
            $changes_array['item'] = $item;

            $job_qty_unit=Unit_of_measurement::where('id',$request->input('job_qty_unit'))->select('uom_name')->get()->first();
            $job_qty_unit = isset($job_qty_unit->uom_name)?$job_qty_unit->uom_name:'';
            $changes_array['job_qty_unit'] = $job_qty_unit;

          if($request->input('io_type')!=null){
            $io_type=IoType::where('id',$request->input('io_type'))->select('name')->get()->first();
            $io_type = isset($io_type->name)?$io_type->name:'';
            $changes_array['io_type'] = $io_type;
          }
          
          

           
            $qtys=0;
            $internal_order_data = InternalOrder::where('id',$id)->first();
            $advance_io_id = JobDetails::where('id', $internal_order_data['job_details_id'])->value('advance_io_id');
            $adv_received=$request->input('adv_received');
            $date1 = strtotime($request->input('po_date'));
            $newDate1 = date("Y-m-d", $date1);
            $date2 = strtotime($request->input('job_date'));
            $newDate2 = date("Y-m-d", $date2);
            $date3 = strtotime($request->input('amt_received_date'));
            $newDate3 = date("Y-m-d", $date3);
            $date4 = strtotime($request->input('delivery_date'));
            $newDate4 = date("Y-m-d", $date4);
            $timestamp = date('Y-m-d G:i:s');
            $mode="";
            $adv="No";
            if($advance_io_id==0){
                if ($adv_received==1){
                    $modes=$request->input('mode_of_receive');
                    if($modes==0){
                        $mode="Cash";
                    }
                    else if($modes==1){
                        $mode="Cheque";
                    }
                    else{
                        $mode="RTGS";
                    }
                    $adv="Yes";
                    $amount_id= advanceIO::where('id',$advance_io_id)->update(
                        [
                            'amount' => $request->input('amount'),
                            'mode_of_receive' =>$request->input('mode_received'),
                            'date' => $newDate3
                        ]
                        
                    );
                }
                else{
                    $amount_id=NULL;
                }
            }
            else{
                if ($adv_received==1){
                    $amount_id= advanceIO::insertGetId(
                        [
                            'amount' => $request->input('amount'),
                            'mode_of_receive' =>$request->input('mode_received'),
                            'date' => $newDate3
                        ]
                        
                    );
                }
                else{
                    $amount_id=NULL;
                }
            }
           
            
            $qty_new=$request->input('job_qty');
            $qty_old=$request->input('old_job_qty');
            $qty_left=$request->input('old_leftqty');
            if($qty_new>=($qty_old-$qty_left)){
                $qtys=$qty_new-($qty_old-$qty_left);
             
            }
            else if($qty_new<($qty_old-$qty_left) && $request->input('flag')==0){
                
            }
            else{
                return redirect('/internalorder/update/'.$id)->with('error','Qty cannot be less than original qty')->withInput();  
            }
            $jobdetail_id= jobDetails::where('id' , $internal_order_data['job_details_id'])->update(
                [
                    'io_type_id' => $request->input('flag') ==0 ? $request->input('io_type') : $request->input('io_type_old'),
                    'job_date' =>$newDate2,
                    'hsn_code' => $request->input('hsn'),
                    'delivery_date' => $newDate4,
                    'qty' =>$request->input('job_qty'),
                    'left_qty' =>$request->input('flag') ==1 ? DB::raw('left_qty + '.$qtys) : $request->input('job_qty'),
                    'unit' => $request->input('job_qty_unit'),
                    'job_size' => $request->input('job_size'),
                     'dimension' => $request->input('dimension'),
                    'rate_per_qty' =>$request->input('job_rate'),
                    'marketing_user_id' =>$request->input('market'),    
                    'details' => $request->input('details'),
                    'front_color' =>$request->input('front_color'),
                    'back_color' => $request->input('back_color'),
                    'is_supplied_paper' =>$request->input('paper'),
                    'is_supplied_plate' =>$request->input('plates'),
                    //'payment_due_term' => $request->input('payment_due'),
                    'remarks' => $request->input('remark'),
                    'transportation_charge' =>$request->input('transportaion_charges'),
                    'other_charge' => $request->input('other_charges'),
                    'advanced_received' => $request->input('adv_received'),
                    'advance_io_id' =>$amount_id,
                    'updated_at'=>$timestamp
                ]
            );
            $d=$this->update_rate($id,$request->input('job_rate'));
            if($d==0){
                return redirect('/internalorder/update/'.$id)->with('error','Some Error occurred.')->withInput();  
            }
            $party_id = 0;
            if($request->input('item')==15)
            {
                $other_name = $request->input('other_item_name');
                $other_name = $other_name!=""?$other_name:'';
            }
            else
                $other_name = "";

            $io_id= InternalOrder::where('id' , $id)->update(
                [
                    'reference_name' =>$request->input('reference_name'),
                    'party_id'=> $party_id,
                    //'po_id' => $request->input('po_num'),
                    //'received_po' => $request->input('po_received'),
                    'item_category_id' =>$request->input('item'),
                    'other_item_name' =>$other_name,
                    
                    //'created_by' => Auth::id(),
                    //'is_active' =>1,
                    'updated_at'=>$timestamp
                ]
            );
            if($req){
                $req=RequestPermission::where('data_id',$id)->where('data_for','internalorder')->where('status','pending')->update(['status'=>'expired']);
            }
            
            CustomHelpers::userLog($request->input()['update_reason'],$id,'Internal Order Update',
            $log_array=array(
                'HSN Code'=>$changes_array['hsn'],
                'Reference Name'=>$changes_array['reference_name'],
                'Item Category'=>$changes_array['item'],
                'IO Type'=>$changes_array['io_type'],
                'Job Date'=>$request->input('job_date'),

                'Delivery Date'=>$request->input('delivery_date'),
                'Job Qty'=>$request->input('job_qty'),
                'Job Qty Unit'=>$changes_array['job_qty_unit'],
                'Job Size'=>$request->input('job_size'),
                'Dimension'=>$request->input('dimension'),
                'Job Rate'=>$request->input('job_rate'),
                'Job Details'=>$request->input('details'),
                'Front Color'=>$request->input('front_color'),
                'Back Color'=>$request->input('back_color'),
                'Paper'=>$request->input('paper'),
                'Plates'=>$request->input('plates'),
                'Transportation Charges'=>$request->input('transportaion_charges'),
                'Other Charges'=>$request->input('other_charges'),
                'Remark'=>$request->input('remark'),
                'Advanced Received'=>$adv,
                'Mode Received'=>$mode,
                'Amount Received Date'=>$newDate3,
                'Amount'=>$request->input('amount')
                // ''=>'',
            
            ));
            /***  END USER LOG ***/
             
            return redirect('/internal/list/open')->with('success',"Internal Order updated successfully.");
        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/internalorder/update/'.$id)->with('error',$ex->getMessage())->withInput();
    }
   
}
    public function taxinvoiceupdate($id){
        $tax_date=Tax_Invoice::where('id','=',$id)->select(DB::raw('DATE_FORMAT(created_at,"%d-%m-%Y") as date'))->get()->first();
        $cur_date=date('d-m-Y');
        // return $cur_date;
        $x=Auth::user()->user_type;
        $data1=RequestPermission::where('data_for','taxinvoiceupdate')->where('data_id',$id)
                ->select(
                    'request_permission.id',
                    'request_permission.status'
                )->orderBy('id','DESC')
                ->first();
                if($data1 && $x!="superadmin"){
                    if($data1->status=="pending"){
                     return redirect('taxinvoice/list')->with('error','Request For Edit is still Pending');  
                    } 
                    else if($data1->status=="expired"){
                        return redirect('taxinvoice/list')->with('info','Request For Edit is Expired');  
                       }  
                }
                else if(!$data1 && $x!="superadmin" && ($tax_date['date']!=$cur_date)){
                    return redirect('taxinvoice/list')->with('error','Request For Edit is not Found');
                }
        $hsn = Hsn::get(['hsn.gst_rate','hsn.hsn','hsn.id','hsn.item_id as name']);
        $party=Party::all();
        $consignee = Consignee::all();
        $payment=Payment::all();
        $uom=Unit_of_measurement::all();
        $delivery=Delivery_challan::leftJoin('tax','delivery_challan.id','=','tax.delivery_challan_id')
                                    ->where('tax.id','=',NULL)->get('delivery_challan.id');
        
        $invoice = Tax_Invoice::where('id','=',$id)->get()->first();
        if(!$invoice)
            return redirect('/taxinvoice/list')->with('error',"Data not found.");

        $delivery_id=$this->party_delivery($invoice->party_id,$invoice->id,'update');
        // return $delivery_id;
       
        $tax = Tax::where('tax_invoice_id','=',$id)
        ->where('is_active','=',1)->get(['tax_invoice_id as id','delivery_challan_id as id1','io_id']);
        $arr=array();
        $arr1=array();
        $arr_dc=array();
        $arr_io=array();
        foreach ($tax as $item) {
           if($item['id1']!=''){
            $arr[]=$item['id1'];
           }
           if($item['io_id']!=''){
            $arr1[]=$item['io_id'];
           }
        }
        
        $unique_data = array_unique($arr);
        $unique_data1 = array_unique($arr1);
   
         foreach($unique_data as $val) {
              $arr_dc[]=$val;
         }
          foreach($unique_data1 as $val) {
               $arr_io[]=$val;
          }

        $data=array('layout'=>'layouts.main','hsn'=>$hsn,'delivery'=>$delivery,'party'=>$party,
        'uom'=>$uom,'payment'=>$payment,'tax'=>$tax ,"invoice"=>$invoice,'consignee'=>$consignee
        ,'delivery_id'=>$delivery_id,'dc'=>$arr_dc,'io'=>$arr_io);

        
        return view('sections.tax_invoice_update', $data);
    }
    function tax_invoice_update_db(Request $request,$id)
    {
        // print_r($request->input());die;
        if($request->input('party_id')){
            $party_data=$request->input('party_id');
        }
        else{
            $party_data=$request->input('party');
        }
        $party=$party_data;
        // print_r($party);die;
        try {
            $msg='';
            $valarr=[
                'update_reason'=>'required',
                'consignee'=>'required',
                'terms'=>'required',
                'transportation'=>'required|numeric|gte:0',
                'other'=>'required|numeric|gte:0',
                'gst'=>'required'
            ];
            $valmsg =[ 
                'update_reason.required'=>'Update Reason is required',
                'consignee.required'=>'Consignee List is required',
                'delivery.*.required'=>'Delivery Challan is Required',
                'terms.required'=>' Terms Of Delivery is required',
                'transportation.required'=>'Transportation Charges is required',
                'transportation.numeric'=>'Transportation Charges must be numeric',
                'transportation.gte'=>'Transportation Charges must be greater than or equal to :get',
                'other.required'=>'Other Charges is required',
                'other.numeric'=>'other Charges must be numeric',
                'other.gte'=>'other Charges must be greater than or equal to :get',
                'gst'=>'gst type is required',
            ];
            if(!$request->input('delivery')){
                $arr_io=['io'=>'required'];
                $arr_io_msg =[ 'io.required'=>'Internal Order or Delivery Challan is required'];
                $valarr = array_merge($valarr,$arr_io);
                $valmsg = array_merge($valmsg,$arr_io_msg);
            }
            if(!$request->input('io')){
                $arr_io=['delivery'=>'required'];
                $arr_io_msg =[ 'delivery.required'=>'Delivery Challan or Internal Order is required'];
                $valarr = array_merge($valarr,$arr_io);
                $valmsg = array_merge($valmsg,$arr_io_msg);
            }
            $delivery=$request->input('delivery');
            if($request->input('delivery')){
                for($i=0;$i<count($request->input('delivery'));$i++)
                { 
                        $data=NULL;
                        $ios=$request->input('internal_'.$delivery[$i]);
                         DB::enableQueryLog();
                        for($j=0;$j<count($ios);$j++){
                            $io_number=InternalOrder::where('internal_order.id',$ios[$j])
                            ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
                            ->get(['io_number','io_type_id'])->first();
                            // print_r($io_number);die;
                            $data_cpo=Client_po::where('io',$ios[$j])->select('is_po_provided')->get()->first();
                            if($io_number['io_type_id']==9){
                                $msg='';
                            }
                            else{
                                if($data_cpo && $data_cpo['is_po_provided']==1){
                                    $data = Client_po::where('io',$ios[$j])->leftJoin('client_po_party','client_po_party.client_po_id','client_po.id')->where('party_name',$party)
                                    ->get(['po_number','client_po_party.party_name','client_po.id','client_po.is_po_provided']);
                                    if(!isset($data) || count($data->toArray())<=0)
                                    {
                                        $msg = $msg." ".$io_number['io_number']." does not have Client Purchase Order.<br>";
                                    }
                                    else{
                                        $msg='';
                                    }
                                }
                                else if(($data_cpo && $data_cpo['is_po_provided']==0)){
                                    $msg='';
                                }
                                else if(!$data_cpo){
                                    if($io_number['io_type_id']==9){
                                        $msg='';
                                    }
                                    else{
                                        $msg = $msg." ".$io_number['io_number']." does not have Client Purchase Order.<br>"; 
                                    }
                                }
                                else{
                                    $msg='';
                                }
                            }
                        }
                    $valarr1 = [ 
                        'internal_'.$delivery[$i].'.*'=>'required',
                        'goods_'.$delivery[$i].'.*'=>'required',
                        'qty_'.$delivery[$i].'.*'=>'required|numeric|gt:0',
                        'rate_'.$delivery[$i].'.*'=>'required|numeric',
                        'per_'.$delivery[$i].'.*'=>'required',
                        'discount_'.$delivery[$i].'.*'=>'required|numeric',
                        'hsn_'.$delivery[$i].'.*'=>'required',
                        'transportation_'.$delivery[$i].'*'=>'required|numeric|gte:0',
                        'other_'.$delivery[$i].'.*'=>'required|numeric|gte:0',
                        'payment_'.$delivery[$i].'.*'=>'required|present'
                    ];
                    $valmsg1=[
                        'internal_'.$delivery[$i].'.*.required'=>'internal order is required',
                        'goods_'.$delivery[$i].'.*.required'=>'Description of Goods is required',
                        'qty_'.$delivery[$i].'.*.required'=>'Quantity is required|',
                        'qty_'.$delivery[$i].'.*.numeric'=>'Quantity must be numeric',
                        'qty_'.$delivery[$i].'.*.gte'=>'Quantity must be greater than or equal to :gte',
                        'rate_'.$delivery[$i].'.*.required'=>'Rate is required',
                        'rate_'.$delivery[$i].'.*.numeric'=>'Rate must be numeric',
                        'rate_'.$delivery[$i].'.*.gte'=>'Rate must be greater than or equal to :gte',
                        'per_'.$delivery[$i].'.*.required'=>'Per is required',
                        'discount_'.$delivery[$i].'.*.required'=>'Discount is required',
                        'discount_'.$delivery[$i].'.*.numeric'=>'Discount must be numeric',
                        'discount_'.$delivery[$i].'.*.gte'=>'Discount must be greater than or equal to :gte',
                        'hsn_'.$delivery[$i].'.*.required'=>'HSN/SAC is required',
                        'transportation_'.$delivery[$i].'*.required'=>'Transportation Charges is required',
                        'transportation_'.$delivery[$i].'.*.numeric'=>'Transportation Charges must be numeric',
                        'transportation_'.$delivery[$i].'.*.gte'=>'Transportation Charges must be greater than or equal to :gte',
                        'other_'.$delivery[$i].'.*.required'=>'Other Charges is required',
                        'other_'.$delivery[$i].'.*.numeric'=>'Other Charges must be numeric',
                        'other_'.$delivery[$i].'.*.gte'=>'Other Charges must be greater than or equal to :gte',
                        'payment_'.$delivery[$i].'.*.present'=>'oresent',
                        'payment_'.$delivery[$i].'.*.required'=>'required'
                    ];
                    $valarr = array_merge($valarr,$valarr1);
                    $valmsg = array_merge($valmsg,$valmsg1);
                }
            }
            if($request->input('io')){
                $msg='';
                $ioss=$request->input('io');
                for($i=0;$i<count($request->input('io'));$i++)
                { 
                        $data=NULL;
                        $io=$request->input('internal_io_'.$ioss[$i]);
                        // print_r($io);die;
                         DB::enableQueryLog();
                            $io_number=InternalOrder::where('internal_order.id',$io) 
                            ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
                            ->get(['io_number','io_type_id'])->first();
                            $data_cpo=Client_po::where('io',$io)->select('is_po_provided')->get()->first();
                            if($io_number['io_type_id']==9){
                                $msg='';
                            }
                            else{
                                if($data_cpo && $data_cpo['is_po_provided']==1){
                                    $data = Client_po::where('io',$io)->leftJoin('client_po_party','client_po_party.client_po_id','client_po.id')
                                    ->where('party_name',$party)
                                    // ->orwhere('client_po.is_po_provided',0)
                                    ->get(['po_number','client_po_party.party_name','client_po.id','client_po.is_po_provided']);
                                    if(!isset($data) || count($data->toArray())<=0)
                                    {
                                        $msg = $msg." ".$io_number['io_number']." does not have Client Purchase Order.<br>";
                                    }
                                    else{
                                        $msg='';
                                    }
                                }
                                else if($data_cpo && $data_cpo['is_po_provided']==0){
                                    $msg='';
                                }
                                else if(!$data_cpo){
                                    $msg = $msg." ".$io_number['io_number']." does not have Client Purchase Order.<br>";
                                }
                                else{
                                    $msg='';
                                }
                            }
                            
                        
                    $valarr1 = [ 
                        'internal_io_'.$ioss[$i]=>'required',
                        'goods_io_'.$ioss[$i]=>'required',
                        'qty_io_'.$ioss[$i]=>'required|numeric|gt:0',
                        'rate_io_'.$ioss[$i]=>'required|numeric',
                        'per_io_'.$ioss[$i]=>'required',
                        'discount_io_'.$ioss[$i]=>'required|numeric',
                        'hsn_io_'.$ioss[$i]=>'required',
                        'transportation_io_'.$ioss[$i]=>'required|numeric|gte:0',
                        'other_io_'.$ioss[$i]=>'required|numeric|gte:0',
                        'payment_io_'.$ioss[$i]=>'required|present'
                    ];
                    $valmsg1=[
                        'internal_io_'.$ioss[$i].'.required'=>'internal order is required',
                        'goods_io_'.$ioss[$i].'.required'=>'Description of Goods is required',
                        'qty_io_'.$ioss[$i].'.required'=>'Quantity is required|',
                        'qty_io_'.$ioss[$i].'.numeric'=>'Quantity must be numeric',
                        'qty_io_'.$ioss[$i].'.gte'=>'Quantity must be greater than or equal to :gte',
                        'rate_io_'.$ioss[$i].'.required'=>'Rate is required',
                        'rate_io_'.$ioss[$i].'.numeric'=>'Rate must be numeric',
                        'rate_io_'.$ioss[$i].'.gte'=>'Rate must be greater than or equal to :gte',
                        'per_io_'.$ioss[$i].'.required'=>'Per is required',
                        'discount_io_'.$ioss[$i].'.required'=>'Discount is required',
                        'discount_io_'.$ioss[$i].'.numeric'=>'Discount must be numeric',
                        'discount_io_'.$ioss[$i].'.gte'=>'Discount must be greater than or equal to :gte',
                        'hsn_io_'.$ioss[$i].'.required'=>'HSN/SAC is required',
                        'transportation_io_'.$ioss[$i].'.required'=>'Transportation Charges is requiredda',
                        'transportation_io_'.$ioss[$i].'.numeric'=>'Transportation Charges must be numeric',
                        'transportation_io_'.$ioss[$i].'.gte'=>'Transportation Charges must be greater than or equal to :gte',
                        'other_io_'.$ioss[$i].'.required'=>'Other Charges is required',
                        'other_io_'.$ioss[$i].'.numeric'=>'Other Charges must be numeric',
                        'other_io_'.$ioss[$i].'.gte'=>'Other Charges must be greater than or equal to :gte',
                        'payment_io_'.$ioss[$i].'.present'=>'oresent',
                        'payment_io_'.$ioss[$i].'.required'=>'required'
                    ];
                    $valarr = array_merge($valarr,$valarr1);
                    $valmsg = array_merge($valmsg,$valmsg1);
                }
            }
            $validator = Validator::make($request->all(),$valarr,$valmsg);
            if(strlen($msg)>0)
            {
                $validator->getMessageBag()->add('delivery', $msg);
                $errors = $validator->errors();
                return redirect('/taxinvoice/update/'.$id)->withErrors($errors)->withInput();
            }
            if ($validator->fails())
            {
                $errors = $validator->errors();
                return redirect('/taxinvoice/update/'.$id)->withErrors($errors)->withInput();
            }
            $old_delivery_ids = Tax::where('tax_invoice_id',$id)->get('delivery_challan_id')->toArray();
            $val = '';
            for($i=0;$i<count($old_delivery_ids);$i++)
            {
                $val = $val.$old_delivery_ids[$i]['delivery_challan_id'].',';
            }
            $val = explode(',',$val);
            $error=0;
            if($request->input('delivery')){
                $counts=count($request->input('delivery'));
            }
            else{
                $counts=0; 
            }
            for($i=0;$i<$counts;$i++)
            {
                if(array_search($request->input('delivery')[$i],$val))
                {   
                    $taxx = Tax::where('delivery_challan_id',$request->input('delivery')[$i])
                    ->first('id')['id'];
                    // if(isset($taxx) && $id != $taxx)
                    //     $error = 1;  
                }
            }
            if($error==1)
            {
                $validator->getMessageBag()->add('delivery.0','Delivery Challan is Already taken');
                $errors = $validator->errors();
                return redirect('/taxinvoice/update/'.$id)->withErrors($errors)->withInput();
            }
            if ($validator->fails())
            {
                $errors = $validator->errors();
                return redirect('/taxinvoice/update/'.$id)->withErrors($errors)->withInput();
            }
            // DB::beginTransaction();
            $settings = Settings::where('name','Tax_Invoice_Prefix')->first();

            $tax_number = $settings->value;
            $gst_rate=$request->input('gst');
            
            $taxss=Tax_Invoice::where('id','=',$id)->update([
                'party_id' =>$party_data,
                'consignee_id' =>$request->input('consignee'),
                'terms_of_delivery' =>$request->input('terms'),
                'gst_type' =>$request->input('gst'),
                'transportation_charge' =>$request->input('transportation'),
                'other_charge' =>$request->input('other'),
                'total_amount' =>0,
                'is_active'=>1,
                'date' =>date('Y-m-d',strtotime($request->input('created_at')))
            ]
            );
            // print_r($taxss);die;
           $po=date('Y-m-d',strtotime($request->input('created_at')));
            $tax=$id;
            DB::enableQueryLog();
            if($request->input('delivery')){
                Tax::whereNotIn('delivery_challan_id',$request->input('delivery'))
                ->where('tax_invoice_id','=',$id)->update(
                    [
                        'is_active'=>'0'
                    ]
                );
            }
           
            $queries = DB::getQueryLog();
           
            if($tax){
                
                $new_dc=$request->input('delivery');
                $old_dcc=$request->input('old_dc');
               
                if(($old_dcc) && !($new_dc))
                {
                    foreach($old_dcc as $key){
                        $check = Tax::where('tax_invoice_id','=',$id)->where('delivery_challan_id','=',$key)->select('id','qty','io_id')->get(); 
                        foreach($check as $it){
                            $io_no=InternalOrder::where('id','=',$it['io_id'])->select('job_details_id')->get()->first();
                            DB::enableQueryLog();
                            $internal_order= JobDetails::where('id',$io_no['job_details_id'])->update(['left_qty'=>DB::raw('left_qty + '.$it['qty'])]);
                            if($internal_order){
                                Tax::where('id',$it['id'])->delete();

                            }
                        }
                    }
                }
                if(($old_dcc) && ($new_dc)){
                    $result=array_diff($old_dcc,$new_dc);
                    foreach($result as $key){
                        $check = Tax::where('tax_invoice_id','=',$id)->where('delivery_challan_id','=',$key)->select('id','qty','io_id')->get(); 
                        foreach($check as $it){
                            $io_no=InternalOrder::where('id','=',$it['io_id'])->select('job_details_id')->get()->first();
                            $internal_order= JobDetails::where('id','=',$io_no['job_details_id'])->update(['left_qty'=>DB::raw('left_qty + '.$it['qty'])]);
                            if($internal_order){
                                Tax::where('id',$it['id'])->delete();

                            }
                        }
                    }
                }


                if($request->input('delivery')){
                    $count=count($request->input('delivery'));
                }
                else{
                    $count=0; 
                }

                $delivery=$request->input('delivery');
                $trans=$request->input('transportation');
                $other=$request->input('other');
                $tot_amnt=0;
                for($i=0;$i<$count;$i++)
                {
                    $internal_delivery_id = $request->input('internal_'.$delivery[$i]);
                    $count1=count( $internal_delivery_id);
                    for($j=0;$j<$count1;$j++)
                    {
                        $total_amount=0;
                        $io_id=$request->input('internal_'.$delivery[$i]);
                        $goods=$request->input('goods_'.$delivery[$i]);
                        $qty=$request->input('qty_'.$delivery[$i]);
                        $rate=$request->input('rate_'.$delivery[$i]);
                        $per=$request->input('per_'.$delivery[$i]);
                        $discount=$request->input('discount_'.$delivery[$i]);
                        $hsn=$request->input('hsn_'.$delivery[$i]);
                        $transportation_charge=$request->input('transportation_'.$delivery[$i]);
                        $other_charge=$request->input('other_'.$delivery[$i]);
                        $payment=$request->input('payment_'.$delivery[$i]);
                        $hsn_gst=Hsn::where('id',$hsn[$j])->get('gst_rate')->first();
                        $amount=($rate[$j] * $qty[$j]);
                        $gst=($amount*$hsn_gst['gst_rate'])/100;
                        $total=$amount+$gst;
                        $discount_amt=$total-(($total*$discount[$j])/100);
                        $total_amount=$discount_amt;
                        $tot_amnt=$tot_amnt+$total_amount;
                        $check = Tax::where('delivery_challan_id','=',$delivery[$i])
                        ->where('io_id','=',$io_id[$j])
                        ->get('id')->first();
                        // print_r($io_id[$j]);die;
                        if($check)
                        {
                            $old_qty = Tax::where('id','=',$check->id)
                            ->get('qty')->first()->qty;
                            $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                            ->where('internal_order.id','=',$io_id[$j])->get(['job_details.id','job_details.left_qty'])->first();
                            $lqty = $jd_id->left_qty-$qty[$j]+$old_qty;
                            
                            if( $qty[$j] >($jd_id['left_qty']+$qty[$j]))
                            {
                                DB::rollback();
                                return redirect('/taxinvoice/update/'.$id)->with('error','Internal Order Exhausted. dc');
                            }
                            Tax::where('id','=',$check->id)->update([
                                'goods' =>$goods[$j],
                                'qty' =>$qty[$j],
                                'rate' =>$rate[$j],
                                'per' =>$per[$j],
                                'discount' =>$discount[$j],
                                'hsn' =>$hsn[$j],
                                'transport_charges' =>$transportation_charge[$j],
                                'other_charges' =>$other_charge[$j],
                                'payment' =>$payment[$j],
                                'amount'=>$total_amount,
                                'is_active'=>'1'
                            ]);
                        }
                        else
                        {
                            $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                            ->where('internal_order.id','=',$io_id[$j])->select('job_details.id','job_details.left_qty','qty')->get()->first();
                            $lqty = $jd_id['left_qty']-$qty[$j]+$jd_id['qty'];
                           
                            if(($qty[$j])>($jd_id['left_qty']+$qty[$j]))
                            {
                                
                                DB::rollback();
                                return redirect('/taxinvoice/update/'.$id)->with('error','Internal Order Exhausted.');
                            }
                            Tax::insert(
                            [
                                'id' => NULL,
                                'tax_invoice_id' =>$tax,
                                'delivery_challan_id'=>$delivery[$i],
                                'io_id' =>$io_id[$j],
                                'goods' =>$goods[$j],
                                'qty' =>$qty[$j],
                                'rate' =>$rate[$j],
                                'per' =>$per[$j],
                                'discount' =>$discount[$j],
                                'hsn' =>$hsn[$j],
                                'transport_charges' =>$transportation_charge[$j],
                                'other_charges' =>$other_charge[$j],
                                'payment' =>$payment[$j],
                                'amount'=>$total_amount
                            ]              
                            );
                        }
                       
                        JobDetails::where('id','=',$jd_id['id'])->update(['left_qty'=>$lqty]);
                    }
                    
                    $tot=$tot_amnt+$trans+$other;
                    $totalamt=$tot;
                    // print_r($tax);die;
                    $tax_prefix= Tax_Invoice::where('id','=',$tax)->update([
                        
                        'total_amount'=>$totalamt
                    ]);
                        
                }
                
                $new_io=$request->input('io');
                $old_io=$request->input('old_io');
             
                if(($old_io[0]) && !($new_io))
                {
                    // print_r($new_io);die;
                    foreach($old_io as $key){
                        // print_r($key);die;
                        DB::enableQueryLog();
                        $check = Tax::where('io_id','=',$key)->where('tax_invoice_id','=',$id)->select('qty','id')->get()->first(); 
                        $io_no=InternalOrder::where('id','=',$key)->select('job_details_id')->get()->first();
                        $internal_order= JobDetails::where('id','=',$io_no['job_details_id'])->update(['left_qty'=>DB::raw('left_qty + '.$check['qty'])]);
                        if($internal_order){
                            Tax::where('id',$check['id'])->delete();

                        }
                    }
                }
                if(($old_io[0]) && ($new_io)){
                    $result=array_diff($old_io,$new_io);
                   
                    foreach($result as $key){
                        $check = Tax::where('tax_invoice_id','=',$id)->where('io_id','=',$key)->select('id','qty')->get()->first(); 
                        $io_no=InternalOrder::where('id','=',$key)->select('job_details_id')->get()->first();
                        $internal_order= JobDetails::where('id','=',$io_no['job_details_id'])->update(['left_qty'=>DB::raw('left_qty + '.$check['qty'])]);
                        if($internal_order){
                            Tax::where('id',$check['id'])->delete();

                        }
                    }
                }

                if($request->input('io')){
                    $io=$request->input('io');
                    $count2=count($io);
                }
                else{
                    $count2=0; 
                }
               
                for($i=0;$i<$count2;$i++){
                
                    $total_amount=0;
                    $io_id=$request->input('internal_io_'.$io[$i]);
                    $goods=$request->input('goods_io_'.$io[$i]);
                    $qty=$request->input('qty_io_'.$io[$i]);
                    $ori_leftqty=$request->input('orig_qty_left_'.$io[$i]);
                    $good_qty_old=$request->input('old_good_qty_'.$io[$i]);
                    $rate=$request->input('rate_io_'.$io[$i]);
                    $per=$request->input('per_io_'.$io[$i]);
                    $discount=$request->input('discount_io_'.$io[$i]);
                    $hsn=$request->input('hsn_io_'.$io[$i]);
                    $transportation_charge=$request->input('transportation_io_'.$io[$i]);
                    $other_charge=$request->input('other_io_'.$io[$i]);
                    $payment=$request->input('payment_io_'.$io[$i]);

                    $hsn_gst=Hsn::where('id',$hsn)->get('gst_rate')->first();
                    $amount=($rate * $qty);
                    $gst=($amount*$hsn_gst['gst_rate'])/100;
                    $total=$amount+$gst;
                    $discount_amt=$total-(($total*$discount)/100);
                    $total_amount=$discount_amt;
                    $tot_amnt=$tot_amnt+$total_amount;

                    $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                    ->where('internal_order.id','=',$io[$i])->get(['job_details.id','job_details.left_qty'])->first();
                    // print_r($jd_id);die;
                    $check = Tax::where('tax_invoice_id','=',$id)
                    ->where('io_id','=',$io[$i])
                    ->get('id')->first();
                    if($check){
                        Tax::where('id',$check->id)->update([
                            'goods' =>$goods,
                            'qty' =>$qty,
                            'rate' =>$rate,
                            'per' =>$per,
                            'discount' =>$discount,
                            'hsn' =>$hsn,
                            'transport_charges' =>$transportation_charge,
                            'other_charges' =>$other_charge,
                            'payment' =>$payment,
                            'amount'=>$total_amount
                        ]);
                            if($good_qty_old > $qty)
                            { 
                                $diff_qty=$good_qty_old - $qty;
                                JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>DB::raw('left_qty + '.$diff_qty)]);
                            }
                            else {
                                $diff_qty= $qty - $good_qty_old;
                                JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>DB::raw('left_qty - '.$diff_qty)]);
                            }
                        
                    }
                    else{
                        
                        Tax::insert([
                                'id' => NULL,
                                'tax_invoice_id' =>$tax,
                                'io_id' =>$io_id,
                                'goods' =>$goods,
                                'qty' =>$qty,
                                'rate' =>$rate,
                                'per' =>$per,
                                'discount' =>$discount,
                                'hsn' =>$hsn,
                                'transport_charges' =>$transportation_charge,
                                'other_charges' =>$other_charge,
                                'payment' =>$payment,
                                'amount'=>$total_amount
                            ]);
                            JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>DB::raw('left_qty - '.$qty)]);
                    }
            }
               
                $tot=$tot_amnt+$trans+$other;
                $totalamt=$tot;
                
                $tax_prefix= Tax_Invoice::where('id','=',$id)->update([
                    'total_amount'=>$totalamt
                ]); 
                // print_r($tax_prefix);die;

            }
            else{
                return redirect('/taxinvoice/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
            }
            $data=RequestPermission::where('data_for','taxinvoicedelete')->where('data_id',$id)
                ->select(
                    'request_permission.id',
                    'request_permission.status'
                )->orderBy('id','DESC')
                ->first();
                if($data){
                    if($data->status=="allowed"){
                        RequestPermission::where('data_for','taxinvoicedelete')->where('data_id',$id)->update(['status'=>'expired']);
                    }
                }

            $data1=RequestPermission::where('data_for','taxinvoiceupdate')->where('data_id',$id)
                ->select(
                    'request_permission.id',
                    'request_permission.status'
                )->orderBy('id','DESC')
                ->first();
                if($data1){
                    if($data1->status=="allowed"){
                        RequestPermission::where('data_for','taxinvoiceupdate')->where('data_id',$id)->update(['status'=>'expired']);
                        
                    }
                    Tax_Invoice::where('id',$id)->update(['is_update'=>'expired']);
                }
             /*** USER LOG ***/
             $party=Party::where('id',$request->input('party'))->select('partyname')->get()->first();
             $party = isset($party->partyname)?$party->partyname:'';
             $changes_array['party'] = $party;
 
             $consignee_name=Consignee::where('id',$request->input('consignee'))->select('consignee_name')->get()->first();
              $consignee_name = isset($consignee_name->consignee_name)?$consignee_name->consignee_name:'';
              $changes_array['consignee'] = $consignee_name;
 
              if($request->input('io')){
                 $io_id = InternalOrder::whereIN('id',$request->input('io'))->select(DB::raw('group_concat(io_number) as io_number'))->get()->first();
                 $io_id = isset($io_id->io_number)?$io_id->io_number:'';
                 $changes_array['io'] = $io_id;
              }
 
             if($request->input('delivery')){
                $delivery = Delivery_challan::whereIN('id',$request->input('delivery'))->select(DB::raw('group_concat(challan_number) as challan_number'))->get()->first();
                $delivery = isset($delivery->challan_number)?$delivery->challan_number:'';
                $changes_array['delivery'] = $delivery;
             }
 
              $log_array=array(
                 
                 'Party'=>$party,
                 'Consignee Name'=>$consignee_name,
                 'Delivery Challan'=>$delivery,
                 'Internal Order'=>$io_id,
                 'Terms Of Delivery'=>$request->input('terms'),
                 'GST Type'=>$request->input('gst'),
                 'Transportation Charges'=>$request->input('transportation'),
                 'Other Charges'=>$request->input('other'),
                 'Tax Date'=>$request->input('created_at')
          
              );
 
              CustomHelpers::userLog('Tax Invoice Updated',$id,'Tax Invoice Updated',
              $log_array);
            $partyid=$request->input('party');
            $party=Party::where('id',$partyid)->get('party.*')->first();
            $getAmount=Tax_Invoice::whereDate('tax_invoice.date', $po)
            ->leftJoin('tax','tax.tax_invoice_id','=','tax_invoice.id')
            ->leftJoin('waybill','waybill.challan_id','=','tax.delivery_challan_id')
            ->leftJoin('party','tax_invoice.party_id','=','party.id')
            ->where('party.gst',$party['gst'])
            ->where('tax_invoice.waybill_status','!=','2')
            ->get([
                'tax_invoice.id',
                'tax_invoice.invoice_number',
                'tax_invoice.total_amount',
                'tax_invoice.waybill_status',
                'waybill.challan_id as waybill_challan',
                'tax.delivery_challan_id',
                'tax.io_id',
                'party.gst',
                'party.id as party'
    
            ])->toArray();
           
            $counter=1; 
            $total_ByDate=0;
            $del_id=NULL;
            $Todatdate=date('Y-m-d'); 
            $delivery_id=[];
            $umsetflag=0;
            if(count($getAmount)>0){
                if($getAmount[0]['waybill_challan']!==NULL)
                {
                        $umsetflag=1;
                        $total_ByDate=0;
                   
                }
                else
                {
                    // $total_ByDate=$getAmount[0]['total_amount'] ;
                    $delivery_id[0]=$getAmount[0]['id'];  
                }
               
                if(count($getAmount)>1){
                    for($j=1;$j<count($getAmount);$j++){
                        if($getAmount[$j]['waybill_challan']!==NULL)
                            unset($getAmount[$j]);    
                        }
                        foreach($getAmount as $key){
                            $total_ByDate=$total_ByDate+$key['total_amount']; 
                            $delivery_id[$j]=$key['id']; 
                        }
                    $del_id=implode(':',$delivery_id); 
                   
                }
                if($umsetflag==1)
                    unset($getAmount[0]);
                
                    $waybill = Settings::where('name','delivery_amount')->first();
                    $waybill_value = $waybill->value;
                    if($total_ByDate>=$waybill_value){
                        foreach($getAmount as $key){
                            $way_id=$key['id'];
                            Tax_Invoice::where('id',$way_id)->update([
                                    'waybill_status'=> 1,
                                ]);
                        }
                    $amntDate=$total_ByDate;
                    $mesg="Today's Total Amount of Tax Invoice has been exceeded the limit ".$waybill_value. ".Create WayBill..";
                    }
                    else{
                        $amntDate=$total_ByDate;
                        $mesg=NULL;
                        $del_id=NULL;

                    }
                
                
            }
            else{
                $amntDate=NULL;
                $mesg=NULL;
                $del_id=NULL;
            }
                $prefix=Tax_Invoice::where('id','=',$id)->select('invoice_number')->get()->first();
                // DB::commit();
                // print_r($amntDate);die;
                return redirect('/taxinvoice/update/'.$id)->with(['message'=>'tax','tax'=>$tax,'prefix'=>$prefix['invoice_number'],'amntDate'=>$amntDate,'delivery_id'=>$del_id,'mesg'=>$mesg,'gst'=>$party['gst'],'date'=>$Todatdate,'refer'=>$party['reference_name'],'pointer'=>$party['gst_pointer']]);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/taxinvoice/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
      
    }
    public function taxinvoice(){
        $hsn = Hsn::get(['hsn.gst_rate','hsn.hsn','hsn.id','hsn.item_id as name']);
        $party=Party::all();
        $payment=Payment::all();
        $uom=Unit_of_measurement::all();
        $delivery=Delivery_challan::leftJoin('tax','delivery_challan.id','=','tax.delivery_challan_id')
                                    ->where('tax.id','=',NULL)->get('delivery_challan.id');
        $data=array('layout'=>'layouts.main','hsn'=>$hsn,'delivery'=>$delivery,'party'=>$party,'uom'=>$uom,'payment'=>$payment);
        //return $delivery;
        return view('sections.tax_invoice', $data);
    }
    public function tax_invoice_cancel($id)
    {
        $check_valid = RequestPermission::where('request_for','/cancel/taxinvoice/'.$id)
            ->where('status','allowed')
            ->get('id');
        if(!$check_valid)
        {
            return redirect('taxinvoice/list')->with('error','Permission not granted for Cancellation.');
        }
        else
        {
            $tax_invoice_details = Tax_invoice::where('id',$id)->select(
                'id','invoice_number'
            )->first();
            $data = array('layout'=>'layouts.main','tax_invoice_details'=>$tax_invoice_details,'id'=>$id);
            return view('sections.tax_invoice_cancel',$data);
        }
    }
    public function tax_invoice_cancel_db($id,Request $request)
    {
        $check_valid = RequestPermission::where('request_for','/cancel/taxinvoice/'.$id)
            ->where('status','allowed')
            ->get('id')->first();
        if(!$check_valid)
        {
            return redirect('taxinvoice/list')->with('error','Permission not granted for Cancellation.');
        }
        else
        {
            try
            {
                DB::beginTransaction();
                $tax_invoice_details = Tax_invoice::where('id',$id)->update([
                    'status'=>'Cancelled',
                    'cancellation_reason'=>$request->input('reason'),
                    'cancellation_advised_by'=>$request->input('cancellation')
                ]);
                RequestPermission::where('id',$check_valid->id)->update(['status'=>'cancelled']);
                DB::commit();
                return redirect('/taxinvoice/list')->with('success','Tax Invoice Cancelled');
            }
            catch(Exception $ex)
            {
                DB::rollback();
                return redirect('/taxinvoice/list')->with('error','Some Error Occured');
            }
        }
    }
    public function taxInsert(Request $request){
        try {
            ini_set('max_execution_time', 600);
            $msg='';
            $valarr=[
                'party'=>'required',
                'consignee'=>'required',
                // 'delivery.*'=>'unique:tax,delivery_challan_id,io_id',
                'terms'=>'required',
                'transportation'=>'required|numeric|gte:0',
                'other'=>'required|numeric|gte:0',
                'gst'=>'required'
            ];
            $valmsg =[
                'party.required'=>'Client is required',
                'consignee.required'=>'Consignee List is required',
                'terms.required'=>' Terms Of Delivery is required',
                'transportation.required'=>'Transportation Charges is required2',
                'transportation.numeric'=>'Transportation Charges must be numeric',
                'transportation.gte'=>'Transportation Charges must be greater than or equal to :get',
                'other.required'=>'Other Charges is required',
                'other.numeric'=>'other Charges must be numeric',
                'other.gte'=>'other Charges must be greater than or equal to :get',
                'gst'=>'gst type is required',
            ];
            $delivery=$request->input('delivery');
            
            if($request->input('delivery')){
                for($i=0;$i<count($request->input('delivery'));$i++)
                { 
                        $data=NULL;
                        $ios=$request->input('internal_'.$delivery[$i]);
                         //DB::enableQueryLog();
                        for($j=0;$j<count($ios);$j++){
                            $io_number=InternalOrder::where('internal_order.id',$ios[$j])
                            ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
                            ->get(['io_number','io_type_id'])->first();
                            // print_r($io_number);die;
                            $data_cpo=Client_po::where('io',$ios[$j])->select('is_po_provided')->get()->first();
                            if($io_number['io_type_id']==9){
                                $msg='';
                            }
                            else{
                                if($data_cpo && $data_cpo['is_po_provided']==1){
                                    $data = Client_po::where('io',$ios[$j])->leftJoin('client_po_party','client_po_party.client_po_id','client_po.id')->where('party_name',$request->input('party'))
                                    ->get(['po_number','client_po_party.party_name','client_po.id','client_po.is_po_provided']);
                                    if(!isset($data) || count($data->toArray())<=0)
                                    {
                                        $msg = $msg." ".$io_number['io_number']." does not have Client Purchase Order.<br>";
                                    }
                                    else{
                                        $msg='';
                                    }
                                }
                                else if(($data_cpo && $data_cpo['is_po_provided']==0)){
                                    $msg='';
                                }
                                else if(!$data_cpo){
                                    if($io_number['io_type_id']==9){
                                        $msg='';
                                    }
                                    else{
                                        $msg = $msg." ".$io_number['io_number']." does not have Client Purchase Order.<br>"; 
                                    }
                                }
                                else{
                                    $msg='';
                                }
                            }
                        }
                    $valarr1 = [ 
                        'internal_'.$delivery[$i].'.*'=>'required',
                        'goods_'.$delivery[$i].'.*'=>'required',
                        'qty_'.$delivery[$i].'.*'=>'required|numeric|gt:0',
                        'rate_'.$delivery[$i].'.*'=>'required|numeric',
                        'per_'.$delivery[$i].'.*'=>'required',
                        'discount_'.$delivery[$i].'.*'=>'required|numeric',
                        'hsn_'.$delivery[$i].'.*'=>'required',
                        'transportation_'.$delivery[$i].'*'=>'required|numeric|gte:0',
                        'other_'.$delivery[$i].'.*'=>'required|numeric|gte:0',
                        'payment_'.$delivery[$i].'.*'=>'required|present'
                    ];
                    $valmsg1=[
                        'internal_'.$delivery[$i].'.*.required'=>'internal order is required',
                        'goods_'.$delivery[$i].'.*.required'=>'Description of Goods is required',
                        'qty_'.$delivery[$i].'.*.required'=>'Quantity is required|',
                        'qty_'.$delivery[$i].'.*.numeric'=>'Quantity must be numeric',
                        'qty_'.$delivery[$i].'.*.gte'=>'Quantity must be greater than or equal to :gte',
                        'rate_'.$delivery[$i].'.*.required'=>'Rate is required',
                        'rate_'.$delivery[$i].'.*.numeric'=>'Rate must be numeric',
                        'rate_'.$delivery[$i].'.*.gte'=>'Rate must be greater than or equal to :gte',
                        'per_'.$delivery[$i].'.*.required'=>'Per is required',
                        'discount_'.$delivery[$i].'.*.required'=>'Discount is required',
                        'discount_'.$delivery[$i].'.*.numeric'=>'Discount must be numeric',
                        'discount_'.$delivery[$i].'.*.gte'=>'Discount must be greater than or equal to :gte',
                        'hsn_'.$delivery[$i].'.*.required'=>'HSN/SAC is required',
                        'transportation_'.$delivery[$i].'*.required'=>'Transportation Charges is required',
                        'transportation_'.$delivery[$i].'.*.numeric'=>'Transportation Charges must be numeric',
                        'transportation_'.$delivery[$i].'.*.gte'=>'Transportation Charges must be greater than or equal to :gte',
                        'other_'.$delivery[$i].'.*.required'=>'Other Charges is required',
                        'other_'.$delivery[$i].'.*.numeric'=>'Other Charges must be numeric',
                        'other_'.$delivery[$i].'.*.gte'=>'Other Charges must be greater than or equal to :gte',
                        'payment_'.$delivery[$i].'.*.present'=>'oresent',
                        'payment_'.$delivery[$i].'.*.required'=>'required'
                    ];
                    $valarr = array_merge($valarr,$valarr1);
                    $valmsg = array_merge($valmsg,$valmsg1);
                }
            }
            if($request->input('io')){
                $msg='';
                $ioss=$request->input('io');
                for($i=0;$i<count($request->input('io'));$i++)
                { 
                        $data=NULL;
                        $io=$request->input('internal_io_'.$ioss[$i]);
                         //DB::enableQueryLog();
                            $io_number=InternalOrder::where('internal_order.id',$io) 
                            ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
                            ->get(['io_number','io_type_id'])->first();
                            $data_cpo=Client_po::where('io',$io)->select('is_po_provided')->get()->first();
                            if($io_number['io_type_id']==9){
                                $msg='';
                            }
                            else{
                                if($data_cpo && $data_cpo['is_po_provided']==1){
                                    $data = Client_po::where('io',$io)->leftJoin('client_po_party','client_po_party.client_po_id','client_po.id')
                                    ->where('party_name',$request->input('party'))
                                    // ->orwhere('client_po.is_po_provided',0)
                                    ->get(['po_number','client_po_party.party_name','client_po.id','client_po.is_po_provided']);
                                    if(!isset($data) || count($data->toArray())<=0)
                                    {
                                        $msg = $msg." ".$io_number['io_number']." does not have Client Purchase Order.<br>";
                                    }
                                    else{
                                        $msg='';
                                    }
                                }
                                else if($data_cpo && $data_cpo['is_po_provided']==0){
                                    $msg='';
                                }
                                else if(!$data_cpo){
                                    $msg = $msg." ".$io_number['io_number']." does not have Client Purchase Order.<br>";
                                }
                                else{
                                    $msg='';
                                }
                            }
                            
                        
                    $valarr1 = [ 
                        'internal_io_'.$ioss[$i]=>'required',
                        'goods_io_'.$ioss[$i]=>'required',
                        'qty_io_'.$ioss[$i]=>'required|numeric|gt:0',
                        'rate_io_'.$ioss[$i]=>'required|numeric',
                        'per_io_'.$ioss[$i]=>'required',
                        'discount_io_'.$ioss[$i]=>'required|numeric',
                        'hsn_io_'.$ioss[$i]=>'required',
                        'transportation_io_'.$ioss[$i]=>'required|numeric|gte:0',
                        'other_io_'.$ioss[$i]=>'required|numeric|gte:0',
                        'payment_io_'.$ioss[$i]=>'required|present'
                    ];
                    $valmsg1=[
                        'internal_io_'.$ioss[$i].'.required'=>'internal order is required',
                        'goods_io_'.$ioss[$i].'.required'=>'Description of Goods is required',
                        'qty_io_'.$ioss[$i].'.required'=>'Quantity is required|',
                        'qty_io_'.$ioss[$i].'.numeric'=>'Quantity must be numeric',
                        'qty_io_'.$ioss[$i].'.gte'=>'Quantity must be greater than or equal to :gte',
                        'rate_io_'.$ioss[$i].'.required'=>'Rate is required',
                        'rate_io_'.$ioss[$i].'.numeric'=>'Rate must be numeric',
                        'rate_io_'.$ioss[$i].'.gte'=>'Rate must be greater than or equal to :gte',
                        'per_io_'.$ioss[$i].'.required'=>'Per is required',
                        'discount_io_'.$ioss[$i].'.required'=>'Discount is required',
                        'discount_io_'.$ioss[$i].'.numeric'=>'Discount must be numeric',
                        'discount_io_'.$ioss[$i].'.gte'=>'Discount must be greater than or equal to :gte',
                        'hsn_io_'.$ioss[$i].'.required'=>'HSN/SAC is required',
                        'transportation_io_'.$ioss[$i].'.required'=>'Transportation Charges is requiredda',
                        'transportation_io_'.$ioss[$i].'.numeric'=>'Transportation Charges must be numeric',
                        'transportation_io_'.$ioss[$i].'.gte'=>'Transportation Charges must be greater than or equal to :gte',
                        'other_io_'.$ioss[$i].'.required'=>'Other Charges is required',
                        'other_io_'.$ioss[$i].'.numeric'=>'Other Charges must be numeric',
                        'other_io_'.$ioss[$i].'.gte'=>'Other Charges must be greater than or equal to :gte',
                        'payment_io_'.$ioss[$i].'.present'=>'oresent',
                        'payment_io_'.$ioss[$i].'.required'=>'required'
                    ];
                    $valarr = array_merge($valarr,$valarr1);
                    $valmsg = array_merge($valmsg,$valmsg1);
                }
            }
            
            $validator = Validator::make($request->all(),$valarr,$valmsg);
            if(strlen($msg)>0)
            {
                $validator->getMessageBag()->add('delivery', $msg);
                $errors = $validator->errors();
                return redirect('/taxinvoice')->withErrors($errors)->withInput();
            }
            if ($validator->fails())
            {
                $errors = $validator->errors();
                return redirect('/taxinvoice')->withErrors($errors)->withInput();
            }
           
            DB::beginTransaction();
            $settings = Settings::where('name','Tax_Invoice_Prefix')->first();
            $tax_number = $settings->value;
           
            // $valarr = array_merge($valarr,$ar);  
            $gst_rate=$request->input('gst');
           
             $fromDate=date('Y-m',strtotime($request->input('created_at')));

             $tax_date = $request->input('created_at');
             $fin_year=CustomHelpers::getFinancialFromDate($tax_date);


             if($fin_year){
                $financial_year = $year=$fin_year;
             }
             else{
                 return redirect('/taxinvoice')->with('error','Enter Document Date According to Financial Year.')->withInput();
             }
                $old_tax=Tax_Invoice::where('financial_year',$year)->get('invoice_number')->last();
                if($old_tax){
                 $taxs=explode('/',$old_tax['invoice_number']);
                 print_r($old_tax['invoice_number']);
                 $v = (int)$taxs[count($taxs)-1];
                 // if($v<1){
                 //     $v=1013;
                 // }
                 $taxs=$v+1;   
             }
             else{
                 $taxs=1; 
             }
                 $prefix = $tax_number."/".$year."/".$taxs;
        
            $x=date('Y-m-d G:i:s',strtotime($request->input('created_at')));
            // print_r($x);die;
            $timestamp = date('Y-m-d G:i:s');
            try {
                $tax=Tax_Invoice::insertGetId([
                    'id' => NULL,
                    'invoice_number'=>$prefix,
                    'financial_year'=>$year,
                    'party_id' =>$request->input('party'),
                    'consignee_id' =>$request->input('consignee'),
                    'terms_of_delivery' =>$request->input('terms'),
                    'gst_type' =>$request->input('gst'),
                    'transportation_charge' =>$request->input('transportation'),
                    'other_charge' =>$request->input('other'),
                    'total_amount' =>0,
                    'created_by' =>Auth::id(),
                    'is_active'=>1,
                    'is_cancelled'=>0,
                    'cancellation_reason'=>'',
                    'cancellation_advised_by'=>'',
                    'date' =>date('Y-m-d',strtotime($request->input('created_at'))),
                    'created_at' =>$timestamp
                ]
                );
            } catch(\Illuminate\Database\QueryException $ex) {
                DB::rollback();
                return redirect('/taxinvoice')->with('error','some error occurred'.$ex->getMessage());
                }
            try {
                $po=date('Y-m-d',strtotime($request->input('created_at')));
            if($tax)
            {
                if($request->input('delivery')){
                    $count=count($request->input('delivery'));
                }
                else{
                    $count=0; 
                }
             
                $delivery=$request->input('delivery');
                $trans=$request->input('transportation');
                $other=$request->input('other');
                $tot_amnt=0;
                for($i=0;$i<$count;$i++){
                    $count1=count($request->input('internal_'.$delivery[$i]));
                    for($j=0;$j<$count1;$j++){
                        $total_amount=0;
                        $io_id=$request->input('internal_'.$delivery[$i]);
                        $goods=$request->input('goods_'.$delivery[$i]);
                        $qty=$request->input('qty_'.$delivery[$i]);
                        $rate=$request->input('rate_'.$delivery[$i]);
                        $per=$request->input('per_'.$delivery[$i]);
                        $discount=$request->input('discount_'.$delivery[$i]);
                        $hsn=$request->input('hsn_'.$delivery[$i]);
                        $transportation_charge=$request->input('transportation_'.$delivery[$i]);
                        $other_charge=$request->input('other_'.$delivery[$i]);
                        $payment=$request->input('payment_'.$delivery[$i]);


                        $hsn_gst=Hsn::where('id',$hsn[$j])->get('gst_rate')->first();
                        $amount=($rate[$j] * $qty[$j]);
                        $gst=($amount*$hsn_gst['gst_rate'])/100;
                        $total=$amount+$gst;
                        $discount_amt=$total-(($total*$discount[$j])/100);
                        $total_amount=$discount_amt;
                        $tot_amnt=$tot_amnt+$total_amount;

                        $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                        ->where('internal_order.id','=',$io_id[$j])->get(['job_details.id','job_details.left_qty'])->first();
                        $cpio_qty = Challan_per_io::where('io',$io_id[$j])
                        ->where('delivery_challan_id',$delivery[$i])->get('good_qty')->first();
                        $lq=$jd_id->left_qty;
                        $q=$cpio_qty->good_qty;
                        if( $qty[$j] >$lq+$q) 
                        {
                            DB::rollback();
                            return redirect('/taxinvoice')->with('error','Internal Order Exhausted.');
                        }
                        Tax::insert([
                                'id' => NULL,
                                'tax_invoice_id' =>$tax,
                                'delivery_challan_id'=>$delivery[$i],
                                'io_id' =>$io_id[$j],
                                'goods' =>$goods[$j],
                                'qty' =>$qty[$j],
                                'rate' =>$rate[$j],
                                'per' =>$per[$j],
                                'discount' =>$discount[$j],
                                'hsn' =>$hsn[$j],
                                'transport_charges' =>$transportation_charge[$j],
                                'other_charges' =>$other_charge[$j],
                                'payment' =>$payment[$j],
                                'amount'=>$total_amount
                            ]);
                            $lqty = $lq - $qty[$j]+$q;

                            JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>$lqty]);
                    }
                }
           
                if($request->input('io')){
                    $io=$request->input('io');
                    $count2=count($io);
                }
                else{
                    $count2=0; 
                }
                for($i=0;$i<$count2;$i++){
                    
                        $total_amount=0;
                        $io_id=$request->input('internal_io_'.$io[$i]);
                        $goods=$request->input('goods_io_'.$io[$i]);
                        $qty=$request->input('qty_io_'.$io[$i]);
                        $ori_leftqty=$request->input('orig_qty_left_'.$io[$i]);
                        $rate=$request->input('rate_io_'.$io[$i]);
                        $per=$request->input('per_io_'.$io[$i]);
                        $discount=$request->input('discount_io_'.$io[$i]);
                        $hsn=$request->input('hsn_io_'.$io[$i]);
                        $transportation_charge=$request->input('transportation_io_'.$io[$i]);
                        $other_charge=$request->input('other_io_'.$io[$i]);
                        $payment=$request->input('payment_io_'.$io[$i]);

                        $hsn_gst=Hsn::where('id',$hsn)->get('gst_rate')->first();
                        $amount=($rate * $qty);
                        $gst=($amount*$hsn_gst['gst_rate'])/100;
                        $total=$amount+$gst;
                        $discount_amt=$total-(($total*$discount)/100);
                        $total_amount=$discount_amt;
                        $tot_amnt=$tot_amnt+$total_amount;

                        $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                        ->where('internal_order.id','=',$io[$i])->get(['job_details.id','job_details.left_qty'])->first();
                        // $cpio_qty = Challan_per_io::where('io',$io_id[$j])
                        // ->where('delivery_challan_id',$delivery[$i])->get('good_qty')->first();
                        // $lq=$jd_id->left_qty;
                        // $q=$cpio_qty->good_qty;
                        // print_r($ori_leftqty);die;
                        if( $ori_leftqty==0) 
                        {
                            DB::rollback();
                            return redirect('/taxinvoice')->with('error','Internal Order Exhausted.');
                        }
                        Tax::insert([
                                'id' => NULL,
                                'tax_invoice_id' =>$tax,
                                'io_id' =>$io_id,
                                'goods' =>$goods,
                                'qty' =>$qty,
                                'rate' =>$rate,
                                'per' =>$per,
                                'discount' =>$discount,
                                'hsn' =>$hsn,
                                'transport_charges' =>$transportation_charge,
                                'other_charges' =>$other_charge,
                                'payment' =>$payment,
                                'amount'=>$total_amount
                            ]);
                            // $lqty = $lq - $qty[$j]+$q;

                            JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>DB::raw('left_qty - '.$qty)]);
                }
               
                $tot=$tot_amnt+$trans+$other;
                $totalamt=$tot;
                $tax_prefix= Tax_Invoice::where('id',$tax)->update([
                    'total_amount'=>$totalamt
                ]);   
            }
            else{
                return redirect('/taxinvoice')->with('error','some error occurred');
            }
            } catch(\Illuminate\Database\QueryException $ex) {
                DB::rollback();
                return redirect('/taxinvoice')->with('error','some error occurred'.$ex->getMessage());
                }
            try {
                 /*** USER LOG ***/
            $party=Party::where('id',$request->input('party'))->select('partyname')->get()->first();
            $party = isset($party->partyname)?$party->partyname:'';
            $changes_array['party'] = $party;

            $consignee_name=Consignee::where('id',$request->input('consignee'))->select('consignee_name')->get()->first();
             $consignee_name = isset($consignee_name->consignee_name)?$consignee_name->consignee_name:'';
             $changes_array['consignee'] = $consignee_name;

             if($request->input('io')){
                $io_id = InternalOrder::whereIN('id',$request->input('io'))->select(DB::raw('group_concat(io_number) as io_number'))->get()->first();
                $io_id = isset($io_id->io_number)?$io_id->io_number:'';
                $changes_array['io'] = $io_id;
             }
             else{
                 $io_id=NULL;
             }

             if($request->input('delivery')){
                $delivery = Delivery_challan::whereIN('id',$request->input('delivery'))->select(DB::raw('group_concat(challan_number) as challan_number'))->get()->first();
                $delivery = isset($delivery->challan_number)?$delivery->challan_number:'';
                $changes_array['delivery'] = $delivery;
             }
             else{
                 $delivery=NULL;
             }

             $log_array=array(
                
                'Party'=>$party,
                'Consignee Name'=>$consignee_name,
                'Delivery Challan'=>$delivery,
                'Internal Order'=>$io_id,
                'Terms Of Delivery'=>$request->input('terms'),
                'GST Type'=>$request->input('gst'),
                'Transportation Charges'=>$request->input('transportation'),
                'Other Charges'=>$request->input('other'),
                'Tax Date'=>$request->input('created_at')
         
             );

             CustomHelpers::userLog('Tax Invoice Created',$tax,'Tax Invoice Created',
             $log_array);
            } catch(\Illuminate\Database\QueryException $ex) {
                DB::rollback();
                return redirect('/taxinvoice')->with('error','some error occurred'.$ex->getMessage());
                }

            try {
                $partyid=$request->input('party');
                $party=Party::where('id',$partyid)->get('party.*')->first();
                $getAmount=Tax_Invoice::whereDate('tax_invoice.date',$po)
                ->leftJoin('tax','tax.tax_invoice_id','=','tax_invoice.id')
                ->leftJoin('waybill','waybill.challan_id','=','tax.delivery_challan_id')
                ->leftJoin('party','tax_invoice.party_id','=','party.id')
                ->where('party.gst',$party['gst'])
                ->where('tax_invoice.waybill_status','!=','2')
                ->get([
                    'tax_invoice.id',
                    'tax_invoice.invoice_number',
                    'tax_invoice.total_amount',
                    'tax_invoice.waybill_status',
                    'waybill.challan_id as waybill_challan',
                    'tax.delivery_challan_id',
                    'tax.io_id',
                    'party.gst',
                    'party.id as party'
        
                ])->toArray();
                $counter=1; 
            $total_ByDate=0;
            $amntDate=0;
            $Todatdate=date('Y-m-d'); 
            $delivery_id=[];
            $mesg="";
            $umsetflag=0;
            $del_id=0;
            if(count($getAmount)>0){
                if($getAmount[0]['waybill_challan']!==NULL)
                {
                        $umsetflag=1;
                        $total_ByDate=0;
                        
                   
                }
                else
                {
                    $total_ByDate=$getAmount[0]['total_amount'] ;
                    $delivery_id[0]=$getAmount[0]['id'];  
                }
                if(count($getAmount)>1){
                   
                    for($j=1;$j<count($getAmount);$j++){
                        if($getAmount[$j]['waybill_challan']!==NULL)
                            unset($getAmount[$j]);    
                        }
                        foreach($getAmount as $key){
                            $total_ByDate=$total_ByDate+$key['total_amount']; 
                            $delivery_id[$j]=$key['id']; 
                        }
                    $del_id=implode(':',$delivery_id);    
                }
                if($umsetflag==1)
                    unset($getAmount[0]);
                
                    $waybill = Settings::where('name','delivery_amount')->first();
                    $waybill_value = $waybill->value;
                    if($total_ByDate>=$waybill_value)
                    {
                        foreach($getAmount as $key){
                            $way_id=$key['id'];
                            
                            Tax_Invoice::where('id',$way_id)->update([
                                    'waybill_status'=> 1,
                                ]);
                        }
    
                        $amntDate=$total_ByDate;
                        $mesg="Today's Total Amount of Tax Invoice has been exceeded the limit ".$waybill_value. ".Create WayBill..";
                        $del_id=implode(':',$delivery_id);    
                    }
                    else{
                        $amntDate=$total_ByDate;
                        $mesg=NULL;
                        
                    }
            }
            } catch(\Illuminate\Database\QueryException $ex) {
                DB::rollback();
                return redirect('/taxinvoice')->with('error','some error occurred'.$ex->getMessage());
                }
            DB::commit();
            return redirect('/taxinvoice')->with(['message'=>'tax','tax'=>$tax,'prefix'=>$prefix,'amntDate'=>$amntDate,'delivery_id'=>$del_id,'mesg'=>$mesg,'gst'=>$party['gst'],'date'=>$Todatdate,'refer'=>$party['reference_name'],'pointer'=>$party['gst_pointer']]);
            }
            catch(\Illuminate\Database\QueryException $ex) {
                DB::rollback();
            return redirect('/taxinvoice')->with('error','some error occurred'.$ex->getMessage());
            }
        }

                public function delivery_challan_list()
                {
                    $prefix = Settings::where('name', '=', 'internal_order_prefix')->first()->value;
                    $data=array('layout'=>'layouts.main','prefix'=>$prefix);
                    return view('sections.delivery_challan_summary', $data);
                }
                public function delivery_challan_api(Request $request)
                {
                    $search = $request->input('search');
                    $serach_value = $search['value'];
                    $start = $request->input('start');
                    $limit = $request->input('length');
                    $offset = empty($start) ? 0 : $start ;
                    $limit =  empty($limit) ? 10 : $limit ;
                    $client_po = Delivery_challan::where('delivery_challan.is_active',1)
                    ->rightJoin('consignee','delivery_challan.consignee_id', '=', 'consignee.id')
                        ->leftJoin('party','delivery_challan.party_id', '=', 'party.id')
                        ->leftJoin('challan_per_io','delivery_challan.id', '=', 'challan_per_io.delivery_challan_id')
                        ->leftJoin('request_permission',function($join){
                            $join->on('request_permission.data_id','=','delivery_challan.id');
                            $join->where('request_permission.status','=','pending');
                            $join->where('request_permission.data_for','=','deliverychallan');
                        })
                        ->leftJoin('request_permission as rp',function($join){
                            $join->on('rp.data_id','=','delivery_challan.id');
                            $join->where('rp.status','=','pending');
                            $join->where('rp.data_for','=','deliverychallanupdate');
                        })
                        ->leftJoin('internal_order','internal_order.id', '=', 'challan_per_io.io')->select(
                            DB::raw('CONVERT(group_concat(DISTINCT(SUBSTRING_INDEX(challan_number,"/",-1))),UNSIGNED  INTEGER) as asd'),
                            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as ch_no') ,
                            DB::raw('group_concat(DISTINCT(delivery_challan.is_update)) as is_update') ,
                            DB::raw('group_concat(DISTINCT(delivery_challan.financial_year)) as financial_year') ,
                            DB::raw('group_concat(DISTINCT(delivery_challan.id)) as id') ,
                            DB::raw('group_concat(DISTINCT(party.partyname)) as pname') ,
    
                            DB::raw('(DATE_FORMAT(delivery_challan.delivery_date ,"%Y-%m-%d")) as delivery_date'),
                            DB::raw('(DATE_FORMAT(delivery_challan.created_time ,"%d-%m-%Y %r")) as created'),
                            DB::raw('(DATE_FORMAT(delivery_challan.created_time ,"%d-%m-%Y")) as date'),
                            DB::raw('group_concat(DISTINCT(consignee.consignee_name)) as cname') ,
                            DB::raw('group_concat(DISTINCT(delivery_challan.dispatch)) as dispatch') ,
                            DB::raw('group_concat(internal_order.io_number) as io') ,
                            'request_permission.status as st',
                            'rp.status as st2'
                        )->groupBy('delivery_challan.id');
                      
                    if(!empty($serach_value))
                    {

                        $client_po->where(function($query) use ($serach_value){
                            $query->where('party.partyname','LIKE',"%".$serach_value."%")
                                ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
                                ->orwhere('delivery_challan.challan_number','like',"%".$serach_value."%")
                                ->orwhere('internal_order.io_number','like',"%".$serach_value."%")
                                ->orwhere('delivery_challan.dispatch','like',"%".$serach_value."%");
                        });
                    }
                    $count = count($client_po->get());
                    $client_po = $client_po->offset($offset)->limit($limit);
                    if(isset($request->input('order')[0]['column']))
                    {
                        $data = ['ch_no','pname','cname','rp.status','delivery_challan.is_update','request_permission.status as st','io','dispatch','delivery_challan.created_time','delivery_challan.delivery_date'];
                        $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                        $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
                    }
                    else
                        $client_po->orderBy('financial_year','desc')->orderBy('asd','desc')->orderBy('delivery_challan.id','desc');;

                    $client_po = $client_po->get();


                    $array['recordsTotal'] = $count;
                    $array['recordsFiltered'] = $count;
                    $array['data'] = $client_po; 
                    return json_encode($array);
                }
        public function delivery_challan_updatedb($id,Request $request)
        {
            // print_r($request->input());die;
            try {
                $valarr = [
                    'party' => 'required|numeric|exists:party,id',
                    'reference'=>'required|string|exists:party,reference_name',
                    'consignee_name' => 'required',
                    'io_id.*' => 'required',
                    'delivery_challan_date.*' => 'required|date',
                    'uom.*' => 'required',
                    'goods_qty.*' => 'required|numeric',
                    'goods_des.*' => 'required',
                    'pak_details.*' => 'required',
                    'update_reason' => 'required',
                    'goods_dispatch' => 'required' ,
                    'del_date' => 'required'        
                ];
                $vehicle_no1=0;
                if($request->input('goods_dispatch')==2)
                {
                    $valarr1=['self_id'=>'required',
                                'vehicle_no_self'=>'required'
                            ];
                    $dispatch_id=implode(',',$request->input('self_id'));
                    $bilty_docket = NULL;
                    $bilty_date = NULL;
                    $vehicle_no1 = $request->input('vehicle_no_self');
                    $valarr = array_merge($valarr,$valarr1);     
                    $self_id=Goods_Dispatch::whereIN('id',$request->input('self_id'))->select(DB::raw('group_concat(courier_name) as courier_name'))->get()->first();
                    $self_id = isset($self_id->courier_name)?$self_id->courier_name:'';
                    $changes_array['self_id'] = $self_id;
                    $vehicle_no_self=Vehicle::where('id',$request->input('vehicle_no_self'))->select('vehicle_number')->get()->first();
                    $vehicle_no_self = isset($vehicle_no_self->vehicle_number)?$vehicle_no_self->vehicle_number:'';
                    $changes_array['vehicle_no_self'] = $vehicle_no_self;   
                }
                else if($request->input('goods_dispatch')==3)
                {
                    $valarr1=[
                        'goods_dispatch_id'=>'required',
                        'bilty_docket'=>'required',
                        'bilty_date'=>'required',
                    ];
                    $dispatch_id=$request->input('goods_dispatch_id');
                
                    $bilty_docket = $request->input('bilty_docket');
                    $bilty_date = $request->input('bilty_date');
                    $valarr = array_merge($valarr,$valarr1);  
                    $goods_dispatch_id=Goods_Dispatch::where('id',$request->input('goods_dispatch_id'))->select('courier_name')->get()->first();
                $goods_dispatch_id = isset($goods_dispatch_id->courier_name)?$goods_dispatch_id->courier_name:'';
                $changes_array['goods_dispatch_id'] = $goods_dispatch_id;      
                }
                else if($request->input('goods_dispatch')==1)
                {
                    $valarr1=[
                        'goods_dispatch_id1'=>'required',
                        'bilty_docket1'=>'required',
                        'bilty_date1'=>'required|date',
                    ];
                    $dispatch_id=$request->input('goods_dispatch_id1');
                    $bilty_date = $request->input('bilty_date1');
                    $bilty_docket = $request->input('bilty_docket1');
                    $vehicle_no1 = 0;
                    $valarr = array_merge($valarr,$valarr1);   
                    
                    $goods_dispatch_id1=Goods_Dispatch::where('id',$request->input('goods_dispatch_id1'))->select('courier_name')->get()->first();
                    $goods_dispatch_id1 = isset($goods_dispatch_id1->courier_name)?$goods_dispatch_id1->courier_name:'';
                    $changes_array['goods_dispatch_id1'] = $goods_dispatch_id1;
    
                }
                
                $this->validate($request,$valarr);
                $value=0;
                $party=$request->input('party');
                $io_id=$request->input('io_id');
                $old_io=$request->input('old_io');
                $consignee_name=$request->input('consignee_name');
                $posted_date = date('Y-m-d',strtotime($request->input('del_date')));
                $pak_details=$request->input('pak_details');
                $goods_des=$request->input('goods_des');
                $goods_qty=$request->input('goods_qty');
                $reference=$request->input('reference');
                $delivery_date = $request->input('delivery_date');
                $uom = $request->input('uom');
                $rate_per_qty=$request->input('rate_per_qty');
                $gst_rate=$request->input('gst_rate');
                $bilty_date = $request->input('bilty_date');
                $timestamp = date('Y-m-d G:i:s');
                
                $settings = Settings::where('name','Delivery_Challan_Prefix')->first();
                $delivery_number = $settings->value;

                if($request->input('io_id')){
                    if(count($io_id) !== count(array_unique($io_id))){
                        return redirect('/deliverychallan/update/'.$id)->with('error','No Two Or More Same IO Clubbed In One DC.');
                    }
                }
                else{
                    return redirect('/deliverychallan/update/'.$id)->with('error','Please select atleast one internal order');
                }

                // CustomHelpers::userActionLog($request->input()['update_reason'],$id,'Delivery challan Update');
                $insert_array = [
                        'party_id' => $party,
                        'reference_name'=>$request->input('reference'),
                        'consignee_id' => $consignee_name,
                        'delivery_date' => $posted_date,
                        'total_amount'=>0,
                        'dispatch' =>$request->input('goods_dispatch'),
                        'dispatch_id' =>$dispatch_id,
                        'bilty_docket' =>$bilty_docket,
                        'created_by' =>Auth::id(),
                        'vehicle_id'=>$vehicle_no1,
                        'is_active'=>1,
                        'updated_at' =>$timestamp
                    ];
                $docket_date="";
                if($bilty_date)
                {
                    $docket_date = date("Y-m-d", strtotime($bilty_date));
                    $insert_array['docket_date'] = $docket_date;
                }
                $delivery=Delivery_challan::where('id','=',$id)->update($insert_array);
                 $all_amt_add=0;
                if(isset($old_io)){
                    $count= count($old_io);
                    for($i=0;$i<$count;$i++){
                        $total_amount=0;
                        $amount=($rate_per_qty[$i] * $goods_qty[$i]);
                        $gst=($amount*$gst_rate[$i])/100;
                        $total=$amount+$gst;
                        $total_amount=$total_amount+$total;
                        $all_amt_add=$all_amt_add+$total_amount;
                        $delivery_date[$i] = date('Y-m-d',strtotime($delivery_date[$i]));
                       
                        $old_qty=Challan_per_io::where('id','=',$old_io[$i])->get('good_qty')->first()->good_qty;
                        Challan_per_io::where('id','=',$old_io[$i])->update(
                            [
                                'io' =>$io_id[$i],
                                'delivery_challan_id' => $id,
                                'delivery_challan_date' => $delivery_date[$i],
                                'uom_id' => $uom[$i],
                                'rate'=>$rate_per_qty[$i],
                                'good_desc' => $goods_des[$i],
                                'good_qty' => $goods_qty[$i],
                                'packing_details' => $pak_details[$i],
                                'amount'=>$total
                            ]
                        );
                        $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                        ->where('internal_order.id','=',$io_id[$i])->get(['job_details.id','job_details.left_qty'])->first();

                        $lqty = $jd_id->left_qty-$goods_qty[$i]+$old_qty;
                        JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>$lqty]);

                    }
                }
                else{
                    $count= 0;
                }
            
            
                $count1= count($request->input('io_id'));
            
                    for($i=$count;$i<$count1;$i++){
                        $total_amount=0;
                        $amount=($rate_per_qty[$i] * $goods_qty[$i]);
                        $gst=($amount*$gst_rate[$i])/100;
                        $total=$amount+$gst;
                        $total_amount=$total_amount+$total;
                        $all_amt_add=$all_amt_add+$total_amount;
                        $delivery_date[$i] = date('Y-m-d',strtotime($delivery_date[$i]));

                    
                        Challan_per_io::insert(
                            [
                                'id' => NULL,
                                'io' =>$io_id[$i],
                                'delivery_challan_id' => $id,
                                'delivery_challan_date' => $delivery_date[$i],
                                'uom_id' => $uom[$i],
                                'rate'=>$rate_per_qty[$i],
                                'good_desc' => $goods_des[$i],
                                'good_qty' => $goods_qty[$i],
                                'packing_details' => $pak_details[$i],
                                'amount'=>$total
                            ]
                        );
                        $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                        ->where('internal_order.id','=',$io_id[$i])->get(['job_details.id','job_details.left_qty'])->first();
                        $lqty = $jd_id->left_qty-$goods_qty[$i];
                        JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>$lqty]);

                    }
                    $Challan_per_io= Challan_per_io::where('delivery_challan_id','=',$id)->get();
                    $amt_total=0;
                    foreach($Challan_per_io as $key){
                        $amt_total=$amt_total+$key['amount'];
                    }
                    $prefix = Delivery_challan::where('id','=',$id)->get('challan_number')->first();
                    $number= Delivery_challan::where('id','=',$id)->update(
                    [
                        'total_amount'=>$amt_total
                    ]
                );
                $data=RequestPermission::where('data_for','deliverychallan')->where('data_id',$id)
                ->select(
                    'request_permission.id',
                    'request_permission.status'
                )->orderBy('id','DESC')
                ->first();
                if($data){
                    if($data->status=="allowed"){
                        RequestPermission::where('data_for','deliverychallan')->where('data_id',$id)->update(['status'=>'expired']);
                    }
                }
                $data1=RequestPermission::where('data_for','deliverychallanupdate')->where('data_id',$id)
                ->select(
                    'request_permission.id',
                    'request_permission.status'
                )->orderBy('id','DESC')
                ->first();
                if($data1){
                    if($data1->status=="allowed"){
                        RequestPermission::where('data_for','deliverychallanupdate')->where('data_id',$id)->update(['status'=>'expired']);
                        
                    }
                }
                    Delivery_challan::where('id',$id)->update(['is_update'=>'expired']);
       
                    /*** USER LOG ***/
             $reference=Reference::where('id',$request->input('reference'))->select('referencename')->get()->first();
             $reference = isset($reference->referencename)?$reference->referencename:'';
             $changes_array['reference'] = $reference;

             $party=Party::where('id',$request->input('party'))->select('partyname')->get()->first();
             $party = isset($party->partyname)?$party->partyname:'';
             $changes_array['party'] = $party;
 
              $consignee_name=Consignee::where('id',$request->input('consignee_name'))->select('consignee_name')->get()->first();
             $consignee_name = isset($consignee_name->consignee_name)?$consignee_name->consignee_name:'';
             $changes_array['consignee_name'] = $consignee_name;

            //  $changes_array['delivery_date'] = $posted_date;
        
             $io_id = InternalOrder::whereIN('id',$request->input('io_id'))->select(DB::raw('group_concat(io_number) as io_number'))->get()->first();
             $io_id = isset($io_id->io_number)?$io_id->io_number:'';
             $changes_array['io_id'] = $io_id;

             $uom=Unit_of_measurement::whereIN('id',$request->input('uom'))->select(DB::raw('group_concat(uom_name) as uom_name'))->get()->first();
             $uom = isset($uom->uom_name)?$uom->uom_name:'';
             $changes_array['uom'] = $uom;

             $goods_dispatch=Dispatch_mode::where('id',$request->input('goods_dispatch'))->select('name')->get()->first();
             $goods_dispatch = isset($goods_dispatch->name)?$goods_dispatch->name:'';
             $changes_array['goods_dispatch'] = $goods_dispatch;
             $log_array=array(
                
                'reference'=>'Reference Name',
                'party'=>'Party',
                'consignee_name'=>'Consignee Name',
                'delivery_date'=>'Delivery Date',
                'io_id'=>'Internal Order',
                'uom'=>'Qty Unit',
                'gst_rate'=>'Gst',
                'goods_qty'=>'Qty',
                'goods_des'=>'Goods Description',
                'rate_per_qty'=>'Rate Per qty',
                'pak_details'=>'Packing Details',
                'goods_dispatch'=>'Goods Dispatch Mode',
                'goods_dispatch_id1'=>'Company Name',
                'goods_dispatch_id'=>'Company Name',
                'vehicle_no_self'=>'Vehicle',
                'self_id'=>'Carrier Name',
                'bilty_docket1'=>'Bilty Docket',
                'bilty_docket'=>'Bilty Docket',
                'bilty_date1'=>'Bilty Date',
                'bilty_date'=>'Bilty Date'
         
             );
             CustomHelpers::userActionLog($request->input()['update_reason'],$id,'Delivery Challan Updated',
             $log_array,$changes_array,$removekeys=array('gst_rate','old_io'));
            /***  END USER LOG ***/
                $partyid=$request->input('party');
                $party=Party::where('id',$partyid)->get('party.*')->first();
                $getAmount=Delivery_challan::where('delivery_challan.party_id',$partyid)
                ->leftJoin('party','delivery_challan.party_id','=','party.id')
                ->where('party.gst',$party['gst'])
                ->whereDate('delivery_date', $posted_date)
                ->where('delivery_challan.waybill_status','!=','2')
               ->get([
                    'party.gst',
                    'delivery_challan.id',
                    'delivery_challan.total_amount'
                ]);
                    
                $Todatdate=date('Y-m-d'); 
                if(count($getAmount)>0){
                    $waybill = Settings::where('name','delivery_amount')->first();
                    $waybill_value = $waybill->value;
                    $total_ByDate=0;
                    foreach($getAmount as $key){
                        $total_ByDate=$total_ByDate+$key['total_amount'];
                        $delivery_id[]=$key['id'];
                    }
                    if($total_ByDate>=$waybill_value){
                        foreach($getAmount as $key){
                            $way_id=$key['id'];
                            Delivery_challan::where('id',$way_id)->update(
                                [
                                    'waybill_status'=> 1,
                                    
                                ]
                            );
                        }
                    $amntDate=$total_ByDate;
                    $mesg="Today's Total Amount of Delivery has been exceeded the limit ".$waybill_value. ".Create WayBill..";
                    }
                    else{
                        $amntDate=$total_ByDate;
                    $mesg=NULL;
                    }
                    // print_r($amt_total);die;
            }
            else{
                // print_r($amt_total);die;
                $amntDate=$amt_total;
                $mesg=NULL;
            }
            if(isset($delivery_id)){
                $del_id=implode(':',$delivery_id);
            }
            else{
                $del_id=0;
            }
           
                    return redirect('/deliverychallan/update/'.$id)->with(['message'=>'delivery','delivery'=>$delivery,'delivery_prefix'=>$prefix['challan_number'],'amntDate'=>$amntDate,'delivery_id'=>$del_id,'mesg'=>$mesg,'gst'=>$party['gst'],'date'=>$Todatdate,'refer'=>$reference,'pointer'=>$party['gst_pointer']]);
                }
                catch(\Illuminate\Database\QueryException $ex) {
                return redirect('/deliverychallan/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        
                }


        }
        public function tax_invoice_delete($id,$do){
            $data = RequestPermission::where('id',$id)->first();
            if($data->status== 'allowed' && $do == 'rejected' || 
            $data->status== 'pending' && $do == 'rejected' || 
            $data->status== 'pending' && $do == 'allowed' ||
            $data->status== 'rejected' && $do == 'allowed')
            {
                if($do=='pending')
                    $st='Pending';
                else if($do=='allowed')
                    $st='Cancelled';
                else if($do=='rejected')
                    $st='CancelRequest';
                
                RequestPermission::where('id',$id)->update([
                    'authorised_by'=>Auth::id(),
                    'status'=>$do,
                ]);
                if($do=='allowed'){
                    $id=$data->data_id;
                    $check = Tax::where('tax_invoice_id','=',$id)->select('id','qty','io_id')->get(); 
            $dc = Tax::where('tax_invoice_id','=',$id)
            ->leftJoin('delivery_challan','delivery_challan.id','tax.delivery_challan_id')
            ->select(DB::raw('group_concat(DISTINCT(challan_number)) as challan_number'))->get()->first(); 
            // print($dc);die;
            $i=0;
            foreach($check as $it){
                $io_no=InternalOrder::where('id','=',$it['io_id'])->select('job_details_id')->get()->first();
                DB::enableQueryLog();
                $internal_order= JobDetails::where('id',$io_no['job_details_id'])->update(['left_qty'=>DB::raw('left_qty + '.$it['qty'])]);
                if($internal_order){
                    Tax::where('id',$it['id'])->delete();
                    $x[$i]=$io_no['io_number'];
                }
                else{
                    $message="error";

                }
                $i++;
            }
    
            $invoice_number=Tax_Invoice::where('id',$id)->select('invoice_number')->get()->first();
            $invoice_number = isset($invoice_number->invoice_number)?$invoice_number->invoice_number:'';
          
            // print_r($dc['challan_number']);die;
            $changes_array['invoice_number'] = $invoice_number;
            $changes_array['delivery_challan'] = $dc['challan_number'];
            $page = \Request::fullUrl();
            $user=Userlog::insertGetId([
                'userid'=>Auth::id(),
                'page'=>$page,
                'action'=>'Tax Invoice',
                'data_id'=>$id,
                'description'=>'Delete Tax Invoice Delivery Challan',
                'content_changes'=>json_encode($changes_array),
                'content'=>json_encode($changes_array)
                
            ]);
            $upd_ti = Tax_Invoice::where('id', '=', $id)
            ->update(['total_amount'=>0]);

            $new_dc = Tax_Invoice::where('id', $id)->get('tax_invoice.*')->first();
            $party=Party::where('id',$new_dc['party_id'])->get('party.*')->first();

            $getAmount = Tax_Invoice::leftjoin('party','tax_invoice.party_id','party.id')
            ->leftJoin('tax','tax.tax_invoice_id','=','tax_invoice.id')
            ->leftJoin('waybill','waybill.challan_id','=','tax.delivery_challan_id')
            ->where('party.gst',$party['gst'])
            ->whereDate('tax_invoice.date',$new_dc['date'])
            ->where('waybill_status','<>',2)
            ->get([
                'tax_invoice.id',
                'tax_invoice.invoice_number',
                'tax_invoice.total_amount',
                'tax_invoice.waybill_status',
                'waybill.challan_id as waybill_challan',
                'tax.delivery_challan_id',
                'tax.io_id',
                'party.gst',
                'party.id as party'
    
            ])->toArray();
            $counter=1; 
            $total_ByDate=0;
            $amntDate=0;
            $Todatdate=date('Y-m-d'); 
            $delivery_id=[];
            $mesg="";
            $umsetflag=0;
            $del_id=0;

            if(count($getAmount)>0){
                if($getAmount[0]['waybill_challan']!==NULL)
                {
                        $umsetflag=1;
                        $total_ByDate=0;
                        
                   
                }
                else
                {
                    $total_ByDate=$getAmount[0]['total_amount'] ;
                    $delivery_id[0]=$getAmount[0]['id'];  
                }
                if(count($getAmount)>1){
                   
                    for($j=1;$j<count($getAmount);$j++){
                        if($getAmount[$j]['waybill_challan']!==NULL)
                            unset($getAmount[$j]);    
                        }
                        foreach($getAmount as $key){
                            $total_ByDate=$total_ByDate+$key['total_amount']; 
                            $delivery_id[$j]=$key['id']; 
                        }
                    $del_id=implode(':',$delivery_id);    
                }
                if($umsetflag==1)
                    unset($getAmount[0]);
                
                    $waybill = Settings::where('name','delivery_amount')->first();
                    $waybill_value = $waybill->value;
                    if($total_ByDate>=$waybill_value)
                    {
                        foreach($getAmount as $key){
                            $way_id=$key['id'];
                            
                            Tax_Invoice::where('id',$way_id)->update([
                                    'waybill_status'=> 1,
                                ]);
                        }
    
                        $amntDate=$total_ByDate;
                        $del_id=implode(':',$delivery_id);    
                    }
                    else{
                        $amntDate=$total_ByDate;   
                    }

                    $mesg="success";
            }
            else{
                $mesg="success";;
            }
                }
                    return array('message'=>'success');

                }
                else{
                    return array('message'=>'error occured');
                } 
           

        }
        public function delivery_challan_delete($id,$do){
            $data = RequestPermission::where('id',$id)->first();
            if($data->status== 'allowed' && $do == 'rejected' || 
            $data->status== 'pending' && $do == 'rejected' || 
            $data->status== 'pending' && $do == 'allowed' ||
            $data->status== 'rejected' && $do == 'allowed')
            {
                if($do=='pending')
                    $st='Pending';
                else if($do=='allowed')
                    $st='Cancelled';
                else if($do=='rejected')
                    $st='CancelRequest';
                
                RequestPermission::where('id',$id)->update([
                    'authorised_by'=>Auth::id(),
                    'status'=>$do,
                ]);
                
                if($do=='allowed'){
                    $id=$data->data_id;
                    $cpo=Challan_per_io::where('delivery_challan_id',$id)->get(['io','id','delivery_challan_id','good_qty']);
                    $i=0; 
                    foreach ($cpo as $key) {
                        $io=InternalOrder::where('internal_order.id',$key['io'])->get(['job_details_id','io_number'])->first();
                        $job=JobDetails::where('id',$io['job_details_id'])->update([
                            'left_qty'=>DB::raw('left_qty + '.$key['good_qty'])
                        ]);
        
                        if($job){
                        
                            $x[$i]=$io['io_number'];
                       
                            Challan_per_io::where('id',$key['id'])->delete();
                        }
                        else{
                            $message="error";
        
                        }
                        $i++;
                    }
                    $challan_number=Delivery_challan::where('id',$id)->select('challan_number')->get()->first();
                    $challan_number = isset($challan_number->challan_number)?$challan_number->challan_number:'';
                    $z=implode(',',$x);
                
                    $changes_array['challan_number'] = $challan_number;
                    $changes_array['io_number'] = $z;
                    $page = \Request::fullUrl();
                    $user=Userlog::insertGetId([
                        'userid'=>Auth::id(),
                        'page'=>$page,
                        'action'=>'Delivery Challan',
                        'data_id'=>$id,
                        'description'=>'Delete Delivery Challan Internal Order',
                        'content_changes'=>json_encode($changes_array),
                        'content'=>json_encode($changes_array)
                        
                    ]);
                    $upd_dc = Delivery_challan::where('delivery_challan.id', '=', $id)
                    ->update(['delivery_challan.total_amount'=>0]);
                    
                    $new_dc = Delivery_challan::where('id', $id)->get('delivery_challan.*')->first();
                    $party=Party::where('id',$new_dc['party_id'])->get('party.*')->first();
                   
                    $check_waybill = Delivery_challan::leftjoin('party','delivery_challan.party_id','party.id')
                    ->where('party.gst',$party['gst'])
                    ->whereDate('delivery_challan.delivery_date',$new_dc['delivery_date'])
                    ->where('waybill_status','<>',2)
                    ->get()->toArray();
                    if($check_waybill){
                        $waybill = Settings::where('name','delivery_amount')->first();
                        $waybill_value = $waybill->value;
                        $total_ByDate=0;
                        foreach($check_waybill as $key){
                            $total_ByDate= $total_ByDate + $key['total_amount'];
                            $delivery_id[]=$key['id'];
                        }
                        if($total_ByDate < $waybill_value)
                        {
                            foreach($check_waybill as $key){
                                $way_id=$key['id'];
                                Delivery_challan::where('id',$way_id)->update(
                                    [
                                        'waybill_status'=> 0,
                                    ]
                                );
                            }
                        }
                        else{
                            $message="error";
                        }
                    }
                    else{
                        $message="error";
                    }
                }
                return array('message'=>'success');

            }
            else{
                return array('message'=>'error occured');
            }
           
        }
        public function delivery_challan_view($id)
        {
            $dc = [];
            $dc['dc'] = Delivery_challan::where('delivery_challan.id', '=', $id)->where('delivery_challan.is_active','=',1)
                    ->leftJoin('challan_per_io', 'delivery_challan.id','=', 'challan_per_io.delivery_challan_id')
                    ->leftJoin('internal_order as io', 'io.id','=', 'challan_per_io.io')
                    ->leftJoin('job_details as jd', 'io.job_details_id','=', 'jd.id')
                    ->leftJoin('hsn', 'jd.hsn_code','=', 'hsn.id')
                    ->leftJoin('unit_of_measurement','challan_per_io.uom_id','=','unit_of_measurement.id')
                    ->leftJoin('advance_io', 'jd.advance_io_id','=', 'advance_io.id')
                    ->select('delivery_challan.*', 'challan_per_io.io', 'challan_per_io.delivery_challan_date', 'unit_of_measurement.uom_name',
                            'challan_per_io.good_desc', 'challan_per_io.good_qty','challan_per_io.rate as challan_rate','challan_per_io.packing_details', 'io.created_time as io_time', 'io.io_number',
                            'hsn.hsn as hsn', 'hsn.gst_rate as gst', 'jd.rate_per_qty as qty_rate', 'advance_io.amount as amount')
                    ->get();
                    $dc['party'] = Delivery_challan::where('delivery_challan.id', '=', $id)->where('delivery_challan.is_active','=',1)
                    ->leftJoin('party', 'party.id','=', 'delivery_challan.party_id') // party started
                    ->leftJoin('party_reference','party_reference.id','delivery_challan.reference_name')
                    ->leftJoin('cities as cp', 'party.city_id','=', 'cp.id')
                    ->leftJoin('states as sp', 'sp.id','=', 'party.state_id')
                    ->leftJoin('countries as ccp', 'ccp.id','=', 'party.country_id') // party ennded
                    ->leftJoin('consignee', 'consignee.id','=', 'delivery_challan.consignee_id') // consg started
                    ->leftJoin('cities as cc', 'consignee.city','=', 'cc.id')
                    ->leftJoin('vehicle', 'vehicle.id','=', 'delivery_challan.vehicle_id')
                    ->leftJoin('states as sc', 'sc.id','=', 'consignee.state')
                    ->leftJoin('countries as ccc', 'ccc.id','=', 'consignee.country') // consg ennede
                    ->leftJoin('goods_dispatch', 'goods_dispatch.id','=', 'delivery_challan.dispatch_id')
                    ->leftJoin('dispatch_mode', 'goods_dispatch.mode','=', 'dispatch_mode.id')
                    ->select('party.partyname as pname','party_reference.referencename as refname', 'party.address as paddr',
                    'party.pincode as ppcode', 'cp.city as pcity', 'sp.name as pstate', 'ccp.name as pcountry',
                    'consignee.address as caddr', 'consignee.consignee_name as cname', 'consignee.pincode as cpcode',
                    'cc.city as ccity', 'sc.name as cstate','ccc.name as ccountry', 'dispatch_mode.name as mode',
                    'goods_dispatch.address','goods_dispatch.gst as dp_gst','vehicle.vehicle_number as vehicle')
                    ->first();
                $dc['goods_dispatch'] = Goods_Dispatch::whereIn('goods_dispatch.id',explode(',',$dc['dc'][0]->dispatch_id))
                        ->select( 'goods_dispatch.courier_name')->get()->toArray();
                $dc['layout']='layouts.main' ;
                if($dc!=NULL && count($dc['dc'])!=0){
                    return view('sections.delivery_challan_view',$dc);
                }
                    else{
                        $message="No Delivery Challan exist!!";
                        return redirect('/deliverychallan/list')->with('error',$message);
                    }
            
            }
            public function delivery_challan_update($id)
        {
            $tax_date=Delivery_challan::where('id','=',$id)->select(DB::raw('DATE_FORMAT(created_time,"%d-%m-%Y") as date'))->get()->first();
            $cur_date=date('d-m-Y');
            // return $cur_date;
            $x=Auth::user()->user_type;
            $data1=RequestPermission::where('data_for','deliverychallanupdate')->where('data_id',$id)
                    ->select(
                        'request_permission.id',
                        'request_permission.status'
                    )->orderBy('id','DESC')
                    ->first();
                    if($data1 && $x!="superadmin"){
                        if($data1->status=="pending"){
                         return redirect('deliverychallan/list')->with('error','Request For Edit is still Pending');  
                        } 
                        else if($data1->status=="expired"){
                            return redirect('deliverychallan/list')->with('info','Request For Edit is Expired');  
                           }  
                    }
                 
                    else if(!$data1  && $x!="superadmin" && ($tax_date['date']!=$cur_date)){
                        return redirect('deliverychallan/list')->with('error','Request For Edit is not Found');
                    }
            $Delivery_challan = Delivery_challan::where('delivery_challan.id','=',$id)->get()->first();
            $Challan_per_io= Challan_per_io::where('delivery_challan_id','=',$id)
            ->leftJoin('internal_order','internal_order.id','=','challan_per_io.io')
            ->leftJoin('job_details','job_details.id','=','internal_order.job_details_id')
            ->leftJoin('hsn','hsn.id','=','job_details.hsn_code')
            ->select(
                'challan_per_io.id as id',
                'challan_per_io.io',
                'internal_order.io_number',
                'job_details.rate_per_qty',
                'job_details.left_qty',
                'challan_per_io.good_qty',
                'challan_per_io.rate',
                'challan_per_io.good_desc',
                'challan_per_io.uom_id',
                'challan_per_io.packing_details',
                'challan_per_io.delivery_challan_date',
                'hsn.gst_rate'
                )
            ->get();
            // $max_qty=0;
            $reference_name = Reference::all();
            $vehicle = Vehicle::all();
            $ref_name = Party::where('id',$Delivery_challan['party_id'])->first('reference_name')['reference_name'];
            $party = Party::where('reference_name',$ref_name)->get();
            $consignee = Consignee::where('party_id','=',$Delivery_challan['party_id'])->get();
            $uom = Unit_of_measurement::all();
            
            $client_id = Party::where('reference_name',$ref_name)->select('id')->get()->toArray();
            $io = InternalOrder::where('reference_name',$Delivery_challan['reference_name'])->get();

            $self=Goods_Dispatch::where('mode','=','2')->get();
            $trans=Goods_Dispatch::where('mode','=','1')->get();
            $courier=Goods_Dispatch::where('mode','=','3')->get();
            $count=count($Challan_per_io);
            $all_io=[];
            $qty=0;
            for($i=0;$i<$count;$i++){
                $val=$Challan_per_io[$i]->io;
                $internal=InternalOrder::where('internal_order.id','=',$val)
                ->leftJoin('job_details','job_details.id','=','internal_order.job_details_id')
                ->get('job_details.qty')->first();
                $all_io=Challan_per_io::where('challan_per_io.io','=',$val)->get('good_qty');
                foreach($all_io as $key){
                    $qty=$qty+$key['good_qty'];
                }
             
                $max_qty[$val]=$internal['qty']-$qty;
                $qty=0;
            }
            if(!isset($max_qty)){
                $max_qty[0]=0;
            }
            $data=array(
                'Delivery_challan'=>$Delivery_challan,
                'ref_name'=>$ref_name,
                'challan_per_io'=>$Challan_per_io,
                'consignee'=>$consignee,
                'reference_name'=>$reference_name,
                'party'=>$party,
                'self'=>$self,
                'trans'=>$trans,
                'vehicle'=>$vehicle,
                'courier'=>$courier,
                'uom'=>$uom,
                'io'=>$io,
                'max_qty'=>$max_qty ,
                'layout' => 'layouts.main' 
            );
            // return $Challan_per_io;
        return view('sections.delivery_challan_update', $data);   
        }
        public function get_party_by_reference($name)
        {
           
            $party= Party::where('reference_name',$name)->get(['id','partyname']);
            // print_r($party);die;
            return array('party_list' => $party);
        }

        public function delivery_challan()
        {
            $reference_name =Reference::all();
            $self=Goods_Dispatch::where('mode','=','2')->get();
            $trans=Goods_Dispatch::where('mode','=','1')->get();
            $courier=Goods_Dispatch::where('mode','=','3')->get();
            $settings = Settings::where('name','Delivery_Challan_Prefix')->first();
            $uom = Unit_of_measurement::all();
            $vehicle=Vehicle::all();
            $data=array(
                    // 'party'=>$party,
                    'reference'=>$reference_name,
                    'self'=>$self,
                    'trans'=>$trans,
                    'vehicle'=>$vehicle,
                    'courier'=>$courier,
                    'uom'=>$uom,
                    'settings'=>$settings->value,
                    'layout' => 'layouts.main' 
            );
            return view('sections.delivery_challan', $data);
    }

    public function delivery_insert(Request $request)
    {
        // $old_dc=Delivery_challan::where('challan_number_status','New')->get('challan_number')->last();
       
        try {
            $valarr = [
                'reference'=>'required|string|exists:party,reference_name',
                'party' => 'required|numeric|exists:party,id',
                'consignee_name' => 'required|numeric|exists:consignee,id',
                'io_id.*' => 'required',
                'delivery_challan_date.*' => 'required|date',
                'uom.*' => 'required',
                'goods_qty.*' => 'required|numeric',
                'goods_des.*' => 'required',
                'pak_details.*' => 'required',
                // 'del_date' => 'required',
                
                'goods_dispatch' => 'required',
                'challan_number_status' => 'required'        
            ];
            $nextdate = new DateTime('tomorrow');
            $nextdate=$nextdate->format('Y-m-d');
            $nextdate=strtotime($nextdate);
            $del=Delivery_challan::orderBy('delivery_date', 'DESC')->get('delivery_date')->first();
            if($del)
            {
                $del = $del->toArray();
                $del_date=$del['delivery_date'];
                $del_date=strtotime($del_date);
            }else
            {
                $del_date=strtotime(date("Y-m-d"));
            }
            $nowdate=new DateTime('today');
            $nowdate=$nowdate->format('Y-m-d');
            $nowdate=strtotime($nowdate);
            $vehicle_no1=0;
            if($request->input('goods_dispatch')==2)
            {
                $valarr1=['self_id'=>'required',
                          'vehicle_no_self'=>'required'
                        ];
                $dispatch_id=implode(',',$request->input('self_id'));
                $bilty_docket = $request->input('bilty_docket');
                $bilty_date = $request->input('bilty_date');
                $vehicle_no1 = $request->input('vehicle_no_self');
             
                $valarr = array_merge($valarr,$valarr1);   

                $self_id=Goods_Dispatch::whereIN('id',$request->input('self_id'))->select(DB::raw('group_concat(courier_name) as courier_name'))->get()->first();
                $self_id = isset($self_id->courier_name)?$self_id->courier_name:'';
                $changes_array['self_id'] = $self_id;

                $vehicle_no_self=Vehicle::where('id',$request->input('vehicle_no_self'))->select('vehicle_number')->get()->first();
                $vehicle_no_self = isset($vehicle_no_self->vehicle_number)?$vehicle_no_self->vehicle_number:'';
                $changes_array['vehicle_no_self'] = $vehicle_no_self;
            }
            else if($request->input('goods_dispatch')==3)
            {
                $valarr1=[
                    'goods_dispatch_id'=>'required',
                    'bilty_docket'=>'required',
                    'bilty_date'=>'required',
                ];
                $dispatch_id=$request->input('goods_dispatch_id');
            
                $bilty_docket = $request->input('bilty_docket');
                $bilty_date = $request->input('bilty_date');
                $valarr = array_merge($valarr,$valarr1);   

                $goods_dispatch_id=Goods_Dispatch::where('id',$request->input('goods_dispatch_id'))->select('courier_name')->get()->first();
                $goods_dispatch_id = isset($goods_dispatch_id->courier_name)?$goods_dispatch_id->courier_name:'';
                $changes_array['goods_dispatch_id'] = $goods_dispatch_id;

            }
            else if($request->input('goods_dispatch')==1)
            {
                $valarr1=[
                    'goods_dispatch_id1'=>'required',
                    'bilty_docket1'=>'required',
                    'bilty_date1'=>'required|date',
                ];
                $dispatch_id=$request->input('goods_dispatch_id1');
                $bilty_date = $request->input('bilty_date1');
                $bilty_docket = $request->input('bilty_docket1');
                $valarr = array_merge($valarr,$valarr1);        

                $goods_dispatch_id1=Goods_Dispatch::where('id',$request->input('goods_dispatch_id1'))->select('courier_name')->get()->first();
                $goods_dispatch_id1 = isset($goods_dispatch_id1->courier_name)?$goods_dispatch_id1->courier_name:'';
                $changes_array['goods_dispatch_id1'] = $goods_dispatch_id1;

                
            }
            
           
            $value=0;
            $party=$request->input('party');
            $io_id=$request->input('io_id');
            $consignee_name=$request->input('consignee_name');

            $pak_details=$request->input('pak_details');
            $goods_des=$request->input('goods_des');
            $goods_qty=$request->input('goods_qty');
            $delivery_date = $request->input('delivery_date');
            $posted_date = $request->input('challan_number_status')=="New" ? date('Y-m-d',strtotime($request->input('del_date_new'))) : date('Y-m-d',strtotime($request->input('del_date_old')));
            $uom = $request->input('uom');
            $rate_per_qty=$request->input('rate_per_qty');
            $gst_rate=$request->input('gst_rate');

            $bilty_date = $request->input('bilty_date');
            $timestamp = date('Y-m-d G:i:s');
            $reference = $request->input('reference'); 
            $settings = Settings::where('name','Delivery_Challan_Prefix')->first();
           
            $fromDate=$request->input('challan_number_status')=="New" ? date('Y-m-d',strtotime($request->input('del_date_new'))) : date('Y-m-d',strtotime($request->input('del_date_old')));

            
             $fin_year=CustomHelpers::getFinancialFromDate($fromDate);


             if($fin_year){
                $financial_year = $year=$fin_year;
             }
            else{
                return redirect('/deliverychallan')->with('error','Enter Document Date According to Financial Year.')->withInput();
            }
           
           
            if($request->input('challan_number_status')=='Old'){

                $delivery_number = $request->input('old_dc');

                if(strpos($delivery_number, $financial_year) !== false){
                    $prefix = $delivery_number;
                }
                else{
                    return redirect('/deliverychallan')->with('error','Enter Old  Series According to Delivery Challan Date Financial Year.')->withInput();
                }

                $prefix = $delivery_number;
                $ar=[
                  
                    'old_dc'=>'required|unique:delivery_challan,challan_number',
                    'del_date_old'=>'required',
                ];
                $valarr = array_merge($valarr,$ar);  
            }
            else if($request->input('challan_number_status')=='New'){
            
                $old_dc=Delivery_challan::where('challan_number_status','New')
                ->where('financial_year',$year)->get('challan_number')->last();
                if($old_dc){
                    $dc=explode('/',$old_dc['challan_number']);
                    $v = (int)$dc[count($dc)-1];
                    if($v<1){
                        $v=3257;
                    }
                    $dc_id=$v+1;
                }
                else{
                    $dc_id=1;
                }
               
                $delivery_number = $settings->value;
                $delivery_number = $delivery_number ."/".$year.'/'.$dc_id;
                $prefix = $delivery_number;
                $ar=[
                    'del_date_new'=>'required',
                ];
                $valarr = array_merge($valarr,$ar);  
            }
            else{
                 $delivery_number = $settings->value;
                 $prefix = $delivery_number;
            }
            $this->validate($request,$valarr);
            if(count($io_id) !== count(array_unique($io_id))){
                return redirect('/deliverychallan')->with('error','No Two Or More Same IO Clubbed In One DC.');
            }
            $insert_array = [
                    'id' => NULL,
                    'reference_name' => $reference,
                    'challan_number'=>$delivery_number,
                    'challan_number_status'=>$request->input('challan_number_status'),
                    'financial_year'=>$request->input('challan_number_status') =='New' ? $year : $financial_year,
                    'party_id' => $party,
                    'consignee_id' => $consignee_name,
                    'delivery_date' => $posted_date,
                    'total_amount'=>0,
                    'dispatch' =>$request->input('goods_dispatch'),
                    'dispatch_id' =>$dispatch_id,
                    'bilty_docket' =>$bilty_docket,
                    'docket_date' =>$bilty_date,
                    'created_by' =>Auth::id(),
                    'is_active'=>1,
                    'vehicle_id'=>$vehicle_no1,
                    'date' =>date('Y-m-d'),
                    'created_time' =>$timestamp
                ];
            $docket_date="";
            if($bilty_date)
            {
                $docket_date = date("Y-m-d", strtotime($bilty_date));
                $insert_array['docket_date'] = $docket_date;
            }
            $posteddate=strtotime($posted_date);
            // if(($del_date<=$nowdate && $posteddate<=$nextdate) || ($del_date>=$nextdate && $posteddate==$nextdate) || $request->input('challan_number_status')=='Old')
            // {
                $delivery=Delivery_challan::insertGetId($insert_array);
                $count=count($request->input('io_id'));
                $total_amount=0;
                for($i=0;$i<$count;$i++){
                    $amount=($rate_per_qty[$i] * $goods_qty[$i]);
                    $gst=($amount*$gst_rate[$i])/100;
                    $total=$amount+$gst;
                    $total_amount=$total_amount+$total;
                    Challan_per_io::insert(
                        [
                            'id' => NULL,
                            'io' =>$io_id[$i],
                            'delivery_challan_id' => $delivery,
                            'delivery_challan_date' => date("Y-m-d",strtotime($delivery_date[$i])),
                            'uom_id' => $uom[$i],
                            'rate'=>$rate_per_qty[$i],
                            'good_desc' => $goods_des[$i],
                            'good_qty' => $goods_qty[$i],
                            'packing_details' => $pak_details[$i],
                            'amount'=>$total
                        ]
                    );
                    $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                    ->where('internal_order.id','=',$io_id[$i])->get(['job_details.id','job_details.left_qty','qty'])->first();
                    $chal=Challan_per_io::where('io',$io_id[$i])->select(DB::raw('IFNULL(SUM(good_qty),0) as good_qty'))->get()->first();
                    $lqty = $jd_id->qty-$chal['good_qty'];
                    JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>$lqty]);
                }
                    $number= Delivery_challan::where('id',$delivery)->update(
                        [
                            'total_amount'=>$total_amount
                        ]
                    );
                     /*** USER LOG ***/
             $reference=Reference::where('id',$request->input('reference'))->select('referencename')->get()->first();
             $reference = isset($reference->referencename)?$reference->referencename:'';
             $changes_array['reference'] = $reference;

             $party=Party::where('id',$request->input('party'))->select('partyname')->get()->first();
             $party = isset($party->partyname)?$party->partyname:'';
             $changes_array['party'] = $party;
 
              $consignee_name=Consignee::where('id',$request->input('consignee_name'))->select('consignee_name')->get()->first();
             $consignee_name = isset($consignee_name->consignee_name)?$consignee_name->consignee_name:'';
             $changes_array['consignee_name'] = $consignee_name;

            //  $changes_array['delivery_date'] = $posted_date;
        
             $io_id = InternalOrder::whereIN('id',$request->input('io_id'))->select(DB::raw('group_concat(io_number) as io_number'))->get()->first();
             $io_id = isset($io_id->io_number)?$io_id->io_number:'';
             $changes_array['io_id'] = $io_id;

             $uom=Unit_of_measurement::whereIN('id',$request->input('uom'))->select(DB::raw('group_concat(uom_name) as uom_name'))->get()->first();
             $uom = isset($uom->uom_name)?$uom->uom_name:'';
             $changes_array['uom'] = $uom;

             $goods_dispatch=Dispatch_mode::where('id',$request->input('goods_dispatch'))->select('name')->get()->first();
             $goods_dispatch = isset($goods_dispatch->name)?$goods_dispatch->name:'';
             $changes_array['goods_dispatch'] = $goods_dispatch;
             $log_array=array(
                
                'reference'=>'Reference Name',
                'party'=>'Party',
                'consignee_name'=>'Consignee Name',
                'delivery_date'=>'Delivery Date',
                'io'=>'Internal Order',
                'uom'=>'Qty Unit',

                'goods_qty'=>'Qty',
                'goods_des'=>'Goods Description',
                
                'pak_details'=>'Packing Details',
                'goods_dispatch'=>'Goods Dispatch Mode',
                'goods_dispatch_id1'=>'Company Name',
                'goods_dispatch_id'=>'Company Name',
                'self_id'=>'Carrier Name',
                'bilty_docket1'=>'Bilty Docket',
                'bilty_docket'=>'Bilty Docket',
                'bilty_date1'=>'Bilty Date',
                'bilty_date'=>'Bilty Date'
         
             );
             CustomHelpers::userActionLog('Delivery Challan Created',$delivery,'Delivery Challan Created',
             $log_array,$changes_array,$removekeys=array('gst_rate'));
            /***  END USER LOG ***/
                $partyid=$request->input('party');
                $party=Party::where('id',$partyid)->get('party.*')->first();
               
                $getAmount=Delivery_challan::where('delivery_challan.party_id',$partyid)
                ->leftJoin('party','delivery_challan.party_id','=','party.id')
                ->where('party.gst',$party['gst'])
                ->where('delivery_challan.waybill_status','<>',2)
                ->whereDate('delivery_date', $posted_date)
                ->get([
                    'party.gst',
                    'delivery_challan.id',
                    'delivery_challan.total_amount',

                ]);
                
                $Todatdate=date('Y-m-d');
                $reference = $request->input('reference'); 
                if($getAmount){
                    $waybill = Settings::where('name','delivery_amount')->first();
                    $waybill_value = $waybill->value;
                    $total_ByDate=0;
                    foreach($getAmount as $key){
                        $total_ByDate=$total_ByDate+$key['total_amount'];
                        $delivery_id[]=$key['id'];
                    }
                    if($total_ByDate>=$waybill_value)
                    {
                        foreach($getAmount as $key){
                            $way_id=$key['id'];
                            Delivery_challan::where('id',$way_id)->update(
                                [
                                    'waybill_status'=> 1,
                                    
                                ]
                            );
                        }
                        $amntDate=$total_ByDate;
                        $mesg="Today's Total Amount of Delivery has been exceeded the limit ".$waybill_value. ".Create WayBill..";
                    }
                    else{
                        $amntDate=$total_ByDate;
                    $mesg=NULL;
                    }
               }
               else{
                $amntDate=NULL;
                $mesg=NULL;
               }
               $del_id=implode(':',$delivery_id);
           
            return redirect('/deliverychallan')->with(['message'=>'delivery','delivery'=>$delivery,'delivery_prefix'=>$prefix,'amntDate'=>$amntDate,'delivery_id'=>$del_id,'mesg'=>$mesg,'gst'=>$party['gst'],'date'=>$Todatdate,'refer'=>$reference,'pointer'=>$party['gst_pointer']]);
          
            }
            catch(\Illuminate\Database\QueryException $ex) {
              return redirect('/deliverychallan')->with('error','some error occurred'.$ex->getMessage());
      
            }
    }

  
    public function party_delivery($id,$d,$inv_id){
        $party=Delivery_challan::where('delivery_challan.party_id',$id)
            ->leftJoin('party','delivery_challan.party_id','=','party.id')
            ->leftJoin('consignee','delivery_challan.consignee_id','=','consignee.id')
            ->leftJoin('tax','delivery_challan.id','=','tax.delivery_challan_id')
            ->leftJoin('payment_term','payment_term.id','=','party.payment_term_id')
            ;
        $consignee=Consignee::where('party_id',$id)->select('id','consignee_name')->get();
        $refer=Party::where('id',$id)->select('reference_name')->get()->first();
        $io=InternalOrder::where('reference_name',$refer['reference_name'])
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id');

        if($d=='update')
           { 
               $party = $party->where('tax.id','=',NULL)->orwhere('tax.tax_invoice_id','=',$inv_id)->orwhere('tax.is_cancelled',1);
               $io=$io->orwhere('job_details.left_qty','!=',0)->where('job_details.io_type_id',8);
            }
        else if($d=="ins")
           { 
               $party = $party->where('tax.id',NULL)->orwhere('tax.is_cancelled',1);
                $io=$io->where('job_details.left_qty','!=',0)->where('job_details.io_type_id','=',8);
            }
      
           
        $party = $party->select(
            DB::raw('group_concat(DISTINCT(delivery_challan.id)) as id'),
            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number')     ,
            DB::raw('group_concat(DISTINCT(delivery_challan.party_id)) as party_id')  
            
        )->where('delivery_challan.party_id',$id)->groupBy('delivery_challan.id')->get(); 

        $io=$io->select('internal_order.id','internal_order.io_number','job_details.left_qty','job_details.io_type_id')->get();
        // return $io;
        return $data=array('party'=>$party,'io'=>$io,'consignee'=>$consignee);
    }
    
    public function delivery_io_details($id,$type,$tax){
        if($type=='ins')
        {
            $details=InternalOrder::where('internal_order.id',$id)
                ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
                ->leftJoin('unit_of_measurement as io_uom','job_details.unit','=','io_uom.id')
                ->leftJoin('hsn as io_hsn','job_details.hsn_code','=','io_hsn.id')
                ->leftJoin('client_po','internal_order.id','=','client_po.io')
                ->leftJoin('client_po_party','client_po.id','=','client_po_party.client_po_id')
                ->leftJoin('payment_term as client_po_payment','client_po_payment.id','=','client_po_party.payment_terms')
                ->leftjoin('hsn as po_hsn','client_po.hsn','=','po_hsn.id')
                ->leftJoin('unit_of_measurement as po_uom','client_po.unit_of_measure','=','po_uom.id')
                    ->select(
                        'internal_order.io_number as ionumber',
                        'internal_order.id',
                        'job_details.hsn_code as io_hsn',
                        'job_details.rate_per_qty',
                        'job_details.qty as qty',
                        'job_details.io_type_id as io_type',
                        'job_details.transportation_charge',
                        'job_details.other_charge',
                        'job_details.remarks as good_desc',
                        'job_details.left_qty as good_qty',
                        'job_details.left_qty',
                        'io_hsn.item_id',
                        DB::raw('group_concat(DISTINCT(client_po_party.party_name)) as party_name'),
                        DB::raw('group_concat(DISTINCT(client_po_party.payment_terms)) as party_pay_terms'),
                        DB::raw('group_concat(client_po_payment.value) as payment_po'),
                        'client_po.is_po_provided',
                        'client_po.qty as po_qty',
                        // 'client_po.payment_terms as client_payment_id',
                        'client_po.po_number',
                        'client_po.item_desc',
                        'client_po.per_unit_price',
                        'client_po.hsn as po_hsn_id',
                        'client_po.discount',
                        'client_po.discount as po_discount',
                        // 'client_po_payment.value as client_po_payment',
                        'client_po.unit_of_measure as po_uom_id',
                        'job_details.unit as io_uom_id',
                        'po_uom.uom_name as po_uom_name',
                        'io_uom.uom_name as io_uom_name',
                        'io_hsn.hsn as io_hsn_name',
                        'io_hsn.item_id as io_item_category_name',
                        'io_hsn.gst_rate as io_gst_rate',
                        'po_hsn.hsn as po_hsn_name',
                        'po_hsn.gst_rate as po_gst_rate'
                        
            )->groupBy(
                'job_details.io_type_id',
                        'internal_order.io_number',
                        'job_details.remarks',
                        'internal_order.id',
                        'job_details.hsn_code',
                        'io_hsn.item_id',
                        'job_details.rate_per_qty',
                        'job_details.qty',
                        'job_details.transportation_charge',
                        'job_details.other_charge',
                        'job_details.left_qty',
                        'client_po.is_po_provided',
                        'client_po.qty',
                        // 'client_po.payment_terms as client_payment_id',
                        'client_po.po_number',
                        'client_po.item_desc',
                        'client_po.per_unit_price',
                        'client_po.hsn',
                        'client_po.discount',
                        'client_po.discount',
                        // 'client_po_payment.value as client_po_payment',
                        'client_po.unit_of_measure',
                        'job_details.unit',
                        'po_uom.uom_name',
                        'io_uom.uom_name',
                        'io_hsn.hsn',
                        'io_hsn.item_id',
                        'io_hsn.gst_rate',
                        'po_hsn.hsn',
                        'po_hsn.gst_rate'
            )->get();
        }
        else if($type=='update')
        {
            $details=Tax::where('tax.tax_invoice_id',$tax)
            ->where('tax.io_id',$id)
                ->leftJoin('payment_term','payment_term.id','=','tax.payment')
                ->leftJoin('internal_order','tax.io_id','=','internal_order.id')
                ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
                ->leftJoin('unit_of_measurement as io_uom','tax.per','=','io_uom.id')
                ->leftJoin('hsn as io_hsn','tax.hsn','=','io_hsn.id')
                ->leftJoin('client_po','tax.io_id','=','client_po.io')
                ->leftJoin('client_po_party','client_po.id','=','client_po_party.client_po_id')
                ->leftJoin('payment_term as client_po_payment','client_po_payment.id','=','client_po_party.payment_terms')
                ->leftjoin('hsn as po_hsn','tax.hsn','=','po_hsn.id')
                ->leftJoin('unit_of_measurement as po_uom','client_po.unit_of_measure','=','po_uom.id')
                ->select(

                    'payment_term.value as party_payment_term',
                    
                    'internal_order.io_number as ionumber',
                    
                    'job_details.left_qty',

                    'tax.payment as party_payment_id',
                    'tax.io_id as id',
                    'tax.goods as good_desc',
                    'tax.qty as good_qty',
                    'tax.hsn as io_hsn',
                    'tax.rate as rate_per_qty',
                    'tax.transport_charges as transportation_charge',
                    'tax.other_charges as other_charge',
                    'tax.per as io_uom_id',
                    'tax.discount',
                    'client_po.is_po_provided',
                    'client_po.qty as po_qty',
                    'client_po.po_number',
                    'client_po.item_desc',
                    'client_po.per_unit_price',
                    'client_po.hsn as po_hsn_id',
                    'client_po.discount as po_discount',
                    DB::raw('group_concat(DISTINCT(client_po_party.party_name)) as party_name'),
                    DB::raw('group_concat(DISTINCT(client_po_party.payment_terms)) as party_pay_terms'),
                    DB::raw('group_concat(client_po_payment.value) as payment_po'),
                    'po_uom.uom_name as po_uom_name',
                    'io_uom.uom_name as io_uom_name',
                    'io_hsn.hsn as io_hsn_name',
                    'io_hsn.gst_rate as io_gst_rate',
                    'io_hsn.item_id',
                    'po_hsn.hsn as po_hsn_name',
                    'po_hsn.gst_rate as po_gst_rate'
                    
                )->groupBy(
                    'payment_term.value',
                    'internal_order.io_number',
                    'job_details.left_qty',
                    'tax.payment',
                    'tax.io_id',
                    'tax.goods',
                    'tax.qty',
                    'tax.hsn',
                    'tax.rate',
                    'tax.transport_charges',
                    'tax.other_charges',
                    'tax.per',
                    'tax.discount',
                    'io_hsn.item_id',
                    'client_po.is_po_provided',
                    'client_po.qty',
                    'client_po.po_number',
                    'client_po.item_desc',
                    'client_po.per_unit_price',
                    'client_po.hsn',
                    'client_po.discount',
                    'po_uom.uom_name',
                    'io_uom.uom_name',
                    'io_hsn.hsn',
                    'io_hsn.gst_rate',
                    'po_hsn.hsn',
                    'po_hsn.gst_rate'
                )->get();
        }
        return $details;
    }
    public function delivery_details($id,$type){
        if($type=='ins')
        {
            $details=Delivery_challan::where('delivery_challan.id',$id)
                ->leftjoin('challan_per_io','delivery_challan.id','=','challan_per_io.delivery_challan_id')
                ->leftJoin('party','delivery_challan.party_id','=','party.id')
                ->leftJoin('payment_term','payment_term.id','=','party.payment_term_id')
                ->leftJoin('consignee','delivery_challan.consignee_id','=','consignee.id')
                ->leftJoin('internal_order','challan_per_io.io','=','internal_order.id')
                ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
                ->leftJoin('unit_of_measurement as io_uom','job_details.unit','=','io_uom.id')
                ->leftJoin('hsn as io_hsn','job_details.hsn_code','=','io_hsn.id')
                ->leftJoin('client_po','challan_per_io.io','=','client_po.io')
                ->leftJoin('client_po_party','client_po.id','=','client_po_party.client_po_id')
                ->leftJoin('payment_term as client_po_payment','client_po_payment.id','=','client_po_party.payment_terms')
                ->leftjoin('hsn as po_hsn','client_po.hsn','=','po_hsn.id')
                ->leftJoin('unit_of_measurement as po_uom','client_po.unit_of_measure','=','po_uom.id')
                ->where('challan_per_io.io','!=',NULL)
                    ->select(
                        'delivery_challan.id as id',
                        'delivery_challan.party_id',
                        'delivery_challan.challan_number',
                        'payment_term.value as party_payment_term',
                        'party.partyname',
                        'party.payment_term_id as party_payment_id',
                        'consignee.consignee_name',
                        'delivery_challan.consignee_id',
                        'challan_per_io.io',
                        'internal_order.io_number as ionumber',
                        'internal_order.id as io_id',
                        'challan_per_io.good_desc',
                        'challan_per_io.good_qty',
                        'job_details.hsn_code as io_hsn',
                        'job_details.rate_per_qty',
                        'io_hsn.item_id',
                        'job_details.qty as io_qty',
                        'job_details.io_type_id as io_type',
                        'job_details.transportation_charge',
                        'job_details.other_charge',
                        'job_details.left_qty',
                        DB::raw('group_concat(DISTINCT(client_po_party.party_name)) as party_name'),
                        DB::raw('group_concat(client_po_payment.value) as payment_po'),
                        'client_po.is_po_provided',
                        'client_po.qty as po_qty',
                        // 'client_po.payment_terms as client_payment_id',
                        'client_po.po_number',
                        'client_po.item_desc',
                        'client_po.per_unit_price',
                        'client_po.hsn as po_hsn_id',
                        'client_po.discount',
                        'client_po.discount as po_discount',
                        // 'client_po_payment.value as client_po_payment',
                        'client_po.unit_of_measure as po_uom_id',
                        'job_details.unit as io_uom_id',
                        'po_uom.uom_name as po_uom_name',
                        'io_uom.uom_name as io_uom_name',
                        'io_hsn.hsn as io_hsn_name',
                        'io_hsn.gst_rate as io_gst_rate',
                        'po_hsn.hsn as po_hsn_name',
                        'po_hsn.gst_rate as po_gst_rate'
                        
            )->groupBy(
                '.job_details.io_type_id',
                'delivery_challan.id',
                'delivery_challan.party_id',
                        'delivery_challan.challan_number',
                        'payment_term.value',
                        'party.partyname',
                        'party.payment_term_id',
                        'consignee.consignee_name',
                        'delivery_challan.consignee_id',
                        'challan_per_io.io',
                        'internal_order.io_number',
                        'internal_order.id',
                        'challan_per_io.good_desc',
                        'challan_per_io.good_qty',
                        'job_details.hsn_code',
                        'job_details.rate_per_qty',
                        'job_details.qty',
                        'job_details.transportation_charge',
                        'job_details.other_charge',
                        'job_details.left_qty',
                        'client_po.is_po_provided',
                        'client_po.qty',
                        // 'client_po.payment_terms as client_payment_id',
                        'client_po.po_number',
                        'client_po.item_desc',
                        'client_po.per_unit_price',
                        'client_po.hsn',
                        'io_hsn.item_id',
                        'client_po.discount',
                        'client_po.discount',
                        // 'client_po_payment.value as client_po_payment',
                        'client_po.unit_of_measure',
                        'job_details.unit',
                        'po_uom.uom_name',
                        'io_uom.uom_name',
                        'io_hsn.hsn',
                        'io_hsn.gst_rate',
                        'po_hsn.hsn',
                        'po_hsn.gst_rate'
            )->get();
        }
        else if($type=='update')
        {
            $details=Tax::where('tax.tax_invoice_id',$id)
                ->rightjoin('delivery_challan','delivery_challan.id','=','tax.delivery_challan_id')
                ->leftJoin('payment_term','payment_term.id','=','tax.payment')
                ->leftJoin('internal_order','tax.io_id','=','internal_order.id')
                ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
                ->leftJoin('unit_of_measurement as io_uom','tax.per','=','io_uom.id')
                ->leftJoin('hsn as io_hsn','tax.hsn','=','io_hsn.id')
                ->leftJoin('client_po','tax.io_id','=','client_po.io')
                ->leftJoin('client_po_party','client_po.id','=','client_po_party.client_po_id')
                ->leftJoin('payment_term as client_po_payment','client_po_payment.id','=','client_po_party.payment_terms')
                ->leftjoin('hsn as po_hsn','tax.hsn','=','po_hsn.id')
                ->leftJoin('unit_of_measurement as po_uom','client_po.unit_of_measure','=','po_uom.id')
                ->select(
                    'delivery_challan.id as id',
                    'delivery_challan.challan_number',
                    
                    'payment_term.value as party_payment_term',
                    
                    'internal_order.io_number as ionumber',
                    
                    'job_details.left_qty',

                    'tax.payment as party_payment_id',
                    'tax.io_id as io_id',
                    'tax.goods as good_desc',
                    'tax.qty as good_qty',
                    'tax.hsn as io_hsn',
                    'tax.rate as rate_per_qty',
                    'tax.transport_charges as transportation_charge',
                    'tax.other_charges as other_charge',
                    'tax.per as io_uom_id',
                    'tax.discount',
                    'client_po.is_po_provided',
                    'client_po.qty as po_qty',
                    'client_po.po_number',
                    'client_po.item_desc',
                    'client_po.per_unit_price',
                    'client_po.hsn as po_hsn_id',
                    'client_po.discount as po_discount',
                    DB::raw('group_concat(DISTINCT(client_po_party.party_name)) as party_name'),
                    DB::raw('group_concat(DISTINCT(client_po_party.payment_terms)) as party_pay_terms'),
                    DB::raw('group_concat(client_po_payment.value) as payment_po'),
                    'po_uom.uom_name as po_uom_name',
                    'io_uom.uom_name as io_uom_name',
                    'io_hsn.hsn as io_hsn_name',
                    'io_hsn.gst_rate as io_gst_rate',
                    'po_hsn.hsn as po_hsn_name',
                    'io_hsn.item_id',
                    'po_hsn.gst_rate as po_gst_rate'
                    
                )->groupBy(
                    'delivery_challan.id',
                    'delivery_challan.challan_number',
                    'payment_term.value',
                    'internal_order.io_number',
                    'job_details.left_qty',
                    'tax.payment',
                    'tax.io_id',
                    'tax.goods',
                    'tax.qty',
                    'tax.hsn',
                    'tax.rate',
                    'tax.transport_charges',
                    'tax.other_charges',
                    'tax.per',
                    'tax.discount',
                    'client_po.is_po_provided',
                    'client_po.qty',
                    'client_po.po_number',
                    'client_po.item_desc',
                    'client_po.per_unit_price',
                    'client_po.hsn',
                    'client_po.discount',
                    'po_uom.uom_name',
                    'io_uom.uom_name',
                    'io_hsn.hsn',
                    'io_hsn.item_id',
                    'io_hsn.gst_rate',
                    'po_hsn.hsn',
                    'po_hsn.gst_rate'
                )->get();
        }
        return $details;
    }
    public function tax_invoice_dispatch_update($id){
        $tax=Tax_Dispatch::where('tax_dispatch.is_active',1)->where('tax_dispatch.id',$id)->rightJoin('tax_invoice','tax_dispatch.tax_invoice_id','=','tax_invoice.id')
        ->select(
            'tax_dispatch.id as id',
            'tax_dispatch.dispatch_date',
            'tax_dispatch.dispatch_mode',
            'tax_dispatch.courier_company',
            'tax_dispatch.docket_number',
            DB::raw('DATE_FORMAT(tax_dispatch.docket_date,"%d-%m-%Y") as docket_date'),
            'docket_file',
            'tax_dispatch.person',
            'tax_dispatch.byhand_invoice',
            'tax_dispatch.party_invoice',
            'tax_dispatch.created_time',
            'tax_invoice.invoice_number',
            DB::raw('DATE_FORMAT(tax_invoice.created_at,"%d-%m-%Y") as created_date')
        )->get()->first();
        // print_r($tax);die();
        $file_type=explode('.',$tax['docket_file']);
        $file_type = $file_type[count($file_type)-1];
        $emp=EmployeeProfile::all();
        $goods=Goods_Dispatch::where('mode',3)->select('id','courier_name')->get();
         $data=array(
                 'emp'=>$emp,
                 'goods'=>$goods,
                'tax'=>$tax,
                'file_type'=>$file_type,
                'layout' => 'layouts.main' 
        );
        // return $file_type;
        return view('sections.tax_dispatch_update', $data);
    }
        public function tax_dispatch_update(Request $request,$id){
            try {
                $validator = Validator::make($request->all(),
                [
                    
                    'list_available' => 'required',
                    'date' => 'required',
                ],
                [
                    
                    'list_available.required' => 'This field is required',
                    'date.required' => 'This field is required',
                ]
                );
                CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Tax Invoice Dispatch Update");
                $errors = $validator->errors();
                if ($validator->fails()) 
                {
                    return redirect('/taxinvoicedispatch/update/'.$id)
                                ->withErrors($errors);
                }
                $timestamp = date('Y-m-d G:i:s');
                $date = strtotime($request->input('date'));
                $docket_date = date("Y-m-d", $date);
                if($request->hasFile('file'))
                {
                   
                        $file = $request->file('file');
                        $destinationPath = public_path().'/upload/taxdispatch';
                        $filenameWithExt=$file->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
                        $extension = $file->getClientOriginalExtension();    
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;   
                        $resume=$fileNameToStore;
                        $file->move($destinationPath,$fileNameToStore);
                }
                else{
                    $resume=$request->input('files');
                }
            
                $byhand_invoice = $request->file('hand_invoice');
                $pickbyparty_invoice = $request->file('pickparty_invoice');

                $hand ='';
                if(isset($byhand_invoice) || $byhand_invoice != null){
                    $destinationPath = public_path().'/upload/taxdispatch';
                    $filenameWithExt = $request->file('hand_invoice')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('hand_invoice')->getClientOriginalExtension();
                    $hand = $filename.'_'.time().'.'.$extension;
                    $path = $byhand_invoice->move($destinationPath, $hand);
                    File::delete($destinationPath.$request->input('old_hand_invoice'));
                    
                }else{
                    $hand = $request->input('old_hand_invoice');
                }
                $party ='';
                if(isset($pickbyparty_invoice) || $pickbyparty_invoice != null){
                    $destinationPath = public_path().'/upload/taxdispatch';
                    $filenameWithExt = $request->file('pickparty_invoice')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('pickparty_invoice')->getClientOriginalExtension();
                    $party = $filename.'_'.time().'.'.$extension;
                    $path = $pickbyparty_invoice->move($destinationPath, $party);
                    File::delete($destinationPath.$request->input('old_party_invoice'));
                }else{
                    $party = $request->input('old_party_invoice');
                }

                    $dispatch=Tax_Dispatch::where('id','=',$id)->update([
                      
                        
                        'dispatch_date' =>$docket_date,
                        'dispatch_mode' =>$request->input('list_available'),
                        'courier_company' =>$request->input('list_available') =="Courier" ? $request->input('com') : '',
                        'docket_number' =>$request->input('list_available') =="Courier" ? $request->input('comp') : '',
                        'docket_date' =>$request->input('list_available') =="Courier" ? date('Y-m-d',strtotime($request->input('doc_date'))) : '',
                        'docket_file' =>$request->input('list_available') =="Courier" ? $resume : '',
                        'person'=>$request->input('list_available') =="Hand" ? $request->input('persname') : '',
                        'byhand_invoice'=>$hand,
                        'party_invoice'=>$party,
                        'updated_at'=> $timestamp,
                    ]);
                /*** USER LOG ***/
                $goods_dispatch_id1=Goods_Dispatch::where('id',$request->input('goods_dispatch_id1'))->select('courier_name')->get()->first();
                $goods_dispatch_id1 = isset($goods_dispatch_id1->courier_name)?$goods_dispatch_id1->courier_name:'';
                        
                $person=EmployeeProfile::where('id',$request->input('persname'))->select('name')->get()->first();
                $person = isset($person->name)?$person->name:'';

                if($request->input('comp')){
                    $docket=$request->input('comp');
                   }
                   else{
                       $docket='';
                   }
                   $log_array=array(
                     
                      'Dispatch Date'=>$request->input('date'),
                      'Dispatch Mode'=>$request->input('list_available'),
                      'Courier Company'=>$goods_dispatch_id1,
                      'Docket Number'=>$docket,
                      'Person'=>$person,
                   );
      
                   CustomHelpers::userLog('Tax Invoice Dispatch Updated',$id,'Tax Invoice Dispatch Updated',
                   $log_array);
 
             
                return redirect('/taxinvoicedispatch/update/'.$id)->with('success','Tax Invoice Dispatch updated successfully.');
            } catch (\Illuminate\Database\QueryException $ex) {
                return redirect('/taxinvoicedispatch/update/'.$id)->with('error',$ex->getMessage());
            }
        }
        public function tax_invoice_dispatch_view($id)
        {
            $po=Tax_Invoice::where('tax_invoice_id',$id)
            ->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
            ->leftJoin('internal_order','internal_order.id','=','tax.io_id')
            ->leftJoin('party', 'party.id','=', 'tax_invoice.party_id')
            ->leftJoin('client_po','tax.io_id','=','client_po.io')
            ->leftJoin('client_po_data','client_po_data.client_po_id','client_po.id')
            ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
            ->select(DB::raw('group_concat(DISTINCT(io_number)) as io_number'),
            DB::raw('group_concat(DISTINCT(IFNULL(client_po_data.po_number,"Yet Not Received!!"))) as po_number'),
            DB::raw('group_concat(DISTINCT(IFNULL(DATE_FORMAT(client_po_data.po_date,"%d-%m-%Y"),"-"))) as po_date'),
            DB::raw('group_concat(DISTINCT(IFNULL(payment_term.value,"-"))) as payment_term')
            
            )->get();
            $tax_detail = Tax_Dispatch::where('tax_dispatch.id',$id)->where('tax_dispatch.is_active','=',1)
            ->leftJoin('tax_invoice','tax_invoice.id','=','tax_dispatch.tax_invoice_id')
            ->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
            ->leftJoin('settings','tax_invoice.gst_type','=','settings.name')
            ->leftJoin('payment_term as tax_pyment','tax.payment','=','tax_pyment.id')
            ->leftJoin('delivery_challan','delivery_challan.id','=','tax.delivery_challan_id')
            ->leftJoin('internal_order','internal_order.id','=','tax.io_id')
            ->leftJoin('hsn','hsn.id','=','tax.hsn')
           
            ->leftJoin('client_po','tax.io_id','=','client_po.io')
            ->leftJoin('party', 'party.id','=', 'tax_invoice.party_id')
            ->leftJoin('consignee', 'consignee.id','=', 'tax_invoice.consignee_id')
            ->leftJoin('countries','party.country_id','=','countries.id')
            ->leftJoin('states','party.state_id','=','states.id')
            ->leftJoin('cities','party.city_id','=','cities.id')
    
            ->leftJoin('countries as con_country','consignee.country','=','con_country.id')
            ->leftJoin('states as con_states','consignee.state','=','con_states.id')
            ->leftJoin('cities as con_cities','consignee.city','=','con_cities.id')
    
            ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
            ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
            ->leftJoin('unit_of_measurement','tax.per','=','unit_of_measurement.id')
            ->leftJoin('employee__profile', function($join) {
                $join->on('tax_dispatch.person', '=', 'employee__profile.id');})
           
            ->get([
                'tax_dispatch.id as id',
                                'tax_dispatch.dispatch_date',
                                'tax_dispatch.dispatch_mode',
                                'tax_dispatch.courier_company',
                                'tax_dispatch.docket_number',
                                'employee__profile.name as person',
                                'tax_dispatch.created_time',
                                'tax_invoice.invoice_number as ch_no',
                'tax_invoice.id as tax_invoice_id',
                'tax_invoice.invoice_number',
                'tax_invoice.terms_of_delivery',
                'tax_invoice.gst_type',
                'tax_invoice.transportation_charge',
                'tax_invoice.other_charge',
                'settings.value as gst_type_rate',
                'tax_invoice.created_at',
                'tax_invoice.party_id as tax_party_id',
                'tax_invoice.consignee_id as tax_consignee_id',
                'tax_invoice.created_at',
                'tax.delivery_challan_id',
                'tax.io_id',
                'tax.goods',
                'tax.qty',
                'tax_pyment.value as tax_payment',
                
                'tax.rate',
                'tax.per',
                'tax.discount',
                'tax.hsn',
                'unit_of_measurement.uom_name',
                'internal_order.id as internalorder_id',
                'internal_order.io_number',
                'item_category.name as item',
                'payment_term.value as payment_term',
                'states.name as state',
                'countries.name as country',
                'cities.city',
    
                'con_states.name as con_states',
                'con_country.name as con_country',
                'con_cities.city as con_cities',
                'party.id as party_id',
                'party.partyname',
                'party.address as party_address',
                'party.pincode',
                'hsn.hsn',
                'hsn.gst_rate as hsn_gst',
                
                'delivery_challan.challan_number',
                'client_po.is_po_provided',
                'client_po.po_number',
                'client_po.po_date',
                'delivery_challan.dispatch',
                'consignee.id as con_id',
                'consignee.consignee_name as con_name',
                'consignee.address as con_address',
                'consignee.pincode as con_pincode'
            ]);
            
            if(count($tax_detail)!==0){
           $data = [
            'foo' => 'bar',
            'po'=>$po,
            'tax_detail'=>$tax_detail,
            'layout'=>'layouts.main'
            ];
            return view('sections.tax_invoice_dispatch_view',$data);
                
            }
            else{
                $message="No Tax Invoice exist!!";
                return redirect('/taxinvoice/list')->with('error',$message);
            }
        }
       
        public function tax_invoice_dispatch_list($mode)
        {
            if($mode=='*')
                $mode='Hand';
            else 
                $mode=$mode;
      
            $data=array('layout'=>'layouts.main','mode'=>$mode);
            return view('sections.tax_invoice_dispatch_summary', $data);
        }
        public function tax_invoice_dispatch_api(Request $request,$mode)
        {
            $search = $request->input('search');
                $serach_value = $search['value'];
                $start = $request->input('start');
                $limit = $request->input('length');
                $offset = empty($start) ? 0 : $start ;
                $limit =  empty($limit) ? 10 : $limit ;
                if($mode=="Hand"){
                    $mode="Hand";
                }
                else if($mode=="Courier"){
                    $mode="Courier";
                }
                else{
                    $mode="Pick By Party";
                }
                $client_po = Tax_Dispatch::leftJoin('tax_invoice', function($join) {
                    $join->on('tax_invoice.id', '=', 'tax_dispatch.tax_invoice_id');})
                    ->leftJoin('party', function($join) {
                        $join->on('tax_invoice.party_id', '=', 'party.id');})
                        ->leftJoin('consignee', function($join) {
                            $join->on('tax_invoice.consignee_id', '=', 'consignee.id');})
                        ->leftJoin('employee__profile', function($join) {
                                $join->on('tax_dispatch.person', '=', 'employee__profile.id');})
                        ->leftJoin('goods_dispatch', function($join) {
                            $join->on('tax_dispatch.courier_company', '=', 'goods_dispatch.id');})
                                       ->where('tax_dispatch.is_active',1)->where('tax_dispatch.dispatch_mode',$mode);

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
            
                DB::raw('(DATE_FORMAT(tax_dispatch.dispatch_date ,"%d-%m-%Y")) as dispatch_date'),
                'tax_dispatch.dispatch_mode',
                'goods_dispatch.courier_name as courier_company',
                'tax_dispatch.docket_number',
                'employee__profile.name as person',
                DB::raw('DATE_FORMAT(tax_dispatch.created_time,"%d-%m-%Y") as created_time'),
                DB::raw('DATE_FORMAT(tax_dispatch.docket_date,"%d-%m-%Y") as docket_date'),
                'tax_invoice.invoice_number as ch_no',
                'tax_invoice.id as tax_id',
                'tax_dispatch.status',
                'tax_dispatch.docket_file',
                        'party.partyname as pname',
                        'tax_dispatch.byhand_invoice',
                        'tax_dispatch.party_invoice',
                        'consignee.consignee_name as cname')->get());
            $client_po = $client_po->offset($offset)->limit($limit);
            if(isset($request->input('order')[0]['column'])){
                $data = ['ch_no','party.partyname','tax_dispatch.docket_file','tax_dispatch.byhand_invoice',
                'tax_dispatch.party_invoice','tax_dispatch.docket_date','consignee.consignee_name','tax_invoice.id as tax_id','tax_dispatch.id','tax_dispatch.dispatch_date','goods_dispatch.courier_name','employee__profile.name as person','tax_dispatch.docket_number','tax_dispatch.created_time'];
                    $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                    $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else
            $client_po->orderBy('tax_id','desc');
            $client_po= $client_po->select(
                'tax_dispatch.id as id',
                DB::raw('(DATE_FORMAT(tax_dispatch.dispatch_date ,"%d-%m-%Y")) as dispatch_date'),
                'tax_dispatch.dispatch_mode',
                'goods_dispatch.courier_name as courier_company',
                'tax_dispatch.docket_number',
                'tax_dispatch.docket_date',
                'employee__profile.name as person',
                DB::raw('(DATE_FORMAT(tax_dispatch.created_time ,"%d-%m-%Y %r")) as created_time'),
                'tax_invoice.invoice_number as ch_no',
                'tax_invoice.id as tax_id',
                'tax_dispatch.docket_file',
                        'party.partyname as pname',
                        'tax_dispatch.status',
                        'tax_dispatch.byhand_invoice',
                        'tax_dispatch.party_invoice',
                        'consignee.consignee_name as cname')->get();

           
            $array['recordsTotal'] = $count;
            $array['recordsFiltered'] = $count;
            $array['data'] = $client_po; 
            return json_encode($array);
        }
        public function tax_dispatch_status(Request $request){
            try {
                 $this->validate($request,
                [
                    'status'=>'required'
                ],
                [
                    'status.required'=>'This field is required'
                ]
                );
                $up_hr=Tax_Dispatch::where('id',$request->input('id'))->update([
                    'status'=>$request->input('status')
                ]);
                if($up_hr==NULL){
                    return redirect('/taxinvoicedispatch/list/*')->with('error','some error occurred')->withInput();
                }else{
                    return redirect('/taxinvoicedispatch/list/*')->with('success','Successfully Tax Dispatch for Courier Mode Status updated.');
                }
            } catch (Exception $e) {
                 return redirect('/taxinvoicedispatch/list/*')->with('error','some error occurred'.$ex->getMessage())->withInput();
            }
        }
        public function taxinvoice_not_dispatch_list()
        {
            $data=array('layout'=>'layouts.main');
            return view('sections.taxinvoice_not_dispatch', $data);
        }
        public function taxinvoice_not_dispatch_api(Request $request)
        {
            $search = $request->input('search');
            $serach_value = $search['value'];
            $start = $request->input('start');
            $limit = $request->input('length');
            $offset = empty($start) ? 0 : $start ;
            $limit =  empty($limit) ? 10 : $limit ;
            $client_po = Tax_Invoice::leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
                            ->leftJoin('party','tax_invoice.party_id', 'party.id')
                            ->leftJoin('tax_dispatch','tax_dispatch.tax_invoice_id', 'tax_invoice.id')
                            ->leftJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                
                            ->leftjoin('internal_order','tax.io_id', 'internal_order.id')
                            ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
                            ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
                            ->where('tax_invoice.is_active',1)->where('tax_dispatch.id','=',NULL)->where('tax_invoice.is_cancelled','=',0);
    
            if(!empty($serach_value))
            {
                $client_po->where(function($query) use ($serach_value){
                    $query->where('party.partyname','LIKE',"%".$serach_value."%")
                    ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
                    ->orwhere('delivery_challan.challan_number','like',"%".$serach_value."%")
                    ->orwhere('tax_invoice.terms_of_delivery','like',"%".$serach_value."%")
                    ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%")
                    ->orwhere('item_category.name','like',"%".$serach_value."%")
                    ->orwhere('internal_order.other_item_name','like',"%".$serach_value."%");
                });                
            }
            $count = count($client_po->select(
                'tax_invoice.invoice_number as ch_no',
                'party.partyname as pname',
                'consignee.consignee_name as cname',
                'tax_invoice.terms_of_delivery',
                'tax_invoice.id',
                DB::raw('SUM(tax.qty) as qty'),
                DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
                'tax_invoice.total_amount','tax_invoice.date','tax_invoice.created_at',
                DB::raw('group_concat(tax.io_id) as io') ,
                DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number')
            )->groupBy('tax_invoice.id')->get());
            $client_po = $client_po->offset($offset)->limit($limit);
            if(isset($request->input('order')[0]['column'])){
                $data = ['ch_no','party.partyname','consignee.consignee_name','qty','item_name','tax_invoice.id','tax_invoice.terms_of_delivery','internal_order.io_number','tax_invoice.total_amount','delivery_challan.challan_number'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else
                $client_po->orderBy('id','desc');
            $client_po= $client_po->select(
                'tax_invoice.invoice_number as ch_no',
                'party.partyname as pname',
                'consignee.consignee_name as cname',
                'tax_invoice.terms_of_delivery',
                'tax_invoice.id',
                'tax_invoice.total_amount',
                DB::raw('DATE_FORMAT(tax_invoice.date,"%d-%m-%Y") as date'),
                DB::raw('SUM(tax.qty) as qty'),
                DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
                DB::raw('DATE_FORMAT(tax_invoice.created_at,"%d-%m-%Y %r") as created'),
                DB::raw('group_concat(tax.io_id) as io') ,
                DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number')
            )->groupBy('tax_invoice.id')->get();
            $array['recordsTotal'] = $count;
            $array['recordsFiltered'] = $count;
            $array['data'] = $client_po; 
            return json_encode($array);
        }
        public function tax_dispatch(){
        //    $tax=Tax_Invoice::where('tax_invoice.is_active',1)->leftJoin('tax_dispatch','tax_dispatch.tax_invoice_id','=','tax_invoice.id')
        //    ->where('tax_dispatch.id','=',NULL)->get(['tax_invoice.id','tax_invoice.invoice_number']);

           $taxs = Tax_Invoice::where('tax_invoice.is_active',1)->leftJoin('tax_dispatch','tax_dispatch.tax_invoice_id','=','tax_invoice.id')
           ->where('tax_dispatch.id','=',NULL)
           ->leftJoin('tax','tax.tax_invoice_id','tax_invoice.id')
           ->leftjoin('internal_order','internal_order.id','tax.io_id')
           ->select(
               DB::raw('group_concat(DISTINCT(tax_invoice.id)) as id'),
               DB::raw('group_concat(DISTINCT(invoice_number)) as invoice_number'),
               DB::raw('group_concat(DISTINCT((internal_order.status))) as st')
       )
       ->groupBy('tax_invoice.id')
       ->get();
       $tax=[];
       foreach($taxs as $item){
           if($item['st']=="Open"){
               $tax[$item['id']]['id']=$item['id'];
               $tax[$item['id']]['invoice_number']=$item['invoice_number'];
               $tax[$item['id']]['st']=$item['st'];
           }
       }
           $emp=EmployeeProfile::all();
           $goods=Goods_Dispatch::where('mode',3)->select('id','courier_name')->get();
            $data=array(
                    'emp'=>$emp,
                    'goods'=>$goods,
                   'tax'=>$tax,
                    'layout' => 'layouts.main' 
            );
            return view('sections.tax_dispatch', $data);
        }
        public function createtax_dispatch(Request $request){
            
            try {
                $validator = Validator::make($request->all(),
                [
                    'num' => 'required',
                    'list_available' => 'required',
                    'date' => 'required',
                ],
                [
                    'num.required' => 'This field is required',
                    'list_available.required' => 'This field is required',
                    'date.required' => 'This field is required',
                ]
                );
                
                $errors = $validator->errors();
                if ($validator->fails()) 
                {
                    return redirect('/taxdispatch')
                                ->withErrors($errors);
                }
                if($request->hasFile('file'))
                {
                   
                        $file = $request->file('file');
                        $destinationPath = public_path().'/upload/taxdispatch';
                        $filenameWithExt=$file->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
                        $extension = $file->getClientOriginalExtension();    
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;   
                        $resume=$fileNameToStore;
                        $file->move($destinationPath,$fileNameToStore);
                }
                else{
                    $resume=NULL;
                }
               
                $byhand_invoice = $request->file('hand_invoice');
                $pickbyparty_invoice = $request->file('pickparty_invoice');

                $hand ='';
                if(isset($byhand_invoice) || $byhand_invoice != null){
                    $destinationPath = public_path().'/upload/taxdispatch';
                    $filenameWithExt = $request->file('hand_invoice')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('hand_invoice')->getClientOriginalExtension();
                    $hand = $filename.'_'.time().'.'.$extension;
                    $path = $byhand_invoice->move($destinationPath, $hand);
                    
                }else{
                    $hand = '';
                }
                $party ='';
                if(isset($pickbyparty_invoice) || $pickbyparty_invoice != null){
                    $destinationPath = public_path().'/upload/taxdispatch';
                    $filenameWithExt = $request->file('pickparty_invoice')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('pickparty_invoice')->getClientOriginalExtension();
                    $party = $filename.'_'.time().'.'.$extension;
                    $path = $pickbyparty_invoice->move($destinationPath, $party);
                }else{
                    $party = '';
                }

                $timestamp = date('Y-m-d G:i:s');
                $date = strtotime($request->input('date'));
                $docket_date = date("Y-m-d", $date);
                    $dispatch=Tax_Dispatch::insertGetId([
                        'id' => NULL,
                        'tax_invoice_id' =>$request->input('num'),
                        'dispatch_date' =>$docket_date,
                        'dispatch_mode' =>$request->input('list_available'),
                        'courier_company' =>$request->input('com'),
                        'docket_number' =>$request->input('comp'),
                        'docket_date'  =>$request->input('list_available') =="Courier" ? date('Y-m-d',strtotime($request->input('doc_date'))) : '',
                        'docket_file' =>$resume,
                        'person'=>$request->input('persname'),
                        'byhand_invoice'=>$hand,
                        'party_invoice'=>$party,
                        'created_by' =>Auth::id(),
                        'is_active'=>1,
                        'created_time'=> $timestamp,
                    ]);
                  /*** USER LOG ***/
                  $goods_dispatch_id1=Goods_Dispatch::where('id',$request->input('goods_dispatch_id1'))->select('courier_name')->get()->first();
                  $goods_dispatch_id1 = isset($goods_dispatch_id1->courier_name)?$goods_dispatch_id1->courier_name:'';
                          
                  $person=EmployeeProfile::where('id',$request->input('persname'))->select('name')->get()->first();
                  $person = isset($person->name)?$person->name:'';

                $tax=Tax_Invoice::where('id',$request->input('num'))->select('invoice_number')->get()->first();
                $tax = isset($tax->invoice_number)?$tax->invoice_number:'';
  
             if($request->input('comp')){
                 $docket=$request->input('comp');
                }
                else{
                    $docket='';
                }
                $log_array=array(
                    'Tax Invoice'=>$tax,
                   'Dispatch Date'=>$request->input('date'),
                   'Dispatch Mode'=>$request->input('list_available'),
                   'Courier Company'=>$goods_dispatch_id1,
                   'Docket Number'=>$docket,
                   'Person'=>$person,
                );
   
                CustomHelpers::userLog('Tax Invoice Dispatch Created',$dispatch,'Tax Invoice Dispatch Created',
                $log_array);
            
                return redirect('/taxdispatch')->with('success','Tax Invoice Dispatch created successfully.');
            } catch (\Illuminate\Database\QueryException $ex) {
                return redirect('/taxdispatch')->with('error',$ex->getMessage());
            }
        }
        public function client_po_list()
        {
            $data=array('layout'=>'layouts.main');
            return view('sections.client_po_summary', $data);
        }
        public function client_po_view($id)//doubt
        {
            $identifier = Client_po::leftJoin('internal_order','client_po.io', '=', 'internal_order.id')
                ->where('client_po.id','=',$id)
                ->get(['client_po.reference_name','client_po.id'])->first();

            $client_po = Client_po::leftJoin('internal_order', 'client_po.io', '=', 'internal_order.id')
                ->leftJoin('unit_of_measurement', function($join) {
                    $join->on('client_po.unit_of_measure', '=', 'unit_of_measurement.id');})
                    ->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
                    ->where('client_po.id','=',$id);
            $client_po=$client_po->select(
                    'client_po.*',
                    'internal_order.io_number',
                    'party_reference.referencename as reference_name',
                    'unit_of_measurement.uom_name'
                )->get();

                $po=Client_po_data::where('client_po_data.client_po_id',$id)
                 ->select('po_number','po_qty','po_upload')->get();
            $consignee=Client_po_party::where('client_po_party.client_po_id',$id)
            ->leftJoin('client_po_consignee','client_po_consignee.client_po_party_id','client_po_party.id')
            ->leftJoin('party','party.id','client_po_party.party_name')
            ->leftJoin('consignee','consignee.id','client_po_consignee.consignee_id')
            ->select(
                'payment_terms',
                'party.partyname',
                'is_consignee',
                'party_name as partyid',
                DB::raw('group_concat(client_po_consignee.qty) as cpoc_qty'),
                DB::raw('group_concat(client_po_consignee.consignee_id) as consignee'),
                DB::raw('group_concat(consignee.consignee_name) as consignee_name'),
                DB::raw('group_concat(client_po_consignee.id) as client_po_id'))
            ->groupBy('client_po_party.party_name','party.partyname','client_po_party.payment_terms','client_po_party.is_consignee')->get();
            $hsn = Hsn::where('hsn.id','=',$client_po->first()['hsn'])
            ->select('hsn','gst_rate','item_id as name')->get();    

            $file_type=explode('.',$client_po->first()['po_file_name']);
            $file_type = $file_type[count($file_type)-1];

            $data=array(
                'layout'=>'layouts.main',
                'hsn'=>$hsn->first(),
                'data'=>$client_po,
                'po'=>$po,
                'data1'=>$consignee,
                'file_type'=>$file_type       
                );
                // return ($client_po);
            return view('sections.client_po_view', $data);
        }
        public function client_po_delete($id)
        {
            $client_po_io = Client_po::where('id',$id)->first('io')['io'];
            Client_po::where('id',$id)->update([
                'io'=>'',
                'del_io'=>$client_po_io
            ]);
            $data = Client_po::where('id',$id)->delete();
            $data1 = Client_po_party::where('client_po_id',$id)->delete();
            $data1 = Client_po_consignee::where('client_po_id',$id)->delete();
            return redirect('/clientpo/list')->with('status',"Client po deleted.");
        }
        public function client_po_update($id,Request $request)
        {
 
            $feed['reference'] = Reference::all();
            $feed['prefix_io'] = Settings::where('name', '=', 'internal_order_prefix')
                    ->first()->value;
            $feed['hsn'] = Hsn::get(['hsn.gst_rate','hsn.hsn','hsn.id','hsn.item_id as name']);
            $feed['pay_term'] = Payment::all();
            $feed['tax_per'] = TaxPercentageApplicable::all();
            $feed['uom'] = Unit_of_measurement::all();
            if ($request->old('party_name') != null) {
                $feed['io_feed'] = InternalOrder::where('party_id', '=', $request->old('party_name'))->get();
            }
            
            $prefix = Settings::where('name', '=', 'internal_order_prefix')->first()->value;
            $client_po = Client_po::leftJoin('internal_order', function($join) {
                    $join->on('client_po.io', '=', 'internal_order.id');})
                   ->leftJoin('job_details','job_details.id','internal_order.job_details_id')
                ->leftJoin('unit_of_measurement', function($join) {
                    $join->on('client_po.unit_of_measure', '=', 'unit_of_measurement.id');})
                ->where('client_po.id','=',$id)->select(
                    'client_po.*',
                    'unit_of_measurement.uom_name',
                    'job_details.qty as max_qty'
                )->get();
            $po=Client_po_data::where('client_po_id','=',$id)->select('id','client_po_id','po_number','po_qty',DB::raw('DATE_FORMAT(po_date,"%d-%m-%Y") as po_date'),'po_upload')->get();
            $po_number = Client_po_data::where('client_po_id',$id)->selectRaw('DISTINCT(po_number)')->get();
            $file_type=explode('.',$client_po->first()['po_file_name']);
            $file_type = $file_type[count($file_type)-1];
            // $party_id=InternalOrder::where('id','=',$client_po->first()['io'])->first('reference_name')['reference_name'];
            // $party_data = Party::where('id',$party_id)->first('reference_name')['reference_name'];
            $feed['party'] = Party::where('reference_name',$client_po->first()['reference_name'])->get(['id','partyname']);
            // $party_ids = Party::where('reference_name',$party_data)->get('id')->toArray(); 
            $io=InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
            ->where('reference_name',$client_po->first()['reference_name'] )
            ->select(
                'internal_order.id',
                'internal_order.io_number',
                'job_details.left_qty',
                'internal_order.reference_name'
            )
            ->get();
                $consignee=Client_po_party::where('client_po_party.client_po_id',$id)
                ->leftJoin('client_po_consignee','client_po_consignee.client_po_party_id','client_po_party.id')
                ->leftJoin('party','client_po_party.party_name','party.id')
                ->select(
                    'payment_terms',
                    'is_consignee',
                    'client_po_party.id as client_po_party_id',
                    'party_name as partyid',
                    'partyname',
                    DB::raw('group_concat(client_po_consignee.qty) as cpoc_qty'),
                    DB::raw('group_concat(client_po_consignee.consignee_id) as consignee'),
                    DB::raw('group_concat(client_po_consignee.id) as client_po_id'))
                ->groupBy('client_po_party.party_name','client_po_party.payment_terms','client_po_party.is_consignee','partyname','client_po_party.id')->get();


            $party_items=Client_po_party::where('client_po_id',$id)->select('payment_terms','is_consignee','party_name')->get();

            //    for($i=0;$i<count($po_ids);$i++) {
            //     $party_consignee = Client_po::where('id',$po_ids[$i])->get('party_name')->first();
            //     $par=$party_consignee['party_name'];
            //     $consignees[$par]=Consignee::where('party_id',$par)->get(['id','consignee_name']);
            //    }
            //    DB::enableQueryLog();           
            // $cpo=Client_po::where('client_po.id',$id)->leftjoin("client_po_consignee",\DB::raw("FIND_IN_SET(client_po_consignee.client_po_id,'client_po.id')"),"client_po_consignee.client_po_id")
            // // ->where('client_po_consignee.id','!=',NULL)
            // ->select('client_po.id','client_po_consignee.id as cpo')->get();
            // $queries = DB::getQueryLog();
                $cons=Array();
            foreach ($consignee as $key) {
                $cons[$key['partyid']]=Consignee::where('party_id',$key['partyid'])->select('id','consignee_name')->get();
            }
            $data=array(
                'layout'=>'layouts.main',
                'internalorder'=>$io,
                'feed' => $feed,
                'id' => $id,
                'data'=>$client_po,
                'data1'=>$consignee,
                'party'=>$party_items,
                'prefix'=> $prefix,
                'po_number'=>$po_number,
                'po'=>$po,
                'consignees'=> $cons,
                'file_type'=>$file_type        
            );
            // $new_cpo=array_filter($cpo);
            
            //  return $po;
            return view('sections.client_po_update', $data);        
                }
                
    public function client_po_updatedb($id,Request $request){
        
        $data = $request->input();
        // print_r($data);die;
        $data1 = $request->input();
        // $data['con_id'] = array_map('array_filter', $data['con_id']);
        // $data['con_id'] = array_filter($data['con_id']);
        // $data['old_consignee'] = array_map('array_filter', $data['old_consignee']);
        // $data['old_consignee'] = array_filter($data['old_consignee']);
        // print_r(array_filter($data['old_consignee']));
        unset($data['pay']);
        unset($data['is_con']);
        unset($data['cons']);
        unset($data['quan']);

        unset($data1['pay']);
        unset($data1['is_con']);
        unset($data1['cons']);
        unset($data1['quan']);

       

        $po_dates_old=$request->input('po_dates_old');
        $po_number_old=$request->input('po_number_old');
        $poqty_old=$request->input('poqty_old');
        $old_po_data_id=$request->input('old_po_data_id');
        $old_po_num=$request->input('old_po_num');
        $po_files_old=$request->file('po_files_old');
        $po_types=$request->input('po_type');

        $qtyss=$poqty_old;
      
        unset($data['po_number_old']);
        unset($data['poqty_old']);
        unset($data['old_po_data_id']);
        unset($data['old_po_num']);
        unset($data['po_files_old']);
        unset($data['po_dates_old']);
        unset($data['po_type']);
       

        unset($data1['po_type']);
        unset($data1['po_dates_old']);
        unset($data1['po_number_old']);
        unset($data1['poqty_old']);
        unset($data1['old_po_data_id']);
        unset($data1['old_po_num']);
        unset($data1['po_files_old']);
       
        if(isset($po_types)){
            $po_number=$request->input('po_number');
            $po_number1=$request->input('po_number1');
            $poqty=$request->input('poqty');
            $po_dates=$request->input('po_dates');
            $po_files=$request->file('po_files');

            if(isset($poqty_old)){
                 $qtyss=array_merge($poqty,$poqty_old);
                $pos=array_merge($po_number,$po_number1);
            }
            else{
                $qtyss=$poqty;
                $pos=$po_number;
            }
            if(isset($po_number_old)){
                $pos=array_merge($pos,$po_number_old);
            }
            else{
                $pos=$pos;
            }
            $pos=array_unique($pos);
            $pos=array_filter($pos);
            $pos=implode(',',$pos);
              
            unset($data['po_number']);
            unset($data['poqty']);
            unset($data['po_files']);
            unset($data['po_dates']);
            unset($data['po_number1']);

            unset($data1['po_number']);
            unset($data1['poqty']);
            unset($data1['po_files']);
            unset($data1['po_dates']);
            unset($data1['po_number1']);
        }
        else{
            $pos=$po_number_old;
            $pos=array_unique($pos);
            $pos=array_filter($pos);
            $pos=implode(',',$pos);
        }
        // print_r($pos);die;
        $val_arr = [
            'reference_name' => 'required|exists:party_reference,id',
            'is_po_provided' => 'required|boolean',
            'update_reason' => 'required',
            'created_by' => 'required|exists:users,id'
        ];
        // print_r($pos);die;
        $sum_qty=array_sum($qtyss);
        
        $io = InternalOrder::where('internal_order.id',$request->input('io'))->leftJoin('job_details','job_details.id','internal_order.job_details_id')->select('io_number','qty')->get()->first();
        // print_r($io['qty']);die;
        if($sum_qty>$io['qty']){
            DB::rollback();
            return redirect('/clientpo/update/'.$id)->with('error', 'PO Quantity Not Equal To IO Quantity.');
           }

        if(isset($data['is_po_provided']) && $data['is_po_provided'] == 1){
            $val_arr2 = [
                'party_name.*' => 'required|exists:party,id',
                'po_number.*' => 'required_if:po_type,0',
                'po_number1.*' => 'required_if:po_type,1',
                'po_dates.*' => 'required|date',
                'hsn' => 'required|exists:hsn,id',
                'payment_terms' => 'required|exists:payment_term,id',
                'item_desc' => 'required',
                'delivery_date' => 'required|date',
                'qty' => 'required|integer',
                'unit_of_measure' => 'required|exists:unit_of_measurement,id',
                'per_unit_price' => 'required|numeric',
                'discount' => 'required|numeric',
                'is_consignee.*' => 'required|boolean',
                'po_files.*'=>'required|file|mimes:pdf,jpg,jpeg,png|max:'.CustomHelpers::getfilesize()
            ];
            
            $val_arr = array_merge($val_arr, $val_arr2);
        } 
        else
        {
            
            try
            {
                $remove = ['_token','userAlloweds','party_name','po_type','po_number','po_number1', 'po_date',
                        'hsn', 'payment_terms', 'item_desc', 'delivery_date','qty', 'unit_of_measure', 'per_unit_price',
                        'discount','po_file', 'tax_perc_applicable', 'is_consignee','update_reason','old_party','client_po_party_id','con_id','consignee_name',
                    'old_quantity','old_consignee','consignee_qty'];
                      
                $data1 = array_diff_key($data1, array_flip($remove));  
                $validator = $request->validate($val_arr);
               
                 Client_po::where('id',$id)->update([
                     'is_po_provided'=>$data1['is_po_provided'],
                    //  'po_number'=>NULL,
                     'hsn'=>NULL,
                     'item_desc'=>NULL,
                     'delivery_date'=>NULL,
                     'qty'=>NULL,
                     'unit_of_measure'=>NULL,
                     'per_unit_price'=>NULL,
                     'discount'=>NULL,
                 ]);
                DB::enableQueryLog();
                 Client_po_consignee::where('client_po_id',$id)->delete();
                 Client_po_data::where('client_po_id',$id)->delete();
                 Client_po_party::where('client_po_id',$id)->delete();
                 return redirect('/clientpo/update/'.$id)->with('success',"Updated Client Po Successfully");
            }
            catch(Exception $ex){
                return redirect('/clientpo/update/'.$id)->with('error','Something went wrong!'.$ex->getMessage());
            }
        }
        // extracting excel and applying validation
        $party = $request->input('party_name');
        if(isset($party)){
            foreach($party as $pid)
        {
            if($request->file('excel.'.$pid)){
                $val_arr['excel.'.$pid] = 'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx';
                $path[$pid] = $request->file('excel.'.$pid);
            }
        }
        }
        unset($data['excel']);
        $validator = $request->validate($val_arr);
        // performing server sided validation
        // if($request->hasFile('po_file'))
        // {
        //     $file = $request->file('po_file');
        //     $destinationPath = public_path().'/upload/clientpo';
        //     $filenameWithExt = $file->getClientOriginalName();
        //     $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);      
        //     $extension = $file->getClientOriginalExtension();
        //     $fileNameToStore = $filename.'_'.time().'.'.$extension;  
        //     $path1 = $file->move($destinationPath, $fileNameToStore);
        //     $data['po_file_name'] = $fileNameToStore;
        // }
        // unset($data['po_file']);
        
        // extracting data for `Client_po_consignee`
        if (isset($data['consignee_name']) && (!empty($data['consignee_name']))) {
            $consg_name = $data['consignee_name'];
            $consg_qty = $data['consignee_qty'];
        }
        $update_reason = $data['update_reason'];
        $payment = $data['payment_terms'];
        $is_consignee = $data['is_consignee'];
        // $consignee_po_id=$data['con_id'];
        // $client_po_party_id=$data['client_po_party_id'];
        // removing unnessary data from `client_po` 
            unset($data['userAlloweds']);
            unset($data['_token']);
            unset($data['consignee_name']);
            unset($data['consignee_qty']);
            unset($data['old_consignee']);
            unset($data['old_quantity']);
            unset($data['old_party']);
            unset($data['update_reason']);      
            unset($data['con_id']);  
            unset($data['client_po_party_id']);      
                     
        // converting time format
        if (isset($data['is_po_provided']) && $data['is_po_provided'] == 1) 
        {

            $data['delivery_date'] = date('Y-m-d', strtotime($data['delivery_date']));
            // $data['po_date'] = date('Y-m-d', strtotime($data['po_date']));
        }
        
       
        $new_data = array();
        $po_data = array();
       
        foreach($party as $pid)
            $new_data=array_merge($new_data,array($data));
        for($i=0;$i<count($new_data);$i++)
        {
            $new_data[$i]['party_name'] =$party[$i]; 
            $new_data[$i]['payment_terms'] =$new_data[$i]['payment_terms'][$party[$i]]; 
            $new_data[$i]['is_consignee'] =$new_data[$i]['is_consignee'][$party[$i]]; 
        }
        try 
        {
            if(isset($po_types)){
                
                foreach($po_types as $key=>$value){
                    if($value==1 )
                    {
                        $po_number[$key] = $po_number1[$key];
                    }
                }
                
                if(isset($po_number_old)){
                    $po_numbers=array_merge( $po_number_old , $po_number);
                }
                else{
                    $po_numbers=$po_number;
                }
                $data['po_number']=implode(',',$po_numbers);
                foreach($po_number as $key=>$value){
                    $po_data_array['po_number']=$po_number[$key];
                    $po_data_array['po_qty']=$poqty[$key];
                    $po_data_array['po_date']=date('Y-m-d',strtotime($po_dates[$key]));
                    $po_data_array['client_po_id']=$id;
                    $file=$request->file('po_files'); 
                    if(isset($file[$key])){
                        $destinationPath = public_path().'/upload/clientpo';
                        $filenameWithExt = $file[$key]->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);      
                        $extension = $file[$key]->getClientOriginalExtension();
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;  
                        $path1 = $file[$key]->move($destinationPath, $fileNameToStore);
                        $po_data_array['po_upload']=$fileNameToStore;
                    }
                    else{
                        $po_data_array['po_upload']=NULL;
                    }
                    
                    $client_po_data=Client_po_data::insertGetId($po_data_array);
                }
              }

            unset($data['party_name']);
            unset($data['is_consignee']);
            unset($data['payment_terms']);
           
            $data['po_number']=$pos;
            $client_po = Client_po::where('id',$id)->update($data);
            $po_data_array=[];

            if(isset($po_number_old)){
                foreach($po_number_old as $key=>$value){
                    $po_data_array['po_number']=$po_number_old[$key];
                    $po_data_array['po_qty']=$poqty_old[$key];
                    $po_data_array['po_date']=date('Y-m-d',strtotime($po_dates_old[$key]));
                    $file=$request->file('po_files_old'); 
                    if(isset($file[$key])){
                        $destinationPath = public_path().'/upload/clientpo';
                        $filenameWithExt = $file[$key]->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);      
                        $extension = $file[$key]->getClientOriginalExtension();
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;  
                        $path1 = $file[$key]->move($destinationPath, $fileNameToStore);
                        $po_data_array['po_upload']=$fileNameToStore;
                    }
                   
    
                    // print_r($key);
                    // DB::enableQueryLog();
                    $client_po_data=Client_po_data::where('client_po_id',$id)->where('id',$key)     
                    ->where('po_number',$old_po_num[$key])->update($po_data_array);
    
                    // print_r(DB::getQueryLog());die;
                }
            }
            // print_r($po_data_array);die;
            // die;
        
            // foreach($delete_ponum as $key=>$value){
            //     //    print_r($value);
            //     $client_po_data=Client_po_data::where('client_po_id',$id)
            //     ->where('po_number',$value)->delete();
            //    }
            // die;
            $po_data['client_po_id']=$client_po;
           
             /*** USER LOG ***/
             $removekeys=array(
            'old_party',
             'client_po_party_id',
             'payment_terms',
             'is_consignee',
             'con_id',
             'consignee_name',
             'old_quantity',
             'old_consignee',
             'consignee_qty');
             $io = InternalOrder::where('id',$request->input('io'))->select('io_number')->get()->first();
             $io = isset($io->io_number)?$io->io_number:'';
             $changes_array['io'] = $io;


             $hsn = Hsn::where('id',$request->input('hsn'))->selectRaw("CONCAT(hsn.item_id,' - ',hsn.hsn,' - ',hsn.gst_rate) as name")->get()->first();
             $hsn = isset($hsn->name)?$hsn->name:'';
             $changes_array['hsn'] = $hsn;

             $reference=Reference::where('id',$request->input('reference_name'))->select('referencename')->get()->first();
             $reference_name = isset($reference->referencename)?$reference->referencename:'';
             $changes_array['reference_name'] = $reference_name;


             $unit_of_measure=Unit_of_measurement::where('id',$request->input('unit_of_measure'))->select('uom_name')->get()->first();
             $unit_of_measure = isset($unit_of_measure->uom_name)?$unit_of_measure->uom_name:'';
             $changes_array['unit_of_measure'] = $unit_of_measure;

             $party_name=Party::whereIN('id',$request->input('party_name'))->select(DB::raw('group_concat(party.partyname) as partyname'))->get()->first();
             $party_name = isset($party_name->partyname)?$party_name->partyname:'';
             $changes_array['party_name'] = $party_name;

                 CustomHelpers::userActionLog($request->input()['update_reason'],$id,'Client PO Updated',
                 $log_array=array(
                     
                     'reference_name'=>'Reference Name',
                     'io'=>'Internal Order',
                     'party_name'=>'Party',
                     'po_number'=>'PO Order',
                     'po_date'=>'PO Date',
                     'hsn'=>'HSN Code',
                     'unit_of_measure'=>'Qty Unit',
                     'item_desc'=>'Item Description',
                     'delivery_date'=>'Delivery Date',
                     'qty'=>'Quantity',
                     'per_unit_price'=>'Per Unit Price',
                     'discount'=>'Discount',
                 ),
                 $changes_array);
                 /***  END USER LOG ***/
                            for($k=0;$k<count($party);$k++) 
                            {
                                if(!isset($path[$party[$k]]))
                                {
                                    $Client_po_party_id = Client_po_party::where('client_po_id',$id)->where('party_name',$party[$k])->get()->first();
                                    if(isset($Client_po_party_id))
                                    {
                                        Client_po_party::where('id',$Client_po_party_id->id)->update([
                                            'payment_terms'=>$payment[$party[$k]],
                                            'is_consignee'=>$is_consignee[$party[$k]],
                                        ]);
                                        if($is_consignee[$party[$k]]==0)
                                        {
                                            Client_po_consignee::where('party_id',$party[$k])
                                            ->where('client_po_id',$id)->delete();
                                        }
                                        $client_party[$party[$k]] = $Client_po_party_id->id;
                                    }
                                    else
                                    {
                                        $client_party[$party[$k]]=Client_po_party::insertGetId([
                                            'client_po_id'=>$id,
                                            'party_name'=>$party[$k],
                                            'payment_terms'=>$payment[$party[$k]],
                                            'is_consignee'=>$is_consignee[$party[$k]]
                                        ]);
                                    }
                                }
                            }
                     
                            // using id from above to multiple enter `Client_po_consignee`
                            $qty_sum=0; 
                            $count = count($party);
                            for ($i = 0; $i < $count; $i++) 
                            {
                                if(!isset($path[$party[$i]]))
                                {
                                    if (isset($consg_name) && is_array($consg_name) && count($consg_name)>0 && $new_data[$i]['is_consignee'] == 1 ) 
                                    {
                                        $count1 = count($consg_name[$party[$i]]);
                                        for ($j = 0; $j < $count1; $j++) 
                                        {
                                            if($consg_qty[$party[$i]][$j] ==0)
                                                continue;
                                            $Client_po_consignee_id = Client_po_consignee::where('consignee_id',$consg_name[$party[$i]][$j])
                                                ->where('party_id',$party[$i])
                                                ->where('client_po_id',$id)->get()->first();
                                            $qty_sum = $qty_sum+ $consg_qty[$party[$i]][$j];
                                            if(isset($Client_po_consignee_id))
                                            {
                                                Client_po_consignee::where('id',$Client_po_consignee_id->id)->update([
                                                    'client_po_party_id'=>$client_party[$party[$i]],
                                                    'qty' => $consg_qty[$party[$i]][$j],
                                                ]);
                                            }
                                            else
                                            {
                                                Client_po_consignee::insert([
                                                    'consignee_id' => $consg_name[$party[$i]][$j],
                                                    'qty' => $consg_qty[$party[$i]][$j],
                                                    'client_po_party_id'=>$client_party[$party[$i]],
                                                    'client_po_id' => $id,
                                                    'party_id'=>$party[$i]
                                                ]);
                                            }
                                        }
                                    }
                                    Client_po_consignee::whereNotIn('consignee_id',$consg_name[$party[$i]])
                                    ->where('party_id',$party[$i])
                                    ->where('client_po_id',$id)
                                    ->delete();
                                }
                    
                            }

                                      
                        // delete code
                            $Client_po_party_id = Client_po_party::whereNotIn('party_name',$party)
                                ->where('client_po_id',$id)->get('party_name')->toArray();
                            Client_po_party::whereIn('party_name',$Client_po_party_id)
                                ->where('client_po_id',$id)->delete();
                            Client_po_consignee::whereIn('party_id',$Client_po_party_id)
                                ->where('client_po_id',$id)->delete();

                    
                    
        }
        catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return redirect('/clientpo')->with('error','Something went wrong!'.$ex->getMessage());
        }
        
        $total_error = 0;
        $column_name_err=0;
        $error = "";

        for($party_acc=0;$party_acc<count($party);$party_acc++)
        {
            $pid = $party[$party_acc];
            
            //storing list from excel
            if(isset($path[$pid]) && $new_data[$party_acc]['is_consignee'] == 1 && isset($client_po))
            {
                $Client_po_party_id = Client_po_party::where('party_name',$pid)
                    ->where('client_po_id',$id)->delete();
                Client_po_consignee::where('party_id',$pid)
                    ->where('client_po_id',$id)->delete();
                $client_party[$pid]=Client_po_party::insertGetId([
                    'client_po_id'=>$id,
                    'party_name'=>$pid,
                    'payment_terms'=>$payment[$pid],
                    'is_consignee'=>$is_consignee[$pid]
                ]);
                $party_id = $pid;
                $data = Excel::toArray(new Import(),$path[$pid]);
                if($data){    
                    $v = $data[0];
                }
                $column_name_format = array('consignee_name','gst','pan','address','pincode','country','state','city', 'qty');
                $index = 0;
                $char = '65';
                foreach($v[0] as $p=>$q)
                {
    
                    if($q!=$column_name_format[$index]){
                        $column_name_err++;
                        $error=$error."Column Name not in provided format. Error At ".chr($char)."1.";
                    }
                    $index++;
                    $char++;
                }
    
                $total_error = $total_error + $column_name_err;
                if($column_name_err == 0)
                {
                    for($i = 1; $i < count($v); $i++)   
                    {
                        $char = '65';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($v1 == "")
                            {
                                $error=$error."Empty Cell at ".chr($char).($i).". "; 
                                $fl++;
                            }
                            $char++; 
                        }
                        $total_error = $total_error + $fl;
                        if($fl==0)
                        {
                            $data_err=0;
                            $country = Country::where('name','like',$v[$i]['5'])
                            ->get('id')->first();
                            if(!$country)
                            {
                                $error = $error."Country at F".($i)." Not found.<br/>";
                                $data_err++;
                            }
                            else
                                $state = State::where('country_id','=',$country->id)
                                ->where('name','like',$v[$i]['6'])
                                ->get('id')->first();
                            if(!$country || !$state)
                            {
                                $error = $error."State at G".($i)." Not found.<br/>";
                                $data_err++;
                            }
                            else
                                $city = City::where('state_id','=',$state->id)
                                            ->where('city','like',$v[$i]['7'])
                                            ->get('id')->first();
                            if(!$country || !$state || !$city )
                            {
                                $error = $error."City at H".($i)." Not found.<br/>";
                                $data_err++;
                            }
                            $total_error = $total_error + $data_err;
                        
                            if($data_err == 0)
                            {
                                try{                    
                                    $consg_name_id = Consignee::insertGetId(
                                        [
                                            'id' => NULL,
                                            'consignee_name' => $v[$i][0],
                                            'party_id' =>$party_id,
                                            'gst' =>$v[$i][1],
                                            'pan' =>$v[$i][2],
                                            'address' =>$v[$i][3],
                                            'city' =>$city->id,
                                            'pincode' =>$v[$i][4],
                                            'state' =>$state->id,
                                            'country' =>$country->id,
                                            'created_by'=>Auth::id(),
                                            'is_active'=>'1',
                                            'created_time'=>date('Y-m-d G:i:s'),
                                        ]);
                                    $qty_sum = $qty_sum + $v[$i][8];
                                    
                                    Client_po_consignee::insert([
                                        'client_po_party_id'=>$client_party[$pid],
                                        'party_id'=>$pid,
                                        'consignee_id' => $consg_name_id,
                                        'qty' => $v[$i][8],
                                        'client_po_id' => $id
                                    ]);
                                }catch (\Illuminate\Database\QueryException $ex) {
                                    DB::rollback();
                                    return redirect('/clientpo')->with('error', 'Something went wrong!');
                                }
                            }
                        }
                        else{
                            $error=$error."<br/>";
                            DB::rollback();
                        }
                    }
                }
                else
                {
                    DB::rollback();
                    $error = $error." No data is inserted.";
                }
            }
        }    
        $is_consignee_counter=0;
        foreach($new_data as $nd)
            $is_consignee_counter = $is_consignee_counter + $nd['is_consignee'];
        if($total_error==0)
        {
            if($qty_sum!=$new_data[0]['qty'] && $is_consignee_counter!=0)
            {
                DB::rollback();
                return redirect('/clientpo/update/'.$id)->with('error', 'Consignee Quantity Not Equal To Purchase Order Quantity.');
            }
            DB::commit();
            $request->session()->flash('importerrors',"");
            return redirect('/clientpo/update/'.$id)->with('success', 'Client Purchase Order has been Updated.');
        }
        else if($column_name_err!=0)
        {
            $request->session()->flash('importerrors', $error);
            return redirect('/clientpo/update/'.$id)->with('error', 'Errors Found.');        
        }
        else
        {
            $request->session()->flash('importerrors', $error."No Data is Updated.");
            return redirect('/clientpo/update/'.$id)->with('error', $error.'Errors Found.');        
        }
    }
    public function client_po_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $client_po = Client_po::leftJoin('internal_order', 'client_po.io', 'internal_order.id')
        ->leftJoin('client_po_data','client_po_data.client_po_id','client_po.id')
        // ->leftJoin('client_po_party','client_po_party.client_po_id','client_po.id')
        ->leftJoin('client_po_party', function($join) {
			$join->on('client_po_party.client_po_id','=','client_po.id');
			$join ->where('client_po_party.deleted_at',NULL)  ;
		})
        ->leftJoin('party', function($join) {
			$join->on('party.id', '=', 'client_po_party.party_name');
			$join ->where('client_po_party.deleted_at',NULL)  ;
		})
            // ->leftJoin('party', 'client_po_party.party_name','party.id')
            ->leftJoin('party_reference','party_reference.id','client_po.reference_name')
            ->leftJoin('item_category', 'internal_order.item_category_id','item_category.id')
            ->leftJoin('hsn', 'hsn.id','client_po.hsn')
            ->leftJoin('job_details', 'job_details.id','internal_order.job_details_id')
            ->groupBy('internal_order.id','client_po.id','party.reference_name');  
           
        if(!empty($serach_value))
        {
            $client_po->where('party.partyname','LIKE',"%".$serach_value."%")
            ->orwhere('hsn.hsn','like',"%".$serach_value."%")
            ->orwhere('internal_order.io_number','like',"%".$serach_value."%")
            ->orwhere('item_category.name','like',"%".$serach_value."%")
            ->orwhere('party_reference.referencename','like',"%".$serach_value."%")
            ;
        }
        
        $count = count($client_po->select(
            DB::raw('group_concat(DISTINCT(party_reference.referencename)) as refer'),
            DB::raw('group_concat(DISTINCT(client_po.is_po_provided)) as is_po_provided'),
            DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io'),
            DB::raw('group_concat(DISTINCT(client_po.id)) as id1'),'client_po.created_at',
            DB::raw('group_concat(client_po_data.po_number) as pono'),
            DB::raw('group_concat(client_po_data.po_date) as po_date'),
            DB::raw('group_concat(DISTINCT(IFNULL(client_po.qty, 0))) as qty'),
            DB::raw('group_concat(DISTINCT(job_details.qty)) as io_qty'),
            DB::raw('group_concat(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
            DB::raw('group_concat(party.partyname) as pname'),
            DB::raw('group_concat(client_po_party.deleted_at) as deleted_atas'),
            DB::raw('group_concat(party.id) as pid'))->where('client_po.is_active','=',1)->get());
          
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['pname','client_po.po_number','io','pono', 'hsn.hsn','qty','po_date','io_qty','created_at'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $client_po->orderBy('client_po.id','desc');
        }
        $client_po= $client_po->select(
            DB::raw('group_concat(DISTINCT(party_reference.referencename)) as refer'),
            DB::raw('group_concat(DISTINCT(client_po.is_po_provided)) as is_po_provided'),
            DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io'),
            DB::raw('group_concat(DISTINCT(client_po.id)) as id1'),'client_po.created_at',
            DB::raw('group_concat(DISTINCT(client_po_data.po_number)) as pono'),
            DB::raw('group_concat(DISTINCT(DATE_FORMAT(client_po_data.po_date ,"%d-%m-%Y"))) as po_date'),
           DB::raw('group_concat(DISTINCT(IFNULL(client_po.qty, 0))) as qty'),
            DB::raw('group_concat(DISTINCT(job_details.qty)) as io_qty'),
            DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as item_name'),
            DB::raw('group_concat(party.partyname) as pname'),
            DB::raw('group_concat(client_po_party.deleted_at) as deleted_atas'),
            DB::raw('group_concat(DISTINCT(IFNULL(job_details.qty,0))) as io_qty')
                            
        )->where('client_po.is_active','=',1)->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
         return $array;
    }
    public function get_user_ip(Request $req)
    {
        //      $local_ip = $_SERVER['REMOTE_HOST'];
           
          
        //$arr = file(storage_path('logs')."");
        $global_ip = '';//(explode(' ',$arr[count($arr)-1])[0]);
                $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_X_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if(isset($_SERVER['REMOTE_ADDR']))
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else
        $ipaddress = 'UNKNOWN';
            $prefix = Settings::where('name', '=', 'internal_order_prefix')->first()->value;
            $data=array('layout'=>'layouts.main','local_ip'=>$ipaddress,'global_ip'=>$global_ip);
            return $data;
    }
    public function getroute(){
        ini_set('max_execution_time', 18000);
        // $consignee = Party::where('reference_name','like','%=a%')->get(['id','partyname']);

        // foreach($consignee as $key)
        // {
        //     Party::where('id',$key->id)->update([
        //         'reference_name'=>$key->partyname
        //     ]);
        // }
        // return 'done';/*
        // $consignee = SectionRight::all();
        // ini_set('max_execution_time', 18000);
      
        // foreach($consignee as $key)
        // {
        //     SectionRight::where('id',$key->id)->update([
        //         'permission_pid'=>$key->pid
        //     ]);
        // }
        // return 'done';*/
        /*
        $consignee = Consignee::all();
        ini_set('max_execution_time', 18000);
      
        foreach($consignee as $key)
        {
            Consignee::where('id',$key->id)->update([
                'id'=>$key->party_id
            ]);
        }
        return 'done';
        */
            
        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $value) 
        {
        $link = '/'.preg_replace("/{[a-zA-Z0-9_]*}/","*",$value->Uri());
        $name =ucwords(strtolower(explode('*',implode(' ',explode('/',$link)))[0]));
        $icon='';
        $pid=0;
        $showorder=0;
        $showmenu=0;
        $id=SectionRight::where('link','=',$link)->get('id')->first();
            if($id){ 
                $id=$id->id;
                // echo $id.' - - - ';
            }
            else 
            {
                echo $value->Uri().'<br>';
                // $section = SectionRight::insertGetId([
                //     'id'=>NULL,
                //     'link'=>$link,
                //     'name'=>$name,
                //     'icon'=>$icon,
                //     'pid'=>$pid,
                //     'showorder'=>$showorder,
                //     'show_menu'=>$showmenu
                // ]);
                // UserSectionRight::insert([
                //     'id'=>NULL,
                //     'user_id'=>'1',
                //     'section_id'=>$section,
                //     'allowed'=>'1,3'
                // ]);
            }
        }
       
    }
  //-----------------------------------Tax Invoice Summary--------------------------------------------------      
    public function tax_invoice_list(Request $request){
        $prefix = Settings::where('name', '=', 'internal_order_prefix')->first()->value;
        $data=array('layout'=>'layouts.main','prefix'=>$prefix);
        return view('sections.tax_invoice_summary', $data);
    }
    public function get_tax_invoice_ios($id){
        $data = Tax_Invoice::leftJoin('tax','tax.tax_invoice_id','tax_invoice.id')
        ->leftJoin('client_po','client_po.io','tax.io_id')
        ->leftJoin('internal_order','internal_order.id','tax.io_id')
        ->where('tax_invoice.id',$id)
        ->select(
            DB::raw('DISTINCT(internal_order.io_number)'),
            DB::raw('IFNULL(client_po.po_number,"Verbal") as po_number'),
            'internal_order.id as io',
            DB::raw('IFNULL(client_po.is_po_provided,"0") as is_po_provided')
        )->get()->toArray();
        $data1=Tax_Print::where('invoice_id',$id)->get();
            return array('data'=>$data,'data1'=>$data1->toArray());
    }
    
    public function tax_invoice_print_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $client_po = Tax_Print::leftJoin('tax_invoice','tax_invoice.id', 'tax_print.invoice_id')
                        ->leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
                        ->leftJoin('party','tax_invoice.party_id', 'party.id')
                        ->leftJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                        ->leftJoin('internal_order','internal_order.id',  'tax.io_id')
                        ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
                      
                        ->where('tax_invoice.is_active',1)
                        ->select(
                            'tax_invoice.invoice_number as ch_no',
                            'party.partyname as pname',
                            'consignee.consignee_name as cname',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.id',
                            'tax_print.id as tx_p',
                            'tax_invoice.total_amount',
                            'tax_print.io_po_number',
                            DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io_number'),
                            DB::raw('group_concat(DISTINCT(tax_invoice.created_at)) as created_at'),
                            DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number')
                        )->groupBy('tax_invoice.id','tax_print.io_po_number')
                       ;

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
        $count = count($client_po->get()->toArray());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['ch_no','party.partyname','io_number','tax_print.io_po_number','created_at','consignee.consignee_name','tax_invoice.id','tax_invoice.terms_of_delivery','internal_order.io_number','tax_invoice.total_amount','delivery_challan.challan_number'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $client_po->orderBy('tax_print.id','desc');
        $client_po= $client_po->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        return json_encode($array);
    }
    public function tax_invoice_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $client_po = Tax_Invoice::leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
                        ->leftJoin('party','tax_invoice.party_id', 'party.id')
                        ->leftJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                        ->leftJoin('internal_order','internal_order.id',  'tax.io_id')
                        ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
                        ->leftJoin('request_permission',function($join){
                            $join->on('request_permission.data_id','=','tax_invoice.id');
                            $join->where('request_permission.status','=','pending');
                            $join->where('request_permission.data_for','=','taxinvoice');
                        })
                        ->leftJoin('request_permission as rp',function($join){
                            $join->on('rp.data_id','=','tax_invoice.id');
                            $join->where('rp.status','=','pending');
                            $join->where('rp.data_for','=','taxinvoicedelete');
                        })
                        ->leftJoin('request_permission as rp1',function($join){
                            $join->on('rp1.data_id','=','tax_invoice.id');
                            $join->where('rp1.status','=','pending');
                            $join->where('rp1.data_for','=','taxinvoiceupdate');
                        })
                        ->where('tax_invoice.is_active',1)
                        ->select(
                            'tax_invoice.invoice_number as ch_no',
                            DB::raw('CONVERT(group_concat(DISTINCT(SUBSTRING_INDEX(tax_invoice.invoice_number,"/",-1))),UNSIGNED  INTEGER) as asd'),
                            'party.partyname as pname',
                            'consignee.consignee_name as cname',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.id',
                            'tax_invoice.financial_year',
                            'request_permission.status as st',
                            'rp.status as st1',
                            'rp1.status as st2',
                            'tax_invoice.is_update',
                            'tax_invoice.total_amount','tax_invoice.is_cancelled',
                            'tax_invoice.status',DB::raw('(DATE_FORMAT(tax_invoice.date ,"%d-%m-%Y")) as date'),
                            DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io_number'),
                            DB::raw('group_concat(DISTINCT(tax_invoice.created_at)) as created_at'),
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
                ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
                ->orwhere('internal_order.io_number','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.status','like',"%".$serach_value."%")
                ->orwhere('delivery_challan.challan_number','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.terms_of_delivery','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%");
            });                
        }
        $count = count($client_po->get()->toArray());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['ch_no','party.partyname','rp.status','tax_invoice.is_update','rp1.status','consignee.consignee_name','tax_invoice.date','tax_invoice.status','created_at','internal_order.io_number','tax_invoice.id','tax_invoice.terms_of_delivery','internal_order.io_number','tax_invoice.total_amount','delivery_challan.challan_number'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $client_po->orderBy('financial_year','desc')->orderBy('asd','desc')->orderBy('tax_invoice.id','desc');;
        $client_po= $client_po->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        return json_encode($array);
    }
    public function tax_invoice_cancelled_list_api(Request $request){
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
                        ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
                        ->where('tax_invoice.status','Cancelled')
                        ->select(
                            'tax_invoice.invoice_number as ch_no',
                            'party.partyname as pname',
                            'consignee.consignee_name as cname',
                            'tax_invoice.terms_of_delivery',
                            'tax_invoice.id',
                            'tax_invoice.total_amount','tax_invoice.is_cancelled',
                            DB::raw('(DATE_FORMAT(tax_invoice.date ,"%d-%m-%Y")) as date'),'tax_invoice.status',
                            DB::raw('group_concat(internal_order.io_number) as io_number'),
                            DB::raw('group_concat(DISTINCT(tax_invoice.created_at)) as created_at'),
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
                ->orwhere('consignee.consignee_name','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.status','like',"%".$serach_value."%")
                ->orwhere('delivery_challan.challan_number','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.terms_of_delivery','like',"%".$serach_value."%")
                ->orwhere('tax_invoice.invoice_number','like',"%".$serach_value."%");
            });                
        }
        $count = count($client_po->get()->toArray());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['ch_no','party.partyname','consignee.consignee_name','tax_invoice.date','tax_invoice.status','created_at','internal_order.io_number','tax_invoice.id','tax_invoice.terms_of_delivery','internal_order.io_number','tax_invoice.total_amount','delivery_challan.challan_number'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $client_po->orderBy('invoice_number','desc');
        $client_po= $client_po->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        return json_encode($array);
    }
    public function tax_details($id){
        $tax=Tax_Invoice::where('id',$id)->select(DB::raw('DATE_FORMAT(date,"%d-%m-%Y") as date'))->get()->first();
        return $tax;
    }
    public function tax_invoice_view($id){
        $po=Tax_Invoice::where('tax_invoice_id',$id)
        ->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
        ->leftJoin('internal_order','internal_order.id','=','tax.io_id')
        ->leftJoin('party', 'party.id','=', 'tax_invoice.party_id')
        ->leftJoin('client_po','tax.io_id','=','client_po.io')
        ->leftJoin('client_po_data','client_po_data.client_po_id','client_po.id')
        ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
        ->select(DB::raw('group_concat(DISTINCT(io_number)) as io_number'),
        DB::raw('group_concat(DISTINCT(IFNULL(client_po_data.po_number,"Yet Not Received!!"))) as po_number'),
        DB::raw('group_concat(DISTINCT(IFNULL(DATE_FORMAT(client_po_data.po_date,"%d-%m-%Y"),"-"))) as po_date'),
        DB::raw('group_concat(DISTINCT(IFNULL(payment_term.value,"-"))) as payment_term')
        
        )->get();

        $tax_detail = Tax_Invoice::where('tax_invoice.id',$id)
        ->where('tax_invoice.is_active','=',1)
        ->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
        ->leftJoin('settings','tax_invoice.gst_type','=','settings.name')
        ->leftJoin('payment_term as tax_pyment','tax.payment','=','tax_pyment.id')
        ->leftJoin('delivery_challan','delivery_challan.id','=','tax.delivery_challan_id')
        ->leftJoin('internal_order','internal_order.id','=','tax.io_id')
        ->leftJoin('hsn','hsn.id','=','tax.hsn')
        ->leftJoin('client_po','tax.io_id','=','client_po.io')
        //->leftJoin('client_po_data','client_po_data.client_po_id','client_po.id')
        ->leftJoin('party', 'party.id','=', 'tax_invoice.party_id')
        ->leftJoin('consignee', 'consignee.id','=', 'tax_invoice.consignee_id')
        ->leftJoin('countries','party.country_id','=','countries.id')
        ->leftJoin('states','party.state_id','=','states.id')
        ->leftJoin('cities','party.city_id','=','cities.id')
        ->leftJoin('countries as con_country','consignee.country','=','con_country.id')
        ->leftJoin('states as con_states','consignee.state','=','con_states.id')
        ->leftJoin('cities as con_cities','consignee.city','=','con_cities.id')
        ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
        ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
        ->leftJoin('unit_of_measurement','tax.per','=','unit_of_measurement.id')
        ->get([
            'tax_invoice.id as tax_invoice_id',
            'tax_invoice.invoice_number',
            'tax_invoice.terms_of_delivery',
            'tax_invoice.gst_type',
            'tax_invoice.transportation_charge',
            'tax_invoice.other_charge',
            'settings.value as gst_type_rate',
            'tax_invoice.created_at',
            'tax_invoice.party_id as tax_party_id',
            'tax_invoice.consignee_id as tax_consignee_id',
            'tax_invoice.created_at',
            
            'tax.delivery_challan_id',
            'tax.io_id',
            'tax.goods',
            'tax.qty',
            'tax_pyment.value as tax_payment',
            
            'tax.rate',
            'tax.per',
            'tax.discount',
            'tax.hsn',
            'unit_of_measurement.uom_name',
            'internal_order.id as internalorder_id',
            'internal_order.io_number',
            'item_category.name as item',
            'payment_term.value as payment_term',
            'states.name as state',
            'countries.name as country',
            'cities.city',

            'con_states.name as con_states',
            'con_country.name as con_country',
            'con_cities.city as con_cities',
            'party.id as party_id',
            'party.partyname',
            'party.address as party_address',
            'party.pincode',
            'hsn.hsn',
            'hsn.gst_rate as hsn_gst',
            
            'delivery_challan.challan_number',
            'client_po.is_po_provided',
            'client_po.po_number',
            'client_po.po_date',
            'delivery_challan.dispatch',
            'consignee.id as con_id',
            'consignee.consignee_name as con_name',
            'consignee.address as con_address',
            'consignee.pincode as con_pincode'
        ]);
        
        if(count($tax_detail)!==0){
            $data = [
                    'foo' => 'bar',
                    'tax_detail'=>$tax_detail,
                    'po'=>$po,
                    'layout'=>'layouts.main'
                ];
            return view('sections.tax_invoice_view',$data);
            
        }
        else{
            $message="No Tax Invoice exist!!";
            return redirect('/taxinvoice/list')->with('error',$message);
        }
        
    }
    public function get_internal_order_by_partyid($id)
    {
            
        // $ref_name = Party::where('id',$id)->first('reference_name')['reference_name'];
        // $client_id = Party::where('reference_name',$ref_name)->get('id')->toArray();
        $details=InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
            ->where('internal_order.reference_name',$id)
            ->where('internal_order.is_active',1)
            ->where('internal_order.status','=','Open')
            ->where('job_details.left_qty','>',0)
            ->whereNotIn('job_details.io_type_id',array('8'))
            ->get([
                'internal_order.id',
                'internal_order.io_number',
                'internal_order.reference_name'
            ])
            ->toArray();
        $arr = array('io_list' => $details);
        return $arr;

    }

    public function io_update_request($id)
    {
        $flag=0;
    $usertype=Auth::user()->user_type;
    $io_dc=tax::where('tax.io_id',$id)->get('tax.io_id')->first();
    // $req=RequestPermission::where('data_id',$id)->where('data_for','internalorder')->where('requested_by',Auth::id())->where('status','allowed')->first();
    // print_r($req);die;
    if($io_dc && $usertype=="superadmin"){
        $flag=1;
        $msg="Tax Invoice is already Raised for this Internal Order.";
    }
    else if(!($io_dc) && $usertype=="superadmin"){
        $dc=Challan_per_io::where('io',$id)->get('challan_per_io.id')->first();
        $jc=JobCard::where('io_id',$id)->get('job_card.id')->first();
        if($dc){
            $flag=1;
            $msg="Delivery Challan Has Already Raised for this Internal Order";
        }
        else if($jc){
            $flag=0;
            $msg="Job Card Has Already Raised for this Internal Order";
        }
        else{
            $flag=0; 
            $msg="";
        }
    }
    else{
        $flag=0; 
        $msg="";
    }

        $item = ItemCategory::all();
        $hsn = Hsn::get(['hsn.gst_rate','hsn.hsn','hsn.id','hsn.item_id as name']);
        // $users=EmployeeProfile::where('department_id','=',10)->get();
        $users=MasterMarketingPerson::all();
    
        // $party=City::join('party', 'cities.id', '=', 'party.city_id')
        //             ->get(['party.id','party.partyname','party.city_id','cities.city']);
        $party = Reference::all();
    
        $io_type=IoType::all();
        $unitof = Unit_of_measurement::all();
        $po_num=PoNumber::all();
        $partydata = InternalOrder::leftJoin('job_details',function($join){
            $join->on('internal_order.job_details_id','=','job_details.id');
        })->leftJoin('item_category',function($join){
            $join->on('internal_order.item_category_id','=','item_category.id');
        })->leftJoin('advance_io',function($join){
            $join->on('job_details.advance_io_id','=','advance_io.id');
        })->where('internal_order.id','=',$id);
    
        $internal_order_data = InternalOrder::where('id',$id)->where('status','open')->get()->first();
        if($internal_order_data){
            $partydata = $partydata->select(
                'internal_order.id',
                'internal_order.other_item_name',
                'internal_order.reference_name',
                'item_category.id as item_category_id',
                'job_details.io_type_id',
                'job_details.job_date',
                'job_details.hsn_code',
                'job_details.delivery_date',
                'job_details.qty',
                'job_details.left_qty',
                'job_details.unit',
                'job_details.job_size',
                'job_details.rate_per_qty',
                'job_details.details',
                'job_details.front_color',
                'job_details.back_color',
                'job_details.is_supplied_paper',
                'job_details.dimension',
                'job_details.is_supplied_plate',
                'job_details.transportation_charge',
                'job_details.other_charge',
                'job_details.remarks',
                'job_details.advanced_received',
                'advance_io.amount',
                'advance_io.mode_of_receive',
                'advance_io.date',
                'job_details.marketing_user_id'
                )->get();
                $data=array(
                    'layout' => 'layouts.main',
                    'item' => $item,
                    'party' => $party,
                    'po_num' => $po_num,
                    'io_type' => $io_type,
                    'flag' => $flag,
                    'msg' => $msg,
                    'hsn' => $hsn,
                    'data'=>$partydata,
                    'job_qty_unit'=>$unitof,
                    'users'=>$users
                );
            return view('sections.io_update_request', $data);
        }
        else{
            return redirect('/internal/list/open')->with('error','This IO Doesnt Exist.');
        }
        //$advance_io_id = JobDetailsView::where('id', $internal_order_data['party_id'])->value('advance_io_id');
    
       
    
    }
    public function io_update_request_db(Request $request,$id)
    {
        $qtys=0;
        $cc=1;
        if($request->input('jobdetail_id')){

        }
        else{
            $inter=$request->input('io_id');
            $amt=$request->input('amount');
            if($amt!=NULL){
                $arr=array_merge($inter,$amt);
            }
            else{
                $arr=$inter;
            }

            $arr=array_filter($arr);
            $cc=count($arr);
            
        }
        // print_r(count($arr));die;
        if($cc==0){
            return redirect('/addreqiredpermission/internalorder/update/'.$id)->with('error','Please Select Atleast one Input ')->withInput();   
        }
        
        $internalorder_data=InternalOrder::where('internal_order.id',$id)
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
        ->leftJoin('hsn','hsn.id','=','job_details.hsn_code')
        ->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
        ->select(
            'internal_order.id as io_id',
            'internal_order.io_number',
            'internal_order.status',
            'internal_order.party_id',
            'internal_order.item_category_id',
            'internal_order.job_details_id',
            'internal_order.created_time',
            'internal_order.created_by',
            'internal_order.other_item_name',
            'party.address',
            'party.pincode',
            'payment_term.value',
            'party_reference.referencename as partyname',
            'item_category.name as item_category',
            'io_type.name as io_type',
            'unit_of_measurement.uom_name',
            'job_details.job_date',
            'job_details.delivery_date',
            'job_details.qty',
            'job_details.job_size',
            'job_details.rate_per_qty',
            'job_details.dimension',
            'job_details.marketing_user_id',
            'job_details.details',
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
            'advance_io.date as amount_received_date',
            DB::raw("CONCAT(hsn.item_id,' - ',hsn.hsn,' - ',hsn.gst_rate) as hsn_name"),
            'hsn.gst_rate as gst'
        )->get()->first();
            if($internalorder_data['mode_of_receive']==0)
                $internalorder_data['mode_of_receive']=="Cash";
            else if($internalorder_data['mode_of_receive']==1)
                $internalorder_data['mode_of_receive']=="Cheque";
            else if($internalorder_data['mode_of_receive']==2)
                $internalorder_data['mode_of_receive']=="RTGS";
            else
                $internalorder_data['mode_of_receive']=="";
        $orig_data=[
            'Reference Name' => $internalorder_data['partyname'],
            'Item Category' =>$internalorder_data['item_category'],
            'Other Item Desc' =>$internalorder_data['other_item_name'],
            'IO Type' =>$internalorder_data['io_type'],
            'Job Date' =>$internalorder_data['job_date'],
            'HSN Code' => $internalorder_data['hsn_name'],
            'Delivery Date' => $internalorder_data['delivery_date'],
            'Job Qty' =>$internalorder_data['qty'],
            'Job Qty Unit' => $internalorder_data['uom_name'],
            'Job Size' =>$internalorder_data['job_size'],
            'Dimension' =>$internalorder_data['dimension'],
            'Job Rate' =>$internalorder_data['rate_per_qty'],
            'Marketing Person' =>$internalorder_data['marketing_name'],
            'Job Details' =>$internalorder_data['details'],
            'Front Color' =>$internalorder_data['front_color'],
            'Back Color' => $internalorder_data['back_color'],
            'Is Supplied Paper' =>$internalorder_data['is_supplied_paper'],
            'Is Supplied Plates' =>$internalorder_data['is_supplied_plate'],
            'Remark' => $internalorder_data['remarks'],
            'Transportation Charges' =>$internalorder_data['transportation_charge'],
            'Other Charges' =>$internalorder_data['other_charge'],
            'Advanced Received' => $internalorder_data['advanced_received']==0?"No":"Yes",
            'Amount' => $internalorder_data['amount'],
            'Mode Received' =>$internalorder_data['mode_of_receive'],
            'Amount Received Date' => $internalorder_data['amount_received_date']
        ];

                // print_r($orig_io);die;
        if($request->input('jobdetail_id')){
            $jobdetail_id=$request->input('jobdetail_id');
            $changes_array=$request->input('jobdetail_id');
            if(isset($jobdetail_id['qty'])){
                $qty_new=$jobdetail_id['qty'];
                $qty_old=$request->input('old_job_qty');
                $qty_left=$request->input('old_leftqty');
                if($qty_new>=($qty_old-$qty_left)){
                    $qtys=$qty_new-($qty_old-$qty_left);
                    $jobdetail_id['left_qty']=$qtys;
                 
                }
                else{
                    return redirect('/internalorder/update/'.$id)->with('error','Qty cannot be less than original qty')->withInput();  
                }
              }

              if (isset($jobdetail_id['advanced_received'])){
                if($jobdetail_id['advanced_received']==1){
                    $jobdetail_id['advanced_received']="Yes";
                    $amount_id['date']=date("Y-m-d", strtotime($request->input('date')));
                    $amount_id['amount']=$request->input('amount');
                    $amount_id['mode_of_receive']=$request->input('adv_received');
                   
                    if($amount_id['mode_of_receive']==0)
                        $amount_id['mode_of_receive']="Cash";
                    else if($amount_id['mode_of_receive']==1)
                        $amount_id['mode_of_receive']="Cheque";
                    else if($amount_id['mode_of_receive']==2)
                        $amount_id['mode_of_receive']="RTGS";
                    else
                        $amount_id['mode_of_receive']=="";
                    $data['amount_id']=$amount_id;
                    if(isset($changes_array)){
                        $changes_array=array_merge($changes_array,$amount_id);
                    }
                    else{
                        $changes_array=$request->input('amount_id'); 
                    }
                }
                else{
                    $jobdetail_id['advanced_received']="No";
                }
            }
           
            if(isset($jobdetail_id['delivery_date'])){
                $jobdetail_id['delivery_date']=date("Y-m-d", strtotime($jobdetail_id['delivery_date']));
            }
            if(isset($jobdetail_id['job_date'])){
                $jobdetail_id['job_date']=date("Y-m-d", strtotime($jobdetail_id['job_date']));
            }
            $data['jobdetail_id']=$jobdetail_id;           
        }
        if($request->input('io_id')){ 
            $io_id=$request->input('io_id');
           
            
            if(isset($io_id['item_category_id'])){
                if($io_id['item_category_id']==15)
                {
                    $other_name = $io_id['other_item_name'];
                    // $other_name = $other_name!=""?$other_name:'';
                    $io_id['other_item_name']=$other_name;
                }
                else{
                    // $io_id['other_item_name'] = "";
                    unset($io_id['other_item_name']);
                    
                }
            }
           if(count($io_id)>0){
            $data['io_id']=$io_id;
           }
           if(isset($changes_array)){
            $changes_array=array_merge($changes_array,$io_id);
            }
            else{
                $changes_array=$request->input('io_id'); 
            }

        }
     
            try {
              
                if(isset($data['io_id']['item_category_id'])){
                    if($data['io_id']['item_category_id']!=15)
                        unset($data['io_id']['other_item_name']);
                    
                }
                else{
                    unset($data['io_id']['other_item_name']);
                }
                // print_r("dsada23=>".$changes_array['other_item_name']);die;
                // print_r($changes_array);die;
                if(isset($jobdetail_id['hsn_code'])){
                    $hsn = Hsn::where('id',$jobdetail_id['hsn_code'])->selectRaw("CONCAT(hsn.item_id,' - ',hsn.hsn,' - ',hsn.gst_rate) as name")->get()->first();
                    $hsn = isset($hsn->name)?$hsn->name:'';
                    $changes_array['hsn_code'] = $hsn;
                }
                if(isset($io_id['reference_name'])){
                    $reference=Reference::where('id',$io_id['reference_name'])->select('referencename')->get()->first();
                    $reference_name = isset($reference->referencename)?$reference->referencename:'';
                    $changes_array['reference_name'] = $reference_name;
                }
                if(isset($io_id['item_category_id'])){
                    $item=ItemCategory::where('id',$io_id['item_category_id'])->select('name')->get()->first();
                    $item = isset($item->name)?$item->name:'';
                    $changes_array['item_category_id'] = $item;
                }
                if(isset($jobdetail_id['job_qty_unit'])){
                    $job_qty_unit=Unit_of_measurement::where('id',$jobdetail_id['job_qty_unit'])->select('uom_name')->get()->first();
                    $job_qty_unit = isset($job_qty_unit->uom_name)?$job_qty_unit->uom_name:'';
                    $changes_array['unit'] = $job_qty_unit;
                }
                if(isset($jobdetail_id['io_type_id'])){
                    $io_type=IoType::where('id',$jobdetail_id['io_type_id'])->select('name')->get()->first();
                    $io_type = isset($io_type->name)?$io_type->name:'';
                    $changes_array['io_type_id'] = $io_type;
                }
                if(isset($jobdetail_id['marketing_user_id'])){
                    $market=MasterMarketingPerson::where('id',$jobdetail_id['marketing_user_id'])->select('name')->get()->first();
                    $market = isset($market->name)?$market->name:'';
                    $changes_array['marketing_user_id'] = $market;
                }

              $log_array=array(
                'hsn_code'=>'HSN Code',
                'reference_name'=>'Reference Name',
                'item_category_id'=>'Item Category',
                'io_type_id'=>'IO Type',
                'other_item_name'=>'Other Item Desc',
                'job_date'=>'Job Date',
                'delivery_date'=>'Delivery Date',
                'qty'=>'Job Qty',
                'unit'=>'Job Qty Unit',
                'job_size'=>'Job Size',
                'dimension'=>'Dimension',
                'rate_per_qty'=>'Job Rate',
                'details'=>'Job Details',
                'marketing_user_id'=>'Marketing Person',
                'front_color'=>'Front Color',
                'back_color'=>'Back Color',
                'is_supplied_paper'=>'Is Supplied Paper',
                'is_supplied_plate'=>'Is Supplied Plates',
                'transportation_charge'=>'Transportation Charges',
                'other_charge'=>'Other Charges',
                'remarks'=>'Remark',
                'advanced_received'=>'Advanced Received',
                'mode_of_receive'=>'Mode Received',
                'date'=>'Amount Received Date',
                'amount'=>'Amount'
              );

              
                    if(isset($data)){
                        foreach ($log_array as $key => $value) {
					
                            if(array_key_exists($key,$changes_array))
                            {
                                $posted_data_array[$value] = $changes_array[$key];
                            }
                        }
                        
                       
                        if(!(isset($posted_data_array['Item Category']))){  
                            unset($posted_data_array['Other Item Desc']);                      
                        }
                        
                        if(isset($posted_data_array['Advanced Received'])){
                            if($posted_data_array['Advanced Received']==1){
                                $posted_data_array['Advanced Received']="Yes";
                            }
                            else{
                                $posted_data_array['Advanced Received']="No";
                            }
                        }

                        if(isset($posted_data_array['Job Qty Unit'])){
                            $job_qty_unit=Unit_of_measurement::where('id',$posted_data_array['Job Qty Unit'])->select('uom_name')->get()->first();
                            $job_qty_unit = isset($job_qty_unit->uom_name)?$job_qty_unit->uom_name:'';
                            $posted_data_array['Job Qty Unit']=$job_qty_unit;
                            
                        }
                       
                        $result=array_intersect_key($orig_data,$posted_data_array);
                        
                        $req=RequestPermission::where('data_id',$id)->where('status','pending')->where('data_for','internalorder')->select('id')->get()->first();
                        if($req){
                            return redirect('/internal/list/open')->with('error','This IO is already in pending request for editing.');
                        }
                        else{
                           
                            $request=RequestPermission::insertGetId([
                                'data_id'=>$id,
                                'data_for'=>'internalorder',
                                'operation'=>'Update Internal Order',
                                'reason'=>$request->input('update_reason'),
                                'requested_by'=>Auth::id(),
                                'io_data'=>json_encode($data),
                                'changes_data'=>json_encode($posted_data_array),
                                'original_data'=>json_encode($result),
                                'status'=>'pending'
                            ]);
                            if($request==NULL){
                                return redirect('addreqiredpermission/internalorder/update/'.$id.'/'.$request->input('update_reason'))
                                ->with('error','Some error Occurred while generating request.Please try again');
                            }
                            else{
                                return redirect('/internal/list/open')->with('success','Update Permission Raised.');
                            }
                        }
                    }  
                    else{
                        return redirect('/addreqiredpermission/internalorder/update/'.$id)->with('error','Please Select Atleast One Field For Edit.');
                    }  
            } catch(\Illuminate\Database\QueryException $ex) {
                return redirect('/addreqiredpermission/internalorder/update/'.$id)->with('error',$ex->getMessage())->withInput();
        }
       
    }
    public function io_edit_auth_req()
    {
        $data=array('layout'=>'layouts.main');
        return view('sections.io_edit_auth_summary', $data);
    
    }
    public function admin_auth_req_api(Request $request)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $client_po = RequestPermission::leftJoin('users','users.id','request_permission.requested_by')

        ->leftJoin('internal_order', function($join) {
			$join->on('internal_order.id', '=', 'request_permission.data_id');
			$join->where('data_for', '=', 'internalorder');
        })
        ->leftJoin('tax_invoice', function($join) {
			$join->on('tax_invoice.id', '=',  'request_permission.data_id');
			$join->where('data_for', '=', 'taxinvoice');
        })
        ->leftJoin('tax_invoice as ti', function($join) {
			$join->on('ti.id', '=',  'request_permission.data_id');
			$join->where('data_for', '=', 'taxinvoicedelete');
        })
        ->leftJoin('tax_invoice as tie', function($join) {
			$join->on('tie.id', '=',  'request_permission.data_id');
			$join->where('data_for', '=', 'taxinvoiceupdate');
        })
        ->leftJoin('delivery_challan', function($join) {
			$join->on('delivery_challan.id', '=',  'request_permission.data_id');
			$join->where('data_for', '=', 'deliverychallan');
        })
        ->leftJoin('delivery_challan as dce', function($join) {
			$join->on('dce.id', '=',  'request_permission.data_id');
			$join->where('data_for', '=', 'deliverychallanupdate');
		})
            ->select(
                'request_permission.reason as rea',
                'io_number',
                'operation',
                'tax_invoice.invoice_number as invoice_number',
                'ti.invoice_number as ti',
                'tie.invoice_number as tie',
                'delivery_challan.challan_number as challan_number',
                'dce.challan_number as dce',
                'users.name as req',
                'request_permission.id as id',
                'request_permission.changes_data',
                'request_permission.original_data',
                'request_permission.status as stat',
                DB::raw('request_permission.created_at')
            );
        if(!empty($serach_value))
        {
            $client_po->where(function($query) use ($serach_value){
                $query->where('request_permission.reason','LIKE',"%".$serach_value."%")
                ->orwhere('request_permission.data_for','like',"%".$serach_value."%")
                ->orwhere('users.name','like',"%".$serach_value."%");
            });                
        }
        
        $count = count($client_po->get()->toArray());
        $client_po = $client_po->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['op','rea','req','io_number',
            'invoice_number','delivery_challan.challan_number','ti.invoice_number','dce.challan_number','tie.invoice_number','stat','operation','id','original_data','changes_data'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $client_po->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $client_po->orderBy('id','desc');
        $client_po= $client_po->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $client_po; 
        return json_encode($array);
    

    }
    public function io_edit_req_update($id,$opr){
      
            if($opr=="allowed"){
                $data=RequestPermission::where('id',$id)->select('io_data','data_id')->get()->first();
                $io_data=json_decode($data['io_data'],true);
                $internal_order_data = InternalOrder::where('id',$data['data_id'])->get()->first();
                $advance_io_id = JobDetails::where('id', $internal_order_data['job_details_id'])->value('advance_io_id');
               
                if(isset($io_data['jobdetail_id'])){
                    
                    if(isset($io_data['jobdetail_id']['rate_per_qty'])){
                        $d=$this->update_rate($internal_order_data['id'],$io_data['jobdetail_id']['rate_per_qty']);
                        if($d==0){
                            return redirect('/internalorder/update/'.$id)->with('error','Some Error occurred.')->withInput();  
                        }
                    }
                    $jobdetail_id=$io_data['jobdetail_id'];
                }
                if($advance_io_id==NULL && isset($io_data['amount_id'])){
                    $amount= advanceIO::insertGetId($io_data['amount_id']);
                    
                }
                else if($advance_io_id!=NULL && isset($io_data['amount_id'])){
                    $amount= advanceIO::where('id',$advance_io_id)->update($io_data['amount_id']);
                }
                else{
                    $amount=NULL;
                }
                $jobdetail_id['advance_io_id']=$amount;
                $job_id= jobDetails::where('id' , $internal_order_data['job_details_id'])->update($jobdetail_id);
                   
                if(isset($io_data['io_id'])){
                    $io_id=$io_data['io_id'];
                    $ioid= InternalOrder::where('id',$data['data_id'])->update($io_id);
                }
               
            }
            RequestPermission::where('id',$id)->update(['status'=>$opr]);
            return array('message'=>'success','mess'=>'Successfully Updated Status');   
        
    }

    //dispatch planning
    public function dispatch_list() {
        $data = array('layout'=>'layouts.main');
         return view('sections.dispatchorder',$data); 
    }
  
     public function dispatch_list_api(Request $request) {
         //DB::enableQueryLog();
         $search = $request->input('search');
         $serach_value = $search['value'];
         $start = $request->input('start');
         $limit = $request->input('length');
         $offset = empty($start) ? 0 : $start ;
         $limit =  empty($limit) ? 10 : $limit ;
         $date=date('Y-m-d');
         $userlog = InternalOrder::where('internal_order.status','Open')
         ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
         ->leftJoin('tax','tax.io_id','internal_order.id')
         ->leftjoin('challan_per_io','challan_per_io.io','internal_order.id')
         ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
         ->leftjoin('party','internal_order.party_id','party.id')
         ->leftJoin('io_type','job_details.io_type_id','io_type.id')
         ->leftJoin('item_category',function($join){
            $join->on('internal_order.item_category_id','=','item_category.id');
       })
    //    ->leftJoin('dispatch_plan','dispatch_plan.io_id','=','internal_order.id')
       ->leftJoin('dispatch_plan',function($join){
        $join->on('dispatch_plan.io_id','=','internal_order.id');
        $join->where('dispatch_plan.date','>=',date('Y-m-d'));
        // $join->where('dispatch_plan.io_id','<>',NULL);
   })
         ->select('internal_order.id',
         'internal_order.io_number',
         'job_details.qty as io_qty',
         'party_reference.referencename',
         'party.partyname','io_type.name',
         'internal_order.created_time',
         DB::raw('(CASE WHEN dispatch_plan.io_id is null THEN "-"
          ELSE (SELECT
                    SUM(dispatch_plan.qty)
                    FROM dispatch_plan
                    WHERE dispatch_plan.io_id = internal_order.id
                    AND dispatch_plan.date >= \''.$date.'\'
                     GROUP BY  dispatch_plan.io_id ) END) as balance'),
         DB::raw('SUM(IFNULL(dispatch_plan.qty,0)) as dispatch_plan_qty'),
         DB::raw('SUM(IFNULL(challan_per_io.good_qty,0)) as dispatch_qty'),
         DB::raw('(job_details.qty - SUM(IFNULL(challan_per_io.good_qty,0)))as remaining_qty'),
         DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as itemss')
         )->havingRaw('(job_details.qty - SUM(IFNULL(challan_per_io.good_qty,0))) > 0')
         ->groupBy('internal_order.id','dispatch_plan.io_id');

         if(!empty($s_date))
         {
             $userlog->where(function($query) use ($s_date){
                 $query->where('dispatch_plan.date','>',date('Y-m-d'));
             });
         } 

         if(!empty($serach_value))
         {
             $userlog = $userlog->where('internal_order.io_number','LIKE',"%".$serach_value."%")
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
                 'internal_order.created_time',
                 'party_reference.referencename',
                 'internal_order.io_number',
                 'dispatch_qty',
                 'remaining_qty',
                 'itemss',
                 'balance'
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
     public function dispatch_plan(Request $request,$id){
        $dis=DispatchPlan::insertGetId([
            'id'=>NULL,
            'io_id'=>$id,
            'qty'=>$request->input('qty'),
            'date'=>date('Y-m-d',strtotime($request->input('date'))),
            'time'=>$request->input('time'),
            'priority'=>$request->input('priority'),
            'created_by'=>Auth::id()
        ]);
        if($dis==NULL){
            return redirect('/dispatch/list')->with('error','some error occurred');
        }
        else{
            return redirect('/dispatch/list')->with('success','Successfully Dispatch Planning Done');
        }
     }

     public function daily_dispatch_report(){
        $data=array('layout'=>'layouts.main');
        return view('sections.daily_dispatch_report',$data);
    }
    public function daily_dispatch_report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $s_date = date("Y-m-d", strtotime($request->input('selectDate')));

        $dailyprocesslog =DispatchPlan::leftJoin('internal_order','internal_order.id','dispatch_plan.io_id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('tax','tax.io_id','internal_order.id')
        ->leftjoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('party','internal_order.party_id','party.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftJoin('item_category',function($join){
                 $join->on('internal_order.item_category_id','=','item_category.id');
            })
        ->leftJoin('delivery_challan',function($join) use ($s_date){
                $join->on('challan_per_io.delivery_challan_id','=','delivery_challan.id');
                $join->where('delivery_challan.delivery_date','=',$s_date);
           })
        ->leftJoin('challan_per_io as day_dc','day_dc.delivery_challan_id','delivery_challan.id')
        ->leftJoin('gatepass',function($join) {
            $join->on('gatepass.challan_id','=','delivery_challan.id');
            $join->where('gatepass.challan_type','=','delivery_challan');
       })
       ->leftJoin('gatepass as gate_mat',function($join){
        $join->on('gate_mat.challan_id','=','delivery_challan.id');
        $join->where('gate_mat.challan_type','=','PPML/DCN/');
        })

        ->leftJoin('material_outward',function($join) use ($s_date){
            $join->on('gatepass.id','=','material_outward.gatepass');
            $join->where('material_outward.date','=',$s_date);
            })
            ->leftJoin('material_outward as material_mat',function($join) use ($s_date){
                $join->on('gate_mat.id','=','material_mat.gatepass');
                $join->where('material_mat.date','=',$s_date);
                })

      
        ->select(
        'internal_order.io_number',
        'internal_order.id as io_id',
        DB::raw('(DATE_FORMAT(dispatch_plan.date ,"%d-%m-%Y")) as date'),
        'dispatch_plan.time',
        'dispatch_plan.priority',
        'dispatch_plan.qty',
        'gate_mat.gatepass_number',
        'delivery_challan.id as dc',
        'delivery_challan.challan_number',
        'day_dc.good_qty as day_dc_id',
        'job_details.qty as io_qty',
         'party_reference.referencename',
         'party.partyname','io_type.name',
         'internal_order.created_time',
         DB::raw('SUM(challan_per_io.good_qty) as dispatch_qty'),
         DB::raw("group_concat(DISTINCT(concat(delivery_challan.challan_number,':',day_dc.good_qty))) as day_dc_qty"),
         DB::raw('(job_details.qty - SUM(challan_per_io.good_qty))as remaining_qty'),
         DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as itemss')
         ,
         DB::raw("group_concat(DISTINCT(concat(delivery_challan.challan_number,':',material_outward.material_outward_number))) as ret"),
         DB::raw("group_concat(DISTINCT(concat(delivery_challan.challan_number,':',material_mat.material_outward_number))) as mat")
         )->groupBy('dispatch_plan.io_id');
      
        if(!empty($s_date))
        {
            $dailyprocesslog->where(function($query) use ($s_date){
                $query->where('dispatch_plan.date','=',$s_date);
            });
        } 
        if(!empty($serach_value))
        {
             $dailyprocesslog->where(function($query) use ($serach_value){
                $query->where('internal_order.io_number','LIKE',"%".$serach_value."%")
                ->orwhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                ->orwhere('job_details.qty','LIKE',"%".$serach_value."%")
                ->orwhere('item_category.name','LIKE',"%".$serach_value."%")
                ->orwhere('internal_order.other_item_name','LIKE',"%".$serach_value."%")
                ->orwhere('dispatch_plan.qty','LIKE',"%".$serach_value."%")
                ->orwhere('dispatch_plan.date','LIKE',"%".$serach_value."%")
                ->orwhere('dispatch_plan.time','LIKE',"%".$serach_value."%")
                ->orwhere('dispatch_plan.priority','LIKE',"%".$serach_value."%")
                ;
        });
         }
        
        $count = count($dailyprocesslog->get()->toArray());
        $dailyprocesslog = $dailyprocesslog->offset($offset)->limit($limit);
 
        if(isset($request->input('order')[0]['column'])){
            $data = [ 'internal_order.io_number',
            'internal_order.id',
            'dispatch_plan.date',
            'dispatch_plan.time',
            'dispatch_plan.priority',
            'dispatch_plan.qty',
            'job_details.qty',
            'day_dc_qty',
             'party_reference.referencename',
             'party.partyname','io_type.name',
             'internal_order.created_time',
             'dispatch_qty','remaining_qty','itemss'
                    ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $dailyprocesslog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $dailyprocesslog->orderBy('dispatch_plan.priority','asc');
        }
        $dailyprocesslog = $dailyprocesslog->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $dailyprocesslog; 
        return json_encode($array);
    }
    public function dispatch_details($id){
        $dis=DispatchPlan::where('io_id',$id)
        ->leftJoin('internal_order','internal_order.id','dispatch_plan.io_id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('item_category',function($join){
           $join->on('internal_order.item_category_id','=','item_category.id');
      })
        ->select('dispatch_plan.*','internal_order.io_number','party_reference.referencename',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as itemss'))->get();
        $data=array(
            'dis'=>$dis,
            'layout'=>'layouts.main'
        );
        return view('sections.dispatch_plan_view',$data);
    }
    // public function dispatch_data($id){
    //     $dc=Challan_per_io::where('challan_per_io.io',$id)
    //     ->leftJoin('delivery_challan','challan_per_io.delivery_challan_id','delivery_challan.id')
    // }
}