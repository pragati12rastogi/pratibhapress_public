<?php

namespace App\Http\Controllers\vendor;
use App\Model\State;
use App\Model\Employee;
use App\Model\Vendor\vendors;
use App\Model\Purchase\LevelAuthority;
use App\Model\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use Session;
use App\dkerp;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Response;
use App\Custom\CustomHelpers;
use \Carbon\Carbon;
use App\Http\Controllers\Controller;

class Vendor extends Controller
{
    public function  vendor_list()
    {
        $data=array('layout'=>'layouts.main');
        return view('vendor.vendor_summary',$data); 
    }
    public function vendor_list_api(Request $request)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
           
            $api_data= vendors::leftJoin('states','states.id','vendor.state')
           ->leftJoin('payment_term','payment_term.id','=','vendor.payment_term_id') 
           ->leftJoin('pur_pass_authority','pur_pass_authority.id','=','vendor.level_authority1') 
           ->leftJoin('pur_pass_authority as lv2','lv2.id','=','vendor.level_authority2') 
            ->select(
                'vendor.id',
                'vendor.name as vendor_name',
                'vendor.address',
                'vendor.con_person',
                'vendor.number',
                'vendor.alt_num',
                'vendor.email',
                'vendor.gst',
                'vendor.pan',
                'lv2.user_name as level_authority2',
                'pur_pass_authority.user_name as level_authority1',
                'payment_term.value as payment',
                'states.name as state'
            );
            if(!empty($serach_value))
            {
                $api_data->where(function($query) use ($serach_value){
                    $query->where('vendor.name','like',"%".$serach_value."%")
                    ->orwhere('vendor.address','like',"%".$serach_value."%")
                    ->orwhere('vendor.con_person','like',"%".$serach_value."%")
                    ->orwhere('vendor.number','like',"%".$serach_value."%")
                    ->orwhere('vendor.email','like',"%".$serach_value."%")
                    ->orwhere('vendor.gst','like',"%".$serach_value."%")
                    ->orwhere('vendor.pan','like',"%".$serach_value."%")
                    ->orwhere('vendor.level_authority1','like',"%".$serach_value."%")
                    ->orwhere('vendor.level_authority2','like',"%".$serach_value."%");
                });
            }
            if(isset($request->input('order')[0]['column']))
            {
                $data = [
                    'vendor.id',
                'vendor.name',
                'vendor.address',
                'vendor.con_person',
                'vendor.number',
                'vendor.alt_num',
                'vendor.email',
                'vendor.gst',
                'vendor.pan',
                'vendor.level_authority1',
                'vendor.level_authority2',
                'payment_term.value',
                'states.name'
                ];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $api_data->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else
                $api_data->orderBy('vendor.id','desc');      
        
        $count = count( $api_data->get()->toArray());
        $api_data = $api_data->offset($offset)->limit($limit)->get()->toArray();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $api_data; 
        return json_encode($array); 
    }

    public function vendor_create(){
        $state=State::get();
        $payment=Payment::get();
        $authority1=LevelAuthority::where('level_type','1')->get();
        $authority2=LevelAuthority::where('level_type','2')->get();
        $data=[
            'layout' => 'layouts.main',
            'state'=>$state,
            'payment'=>$payment,
            'authority1'=>$authority1,
            'authority2'=>$authority2,
        ];
        return view('vendor.create_vendor',$data);
    }
    public function vendor_createDb(Request $request){
        //print_r($request->input());die;
        try {
            $timestamp = date('Y-m-d G:i:s');
            $validerrarr =[
                'name'=>'required',
                'address'=>'required',
                'state'=>'required',
                'person'=>'required',
                'num'=>'required|unique:vendor,number',
                'email'=>'unique:vendor,email',
                'pay'=>'required',
                'lev1'=>'required',
                'lev2'=>'required',
                'gst'=>'required_if:gst_type,1',
                'gst_sel'=>'required_if:gst_type,0',
                'pan'=>'required_if:pan_type,1',
                'pan_sel'=>'required_if:pan_type,0',
                'acc_name'=>'required',
                'acc_number'=>'required',
                'acc_ifsc'=>'required'
                
            ]; 
            $validmsgarr =[
                'name.required'=>'This field is required',
                'address.required'=>'This field is required',
                'state.required'=>'This field is required',
                'person.required'=>'This field is required',
                'num.required'=>'This field is required',
                'alt.required'=>'This field is required',
                // 'email.required'=>'This field is required',
                'pay.required'=>'This field is required',
                'gst.required'=>'This field is required',
                'pan.required'=>'This field is required',
                'lev1.required'=>'This field is required',
                'lev2.required'=>'This field is required',
                'email.unique'    => 'Sorry, This Email Address Is Already Exist',
                'num.unique'      => 'Sorry, This Phone Is Already Exist',
                'acc_name.required'=>'Account Name is required',
                'acc_number.required'=>'Account Number is required',
                'acc_ifsc.required'=>'IFSC Code is required'
            ];
            // $count=count($request->input('name'));
            $request->session()->flash('lastformdata',$request->all());        
            $this->validate($request,$validerrarr,$validmsgarr);
                $vendor=vendors::insertGetId([
                    'id'=>NULL,
                    'name'=>$request->input('name'),
                    'address' => $request->input('address'),    
                    'state' =>$request->input('state'),
                    'con_person' => $request->input('person'),
                    'number' =>  $request->input('num'),
                    'alt_num' => $request->input('alt'),  
                    'email' => $request->input('email'),    
                    'payment_term_id' =>$request->input('pay'),
                    'gst' =>$request->input('gst_type') ==0 ? $request->input('gst_sel') : $request->input('gst'),
                    'pan' =>$request->input('pan_type') ==0 ? $request->input('pan_sel') : $request->input('pan'),
                    'level_authority1' => $request->input('lev1'),  
                    'level_authority2' => $request->input('lev2'),  
                    'acc_name'=>$request->input('acc_name'),
                    'acc_number'=>$request->input('acc_number'),
                    'acc_ifsc'=>$request->input('acc_ifsc'),
                    'created_by' => Auth::id(),
                    'created_at' => $timestamp,     
                ]);
                if($vendor==NULL) 
                {
                   DB::rollback();
                    return redirect('/vendor/create')->with('error','Some Unexpected Error occurred.');
                }
                else{  
                    Session::forget('lastformdata');  
                    return redirect('/vendor/create')->with('success','Successfully Created Vendor.');      
                }
        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/vendor/create')->with('error','some error occurred'.$ex->getMessage());
    
            }
       
    }

    public function vendor_update($id){

        $vendor=vendors::where('id',$id)->get([
                'vendor.id',
                'vendor.name',
                'vendor.address',
                'vendor.con_person',
                'vendor.number',
                'vendor.alt_num',
                'vendor.email',
                'vendor.gst',
                'vendor.pan',
                'vendor.level_authority1',
                'vendor.level_authority2',
                'vendor.payment_term_id',
                'vendor.state',
                'vendor.acc_name',
                'vendor.acc_number',
                'vendor.acc_ifsc'
        ]);
        $state=State::get();
        $payment=Payment::get();
        $authority1=LevelAuthority::where('level_type','1')->get();
        $authority2=LevelAuthority::where('level_type','2')->get();
        $data=[
            'id'=>$id,
            'layout' => 'layouts.main',
            'state'=>$state,
            'payment'=>$payment,
            'authority1'=>$authority1,
            'authority2'=>$authority2,
            'vendor'=>$vendor
        ];
        //return $vendor;
        return view('vendor.update_vendor',$data);
    }
    public function vendor_updateDb(Request $request,$id){
       // print_r($request->input());die;
        try {
            $timestamp = date('Y-m-d G:i:s');
            $this->validate($request,[
                'name'=>'required',
                'address'=>'required',
                'state'=>'required',
                'person'=>'required',
                'num'=>'required|unique:vendor,number,'.$id,
                'email'=>'unique:vendor,email,'.$id,
                'pay'=>'required',
                'gst'=>'required_if:gst_type,1',
                'gst_sel'=>'required_if:gst_type,0',
                'pan'=>'required_if:pan_type,1',
                'pan_sel'=>'required_if:pan_type,0',
                'lev1'=>'required',
                'lev2'=>'required',
                'acc_name'=>'required',
                'acc_number'=>'required',
                'acc_ifsc'=>'required'
            ],
        [
            'name.required'=>'This field is required',
            'address.required'=>'This field is required',
            'state.required'=>'This field is required',
            'person.required'=>'This field is required',
            'num.required'=>'This field is required',
            'alt.required'=>'This field is required',
            // 'email.required'=>'This field is required',
            'pay.required'=>'This field is required',
            'gst.required'=>'This field is required',
            'pan.required'=>'This field is required',
            'lev1.required'=>'This field is required',
            'lev2.required'=>'This field is required',
            'email.unique'    => 'Sorry, This Email Address Is Already Exist',
            'num.unique'      => 'Sorry, This Phone Is Already Exist'  ,
            'acc_name.required'=>'Account Name is required',
            'acc_number.required'=>'Account Number is required',
            'acc_ifsc.required'=>'IFSC Code is required'  
        ]);
        CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Vendor Profile Update");
            // $count=count($request->input('name'));
            $request->session()->flash('lastformdata',$request->all());        
                $vendor=vendors::where('id',$id)->update([
                    
                    'name'=>$request->input('name'),
                    'address' => $request->input('address'),    
                    'state' =>$request->input('state'),
                    'con_person' => $request->input('person'),
                    'number' =>  $request->input('num'),
                    'alt_num' => $request->input('alt'),  
                    'email' => $request->input('email'),    
                    'payment_term_id' =>$request->input('pay'),
                    'gst' =>$request->input('gst_type') ==0 ? $request->input('gst_sel') : $request->input('gst'),
                    'pan' =>$request->input('pan_type') ==0 ? $request->input('pan_sel') : $request->input('pan'),
                    'level_authority1' => $request->input('lev1'),  
                    'level_authority2' => $request->input('lev2'),  
                    'acc_name'=>$request->input('acc_name'),
                    'acc_number'=>$request->input('acc_number'),
                    'acc_ifsc'=>$request->input('acc_ifsc')
                ]);
                if($vendor==NULL) 
                {
                   DB::rollback();
                    return redirect('/vendor/list/edit/'.$id)->with('error','Some Unexpected Error occurred.');
                }
                else{  
                    Session::forget('lastformdata');  
                    return redirect('/vendor/list/edit/'.$id)->with('success','Successfully Updated Vendor.');      
                }
        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/vendor/list/edit/'.$id)->with('error','some error occurred'.$ex->getMessage());
    
            }
       
    }
}
