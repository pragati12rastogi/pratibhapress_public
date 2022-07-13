<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\dkerp;
use App\Model\Party;
use App\Model\Asn;
use App\Model\Tax_Invoice;

use App\Model\Grn;
use App\Model\Settings;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Custom\CustomHelpers;
use \Carbon\Carbon;


class AsnGrn extends Controller
{
   
    
    public function asn_not_created()
    {
        $data=array( 'layout' => 'layouts.main','list_of'=>'asn');
        return view('sections.asn_not_generated',$data);
    }
    public function grn_not_created()
    {
        $data=array( 'layout' => 'layouts.main','list_of'=>'grn');
        return view('sections.asn_not_generated',$data);
    }
    public function asn_not_created_api($type,Request $request)
    {
        
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        if($type=='asn'){
            $type = 'asn_client';
            $asn_party =explode(',', Settings::where('name','=',$type)->get('value')->first()->value);
            $tax = \App\Model\Tax_Invoice::whereIn('tax_invoice.party_id',$asn_party)
            ->leftJoin('party','party.id','tax_invoice.party_id')
            ->leftJoin('asn','asn.invoice_id','=','tax_invoice.id')
            ->where('asn.invoice_id','=',NULL)
            ->where('tax_invoice.is_active',1)->select(
                'tax_invoice.id',
                DB::raw('(DATE_FORMAT(tax_invoice.date ,"%d-%m-%Y")) as date'),
               
                'tax_invoice.invoice_number',
                'party.partyname',
                DB::raw('concat(party.id,"/",tax_invoice.id) as control')
            );
            if(!empty($serach_value)){
                $tax->where(function($query) use ($serach_value){
                    $query->where('invoice_number','LIKE',"%".$serach_value."%")
                    ->orwhere('partyname','LIKE',"%".$serach_value."%")
                    ->orwhere('tax_invoice.id','LIKE',"%".$serach_value."%"); 
                });
            }
                    
            $count = $tax->count();
            $tax = $tax->offset($offset)->limit($limit);
            if(isset($request->input('order')[0]['column'])){
                $data = [ 'id', 'partyname', 'invoice_number','tax_invoice.date','tax_invoice.created_at'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $tax->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else{
                $tax->orderBy('id','desc');}
        }
        else if($type=='grn'){
            $type = 'grn_client';
            $asn_party =explode(',', Settings::where('name','=',$type)->get('value')->first()->value);
            $tax = \App\Model\Tax_Invoice::whereIn('tax_invoice.party_id',$asn_party)
            ->leftJoin('party','party.id','tax_invoice.party_id')
             ->leftJoin('grn','grn.invoice_id','=','tax_invoice.id')
             ->where('grn.invoice_id','=',NULL)
            ->where('tax_invoice.is_active',1)->select(
                'tax_invoice.id',   DB::raw('(DATE_FORMAT(tax_invoice.date ,"%d-%m-%Y")) as date'),
              
                'tax_invoice.invoice_number',
                'party.partyname',
                DB::raw('concat(party.id,"/",tax_invoice.id) as control')
            );
            if(!empty($serach_value)){
                $tax->where(function($query) use ($serach_value){
                    $query->where('invoice_number','LIKE',"%".$serach_value."%")
                    ->orwhere('partyname','LIKE',"%".$serach_value."%")
                    ->orwhere('tax_invoice.id','LIKE',"%".$serach_value."%"); 
                });
            }
               
                    
            $count = $tax->count();
            $tax = $tax->offset($offset)->limit($limit);
            if(isset($request->input('order')[0]['column'])){
                $data = [ 'id', 'partyname', 'invoice_number','tax_invoice.date','tax_invoice.created_at'];
                $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
                $tax->orderBy($data[$request->input('order')[0]['column']], $by);
            }
            else{
                $tax->orderBy('id','desc');}
        }
        $tax= $tax->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $tax; 
        return json_encode($array);
    }

    public function asn_client_create(){
        $party=Party::where('party.is_active',1)->get();
        $asn=Settings::where('name','=','asn_client')->get('value')->first()->value;
        $grn=Settings::where('name','=','grn_client')->get('value')->first()->value;
        $data=array('layout' => 'layouts.main','asn_client'=> $asn,'grn_client'=>$grn,'party'=>$party);
        return view('sections.asn_grn_master',$data);
    
    }
    public function asn_client_insert(Request $request){
        try{
            $this->validate($request,
                [
                    'asn_party'=>'required',
                    'grn_party'=>'required',
                ]
            );

            Settings::where('name','=','asn_client')->update([
                'value'=>implode(',',$request->input('asn_party'))
                
            ]);
            Settings::where('name','=','grn_client')->update([
                'value'=>implode(',',$request->input('grn_party'))
                
            ]);
            return redirect('/asn/setting')->with('success', 'ASN GRN clients Added.');
            }
            catch(\Illuminate\Database\QueryException $ex) {
                return redirect('/asn/setting')->with('error',$ex->getMessage());
            }

    }
    
    public function asn_create(Request $request,$id='',$invoice=''){
       try {
        if(!$request->has('asn_num')){
            return redirect('asn/create/'.$id.'/'.$invoice)->with('error','Atleast one GRN Number is required.')->withInput();  
           }
        $validator = Validator::make($request->all(),
                [
                    'party'=>'required',
                ],
                [
                    'party.required'=>'This field is required',
                
                ]
                );
                $errors = $validator->errors();
                
                if ($validator->fails()) 
                {
                    if ($id==''  && $invoice='') 
                    return redirect('asn/create')->withErrors($errors)->withInput();
                    else
                    return redirect('asn/create/'.$id.'/'.$invoice)->withErrors($errors)->withInput();
                   
                }
                else{
                    $timestamp = date('Y-m-d G:i:s');
                    $asn_num=$request->input('asn_num');
                    $asn_date=$request->input('asn_date');
                       if(count($asn_num)>0){ foreach($asn_num as $key => $value)          
                            if(empty($value)) {
                                unset($asn_num[$key]);
                            }
                            else{
                                $tax_id[]=$key;
                            }}
                            else{
                                return redirect('asn/create/'.$id.'/'.$invoice)->with('error','No Tax invoice is selected.')->withInput(); 
                            }
                       $counter=0;     
                   for($i=0;$i<count($asn_num);$i++){
                        $counter++;
                       $tax=$tax_id[$i];
                    Asn::insert([
                        'id'=>NULL,
                        'party_id'=>$request->input('party'),
                        'invoice_id'=>$tax_id[$i],
                        'asn_number'=>$asn_num[$tax],
                        'asn_date'=>date("Y-m-d", strtotime($asn_date[$tax])),
                        'created_by' => Auth::id(),
                        'created_time' => $timestamp,  
                    ]);
                   }
                }
                if($counter > 0){
                    return redirect('asn/create')->with('success','Asn created Successfully');
                }
                else{
                    if ($id==''  && $invoice='') 
                    return redirect('asn/create')->with('error','Atleast one ASN Number is required.')->withInput();
                    else
                    return redirect('asn/create/'.$id.'/'.$invoice)->with('error','Atleast one ASN Number is required.')->withInput();
                 
                }
       }  
       catch(\Illuminate\Database\QueryException $ex)
       {
           return redirect('asn/create')->with('error',$ex->getMessage())->withInput();
       }
    }
    public function asn_list(){
        $data=array('layout'=>'layouts.main');
        return view('sections.asn_summary', $data);
    }
    public function asn_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $asn = Asn::leftJoin('tax_invoice','tax_invoice.id','=','asn.invoice_id')
        ->leftJoin('party','party.id','=','asn.party_id')
        ->leftJoin('states','party.state_id','=','states.id');    
        if(!empty($serach_value))
            $asn->where('asn.id','LIKE',"%".$serach_value."%")
            ->orwhere('party.partyname','LIKE',"%".$serach_value."%")
            ->orwhere('asn.asn_number','LIKE',"%".$serach_value."%")
            ->orwhere('tax_invoice.invoice_number','LIKE',"%".$serach_value."%")
            ->orwhere('states.name','LIKE',"%".$serach_value."%");
          
            

        $count = $asn->count();
        $asn = $asn->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = [ 'asn.id', 'party.partyname', 'asn.asn_number','asn.asn_date',
            'asn.created_time','tax_invoice.invoice_number','states.name'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $asn->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $asn->orderBy('id','desc');
        $asndata= $asn->select(
            'asn.id', 
            'asn.asn_date',
            'asn.created_time',
            'party.partyname', 
            'asn.asn_number',
            'tax_invoice.invoice_number',
            'states.name'
        )->get();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $asndata; 
        return json_encode($array);
    }
    public function asn_form($id='',$invoice=''){
        $asn_party =explode(',', Settings::where('name','=','asn_client')->get('value')->first()->value);
        $party=Party::where('party.is_active',1)
        ->whereIn('id',$asn_party);
        $invoice_id='';
        if($id!='')
            $party = $party->where('id','=',$id);
        $party = $party->get();
        if($invoice!='')
        {
            $invoice=  Tax_Invoice::where('id','=',$invoice)
                  ->where('party_id','=',$id)
                  ->get(['invoice_number','id'])
                  ->first();
            if(isset($invoice))
            {
                $invoice_id = $invoice->id;
                $invoice=$invoice->invoice_number;
            }
         
        }
        $data=array( 'layout' => 'layouts.main','party'=>$party,'type'=>$id,'invoice'=>$invoice,'invoice_id'=>$invoice_id);
        return view('sections.asn_form',$data);
    }
 //----------------------------------------------------------------GRN----------------------------------------------
 public function grn_form($id='',$invoice=''){
    $grn_party =explode(',', Settings::where('name','=','grn_client')->get('value')->first()->value);
       
    $party=Party::where('party.is_active',1)
    ->whereIn('id',$grn_party);
    $invoice_id='';
    if($id!='')
        $party = $party->where('id','=',$id);
    $party = $party->get();
    if($invoice!='')
    {
        $invoice=  Tax_Invoice::where('id','=',$invoice)->where('party_id','=',$id)->get(['invoice_number','id'])->first();
        if(isset($invoice))
        {
            $invoice_id = $invoice->id;
            $invoice=$invoice->invoice_number;
        }
        
    }
 
        
    $data=array( 'layout' => 'layouts.main','party'=>$party,'type'=>$id,'invoice'=>$invoice,'invoice_id'=>$invoice_id);
    return view('sections.grn_form',$data);
}
public function grn_create(Request $request,$id='',$invoice=''){
   try {
       if(!$request->has('grn_num')){
        return redirect('grn/create/'.$id.'/'.$invoice)->with('error','Atleast one GRN Number is required.')->withInput();  
       }
    $validator = Validator::make($request->all(),
            [
                'party'=>'required',
            ],
            [
                'party.required'=>'This field is required',
            ]
            );
            $errors = $validator->errors();
            if ($validator->fails()) 
            {
                if ($id==''  && $invoice='') 
                    return redirect('grn/create')->withErrors($errors)->withInput();
                else
                    return redirect('grn/create/'.$id.'/'.$invoice)->withErrors($errors)->withInput();
            }
            
            else{
                $timestamp = date('Y-m-d G:i:s');
                $grn_num=$request->input('grn_num');
                $grn_date=$request->input('grn_date');

                if(count($grn_num)>0){foreach($grn_num as $key => $value) {      
                        if(empty($value)) {
                            unset($grn_num[$key]);
                        }
                        else{
                            $tax_id[]=$key;
                        }
                    }}
                    else{
                        return redirect('grn/create/'.$id.'/'.$invoice)->with('error','No Tax Invoice is selected.')->withInput(); 
                    }
                   $counter=0;     
               for($i=0;$i<count($grn_num);$i++){
                    $counter++;
                   $tax=$tax_id[$i];
                Grn::insert([
                    'id'=>NULL,
                    'party_id'=>$request->input('party'),
                    'invoice_id'=>$tax_id[$i],
                    'grn_number'=>$grn_num[$tax],
                    'grn_date'=>date("Y-m-d", strtotime($grn_date[$tax])),
                    'created_by' => Auth::id(),
                    'created_time' => $timestamp,  
                ]);
               }
            }
            if($counter > 0){
                return redirect('grn/create')->with('success','GRN created Successfully');
            }
            else{
                if ($id==''  && $invoice='') 
                    return redirect('grn/create')->with('error','Atleast one GRN Number is required.')->withInput();
                else
                    return redirect('grn/create/'.$id.'/'.$invoice)->with('error','Atleast one GRN Number is required.')->withInput();
      
            }
            return redirect('grn/create')->with('success','Asn created Successfully');
   }  
   catch(\Illuminate\Database\QueryException $ex)
   {
       return redirect('grn/create')->with('error',$ex->getMessage())->withInput();
   }
}
public function grn_list(){
    $data=array('layout'=>'layouts.main');
    return view('sections.grn_summary', $data);
}
public function grn_api(Request $request){
    $search = $request->input('search');
    $serach_value = $search['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $offset = empty($start) ? 0 : $start ;
    $limit =  empty($limit) ? 10 : $limit ;
    $asn = Grn::leftJoin('tax_invoice','tax_invoice.id','=','grn.invoice_id')
    ->leftJoin('party','party.id','=','grn.party_id')
    ->leftJoin('states','party.state_id','=','states.id');    
    if(!empty($serach_value))
        $asn->where('grn.id','LIKE',"%".$serach_value."%")
        ->orwhere('party.partyname','LIKE',"%".$serach_value."%")
        ->orwhere('grn.grn_number','LIKE',"%".$serach_value."%")
        ->orwhere('tax_invoice.invoice_number','LIKE',"%".$serach_value."%")
        ->orwhere('states.name','LIKE',"%".$serach_value."%")
        ->orwhere('grn.grn_date','LIKE',"%".$serach_value."%");
      
        

    $count = $asn->count();
    $asn = $asn->offset($offset)->limit($limit);
    if(isset($request->input('order')[0]['column'])){
        $data = [ 'grn.id', 'party.partyname', 'grn.grn_number','grn.grn_date','grn.created_time',
                    'tax_invoice.invoice_number','states.name'];
        $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
        $asn->orderBy($data[$request->input('order')[0]['column']], $by);
    }
    else
        $asn->orderBy('id','desc');
    $asndata= $asn->select(
        'grn.id', 
        'party.partyname', 
        'grn.grn_number',
        'tax_invoice.invoice_number',
        'states.name',
       'grn.grn_date','grn.created_time'
    )->get();
    $array['recordsTotal'] = $count;
    $array['recordsFiltered'] = $count;
    $array['data'] = $asndata; 
    return json_encode($array);
}
}
