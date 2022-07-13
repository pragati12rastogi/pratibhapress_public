<?php

namespace App\Http\Controllers;
use App\Model\PoNumber;
use App\Model\Party;
use App\Model\Tax_Invoice;
use App\Model\ItemCategory;
use App\Custom\CustomHelpers;
use App\Model\Unit_of_measurement;
use App\Model\Goods_Dispatch;
use App\Model\Country;
use App\Model\State;
use App\Model\Production\MachineName;
use App\Model\City;
use App\Model\Tax;
use App\Model\Settings;
use App\Model\FinancialYear;
use App\Model\Dispatch_mode;
use App\Model\Delivery_challan;
use App\Model\Reference;
use App\Model\Employee\EmployeeProfile;
use App\Model\Department;
use App\Model\Employee\Assets;
use App\Model\Employee\AssetAssign;
use App\Model\Employee\AssetReturn;
use App\Model\Employee\AssetCategory;
use App\Model\Waybill;
use App\Model\Hsn;
use App\Model\Vehicle;
use App\Model\PlateSize;
use App\Model\Userlog;
use App\Model\MsVehicle_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\dkerp;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Str;
class MastersController extends Controller
{
    /**
     * create new masters in ERP.
     * @param masterName name of the model to enter.
     * @return \Illuminate\Contracts\Support\Renderable
    */
    
    public function get_hsn_from_id($id)
    {
        return Hsn::where('item_id', $id)->get();
    }

    public function get_uom()
    {
        $uom=Unit_of_measurement::get();
        $arr=array( 'uom' => $uom);
        return $arr;
    }
    
    
    public function create($masterName, Request $request){
        $data = $request->input();
        unset($data['userAlloweds']);
        unset($data['_token']);
        $success_redirect = $data['success_redirect'];
        $failure_redirect = $data['failure_redirect'];
        unset($data['success_redirect']);
        unset($data['failure_redirect']);
        $masterName = "\\App\\Model\\".$masterName;
        $master = new $masterName($data);
        try{
            $master->save();
        }catch(Exception $e){
            return redirect()->intended($failure_redirect);
        }
        return redirect()->intended($success_redirect);
    }


    public function po_form(){
        $party=City::join('party', 'cities.id', '=', 'party.city_id')
        ->get(['party.id','party.partyname','party.city_id','cities.city']);
        $data=array(
            'layout' => 'layouts.main',
            'party' => $party,
        );

        return view('sections.create_po',$data);
    }
    public function po_insert(Request $request){
        try {

            $this->validate($request,
            [
                'party_name'=>'required',
                'po_number'=>'required',
                'po_date'=>'required'
            ]
        );
            $date = strtotime($request->input('po_date'));
            $newDate = date("Y-m-d", $date);
            $timestamp = date('Y-m-d G:i:s');
            PoNumber::insert(
                [
                    'id' => NULL,
                    'party_id' =>$request->input('party_name'),
                    'number' => $request->input('po_number'),
                    'po_date' => $newDate,
                    'created_by' => Auth::id(),
                    'is_active' =>1,
                    'created_time' => $timestamp,
                ]
            );
            return redirect('/createpo')->with('success','PO created successfully.');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/createpo')->with('error','some error occurred')->withInput();
        }
    }
    public function vehicle_form(){
        $vehicle_type = MsVehicle_type::all();
        $data=array(
            'layout' => 'layouts.main',
            'v_type'=>$vehicle_type
        );

        return view('sections.create_vehicle',$data);
    }
    public function vehicle_insert(Request $request){
        try {

            $this->validate($request,
            [
                'vehicle_number'=>'required',
                'owner'=>'required',
                'vehicle_brand'=>'required',
                'vehicle_type'=>'required'
            ],
            [
                'vehicle_number.required'=>'Vehicle Number is Required',
                'owner.required'=>'Vehicle Owner is Required',
                'vehicle_brand.required'=>'Vehicle Brand is Required',
                'vehicle_type.required'=>'Vehicle Type is Required',
            ]
        );
            $timestamp = date('Y-m-d G:i:s');
            Vehicle::insert(
                [
                    'id' => NULL,
                    'vehicle_number' => $request->input('vehicle_number'),
                    'owner_name' => $request->input('owner'),
                    'brand'=>$request->input('vehicle_brand'),
                    'vehicle_type'=>$request->input('vehicle_type'),
                    'is_active' => 1,
                    'created_at' => $timestamp,
                ]
            );
            return redirect('/vehicle/create')->with('success','Vehicle created Successfully.');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/vehicle/create')->with('error','some error occurred')->withInput();
        }
    }
    
    public function vehicle_update_db(Request $request,$id){
        try {
            $this->validate($request,
            [
                'update_reason'=>'required',
                'vehicle_number'=>'required',
                'owner'=>'required',
                'vehicle_brand'=>'required',
                'vehicle_type'=>'required'
            ],
            [
                'vehicle_number.required'=>'Vehicle Number is Required',
                'owner.required'=>'Vehicle Owner is Required',
                'vehicle_brand.required'=>'Vehicle Brand is Required',
                'vehicle_type.required'=>'Vehicle Type is Required'
            ]
        );
        CustomHelpers::userActionLog($request->input('update_reason'),$id,'Vehicle Update');

            $timestamp = date('Y-m-d G:i:s');
            Vehicle::where('id','=',$id)
            ->update(
                [
                'vehicle_number' => $request->input('vehicle_number'),
                'owner_name' => $request->input('owner'),
                'brand'=>$request->input('vehicle_brand'),
                'vehicle_type'=>$request->input('vehicle_type'),
                'is_active' => 1,
                'updated_at' => $timestamp,
       
                ]
            );
            return redirect('/vehicle/list')->with('success','Vehicle Updated successfully ');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/vehicle/list')->with('error','some error occurred')->withInput();
        }
    }
    public function vehicle_update($id)
    {
        $vehicle_type = MsVehicle_type::all();
        $hsn = Vehicle::where('id','=',$id)
            ->where('is_active','=',1)
            ->get()->first();
        $data=array(
            'layout' => 'layouts.main',
            'data'=> $hsn,
            'v_type'=>$vehicle_type
        );

        if($hsn)
            return view('sections.edit_vehicle',$data);    
        else {
            return  redirect('/vehicle/list')->with('error', 'Data Not Found!');    
            
        }    
    }
    public function vehicle_list()
    {
        $data=array('layout'=>'layouts.main');
        return view('sections.vehicle_summary', $data);
    }
    public function vehicle_list_api(Request $request)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $hsn = Vehicle::where('is_active','=',1)->leftjoin('master__vehicle_type','master__vehicle_type.id','vehicle.vehicle_type');    
        if(!empty($serach_value))
        {
            $hsn->where('vehicle_number','LIKE',"%".$serach_value."%")
                ->orWhere('owner_name','LIKE',"%".$serach_value."%")
                ->orWhere('brand','LIKE',"%".$serach_value."%")
                ->orWhere('master__vehicle_type.type','LIKE',"%".$serach_value."%")
                ;
        
        }$count = $hsn->count();
        $hsn = $hsn->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['vehicle.id', 'vehicle_number', 'owner_name','brand','master__vehicle_type.type'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $hsn->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $hsn->orderBy('vehicle.id','desc');
        $hsdata= $hsn->select(
            'vehicle.id',
            'vehicle_number',
            'owner_name',
            'brand',
            'master__vehicle_type.type'
        )->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $hsdata; 
        return json_encode($array);
    }

    public function hsn_form(){
        $item = ItemCategory::all();
        $data=array(
            'layout' => 'layouts.main',
            'item' => $item,
        );

        return view('sections.create_hsn',$data);
    }
    public function hsnupdate(Request $request,$id){
        try {
            $this->validate($request,
            [
                'update_reason'=>'required',
                'item'=>'required',
                'hsn'=>'required',
                'gst'=>'required',
                'item_desc'=>'required',
            ],
            [
                'item.required'=>'Item Name is Required',
                'hsn.required'=>'HSN/SAC is Required',
                'gst.required'=>'GST Rate is Required',
                'item_desc.required'=>'Item Description Rate is Required',
            ]
        );
        CustomHelpers::userActionLog($request->input()['update_reason'],$id,'HSN/SAC Update');

        $timestamp = date('Y-m-d G:i:s');
            Hsn::where('id','=',$id)
            ->update(
                [                    
                    'item_id' =>$request->input('item'),
                    'hsn' => $request->input('hsn'),
                    'gst_rate' => $request->input('gst'),
                    'item_description'=>$request->input('item_desc'),
                    'created_by' => Auth::id(),
                    'updated_at' => $timestamp,
                ]
            );
            return redirect('/hsn/list')->with('success','Hsn Updated successfully ');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/hsn/list')->with('error','some error occurred')->withInput();
        }
    }
    public function hsn_insert(Request $request){
        try {

            $this->validate($request,
            [
                'item'=>'required',
                'hsn'=>'required',
                'gst'=>'required',
                'item_desc'=>'required'
            ],
            [
                'item.required'=>'Item Name is Required',
                'hsn.required'=>'HSN/SAC is Required',
                'gst.required'=>'GST Rate is Required',
                'item_desc.required'=>'Item Description is Required'
            ]
        );
            $timestamp = date('Y-m-d G:i:s');
            Hsn::insert([
                'id' => NULL,
                'item_id' =>$request->input('item'),
                'hsn' => $request->input('hsn'),
                'gst_rate' => $request->input('gst'),
                'item_description'=>$request->input('item_desc'),
                'created_by' => Auth::id(),
                'is_active' =>1,
                'created_time' => $timestamp
            ]);
            return redirect('/hsn/create')->with('success','HSN created Successfully.');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/hsn/create')->with('error','Some error occured.')->withInput();
        }
    }
    public function viewHSNList()
    {
        $data=array('layout'=>'layouts.main', 'io'=>'$io');
        return view('sections.hsn_summary', $data);
    }
    public function viewHSNListApi(Request $request)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $hsn = Hsn::where('is_active','=',1);    
        if(!empty($serach_value))
            $hsn->where('item_id','LIKE',"%".$serach_value."%")
            ->orwhere('gst_rate','like',"%".$serach_value."%")
            ->orwhere('hsn','like',"%".$serach_value."%");
        $count = $hsn->count();
        $hsn = $hsn->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['id', 'itid', 'gst', 'hsn','itemdesc'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $hsn->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $hsn->orderBy('id','desc');
        $hsdata= $hsn->select(
            'hsn.id',
            'hsn.item_id as itid',
            'hsn.gst_rate as gst',
            'hsn.hsn as hsn',
            'hsn.item_description as itemdesc'
        )->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $hsdata; 
        return json_encode($array);
    }
    public function viewHSNListDelete($id)
    {
        $hsn = Hsn::where('id','=',$id)
            ->update([
                'is_active'=>'0'
            ]);
            return redirect('/hsn/list')->with('success', 'Item is Deleted!');
    }
    public function viewHSNListEdit($id)
    {
        $hsn = Hsn::where('id','=',$id)
            ->where('is_active','=',1)
            ->get()->first();
        $data=array(
            'layout' => 'layouts.main',
            'data'=> $hsn
        );

        if($hsn)
            return view('sections.edit_hsn',$data);    
        else {
            return  redirect('/hsn/list')->with('error', 'Data Not Found!');    
            
        }    
    }
    
    public function dispatch_form(){
        $data=array(
            'layout' => 'layouts.main'
        );

        return view('sections.goods_dispatch',$data);
    }
    public function GoodsDispatch_insert(Request $request){
        try {

                $this->validate($request,
                    [
                        'mode'=>'required',
                        'carrier'=>'required_without:company',
                        'company'=>'required_without:carrier',
                        'number'=>'digits:10|unique:goods_dispatch,contact',
                        // 'gst'=>'required_with:company|nullable|string|alpha_num|string|size:15',
                        'gst'=>'nullable|string|alpha_num|string|size:15',
                        'gst_sel'=>'required_with:company',
                        'address'=>'required_with:company',
                    ]
                );
        
            $timestamp = date('Y-m-d G:i:s');
            $courier_name = $request->input('mode')==2? $request->input('carrier'):$request->input('company');
           
            if($request->input('mode')==2){
                $gst=NULL;
            }
            else{
                $gst= $request->input('gst_type') ==0 ? $request->input('gst_sel') : $request->input('gst');
            }
            Goods_Dispatch::insert(
                [
                    'id' => NULL,
                    'mode' =>$request->input('mode'),
                    'courier_name' => $courier_name,
                    'contact' => $request->input('number'),
                    'gst' => $gst,
                    'address' => $request->input('address'),
                    'created_by' => Auth::id(),
                    'is_active' =>1,
                    'created_time' => $timestamp,
                ]
            );
            return redirect('/createdispatch')->with('success','Dispatch Profiles created successfully');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/createdispatch')->with('error','some error occurred')->withInput();
        }
    }
    
    public function goodsdispatch_list(){
        $data=array('layout'=>'layouts.main');
        return view('sections.good_dispatch_summary', $data);
    }
    public function goodsdispatch_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $hsn = Goods_Dispatch::where('is_active','=',1)->leftJoin('dispatch_mode','dispatch_mode.value','=','goods_dispatch.mode');    
        if(!empty($serach_value))
            $hsn->where('goods_dispatch.courier_name','LIKE',"%".$serach_value."%")
            ->orwhere('dispatch_mode.name','LIKE',"%".$serach_value."%")
            ->orwhere('goods_dispatch.contact','LIKE',"%".$serach_value."%")
            ->orwhere('goods_dispatch.gst','LIKE',"%".$serach_value."%")
            ->orwhere('goods_dispatch.address','LIKE',"%".$serach_value."%");
          
            

        $count = $hsn->count();
        $hsn = $hsn->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = [ 'goods_dispatch.mode', 'goods_dispatch.courier_name', 'goods_dispatch.contact',
                        'goods_dispatch.gst','goods_dispatch.address'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $hsn->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $hsn->orderBy('id','desc');
        $hsdata= $hsn->select(
            'goods_dispatch.id',
            'goods_dispatch.mode',
            'goods_dispatch.courier_name',
            'goods_dispatch.contact',
            'goods_dispatch.gst',
            'goods_dispatch.address',
            'dispatch_mode.name'
        )->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $hsdata; 
        return json_encode($array);
    }
    public function dispatch_edit($id){
        $dispatch=Goods_Dispatch::where('id',$id)->where('goods_dispatch.is_active',1)->get()->first();
        $data=[
            'layout' => 'layouts.main',
            'dispatch'=> $dispatch
        ];
        return view('sections.dispatch_update',$data);
    }
    public function dispatch_update(Request $request,$id){
        // print_r($request->input());die;
        try {
            $this->validate($request,[
                'mode'=>'required',
                'carrier'=>'required_without:company',
                'company'=>'required_without:carrier',
                'number'=>'digits:10|unique:goods_dispatch,contact,'.$id,
                // 'gst'=>'nullable|string|alpha_num|string|size:15',
                // 'gst_sel'=>'required_with:company',
                'address'=>'required_with:company'
            ]);
            // CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Goods Dispatch Profile Update");
            $timestamp = date('Y-m-d G:i:s');
            $courier_name = $request->input('mode')==2?$request->input('carrier'):$request->input('company');
            if($request->input('mode')==2){
                $gst=NULL;
            }
            else{
                $gst= $request->input('gst_type') ==0 ? $request->input('gst_sel') : $request->input('gst');
            }
            Goods_Dispatch::where('id','=',$id)->update(
                [
                    'mode' =>$request->input('mode'),
                    'courier_name' => $courier_name,
                    'contact' => $request->input('number'),
                    'gst' => $gst,
                    'address' => $request->input('address'),
                    'updated_at' => $timestamp,
                ]
            );
            return redirect('/goodsdispatch/list')->with('success','successfull updated Dispatch Profile');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/dispatch/edit/'.$id)->with('error','some error occurred'.$ex->getMessage())->withInput();
        } 
    }
    public function setting()
    {
        $setting = Settings::get();
        $pass=[];
        $setting=$setting->toArray();
        $finan=FinancialYear::get()->last();
        
        for($i=0;$i<count($setting);$i++)
        {
            $pass+=[$setting[$i]['name']=>$setting[$i]['value']];
        }
        $data=array('layout'=>'layouts.main','settings'=>$pass,'finan'=>$finan);
    //    return $finan;
        return view('sections.settings_form', $data);

    }
    public function create_financial(){
       
        $finan=FinancialYear::get()->last();
        $fromDate= date('Y-m-d');
        $date=date_create($fromDate);
        $year=CustomHelpers::getFinancialYear($date);
        // print_r($year);die; 
        // $finan=FinancialYear::get()->last();
        // if($finan['to'] <= date('Y-m')){
            // return $finan;
            $data=array('layout'=>'layouts.main','finan'=>$finan);
            return view('sections.financial_form', $data); 
        // }
        // else{
        //     return redirect('/financial/year/summary/')->with('error','This Financial Year Already Exist');
        // }
        
    //    return $finan;
        
    }
    public function create_financialDb(Request $req){
        try {
            if($req->input('financial_year')){
                $finan=FinancialYear::get()->last();
                $fromDate= date('Y-m');
                $date=date_create($fromDate);
                $year=CustomHelpers::getFinancialYear($date); 
                $finan=FinancialYear::where('from', '<=', $fromDate)
                ->where('to', '>=', $fromDate)->get();
                if(count($finan)<1){
                   
                }
                else{
                    return redirect('/financial/year/summary/')->with('error','Current Financial Year Exist. Cannot Create Future Financial Year.');
                }
                $fromDate=$req->input('financial_year')[0];

                $from=explode('-',$req->input('financial_year')[0]);
                $from=substr( $from[0], -2);
                $to=explode('-',$req->input('financial_year')[1]);
                $to=substr( $to[0], -2);
                $finan=FinancialYear::where('from', '<=', $fromDate)
                ->where('to', '>=', $fromDate)->get();
                // print_r(count($finan));die;
                if(count($finan)>0){
                    return redirect('/financial/year/create')->with('error','This Financialte Year Already Exist');
                }
                FinancialYear::insert([
                    'from'=>$req->input('financial_year')[0],
                    'to'=>$req->input('financial_year')[1],
                    'financial_year'=>$from.'-'.$to,
                    'bonus_per'=>$req->input('bonus')
                ]);
                return redirect('/financial/year/create')->with('success','Successfully Updated Financial Year');
              
            }     
        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/financial/year/create')->with('error','some error occurred'.$ex->getMessage());
      }
    }

    public function update_financial($id){
       
        $finan=FinancialYear::where('id',$id)->get()->first();
    
        $data=array('layout'=>'layouts.main','finan'=>$finan);
    //    return $finan;
        return view('sections.financial_update', $data); 
    }
    public function update_financialDb(Request $req,$id){
        try {
            if($req->input('financial_year')){
                $fromDate=$req->input('financial_year')[0];

                $from=explode('-',$req->input('financial_year')[0]);
                $from=substr( $from[0], -2);
                $to=explode('-',$req->input('financial_year')[1]);
                $to=substr( $to[0], -2);
                $finan=FinancialYear::where('from', '<=', $fromDate)
                ->where('to', '>=', $fromDate)->get();
                // print_r(count($finan));die;
                // if(count($finan)>0){
                //     return redirect('/financialYear/edit/'.$id)->with('error','This Financial Year Already Exist');
                // }
                FinancialYear::where('id',$id)->update([
                    'to'=>$req->input('financial_year')[1],
                    'financial_year'=>$from.'-'.$to,
                    'bonus_per'=>$req->input('bonus')
                ]);
                return redirect('/financialYear/edit/'.$id)->with('success','Successfully Updated Financial Year');
              
            }     
        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/financialYear/edit/'.$id)->with('error','some error occurred'.$ex->getMessage());
      }
    }
    public function financial_list()
    {
        $data=array('layout'=>'layouts.main');
        return view('sections.financial_list', $data);      
    }
    public function financial_list_api(Request $request)
    {        
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $paymentData= FinancialYear::select(
            'id',
            'to','from','financial_year','bonus_per');

        if(!empty($serach_value))
        {
            $paymentData = $paymentData->where('id','LIKE',"%".$serach_value."%")
            ->orwhere('to','LIKE',"%".$serach_value."%")
            ->orwhere('from','LIKE',"%".$serach_value."%")
            ->orwhere('financial_year','LIKE',"%".$serach_value."%")
            ->orwhere('bonus_per','LIKE',"%".$serach_value."%");
        }
        $count = $paymentData->count();
        $paymentData = $paymentData->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['id',
            'to','from','financial_year','bonus_per'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $paymentData->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $paymentData->orderBy('id','desc');
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $paymentData->get(); 
        return json_encode($array);

    }
    public function settingaddform( Request $req)
    {
        // print_r($req->input());die;
        try
        {
            $id=Auth::id();
            $input = $req->all();
            unset($input['userAlloweds']);
            unset($input['sub']);
          
           

            foreach($input as $key=>$val)
            {
                if($key=='_token')
                    continue;               
                $count = Settings::where('name',$key)->count();
                if($count==0)
                {
                   $var= Settings::insert([
                        'name'=>$key,
                        'value'=>$val
                    ]);
                }
                else
                {
                    $var=Settings::where('name',$key)->update([
                        'updated_at'=>date('Y-m-d G:i:s'),
                        'value'=>$val,
                        
                    ]);

                }
            }
          
            return redirect('/settings')->with('success', 'Settings has been updated!');
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/settings')->with('error','some error occurred'.$ex->getMessage());
      }


    }

    public function uom_insert_form()
    {        
        $data=array(
            'layout' => 'layouts.main'
        );

        return view('sections.create_uom',$data);
    }
    public function uom_insert(Request $request)
    {        
        try {
            $this->validate($request,
            [
                'uom'=>'required',
            ],
            [
                'uom.required'=>'Unit of measurement is Required.',
            ]
        );
            $timestamp = date('Y-m-d G:i:s');
            Unit_of_measurement::insert(
                [
                    'id' => NULL,
                    'created_by' => Auth::id(),
                    'is_active' =>1,
                    'uom_name'=> $request->input('uom'),
                    'created_at' => $timestamp,
                ]
            );
            return redirect('/uom/create')->with('success','Unit of Measurement created successfully.');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/uom/create')->with('error','some error occurred')->withInput();
        }
    }
    public function uom_update_form($id)
    {        
        $uom = Unit_of_measurement::where('id','=',$id)
        ->where('is_active','=',1)
        ->get()->first();
        $data=array(
        'layout' => 'layouts.main',
        'data'=> $uom
        );

        if($uom)
            return view('sections.edit_uom',$data);    
        else 
            return  redirect('/uom/list')->with('error', 'Data Not Found!');    
        
    }
    public function uom_update(Request $request,$id)
    {
        try 
        {
                $this->validate($request,
                [
                    'uom'=>'required',
                ],
                [
                    'uom.required'=>'Unit of measurement is Required.',
                ]
     
            );
            CustomHelpers::userActionLog($request->input()['update_reason'],$request->input()['_id'],'Unit Of Measurement Update');
            $timestamp = date('Y-m-d G:i:s');
            Unit_of_measurement::where('id','=',$id)
            ->update(
                [                    
                    'uom_name' => $request->input('uom'),
                    'created_by' => Auth::id(),
                    'updated_at' => $timestamp,
                ]
            );
            return redirect('/uom/list')->with('success','Unit of Measurement updated successfully. ');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/uom/update/'.$id)->with('error','some error occurred')->withInput();
        }
        
    }
    public function uom_delete($id)
    {       
        try 
        {
            $timestamp = date('Y-m-d G:i:s');
            Unit_of_measurement::where('id','=',$id)
            ->update(
                [                    
                    'is_active' => 0,
                    'created_by' => Auth::id(),
                    'updated_at' => $timestamp,
                ]
            );
            return redirect('/uom/list')->with('success','Unit of Measurement deleted successfully. ');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/uom/update/'.$id)->with('error','some error occurred')->withInput();
        }
        
 
    }
    public function uom_list()
    {
        $data=array('layout'=>'layouts.main', 'io'=>'$io');
        return view('sections.uom_summary', $data);      
    }
    public function uom_data_api(Request $request)
    {        
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $hsdata= Unit_of_measurement::select(
            'id',
            'uom_name')
            ->where('is_active','=',1);

        if(!empty($serach_value))
            $hsdata = $hsdata->where('uom_name','LIKE',"%".$serach_value."%");
        $count = $hsdata->count();
        $hsdata = $hsdata->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['id', 'uom_name'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $hsdata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $hsdata->orderBy('id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $hsdata->get(); 
        return json_encode($array);

    }

    
    public function userlog()
    {
        $data=array('layout'=>'layouts.main', 'io'=>'$io');
        return view('sections.userlog', $data);      
    }
    public function logdata(Request $request)
    {        
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = Userlog::leftjoin('users',function($join){
            $join->on('users.id','=','userlog.userid');
        });

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('action','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('content','LIKE',"%".$serach_value."%");
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['userlog.id','name','action','content','data_id','createdon'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('id','desc');
        }
        $userlogdata = $userlog->select('userlog.id','name','action','description','data_id','content_changes','createdon')->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);

    }


    public function waybill_create($delivery_id,$text,$client,$date,$amount,$refer,$pointer,Request $request){
        $challan_id=explode(':',$delivery_id);
    
       $request->session()->put('challan_id',$challan_id);
       $request->session()->put('delivery_id',$delivery_id);
       $request->session()->put('gst',$client);
       $request->session()->put('date',$date);
       $request->session()->put('refer',$refer);
       $request->session()->put('pointer',$pointer);
        $request->session()->put('text',$text);
       $request->session()->put('amount',$amount);
        return redirect('waybill/create/data');      
    }
    public function waybill_data(){
         
        $party=Party::all();
        $client = session()->get('gst');
        $delivery_id = session()->get('delivery_id');
        $challan_id = session()->get('challan_id');
        $text = session()->get('text');
        $date = session()->get('date');
        $amount = session()->get('amount');
        $refer1= session()->get('refer');
        $refer=Reference::where('id',$refer1)->select('referencename')->get()->first();
        $pointer = session()->get('pointer');
        if($pointer==0){
            $gst=$client;
        }
        if($pointer==1){
            $gst=$client;
        }
        if($pointer==2){
            $gst=$client;
        }
       
        $data=array('layout'=>'layouts.main',
        'party'=>$party,
        'challan_id'=>$challan_id,
        'delivery_id'=>$delivery_id,
        'text'=>$text,
        'client'=>$client,
        'date'=>$date,
        'amount'=>$amount,
        'refer'=>$refer,
        'gst'=>$gst,
        'pointer'=>$pointer
    );
        return view('sections.waybill',$data);
        //print($client);
    }
    public function waybill_createDb($delivery_id,$text,Request $request){
        try {
            $number=$request->input('number');
            $timestamp = date('Y-m-d G:i:s');
           if($request->input('list_available')=="Challan"){
                $validator = Validator::make($request->all(),
                [
                    'list_available'=>'required',
                    'challan_party_name'=>'required',
                    'number'=>'required',
                    'challan_date'=>'required',
                    'challan_amount'=>'required',
                    'waybill_number1'=>'required',
                    'waybill_date1'=>'required'
                ],
                [
                    'list_available.required'=>'This field is required',
                    'challan_party_name.required'=>'This field is required',
                    'number.required'=>'This field is required',
                    'challan_date.required'=>'This field is required',
                    'challan_amount.required'=>'This field is required',
                    'waybill_number1.required'=>'This field is required',
                    'waybill_date1.required'=>'This field is required'
                ]
                );
                $errors = $validator->errors();
                if ($validator->fails()) 
                {
                    return redirect('waybill/create/'.$delivery_id.'/'.$text)->withErrors($errors);
                }
                else{
                $challan_id=$number;
                $invoice_id=NULL;
                        for($i=0;$i<count($number);$i++){
                            Waybill::insert([
                                'id' => NULL,
                                'waybill_for' =>$request->input('list_available'),
                                'gst_number' => $request->input('challan_party_name'),
                                'challan_id' => $challan_id[$i],
                                'invoice_id' => $invoice_id,
                                'date' => date("Y-m-d", strtotime($request->input('challan_date'))),
                                'amount' => $request->input('challan_amount'),
                                'waybill_number' => $request->input('waybill_number1'),
                                'waybill_date' => date("Y-m-d", strtotime($request->input('waybill_date1'))),
                                'created_by' => Auth::id(),
                                'created_time' => $timestamp,     
                                ]);
                                Delivery_challan::where('id',$challan_id[$i])->update(
                                    [
                                        'waybill_status'=> 2,
                                        
                                    ]
                                );
                                $tax_invoice=Tax::where('delivery_challan_id',$challan_id[$i])->leftJoin('tax_invoice','tax_invoice.id','=','tax.tax_invoice_id')->get('tax_invoice.id')->first();
                                Tax_Invoice::where('id',$tax_invoice['id'])->update(
                                    [
                                        'waybill_status'=> 2
                                        
                                    ]
                                );
                        }
            }
            }
           if($request->input('list_available')=="Sale"){
            $validator = Validator::make($request->all(),
            [
                'list_available'=>'required',
                'tax_party_name'=>'required',
                'number'=>'required',
                'tax_date'=>'required',
                'tax_amount'=>'required',
                'waybill_number'=>'required',
                'waybill_date'=>'required'
            ],
            [
                'list_available.required'=>'This field is required',
                'tax_party_name.required'=>'This field is required',
                'number.required'=>'This field is required',
                'tax_date.required'=>'This field is required',
                'tax_amount.required'=>'This field is required',
                'waybill_number.required'=>'This field is required',
                'waybill_date.required'=>'This field is required'
            ]
            );
            $errors = $validator->errors();
            if ($validator->fails()) 
            {
                return redirect('waybill/create/'.$delivery_id.'/'.$text)->withErrors($errors);
            }
            else{
            $challan_id=NULL;
            $invoice_id=$number;
                    for($i=0;$i<count($number);$i++){
                        // $delivery=Tax::where('tax.tax_invoice_id',$number[$i])
                        // ->groupby('tax.delivery_challan_id')->select('tax.delivery_challan_id')->first()->toArray();
                       
                        Waybill::insert([
                            'id' => NULL,
                            'waybill_for' =>$request->input('list_available'),
                            'gst_number' => $request->input('tax_party_name'),
                            'challan_id' => $challan_id,
                            'invoice_id' => $invoice_id[$i],
                            'date' => date("Y-m-d", strtotime($request->input('tax_date'))),
                            'amount' => $request->input('tax_amount'),
                            'waybill_number' => $request->input('waybill_number'),
                            'waybill_date' => date("Y-m-d", strtotime($request->input('waybill_date'))),
                            'created_by' => Auth::id(),
                            'created_time' => $timestamp,     
                            ]);

                            Tax_Invoice::where('id',$invoice_id[$i])->update(
                                [
                                    'waybill_status'=> 2,
                                    
                                ]
                            );
                            $dc=Tax::where('tax_invoice_id',$invoice_id[$i])->select('delivery_challan_id')->get();
                            foreach($dc as $key){
                                Delivery_challan::where('id',$key['delivery_challan_id'])->update(
                                    [
                                        'waybill_status'=> 2,
                                        
                                    ]
                                );
                            }
                           
                         
                    }
                   
       }
    }
            return redirect('/waybill/list')->with('success','WayBill Created successfully ');
        } 
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('waybill/create/'.$delivery_id.'/'.$text)->with('error',$ex->getMessage())->withInput();
        }
    }
 //-----------------------------------------------waybill list--------------------------------------------------------------------------------------   
    public function waybill_list(){
        $data=array('layout'=>'layouts.main');
        return view('sections.waybill_summary', $data);
    }
    public function waybill_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $hsn = Waybill::leftJoin('party','party.gst','=','waybill.gst_number')  
            ->leftJoin('tax_invoice','tax_invoice.id','=','waybill.invoice_id')   
            ->leftJoin('delivery_challan','delivery_challan.id','=','waybill.challan_id')
            ->leftJoin('party as party_dc','party_dc.id','delivery_challan.party_id')
            ->leftJoin('party as party_tx','party_dc.id','tax_invoice.party_id');    
        if(!empty($serach_value))
            $hsn->where('party.partyname','LIKE',"%".$serach_value."%")
            ->orwhere('party_dc.partyname','LIKE',"%".$serach_value."%")
            ->orwhere('party_tx.partyname','LIKE',"%".$serach_value."%")
            ->orwhere('tax_invoice.invoice_number','LIKE',"%".$serach_value."%")
            ->orwhere('waybill.gst_number','LIKE',"%".$serach_value."%")
            ->orwhere('delivery_challan.challan_number','LIKE',"%".$serach_value."%")
            ->orwhere('waybill.waybill_number','LIKE',"%".$serach_value."%")
            ->orwhere('waybill.amount','LIKE',"%".$serach_value."%")
            ->orwhere('waybill.waybill_for','LIKE',"%".$serach_value."%")
            ->orwhere('waybill.gst_number','LIKE',"%".$serach_value."%");
          
            

        $count = $hsn->count();
        $hsn = $hsn->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = [ 'id', 'waybill_for', 'partyname','invoice_number','challan_number','date',
                        'waybill_number','amount','waybill_date','waybill.created_time','party_dc.partyname as dc_party','party_tx.partyname as tx_party'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $hsn->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $hsn->orderBy('waybill.waybill_number','desc');
        $hsdata= $hsn->select(
            DB::raw('group_concat(party.gst_pointer) as gst_pointer'),
            DB::raw('group_concat(waybill.id) as id'),
            DB::raw('group_concat(distinct(waybill.waybill_for)) as waybill_for'),
            DB::raw('group_concat(distinct(party.partyname)) as partyname'),
            DB::raw('group_concat(distinct(delivery_challan.challan_number)) as challan_number'),
            DB::raw('group_concat(tax_invoice.invoice_number) as invoice_number'),
            DB::raw('group_concat(distinct(DATE_FORMAT(waybill.date ,"%d-%m-%Y"))) as date'),
            DB::raw('group_concat(distinct(waybill.waybill_number)) as waybill_number'),
            DB::raw('group_concat(distinct(DATE_FORMAT(waybill.waybill_date ,"%d-%m-%Y"))) as waybill_date'),
            DB::raw('group_concat(distinct(waybill.amount)) as amount'),DB::raw('group_concat(distinct(DATE_FORMAT(waybill.created_time ,"%d-%m-%Y %r"))) as created_time'),
            DB::raw('group_concat(distinct(waybill.gst_number)) as gst_number'),
            DB::raw('group_concat(distinct(party_dc.partyname)) as dc_partyname'),
            DB::raw('group_concat(distinct(party_tx.partyname)) as tx_partyname')
        )->groupBy('waybill.waybill_number')->get()
        ;
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $hsdata; 
        return json_encode($array);
    }
    //------------------------------------------------------------department---------------------------------------------------------------------------

    public function create_department(){
        $data=array('layout'=>'layouts.main');
        return view('employee.create_department',$data);   
    }
    public function create_departmentDb(Request $request){
        try {
            $validator = Validator::make($request->all(),
                [
                    'department'=>'required',
                ],
                [
                    'department.required'=>'This field is required',
                ]
                );
                $errors = $validator->errors();
                if ($validator->fails()) 
                {
                    return redirect('/master/department')->withErrors($errors);
                }
                else{
                    $dept=Department::insertGetId([
                        'id'=>NULL,
                        'department'=>$request->input('department')

                    ]);
                    if($dept==NULL) 
                    {
                       DB::rollback();
                        return redirect('/master/department')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                           
                        return redirect('/master/department')->with('success','Successfully Created Department.');
                    }
                }
        }  catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/master/department')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function department_list(){
        $data=array('layout'=>'layouts.main');
        return view('employee.department_summary',$data);    
    }
    public function department_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = Department::select('department.id','department.department');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('id','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['department.id','department.department'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
  
    }
    public function update_department($id){
        $dept=Department::where('id',$id)->get()->first();
        $data=array('dept'=>$dept,'layout'=>'layouts.main','id'=>$id);
        return view('employee.update_department',$data);   
    }
    public function update_departmentDb(Request $request,$id){
        try {
            $validator = Validator::make($request->all(),
                [
                    'department'=>'required',
                ],
                [
                    'department.required'=>'This field is required',
                ]
                );
                $errors = $validator->errors();
                if ($validator->fails()) 
                {
                    return redirect('/master/department/edit/'.$id)->withErrors($errors);
                }
                else{
                    $dept=Department::where('id',$id)->update([
                        'department'=>$request->input('department')

                    ]);
                    if($dept==NULL) 
                    {
                       DB::rollback();
                       return redirect('/master/department/edit/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                        CustomHelpers::userActionLog($request->input()['update_reason'],$id,'Department Update');  
                        return redirect('/master/department/edit/'.$id)->with('success','Successfully Updated Department.');
                    }
                }
        }  catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/master/department/edit/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
    }


//------------------------------------------------------------assets---------------------------------------------------------------------------
        public function assets_unique_bill_no(Request $request){
            $x=$request->input('bill');
            $billno = Assets::where('asset_bill_no',$x)->get()->first();
            $result =0;
            if(isset($billno)){
                $result=1;
            }
            return response()->json($result);
        }
        public function create_assets(){
            $asset_category = DB::table('assets_category')->get();
            $data=array('layout'=>'layouts.main','asset_category'=>$asset_category);
            return view('employee.create_assets',$data);   
        }

        public function create_assetsDb(Request $request){
            try {
                $validator = Validator::make($request->all(),
                    [
                        'assets_category'=>'required',
                        'assets_name'=>'required',
                        'assets_brand'=>'required',
                        'assets_number'=>'required',
                        'assets_desc'=>'required',
                        'assets_bill_no'=>'required',
                        'assets_purchase_date'=>'required|before:tomorrow|after:1900-01-01',
                        'assets_value'=>'required',
                        'assets_photo' => 'required_with|image|mimes:jpeg,png,jpg,gif,svg|max:'.CustomHelpers::getfilesize(),
                        'assets_bill_ph' => 'mimes:jpeg,png,jpg,gif,pdf,svg|max:'.CustomHelpers::getfilesize()
                    ],
                    [
                        'assets_category.required'=>'This Field is required',
                        'assets_name.required'=>'This Field is required',
                        'assets_brand.required'=>'This Field is required',
                        'assets_number.required'=>'This Field is required',
                        'assets_desc.required'=>'This Field is required',
                        'assets_bill_no.required'=>'This Field is required',
                        'assets_purchase_date.required'=>'This Field is required',
                        'assets_value.required'=>'This Field is required',
                        'assets_photo.required_with'=>'Image accept only jpeg,png,jpg format',
                        'assets_bill_ph.required_with'=>'Field accept only jpeg,png,jpg,pdf format'
                    ]
                    );
                    $errors = $validator->errors();

                    if ($validator->fails()) 
                    {
                        return redirect('/master/assets')->withErrors($errors);
                    }
                    else{
                        $categoryN= AssetCategory::where('ac_id',$request->input('assets_category'))->get(['category_name'])->first();
                        $cur_id =Assets::where('asset_category_id',$request->input('assets_category'))->get([DB::raw('Count(asset_category_id) as asset_counter')])->first();
                        $as_name= substr($request->input('assets_name'),0,5);
                        $as_cat= substr($categoryN->category_name,0,3);
                        $ass_count = (($cur_id['asset_counter']+1)<10) ? "0".($cur_id['asset_counter']+1) :($cur_id['asset_counter']+1);
                        $assetcode = "ASS/".$as_cat."/".$ass_count;
                        
                        $asset_image_file = '';
                        $asset_bill_file = '';
                        $file = $request->file('assets_photo');
                        $billfile = $request->file('assets_bill_ph');
                        if(isset($file) || $file != null){
                            $destinationPath = public_path().'/upload/assets/';
                            $filenameWithExt = $request->file('assets_photo')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('assets_photo')->getClientOriginalExtension();
                            $asset_image_file = $filename.'_'.time().'.'.$extension;
                            $path = $file->move($destinationPath, $asset_image_file);
                        }else{
                            $asset_image_file = '';
                        }

                        if(isset($billfile) || $billfile != null){
                            $destinationPath = public_path().'/upload/assets/';
                            $filenameWithExt = $request->file('assets_bill_ph')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('assets_bill_ph')->getClientOriginalExtension();
                            $asset_bill_file = $filename.'_'.time().'.'.$extension;
                            $path = $billfile->move($destinationPath, $asset_bill_file);
                        }else{
                            $asset_bill_file = '';
                        }

                        $timestamp = date('Y-m-d G:i:s');
                        $assets=Assets::insertGetId([
                            'asset_id'=>NULL,
                            'asset_category_id'=>$request->input('assets_category'),
                            'name'=>$request->input('assets_name'),
                            'brand'=>$request->input('assets_brand'),
                            'asset_bill_no'=>$request->input('assets_bill_no'),
                            'model_number'=>$request->input('assets_number'),
                            'description'=>$request->input('assets_desc'),
                            'asset_code'=>$assetcode,
                            'purchase_date'=>date("Y-m-d", strtotime($request->input('assets_purchase_date'))),
                            'asset_value'=>$request->input('assets_value'),
                            'asset_photo_upload'=>$asset_image_file,
                            'asset_bill_upload'=>$asset_bill_file,
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
    
                        ]);
                        if($assets==NULL) 
                        {
                           DB::rollback();
                            return redirect('/master/assets')->with('error','Some Unexpected Error occurred.');
                        }
                        else{
                               
                            return redirect('/master/assets')->with('success','Successfully Created Assets.');
                        }
                    }
            }  catch(\Illuminate\Database\QueryException $ex) {
                return redirect('/master/assets')->with('error','some error occurred'.$ex->getMessage());
            }
        }
        public function assets_list(){
            $data=array('layout'=>'layouts.main');
            return view('employee.assets_summary',$data);    
        }
        public function assets_list_api(Request $request){
            $search = $request->input('search');
            $serach_value = $search['value'];
            $start = $request->input('start');
            $limit = $request->input('length');
            $offset = empty($start) ? 0 : $start ;
            $limit =  empty($limit) ? 10 : $limit ;
            
            $userlog = Assets::leftjoin('assets_category','assets.asset_category_id','assets_category.ac_id')
            ->select('assets.asset_id','assets_category.category_name','assets.name','assets.brand',
            'assets.asset_bill_no','assets.model_number','assets.description','assets.asset_value',
            'assets.asset_code','assets.asset_photo_upload','assets.asset_bill_upload','assets.allot_status');
    
            if(!empty($serach_value))
            {
                $userlog = $userlog->where('assets_category.category_name','LIKE',"%".$serach_value."%")
                            ->orwhere('name','LIKE',"%".$serach_value."%")
                            ->orwhere('brand','LIKE',"%".$serach_value."%")
                            ->orwhere('asset_bill_no','LIKE',"%".$serach_value."%")
                            ->orwhere('model_number','LIKE',"%".$serach_value."%")
                            ->orwhere('description','LIKE',"%".$serach_value."%")
                            ->orwhere('asset_value','LIKE',"%".$serach_value."%")
                            ->orwhere('asset_code','LIKE',"%".$serach_value."%")
                            ;
            }
    
            $count = $userlog->count();
            $userlog = $userlog->offset($offset)->limit($limit);
    
            if(isset($request->input('order')[0]['column'])){
                $data = ['asset_id','assets_category.category_name','name','brand',
                'asset_bill_no','model_number','description','asset_value','asset_code'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else
            {
                $userlog->orderBy('asset_id','desc');
            }
            $userlogdata = $userlog->get();
            
            $array['recordsTotal'] = $count;
            $array['recordsFiltered'] = $count ;
            $array['data'] = $userlogdata; 
            return json_encode($array);
      
        }
        public function update_assets($id){
            $asset_category = DB::table('assets_category')->get();
            $assets=Assets::where('asset_id',$id)->get()->first();
            $status=$assets->allot_status;
            if($status=="Disposed"){
                return redirect('/master/assets/list')->with('error','Disposed Assets Cannot Be Updated');
            }
            else{
                 $data=array('assets'=>$assets,'layout'=>'layouts.main','id'=>$id,'asset_category'=>$asset_category);
                return view('employee.update_assets',$data);  
            }
            
        }
        public function update_assetsDb(Request $request,$id){
            
            try {
                $validator = Validator::make($request->all(),
                    [
                        'assets_category'=>'required',
                        'assets_name'=>'required',
                        'assets_brand'=>'required',
                        'assets_number'=>'required',
                        'assets_desc'=>'required',
                        'assets_bill_no'=>'required',
                        'assets_purchase_date'=>'required',
                        'assets_value'=>'required',
                    ],
                    [
                        'assets_category.required'=>'This Field is required',
                        'assets_name.required'=>'This Field is required',
                        'assets_brand.required'=>'This Field is required',
                        'assets_number.required'=>'This Field is required',
                        'assets_desc.required'=>'This Field is required',
                        'assets_bill_no.required'=>'This Field is required',
                        'assets_purchase_date.required'=>'This Field is required',
                        'assets_value.required'=>'This Field is required',
                    ]
                    );
                    $errors = $validator->errors();
                    if ($validator->fails()) 
                    {
                        return redirect('/master/assets/edit/'.$id)->withErrors($errors);
                    }
                    else{
                        $check_cat = Assets::where('asset_id',$id)->get()->first();
                        $assetcode="";
                        if($check_cat['asset_category_id'] == $request->input('assets_category')){
                            $assetcode = $check_cat['asset_code'];
                        }else{
                            $categoryN= AssetCategory::where('ac_id',$request->input('assets_category'))->get(['category_name'])->first();
                            $cur_id =Assets::where('asset_category_id',$request->input('assets_category'))->get([DB::raw('Count(asset_category_id) as asset_counter')])->first();
                            $as_name= substr($request->input('assets_name'),0,5);
                            $as_cat= substr($categoryN->category_name,0,3);
                            $ass_count = (($cur_id['asset_counter']+1)<10) ? "0".($cur_id['asset_counter']+1) :($cur_id['asset_counter']+1);
                            $assetcode = "ASS/".$as_cat."/".$ass_count;

                        }

                        $asset_image_file ='';
                        $asset_bill_file ='';
                        $file = $request->file('upd_assets_photo');
                        $billfile = $request->file('upd_assets_bill');
                        
                        if(!isset($file)||$file == null){
                            $asset_image_file = $request->input('old_image');
                        }else{
                            $destinationPath = public_path().'/upload/assets/';
                            $filenameWithExt = $request->file('upd_assets_photo')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('upd_assets_photo')->getClientOriginalExtension();
                            $asset_image_file = $filename.'_'.time().'.'.$extension;
                            $path = $file->move($destinationPath, $asset_image_file);
                            File::delete($destinationPath.$request->input('old_image'));
                        }

                        if(!isset($billfile)||$billfile == null){
                            $asset_bill_file = $request->input('old_bill');
                        }else{
                            $destinationPath = public_path().'/upload/assets/';
                            $filenameWithExt = $request->file('upd_assets_bill')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('upd_assets_bill')->getClientOriginalExtension();
                            $asset_bill_file = $filename.'_'.time().'.'.$extension;
                            $path = $billfile->move($destinationPath, $asset_bill_file);
                            File::delete($destinationPath.$request->input('old_bill'));
                        }

                        $dept=DB::table('assets')->where('asset_id',$id)->update([
                            
                            'asset_category_id'=>$request->input('assets_category'),
                            'name'=>$request->input('assets_name'),
                            'brand'=>$request->input('assets_brand'),
                            'asset_bill_no'=>$request->input('assets_bill_no'),
                            'model_number'=>$request->input('assets_number'),
                            'description'=>$request->input('assets_desc'),
                            'asset_code'=>$assetcode,
                            'purchase_date'=>date("Y-m-d", strtotime($request->input('assets_purchase_date'))),
                            'asset_value'=>$request->input('assets_value'),
                            'asset_photo_upload'=>$asset_image_file,
                            'asset_bill_upload'=>$asset_bill_file
                        ]);
                        if($dept==NULL) 
                        {
                           DB::rollback();
                           return redirect('/master/assets/edit/'.$id)->with('error','Some Unexpected Error occurred.');
                        }
                        else{
                            CustomHelpers::userActionLog($request->input()['update_reason'],$id,'Assets Update');  
                            return redirect('/master/assets/edit/'.$id)->with('success','Successfully Updated Assets.');
                        }
                    }
            }  catch(\Illuminate\Database\QueryException $ex) {
                return redirect('/master/assets/edit/'.$id)->with('error','some error occurred'.$ex->getMessage());
            }
        }
    public function return_asset($id){
        $assets=AssetAssign::where('aa_id',$id)
        ->leftjoin('assets_category','asset_assign.asset_category_id','assets_category.ac_id')
        ->leftjoin('assets','assets.asset_id','asset_assign.asset_id')
        ->leftjoin('employee__profile','employee__profile.id','asset_assign.employee_id')
        ->select('assets_category.category_name',
            'assets.name','assets.asset_code','asset_assign.asset_id',
            DB::raw("CONCAT(employee__profile.name,'-',employee__profile.employee_number) as employee"))
        ->get()->first();
        $employee = DB::table('employee__profile')->get();
        $data=array('assets'=>$assets,'layout'=>'layouts.main','id'=>$id,'employ'=>$employee);
        return view('employee.return_asset',$data);   
    }   
    public function return_asset_db($id,Request $request){
         try {

            $validator = Validator::make($request->all(),
                [
                    'employee'=>'required',
                    'return_date'=>'required'
                ],
                [
                    'employee.required'=>'This Field is required',
                    'return_date.required'=>'This Field is required'
                    
                    ]
                );
                $errors = $validator->errors();

                if ($validator->fails()) 
                {
                    return redirect('/asset/return/'.$id)->withErrors($errors);
                }
                else{
                    $aa=AssetAssign::where('aa_id',$id)->get()->first();
                    $timestamp = date('Y-m-d G:i:s');
                    $assets=AssetAssign::where('aa_id',$id)->update([
                        'return_to'=>$request->input('employee'),
                        'return_date'=>date("Y-m-d",strtotime($request->input('return_date'))),
                        'status'=>'Returned',
                        'updated_at'=>$timestamp
                    ]);
                    $upd_asset = DB::table('assets')->where('asset_id',$aa['asset_id'])
                    ->update(['allot_bit'=>0,
                    'allot_status'=>'not assign']);

                    if($assets==NULL) 
                    {
                       DB::rollback();
                        return redirect('/master/assets/assign/employee/list')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                           
                        return redirect('/master/assets/assign/employee/list')->with('success','Asset Returned Successfully on '.date("d-m-Y",strtotime($request->input('return_date'))).'.');
                    }
                }
        }  catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/master/assets/assign/employee/list')->with('error','some error occurred'.$ex->getMessage());
        } 
    }    
    public function filter_asset_code_api(Request $request){
        $assets=DB::table('assets')->where('asset_category_id',$request->input('assets'))
        ->where('allot_bit','<>',1)
        ->where('allot_status','<>','Disposed')
        ->pluck('asset_code','asset_id');
        return response()->json($assets);
    }
    public function asset_issue_to_employee(){
        $asset_category = DB::table('assets_category')->get();
        $employee = DB::table('employee__profile')->get();
        $data=array('layout'=>'layouts.main','asset_category'=>$asset_category,'emp_for_asset'=>$employee);
        return view('employee.assign_assets',$data);   
     
    }
    public function asset_issue_to_employeeDb(Request $request){
        try {

            $validator = Validator::make($request->all(),
                [
                    'assets_category'=>'required',
                    'assets_code'=>'required',
                    'assets_emp'=>'required',
                    'assets_from_date'=>'required',
                    'assets_form'=>'required|max:'.CustomHelpers::getfilesize()
                   // 'assets_to_date'=>'required'
                ],
                [
                    'assets_category.required'=>'This Field is required',
                    'assets_code.required'=>'This Field is required',
                    'assets_emp.required'=>'This Field is required',
                    'assets_from_date.required'=>'This Field is required',
                    // 'assets_to_date.required'=>'This Field is required'
                    'assets_form.required'=>'This field is required'
                    ]
                );
                $errors = $validator->errors();

                if ($validator->fails()) 
                {
                    return redirect('/master/assets/assign/employee')->withErrors($errors);
                }
                else{
                    $formfile = $request->file('assets_form');
                    $destinationPath = public_path().'/upload/assets/form';
                    $filenameWithExt = $request->file('assets_form')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('assets_form')->getClientOriginalExtension();
                    $asset_form = $filename.'_'.time().'.'.$extension;
                    $path = $formfile->move($destinationPath, $asset_form);
                    

                    $timestamp = date('Y-m-d G:i:s');
                    $assets=AssetAssign::insertGetId([
                        'aa_id'=>NULL,
                        'asset_category_id'=>$request->input('assets_category'),
                        'asset_id'=>$request->input('assets_code'),
                        'employee_id'=>$request->input('assets_emp'),
                        'from_date'=>date("Y-m-d", strtotime($request->input('assets_from_date'))),
                        'to_date'=>date("Y-m-d", strtotime($request->input('assets_to_date'))),
                        'asset_form'=>$asset_form,
                        'created_by'=>Auth::id(),
                        'created_at'=>$timestamp
                    ]);
                    $upd_asset = DB::table('assets')->where('asset_id',$request->input('assets_code'))
                    ->update(['allot_bit'=>1,
                    'allot_status'=>'Assigned']);

                    if($assets==NULL) 
                    {
                       DB::rollback();
                        return redirect('/master/assets/assign/employee')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                           
                        return redirect('/master/assets/assign/employee')->with('success','Asset Assigned Successfully.');
                    }
                }
        }  catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/master/assets/assign/employee')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function asset_issue_to_employee_list(){
        $data=array('layout'=>'layouts.main');
            return view('employee.assign_asset_list',$data);
    }
    public function asset_issue_to_employee_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = AssetAssign::where('assets.allot_status','<>','Disposed')
        ->leftjoin('assets_category','asset_assign.asset_category_id','assets_category.ac_id')
        ->leftjoin('assets','assets.asset_id','asset_assign.asset_id')
        ->leftjoin('employee__profile','employee__profile.id','asset_assign.employee_id')
        ->select('asset_assign.aa_id',
        'assets_category.category_name','assets.name',
        'assets.model_number','assets.asset_value','assets.asset_code'
        ,DB::raw("CONCAT(employee__profile.name,'-',employee__profile.employee_number) as employee"),
        DB::raw('(DATE_FORMAT(asset_assign.from_date ,"%d-%m-%Y")) as from_date'),
        'asset_assign.to_date','assets.allot_status','asset_assign.asset_form','asset_assign.status');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('assets_category.category_name','LIKE',"%".$serach_value."%")
                        ->orwhere('assets.name','LIKE',"%".$serach_value."%")
                        ->orwhere('model_number','LIKE',"%".$serach_value."%")
                        ->orwhere('asset_value','LIKE',"%".$serach_value."%")
                        ->orwhere('asset_code','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('from_date','LIKE',"%".$serach_value."%")
                        ->orwhere('to_date','LIKE',"%".$serach_value."%")
                        ->orwhere('allot_status','LIKE',"%".$serach_value."%")
                        ->orwhere('asset_assign.status','LIKE',"%".$serach_value."%")

                        ;
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['asset_assign.aa_id','assets_category.category_name','assets.name'
            ,'assets.model_number','assets.asset_value','assets.asset_code','employee'
            ,'asset_assign.from_date','asset_assign.to_date','allot_status','asset_assign.status'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('asset_assign.aa_id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
  
    }
    public function alloted_asset_code_api(Request $request){
        $assets=DB::table('assets')->where('asset_category_id',$request->input('assets'))
        ->where('allot_bit','=',0)
        ->where('allot_status','<>','Assigned')
        ->where('allot_status','<>','Disposed')
        ->pluck('asset_code','asset_id');
        return response()->json($assets);
    }
    public function alloted_asset_emp_api(Request $request){
        $assets=DB::table('asset_assign')->where('asset_category_id',$request->input('assets'))
        ->where('asset_id',$request->input('code'))
        ->leftjoin('employee__profile','employee__profile.id','asset_assign.employee_id')
        ->pluck( DB::raw("CONCAT(employee__profile.name,'-',employee__profile.employee_number) as employee"),
        'employee__profile.id');
        return response()->json($assets);
    }
    public function asset_disposal(){
        $asset_category = DB::table('assets_category')
        ->get();
        
        $data=array('layout'=>'layouts.main','asset_category'=>$asset_category);
        return view('employee.asset_disposal',$data);   
     
    }
    public function asset_disposal_db(Request $request){
        try {
            $validator = Validator::make($request->all(),
                [
                    'assets_category'=>'required',
                    'assets_code'=>'required',
                    'assets_disposal_date'=>'required|before:tomorrow|after:1900-01-01',
                    'assets_disposal_to'=>'required',
                    'assets_disposal_reason'=>'required'
                   
                ],
                [
                    'assets_category.required'=>'This Field is required',
                    'assets_code.required'=>'This Field is required',
                    'assets_disposal_date.required'=>'This Field is required',
                    'assets_disposal_to.required'=>'This Field is required',
                    'assets_disposal_reason.required'=>'This Field is required'
                    ]
                );
                $errors = $validator->errors();

                if ($validator->fails()) 
                {
                    return redirect('/master/assets/disposal')->withErrors($errors);
                }
                else{

                    $timestamp = date('Y-m-d G:i:s');
                    $assets=DB::table('asset_disposal')->insertGetId([
                        'ad_id'=>NULL,
                        'asset_category_id'=>$request->input('assets_category'),
                        'asset_id'=>$request->input('assets_code'),
                        'disposal_on'=>date('Y-m-d',strtotime($request->input('assets_disposal_date'))),
                        'disposal_to'=>$request->input('assets_disposal_to'),
                        'disposal_reason'=>$request->input('assets_disposal_reason'),
                        'created_by'=>Auth::id(),
                        'created_at'=>$timestamp
                    ]);
                    $upd_asset = DB::table('assets')->where('asset_id',$request->input('assets_code'))
                    ->update(['allot_bit'=>0,
                    'allot_status'=>'Disposed']);
                    if($assets==NULL) 
                    {
                       DB::rollback();
                        return redirect('/master/assets/disposal')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                           
                        return redirect('/master/assets/disposal')->with('success','Asset Disposed Successfully.');
                    }
                }
        }  catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/master/assets/disposal')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function asset_disposal_list(){
        $data=array('layout'=>'layouts.main');
        return view('employee.asset_disposal_list',$data); 
    }
    public function asset_disposal_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = DB::table('asset_disposal')
        ->leftjoin('assets_category','asset_disposal.asset_category_id','assets_category.ac_id')
        ->leftjoin('assets','assets.asset_id','asset_disposal.asset_id')
        // ->leftjoin('employee__profile','employee__profile.id','asset_disposal.disposal_to')
        ->select('asset_disposal.ad_id',
        'assets_category.category_name','assets.asset_code',
        DB::raw("CONCAT(assets.name,'-',assets.model_number) as asset"),
        'assets.asset_value',
      
        DB::raw('(DATE_FORMAT(asset_disposal.disposal_to ,"%d-%m-%Y")) as employee'),
        DB::raw('(DATE_FORMAT(asset_disposal.disposal_on ,"%d-%m-%Y")) as disposal_on'),
        'asset_disposal.disposal_reason');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('assets_category.category_name','LIKE',"%".$serach_value."%")
                        ->orwhere('assets.name','LIKE',"%".$serach_value."%")
                        ->orwhere('assets.model_number','LIKE',"%".$serach_value."%")
                        ->orwhere('assets.asset_value','LIKE',"%".$serach_value."%")
                        ->orwhere('assets.asset_code','LIKE',"%".$serach_value."%")
                        ->orwhere('asset_disposal.disposal_to','LIKE',"%".$serach_value."%")
                        // ->orwhere('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('disposal_on','LIKE',"%".$serach_value."%")
                        ->orwhere('disposal_to','LIKE',"%".$serach_value."%")
                        ->orwhere('disposal_reason','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['asset_disposal.ad_id','assets_category.category_name',
            'asset','assets.asset_value','assets.asset_code','employee'
            ,'asset_disposal.disposal_on',
            'asset_disposal.disposal_reason'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('asset_disposal.ad_id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
  
    }
    public function create_city() {
        $country = Country::where('id',105)->get()->first();
        $state = State::where('country_id',105)->get();
        $data = array(
            'country'=>$country,
            'state'=>$state,
            'layout'=>'layouts.main'
        );
        return view('city',$data); 
    }

    public function create_city_DB(Request $request) {
         try {
                $validator = Validator::make($request->all(),
                    [
                        'city'=>'required',
                        'country'=>'required',
                        'state'=>'required',
                    ],
                    [
                        'city.required'=>'This Field is required',
                        'country.required'=>'This Field is required',
                        'state.required'=>'This Field is required',
                    ]
                    );
                    $errors = $validator->errors();
                    $getCity = City::where('state_id',$request->input('state'))->where('city',$request->input('city'))->select('city')->get()->first();
                    if ($validator->fails()) 
                    {
                        return redirect('/master/city')->withErrors($errors);
                    }
                    else{
                        if (Str::lower($getCity['city']) == Str::lower($request->input('city'))) {
                            return redirect('/master/city')->with('error','This city is already added.');
                        }else{
                            $city = City::insertGetId([
                                'city'=>$request->input('city'),
                                'state_id'=>$request->input('state'),
                                'created_by' => Auth::id()
                            ]);
                        }
                        if($city == NULL) 
                        {
                           DB::rollback();
                           return redirect('/master/city')->with('error','Some Unexpected Error occurred.');
                        }
                        else{
                            return redirect('/master/city')->with('success','City Successfully added.');
                        }
                    }
            }  catch(\Illuminate\Database\QueryException $ex) {
                return redirect('/master/city')->with('error','some error occurred'.$ex->getMessage());
            }
    }

    public function city_search(Request $request) {
        //DB::enableQueryLog();
        $text = $request->input('text');
        if ($text != '') {
            $city_name = DB::table('cities')->where('cities.state_id', $request->input('state_id'))->where('cities.city', 'Like', "%$text%")
                    ->leftJoin('states','states.id','cities.state_id')
                    ->select('cities.*')
                    ->get();
          return response()->json($city_name);
        }
    }

    public function create_plate_size() {
        $data = array(
           
            'layout'=>'layouts.main'
        );
        return view('sections.plate_size',$data); 
    }
    public function create_plate_sizeDB(Request $request) {
        try {
           
            $validator = Validator::make($request->all(),
                [
                    'ps_value'=>'required'
                ],
                [
                    'ps_value.required'=>'This Field is required',
                ]
                );
                $errors = $validator->errors();
               
                if ($validator->fails()) 
                {
                    return redirect('master/create/plate/size')->withErrors($errors);
                }
                else{
                   
                    $ins_plate = PlateSize::insertGetId([
                        'id'=>null,
                        'value'=>$request->input('ps_value'),
                        'created_by'=>Auth::id()
                        
                    ]);
                    if($ins_plate==NULL) 
                    {
                       DB::rollback();
                        return redirect('master/create/plate/size')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                           
                        return redirect('master/create/plate/size')->with('success','Successfully Created Plate Size.');
                    }
                }
                
        }  catch(\Illuminate\Database\QueryException $ex) {
            return redirect('master/create/plate/size')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function plate_size_summary() {
        $data = array(
            'layout'=>'layouts.main'
        );
        return view('sections.plate_size_list',$data); 
    } 
    public function plate_size_summaryApi(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog =PlateSize::select('id','value','created_at');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('id','LIKE',"%".$serach_value."%")
                        ->orwhere('value','LIKE',"%".$serach_value."%")
                        ->orwhere('created_at','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['id','value','created_at'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    } 
//machine master
    public function create_machine() {
        
        $data = array(
           
            'layout'=>'layouts.main'
        );
        return view('Production.machine_master.machine',$data); 
    }
    public function create_machineDB(Request $request) {
        try {
           
            $validator = Validator::make($request->all(),
                [
                    'ps_value'=>'required',
                    'm_cat'=>'required',
                    'm_brand'=>'required',
                    'm_date'=>'required',
                    'm_bill_no'=>'required',
                    'm_photo' => 'required_with|image|mimes:jpeg,png,jpg,gif,svg|max:'.CustomHelpers::getfilesize(),
                    'm_bill_upload' => 'required_with|file|mimes:jpeg,png,jpg,gif,pdf,svg|max:'.CustomHelpers::getfilesize()
                ],
                [
                    'ps_value.required'=>'This Field is required',
                    'm_cat.required'=>'This Field is required',
                    'm_brand.required'=>'This Field is required',
                    'm_date.required'=>'This Field is required',
                    'm_bill_no.required'=>'This Field is required',
                    'm_photo.required_with'=>'Image accept only jpeg,png,jpg format',
                    'm_bill_upload.required_with'=>'Field accept only jpeg,png,jpg,pdf format'
                    // 'm_photo.required'=>'This Field is required',
                ]
                );
                $errors = $validator->errors();
               
                if ($validator->fails()) 
                {
                    return redirect('master/machine/create')->withErrors($errors);
                }
                else{
                        $image_file="";
                        $bill_file= "";
                        $file = $request->file('m_photo');
                        $billfile = $request->file('m_bill_upload');
                        if(isset($file) || $file != null){
                            $destinationPath = public_path().'/upload/machine/';
                            $filenameWithExt = $request->file('m_photo')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('m_photo')->getClientOriginalExtension();
                            $image_file = $filename.'_'.time().'.'.$extension;
                            $path = $file->move($destinationPath, $image_file);
                        }else{
                            $image_file = '';
                        }

                        if(isset($billfile) || $billfile != null){
                            $destinationPath = public_path().'/upload/machine/';
                            $filenameWithExt = $request->file('m_bill_upload')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('m_bill_upload')->getClientOriginalExtension();
                            $bill_file = $filename.'_'.time().'.'.$extension;
                            $path = $billfile->move($destinationPath, $bill_file);
                        }else{
                            $bill_file = '';
                        }
                    $ins_plate = MachineName::insertGetId([
                        'id'=>null,
                        'name'=>$request->input('ps_value'),
                        'category'=>$request->input('m_cat'),
                        'brand'=>$request->input('m_brand'),
                        'size'=>$request->input('m_size'),
                        'purchase_date'=>date("Y-m-d",strtotime($request->input('m_date'))),
                        'bill_no'=>$request->input('m_bill_no'),
                        'photo'=>$image_file,
                        'bill_upload'=>$bill_file,
                        'created_by'=>Auth::id(),

                    ]);
                    if($ins_plate==NULL) 
                    {
                       DB::rollback();
                       return redirect('master/machine/create')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                           
                        return redirect('master/machine/create')->with('success','Successfully Created Machine.');
                    }
                }
                
        }  catch(\Illuminate\Database\QueryException $ex) {
            return redirect('master/machine/create')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function update_machine($id) {
        $machine=MachineName::where('id',$id)->get()->first();
        $data = array(
           'machine'=>$machine,
            'layout'=>'layouts.main'
        );
        return view('Production.machine_master.machine_update',$data); 
    }
    public function update_machineDB(Request $request,$id) {
        try {
           
            $validator = Validator::make($request->all(),
                [
                    'ps_value'=>'required',
                    'm_cat'=>'required',
                    'm_brand'=>'required',
                    'm_date'=>'required',
                    'm_bill_no'=>'required',
                    'm_photo' => 'required_with|image|mimes:jpeg,png,jpg,gif,svg|max:'.CustomHelpers::getfilesize(),
                    'm_bill_upload' => 'required_with|file|mimes:jpeg,png,jpg,gif,pdf,svg|max:'.CustomHelpers::getfilesize()
                ],
                [
                    'ps_value.required'=>'This Field is required',
                    'm_cat.required'=>'This Field is required',
                    'm_brand.required'=>'This Field is required',
                    'm_date.required'=>'This Field is required',
                    'm_bill_no.required'=>'This Field is required',
                    'm_photo.required_with'=>'Image accept only jpeg,png,jpg format',
                    'm_bill_upload.required_with'=>'Field accept only jpeg,png,jpg,pdf format'
                    // 'm_photo.required'=>'This Field is required',
                ]
                );
                $errors = $validator->errors();
               
                if ($validator->fails()) 
                {
                    return redirect('master/machine/update/'.$id)->withErrors($errors);
                }
                else{
                        $image_file="";
                        $bill_file= "";
                        $file = $request->file('m_photo');
                        $billfile = $request->file('m_bill_upload');
                        if(isset($file) || $file != null){
                            $destinationPath = public_path().'/upload/machine/';
                            $filenameWithExt = $request->file('m_photo')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('m_photo')->getClientOriginalExtension();
                            $image_file = $filename.'_'.time().'.'.$extension;
                            $path = $file->move($destinationPath, $image_file);
                        }else{
                            $image_file = $request->input('hidden_pic');
                        }

                        if(isset($billfile) || $billfile != null){
                            $destinationPath = public_path().'/upload/machine/';
                            $filenameWithExt = $request->file('m_bill_upload')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('m_bill_upload')->getClientOriginalExtension();
                            $bill_file = $filename.'_'.time().'.'.$extension;
                            $path = $billfile->move($destinationPath, $bill_file);
                        }else{
                            $bill_file = $request->input('hidden_bill');
                        }
                    $ins_plate = MachineName::where('id',$id)->update([
                       
                        'name'=>$request->input('ps_value'),
                        'category'=>$request->input('m_cat'),
                        'brand'=>$request->input('m_brand'),
                        'size'=>$request->input('m_size'),
                        'purchase_date'=>date("Y-m-d",strtotime($request->input('m_date'))),
                        'bill_no'=>$request->input('m_bill_no'),
                        'photo'=>$image_file,
                        'bill_upload'=>$bill_file,
                    

                    ]);
                    if($ins_plate==NULL) 
                    {
                       DB::rollback();
                       return redirect('master/machine/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                           
                        return redirect('master/machine/update/'.$id)->with('success','Successfully Updated Machine.');
                    }
                }
                
        }  catch(\Illuminate\Database\QueryException $ex) {
            return redirect('master/machine/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function machine_summary() {
        $data = array(
            'layout'=>'layouts.main'
        );
        return view('Production.machine_master.machine_list',$data); 
    } 
    public function machine_summaryApi(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog =MachineName::select('id','name','category','brand','size','created_at','purchase_date','bill_no','bill_upload','photo');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('id','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('category','LIKE',"%".$serach_value."%")
                        ->orwhere('brand','LIKE',"%".$serach_value."%")
                        ->orwhere('size','LIKE',"%".$serach_value."%")
                        ->orwhere('purchase_date','LIKE',"%".$serach_value."%")
                        ->orwhere('bill_no','LIKE',"%".$serach_value."%")
                        ->orwhere('created_at','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['id','name','category','brand','size','created_at','purchase_date','bill_no','bill_upload','photo'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    } 
    public function machine_delete($id){
        $machine=MachineName::where('id',$id)->delete();
       if($machine)
            return 1;
        else
            return 0;
    }
    public function employee_assets_report(){
        $employee =EmployeeProfile::all();
        $data=array('layout' => 'layouts.main',
            'employee'=>$employee);
        return view('employee.employee_assets_report',$data);
    }
    public function employee_assets_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog =AssetAssign::where('employee__profile.id',$request->input('emp'))
        ->where("assets.allot_status","Assigned")
        ->leftjoin('assets_category','asset_assign.asset_category_id','assets_category.ac_id')
        ->leftjoin('assets','assets.asset_id','asset_assign.asset_id')
        ->leftjoin('employee__profile','employee__profile.id','asset_assign.employee_id')
        ->select('asset_assign.aa_id',
        'assets_category.category_name','assets.name',
        'assets.model_number','assets.asset_value','assets.asset_code'
        ,DB::raw("CONCAT(employee__profile.name,'-',employee__profile.employee_number) as employee"),
        'asset_assign.from_date','asset_assign.to_date','asset_assign.asset_form','assets.allot_status');
        if(!empty($serach_value))
        {
            $userlog =$userlog->where(function($query) use ($serach_value){
                $query->where('assets_category.category_name','LIKE',"%".$serach_value."%")
                            ->orwhere('assets.name','LIKE',"%".$serach_value."%")
                            ->orwhere('model_number','LIKE',"%".$serach_value."%")
                            ->orwhere('asset_value','LIKE',"%".$serach_value."%")
                            ->orwhere('asset_code','LIKE',"%".$serach_value."%")
                            ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
                            ->orwhere('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                            ->orwhere('from_date','LIKE',"%".$serach_value."%")
                            ->orwhere('to_date','LIKE',"%".$serach_value."%")
                            ->orwhere('allot_status','LIKE',"%".$serach_value."%")

                            ;
            
            });
            
        }
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['asset_assign.aa_id','assets_category.category_name','assets.name'
            ,'assets.model_number','assets.asset_value','assets.asset_code','employee'
            ,'asset_assign.from_date','asset_assign.to_date','allot_status'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('asset_assign.aa_id','desc');
        }

        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function assets_view($id){
        // $asset_category = DB::table('assets_category')->get();
        $assets=DB::table('assets')->where('asset_id',$id)
        ->leftjoin('assets_category','assets.asset_category_id','assets_category.ac_id')
        ->get()->first();
        
        $data=array('assets'=>$assets,'layout'=>'layouts.main','id'=>$id);
        return view('employee.assets_view',$data); 
    }
    
}
