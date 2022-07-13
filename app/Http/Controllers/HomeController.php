<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use \App\Model\Party;
use \App\Model\Consignee;
use \App\Model\City;
use \App\Model\Country;
use \App\Model\State;
use \App\Custom\CustomHelpers;
class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        return view('sections.general_dashboard', ['layout' => 'layouts.main']);
      
    }

    public function generalDashboard(){
        return view('sections.dashboard', ['layout' => 'layouts.main']);
    }

    public function pdf(){
        $data = [
            'title' => 'bar'
        ];
        $pdf = PDF::loadView('temp', $data);
        return $pdf->download('document.pdf');
    }

    public function consignee_list(Request $request){
        $party = Party::all();
        return view('sections.consignee_list', ['layout' => 'layouts.main', 'party' => $party, 'pid' => $request->pid]);
    }

    public function consignee_all(Request $request){
        
        if(!isset($request->id))
            return array(
                'recordsTotal' => 0,
                 'recordsFiltered' => 0,
                 'data' => []
            );
        
        $serach_value = $request->input('search')['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;

        $consgdata = Consignee::leftJoin('party', function($join) {
                                        $join->on('consignee.party_id', '=', 'party.id');
                                    })
                                    ->leftJoin('cities', function($join) {
                                        $join->on('cities.id', '=', 'consignee.city');
                                    })
                                    ->leftJoin('states', function($join) {
                                        $join->on('states.id', '=', 'consignee.state');
                                    })
                                    ->leftJoin('countries', function($join) {
                                        $join->on('countries.id', '=', 'consignee.country');
                                    })
                                    ->select('consignee.*', 'party.partyname', 'cities.city',
                                        'states.name as state', 'countries.name as country')
                                    ->where('party.id', '=', $request->id)
                                    ;
        if($serach_value)
        {
            $consgdata->where(function($query) use ($serach_value){
                $query->where('partyname','LIKE',"%".$serach_value."%")
                         ->orWhere('consignee_name','LIKE',"%".$serach_value."%");
            }); 
        }
        $count = $consgdata->count();
        $consgdata->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['partyname','consignee_name','address', 'consignee.city','consignee.gst','consignee.pan'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $consgdata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $consgdata->orderBy('id','desc');
        }

        $consgdata = $consgdata->get();
        

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $consgdata; 
        return $array;
    }

    public function do_consignee_update(Request $request){

        if(!isset($request->input()['_id']))
            \abort(404);

        $this->validate($request,
            [
                //'house'=>'required',
                'area'=>'required',
                'name'=>'required',
                'city'=>'required',
                'pincode'=>'required|max:10000000|numeric',
                'state'=>'required',
                'country'=>'required',
                'update_reason'=>'required'
            ],
            [
               'update_reason.required'=>'Update Reason is Required',
               'area.required'=>'Address is Required',
               'name.required'=>'Consignee Name Is  Required',
               'city.required'=>'City is Required',
               'pincode.required'=>'Pincode is Required',
               'pincode.numeric'=>'Pincode Contains only numbers',
               'state.required'=>'State is Required',
               'country.required'=>'Country is Required',
               'gst.required'=>'GST No. is Required',
               'pan.required'=>'PAN No. is Required',
            ]
        );
        
        try{

           
            Consignee::find($request->input()['_id'])->update(
                [
                    'locality' => $request->input('house'),
                    'city' => $request->input('city'),
                    'pincode' =>  $request->input('pincode'),
                    'address' => $request->input('area'),
                    'state' => $request->input('state'),
                    'country' => $request->input('country'),
                    'consignee_name' => $request->input('name'),
                    'gst' =>$request->input('gst_type') ==0 ? $request->input('gst_sel') : $request->input('gst'),
                    'pan' =>$request->input('pan_type') ==0 ? $request->input('pan_sel') : $request->input('pan'),
                ]

            );
            /*** USER LOG ***/
            if($request->input('gst_type')==1){
                $changes_array['gst_type'] = 'Yes';
                $changes_array['gst'] = $request->input('gst');
            }
            else{
                $changes_array['gst_type'] = 'No';
                $changes_array['gst'] = $request->input('gst_sel');
            }

            if($request->input('pan_type')==1){
                $changes_array['pan_type'] = 'Yes';
                $changes_array['pan'] = $request->input('pan');
            }
            else{
                $changes_array['pan_type'] = 'No';
                $changes_array['pan'] = $request->input('pan_sel');
            }
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

            'city'=>'City',
            'state'=>'State',
            'country'=>'Country',
            'gst_type'=>'GST Type',
            'pan_type'=>'PAN/IT Type',
            'pan'=>'PAN/IT',
            'gst'=>'GST',
            'name'=>'Consignee Name'

            );
            CustomHelpers::userActionLog($request->input()['update_reason'],$request->input()['_id'],'Consignee Update',$log_array,$changes_array,$removekeys=array('_id','gst_sel','pan_sel'));
            /***  END USER LOG ***/
            return redirect('/consignee/update?id='.$request->input()['_id'])->with('success', 'Consignees has been updated.');
        }
        catch(\Illuminate\Database\QueryException $ex) {
            
            return redirect('/consignee_form')->with('error',$ex->getMessage());
        }
    }

    public function consignee_update(Request $request){
        if(!isset($request->id))
            \abort(404);
        
        $consignee = Consignee::leftJoin('party', function($join) {
                                        $join->on('consignee.party_id', '=', 'party.id');
                                    })
                                    ->select('consignee.*', 'party.partyname', 'party.id as party_id')
                                    ->where('consignee.id', '=', $request->id)
                                    ->first();
        if(!$consignee)
            \abort(404);
        $countries=Country::all();
        $state = State::where('country_id', '=', $consignee->country)->get();
        $city = City::where('state_id', '=', $consignee->state)->get();
        $data=array('layout'=>'layouts.main','countries'=>$countries, 'consignee' => $consignee, 
                        'city' => $city, 'state' => $state);
        return view('sections.consignee_update', $data);
    }

    public function consignee_view(Request $request,$id){

        $consgdata = Consignee::leftJoin('party', function($join) {
                                        $join->on('consignee.party_id', '=', 'party.id');
                                    })
                                    ->leftJoin('cities', function($join) {
                                        $join->on('cities.id', '=', 'consignee.city');
                                    })
                                    ->leftJoin('states', function($join) {
                                        $join->on('states.id', '=', 'consignee.state');
                                    })
                                    ->leftJoin('countries', function($join) {
                                        $join->on('countries.id', '=', 'consignee.country');
                                    })
                                    ->leftJoin('users', function($join) {
                                        $join->on('consignee.created_by', '=', 'users.id');
                                    })
                                    ->select('consignee.*', 'party.partyname', 'cities.city',
                                        'states.name as state', 'countries.name as country','users.name')
                                    ->where('consignee.id', '=', $id)->get()->toarray();
        if(empty($consgdata))
        {
            return redirect('/consignee/list')->with('error','Data not available.');
        }
        $data=array('layout'=>'layouts.main','consignee'=>$consgdata);
        return view('sections.consignee_view', $data);
    }

}



