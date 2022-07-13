<?php

namespace App;
namespace App\Http\Controllers;
use App\Model\ItemCategory;
use App\Model\jobDetails;
use App\Imports\Import;
use Illuminate\Validation\Rule;
use App\Model\IoType;
use App\Model\Vehicle;
use App\Model\PoNumber;
use App\Model\Utilities\Internal_DC;
use App\Model\Printing;
use App\Model\Tax_Invoice;
use App\Model\Tax_Dispatch;
use App\Model\Payment;
use App\Model\Tax;
use App\Model\Unit_of_measurement;
use App\Model\Challan_per_io;
use App\Model\FinancialYear;
use App\Model\Delivery_challan;
use App\Model\Goods_Dispatch;
use App\Model\Settings;
use App\Model\ElementFeeder;
use App\Model\Hsn;
use App\Model\Employee\EmployeeProfile;
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
use App\Model\Dispatch_mode;
use App\Model\Country;
use App\Model\Waybill;
use App\Model\City;
use App\Model\GatePasses;
use App\Model\JobDetailsView;
use App\Model\Users;
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

class Gatepass extends Controller
{
    public function gatepass_material(){
        $delivery=Dispatch_mode::all();
        $carrier=Goods_Dispatch::where('goods_dispatch.is_active',1)
        ->rightJoin('dispatch_mode','goods_dispatch.mode','=','dispatch_mode.id')
        ->get(['goods_dispatch.id','dispatch_mode.name','goods_dispatch.courier_name']);
        $data=array( 'layout' => 'layouts.main','delivery'=>$delivery,'carrier'=>$carrier);
        return view('sections.gatepass.material_gatepass',$data);
    }
    public function gatepass_material_db(Request $request){
        try {
            $validator = Validator::make($request->all(),
            [
                'challan_type'=>'required',
                'challan_num'=>'required',
                'carrier'=>'required'
            ],
            [
                'challan_type.required'=>'This field is required',
                'challan_num.required'=>'This field is required',
                'carrier.required'=>'This field is required',
            ]
            );
            $errors = $validator->errors();
            if ($validator->fails()) 
            {
                return redirect('gatepass/material')->withErrors($errors);
            }
            
            else{
                $settings = Settings::where('name','Gatepass_material_Prefix')->first();
                
                //financial year
                // $finan=FinancialYear::get()->last(); 
                // if($finan){
                //     $year=$finan->financial_year;
                //     $financial_year=$finan['financial_year'];
                // }
                // else{
                //     return redirect('/gatepass/material')->with('error','No Financial Year Exist.')->withInput();
                // }
                $fromDate= date('Y-m-d');
                $date=date_create($fromDate);
                $year=CustomHelpers::getFinancialFromDate($fromDate); 

              $old=GatePasses::where('financial_year',$year)->where('gatepass_for','Material')
              ->get('gatepass_number')->last();  


              if($old)
              {
               
                $gp=explode('/',$old['gatepass_number']);
                $v = (int)$gp[count($gp)-1];
                $gp_id=$v+1;   
                }
                else{
                    $gp_id=1;
                }

             
              $gp_number = $settings->value;
              $gp_number = $gp_number ."/".$year.'/'.$gp_id;
              $prefix = $gp_number;

              
                $challan=$request->input('challan_num');
                $challan_id=implode(',',$challan);
               
                $timestamp = date('Y-m-d G:i:s');
                    $id=GatePasses::insertGetId([
                        'id'=>NULL,
                        'gatepass_number'=>$gp_number,
                        'financial_year'=>$year,
                        'gatepass_for'=>'Material',
                        'challan_type'=>$request->input('challan_type'),
                        'mode_id'=>$request->input('mode'),
                        'challan_id'=>$challan_id,
                        'carrier_id'=>$request->input('carrier'),
                        'employee_id'=>NULL,
                        'reason'=>NULL,
                        'desc'=>NULL,
                        'est_duration'=>NULL,
                        'remark'=>NULL,
                        'return_date'=>NULL,
                        'created_by'=>Auth::id(),
                        'created_at'=>$timestamp
    
                    ]);
                   
                    return redirect('/gatepass/material')->with(['success'=>'Gatepass for Material created Successfully','pass'=>'Material','pass_id'=>$id]);
            }

        }  //throw $th;
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/gatepass/material')->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
           
    }
//----------------------------------------------material update-----------------------------------------------------------------------------------------------
public function gatepass_material_update($id){
    $delivery=Dispatch_mode::all();
    $carrier=Goods_Dispatch::where('goods_dispatch.is_active',1)
    ->rightJoin('dispatch_mode','goods_dispatch.mode','=','dispatch_mode.id')
    ->get(['goods_dispatch.id','dispatch_mode.name','goods_dispatch.courier_name']);
    $detail=GatePasses::where('gatepass.id',$id)->get()->first();
    $challan_id=explode(',',$detail['challan_id']);
    $data=array( 'layout' => 'layouts.main','delivery'=>$delivery,'carrier'=>$carrier,'detail'=>$detail,'challan_id'=>$challan_id);
    return view('sections.gatepass.material_update',$data);
}
public function gatepass_material_update_db(Request $request,$id){
    try {
        $validator = Validator::make($request->all(),
        [
            'challan_type'=>'required',
            'challan_num'=>'required',
            'carrier'=>'required'
        ],
        [
            'challan_type.required'=>'This field is required',
            'challan_num.required'=>'This field is required',
            'carrier.required'=>'This field is required',
        ]
        );
        $errors = $validator->errors();
        if ($validator->fails()) 
        {
            return redirect('gatepass/material')->withErrors($errors);
        }
        
        else{
            $challan=$request->input('challan_num');
            $challan_id=implode(',',$challan);
            $timestamp = date('Y-m-d G:i:s');
                $update=GatePasses::where('id',$id)->update([
                    'challan_type'=>$request->input('challan_type'),
                    'mode_id'=>$request->input('mode'),
                    'challan_id'=>$challan_id,
                    'carrier_id'=>$request->input('carrier'),

                ]);
                if($update){
                    CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Material Gatepass Update");
                    $gatepass_number= GatePasses::where('id',$id)->get('gatepass_number')->first();
                }
                return redirect('gatepass/material/update/'.$id)->with(['success'=>'Gatepass for Material updated Successfully','pass'=>'Material','id'=>$gatepass_number,'pass_id'=>$id]);
        }

    }  //throw $th;
    catch(\Illuminate\Database\QueryException $ex)
    {
        return redirect('/gatepass/material')->with('error','some error occurred'.$ex->getMessage())->withInput();
    }
       
}
//--------------------------------------------material list---------------------------------------------------------------------------------------------------    
    public function material_list(){
        $data=array('layout'=>'layouts.main');
        return view('sections.gatepass.material_summary', $data);
    }
    public function material_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
       // DB::beginTransaction();
        //DB::enableQueryLog();
        $asn=GatePasses::where('gatepass.gatepass_for','=','Material')
        ->leftJoin('goods_dispatch','goods_dispatch.id','=','gatepass.carrier_id')
        ->leftJoin('delivery_challan',function($join){
            $join->on(DB::raw("find_in_set(delivery_challan.id,gatepass.challan_id)"),">",DB::raw("'0'"));
        })
        ->leftJoin('internal_dc',function($join){
            $join->on(DB::raw("find_in_set(internal_dc.id,gatepass.challan_id)"),">",DB::raw("'0'"));
        });
       
        if(!empty($serach_value))

        $asn->where(function($query) use ($serach_value){
            $query->where('gatepass.gatepass_number','LIKE',"%".$serach_value."%")
            ->orwhere('gatepass.challan_type','LIKE',"%".$serach_value."%")
            ->orwhere('gatepass.challan_id','LIKE',"%".$serach_value."%")
            ->orwhere('goods_dispatch.courier_name','LIKE',"%".$serach_value."%")
            ->orwhere('gatepass.created_at','LIKE',"%".$serach_value."%")
            ->orwhere('delivery_challan.challan_number','LIKE',"%".$serach_value."%")
            ->orwhere('internal_dc.idc_number','LIKE',"%".$serach_value."%");
                });
           
          
        $count = $asn->count();
        $asn = $asn->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = [ 'gatepass.id', 'gatepass.gatepass_number', 'gatepass.challan_type','gatepass.challan_id','delivery_challan.id as delivery_id ','delivery_challan.challan_number','internal_dc.idc_number',
                        'goods_dispatch.courier_name','gatepass.created_at'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $asn->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        
            $asn->orderBy('id','desc');
        $asndata= $asn->select(
           
            DB::raw('concat(gatepass.challan_type,":",gatepass.challan_id) as a'),
            'gatepass.id as id', 
            'gatepass.gatepass_number',  
            'goods_dispatch.courier_name',
            DB::raw('(DATE_FORMAT(gatepass.created_at ,"%d-%m-%Y %r")) as created'),
           'gatepass.challan_type',
            'gatepass.challan_id',
            DB::raw('group_concat(delivery_challan.id) as delivery_id'),
            DB::raw('group_concat(internal_dc.idc_number) as idc_number'),
            DB::raw('group_concat(delivery_challan.challan_number) as challan_number')
        )->groupBy(  'gatepass.id', 
        'gatepass.gatepass_number',  
        'goods_dispatch.courier_name',
        'gatepass.created_at',
       
       'gatepass.challan_type',
        'gatepass.challan_id')->get();
      

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $asndata;
        return json_encode($array);
    }
    public function mode_challan($id){
        if($id==1){
            $delivery=Delivery_challan::get(['delivery_challan.id','delivery_challan.challan_number']);
        }
        if($id==2){
            $delivery=Internal_DC::get(['id','idc_number as challan_number']);
        }
        return $delivery;
    }
//---------------------------------------------employee gatepass---------------------------------------------------------------------------------------------
public function gatepass_employee(){
    $employee=EmployeeProfile::where('employee__profile.is_active',1)->get(['employee__profile.id','employee__profile.name']);
    $data=array( 'layout' => 'layouts.main','employee'=>$employee);
    return view('sections.gatepass.employee_gatepass',$data);
}
public function gatepass_employee_db(Request $request){
    try {
        $validator = Validator::make($request->all(),
        [
            'employee'=>'required',
            'reason'=>'required',
            'desc'=>'required',
            'duration'=>'required'
        ],
        [
            'employee.required'=>'This field is required',
            'reason.required'=>'This field is required',
            'desc.required'=>'This field is required',
            'duration.required'=>'This field is required'
        ]
        );
        $errors = $validator->errors();
        if ($validator->fails()) 
        {
            return redirect('gatepass/employee')->withErrors($errors);
        }
        
        else{
            $settings = Settings::where('name','Gatepass_employee_Prefix')->first();
               //financial year
            //    $finan=FinancialYear::get()->last(); 
            //    if($finan){
            //        $year=$finan->financial_year;
            //        $financial_year=$finan['financial_year'];
            //    }
            //    else{
            //        return redirect('/gatepass/employee')->with('error','No Financial Year Exist.')->withInput();
            //    }
            $fromDate= date('Y-m-d');
            $date=date_create($fromDate);
            $year=CustomHelpers::getFinancialFromDate($fromDate); 
               $old=GatePasses::where('financial_year',$year)->where('gatepass_for','Employee')
               ->get('gatepass_number')->last();  
 
 
               if($old)
               {
                
                 $gp=explode('/',$old['gatepass_number']);
                 $v = (int)$gp[count($gp)-1];
                 $gp_id=$v+1;   
                 }
                 else{
                     $gp_id=1;
                 }
 
              
               $gp_number = $settings->value;
               $gp_number = $gp_number ."/".$year.'/'.$gp_id;
               $prefix = $gp_number;

            $timestamp = date('Y-m-d G:i:s');
            $id=GatePasses::insertGetId([
                    'id'=>NULL,
                    'gatepass_number'=>$prefix,
                    'financial_year'=>$year,
                    'gatepass_for'=>'Employee',
                    'challan_type'=>NULL,
                    'challan_id'=>NULL,
                    'carrier_id'=>NULL,
                    'employee_id'=>$request->input('employee'),
                    'reason'=>$request->input('reason'),
                    'desc'=>$request->input('desc'),
                    'est_duration'=>$request->input('duration'),
                    'remark'=>NULL,
                    'return_date'=>NULL,
                    'created_by'=>Auth::id(),
                    'created_at'=>$timestamp

                ]);
           
                return redirect('/gatepass/employee')->with(['success'=>'Gatepass for Employee created Successfully','pass'=>'Employee','pass_id'=>$id]);
        }

    }  //throw $th;
    catch(\Illuminate\Database\QueryException $ex)
    {
        return redirect('/gatepass/employee')->with('error','some error occurred'.$ex->getMessage())->withInput();
    }
}
//----------------------------------------------------------------employee update--------------------------------------------------------------------------
public function gatepass_employee_update($id){
    $employee=EmployeeProfile::where('employee__profile.is_active',1)->get(['employee__profile.id','employee__profile.name']);
    $detail=GatePasses::where('gatepass.id',$id)->get()->first();
    $data=array( 'layout' => 'layouts.main','employee'=>$employee,'detail'=>$detail);
    return view('sections.gatepass.employee_update',$data);
}
public function gatepass_employee_update_db(Request $request,$ids){
    //print($request->input());die();
    try {
        $validator = Validator::make($request->all(),
        [
            'employee'=>'required',
            'reason'=>'required',
            'desc'=>'required',
            'duration'=>'required'
        ],
        [
            'employee.required'=>'This field is required',
            'reason.required'=>'This field is required',
            'desc.required'=>'This field is required',
            'duration.required'=>'This field is required'
        ]
        );
        $errors = $validator->errors();
        if ($validator->fails()) 
        {
            return redirect('gatepass/employee/update/'.$ids)->withErrors($errors);
        }
        
        else{
            $timestamp = date('Y-m-d G:i:s');
            $update=GatePasses::where('id',$ids)->update([
                    
                    'employee_id'=>$request->input('employee'),
                    'reason'=>$request->input('reason'),
                    'desc'=>$request->input('desc'),
                    'est_duration'=>$request->input('duration'),
                ]);
                if($update){
                    CustomHelpers::userActionLog($request->input()['update_reason'],$ids,"Employee Gatepass Update");
                    $gatepass_number= GatePasses::where('id',$ids)->get('gatepass_number')->first();
                }
                return redirect('gatepass/employee/update/'.$ids)->with(['success'=>'Gatepass for Employee updated Successfully','pass'=>'Material','id'=>$gatepass_number,'pass_id'=>$ids]);
               
        }

    }  //throw $th;
    catch(\Illuminate\Database\QueryException $ex)
    {
        return redirect('gatepass/employee/update/'.$id)->with('error','some error occurred'.$ex->getMessage())->withInput();
    }
}
//employee list

public function employee_list(){
    $data=array('layout'=>'layouts.main');
    return view('sections.gatepass.employee_summary', $data);
}
public function employee_api(Request $request){
    $search = $request->input('search');
    $serach_value = $search['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $offset = empty($start) ? 0 : $start ;
    $limit =  empty($limit) ? 10 : $limit ;

    $asn=GatePasses::where('gatepass.gatepass_for','Employee')->leftJoin('employee__profile','employee__profile.id','=','gatepass.employee_id');
   
    if(!empty($serach_value))

    $asn->where(function($query) use ($serach_value){
        $query->where('gatepass.gatepass_number','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.reason','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.desc','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.est_duration','LIKE',"%".$serach_value."%")
        ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.created_at','LIKE',"%".$serach_value."%");
            });
    
      
    $count = $asn->count();
    $asn = $asn->offset($offset)->limit($limit);
    if(isset($request->input('order')[0]['column'])){
        $data = [ 'gatepass.id', 'gatepass.gatepass_number', 'gatepass.reason','gatepass.desc','gatepass.est_duration',
                    'employee__profile.name','gatepass.created_at'];
        $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
        $asn->orderBy($data[$request->input('order')[0]['column']], $by);
    }
    else
    
        $asn->orderBy('id','desc');
    $asndata= $asn->select(
        'gatepass.id', 'gatepass.gatepass_number', 'gatepass.reason','gatepass.desc','gatepass.est_duration',
        'employee__profile.name',DB::raw('(DATE_FORMAT(gatepass.created_at ,"%d-%m-%Y %r")) as created')
    )->get();
  

    $array['recordsTotal'] = $count;
    $array['recordsFiltered'] = $count;
    $array['data'] = $asndata;
    return json_encode($array);
}
//---------------------------------------------------------Returnable Gatepass--------------------------------------------------------------------------------
public function gatepass_returnable(){
    $delivery=Delivery_challan::where('delivery_challan.is_active',1)->get(['delivery_challan.id','delivery_challan.challan_number']);
    $internal_dc = Internal_DC::get(["id","idc_number"]);
    $data=array( 'layout' => 'layouts.main','delivery'=>$delivery,'internal_dc'=>$internal_dc);
    return view('sections.gatepass.returnable_gatepass',$data);
}
public function gatepass_returnable_db(Request $request){
    try {
        $validator = Validator::make($request->all(),
        [
            'challan_type'=>'required',
            'challan_num'=>'required_if:challan_type,delivery_challan',
            'ichallan_num'=>'required_if:challan_type,internal_dc',
            'remark'=>'required',
            'return'=>'required',
        ],
        [
            'challan_num.required'=>'This field is required',
            'remark.required'=>'This field is required',
            'return.required'=>'This field is required',
        ]
        );
        $errors = $validator->errors();
        if ($validator->fails()) 
        {
            return redirect('gatepass/returnable')->withErrors($errors);
        }
        else{
            $settings = Settings::where('name','Gatepass_returnable_Prefix')->first();
                 //financial year
                 //$fromDate= date('Y-m',strtotime($request->input('return')));
                 $fromDate= date('Y-m-d');
                 $date=date_create($fromDate);
                 $fin_year=CustomHelpers::getFinancialFromDate($fromDate);

                 if($fin_year){
                    $financial_year = $year=$fin_year;
                 }
                 else{
                     return redirect('/gatepass/returnable')->with('error','Enter Document Date According to Financial Year.')->withInput();
                 }
              
                 $old=GatePasses::where('financial_year',$year)->where('gatepass_for','Returnable')
                 ->get('gatepass_number')->last();  
   
   
                 if($old)
                 {
                   $gp=explode('/',$old['gatepass_number']);
                   $v = (int)$gp[count($gp)-1];
                   $gp_id=$v+1;   
                   }
                   else{
                       $gp_id=1;
                   }
   
                
                 $gp_number = $settings->value;
                 $gp_number = $gp_number ."/".$year.'/'.$gp_id;
                 $prefix = $gp_number;
            $timestamp = date('Y-m-d G:i:s');
            $challan_type = $request->input("challan_type");
            if($challan_type == "delivery_challan")
                $challan_num = $request->input("challan_num");
            else if($challan_type == "internal_dc")
                $challan_num = $request->input("ichallan_num");
            $id=GatePasses::insertGetId([
                    'id'=>NULL,
                    'gatepass_number'=>$prefix,
                    'financial_year'=>$year,
                    'gatepass_for'=>'Returnable',
                    'challan_type'=>$challan_type,
                    'challan_id'=>$challan_num,
                    'carrier_id'=>NULL,
                    'employee_id'=>NULL,
                    'reason'=>NULL,
                    'desc'=>NULL,
                    'est_duration'=>NULL,
                    'remark'=>$request->input('remark'),
                    'return_date'=>date("Y-m-d", strtotime($request->input('return'))),
                    'created_by'=>Auth::id(),
                    'created_at'=>$timestamp

                ]);
               
                return redirect('/gatepass/returnable')->with(['success'=>'Gatepass for Returnable created Successfully','pass'=>'Returnable','pass_id'=>$id]);
        }

    }  //throw $th;
    catch(\Illuminate\Database\QueryException $ex)
    {
        return redirect('/gatepass/returnable')->with('error','some error occurred'.$ex->getMessage())->withInput();
    }
} 
//--------------------------------------returnable update
public function gatepass_returnable_update($id){
    $detail=GatePasses::where('gatepass.id',$id)->get()->first();
    $internal_dc = Internal_DC::get(["id","idc_number"]);
    $delivery=Delivery_challan::where('delivery_challan.is_active',1)->get(['delivery_challan.id','delivery_challan.challan_number']);
    $data=array( 'layout' => 'layouts.main','delivery'=>$delivery,'detail'=>$detail,'internal_dc'=>$internal_dc);
    return view('sections.gatepass.returnable_update',$data);
}
public function gatepass_returnable_update_db(Request $request,$id){
    try {
        $validator = Validator::make($request->all(),
        [
            'challan_type'=>'required',
            'challan_num'=>'required_if:,challan_type,delivery_challan',
            'ichallan_num'=>'required_if:,challan_type,internal_dc',
            'remark'=>'required',
            'return'=>'required',
        ],
        [
            'challan_num.required'=>'This field is required',
            'remark.required'=>'This field is required',
            'return.required'=>'This field is required',
        ]
        );
        $errors = $validator->errors();
        if ($validator->fails()) 
        {
            return redirect('gatepass/returnable/update/'.$id)->withErrors($errors);
        }
        else{
            
            $timestamp = date('Y-m-d G:i:s');
            $challan_type = $request->input("challan_type");
            if($challan_type == "delivery_challan")
                $challan_num = $request->input("challan_num");
            else if($challan_type == "internal_dc")
                $challan_num = $request->input("ichallan_num");
           
            $update=GatePasses::where('id',$id)->update([
                'challan_id'=>$challan_num,
                'challan_type'=>$challan_type,
                'remark'=>$request->input('remark'),
                'return_date'=>date("Y-m-d", strtotime($request->input('return')))
            ]);
            if($update){
                CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Returnable Gatepass Update");
                $gatepass_number= GatePasses::where('id',$id)->get('gatepass_number')->first();
            }
            return redirect('gatepass/returnable/update/'.$id)->with(['success'=>'Gatepass for Returnable updated Successfully','pass'=>'Returnable','id'=>$gatepass_number,'pass_id'=>$id]);
        }

    }  //throw $th;
    catch(\Illuminate\Database\QueryException $ex)
    {
        return redirect('gatepass/returnable/update/'.$id)->with('error','some error occurred'.$ex->getMessage())->withInput();
    }
} 
public function returnable_list(){
    $data=array('layout'=>'layouts.main');
    return view('sections.gatepass.returnable_summary', $data);
}
public function returnable_api(Request $request){
    $search = $request->input('search');
    $serach_value = $search['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $offset = empty($start) ? 0 : $start ;
    $limit =  empty($limit) ? 10 : $limit ;

    $asn=GatePasses::where('gatepass.gatepass_for','Returnable')->leftJoin('delivery_challan','delivery_challan.id','=','gatepass.challan_id')
    ->leftJoin('internal_dc','internal_dc.id','=','gatepass.challan_id');
   
    if(!empty($serach_value))
        $asn->where(function($query) use ($serach_value){
        $query->where('gatepass.gatepass_number','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.reason','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.desc','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.est_duration','LIKE',"%".$serach_value."%")
        ->orwhere('delivery_challan.challan_number','LIKE',"%".$serach_value."%")
        ->orwhere('internal_dc.idc_number','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.challan_type','LIKE',"%".$serach_value."%")
        ->orwhere('gatepass.created_at','LIKE',"%".$serach_value."%");
            });
        
      
    $count = $asn->count();
    $asn = $asn->offset($offset)->limit($limit);
    if(isset($request->input('order')[0]['column'])){
        $data = [ 'gatepass.id', 
        'gatepass.gatepass_number', 
        'gatepass.remark',
        'gatepass.return_date',
        'gatepass.challan_type',
        'delivery_challan.challan_number',
        'internal_dc.idc_number',
        'gatepass.created_at'
];
        $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
        $asn->orderBy($data[$request->input('order')[0]['column']], $by);
    }
    else
    
        $asn->orderBy('id','desc');
    $asndata= $asn->select(
        'gatepass.id', 
        'gatepass.gatepass_number', 
        'gatepass.remark',
        'gatepass.return_date',
        'gatepass.challan_type',
        'delivery_challan.challan_number',
        'internal_dc.idc_number',
        DB::raw('(DATE_FORMAT(gatepass.created_at ,"%d-%m-%Y %r")) as created')
    )->get();
  

    $array['recordsTotal'] = $count;
    $array['recordsFiltered'] = $count;
    $array['data'] = $asndata;
    return json_encode($array);
}   
}
