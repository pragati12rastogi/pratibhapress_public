<?php

namespace App\Http\Controllers;

use App\Model\Party;
use App\Model\State;
use App\Model\City;
use App\Model\Country;
use App\Model\Payment;
use App\Model\Reference;
use App\Model\Consignee;
use App\Model\InternalOrder;
use App\Model\Client_po;
use App\Model\Delivery_challan;
use App\Model\Tax_Invoice;
use App\Imports\Import;
use App\Custom\CustomHelpers;
use Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Hash;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use App\dkerp;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PartyController extends Controller
{
//--------------------------Party Form------------------------------------------------------
    public function party_form()
    {
        try
        {
            $payment = Payment::all();
            $countries=Country::all();
            $reference_name = Reference::all();
            $data=array(
                'layout'=>'layouts.main'
                ,'countries'=>$countries,
                'payment'=>$payment,
                'reference_name'=>$reference_name
            );
            return view('sections.party_form', $data);
        }
        catch(Exception $e)
        {
           return "No Data Found..";
        }
    }
    public function partylist()
    {
        $data=array('layout'=>'layouts.main');
        return view('sections.partylist', $data);
    }
    public function partylistdata(Request $request)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $partydata = Party::leftJoin('states', function($join) {
                                        $join->on('states.id', '=', 'party.state_id');
                                    })->leftJoin('party_reference','party_reference.id','party.reference_name');
        if(!empty($serach_value))
            $partydata->where('partyname','LIKE',"%".$serach_value."%")
                ->orWhere('party_reference.referencename','LIKE',"%".$serach_value."%")
                ->orWhere('gst','LIKE',"%".$serach_value."%")
                ->orWhere('email','LIKE',"%".$serach_value."%");
        $count = $partydata->count();
        $partydata = $partydata->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['id','partyname','party_reference.reference_name','gst', 'email','state','party.created_time'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $partydata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $partydata->orderBy('id','desc');
        }
        $partydata = $partydata->select(
            'party.id',
            'partyname',
            'party_reference.referencename as reference_name',
            DB::raw('if(gst="","NA",gst) as gst'),
            'gst_pointer',
            'email',
            'states.name as state',
            // 'party.created_time'
            DB::raw('(DATE_FORMAT(party.created_time ,"%d-%m-%Y %r")) as created')
               )->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] =$partydata; 
        return json_encode($array);
    }
   //---------------------------States--------------------------------------------------------
    public function states($id)
    {
        try
        {
            $state = State::where('country_id', $id)->get();
            return $state;
        }
        catch(Exception $e)
        {
            return "No Data Found..";
        }
    }
    // ------------------------------Cities------------------------------------------------------
    public function cities($id)
    {
        try
        {
            $city = City::where('state_id', $id)->get();
            return $city;
        }
        catch(Exception $e)
        {
            return "No Data Found..";
        }
    }
//--------------------------Create Party Integration--------------------------------------------------
    public function party_insert_db(Request $request)
    {
        try {
            // print_r($request->input());die;
                $this->validate($request,
                [
                    'phone'=>'required|unique:party,contact',
                    'email'=>'required|unique:party,email',
                    'partyname'=>'required',
                    'city'=>'required',
                    'address'=>'required',
                    'pincode'=>'required',
                    'state'=>'required',
                    'country'=>'required',
                    'contact_person'=>'required',
                    'payment_term_id'=>'required',
                    'gst'=>'required_if:gst_type,1',
                    'gst_sel'=>'required_if:gst_type,0',
                    'pan'=>'required_if:pan_type,1',
                    'pan_sel'=>'required_if:pan_type,0',
                    'ref_type'=>'required|numeric',
                    'reference_name'=>'required_if:ref_type,0',
                    'reference_name_sel'=>'required_if:ref_type,1',

                ],
                [
                    'email.unique'    => 'Sorry, This Email Address Is Already Exist',
                    'phone.unique'      => 'Sorry, This Phone Is Already Exist',
                ]
            );
                $address=$request->input('address');
                $pincode=$request->input('pincode');
                $code = 'GSTIN' .rand(10,99).rand(10,99).rand(10,99).rand(10,99).rand(10,99);
                if($request->input('ref_type') ==1){
                    $ref=$request->input('reference_name_sel');
                    $reference=Reference::where('id',$ref)->select('referencename')->get()->first();
                    $reference = isset($reference->referencename)?$reference->referencename:'';
                    $changes_array['reference_name_sel'] = $reference;
                }
                else{
                    $this->validate($request,
                    [
                       
                        'reference_name'=>'unique:party_reference,referencename',
    
                    ],
                    [
                        'reference_name.unique'    => 'Sorry, This Reference Name Is Already Exist',
                        
                    ]
                    );
                    $ref=Reference::insertGetId([
                        'referencename'=>$request->input('reference_name')
                    ]);
                    CustomHelpers::userActionLog('Reference Created',$ref,'Reference Created');
                    $changes_array['reference_name'] = $request->input('reference_name');

                    if($ref==NULL){
                        return redirect('/client/create')->with('error','some error occurred')->withInput();
                    }
                }
                // print_r($ref);die;
                $party= Party::insertGetId(
                    [
                        'id' => NULL,
                        'partyname' => $request->input('partyname'),
                        'address' => $address,
                        'pincode' => $pincode,
                        'city_id' => $request->input('city'),
                        'state_id' =>$request->input('state'),
                        'country_id' => $request->input('country'),
                        'contact_person' => $request->input('contact_person'),
                        'contact' => $request->input('phone'),
                        'alt_contact' =>$request->input('alt_contact'),
                        'email' => $request->input('email'),
                        'payment_term_id' => $request->input('payment_term_id'),
                        'gst' => $request->input('gst_type') ==0 ? $code : $request->input('gst'),
                        'gst_pointer' => $request->input('gst_type'),
                        'pan' =>$request->input('pan_type') ==0 ? $request->input('pan_sel') : $request->input('pan'),
                        'reference_name' => $ref,
                        'created_by'=>Auth::id(),
                        'is_active'=>'1',
                        'created_time'=>date('Y-m-d G:i:s')
                    ]);
                $consignee= Consignee::insert(
                    [
                        'id' => NULL,
                        'consignee_name' =>$request->input('partyname'),
                        'party_id' =>$party,
                        'gst' =>$request->input('gst_type') ==0 ? $request->input('gst_sel') : $request->input('gst'),
                        'pan' =>$request->input('pan_type') ==0 ? $request->input('pan_sel') : $request->input('pan'),
                        'address' =>$address,
                        'city' => $request->input('city'),
                        'pincode' => $pincode,
                        'state' =>$request->input('state'),
                        'country' =>$request->input('country'),
                        'created_by'=>Auth::id(),
                        'is_active'=>1,
                        'created_time'=>date('Y-m-d G:i:s'),
                                   
                    ]
                );
                 /*** USER LOG ***/
             $city=City::where('id',$request->input('city'))->select('city')->get()->first();
             $city = isset($city->city)?$city->city:'';
             $changes_array['city'] = $city;

             $state=State::where('id',$request->input('state'))->select('name')->get()->first();
             $state = isset($state->name)?$state->name:'';
             $changes_array['state'] = $state;

             $country=Country::where('id',$request->input('country'))->select('name')->get()->first();
             $country = isset($country->name)?$country->name:'';
             $changes_array['country'] = $country;

             $log_array=array(
                
                'reference_name'=>'Reference Name',
                'reference_name_sel'=>'Reference Name',
                'partyname'=>'Party'
         
             );
             CustomHelpers::userActionLog('Party Created',$party,'Party Created',$log_array,$changes_array);
            /***  END USER LOG ***/
                return redirect('/client/create')->with('success', 'Client has been created.');
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/client/create')->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }

    public function reference_search(Request $request) {
        //DB::enableQueryLog();
        $text = $request->input('text');
        if ($text != '') {
            $ref_name = Reference::where('referencename', 'Like', '%' . $text . '%')
                    ->select('party_reference.*')
                    ->orderby('referencename')
                    ->take(10)
                    ->get();
        return response()->json($ref_name);
        }
       
    }
//----------------------------------Consignee Form------------------------------------------------------------
    public function consignee()
    { 
        $party=City::join('party', 'cities.id', '=', 'party.city_id')
                        ->get(['party.id','party.partyname','party.city_id','cities.city']);
        $countries=Country::all();
        $data=array('layout'=>'layouts.main','countries'=>$countries,'party'=>$party);
        return view('sections.consignee_form', $data);
    }

    
    public function consignee_insert_excel(Request $request)
    {
        $request->session()->flash('data_submit_type','excel');
       
        $out = $request->all();
        unset($out['excel']);
        $request->session()->flash('lastformdata',$out);        

        $this->validate($request,
                [
                    'party'=>'required',
                    'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
                ],
                [
                    'party.required'=>Lang::get('consignee_form.Party_Name_Require_Err'),
                    'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                    'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
                ]
            );

        try{
            if($request->file('excel'))                
            {
                DB::beginTransaction();
                $party_id = $request->input('party');
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                
                $error="";
                $total_error=0;
                if($data)
                {    $v= $data[0];
                    
                }
                $column_name_format = array('consignee_name','gst','pan','address',
                            'pincode','country','state','city'
                        );
                $index=0;
                $column_name_err=0;
                $char = '65';
                if(count($v)<=1){
                    $request->session()->flash('importerrors',"");
                    return redirect('/consignee/create')->with('error', 'Please insert some data in excel sheet.');
                }
                if(count($column_name_format)==count($v[0]))
                {    
                    foreach($v[0] as $p=>$q)
                    {
                        if($q!=$column_name_format[$index])
                        {
                            $column_name_err++;
                            $error=$error."Column Name not in provided format. Error At ".chr($char)."1.";
                        }
                        $index++;
                        $char++;
                    }
                }
                else
                {
                    $column_name_err++;
                    $error=$error."Column Name not in provided format. Please re-download the format and try again.";

                }
                $total_error=$total_error+$column_name_err;

                if($column_name_err==0)
                {
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '65';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($v1 == "")
                            {    
                                $error=$error."Empty Cell at ".chr($char).($i+1).". "; 
                                $fl++;
                            }
                            $char++; 
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {
                            
                            $data_err=0;
                            $country = Country::where('name','like',$v[$i]['5'])
                            ->get('id')->first();
                            if(!$country)
                            {
                                $error = $error."Country at F".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                            else
                                $state = State::where('country_id','=',$country->id)->
                                where('name','like',$v[$i]['6'])
                                ->get('id')->first();
                            if(!$country || !$state)
                            {
                                $error = $error."State at G".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                            else    
                                $city = City::where('state_id','=',$state->id)->
                                where('city','like',$v[$i]['7'])
                                ->get('id')->first();
                            if(!$country || !$state || !$city )
                            {
                                $error = $error."City at H".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                            $total_error=$total_error+$data_err;
                            
                            if($data_err==0)
                            {
                                
                                Consignee::insertGetId(
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
                                        ]
                                    );
                               
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                $error = $error." No data is inserted.";
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',"");
                DB::commit();
                return redirect('/consignee/create')->with('success', 'Consignee data has been imported.');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                return redirect('/consignee/create')->with('error', 'Errors Found.');        
            }
            else
            {    $request->session()->flash('importerrors', $error."Rest Data is Inserted.");
                return redirect('/consignee/create')->with('error', 'Errors Found.');        
            }
        }
        catch (Exception $e)
        {   
            $request->session()->flash('importerrors', $error);
            DB::rollback();    
            return redirect('/consignee/create')->with('error', 'Errors Found.');
        }
    }
 
    public function consignee_insert(Request $request){
        try
        {
           
            // print_r($arr);
            // print_r($request->input());die;
            $request->session()->flash('data_submit_type','data');

          $validerrarr =['party'=>'required',
               'area.*'=>'required|between:0,500',
               'name.*'=>'required',
               'city.*'=>'required|digits_between:0,4',
               'pincode.*'=>'required|digits_between:0,6|numeric',
               'state.*'=>'required|digits_between:0,2',
               'country.*'=>'required|digits:3',
               
            //    'gst.*'=>'required_if:gst_type.*,1',
            //    'gst_sel.*'=>'required_if:gst_type.*,0',
            //    'pan.*'=>'required_if:pan_type.*,1',
            //    'pan_sel.*'=>'required_if:pan_type.*,0',
          ] ; 
          $validmsgarr =[
               'party.required'=>'Party Name is Required',
               'area.*.required'=>'Address is Required',
               'area.*.between'=>'Address Lenght should be between :min & :max',
               'name.*.required'=>'Consignee Name Is  Required',
               'city.*.required'=>'City is Required',
               'city.*.digits_between'=>'City length should be between :min & :max',
               'pincode.*.required'=>'Pincode is Required',
               'pincode.*.numeric'=>'Pincode Contains only numbers',
               'pincode.*.digits_between'=>'Pincode length should be between :min & :max',
               'state.*.required'=>'State is Required',
               'state.*.digits_between'=>'State length should be between :min & :max',
               'country.*.required'=>'Country is Required',
               'country.*.between'=>'Country Lenght should be between :min & :max',
            //    'gst.*.required'=>'Consignee GST/IN is required',
            //    'gst_sel.*.required'=>'Consignee GST/IN is required',
            //    'pan.*.required'=>'Consignee PAN is required',
            //    'pan_sel.*.required'=>'Consignee PAN is required',
       ]; 
          $count=count($request->input('name'));
          $request->session()->flash('lastformdata',$request->all());        
          $this->validate($request,$validerrarr,$validmsgarr);
  
  
        $gst_type=array_merge($request->input('gst_type'))  ;
        $pan_type=array_merge($request->input('pan_type'))  ;
          $id=Auth::id();
          $address= $request->input('area');
          $pincode= $request->input('pincode');
          $country= $request->input('country');
          $state= $request->input('state');
          $city= $request->input('city');
          $consignee_name= $request->input('name');
          $party_id= $request->input('party');
          $gst= array_merge($request->input('gst'))  ;
          $gst_sel= array_merge($request->input('gst_sel'))  ;
          $pan= array_merge($request->input('pan'))  ;
          $pan_sel= array_merge($request->input('pan_sel'))  ;
          for($i=0;$i<$count;$i++){
              $consignee= Consignee::insertGetId(
                  [
                      'id' => NULL,
                      'consignee_name' => @$consignee_name[$i],
                      'party_id' =>$party_id,
                      'gst' =>$gst_type[$i] ==0 ? $gst_sel[$i] : $gst[$i],
                      'pan' => $pan_type[$i] ==0 ? $pan_sel[$i] : $pan[$i],
                      'address' => @$address[$i],
                      'city' =>@$city[$i],
                      'pincode' => @$pincode[$i],
                      'state' => @$state[$i],
                      'country' =>@$country[$i],
                      'created_by'=>$id,
                      'is_active'=>1,
                      'created_time'=>date('Y-m-d G:i:s'),
                                 
                  ]
              );
              /*** USER LOG ***/
              $city=City::where('id',$city[$i])->select('city')->get()->first();
              $city = isset($city->city)?$city->city:'';
              $changes_array['city'] = $city;

              $state=State::where('id',$state[$i])->select('name')->get()->first();
              $state = isset($state->name)?$state->name:'';
              $changes_array['state'] = $state;

              $country=Country::where('id',$country[$i])->select('name')->get()->first();
              $country = isset($country->name)?$country->name:'';
              $changes_array['country'] = $country;

              $party=Party::where('id',$request->input('party'))->select('partyname')->get()->first();
              $party = isset($party->partyname)?$party->partyname:'';
              $changes_array['party'] = $party;

                if($gst_type[$i]==1){
                    $changes_array['gst_type'] = 'Yes';
                }
                else{
                    $changes_array['gst_type'] = 'No';
                }

                if($pan_type[$i]==1){
                    $changes_array['pan_type'] = 'Yes';
                }
                else{
                    $changes_array['pan_type'] = 'No';
                }
              CustomHelpers::userActionLog('Consignee Created',$consignee,'Consignee Created');
          }

          $request->session()->forget('lastformdata');
          return redirect('/consignee/create')->with('success', 'Consignees has been added.');
        }
        catch(\Illuminate\Database\QueryException $ex) {
          return redirect('/consignee/create')->with('error','some error occurred'.$ex->getMessage());
      }
  
      }
  

    public function update_party(Request $request) {
        if(!isset($request->id))
            abort(404);
        $party = Party::where('party.id', '=', trim($request->id, "'"))
                        ->leftJoin('cities', function($join){
                            $join->on('party.city_id', "=", 'cities.id');
                        })
                        ->leftJoin('party_reference','party_reference.id','party.reference_name')
                        ->select(['party.*','cities.city as city','party_reference.referencename','party_reference.id as refer_id']);
        $payment = Payment::all();
        $countries = Country::all();
        $states = State::all();
        $city = City::all();
        $reference_name = Reference::all();
        
        $data = array('layout'=>'layouts.main','party' => $party->first(), 'countries'=>$countries,'payment'=>$payment,'reference_name'=>$reference_name,
                        'states' => $states, 'city' => $city);
        return view('sections.update_party', $data);
    }

    public function do_party_update(Request $request){
        if(!isset($request->id))
            abort(404);
        try {
            // print_r($request->input());die;
            $this->validate($request,
                [
                    'update_reason'=>'required',
                    'partyname'=>'required',
                    'city'=>'required',
                    'address'=>'required|max:100',
                    'pincode'=>'required|max:10000000|numeric',
                    'state'=>'required',
                    'country'=>'required',
                    'contact_person'=>'required',
                    'payment_term_id'=>'required',
                    'gst'=>'required_if:gst_type,1',
                    'gst_sel'=>'required_if:gst_type,0',
                    'pan'=>'required_if:pan_type,1',
                    'pan_sel'=>'required_if:pan_type,0',
                    'reference_name'=>'required',
                    'reference_name'=>'unique:party_reference,referencename,'.$request->input('refer_id'),
                    'phone'=>'required|unique:party,contact,'.$request->id,
                    'email'=>'required|unique:party,email,'.$request->id

                ],
                [
                    'reference_name.unique'    => 'Sorry "'.$request->input('reference_name').'" Is Already Exist',
                ]
            );
            $add=$request->input('address');
            $pincode=$request->input('pincode');
            $fulladd=array($add,$pincode);
            $address=implode(',', $fulladd);
            $code = 'GSTIN' .rand(10,99).rand(10,99).rand(10,99).rand(10,99).rand(10,99);
            
            $gst_code = $request->input('gst_type') ==0 ? $code : $request->input('gst');
            $gst_pointer = $request->input('gst_type') ==1 ? '1' : '0';
            $pan_number = $request->input('pan_type') ==0 ? $request->input('pan_sel') : $request->input('pan');

            if($request->input('gst_type')==1){
                $changes_array['gst_type'] = 'Yes';
            }
            else{
                $changes_array['gst_type'] = 'No';
            }

            if($request->input('pan_type')==1){
                $changes_array['pan_type'] = 'Yes';
            }
            else{
                $changes_array['pan_type'] = 'No';
            }

            if($request->input('ref_type') ==1){
                $ref=$request->input('reference_name_sel');
                $reference=Reference::where('id',$ref)->select('referencename')->get()->first();
                $reference = isset($reference->referencename)?$reference->referencename:'';
                $changes_array['reference_name_sel'] = $reference;
                $changes_array['ref_type'] = 'New';
            }
            else{
                $this->validate($request,
                [
                   
                    'reference_name'=>'unique:party_reference,referencename',

                ],
                [
                    'reference_name.unique'    => 'Sorry, This Reference Name Is Already Exist',
                    
                ]
            );
                $ref=Reference::insertGetId([
                    'referencename'=>$request->input('reference_name')
                ]);
                CustomHelpers::userActionLog('Reference Created',$ref,'Reference Created');
                $changes_array['reference_name'] = $request->input('reference_name');
                $changes_array['ref_type'] = 'Existing';
                if($ref==NULL){
                    return redirect('/client/update?id='.$request->id)->with('error','some error occurred')->withInput();
                }
            }
            $party = Party::where('id',$request->id)->update(
                [
                    'partyname' => $request->input('partyname'),
                    'address' => $address,
                    'city_id' => $request->input('city'),
                    'state_id' =>$request->input('state'),
                    'country_id' => $request->input('country'),
                    'contact_person' => $request->input('contact_person'),
                    'alt_contact' =>$request->input('alt_contact'),
                    'contact' => $request->input('phone'),
                    'email' => $request->input('email'),
                    'reference_name' => $ref,
                    'payment_term_id' => $request->input('payment_term_id'),
                    'gst' => $gst_code,
                    'gst_pointer' => $gst_pointer,
                    'pan' =>$pan_number
                ]);
                /*** USER LOG ***/
                $city=City::where('id',$request->input('city'))->select('city')->get()->first();
                $city = isset($city->city)?$city->city:'';
                $changes_array['city'] = $city;

                $state=State::where('id',$request->input('state'))->select('name')->get()->first();
                $state = isset($state->name)?$state->name:'';
                $changes_array['state'] = $state;

                $country=Country::where('id',$request->input('country'))->select('name')->get()->first();
                $country = isset($country->name)?$country->name:'';
                $changes_array['country'] = $country;

                $log_array=array(
                
                'reference_name'=>'Reference Name',
                'reference_name_sel'=>'Reference Name',
                'partyname'=>'Party Name',
                'city'=>'City',
                'state'=>'State',
                'country'=>'Country',
                'gst_type'=>'GST Type',
                'pan_type'=>'PAN/IT Type',
                'ref_type'=>'Reference Type'

                );
                CustomHelpers::userActionLog($request->input()['update_reason'],$request->id,'Client Update',$log_array,$changes_array,$removekeys=array('ref_type','id'));
                /***  END USER LOG ***/

            return redirect('/client/update?id='.$request->id)->with('success', 'Client has been updated.');
        }
        catch(\Illuminate\Database\QueryException $ex) {
            // print_r($ex->getMessage());die;
            return redirect('/client/update?id='.$request->id)->with('error','some error occurred')->withInput();
        }
    }

    public function view_party(Request $request){
        if(!isset($request->id))
            abort(404);
        $party = Party::where('party.id', '=', trim($request->id, "'"))
                    ->leftJoin('cities', function($join){
                        $join->on('party.city_id', "=", 'cities.id');
                    })
                    ->leftJoin('states', function($join){
                        $join->on('states.id', '=', 'party.state_id');
                    })
                    ->leftJoin('countries', function($join){
                        $join->on('countries.id', '=', 'party.country_id');
                    })
                    ->leftJoin('payment_term', function($join){
                        $join->on('payment_term.id', '=', 'party.payment_term_id');
                    })
                    ->leftJoin('users', function($join){
                        $join->on('users.id', '=', 'party.created_by');
                    })
                    ->leftJoin('party_reference','party_reference.id','party.reference_name')
                    ->select(['party.*','cities.city as city', 'states.name as state','users.name as username', 'countries.name as country', 
                                'payment_term.value as payment_term','party_reference.referencename']);
        $data = array('layout'=>'layouts.main','party' => $party->first());
        return view('sections.view_party', $data);
    }
    public function payment_term_insert_form()
    {        
        $data=array(
            'layout' => 'layouts.main'
        );
        return view('sections.create_payment_term',$data);
    }
    public function payment_term_insert(Request $request)
    {        
        try {
            $this->validate($request,
            [
                'payment'=>'required',
            ],
            [
                'payment.required'=>'Payment Term is Required',
            
            ]
        );
            $timestamp = date('Y-m-d G:i:s');
            Payment::insert(
                [
                    'id' => NULL,
                    'created_by' => Auth::id(),
                    'is_active' =>1,
                    'value'=> $request->input('payment'),
                    'created_at' => $timestamp,
                ]
            );
            return redirect('/paymentterm/create')->with('success','Payment Term created successfully.');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/paymentterm/create')->with('error','some error occurred')->withInput();
        }
    }
    
    public function payment_term_update_form($id)
    {        
        $payment = Payment::where('id','=',$id)
        ->where('is_active','=',1)
        ->get()->first();
        $data=array(
        'layout' => 'layouts.main',
        'data'=> $payment
        );
        if($payment)
            return view('sections.edit_payment_term',$data);    
        else 
            return  redirect('/paymentterm/list')->with('error', 'Data Not Found!');            
    }
    public function payment_term_update(Request $request,$id)
    {
        try 
        {
                $this->validate($request,
                [
                    'payment'=>'required',
                ],
                [
                    'payment.required'=>'Payment Term is Required',
                ]
            );
            CustomHelpers::userActionLog($request->input()['update_reason'],$request->input()['_id'],'Payment Term Update');

            $timestamp = date('Y-m-d G:i:s');
            Payment::where('id','=',$id)
            ->update(
                [                    
                    'value' => $request->input('payment'),
                    'created_by' => Auth::id(),
                    'updated_at' => $timestamp,
                ]
            );
            return redirect('/paymentterm/list')->with('success','Payment Term updated successfully. ');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/paymentterm/update/'.$id)->with('error','some error occurred')->withInput();
        }
        
    }
    public function payment_term_delete($id)
    {       
        try 
        {
            $timestamp = date('Y-m-d G:i:s');
            Payment::where('id','=',$id)
            ->update(
                [                    
                    'is_active' => 0,
                    'created_by' => Auth::id(),
                    'updated_at' => $timestamp,
                ]
            );
            return redirect('/paymentterm/list')->with('success','Payment Term deleted successfully. ');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/paymentterm/list'.$id)->with('error','some error occurred')->withInput();
        }
    }
    public function payment_term_list()
    {
        $data=array('layout'=>'layouts.main', 'io'=>'$io');
        return view('sections.paymentterm_summary', $data);      
    }
    public function payment_term_data_api(Request $request)
    {        
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $paymentData= Payment::select(
            'id',
            'value')
            ->where('is_active','=',1);

        if(!empty($serach_value))
        {
            $paymentData = $paymentData->where('value','LIKE',"%".$serach_value."%");
        }
        $count = $paymentData->count();
        $paymentData = $paymentData->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['id', 'value'];
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
    public function get_consignee_by_partyid($id){
        $consignee = Consignee::where('party_id',$id)->where('consignee.is_active',1)->get();
        return array('consg_list' => $consignee);    
    }


    public function referencelist()
    {
        $data=array('layout'=>'layouts.main');
        return view('sections.referencelist', $data);
    }
    public function referencelistdata(Request $request)
    {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = Reference::select('party_reference.id','party_reference.referencename',DB::raw('(DATE_FORMAT(created_at ,"%d-%m-%Y %r")) as created'));

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('id','LIKE',"%".$serach_value."%")
                        ->orwhere('referencename','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['party_reference.id','party_reference.referencename','created_at'];
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
    public function update_reference(Request $request) {
        if(!isset($request->id))
            abort(404);
   
        $reference_name = Reference::where('id',$request->id)->get()->first();
        // return $reference_name;
        $data = array('layout'=>'layouts.main','reference_name'=>$reference_name);
        return view('sections.update_reference', $data);
    }

    public function do_reference_update(Request $request){
        if(!isset($request->id))
            abort(404);
        try {
            $this->validate($request,
                [
                    'update_reason'=>'required',
                    'reference'=>'required|unique:party_reference,referencename,'.$request->id,
                ],
                [
                    'reference.unique'    => 'Sorry "'.$request->input('reference').'"  Is Already Exist',
                ]
            );
         
                $ref=Reference::where('id',$request->id)->update([
                    'referencename'=>$request->input('reference')
                ]);
                if($ref==NULL){
                    return redirect('/reference/update?id='.$request->id)->with('error','some error occurred')->withInput();
                }

            CustomHelpers::userActionLog($request->input()['update_reason'],$request->id,'Reference Update');

            return redirect('/reference/update?id='.$request->id)->with('success', 'Reference Name has been updated.');
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/reference/update?id='.$request->id)->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }
    
    public function delete_reference(Request $request) {
        if(!isset($request->id))
            abort(404);
   
        $reference_name = Reference::all();
        
        $data = array('layout'=>'layouts.main','reference_name'=>$reference_name,'id'=>$request->id);
        return view('sections.delete_reference', $data);
    }
    public function do_reference_delete(Request $request){
        if(!isset($request->id) || $request->id<1)
            abort(404);
        try {

                $this->validate($request,
                    [
                        'delete_reference'=>'required',
                        'sub_reference'=>'required'
                    ],
                    [
                        'delete_reference.required'    => 'This Field is required',
                        'sub_reference.required'    => 'This Field is required'
                    ]
                );
                $id = $request->id;

                $delete_reference = $request->input('delete_reference');
                $sub_reference = $request->input('sub_reference');

                $party=Party::where('reference_name',$request->input('delete_reference'))->update([
                    'reference_name'=>$request->input('sub_reference')
                ]);
                $internal=InternalOrder::where('reference_name',$request->input('delete_reference'))->update([
                    'reference_name'=>$request->input('sub_reference')
                ]);
                $clientpo=Client_po::where('reference_name',$request->input('delete_reference'))->update([
                    'reference_name'=>$request->input('sub_reference')
                ]);
                $delivery=Delivery_challan::where('reference_name',$request->input('delete_reference'))->update([
                    'reference_name'=>$request->input('sub_reference')
                ]);


                $reference_data = Reference::whereIn('party_reference.id',[$delete_reference,$sub_reference])
                ->selectRaw("party_reference.id, group_concat(referencename ORDER BY FIELD(id,$delete_reference,$sub_reference) SEPARATOR ' To ') as changeref ")->first();
                
                $content_changes="";
                if(isset($reference_data) && $reference_data->id>0)
                {
                    $content_changes = $reference_data->changeref;
                }
                CustomHelpers::userActionLog('Associate Reference Name',$id,'Delete and Associate Reference Name',$log_array=array(),array('Reference'=>$content_changes));

                $ref=Reference::where('id',$request->id)->delete();

                return redirect('/reference/delete?id='.$request->id)->with('success', 'Reference Name has been updated.');
        }
        catch(\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            DB::commit();
            return redirect('/reference/update?id='.$request->id)->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }
 }
