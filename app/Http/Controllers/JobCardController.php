<?php

namespace App;
namespace App\Http\Controllers;
use App\Model\ItemCategory;
use App\Model\jobDetails;
use App\Imports\Import;
use Illuminate\Validation\Rule;
use App\Model\IoType;
use App\Model\PoNumber;
use App\Model\Printing;
use App\Model\Tax_Invoice;
use App\Model\FinancialYear;
use App\Model\Reference;
use App\Model\Tax_Dispatch;
use App\Model\Payment;
use App\Model\PlateSize;
use App\Model\Tax;
use App\Model\Unit_of_measurement;
use App\Model\Challan_per_io;
use App\Model\Delivery_challan;
use App\Model\Employee\EmployeeProfile;
use App\Model\Goods_Dispatch;
use App\Model\Settings;
use App\Model\ElementFeeder;
use App\Model\Hsn;
use App\Model\Client_po;
use App\Model\advanceIO;
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
use App\Model\City;
use App\Model\JobDetailsView;
use App\Model\Users;
use App\Model\MasterMarketingPerson;
use App\Model\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\dkerp;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Custom\CustomHelpers;
use \Carbon\Carbon;


class JobCardController extends Controller
{
   
  //--------------------------Job Card Form--------------------------------------------------
  public function jobcard_form()
  {
    $item = ItemCategory::all();
    $io = InternalOrder::where('internal_order.is_active','=',1)
    ->leftJoin('job_card','job_card.io_id','=','internal_order.id')
    ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
    ->whereNotIn('job_details.io_type_id',array('2','3','8','9'))
    ->where('job_card.id','=',NULL)
    ->select('internal_order.id','internal_order.io_number')
    ->get();
    // $users=EmployeeProfile::where('department_id','=',10)->get();
    $users=MasterMarketingPerson::all();

      $data=array('layout'=>'layouts.main','item'=>$item,'io'=>$io,'users'=>$users);
      return view('sections.job_card', $data);
  }
  //--------------------------Job Card Io Integration--------------------------------------------------
public function io($id) {
    $internal_order =InternalOrder::join('job_details','internal_order.job_details_id', '=', 'job_details.id')
                                    ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
                    ->where('internal_order.id',$id)
                    ->get([
                            'internal_order.id',
                            'internal_order.item_category_id',
                            'internal_order.other_item_name',
                            'internal_order.job_details_id',
                            'job_details.qty',
                            'job_details.is_supplied_paper',
                            'job_details.is_supplied_plate',
                            'item_category.name',
                            'job_details.marketing_user_id'
                        ]);

    return $internal_order;
}

  //--------------------------Job Card Integration--------------------------------------------------
  public function jobcard_insert(Request $request)
  {
      try {
          $this->validate($request,
          [
              'internalorder'=>'required',
              'item'=>'required',
              'open_size'=>'required',
              'creative_name'=>'required',
              'qty'=>'required',
              'close_size'=>'required',
              'job_sample'=>'required'

          ]
      );
              
              $qty=$request->input('qty');
              $io_id=$request->input('internalorder');
              $item_category_id=$request->input('item');
              $timestamp = date('Y-m-d G:i:s');
              $settings = Settings::where('name','Job_Card_Prefix')->first();
              $jc_number = $settings->value;
              //financial year
            //   $finan=FinancialYear::get()->last(); 
            //   if($finan){
            //       $year=$finan->financial_year;
            //       $financial_year=$finan['financial_year'];
            //   }
            //   else{
            //       return redirect('/jobcard/create')->with('error','No Financial Year Exist.')->withInput();
            //   }
  
            $fromDate= date('Y-m-d');
            $date=date_create($fromDate);
            $year=CustomHelpers::getFinancialFromDate($fromDate); 
              $old_jc=JobCard::where('financial_year',$year)
              ->get('job_number')->last();  


              if($old_jc)
              {
                $jc=explode('/',$old_jc['job_number']);
                $v = (int)$jc[count($jc)-1];
                $jc_id=$v+1;   
                }
                else{
                    $jc_id=1;
                }

             
              $jc_number = $settings->value;
              $jc_number = $jc_number ."/".$year.'/'.$jc_id;
              $prefix = $jc_number;

              $jc_id= JobCard::insertGetId(
                  [
                      'id' => NULL,
                      'job_number'=>$prefix,
                      'financial_year'=>$year,
                      'io_id' =>$request->input('internalorder'),
                      'creative_name' => $request->input('creative_name'),
                      'job_qty' =>$request->input('qty'),
                      'open_size' => $request->input('open_size'),
                      'close_size' =>$request->input('close_size'),
                      'dimension'=>$request->input('dimension'),
                      'job_sample_received' => $request->input('job_sample'),
                      'remarks' => NULL,
                      'item_category_id' =>$request->input('item'),
                      'other_item_desc' =>$request->input('item_desc'),
                      'description' =>$request->input('desc'),
                      'created_by' =>Auth::id(),
                      'is_active' =>1,
                      'created_time' => $timestamp
                  ]
              );
              if ( ! $jc_id)
              {
                  return redirect('/jobcard/create')->with('error','some error occurred')->withInput();
              }
             
              
              // if($item_category_id < 6 || $item_category_id == 15){
                  return redirect('/element/create'.'/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id]);
              // }
              // else{
              //     return redirect('/rawmaterial/create'.'/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id]);
              // }
              

      } catch(\Illuminate\Database\QueryException $ex) {
          return redirect('/jobcard/create')->with('error',$ex->getMessage())->withInput();
      }
  }
//------------------------------------------Element Form------------------------------------------------------
public function element_form($jc_id,$io_id){
  $item = ItemCategory::all();
  $plate_size=PlateSize::all();
  $io = JobCard::leftJoin('internal_order','job_card.io_id','=','internal_order.id')
          ->get('internal_order.id');
  $element=ElementType::get();
 
  $element_detail=ElementFeeder::where('job_card_id', $jc_id)->get(['element_type_id','id']);  
  $elem_count=count($element_detail);
  $job=JobCard::where('id',$jc_id)->get();
  $job_count=count($job);
  $data=array('layout'=>'layouts.main','item'=>$item,'io'=>$io,'element'=>$element,'jc_id'=>$jc_id,'io_id'=>$io_id,'elem_count'=>$elem_count,'plate_size'=>$plate_size);
  if($elem_count!=0){
      return redirect('/rawmaterial/create'.'/'.$jc_id.'/'.$io_id)->with('info','element form has already been created');
  }
  else if($job_count==0){
      return redirect('/jobcard/create')->with('info','First create job card!!!');
     
  }
  else{
      return view('sections.element', $data);
  }
      
 
}

public function job_card_details($id){
  $io = JobCard::where('io_id',$id)
              ->join('item_category','job_card.item_category_id', '=', 'item_category.id')
              ->get(['job_card.id','job_card.remarks','job_card.item_category_id','item_category.name','item_category.elements']);
  return $io;

}

//------------------------------------------Raw Material Form------------------------------------------------------
public function raw_material_form($jc_id,$io_id){
  $item = ItemCategory::all();
  $io = JobCard::leftJoin('internal_order','job_card.io_id','=','internal_order.id')
  ->get('internal_order.id');
  $paper = PaperType::all();
  $element=ElementType::get();
  $element_detail=Raw_Material::where('job_card_id', $jc_id)->get(['element_type_id','id']);
  $elem_count=count($element_detail);
  $job=JobCard::where('id',$jc_id)->get();
  $job_count=count($job);
  $data=array('layout'=>'layouts.main','item'=>$item,'io'=>$io,'element'=>$element,'paper'=>$paper,'jc_id'=>$jc_id,'io_id'=>$io_id,'elem_count'=>$elem_count);
  if($elem_count!=0){
      return redirect('/binding/create'.'/'.$jc_id.'/'.$io_id)->with('info','raw material form has already been created');
  }
  else if($job_count==0){
      return redirect('/jobcard/create')->with('info','First create job card!!!');
     
  }
  else{
      return view('sections.raw_material', $data);
  }
      
}
//------------------------------------------Element Form Insert------------------------------------------------------
public function element_insert(Request $request){
  try
    {
      $validator = Validator::make($request->all(),
      [
          'elem_type' => 'required',
          'plate_size' => 'required',
          'plate_sets' => 'required',
          'impression_plate_sets' => 'required',
          'front_color' => 'required',
          'back_color' => 'required'

      ],
      [
          'elem_type.required'    => 'Atleast one element Detail is required'
      ]
      );
    
      $id=Auth::id();
      $timestamp = date('Y-m-d G:i:s');
      $io_id= $request->input('io_id');
      $jc_id= $request->input('jc_id');
      $elem_type= $request->input('elem_type');
      $plate_size= $request->input('plate_size');
      $plate_sets= $request->input('plate_sets');
      $impression_plate_sets= $request->input('impression_plate_sets');
      $front_color= $request->input('front_color');
      $back_color= $request->input('back_color');
      $no_of_pages= $request->input('no_of_pages');

      $errors = $validator->errors();
       if ($validator->fails()) {
          return redirect('/element/create'.'/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id])->withErrors($errors);
      }
      $element=ElementFeeder::where('job_card_id', $jc_id)->get(['element_type_id','id']);
      $elem_count=count($element);
      $job=JobCard::where('id',$jc_id)->get();
      $job_count=count($job);
      if($job_count==0){
          return redirect('/jobcard/create')->with('info','First create job card!!!');
         
      }

      if(!($elem_count == 0))
      {
          return redirect('/rawmaterial/create'.'/'.$jc_id.'/'.$io_id)->with('info','raw material form has already been created');
      }
  else{
      $count=count($request->input('elem_type'));
      for($i=0;$i<$count;$i++){
          ElementFeeder::insert(
              [
                  'id' => NULL,
                  'element_type_id' => @$elem_type[$i],
                  'job_card_id' =>@$jc_id,
                  'plate_size' =>@$plate_size[$i],
                  'plate_sets' => @$plate_sets[$i],
                  'impression_per_plate' =>@$impression_plate_sets[$i],
                  'front_color' => @$front_color[$i],
                  'back_color' =>@$back_color[$i],
                  'no_of_pages' => @$no_of_pages[$i],
                  'created_by' =>@$id,
                  'created_time' =>@$timestamp
              ]

          );

      }
  }

      return redirect('/rawmaterial/create'.'/'.$jc_id.'/'.$io_id)->with('success','successfully created element form');
  }   
  catch(\Illuminate\Database\QueryException $ex) {
      return redirect('/element/create'.'/'.$jc_id.'/'.$io_id)->with('error',$ex->getMessage());

  }
}
//------------------------------------------Raw Material Form Insert------------------------------------------------------
public function raw_material_insert(Request $request){
    try
    {
        // print_r($request->input());die;
        $this->validate($request,[
            'is_paper.*'=>'required',
        ],[
            'is_paper.*.required'=> 'Please Select any one option.'
        ]); 
      $id=Auth::id();
      $timestamp = date('Y-m-d G:i:s');
      $io_id= $request->input('io_id');
      $jc_id= $request->input('jc_id');
      $elem_type= $request->input('elem_type');
      $paper_size= $request->input('paper_size');
      $paper_type= $request->input('paper_type');
      $paperGSM= $request->input('paperGSM');
      $sheets= $request->input('sheets');
      $paper_brand= $request->input('paper_brand');
      $paper_mill= $request->input('paper_mill');
      $item_name= $request->input('item_name');
      $item_size= $request->input('item_size');
      $size_unit= $request->input('size_unit');
      $item_thick= $request->input('item_thick');
      $thick_unit= $request->input('thick_unit');
      $specification= $request->input('specification');
      $is_paper= $request->input('is_paper');

  
      $count=count($request->input('elem_type'));
      $job=JobCard::where('id',$jc_id)->get();
      $job_count=count($job);
    $element=Raw_Material::where('job_card_id', $jc_id)->get(['element_type_id','id']);
    $elem_count=count($element);
    if($job_count==0){
        return redirect('/jobcard/create')->with('info','First create job card!!!');  
    }
    if(!($elem_count == 0))
    {
        return redirect('/binding/create'.'/'.$jc_id.'/'.$io_id)->with('info','raw material form has already been created');
    }
    else{
       
        for($i=0;$i<$count;$i++){
                if($is_paper[$i]=='Paper'){
                    $element_feeder= Raw_Material::insertGetId([
                        'id' => NULL,
                        'job_card_id' =>$jc_id,
                        'element_type_id' => $elem_type[$i],
                        'is_option'=>$is_paper[$i],
                        'paper_size' => $paper_size[$i],
                        'paper_type_id' =>$paper_type[$i],
                        'paper_gsm' => $paperGSM[$i],
                        'paper_mill' =>$paper_mill[$i],
                        'paper_brand' => $paper_brand[$i],
                        'no_of_sheets' => $sheets[$i],
                        'is_option'=>$is_paper[$i],
                        'item_name' => NULL,
                        'size' => NULL,
                        'size_dimension' => NULL,
                        'thickness' => NULL,
                        'thickness_dimension' => NULL,
                        'specification' => NULL,
                        'created_by' =>$id,
                        'created_time' =>$timestamp,
                    ]);
                } 
                if($is_paper[$i]=='Other'){
                    $element_feeder= Raw_Material::insertGetId([
                        'id' => NULL,
                        'job_card_id' =>$jc_id,
                        'element_type_id' => $elem_type[$i],
                        'paper_size' => NULL,
                        'paper_type_id' =>NULL,
                        'paper_gsm' => NULL,
                        'paper_mill' =>NULL,
                        'paper_brand' => NULL,
                        'no_of_sheets' => NULL,
                        'item_name' => @$item_name[$i],
                        'size' => $item_size[$i],
                        'size_dimension' => $size_unit[$i],
                        'thickness' => @$item_thick[$val],
                        'thickness_dimension' => $thick_unit[$i],
                        'specification' => $specification[$i],
                        'is_option'=>$is_paper[$i],
                        'created_by' =>$id,
                        'created_time' =>$timestamp,
                    ]);
                }
                if($element_feeder==NULL){
                    DB::rollback();
                    return redirect('/rawmaterial/update'.'/'.$jc_id.'/'.$io_id)->with('error','Some error occurred'); 
                }
        }
    }
    
      
      return redirect('/binding/create'.'/'.$jc_id.'/'.$io_id)->with('success','successfully created raw material form');
  }
  catch(\Illuminate\Database\QueryException $ex) {
      return redirect('/rawmaterial/create'.'/'.$jc_id.'/'.$io_id)->with('error',$ex->getMessage());
  
  }
}
//-----------------------------------------Binding Form------------------------------------------------------
public function binding_form($jc_id,$io_id){
  $item = ItemCategory::all();
  $labels = Binding_form_labels::all();
  $binding_item = Binding_item::all();
  $io = JobCard::leftJoin('internal_order','job_card.io_id','=','internal_order.id')
  ->get('internal_order.id');
  $paper = PaperType::all();
  $element=ElementType::get();
  $element_detail=Binding_detail::where('job_card_id', $jc_id)->get(['element_type_id','id']);
  $elem=JobCard::where('id', $jc_id)->get('item_category_id')->first();
  $elem_count=count($element_detail);
  $data=array(
      'layout'=>'layouts.main',
      'item'=>$item,
      'labels'=>$labels,
      'binding_item'=>$binding_item,
      'io'=>$io,
      'element'=>$element,
      'paper'=>$paper,
      'jc_id'=>$jc_id,
      'io_id'=>$io_id,
      'elem'=>$elem,
      'elem_count'=>$elem_count
  );
  $job=JobCard::where('id',$jc_id)->get();
  $job_count=count($job);
  $elem_count=0;
  if($elem_count!=0){
      return redirect('/jobcard/create')->with('info','This Form has already been created');
  }
  else if($job_count==0){
      return redirect('/jobcard/create')->with('info','First create job card!!!');
     
  }
  else{
      return view('sections.binding_form', $data);
  }
  
}
//------------------------------------------Binding Form Insert------------------------------------------------------
public function binding_insert(Request $request){
  try
  {
    //   print_r($request->input());die;
      $validator = Validator::make($request->all(),
      [
          'elem_type' => 'required'
      ],
      [
          'elem_type.required' => 'Atleast one element Detail is required'
      ]
      );
      
      $errors = $validator->errors();
      if ($validator->fails()) 
      {
          return redirect('/binding/create')->with(['io_id'=>$io_id,'jc_id'=>$jc_id])
                      ->withErrors($errors);
      }
      $id=Auth::id();
      $jc_id= $request->input('jc_id');
      $io_id= $request->input('io_id');$elem_type= $request->input('elem_type');
      $timestamp = date('Y-m-d G:i:s');
      $element=Binding_detail::where('job_card_id', $jc_id)->get(['element_type_id','id']);
      $elem_count=count($element);
      $job=JobCard::where('id',$jc_id)->get();
      $job_count=count($job);
      $job=JobCard::where('id',$jc_id)->get()->first();
      $prefix=$job->job_number;
      if($job_count==0){
          return redirect('/jobcard/create')->with('info','First create job card!!!');
         
      }
      if(!($elem_count == 0))
      {
          return redirect('/jobcard/create')->with('info','binding details form has already been created');
      }
      $count=count($request->input('elem_type'));
      
      for($i=0;$i<$count;$i++){
          $items= $request->input($elem_type[$i]);
          $output = array_except($items, ['remark']);
          $bind=json_encode($output);
          $getremark = $items['remark'];
          $remark=json_encode($getremark);
      
          $binding_details= Binding_detail::insert(
              [
                  'id' => NULL,
                  'value' =>$bind,
                  'remark' =>$remark,
                  'job_card_id' =>@$jc_id,
                  'element_type_id' => @$elem_type[$i],
                  'created_by' =>@$id,
                  'created_time' =>@$timestamp
              ]

          );
          
      }
      JobCard::where('id',$jc_id)->update(
        [
            'remarks' => $request->input('remark')
        ]
    );
     
  //   return redirect('/printing'.'/'.$jc_id.'/'.$io_id)->with('success','successfully created binding form');
  return redirect('/jobcard/create')->with(['message'=>'successfull','jc_id'=>$jc_id,'prefix'=>$prefix]); 
  }
catch(\Illuminate\Database\QueryException $ex) {
  return redirect('/binding/create'.'/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id,'error'=>$ex->getMessage()]);
}
}
// ----------------------------------------Update JOB CARD------------------------------------------------------
public function jobcard_update($id)
    {

        $item = ItemCategory::all();
      $io = InternalOrder::select('internal_order.id','internal_order.io_number')->get();
    //   $users=EmployeeProfile::where('department_id','=',10)->get();
    $users=MasterMarketingPerson::all();
     $job=JobCard::where('job_card.id',$id)
     ->leftJoin('internal_order','internal_order.id','=','job_card.io_id') 
     ->leftJoin('job_details','job_details.id','=','internal_order.job_details_id')
     ->where('job_card.status','Open')
     ->select(
         'job_card.id as job_id',
         'job_card.io_id',
         'internal_order.other_item_name',
         'job_card.creative_name',
         'job_card.job_qty',
         'job_card.open_size',
         'job_card.close_size',
         'job_card.job_sample_received',
         'job_card.remarks',
         'job_card.item_category_id',
         'job_card.other_item_desc',
         'job_card.dimension',
         'job_card.description',
         'job_details.qty',
         'job_details.marketing_user_id'
     )->first();
     if($job){
        $data=array('layout'=>'layouts.main','item'=>$item,'io'=>$io,'users'=>$users,'job'=>$job);
        return view('sections.jobcard_update',$data);
     }
     else{
        $message="Job Card has been closed or not exist!!";
        return redirect('/JobCard/list/open')->with('error',$message);
     }
      
    }
    public function jobcardupdateDB(Request $request, $id){
        try {
            CustomHelpers::userActionLog($request->input()['update_reason'],$id,"JobCard Update");
            $this->validate($request,
          [
              'internalorder'=>'required',
              'open_size'=>'required',
              'creative_name'=>'required',
              'qty'=>'required',
              'close_size'=>'required',
              'update_reason'=>'required',
              'desc'=>'required'

          ]
      );

            
              $qty=$request->input('qty');
              $io_id=$request->input('internalorder');
              $item_category_id=$request->input('item');
              $timestamp = date('Y-m-d G:i:s');
              $jc_id= JobCard::where('id' , $id)->update(
                  [
                     
                      
                      'creative_name' => $request->input('creative_name'),
                      'job_qty' =>$request->input('qty'),
                      'open_size' => $request->input('open_size'),
                      'close_size' =>$request->input('close_size'),
                      'dimension'=>$request->input('dimension'),
                      'job_sample_received' => $request->input('job_sample'),
                      'remarks' => NULL,
                      'description' =>$request->input('desc'),
                      'updated_at'=>$timestamp
                  ]
              );
             
           
                  return redirect('/jobcardform/update'.'/'.$id)->with(['jc_id'=>$id,'success'=>'Successfully Updated']);
             
      } catch(\Illuminate\Database\QueryException $ex) {
          return redirect('/jobcardform/update'.'/'.$id)->with('error',$ex->getMessage());
      }
    }
//-------------------------------update element form----------------------------------------------------------
public function elementformupdate($jc_id,$io_id){
    $item = ItemCategory::all();
    $plate_size=PlateSize::all();
    $io = JobCard::leftJoin('internal_order','job_card.io_id','=','internal_order.id')
            ->get('internal_order.id');
    $element=ElementType::get();
    $elem=ElementFeeder::where('element_feeder.job_card_id',$jc_id)->get();
    $count=count($elem);
    $job=JobCard::where('id',$jc_id)->where('job_card.io_id','=',$io_id)->get();
    $job_count=count($job);
    $data=array('layout'=>'layouts.main','item'=>$item,'io'=>$io,'element'=>$element,'jc_id'=>$jc_id,'io_id'=>$io_id,'elem'=>$elem,'count'=>$count,'plate_size'=>$plate_size);
      
    if($job_count!=0){
        if($count!=0){
            return view('sections.element_update', $data);
        }
        else{
            return redirect('/element/create'.'/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id,'info'=>'First Create Raw Material Form!!']) ; 
        }
        }
        else{
            return redirect('/jobcardform/update'.'/'.$jc_id)->with(['id'=>$jc_id,'info'=>'Incorrect Job Id or I.O. Id ..']); 
        }
   
  }   
  public function elementformupdateDB(Request $request,$jc_id,$io_id) {
      try
      {
        CustomHelpers::userActionLog($request->input()['update_reason'],$jc_id,"Element Form Update");
        $validator = Validator::make($request->all(),
        [
           
            'plate_size' => 'required',
            'plate_sets' => 'required',
            'impression_plate_sets' => 'required',
            'front_color' => 'required',
            'back_color' => 'required'
  
        ]
       
        );
      
      
        $timestamp = date('Y-m-d G:i:s');
        $plate_size= $request->input('plate_size');
        $plate_sets= $request->input('plate_sets');
        $impression_plate_sets= $request->input('impression_plate_sets');
        $front_color= $request->input('front_color');
        $back_color= $request->input('back_color');
        $no_of_pages= $request->input('no_of_pages');
        $io_id= $request->input('io_id');
        $jc_id= $request->input('jc_id');
        $elem_type=$request->input('elem_type');
       
  
        $errors = $validator->errors();
         if ($validator->fails()) {
            return redirect('elementform/update/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id])->withErrors($errors);
        }
        $old_elem=$request->input('old_elem');
        $old_id=$request->input('old_id');
        $count=count($old_elem);
        for($i=0;$i<$count;$i++){
            $val=$old_elem[$i];
            ElementFeeder::where('id','=',$old_id[$i])->update(
                [
                   
                    'plate_size' =>$plate_size[$val],
                    'plate_sets' => $plate_sets[$val],
                    'impression_per_plate' =>$impression_plate_sets[$val],
                    'front_color' => $front_color[$val],
                    'back_color' =>$back_color[$val],
                    'no_of_pages' => $no_of_pages[$val],
                    'updated_at' =>$timestamp
                ]
            );
        }
       $new_elem=$request->input('new_elem');
       if(isset($new_elem)){
        $count1= count($new_elem);     
        for($i=0;$i<$count1;$i++){
           $val1=$new_elem[$i];
            ElementFeeder::insert(
                [
                    'id' => NULL,
                    'element_type_id' => $new_elem[$i],
                    'job_card_id' =>$jc_id,
                    'plate_size' => $plate_size[$val1],
                    'plate_sets' => $plate_sets[$val1],
                    'impression_per_plate' => $impression_plate_sets[$val1],
                    'front_color' => $front_color[$val1],
                    'back_color' => $back_color[$val1],
                    'no_of_pages' => $no_of_pages[$val1],
                    'created_by' =>Auth::id(),
                    'created_time' => $timestamp
                ]
  
            );
  
        }
       }
    
  
       return redirect('elementform/update/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id,'success'=>'Element Form Successfully Updated']);
    }   
    catch(\Illuminate\Database\QueryException $ex) {
        return redirect('elementform/update/'.$jc_id.'/'.$io_id)->with('error',$ex->getMessage());
  
    }

  }
  //-------------------------------update raw material form----------------------------------------------------------
public function rawformupdate($jc_id,$io_id){
    $item = ItemCategory::all();
  $io = JobCard::leftJoin('internal_order','job_card.io_id','=','internal_order.id')
  ->get('internal_order.id');
  $paper = PaperType::all();
  $element=ElementType::get();
  $element_detail=Raw_Material::where('job_card_id', $jc_id)->select('raw_material.*')->get();
 
  $count=count($element_detail);
  $job=JobCard::where('id',$jc_id)->where('job_card.io_id','=',$io_id)->get();

$job_count=count($job);
  $data=array('layout'=>'layouts.main','item'=>$item,'io'=>$io,'element'=>$element,'jc_id'=>$jc_id,'io_id'=>$io_id,'element_detail'=>$element_detail,'paper'=>$paper,'count'=>$count);
if($job_count!=0){
  if($count!=0){
    //   return $element_detail;
    return view('sections.raw_update', $data);
  }
  else{
    return redirect('/rawmaterial/create'.'/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id,'info'=>'First Create Raw Material Form!!']) ; 
  }
}
else{
    return redirect('/jobcardform/update'.'/'.$jc_id)->with(['id'=>$jc_id,'info'=>'Incorrect Job Id or I.O. Id ..']); 
}
   
  }   
  public function rawformupdateDB(Request $request,$jc_id,$io_id) {
    //   print_r($request->input());die;
      try
      {
        CustomHelpers::userActionLog($request->input()['update_reason'],$jc_id,"Raw Material Form Update");
        $validator = Validator::make($request->all(),
        [
           
            'paper_size' => 'required',
          'paper_type' => 'required',
          'paperGSM' => 'required',
          'sheets' => 'required'
  
        ]
       
        );
        $id=Auth::id();
        $timestamp = date('Y-m-d G:i:s');
        $io_id= $request->input('io_id');
        $jc_id= $request->input('jc_id');
        $elem_type= $request->input('elem_type');
        $paper_size= $request->input('paper_size');
        $paper_type= $request->input('paper_type');
        $paperGSM= $request->input('paperGSM');
        $sheets= $request->input('sheets');
        $paper_brand= $request->input('paper_brand');
        $paper_mill= $request->input('paper_mill');

        $item_name= $request->input('item_name');
      $item_size= $request->input('item_size');
      $size_unit= $request->input('size_unit');
      $item_thick= $request->input('item_thick');
      $thick_unit= $request->input('thick_unit');
      $specification= $request->input('specification');
      $is_paper= $request->input('is_paper');
  
        $errors = $validator->errors();
         if ($validator->fails()) {
            return redirect('rawform/update/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id])->withErrors($errors);
        }
        $old_elem=$request->input('old_elem');
        $old_id=$request->input('old_id');
        $count=count($old_elem);
        for($i=0;$i<$count;$i++){
            $val=$old_elem[$i];
            $paper_data=array(
                'paper_size' => $paper_size[$val],
                'paper_type_id' =>$paper_type[$val],
                'paper_gsm' => $paperGSM[$val],
                'paper_mill' =>$paper_mill[$val],
                'paper_brand' => $paper_brand[$val],
                'no_of_sheets' => $sheets[$val],
                'is_option'=>@$is_paper[$val],
                'item_name' => NULL,
                'size' => NULL,
                'size_dimension' => NULL,
                'thickness' => NULL,
                'thickness_dimension' => NULL,
                'specification' => NULL,
            );
            $other_data=array(
                'paper_size' => NULL,
                'paper_type_id' =>NULL,
                'paper_gsm' => NULL,
                'paper_mill' =>NULL,
                'paper_brand' => NULL,
                'no_of_sheets' => NULL,

                'item_name' => @$item_name[$val],
                'size' => @$item_size[$val],
                'size_dimension' => @$size_unit[$val],
                'thickness' => @$item_thick[$val],
                'thickness_dimension' => @$thick_unit[$val],
                'specification' => @$specification[$val],
                'is_option'=>@$is_paper[$val],
            );
            if($is_paper[$val]=='Paper'){
                $paper_data=array_merge($paper_data,array(
                ));
                $element_feeder= Raw_Material::where('id','=',$old_id[$i])->update($paper_data);
                if($element_feeder==NULL){
                    DB::rollback();
                    return redirect('/rawmaterial/update'.'/'.$jc_id.'/'.$io_id)->with('error','Some error occurred'); 
                }
            } 
            else if($is_paper[$val]=='Other'){
           
                $element_feeder= Raw_Material::where('id','=',$old_id[$i])->update($other_data);
                if($element_feeder==NULL){
                    DB::rollback();
                    return redirect('/rawmaterial/update'.'/'.$jc_id.'/'.$io_id)->with('error','Some error occurred'); 
                }
            }
            else{
                return redirect('/rawmaterial/update'.'/'.$jc_id.'/'.$io_id)->with('error','Some error occurred'); 
            }
        }
       $new_elem=$request->input('new_elem');
       if(isset($new_elem)){
        $count1= count($new_elem);     
        for($i=0;$i<$count1;$i++){
           $val1=$new_elem[$i];
           $paper_data1=array(
            'paper_size' => $paper_size[$val1],
            'paper_type_id' =>$paper_type[$val1],
            'paper_gsm' => $paperGSM[$val1],
            'paper_mill' =>$paper_mill[$val1],
            'paper_brand' => $paper_brand[$val1],
            'no_of_sheets' => $sheets[$val1]
        );
        $other_data1=array(
            'item_name' => @$item_name[$val1],
            'size' => @$item_size[$val1],
            'size_dimension' => @$size_unit[$val1],
            'thickness' => @$item_thick[$val1],
            'thickness_dimension' => @$thick_unit[$val1],
            'specification' => @$specification[$val1],
        );
        if($is_paper[$val1]=='Paper'){
            $paper_data1=array_merge($paper_data1,array(
                'id' => NULL,
                'job_card_id' =>@$jc_id,
                'element_type_id' => @$elem_type[$i],
                'is_option'=>@$is_paper[$i],
                'created_by' =>@$id,
                'created_time' =>@$timestamp,
            ));
            $element_feeder= Raw_Material::insert($paper_data1);
            if($element_feeder==NULL){
                DB::rollback();
                return redirect('/rawmaterial/update'.'/'.$jc_id.'/'.$io_id)->with('error','Some error occurred'); 
            }
        } 
        else if($is_paper[$val1]=='Other'){
       
            $other_data1=array_merge($other_data1,array(
                'id' => NULL,
                  'job_card_id' =>@$jc_id,
                  'element_type_id' => @$new_elem[$i],
                'created_by' =>@$id,
                'created_time' =>@$timestamp,
            ));
            $element_feeder= Raw_Material::insert($other_data1);
            if($element_feeder==NULL){
                DB::rollback();
                return redirect('/rawmaterial/update'.'/'.$jc_id.'/'.$io_id)->with('error','Some error occurred'); 
            }
        }
        else{
            return redirect('/rawmaterial/update'.'/'.$jc_id.'/'.$io_id)->with('error','Some error occurred'); 
        }
        }
       }
    
  
       return redirect('rawform/update/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id,'success'=>'Raw Material Form Successfully Updated']);
    }   
    catch(\Illuminate\Database\QueryException $ex) {
        return redirect('rawform/update/'.$jc_id.'/'.$io_id)->with('error',$ex->getMessage());
  
    }

  }


   //-------------------------------update Binding form----------------------------------------------------------
public function bindingformupdate($jc_id,$io_id){
    $item = ItemCategory::all();
    $labels = Binding_form_labels::all();
    $binding_item = Binding_item::all();
    $io = JobCard::leftJoin('internal_order','job_card.io_id','=','internal_order.id')
    ->get('internal_order.id');
    $paper = PaperType::all();
    $element=ElementType::get();
    $elem=JobCard::where('id', $jc_id)->get('item_category_id')->first();
    $element_detail=Binding_detail::where('job_card_id', $jc_id)->get();
    $elem_count=count($element_detail);
    $data=array(
        'layout'=>'layouts.main',
        'item'=>$item,
        'labels'=>$labels,
        'binding_item'=>$binding_item,
        'io'=>$io,
        'element'=>$element,
        'element_detail'=>$element_detail,
        'paper'=>$paper,
        'jc_id'=>$jc_id,
        'io_id'=>$io_id,
        'elems'=>$elem,
        'elem_count'=>$elem_count
    );
    //print($element_detail);die();
    $job=JobCard::where('id',$jc_id)->get();
    $job_count=count($job);
if($job_count!=0){
  if($elem_count!=0){
    return view('sections.binding_update', $data);
  }
  else{
    return redirect('/rawmaterial/create'.'/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id,'info'=>'First Create Raw Material Form!!']) ; 
  }
}
else{
    return redirect('/jobcardform/update'.'/'.$jc_id)->with(['id'=>$jc_id,'info'=>'Incorrect Job Id or I.O. Id ..']); 
}
   
  }   
  public function bindingformupdateDB(Request $request,$jc_id,$io_id) {
      try
      {
        // print_r($request->input());die;
         CustomHelpers::userActionLog($request->input()['update_reason'],$jc_id,"Binding Form Update");
        
        $id=Auth::id();
        $jc_id= $request->input('jc_id');
        $io_id= $request->input('io_id');

        $timestamp = date('Y-m-d G:i:s');
  
        $old_elem=$request->input('old_elem');
        $old_id=$request->input('old_id');
        $count=count($old_elem);
        for($i=0;$i<$count;$i++){
            $val=$old_elem[$i];
            $items= $request->input($val);
            $output = array_except($items, ['remark']);
            $bind=json_encode($output);
            $getremark = $items['remark'];
            $remark=json_encode($getremark);

            Binding_detail::where('id','=',$old_id[$i])->update(
                [
                  
                    'value' =>$bind,
                    'remark' =>$remark,
                    'updated_at'=>$timestamp
                ]
            );
        }
       $new_elem=$request->input('elem_type');
       if(isset($new_elem)){
        $count1= count($new_elem);     
        for($i=0;$i<$count1;$i++){

           $val1=$new_elem[$i];
           $items= $request->input($val1);
           $output = array_except($items, ['remark']);
           $bind=json_encode($output);
           $getremark = $items['remark'];
           $remark=json_encode($getremark);
       
           $binding_details= Binding_detail::insert(
               [
                   'id' => NULL,
                   'value' =>$bind,
                   'remark' =>$remark,
                   'job_card_id' =>@$jc_id,
                   'element_type_id' => @$val1,
                   'created_by' =>@$id,
                   'created_time' =>@$timestamp
               ]
 
           );
        }
       }
       JobCard::where('id',$jc_id)->update(
        [
            'remarks' => $request->input('remark')
        ]
    );
    
  
       return redirect('bindingform/update/'.$jc_id.'/'.$io_id)->with(['io_id'=>$io_id,'jc_id'=>$jc_id,'success'=>'Binding Form Successfully Updated']);
    }   
    catch(\Illuminate\Database\QueryException $ex) {
        return redirect('bindingform/update/'.$jc_id.'/'.$io_id)->with('error',$ex->getMessage());
  
    }

  }
//-------------------------------------------Job Card Summary--------------------------------------------------
public function job_card_list(Request $request,$status)
{
    $data=array('layout'=>'layouts.main');
    if($status=='*')
       $status='open';
    $data['status'] = $status;
    return view('sections.job_card_summary', $data);
}
public function job_card_api(Request $request,$status)
{
    $search = $request->input('search');
    $serach_value = $search['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $offset = empty($start) ? 0 : $start ;
    $limit =  empty($limit) ? 10 : $limit ;
    
    if($status=='open' || $status=='closed'){
        $jobdata = JobCard::where('job_card.is_active',1)->leftJoin('internal_order', function($join) {
            $join->on('internal_order.id', '=', 'job_card.io_id');
        })->leftJoin('party_reference',function($join){
            $join->on('internal_order.reference_name','=','party_reference.id');
        })
        ->leftJoin('item_category',function($join){
            $join->on('job_card.item_category_id','=','item_category.id');
            
        })
        ->leftJoin('binding_details',function($join){
            $join->on('job_card.id','=','binding_details.job_card_id');
            
        })
   
        ->where('job_card.status','like',$status)->where('binding_details.id','!=',NULL);
    }
    if($status=='incomplete'){
        $jobdata = JobCard::where('job_card.is_active',1)->leftJoin('internal_order', function($join) {
            $join->on('internal_order.id', '=', 'job_card.io_id');
        })->leftJoin('party_reference',function($join){
            $join->on('internal_order.reference_name','=','party_reference.id');
        })
        ->leftJoin('item_category',function($join){
            $join->on('job_card.item_category_id','=','item_category.id');
            
        })
        ->leftJoin('binding_details',function($join){
            $join->on('job_card.id','=','binding_details.job_card_id');
            
        })
    
       ->where('binding_details.id','=',NULL);
    }
    if(!empty($serach_value))
    {
        $jobdata->where(function($query) use ($serach_value){
            $query->where('job_card.job_number','LIKE',"%".$serach_value."%")
            ->orWhere('party_reference.referencename','LIKE',"%".$serach_value."%")
            ->orWhere('internal_order.io_number','LIKE',"%".$serach_value."%")
            ->orWhere('item_category.name','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.created_time','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.job_qty','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.open_size','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.close_size','LIKE',"%".$serach_value."%");
                });
 
    }
    // 
    if(isset($request->input('order')[0]['column']))
    {
        $data = [
            'id',
            'job',
            'item_name',
            'io',
            'partyname',
            'date',
            'name',
            'creative',
            'qty',
            'open',
            'close',
            'sample'
            ];
        $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
        $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
    }
    else
        $jobdata->orderBy('job_card.id','desc');
    $count = count($jobdata->select( 
    DB::raw('group_concat(DISTINCT(job_card.id)) as ids'),
    DB::raw('group_concat(DISTINCT(job_card.job_number)) as job'),
    DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io'),
    DB::raw('group_concat(DISTINCT(party_reference.referencename)) as partyname'),
    DB::raw('group_concat(DISTINCT(job_card.created_time)) as date'),
    DB::raw('group_concat(DISTINCT(item_category.name)) as name'),
    DB::raw('group_concat(DISTINCT(job_card.creative_name)) as creative'),
    DB::raw('group_concat(DISTINCT(job_card.job_qty)) as qty'),
    DB::raw('group_concat(DISTINCT(job_card.open_size)) as open'),
    DB::raw('group_concat(DISTINCT(job_card.close_size)) as close'),
    DB::raw('group_concat(DISTINCT(job_card.job_sample_received)) as sample'),
    DB::raw('group_concat(DISTINCT(job_card.other_item_desc)) as item_name'))
    ->groupBy('job_card.id')->get());
    $jobdata = $jobdata->offset($offset)->limit($limit);
    $jobdata=$jobdata->select(
    DB::raw('group_concat(DISTINCT(job_card.id)) as ids'),
    DB::raw('group_concat(DISTINCT(job_card.job_number)) as job'),
    DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io'),
    DB::raw('group_concat(DISTINCT(party_reference.referencename)) as partyname'),
    DB::raw('group_concat(DISTINCT(DATE_FORMAT(job_card.created_time ,"%d-%m-%Y %r"))) as date'),
    DB::raw('group_concat(DISTINCT(item_category.name)) as name'),
    DB::raw('group_concat(DISTINCT(job_card.creative_name)) as creative'),
    DB::raw('group_concat(DISTINCT(job_card.job_qty)) as qty'),
    DB::raw('group_concat(DISTINCT(job_card.open_size)) as open'),
    DB::raw('group_concat(DISTINCT(job_card.close_size)) as close'),
    DB::raw('group_concat(DISTINCT(job_card.job_sample_received)) as sample'),
    DB::raw('group_concat(DISTINCT(job_card.other_item_desc)) as item_name'))
    ->groupBy('job_card.id')->get();


    $array['recordsTotal'] = $count;
    $array['recordsFiltered'] = $count;
    $array['data'] = $jobdata; 
    return json_encode($array);
    // $count = $jobdata->count();
    //         $jobdata = $jobdata->offset($offset)->limit($limit);
    // if(isset($request->input('order')[0]['column'])){
    //     $data = [
    //     'id',
    //     'job',
    //     'item_name',
    //     'io',
    //     'partyname',
    //     'date',
    //     'name',
    //     'creative',
    //     'qty',
    //     'open',
    //     'close',
    //     'sample'
    //     ];

    //      $ids = array_column($data, 'ids');
    // $ids = array_unique($ids);
    // $jobdata = array_filter($data, function ($key, $value) use ($ids) {
    //     return in_array($value, array_keys($ids));
    // }, ARRAY_FILTER_USE_BOTH);

    // // print_r($jobdata->toArray());die;
    //     $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
    //     $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
    // }
    // else
    // $jobdata->orderBy('ids','desc');
    // $jobdata= $jobdata->get();
    // $count = $jobdata->count();
   
   
    
    // $array['recordsTotal'] = $count;
    // $array['recordsFiltered'] = $count;
    // $array['data'] = $jobdata;
    // return json_encode($array);
}
public function jobcard_statusupdate_prod(){
    $titles="Job Card Close For Production Manager";
    $type="Production";
    $data=array('layout'=>'layouts.main','titles'=>$titles,'status'=>'open','type'=>$type);
    return view('sections.job_close', $data);
}
public function jobcard_statusupdate_log(){
    $titles="Job Card Close For Logistic Manager";
    $type="Logistic";
    $data=array('layout'=>'layouts.main','titles'=>$titles,'status'=>'open','type'=>$type);
    return view('sections.job_close', $data);
}
public function jobcard_statusupdate_prod_api(Request $request){
    $status="open";
    $search = $request->input('search');
    $serach_value = $search['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $offset = empty($start) ? 0 : $start ;
    $limit =  empty($limit) ? 10 : $limit ;
    
    if($status=='open' || $status=='closed'){
        $jobdata = JobCard::where('job_card.is_active',1)->leftJoin('internal_order', function($join) {
            $join->on('internal_order.id', '=', 'job_card.io_id');
        })
        ->leftJoin('job_details', function($join) {
            $join->on('job_details.id', '=', 'internal_order.job_details_id');
        })
        ->leftJoin('io_type', function($join) {
            $join->on('io_type.id', '=', 'job_details.io_type_id');
        })
        ->leftJoin('party_reference',function($join){
            $join->on('internal_order.reference_name','=','party_reference.id');
        })
        ->leftJoin('item_category',function($join){
            $join->on('job_card.item_category_id','=','item_category.id');
            
        })
        ->leftJoin('binding_details',function($join){
            $join->on('job_card.id','=','binding_details.job_card_id');
            
        })
        ->where('job_card.status','like',$status)->where('binding_details.id','!=',NULL)
        ->where('job_details.io_type_id',6)
        ->orwhere('job_details.io_type_id',5)
        ->orwhere('job_details.io_type_id',1);
    }

    if(!empty($serach_value))
    {
        $jobdata->where(function($query) use ($serach_value){
            $query->where('job_card.job_number','LIKE',"%".$serach_value."%")
            ->orWhere('party_reference.referencename','LIKE',"%".$serach_value."%")
            ->orWhere('internal_order.io_number','LIKE',"%".$serach_value."%")
            ->orWhere('item_category.name','LIKE',"%".$serach_value."%")
            ->orWhere('io_type.name','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.created_time','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.job_qty','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.open_size','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.close_size','LIKE',"%".$serach_value."%");
                });
 
    }
    // 
    if(isset($request->input('order')[0]['column']))
    {
        $data = [
            'id',
            'job',
            'item_name',
            'io',
            'partyname',
            'date',
            'name',
            'io_type',
            'creative',
            'qty',
            'open',
            'close',
            'sample'
            ];
        $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
        $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
    }
    else
        $jobdata->orderBy('job_card.id','desc');
    $count = count($jobdata->select( 
    DB::raw('group_concat(DISTINCT(job_card.id)) as ids'),
    DB::raw('group_concat(DISTINCT(job_card.job_number)) as job'),
    DB::raw('group_concat(DISTINCT(io_type.name)) as io_type'),
    DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io'),
    DB::raw('group_concat(DISTINCT(party_reference.referencename)) as partyname'),
    DB::raw('group_concat(DISTINCT(job_card.created_time)) as date'),
    DB::raw('group_concat(DISTINCT(item_category.name)) as name'),
    DB::raw('group_concat(DISTINCT(job_card.creative_name)) as creative'),
    DB::raw('group_concat(DISTINCT(job_card.job_qty)) as qty'),
    DB::raw('group_concat(DISTINCT(job_card.open_size)) as open'),
    DB::raw('group_concat(DISTINCT(job_card.close_size)) as close'),
    DB::raw('group_concat(DISTINCT(job_card.job_sample_received)) as sample'),
    DB::raw('group_concat(DISTINCT(job_card.other_item_desc)) as item_name'))
    ->groupBy('job_card.id')->get());
    $jobdata = $jobdata->offset($offset)->limit($limit);
    $jobdata=$jobdata->select(
    DB::raw('group_concat(DISTINCT(job_card.id)) as ids'),
    DB::raw('group_concat(DISTINCT(job_card.job_number)) as job'),
    DB::raw('group_concat(DISTINCT(io_type.name)) as io_type'),
    DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io'),
    DB::raw('group_concat(DISTINCT(party_reference.referencename)) as partyname'),
    DB::raw('group_concat(DISTINCT(DATE_FORMAT(job_card.created_time ,"%d-%m-%Y %r"))) as date'),
    DB::raw('group_concat(DISTINCT(item_category.name)) as name'),
    DB::raw('group_concat(DISTINCT(job_card.creative_name)) as creative'),
    DB::raw('group_concat(DISTINCT(job_card.job_qty)) as qty'),
    DB::raw('group_concat(DISTINCT(job_card.open_size)) as open'),
    DB::raw('group_concat(DISTINCT(job_card.close_size)) as close'),
    DB::raw('group_concat(DISTINCT(job_card.job_sample_received)) as sample'),
    DB::raw('group_concat(DISTINCT(job_card.other_item_desc)) as item_name'))
    ->groupBy('job_card.id')->get();


    $array['recordsTotal'] = $count;
    $array['recordsFiltered'] = $count;
    $array['data'] = $jobdata; 
    return json_encode($array);
}
public function jobcard_statusupdate_log_api(Request $request){
    $status="open";
    $search = $request->input('search');
    $serach_value = $search['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $offset = empty($start) ? 0 : $start ;
    $limit =  empty($limit) ? 10 : $limit ;
    
    if($status=='open' || $status=='closed'){
        $jobdata = JobCard::where('job_card.is_active',1)->leftJoin('internal_order', function($join) {
            $join->on('internal_order.id', '=', 'job_card.io_id');
        })
        ->leftJoin('job_details', function($join) {
            $join->on('job_details.id', '=', 'internal_order.job_details_id');
        })
        ->leftJoin('io_type', function($join) {
            $join->on('io_type.id', '=', 'job_details.io_type_id');
        })
        ->leftJoin('party_reference',function($join){
            $join->on('internal_order.reference_name','=','party_reference.id');
        })
        ->leftJoin('item_category',function($join){
            $join->on('job_card.item_category_id','=','item_category.id');
            
        })
        ->leftJoin('binding_details',function($join){
            $join->on('job_card.id','=','binding_details.job_card_id');
            
        })
        ->where('job_card.status','like',$status)->where('binding_details.id','!=',NULL)
        ->where('job_details.io_type_id',7);
    }

    if(!empty($serach_value))
    {
        $jobdata->where(function($query) use ($serach_value){
            $query->where('job_card.job_number','LIKE',"%".$serach_value."%")
            ->orWhere('party_reference.referencename','LIKE',"%".$serach_value."%")
            ->orWhere('internal_order.io_number','LIKE',"%".$serach_value."%")
            ->orWhere('item_category.name','LIKE',"%".$serach_value."%")
            ->orWhere('io_type.name','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.created_time','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.job_qty','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.open_size','LIKE',"%".$serach_value."%")
            ->orWhere('job_card.close_size','LIKE',"%".$serach_value."%");
                });
 
    }
    // 
    if(isset($request->input('order')[0]['column']))
    {
        $data = [
            'id',
            'job',
            'item_name',
            'io',
            'partyname',
            'date',
            'name',
            'io_type',
            'creative',
            'qty',
            'open',
            'close',
            'sample'
            ];
        $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
        $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
    }
    else
        $jobdata->orderBy('job_card.id','desc');
    $count = count($jobdata->select( 
    DB::raw('group_concat(DISTINCT(job_card.id)) as ids'),
    DB::raw('group_concat(DISTINCT(job_card.job_number)) as job'),
    DB::raw('group_concat(DISTINCT(io_type.name)) as io_type'),
    DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io'),
    DB::raw('group_concat(DISTINCT(party_reference.referencename)) as partyname'),
    DB::raw('group_concat(DISTINCT(job_card.created_time)) as date'),
    DB::raw('group_concat(DISTINCT(item_category.name)) as name'),
    DB::raw('group_concat(DISTINCT(job_card.creative_name)) as creative'),
    DB::raw('group_concat(DISTINCT(job_card.job_qty)) as qty'),
    DB::raw('group_concat(DISTINCT(job_card.open_size)) as open'),
    DB::raw('group_concat(DISTINCT(job_card.close_size)) as close'),
    DB::raw('group_concat(DISTINCT(job_card.job_sample_received)) as sample'),
    DB::raw('group_concat(DISTINCT(job_card.other_item_desc)) as item_name'))
    ->groupBy('job_card.id')->get());
    $jobdata = $jobdata->offset($offset)->limit($limit);
    $jobdata=$jobdata->select(
    DB::raw('group_concat(DISTINCT(job_card.id)) as ids'),
    DB::raw('group_concat(DISTINCT(job_card.job_number)) as job'),
    DB::raw('group_concat(DISTINCT(io_type.name)) as io_type'),
    DB::raw('group_concat(DISTINCT(internal_order.io_number)) as io'),
    DB::raw('group_concat(DISTINCT(party_reference.referencename)) as partyname'),
    DB::raw('group_concat(DISTINCT(DATE_FORMAT(job_card.created_time ,"%d-%m-%Y %r"))) as date'),
    DB::raw('group_concat(DISTINCT(item_category.name)) as name'),
    DB::raw('group_concat(DISTINCT(job_card.creative_name)) as creative'),
    DB::raw('group_concat(DISTINCT(job_card.job_qty)) as qty'),
    DB::raw('group_concat(DISTINCT(job_card.open_size)) as open'),
    DB::raw('group_concat(DISTINCT(job_card.close_size)) as close'),
    DB::raw('group_concat(DISTINCT(job_card.job_sample_received)) as sample'),
    DB::raw('group_concat(DISTINCT(job_card.other_item_desc)) as item_name'))
    ->groupBy('job_card.id')->get();


    $array['recordsTotal'] = $count;
    $array['recordsFiltered'] = $count;
    $array['data'] = $jobdata; 
    return json_encode($array);
}
public function jobcard_statusupdate(Request $request,$status,$id)
{
    $urlparams = $status=="close"?"open":'close';
    
    if(!empty($id))
    {
        $jobdata = JobCard::select('id','status','job_number')->where("id",$id)->first();
        
        if(!empty($jobdata->id))
        {
            $job_number = $jobdata->job_number;
            $date = Carbon::now();
            $closed_by = Auth::id();
            $status = $status=="close"?"Closed":'Open';

            JobCard::where("id",$id)->update(
                ['status'=>$status,'closed_by'=>$closed_by,'closed_date'=>$date]
            );
            $status = strtolower($status);
            return redirect('/JobCard/list/'.$urlparams)->with('success','Job Card ['.$job_number.'] has been '.$status.' successfully.');
        }else
        {
            return redirect('/JobCard/list/'.$urlparams)->with('error','Job Card not available.');
        }
    }
    else
    {
        return redirect('/JobCard/list/'.$urlparams)->with('error','Status not updated.');
    }
    
}

//-------------------------------------Job Card View--------------------------------------------------------------
public function jobcard_view($id)
{
   
    $jobcard=[];
    $jobcard['job']=JobCard::where('job_card.id',$id)
    ->leftJoin('internal_order','job_card.io_id','=','internal_order.id')
    ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
    ->leftJoin('master__marketing_person','master__marketing_person.id','=','job_details.marketing_user_id')
    ->leftJoin('party','internal_order.party_id','=','party.id')
    ->leftJoin('item_category','job_card.item_category_id','=','item_category.id')
    ->where('job_card.id','!=',NULL)
                ->get([
                    'job_card.id as job_id',
                    'job_card.closed_by as closed_by',
                    'job_card.closed_date as closed_date',
                    'job_card.status as status',
                    'job_card.description as description',
                    'job_details.marketing_user_id',
                    'master__marketing_person.id as user_id',
                    'master__marketing_person.name as marketing_name',
                    'job_card.job_number',
                    'job_card.job_qty as job_qty',
                    'job_card.creative_name',
                    'job_card.open_size',
                    'job_card.close_size',
                    'job_card.dimension',
                    'job_card.job_sample_received',
                    'job_card.remarks',
                    'job_card.item_category_id',
                    'job_card.created_time',
                    'internal_order.io_number',
                    'job_details.is_supplied_paper',
                    'job_details.is_supplied_plate',
                    'job_details.delivery_date',
                    'internal_order.created_time as io_created_time',
                    'internal_order.other_item_name',
                    'item_category.name',
                    'party.reference_name as partyname',
                    'job_details.is_supplied_paper',
                    'job_details.is_supplied_plate',
                    
                ])->first();
                //return $job;

                 $jobcard['element']=JobCard::where('job_card.id',$id)
                 ->leftJoin('element_feeder','job_card.id','=','element_feeder.job_card_id')
                ->where('element_feeder.id','!=',NULL)
                 ->get(['element_feeder.element_type_id as elementfeeder_type_id',
                 'element_feeder.plate_size',
                 'element_feeder.plate_sets',
                 'element_feeder.impression_per_plate',
                 'element_feeder.front_color',
                 'element_feeder.back_color',
                 'element_feeder.no_of_pages']
                );

                 $jobcard['raw']=JobCard::where('job_card.id',$id)
                ->leftJoin('raw_material','job_card.id','=','raw_material.job_card_id')
                ->leftJoin('paper_type','paper_type.id','=','raw_material.paper_type_id')
                ->where('raw_material.id','!=',NULL)
                 ->get([
                    'raw_material.element_type_id as element_type_id',
                        'raw_material.paper_size',
                        'raw_material.paper_type_id',
                        'raw_material.paper_gsm',
                        'raw_material.paper_mill',
                        'raw_material.paper_brand',

                        'raw_material.is_option',
                        'raw_material.item_name',
                        'raw_material.size',
                        'raw_material.size_dimension',
                        'raw_material.thickness',
                        'raw_material.thickness_dimension',
                        'raw_material.specification',
                        'raw_material.no_of_sheets',
                        'paper_type.name']
                );

                    $jobcard['bind']=JobCard::where('job_card.id',$id)
                    ->leftJoin('binding_details','binding_details.job_card_id','=','job_card.id')
                    ->leftJoin('binding_item','binding_details.element_type_id','=','binding_item.id')
                   ->where('binding_details.id','!=',NULL)
                   ->get([
                      'binding_details.id',
                      'binding_details.value',
                      'binding_details.remark',
                      'binding_details.element_type_id' ,
                      'binding_item.item_name'
                   ]);
       
       
        $jobcard['closed_by'] = Users::where('id','=',$jobcard['job']->closed_by)->value('name');
        $jobcard['layout']='layouts.main';
       
    return view('sections.jobcard_view', $jobcard);

    }
}
