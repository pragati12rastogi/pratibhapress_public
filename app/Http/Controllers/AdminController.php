<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Model\Users;
use \App\Model\RequestPermission;
use \App\Model\UserLayoutRight;
use \App\Model\SectionRight;
use \App\Model\UserSectionRight;
use \App\Model\Tax_Invoice;
use \App\Model\Tax;
use \App\Model\Delivery_challan;
use \App\Model\Challan_per_io;
use \App\Model\InternalOrder;
use \App\Model\jobDetails;
use \App\Custom\CustomHelpers;
use DB;
use Hash;
use Auth;
class AdminController extends Controller
{

    public function admin_update()
    {

    }
    public function permission_denied()
    {
         $data=array('layout'=>'layouts.main');
        return view('sections.admin_permssion_denied', $data);
    }
    public function permission($id){

        if($id!=Auth::id())
        {
            $menudata = SectionRight::leftJoin('user_section_rights',function($join) use ($id){
                $join->on(DB::raw('user_section_rights.user_id = '.$id.' and section_rights.id'),'=','user_section_rights.section_id');
            })

            ->where('show_permission','=',1)
    //        ->where('section_rights.linkfor','<>',0)
            ->select(['section_rights.*','user_section_rights.user_id'])
            ->orderBy('showorder')
            ->orderBy('linkfor')
            ->get()->toarray();
            $user = Users::where('id',$id)->get()->first();
            $menudata = CustomHelpers::menuTree($menudata);
                $data=array(
                    'layout'=>'layouts.main',
                    'id'=>$id,
                    'menudata'=>$menudata,
                    'user'=>$user
                );
                return view('sections.admin_permission',$data);
        }
        else
            return abort('403',"You can not change your own Access Rigths.");
    }
    public function getadminpermission($id){
        DB::enableQueryLog();
        $menudata = SectionRight::leftJoin('user_section_rights',function($join) use ($id){
            $join->on(DB::raw('user_section_rights.user_id = '.$id.' and section_rights.id'),'=','user_section_rights.section_id');
        })
        ->where('show_permission','=',1)
        ->select([
            'section_rights.permission_pid as pid',
            'section_rights.name',
            'section_rights.map_id',
            'section_rights.showorder',
            'section_rights.linkfor',
            'section_rights.show_menu',
            'section_rights.id',
            'user_section_rights.user_id'
            ])
        ->orderBy('showorder')
        ->orderBy('linkfor')
        ->get()->toarray();
        $menudata = CustomHelpers::menuTree($menudata);
        $resp = '<table class="table table-condensed ">
            <thead>
                <tr>
                    <th class="info">Name</th>
                    <th class="info">View</th>
                    <th class="info">Create</th>
                    <th class="info">Update</th>
                    <th class="info">Print</th>
                    <th class="info">Summary</th>
                    <th class="info">Import</th>
                    <th class="info">Export</th>
                    <th class="info">Other</th>
                </tr>
            </thead>
        <tbody>';
        $menuitems=array_keys($menudata); 
        //        $resp =$resp .$this->generate_table($menudata);
        $indent='';
        $class='';
        for($i=0;$i<count($menuitems);$i++)
        {
            $val1 = $menudata[$menuitems[$i]]['id'];
            $resp =$resp .$this->generate_table(array('2'=> $menudata[$menuitems[$i]]),$indent,$class,$val1);
        }
        $resp = $resp.'</tbody>
                    </table>';
        return $resp;
    }
    function generate_table($menudata,$indent,$class,$val1)
    {
        $resp='';
        foreach ($menudata as $menu)
        {
            if($menu['map_id']=='all')
                $resp = $resp.'<input type="hidden" name="menu[]" value="'.$menu['id'].'">';
            else
            {
                    $val2 = $val1;
                    $checked1='';
                    if($menu['map_id']!=0 )
                        $val2 = $val1.",".$menu['map_id'];
                    if($menu['user_id'] != NULL)
                        $checked1='Checked';
                    $inp='<input type="checkbox"  class="'.trim($class).'" id="'.str_replace(['.', ' ', '/'],'_',$menu['name']).'" onchange="manageParent1(this)" name="menu[]" value="'.$val2.'" '.$checked1.'>';
                    
                $resp = $resp.'<tr><th style="display:inline">'.$indent.$inp.$menu['name'].'</th>';
                if(isset($menu['children']))
                {
                    $var=0;
                    $menukey = array_keys($menu['children']);
                    for($i=0;$i<count($menu['children']);)
                    {
                        if(isset($menu['children'][$menukey[$i]]['children']))
                        {
                            $indent=$indent.'&nbsp&nbsp&nbsp&nbsp&nbsp';
                            $class=$class.' '.str_replace(['.', ' ', '/'],'_',$menu['name']);
                            $menuitems=array_keys($menu['children']);
                            for($i=0;$i<count($menuitems);$i++)
                            {
                                $val2 = $val1.",".$menu['children'][$menuitems[$i]]['id'];
                                $resp =$resp .$this->generate_table(array($menu['children'][$menuitems[$i]]),$indent,$class,$val2);
                            }
                            return $resp;
                        }
                        else
                        {
                            if( $var==9)
                            {
                                $var=0;
                                $resp = $resp."</tr><tr> <td >".$menu['name'];
                            }
                            $var = $var+1;
                            if($menu['children'][$menukey[$i]]['linkfor']!=$var){
                                $resp = $resp.'<td></td>';
                            }
                            else if( $menu['children'][$menukey[$i]]['linkfor']==$var)
                            {
                                $resp = $resp.'<td>';
                                do{
                                $val = $val2.",".$menu['children'][$menukey[$i]]['id'];
                                if($menu['children'][$menukey[$i]]['map_id']!=0 )
                                    $val = $val.",".$menu['children'][$menukey[$i]]['map_id'];
                                $checked='';
                                if($menu['children'][$menukey[$i]]['user_id'] != NULL)
                                    $checked='Checked';
                                $resp = $resp.'<input type="checkbox" class="'.str_replace(['.', ' ', '/'],'_',$menu['name']).'" onchange="manageParent1(this)" name="menu[]" value="'.$val.'" '.$checked.' title="'.$menu['children'][$menukey[$i]]['name'].'">';
                                // $resp = $resp.'<label>'.$menu['children'][$menukey[$i]]['name'].'<label>';
                                $i++;
                                }while( $i<count($menu['children']) && $menu['children'][$menukey[$i]]['linkfor'] ==  $menu['children'][$menukey[$i-1]]['linkfor']);
                                $resp = $resp.'</td>';
                            }
                        }
                    }
                    $resp = $resp.'</tr>';
                }
                else
                {
                    $resp = $resp.'</tr>';
                }
            }
        }
        return $resp;
    }
    function generate_table1($menudata,$indent,$class)
    {
        $resp='';
        foreach ($menudata as $menu)
        {
            if($menu['map_id']=='all')
                $resp = $resp.'<input type="hidden" name="menu[]" value="'.$menu['id'].'">';
            else
            {
                    $val1 = $menu['id'];
                    $checked1='';
                    if($menu['map_id']!=0 )
                        $val1 = $val1.",".$menu['map_id'];
                    if($menu['user_id'] != NULL)
                        $checked1='checked';
                    $inp='<input type="checkbox"  class="'.trim($class).'" id="'.str_replace(['.', ' ', '/'],'_',$menu['name']).'" onchange="manageParent1(this)" name="menu[]" value="'.$val1.'" '.$checked1.'>';
                    
                $resp = $resp.'<tr><td style="display:inline">'.$indent.$inp.$menu['name'].'    </td>';
                if(isset($menu['children']))
                {
                    $var=0;
                    $menukey = array_keys($menu['children']);
                    for($i=0;$i<count($menu['children']);)
                    {
                        if(isset($menu['children'][$menukey[$i]]['children']))
                        {
                            $indent=$indent.'&nbsp&nbsp&nbsp&nbsp&nbsp';
                            $class=$class.' '.str_replace(['.', ' ', '/'],'_',$menu['name']);
                            $menuitems=array_keys($menu['children']);
                            for($i=0;$i<count($menuitems);$i++)
                            {
                                $resp =$resp .$this->generate_table(array($menu['children'][$menuitems[$i]]),$indent,$class);
                            }
                            return $resp;
                        }
                        else
                        {
                            if( $var==9)
                            {
                                $var=0;
                                $resp = $resp."</tr><tr> <td >".$menu['name'];
                            }
                            $var = $var+1;
                            if($menu['children'][$menukey[$i]]['linkfor']!=$var){
                                $resp = $resp.'<td></td>';
                            }
                            else if( $menu['children'][$menukey[$i]]['linkfor']==$var)
                            {
                                $resp = $resp.'<td>';
                                do{
                                $val = $menu['children'][$menukey[$i]]['id'];
                                if($menu['children'][$menukey[$i]]['map_id']!=0 )
                                    $val = $val.",".$menu['children'][$menukey[$i]]['map_id'];
                                $checked='';
                                if($menu['children'][$menukey[$i]]['user_id'] != NULL)
                                    $checked='checked';
                                $resp = $resp.'<input type="checkbox" class="'.str_replace(['.', ' ', '/'],'_',$menu['name']).'" onchange="manageParent1(this)" name="menu[]" value="'.$val.'" '.$checked.' title="'.$menu['children'][$menukey[$i]]['name'].'">';
                                $resp = $resp.'<label>'.$menu['children'][$menukey[$i]]['name'].'<label>';
                                $i++;
                                }while( $i<count($menu['children']) && $menu['children'][$menukey[$i]]['linkfor'] ==  $menu['children'][$menukey[$i-1]]['linkfor']);
                                $resp = $resp.'</td>';
                            }
                        }
                    }
                    $resp = $resp.'</tr>';
                }
                else
                {
                    $resp = $resp.'</tr>';
                }
            }
        }
        return $resp;
    }
    public function setpermission(Request $request){
        ini_set('max_execution_time', 18000);

        $id = $request->input('id');
        $menudata = $request->input('menu');
        DB::beginTransaction();
        //DB::enableQueryLog();
        $t1 = time();
        try{
            if($menudata)
            {
                $newarray = array();
                foreach ($menudata as $key => $value) {
                    $str = explode(',',$value);
                    foreach($str as $key1=>$val)
                    {
                        $newarray[] = $val; 
                    }
                }

                $menudata = $newarray;
                $menudata1 = array_values(array_unique($menudata));
                $menudata = $menudata1;
                
                UserSectionRight::where('user_id','=',$id)
                    ->whereNotIn('section_id',$menudata)
                    ->delete();
                $added_section_rights = UserSectionRight::where('user_id','=',$id)->get('section_id')->toArray();
                $array = array_column($added_section_rights, 'section_id');
                //$menudata = array_diff($menudata,$array);
                

                foreach ($menudata as $key => $val) {
                    // $str = explode(',',$value);
                    // foreach($str as $key1=>$val)
                    // {
                        if(!in_array($val,$array))
                        {
                            $insert_array['section_id'] = $val;
                            $insert_array['user_id'] = $id;
                            $insert_array['allowed'] = 1;
                            $insert_data[] = $insert_array;
                            // $userlayout = UserSectionRight::Create(['section_id'=>$val,'user_id'=>$id]);
                            // $userlayout->section_id = $val;
                            // $userlayout->user_id = $id;
                            // $userlayout->allowed = 1;
                            // $userlayout->save();
                            
                        }
                    }
                    if(!empty($insert_data))
                    $userlayout = UserSectionRight::insert($insert_data);
                //}
            }
            else {
                UserSectionRight::where('user_id','=',$id)
                ->delete();    
            }
                
                DB::commit();
                return redirect('/admin/permission/'.$id)->with('success','Permission has been set successfully.'); 
        }catch(\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return redirect('/admin/permission/'.$id)->with('error','Something went wrong.'.$ex->getMessage());
        }
    }
    public function admin(){
        $data=array('layout'=>'layouts.main');
        return view('sections.admin', $data);   
    }

    public function admindata(Request $request)
    {        
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = Users::where('users.id','>','0')
        ->leftjoin('department','users.department_id','department.id');
        if(!empty($serach_value))
        {
            $userlog = $userlog->where('email','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('department.department','LIKE',"%".$serach_value."%")
                        ->orwhere('phone','LIKE',"%".$serach_value."%");
                        
        }
        
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['users.id','name','email','department.department','user_type','created_at'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('users.id','desc');
        }
        $userlogdata = $userlog->select('users.id','name','email','department.department','user_type','created_at')->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }

    public function admin_view($id){
        $data = Users::where('id','=',$id)->get([
            'name',
            'email',
            'phone',
            'home_landline',
            'user_type',
            'login',
            'profile_photo',
            'created_at',
            'updated_at'
        ])->first();
        $data=array(
            'layout'=>'layouts.main',
            'data' => $data
        );
        return view('sections.admin_view', $data);   
        
    }

    public function admin_change_pass()
    {
        $data = array('layout'=>'layouts.main');
        
        return view('sections.update_password',$data);
    }
    public function admin_change_pass_db(Request $request)
    {
        $this->validate($request,[
            'pass'=>'required|min:6',
            're_pass'=>'required_with:pass|same:pass',
        ],[
            'pass.required'=> 'Password is required.',
            'pass.min'=> 'Password min lenght is :min.',
            're_pass.required_with'=> 'Confirm Password is required with Password.',
            're_pass.same'=> 'Confirm Password should be same as Password.'
        ]); 

        $user=Users::where('id',Auth::id())->update([
            'password'=>Hash::make($request->input('pass')),

        ]);
        if($user==NULL){
            DB::rollback();
            return redirect('/admin/view/'.Auth::id())->with('error','Some Unexpected Error occurred.');
  
        }
        else{
            return redirect('/admin/view/'.Auth::id())->with('success','Successfully Updated Password.');  
        }
    }
    public function add_required_permission($for,$id,$reason)
    {
        $map_for = array(
            'taxinvoice'=>'/cancel/taxinvoice/'.$id,
            'internalorder'=>'/cancel/internalorder/'.$id,
            'deliverychallan'=>'/deliverychallan/delete/'.$id,
            'deliverychallanupdate'=>'/deliverychallan/update/'.$id,
            'taxinvoicedelete'=>'/taxinvoice/delete/'.$id,
            'taxinvoiceupdate'=>'/taxinvoice/update/'.$id
        );
        $data=RequestPermission::where('data_for',$for)
        ->where('requested_by',Auth::id())
        ->where('data_id',$id)
        ->select(
            'request_permission.id',
            'request_permission.status'
        )->orderBy('id','DESC')
        ->first();
        if($for=="taxinvoice"){
            $ti_number = Tax_Invoice::where('id',$id)->first('invoice_number')['invoice_number'];
            $reason = str_replace(' - - - ','/',$reason);
            RequestPermission::insertGetId([
                'reason'=>$reason,
                'data_id'=>$id,
                'data_for'=>$for,
                'operation'=>'Cancel Tax Invoice',
                'request_for'=>$map_for[$for],
                'requested_by'=>Auth::id(),
                'authorised_by'=>'0',
                'status'=>'pending'
            ]);
            $upd_ti_status=Tax_Invoice::where('id',$id)->update(['is_cancelled'=>1]);
            $redirect_to = array(
                'taxinvoice'=>'/taxinvoice/list'
            );
            return redirect($redirect_to[$for])->with('success','Cancel Permission Raised.');
        }
        if($for=="internalorder"){
            $redirect_to = array(
                'internalorder'=>'/internal/list/open'
            );
            if($data['status']=='pending'){
                return redirect($redirect_to[$for])->with('error','Update Permission Has Already Raised.');
            }
            else{
                $io_number = InternalOrder::where('id',$id)->first('io_number')['io_number'];
                $reason = str_replace(' - - - ','/',$reason);
                RequestPermission::insertGetId([
                    'reason'=>$reason,
                    'data_id'=>$id,
                    'data_for'=>$for,
                    'operation'=>'Update Internal Order '.$io_number,
                    'request_for'=>$map_for[$for],
                    'requested_by'=>Auth::id(),
                    'authorised_by'=>'0',
                    'status'=>'pending'
                ]);
                
                return redirect($redirect_to[$for])->with('success','Update Permission Raised.');
            }
           
        }
        if($for=="deliverychallan"){
            $dc_number = Delivery_challan::where('id',$id)->first('challan_number')['challan_number'];
            $reason = str_replace(' - - - ','/',$reason);
            RequestPermission::insertGetId([
                'reason'=>$reason,
                'data_id'=>$id,
                'data_for'=>$for,
                'operation'=>'Delete Delivery Challan',
                'request_for'=>$map_for[$for],
                'requested_by'=>Auth::id(),
                'authorised_by'=>'0',
                'status'=>'pending'
            ]);
            // $upd_ti_status=Tax_Invoice::where('id',$id)->update(['is_cancelled'=>1]);
            $redirect_to = array(
                'deliverychallan'=>'/deliverychallan/list'
            );
            return redirect($redirect_to[$for])->with('success','Delete Permission Raised.');
        }
        if($for=="deliverychallanupdate"){
            $dc_number = Delivery_challan::where('id',$id)->first('challan_number')['challan_number'];
            $reason = str_replace(' - - - ','/',$reason);
            RequestPermission::insertGetId([
                'reason'=>$reason,
                'data_id'=>$id,
                'data_for'=>$for,
                'operation'=>'Update Delivery Challan',
                'request_for'=>$map_for[$for],
                'requested_by'=>Auth::id(),
                'authorised_by'=>'0',
                'status'=>'pending'
            ]);
            // $upd_ti_status=Tax_Invoice::where('id',$id)->update(['is_cancelled'=>1]);
            $redirect_to = array(
                'deliverychallanupdate'=>'/deliverychallan/list'
            );
            return redirect($redirect_to[$for])->with('success','Update Permission Raised.');
        }
        if($for=="taxinvoicedelete"){
            $tid_number = Tax_Invoice::where('id',$id)->first('invoice_number')['invoice_number'];
            $reason = str_replace(' - - - ','/',$reason);
            RequestPermission::insertGetId([
                'reason'=>$reason,
                'data_id'=>$id,
                'data_for'=>$for,
                'operation'=>'Delete Tax Invoice',
                'request_for'=>$map_for[$for],
                'requested_by'=>Auth::id(),
                'authorised_by'=>'0',
                'status'=>'pending'
            ]);
            // $upd_ti_status=Tax_Invoice::where('id',$id)->update(['is_cancelled'=>1]);
            $redirect_to = array(
                'taxinvoicedelete'=>'/taxinvoice/list'
            );
            return redirect($redirect_to[$for])->with('success','Delete Permission Raised.');
        }
        if($for=="taxinvoiceupdate"){
            $tid_number = Tax_Invoice::where('id',$id)->first('invoice_number')['invoice_number'];
            $reason = str_replace(' - - - ','/',$reason);
            RequestPermission::insertGetId([
                'reason'=>$reason,
                'data_id'=>$id,
                'data_for'=>$for,
                'operation'=>'Update Tax Invoice',
                'request_for'=>$map_for[$for],
                'requested_by'=>Auth::id(),
                'authorised_by'=>'0',
                'status'=>'pending'
            ]);
            // $upd_ti_status=Tax_Invoice::where('id',$id)->update(['is_cancelled'=>1]);
            $redirect_to = array(
                'taxinvoiceupdate'=>'/taxinvoice/list'
            );
            return redirect($redirect_to[$for])->with('success','Update Permission Raised.');
        }
       
    }
    public function check_required_permission($for,$id)
    {
        $map_for = array(
            'taxinvoice'=>'/cancel/taxinvoice/'.$id,
            'internalorder'=>'/update/internalorder/'.$id,
            'deliverychallan'=>'/deliverychallan/delete/'.$id,
            'deliverychallanupdate'=>'/deliverychallan/update/'.$id,
            'taxinvoicedelete'=>'/taxinvoice/delete/'.$id,
            'taxinvoiceupdate'=>'/taxinvoice/update/'.$id
            
        );
       
        $data = RequestPermission::leftJoin('users','users.id','request_permission.authorised_by')
            ->where('request_for',$map_for[$for])
            ->select(
                'request_permission.id',
                'request_permission.reason',
                'users.name as authorised_by',
                'request_permission.status',
                'request_permission.updated_at'
            )->orderBy('id','DESC')
            ->first();

        
        if(!$data){
            $data = array();
            $message = 'Generate Request';
        }
        else if($data)
        {
            $status  = $data['status'];
            if($status == 'pending'){
                $message = 'Pending Request';
            }
            else if($status == 'allowed'){
               $message = 'Allowed Request';
            }
            else if($status == 'rejected'){
               $message = 'Rejected Request';
            }
            else if($status == 'expired'){
                $message = 'Expired Request';
             }
            else if($status == 'cancelled'){
                $message = 'Cancelled Request';
            }
        }
        return array('message'=>$message,'data'=>$data);
        
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
            ->select(
                'request_permission.reason as rea',
                'request_permission.operation as op',
                'users.name as req',
                'request_permission.id as id',
                'request_permission.data_for as data_for',
                'request_permission.status as stat',
                DB::raw('CONCAT(request_permission.id ,"-", request_permission.status) as id1'),
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
            $data = ['op','rea','req','stat','id'];
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
    public function admin_auth_req()
    {
        $data=array('layout'=>'layouts.main');
        return view('sections.admin_grant_auth', $data);
    
    }
    public function admin_auth_req_grant($id,$do)
    {
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
                Tax_Invoice::where('id',$data->data_id)->update([
                    'is_cancelled'=>1,
                    'status'=>$st,
                    'cancellation_reason'=>$data->reason,
                    'cancellation_advised_by'=>Auth::id()
                ]);
                if($do=='allowed'){
                    Tax::where('tax_invoice_id',$data->data_id)->update([
                        'is_cancelled'=>"1"
                    ]);
                   
                    $io_id=Tax::where('tax_invoice_id',$data->data_id)
                    ->leftJoin('challan_per_io','challan_per_io.delivery_challan_id','tax.delivery_challan_id')
                    ->leftJoin('internal_order','internal_order.id','tax.io_id')
                    ->select('tax.io_id','tax.delivery_challan_id','job_details_id','tax.qty as tax_qty','challan_per_io.good_qty as challan_qty')
                    ->groupBy('tax.delivery_challan_id')->get();
                    foreach($io_id as $key){
                        if(isset($key['delivery_challan_id'])){
                            if($key['tax_qty']>=$key['challan_qty'])
                                $qty=$key['tax_qty']-$key['challan_qty'];
                            else
                                $qty= $key['challan_qty']-$key['tax_qty'];
                        }
                        else{
                            $qty= $key['tax_qty'];
                        }
                        JobDetails::where('id','=',$key['job_details_id'])->update(['left_qty'=>DB::raw('left_qty + '.$qty)]);

                    }
                // print($io_id);die;
                }
                return array('message'=>'success');

            }
            else{
                return array('message'=>'error occured');
            }
    }
    public function admin_auth_req_grant_update($id,$do,$for){
        // print_r($for);die;
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
                if($for=="taxinvoiceupdate"){
                    Tax_Invoice::where('id',$data->data_id)->update([
                        'is_update'=>$do
                    ]);
                }
                if($for=="deliverychallanupdate"){
                    Delivery_challan::where('id',$data->data_id)->update([
                        'is_update'=>$do
                    ]);
                }
                
                return array('message'=>'success');
            }
            else{
                return array('message'=>'error occured');
            }
    }

    
}


