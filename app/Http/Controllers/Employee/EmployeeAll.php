<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Employee\Assets;
use App\Model\Employee\EmployeeProfile;
use App\Model\Employee\EmployeePFESI;
use App\Model\Employee\EmployeeBank;
use App\Model\Employee\EmployeeAppoint;
use App\Model\Employee\AssetAssign;
use App\Model\Department;
use App\Model\Holiday;
use App\Model\Employee\EmployeeRelieving;
use App\Model\Employee\EmployeeCategory;
use App\Model\Employee\EmployeeDocument;
use App\Model\Employee\EmployeeNodues;
use App\Model\Employee\EmployeeTask;
use App\Model\Employee\EmployeeTaskStatus;
use App\Model\Employee\EmployeePFESIWithdrawal;
use App\Model\Employee\EmployeeFnF;
use App\Model\Settings;
use App\Custom\CustomHelpers;
use App\Model\Users;
use Auth;
use File;
use DB;
use DateTime;

class EmployeeAll extends Controller
{
    public function create_employee(){
        $assets=Assets::all();
        $dept=Department::all();
        $user=Users::where('active',1)->get();
        $data=array(
            'assets'=>$assets,
            'dept'=>$dept,
            'user'=>$user,
            'layout' => 'layouts.main'
        );
        return view('employee.create_employee',$data);
    }
    public function create_employeeDb(Request $request){
        try {
            $this->validate($request,[
                'emp_name'=>'required',
                'father'=>'required',
                'dob'=>'required',
                'l_add'=>'required',
                'p_add'=>'required',
                'mob'=>'required|digits:10|unique:employee__profile,mobile',
                'h_land'=>'nullable|digits:10|unique:employee__profile,home_landline' ,
                'f_num'=>'nullable|digits:10|unique:employee__profile,family_number',
                'rel'=>'required',
                'doj'=>'required',
                // 'assets'=>'required',
                'join'=>'required',
                'desig'=>'required',
                'skill'=>'required',
                'dept'=>'required',
                'from'=>'required',
                'to'=>'required',
                'ot'=>'required',
                'reporthead'=>'required',
                // 'emp_email'=>'required',
                'emp_aadhar'=>'required'

            ],[
                'emp_name.required'=> 'Employee Name is required.',
                'father.required'=> 'Father Name is required.',
                'dob.required'=> 'DOB is required.',
                'l_add.required'=> 'Local Address is required.',
                'p_add.required'=> 'Permanent Address is required.',
                'mob.required'=> 'Phone Number is required.',
                'mob.digits'=> 'Phone Number contains digits only.',
                'mob.unique'=> 'Phone Number already taken.',
                'f_num.required'=> 'Family Number is required.',
                'f_num.digits'=> 'Family Number contains digits only.',
                'f_num.unique'=> 'Family Number already taken.',
                'h_land.digits'=> 'Landline Number contains digits only.',
                'h_land.unique'=> 'Landline Number already taken.',
                'rel.required'=> 'Relative Name is required.',
                'doj.required'=> 'Date Of Joining is required.',
                'reporthead.required'=> 'Reporting head is required.',
                // 'assets.required'=> 'Assets is required.',
                'join.required'=> 'Paper Signed is required.',
                'desig.required'=> 'Designation is required.',
                'skill.required'=> 'Skill is required.',
                'dept.required'=> 'Department is required.',
                'from.required'=> 'From Shift Timing is required.',
                'to.required'=> 'To Shift Timing is required.',
                'ot.required'=> 'overtime is required.',
                // 'emp_email.required'=> 'Email is required.',
                'emp_aadhar.required'=> 'Email is required.'
                // 'overtime.required'=>'This Fiels Is required'
    
            ]);
            $time = $request->input('from').' to '.$request->input('to');
            $from = CustomHelpers::TImeConvert($request->input('from'));
            $to = CustomHelpers::TimeConvert($request->input('to'));
            $settings = Settings::where('name','Employee_Prefix')->first();
            $emp_number = $settings->value;
            $employee=EmployeeProfile::insertGetId([
                'id'=>NULL,
                'name'=>$request->input('emp_name'),
                'father_name'=>$request->input('father'),
                'dob'=>date("Y-m-d", strtotime($request->input('dob'))),
                'local_address'=>$request->input('l_add'),
                'permanent_address'=>$request->input('p_add'),
                'home_landline'=>$request->input('h_land'),
                'mobile'=>$request->input('mob'),
                'family_number'=>$request->input('f_num'),
                'relation_with_emp'=>$request->input('rel'),
                'doj'=>date("Y-m-d", strtotime($request->input('doj'))),
                'joining_paper_signed'=>$request->input('join'),
                'designation'=>$request->input('desig'),
                'employee_skill'=>$request->input('skill'),
                'department_id'=>$request->input('dept'),
                'shifting_timing'=>$time,
                'is_OT'=>$request->input('ot'),
                'overtime'=>$request->input('is_OT') =='Yes' ? $request->input('overtime') : '',
                'reporting'=>$request->input('reporthead'),
                'email'=>$request->input('emp_email'),
                'aadhar'=>$request->input('emp_aadhar'),
                'created_by'=>Auth::id(),
                'is_active'=>1,
                'shift_from' => $from,
                'shift_to' => $to,
                'created_at'=>date('Y-m-d G:i:s')

            ]);
            // $pfesi=EmployeePFESI::insertGetId([
            //     'id'=>NULL,
            //     'emp_id'=>$employee
            // ]);
            // if($pfesi==NULL){
            //     DB::rollback();
            //     return redirect('/employee/profile/create')->with('error','Some Unexpected Error occurred.');  
            // }
            // $bank=EmployeeBank::insertGetId([
            //     'id'=>NULL,
            //     'emp_id'=>$employee
            // ]);
            // if($bank==NULL){
            //     DB::rollback();
            //     return redirect('/employee/profile/create')->with('error','Some Unexpected Error occurred.');  
            // }
            // $relieving=EmployeeRelieving::insertGetId([
            //     'id'=>NULL,
            //     'emp_id'=>$employee
            // ]);
            // if($relieving==NULL){
            //     DB::rollback();
            //     return redirect('/employee/profile/create')->with('error','Some Unexpected Error occurred.');  
            // }
            if($employee==NULL){
                DB::rollback();
                return redirect('/employee/profile/create')->with('error','Some Unexpected Error occurred.');  
            }
            $prefix = $emp_number ."/".$employee;
            $emp_number= EmployeeProfile::where('id',$employee)->update(
                [
                    'employee_number'=> $prefix,
                ]
            );
            return redirect('/employee/profile/update/'.$employee)->with("success","Employee Profile successfully created.");
        } 
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/profile/create')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function update_employee($id){
        $employee = EmployeeProfile::where('id',$id)->get()->first();
        $shift_from =  date("g : i A", strtotime($employee['shift_from']));
        $shift_to =  date("g : i A", strtotime($employee['shift_to']));
        $assets=Assets::all();
        $dept=Department::all();
        $user=Users::where('active',1)->get();

        $data = array(
            'assets' => $assets,
            'employee' => $employee,
            'dept' => $dept,
            'shift_from' => $shift_from,
            'shift_to' => $shift_to,
            'user' => $user,
            'id' => $id,
            'layout' => 'layouts.main'
        );
        return view('employee.update_employee',$data);
    }
    public function update_employeeDb(Request $request,$id){
        try {
            $this->validate($request,[
                'emp_name'=>'required',
                'father'=>'required',
                'dob'=>'required',
                'l_add'=>'required',
                'p_add'=>'required',
                'mob'=>'required|digits:10|unique:employee__profile,mobile,'.$id,
                'h_land'=>'nullable|digits:10|unique:employee__profile,home_landline,'.$id ,
                'f_num'=>'nullable|digits:10|unique:employee__profile,family_number,'.$id,
                'rel'=>'required',
                'doj'=>'required',
                // 'assets'=>'required',
                'join'=>'required',
                'desig'=>'required',
                'skill'=>'required',
                'dept'=>'required',
                'from'=>'required',
                'to'=>'required',
                'ot'=>'required',
                'reporthead'=>'required',
                // 'emp_email'=>'required',
                'emp_aadhar'=>'required'
                // 'overtime'=>'required',
            ],[
                'emp_name.required'=> 'Employee Name is required.',
                'father.required'=> 'Father Name is required.',
                'dob.required'=> 'DOB is required.',
                'l_add.required'=> 'Local Address is required.',
                'p_add.required'=> 'Permanent Address is required.',
                'mob.required'=> 'Phone Number is required.',
                'mob.digits'=> 'Phone Number contains digits only.',
                'mob.unique'=> 'Phone Number already taken.',
                'f_num.required'=> 'Family Number is required.',
                'f_num.digits'=> 'Family Number contains digits only.',
                'f_num.unique'=> 'Family Number already taken.',
                'h_land.digits'=> 'Landline Number contains digits only.',
                'h_land.unique'=> 'Landline Number already taken.',
                'rel.required'=> 'Relative Name is required.',
                'doj.required'=> 'Date Of Joining is required.',
                // 'assets.required'=> 'Assets is required.',
                'join.required'=> 'Paper Signed is required.',
                'desig.required'=> 'Designation is required.',
                'skill.required'=> 'Skill is required.',
                'dept.required'=> 'Department is required.',
                'from.required'=> 'From Shift Timing is required.',
                'to.required'=> 'To Shift Timing is required.',
                'ot.required'=> 'overtime is required.',
                'reporthead.required'=> 'Reporting head is required.',
                // 'emp_email.required'=> 'Email is required.',
                'emp_aadhar.required'=> 'Email is required.'
                // 'overtime.required'=>'This Fiels Is required',
              
    
            ]);
            $time=$request->input('from').' to '.$request->input('to');
            $from = CustomHelpers::TImeConvert($request->input('from'));
            $to = CustomHelpers::TimeConvert($request->input('to'));
            $employee=EmployeeProfile::where('id',$id)->update([
                'name'=>$request->input('emp_name'),
                'father_name'=>$request->input('father'),
                'dob'=>date("Y-m-d", strtotime($request->input('dob'))),
                'local_address'=>$request->input('l_add'),
                'permanent_address'=>$request->input('p_add'),
                'home_landline'=>$request->input('h_land'),
                'mobile'=>$request->input('mob'),
                'family_number'=>$request->input('f_num'),
                'relation_with_emp'=>$request->input('rel'),
                'doj'=>date("Y-m-d", strtotime($request->input('doj'))),
                'joining_paper_signed'=>$request->input('join'),
                'designation'=>$request->input('desig'),
                'employee_skill'=>$request->input('skill'),
                'department_id'=>$request->input('dept'),
                'shifting_timing'=>$time,
                'reporting'=>$request->input('reporthead'),
                'email'=>$request->input('emp_email'),
                'aadhar'=>$request->input('emp_aadhar'),
                'is_OT'=>$request->input('ot'),
                'shift_to' => $to,
                'shift_from' => $from,
                'overtime'=>$request->input('ot') =='Yes' ? $request->input('overtime') : ''

            ]);
            if($employee==NULL){
                DB::rollback();
                return redirect('/employee/profile/update/'.$id)->with('error','Some Unexpected Error occurred.');  
            }
            CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Employee Profile Updated");
            return redirect('/employee/profile/update/'.$id)->with("success","Employee Profile successfully Updated.");
        } 
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/profile/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
    }

    public function update_pfesi($id){
        $pfesi = EmployeePFESI::where('emp_id',$id)->get()->first();

        $pfesi_withdrawal = EmployeePFESIWithdrawal::leftjoin('employee__profile' ,'employee__pfesi_withdrawal.emp_id','employee__profile.id')->select('employee__pfesi_withdrawal.*','employee__profile.name')->where('emp_id',$id)->get();
        $data=array('id' => $id,
            'layout' => 'layouts.main');
       if($pfesi){
        // return $pfesi;
        $data +=array('pfesi' => $pfesi,
            'pfesi_withdrawal' => $pfesi_withdrawal
          );

       }
       // else{
       //      return redirect('/employee/profile/update/'.$id)->with('error','some error occurred');
       //  }
        return view('employee.update_pfesi',$data);
    }
    public function update_pfesiDb(Request $request,$id){
       // print_r($request->input());die;
        try {
             $pfesi = EmployeePFESI::where('emp_id',$id);
                 $exist =$pfesi->get()->toArray();
            // $this->validate($request,['is_registered'=>'required',],
                // ['is_registered.required'=> 'This field is required.',]);
            // if ($request->input('is_registered') == 'No') {
                
            //      if($exist){
            //         $pfesi->update([
            //     'is_pfesi'=>$request->input('is_registered')]);
            //      CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Employee PF/ESI Details Updated");
            //      return redirect('/employee/pfesi/update/'.$id)->with("success","Employee PF/ESI Details successfully Updated."); 

            //     }else{
            //         EmployeePFESI::insertGetId([
            //             'emp_id'=>$id,
            //         'is_pfesi'=>$request->input('is_registered')]);
            //         return redirect('/employee/pfesi/update/'.$id)->with("success","Employee PF/ESI Details successfully Inserted."); 
            //     }
             // }else{

            $pf_date = null;
            $esi_date = null;
            if($request->input('pf_leaving')){
                $pf_date = date("Y-m-d", strtotime($request->input('pf_leaving')));
            }
            if($request->input('esi_leaving')){
                $esi_date = date("Y-m-d", strtotime($request->input('esi_leaving')));
            }
            if ($request->input('pf') == 'pf') {
            
                if ($request->input('pf_withdrawal') != '') {
                     $pfesi_withdrawal = EmployeePFESIWithdrawal::insertGetId([
                        'emp_id' => $id,
                        'withdrawal_type' => $request->input('pf_withdrawal'),
                        'withdrawal_date' => date("Y-m-d", strtotime($request->input('withdrawal_date')))
                    ]); 
                }
               
            }

            if($request->input('esi') != 'esi' && $request->input('pf') != 'pf'){
                return redirect('/employee/pfesi/update/'.$id)->with('error','Error in submitting form.'); 
            }else if($exist){
                if ($request->input('pf') == 'pf') {
                    $this->validate($request,[
                        'pf_date'=>'required',
                        'pf_no'=>'required|unique:employee__pfesi,pf_no,'.$id.',emp_id',
                        // 'pf_withdrawal'=>'required',
                        // 'pf_leaving'=>'required',
                        // 'withdrawal_date'=>'required',
                    ],[
                        'pf_date.required'=> 'PF date  is required.',
                        'pf_no.required'=> 'PF number is required.',
                        'pf_no.unique'=> 'This PF number is already present.',
                        // 'pf_withdrawal.required'=> 'PF withdrawal is required.',
                        // 'pf_leaving.required'=> 'PF leaving is required.',
                        // 'withdrawal_date.required'=> 'Withdrawal date is required.',
            
                    ]);
                }    
                if ($request->input('esi') == 'esi') {
                        $this->validate($request,[
                        'esi_date'=>'required',
                        'esi_no'=>'required|unique:employee__pfesi,esi_no,'.$id.',emp_id',
                        // 'esi_leaving'=>'required',
                    ],[
                        'esi_date.required'=> 'ESI date  is required.',
                        'esi_no.required'=> 'ESI number is required.',
                        'esi_no.unique'=> 'This ESI number is already present.',
                        
                        // 'esi_leaving.required'=> 'ESI leaving is required.',
                    ]);
                }
                
                $pfesi = EmployeePFESI::where('emp_id',$id)->update([
                'is_pfesi'=>$request->input('is_registered'),
                'enroll_date_pf'=>date("Y-m-d", strtotime($request->input('pf_date'))),
                'enroll_date_esi'=>date("Y-m-d", strtotime($request->input('esi_date'))),
                'pf'=>$request->input('pf'),
                'esi'=>$request->input('esi'),
                'pf_no'=>$request->input('pf_no'),
                'esi_no'=>$request->input('esi_no'),
                'leave_date_pf'=>$pf_date,
                'leave_date_esi'=>$esi_date,
                ]);
                if ($pfesi == NULL) {
                    DB::rollback();
                    return redirect('/employee/pfesi/update/'.$id)->with('error','Some Unexpected Error occurred.');  
                }
                return redirect('/employee/pfesi/update/'.$id)->with("success","Employee PF/ESI Details successfully Updated."); 
            }else{

                if ($request->input('pf') == 'pf') {
                    $this->validate($request,[
                        'pf_date'=>'required',
                        'pf_no'=>'required|unique:employee__pfesi,pf_no',
                        // 'pf_withdrawal'=>'required',
                        // 'pf_leaving'=>'required',
                        // 'withdrawal_date'=>'required',
                    ],[
                        'pf_date.required'=> 'PF date  is required.',
                        'pf_no.required'=> 'PF number is required.',
                        'pf_no.unique'=> 'This PF number is already present.',
                        // 'pf_withdrawal.required'=> 'PF withdrawal is required.',
                        // 'pf_leaving.required'=> 'PF leaving is required.',
                        // 'withdrawal_date.required'=> 'Withdrawal date is required.',
            
                    ]);
                }    
                if ($request->input('esi') == 'esi') {
                        $this->validate($request,[
                        'esi_date'=>'required',
                        'esi_no'=>'required|unique:employee__pfesi,esi_no',
                        // 'esi_leaving'=>'required',
                    ],[
                        'esi_date.required'=> 'ESI date  is required.',
                        'esi_no.required'=> 'ESI number is required.',
                        'esi_no.unique'=> 'This ESI number is already present.',
                        
                        // 'esi_leaving.required'=> 'ESI leaving is required.',
                    ]);
                }

                $pfesi = EmployeePFESI::insertGetId([
                    'emp_id'=>$id,
                    'is_pfesi'=>$request->input('is_registered'),
                    'enroll_date_pf'=>date("Y-m-d", strtotime($request->input('pf_date'))),
                    'enroll_date_esi'=>date("Y-m-d", strtotime($request->input('esi_date'))),
                    'pf'=>$request->input('pf'),
                    'esi'=>$request->input('esi'),
                    'pf_no'=>$request->input('pf_no'),
                    'esi_no'=>$request->input('esi_no'),
                    'leave_date_pf'=>$pf_date,
                    'leave_date_esi'=>$esi_date,
                ]);

                if ($pfesi == NULL) {
                    DB::rollback();
                    return redirect('/employee/pfesi/update/'.$id)->with('error','Some Unexpected Error occurred.');  
                }
                return redirect('/employee/pfesi/update/'.$id)->with("success","Employee PF/ESI Details successfully Inserted."); 
            }
            
            // }
           
        } 
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/pfesi/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
    }


    //bank


    public function update_bank($id){
        $bank = EmployeeBank::where('emp_id',$id)->get()->first();
        if($bank){
        $data=array(
            'employee'=>$bank,
            'id'=>$id,
            'layout' => 'layouts.main'
        );
        // return $pfesi;
        return view('employee.update_bank',$data);
       }
       else{
        $data=array(
            'id'=>$id,
            'layout' => 'layouts.main'
        );
        // return $pfesi;
        return view('employee.update_bank',$data);
        // return redirect('/employee/bank/update/'.$id)->with('error','some error occurred');
    }
    }
    public function update_bankDb(Request $request,$id){
        // print_r($request->input());die;
         $bank = EmployeeBank::where('emp_id',$id)->get()->first();
        try {
            $this->validate($request,[
                'bank_status'=>'required',
                'acc_name'=>'required',
                'acc_number'=>'required',
                'acc_ifsc'=>'required'
            ],[
                'bank_status.required'=>'Bank Status is required',
                'acc_name.required'=>'Account Name is required',
                'acc_number.required'=>'Account Number is required',
                'acc_ifsc.required'=>'IFSC Code is required'
    
            ]);
           if($bank){
            $pfesi=EmployeeBank::where('emp_id',$id)->update([
                'bank_status'=>$request->input('bank_status'),
                'acc_name'=>$request->input('acc_name'),
                'acc_number'=>$request->input('acc_number'),
                'acc_ifsc'=>$request->input('acc_ifsc')
                
            ]);
            if($pfesi==NULL){
                DB::rollback();
                return redirect('/employee/bank/update/'.$id)->with('error','Some Unexpected Error occurred.');  
            }
            CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Employee Bank Details Updated");
            return redirect('/employee/bank/update/'.$id)->with("success","Employee Bank Details successfully Updated.");
        }else{
            $pfesi=EmployeeBank::insertGetId([
                'emp_id'=>$id,
                'bank_status'=>$request->input('bank_status'),
                'acc_name'=>$request->input('acc_name'),
                'acc_number'=>$request->input('acc_number'),
                'acc_ifsc'=>$request->input('acc_ifsc')
                
            ]);
            if($pfesi==NULL){
                DB::rollback();
                return redirect('/employee/bank/update/'.$id)->with('error','Some Unexpected Error occurred.');  
            }
           
            return redirect('/employee/bank/update/'.$id)->with("success","Employee Bank Details successfully Inserted.");
        }
            
        } 
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/bank/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
    }

    public function employee_list(){
        $data=array('layout'=>'layouts.main');
        return view('employee.employee_summary',$data);    
    }
    public function employee_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = EmployeeProfile::leftJoin('department','department.id','employee__profile.department_id')
        ->select('employee__profile.id',
        'employee__profile.name',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        'employee__profile.designation',
        'department.department');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'department.department'];
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
    public function employee_left_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = EmployeeRelieving::leftjoin('employee__profile','employee__relieving.emp_id','employee__profile.id')
        ->leftJoin('department','department.id','employee__profile.department_id')
        
        ->select('employee__profile.id',
        'employee__profile.name',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        'employee__profile.designation','employee__profile.doj',
        'department.department','employee__relieving.leaving_date');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%");
                    });
                        
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'department.department','doj','leaving_date'];
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
    public function employee_working_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = EmployeeProfile::leftJoin('department','department.id','employee__profile.department_id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select('employee__profile.id',
        'employee__profile.name',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        DB::raw('Concat(employee__profile.name,"(",employee__profile.employee_number,")") as name_with_code'),
        'employee__profile.designation',DB::raw('DATE_FORMAT(employee__profile.doj,"%d-%m-%Y")as doj'),
        'department.department','employee__relieving.emp_id');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%");
                    });
                        
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'department.department','doj'];
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
    public function update_relieving($id) {
        $relieving = EmployeeRelieving::where('emp_id',$id)->get()->first();
        $assets = AssetAssign::where('employee_id',$id)->where('status','<>','Returned')
        ->leftjoin('assets','assets.asset_id','asset_assign.asset_id')->get()->toArray();
        
           if($relieving){
            $relieving = $relieving->toArray();
            $data = array(
                'relieving'=>$relieving,
                'assets' => $assets,
                'id'=>$id,
                'layout' => 'layouts.main'
            );
            // return $pfesi;
            return view('employee.update_relieving',$data);
           }
           else{
            $data = array(
                'id'=>$id,
                'assets' =>$assets,
                'layout' => 'layouts.main'
            );
            // return $pfesi;
            return view('employee.update_relieving',$data);
            // return redirect('/employee/profile/update/'.$id)->with('error','some error occurred');
        } 
    }

    public function update_relievingDB(Request $request, $id)
     {
         // print_r($request->input());die;
        try {
            $this->validate($request,[
                // 'update_reason'=>'required',
                // 'assets'=>'required',
                'leaving_date'=>'required',
                'fnf_complete'=>'required',
                'fnf_date'=>'required_if:fnf_complete,yes',
                'certificate_file'=> 'mimes:jpeg,png,jpg,pdf|max:'.CustomHelpers::getfilesize(),
                'signed_copy_file'=> 'mimes:jpeg,png,jpg,pdf|max:'.CustomHelpers::getfilesize(),
                'resignation_latter_file'=> 'mimes:jpeg,png,jpg,pdf|max:'.CustomHelpers::getfilesize(),
                'reason'=>'required',
                'resignation_d'=>'required'
            ],[
                // 'update_reason.required'=> 'Update reason is required.',
                // 'assets.required'=> 'Assets is required.',
                'leaving_date.required'=> 'Date is required.',
                'fnf_complete.required'=> 'FNF Complete is required.',
                'fnf_date.required_if'=> 'FNF date is required.',
                'certificate_file.required'=> 'Document required only jpeg,png,jpg,pdf format',
                'certificate_file.max'=> 'Document exceeded maxSize',
                'signed_copy_file.max'=> 'Document exceeded maxSize',
                'resignation_latter_file.max'=> 'Document exceeded maxSize',
                'signed_copy_file.required'=> 'Document required only jpeg,png,jpg,pdf format',
                'resignation_latter_file.mimes'=> 'Document required only jpeg,png,jpg,pdf format',
                'resignation_latter_file.required'=> 'Document is required',
                'reason.required'=> 'Reason is required.',
                'resignation_d.required'=>'Resignation Date is required'
    
            ]);

            $fnf_date =  new DateTime($request->input('fnf_date'));
            $leaving_date = new DateTime($request->input('leaving_date'));
            if ($leaving_date <= $fnf_date ) {
                
            }else{
                return redirect('/employee/relieving/update/'.$id)->with('error','Please select equal to leaving date or more.'); 
            }           

            $certificate_file ='';
            $signed_copy_file ='';
            $resignation_latter_file='';
            $relieving = EmployeeRelieving::where('emp_id',$id)->select('employee__relieving.*')->get()->first();
            if($relieving){
                $this->validate($request,['update_reason'=>'required'],[
                'update_reason.required'=> 'Update reason is required.']);
                if($request->hasFile('certificate_file') || $request->input('certificate_file'))
                {
                    $file = $request->file('certificate_file');
                    $destinationPath = public_path().'/upload/employee/'.$id.'/relieving/';  
                    $filenameWithExt = $request->file('certificate_file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);  
                    $extension = $request->file('certificate_file')->getClientOriginalExtension();
                    $certificate_file = $filename.'_'.time().'.'.$extension;  
                    $path = $file->move($destinationPath, $certificate_file);

                    if(isset($relieving['certificate_file'])){
                         File::delete($destinationPath.$relieving['certificate_file']); 
                     }
                      
                }
                else{
                    $certificate_file= $relieving['certificate_file'];
                }
                if($request->hasFile('signed_copy_file') || $request->input('signed_copy_file'))
                {
                    $file = $request->file('signed_copy_file');
                    $destinationPath = public_path().'/upload/employee/'.$id.'/relieving/';          
                    $filenameWithExt = $request->file('signed_copy_file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
                    $extension = $request->file('signed_copy_file')->getClientOriginalExtension();
                    $signed_copy_file = $filename.'_'.time().'.'.$extension; 
                    $path = $file->move($destinationPath, $signed_copy_file);

                    if(isset($relieving['signed_copy_file'])){
                        File::delete($destinationPath.$relieving['signed_copy_file']);
                    }
                }
                else{
                    $signed_copy_file= $relieving['signed_copy_file'];
                }
                if ($request->input('assets')) {
                    $assets = implode(',',$request->input('assets'));
                }else{
                    $assets = '';
                }
                if($request->hasFile('resignation_latter_file') || $request->input('resignation_latter_file'))
                {

                    $file = $request->file('resignation_latter_file');
                    $destinationPath = public_path().'/upload/employee/'.$id.'/relieving/';      
                    $filenameWithExt = $request->file('resignation_latter_file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);  
                    $extension = $request->file('resignation_latter_file')->getClientOriginalExtension();
                    $resignation_latter_file = $filename.'_'.time().'.'.$extension;  
                    $path = $file->move($destinationPath, $resignation_latter_file);
                   
                    if(isset($relieving['resignation_latter_file'])){
                       File::delete($destinationPath.$relieving['resignation_latter_file']);
                    }
                }
                else{
                    $resignation_latter_file= $relieving['resignation_latter_file'];
                }
                    //update 
                     $relieving = EmployeeRelieving::where('emp_id',$id)->update([
                    'update_reason' => $request->input('update_reason'),
                    'leaving_assets' => $assets,
                    'fnf_date' => date("Y-m-d", strtotime($request->input('fnf_date'))),
                    'leaving_date' => date("Y-m-d", strtotime($request->input('leaving_date'))),
                    'fnf_complete' => $request->input('fnf_complete'),
                    'certificate_file' => $certificate_file,
                    'signed_copy_file' => $signed_copy_file,
                    'resignation_latter_file' => $resignation_latter_file,
                    'leaving_reason' => $request->input('reason'),
                    'resignation_date'=>date("Y-m-d", strtotime($request->input('resignation_d'))),
                    
                ]);
                if($relieving==NULL){
                    DB::rollback();
                    return redirect('/employee/relieving/update/'.$id)->with('error','Some Unexpected Error occurred.');  
                }
                CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Employee Relieving  Updated");
                return redirect('/employee/relieving/update/'.$id)->with("success","Employee Relieving Updated Successfully .");
           }else{
                if ($request->input('assets')) {
                    $assets = implode(',',$request->input('assets'));
                }else{
                    $assets = '';
                }
                if($request->hasFile('certificate_file') || $request->input('certificate_file'))
                {
                    $file = $request->file('certificate_file');
                    $destinationPath = public_path().'/upload/employee/'.$id.'/relieving/';  
                    $filenameWithExt = $request->file('certificate_file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);  
                    $extension = $request->file('certificate_file')->getClientOriginalExtension();
                    $certificate_file = $filename.'_'.time().'.'.$extension;  
                    $path = $file->move($destinationPath, $certificate_file);
                    File::delete($destinationPath.$relieving['certificate_file']);    
                }
                else{
                    $certificate_file= '';
                }
                if($request->hasFile('signed_copy_file') || $request->input('signed_copy_file'))
                {
                    $file = $request->file('signed_copy_file');
                    $destinationPath = public_path().'/upload/employee/'.$id.'/relieving/';
                    $filenameWithExt = $request->file('signed_copy_file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
                    $extension = $request->file('signed_copy_file')->getClientOriginalExtension();
                    $signed_copy_file = $filename.'_'.time().'.'.$extension; 
                    $path = $file->move($destinationPath, $signed_copy_file);
                    File::delete($destinationPath.$relieving['signed_copy_file']);  
                }
                else{
                    $signed_copy_file= '';
                }
                if($request->hasFile('resignation_latter_file') || $request->input('resignation_latter_file'))
                {

                    $file = $request->file('resignation_latter_file');
                    $destinationPath = public_path().'/upload/employee/'.$id.'/relieving/';      
                    $filenameWithExt = $request->file('resignation_latter_file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);  
                    $extension = $request->file('resignation_latter_file')->getClientOriginalExtension();
                    $resignation_latter_file = $filename.'_'.time().'.'.$extension;  
                    $path = $file->move($destinationPath, $resignation_latter_file);
                    File::delete($destinationPath.$relieving['resignation_latter_file']);  
                }
                else{
                    $resignation_latter_file= '';
                }
                $relieving = EmployeeRelieving::insertGetId([
                'emp_id'=>$id,
                'leaving_assets' => $assets,
                'fnf_date' => date("Y-m-d", strtotime($request->input('fnf_date'))),
                'leaving_date' => date("Y-m-d", strtotime($request->input('leaving_date'))),
                'fnf_complete' => $request->input('fnf_complete'),
                'certificate_file' => $certificate_file,
                'signed_copy_file' => $signed_copy_file,
                'resignation_latter_file' => $resignation_latter_file,
                'leaving_reason' => $request->input('reason'),
                'resignation_date'=>date("Y-m-d", strtotime($request->input('resignation_d'))),
                'created_by' => Auth::id()
                
            ]);
            EmployeeProfile::where('id',$id)->update([
                'is_active'=>0
            ]);
                    
            if($relieving==NULL){
                DB::rollback();
                return redirect('/employee/relieving/update/'.$id)->with('error','Some Unexpected Error occurred.');  
            }
          
            return redirect('/employee/relieving/update/'.$id)->with("success","Employee Relieving Inserted Successfully .");
           }
           
        } 
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/relieving/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
    }

    public function update_emp_category($id) {
        $employee_exist = EmployeeCategory::leftjoin('employee__category_master','employee__category.emp_cat_id','employee__category_master.id')
        ->where('emp_id',$id)->select('employee__category.*',
        'employee__category_master.name as employee_category'
        )->get()->toArray();
 
        $category = array();
        if($employee_exist){
            foreach($employee_exist as $emp){
                $category[$emp['employee_category']]=$emp;
            }
        }
      
        $data=array('layout'=>'layouts.main','id'=>$id,'access_category'=>$category);
        return view('employee.update_emp_category',$data);
    }
    public function update_emp_categoryDB(Request $request,$id){
        try{
            $timestamp = date('Y-m-d G:i:s');

            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$request->input('cat_type'))->first("id");

            $employee_exist = EmployeeCategory::leftjoin('employee__category_master','employee__category.emp_cat_id','employee__category_master.id')
            ->where('emp_id',$id)->where('emp_cat_id',$cat_type_id->id)
            ->get()->toArray();
            
            if($employee_exist)
            {
                if($request->input('cat_type')=="Offer Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'offer_date'=>'required',
                        'designation'=>'required',
                        'joining'=>'required',
                        'letter_issue_of'=>'required',
                        'update_reason_of'=>'required',
                        'letter_upload_of'=>'required_if:letter_issue_of,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        'offer_date.required'=>'This field is required',
                        'designation.required'=>'This field is required',
                        'joining.required'=>'This field is required',
                        'letter_issue_of.required'=>'This field is required',
                        'update_reason_of.required'=>'This field is required',
                        'letter_upload_of.mimes'=>'Document required and accept pdf',
                        'letter_upload_of.max'=>'Document exceeded maxlength',
                        'letter_upload_of.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $file = $request->file('letter_upload_of');
                    $pdf_file ='';    
                    if(!isset($file)||$file == null){
                        $pdf_file = $request->input('old_file_of');
                    }else{
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_of')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_of')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                        File::delete($destinationPath.$request->input('old_file_of'));
                    }
                    $offer_update = EmployeeCategory::where('emp_id',$id)
                    ->where('emp_cat_id',$cat_type_id->id)
                    ->update([
                        'cat_date'=>date("Y-m-d", strtotime($request->input('offer_date'))),
                        'cat_designation'=>$request->input('designation'),
                        'joining'=>date("Y-m-d", strtotime($request->input('joining'))),
                        'is_letter_issue'=>$request->input('letter_issue_of'),
                        'apt_letter'=>$pdf_file,
                        'update_reason'=>$request->input('update_reason_of'),
                        'updated_at' => $timestamp
                    ]);
                    if($offer_update==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Updated Offer Letter.');      
                    }
                }
                if($request->input('cat_type')=="Trainee Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        't_app_date'=>'required',
                        'stipend'=>'required',
                        'letter_issue_tr'=>'required',
                        'update_reason_tr'=>'required',
                        'letter_upload_tr'=>'required_if:letter_issue_tr,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        't_app_date.required'=>'This field is required',
                        'stipend.required'=>'This field is required',
                        'update_reason_tr.required'=>'This field is required',
                        'letter_issue_tr.required'=>'This field is required',
                        'letter_upload_tr.mimes'=>'Document required and accept pdf',
                        'letter_upload_tr.max'=>'Document exceeded maxlength',
                        'letter_upload_tr.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $file = $request->file('letter_upload_tr');
                    $pdf_file ='';    
                    if(!isset($file)||$file == null){
                        $pdf_file = $request->input('old_file_tr');
                    }else{
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_tr')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_tr')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                        File::delete($destinationPath.$request->input('old_file_tr'));
                    }
                    $trainee_update = EmployeeCategory::where('emp_id',$id)
                    ->where('emp_cat_id',$cat_type_id->id)
                    ->update([
                        'cat_date'=>date("Y-m-d", strtotime($request->input('t_app_date'))),
                        'stipend'=>$request->input('stipend'),
                        'is_letter_issue'=>$request->input('letter_issue_tr'),
                        'apt_letter'=>$pdf_file,
                        'update_reason'=>$request->input('update_reason_tr'),
                        'updated_at' => $timestamp
                    ]);
                    if($trainee_update==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Updated Trainee Appointment Letter.');      
                    }
                }
                if($request->input('cat_type')=="Probation Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'prob_design'=>'required',
                        'prob_sal'=>'required',
                        'prob_date'=>'required',
                        'update_reason_pr'=>'required',
                        'letter_issue_pr'=>'required',
                        'letter_upload_pr'=>'required_if:letter_issue_pr,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        'prob_design.required'=>'This field is required',
                        'prob_sal.required'=>'This field is required',
                        'prob_date.required'=>'This field is required',
                        'update_reason_pr.required'=>'This field is required',
                        'letter_issue_pr.required'=>'This field is required',
                        'letter_upload_pr.mimes'=>'Document required and accept pdf',
                        'letter_upload_pr.max'=>'Document exceeded maxlength',
                        'letter_upload_pr.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $file = $request->file('letter_upload_pr');
                    $pdf_file ='';    
                    if(!isset($file)||$file == null){
                        $pdf_file = $request->input('old_file_pr');
                    }else{
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_pr')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_pr')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                        File::delete($destinationPath.$request->input('old_file_pr'));
                    }
                    $probation_update =EmployeeCategory::where('emp_id',$id)
                    ->where('emp_cat_id',$cat_type_id->id)
                    ->update([ 
                        'cat_date'=>date("Y-m-d", strtotime($request->input('prob_date'))),
                        'cat_designation'=>$request->input('prob_design'),
                        'stipend'=>$request->input('prob_sal'),
                        'is_letter_issue'=>$request->input('letter_issue_pr'),
                        'apt_letter'=>$pdf_file,
                        'update_reason'=>$request->input('update_reason_pr'),
                        'updated_at' => $timestamp
                    ]);
                    if($probation_update==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Updated Probation Appointment Letter.');      
                    }
                }
                if($request->input('cat_type')=="Confirmation Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'conf_desig'=>'required',
                        'conf_date'=>'required',
                        'update_reason_co'=>'required',
                        'letter_issue_co'=>'required',
                        'letter_upload_co'=>'required_if:letter_issue_co,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        'conf_desig.required'=>'This field is required',
                        'conf_date.required'=>'This field is required',
                        'update_reason_co.required'=>'This field is required',
                        'letter_issue_co.required'=>'This field is required',
                        'letter_upload_co.mimes'=>'Document required and accept pdf',
                        'letter_upload_co.max'=>'Document exceeded maxlength',
                        'letter_upload_co.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $file = $request->file('letter_upload_co');
                    $pdf_file ='';    
                    if(!isset($file)||$file == null){
                        $pdf_file = $request->input('old_file_co');
                    }else{
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_co')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_co')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                        File::delete($destinationPath.$request->input('old_file_co'));
                    }
                    $conf_update = EmployeeCategory::where('emp_id',$id)
                    ->where('emp_cat_id',$cat_type_id->id)
                    ->update([ 
                        'cat_date'=>date("Y-m-d", strtotime($request->input('conf_date'))),
                        'cat_designation'=>$request->input('conf_desig'),
                        'is_letter_issue'=>$request->input('letter_issue_co'),
                        'apt_letter'=>$pdf_file,
                        'update_reason'=>$request->input('update_reason_co'),
                        'updated_at' => $timestamp
                    ]);
                    if($conf_update==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Updated Confirmation Letter.');      
                    }
                }
                if($request->input('cat_type')=="Fixed Term Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'fx_date'=>'required',
                        'fx_desig'=>'required',
                        'fx_per_date'=>'required',
                        'fx_date_six'=>'required',
                        'fx_sal'=>'required',
                        'update_reason_fx'=>'required',
                        'letter_issue_fx'=>'required',
                        'letter_upload_fx'=>'required_if:letter_issue_fx,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        'fx_date.required'=>'This field is required',
                        'fx_desig.required'=>'This field is required',
                        'fx_per_date.required'=>'This field is required',
                        'fx_date_six.required'=>'This field is required',
                        'fx_sal.required'=>'This field is required',
                        'update_reason_fx.required'=>'This field is required',
                        'letter_issue_fx.required'=>'This field is required',
                        'letter_upload_fx.mimes'=>'Document required and accept pdf',
                        'letter_upload_fx.max'=>'Document exceeded maxlength',
                        'letter_upload_fx.required_if'=>'Document required'

                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $file = $request->file('letter_upload_fx');
                    $pdf_file ='';    
                    if(!isset($file)||$file == null){
                        $pdf_file = $request->input('old_file_fx');
                    }else{
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_fx')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_fx')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                        File::delete($destinationPath.$request->input('old_file_fx'));
                    }
                    $fixed_update = EmployeeCategory::where('emp_id',$id)
                    ->where('emp_cat_id',$cat_type_id->id)
                    ->update([ 
                        'cat_date'=>date("Y-m-d", strtotime($request->input('fx_date'))),
                        'cat_designation'=>$request->input('fx_desig'),
                        'period_date'=>date("Y-m-d", strtotime($request->input('fx_per_date'))),
                        'p_six_date'=>date("Y-m-d", strtotime($request->input('fx_date_six'))),
                        'is_letter_issue'=>$request->input('letter_issue_fx'),
                        'stipend'=>$request->input('fx_sal'),
                        'apt_letter'=>$pdf_file,
                        'update_reason'=>$request->input('update_reason_fx'),
                        'updated_at' => $timestamp
                    ]);
                    if($fixed_update==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Updated Fixed Term Appointment Letter.');      
                    }
                }
            }
            else{
                if($request->input('cat_type')=="Offer Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'offer_date'=>'required',
                        'designation'=>'required',
                        'joining'=>'required',
                        'letter_issue_of'=>'required',
                        'letter_upload_of'=>'required_if:letter_issue_of,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        'offer_date.required'=>'This field is required',
                        'designation.required'=>'This field is required',
                        'joining.required'=>'This field is required',
                        'letter_issue_of.required'=>'This field is required',
                        'letter_upload_of.mimes'=>'Document required and accept pdf',
                        'letter_upload_of.max'=>'Document exceeded maxlength',
                        'letter_upload_of.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $pdf_file = '';
                    $file = $request->file('letter_upload_of');
                    if(isset($file) || $file != null){
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_of')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_of')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                    }else{
                        $pdf_file = '';
                    }
                    $offer_insert = DB::table('employee__category')->insertGetId([
                        'pk_id'=>NULL,
                        'emp_id' =>$id,
                        'emp_cat_id'=>$cat_type_id->id,
                        'cat_date'=>date("Y-m-d", strtotime($request->input('offer_date'))),
                        'cat_designation'=>$request->input('designation'),
                        'joining'=>date("Y-m-d", strtotime($request->input('joining'))),
                        'is_letter_issue'=>$request->input('letter_issue_of'),
                        'apt_letter'=>$pdf_file,
                        'created_by' => Auth::id(),
                        'created_at' => $timestamp
                    ]);
                    if($offer_insert==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Created Offer Letter.');      
                    }
                }
                if($request->input('cat_type')=="Trainee Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        't_app_date'=>'required',
                        'stipend'=>'required',
                        'letter_issue_tr'=>'required',
                        'letter_upload_tr'=>'required_if:letter_issue_tr,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        't_app_date.required'=>'This field is required',
                        'stipend.required'=>'This field is required',
                        'letter_issue_tr.required'=>'This field is required',
                        'letter_upload_tr.mimes'=>'Document required and accept pdf',
                        'letter_upload_tr.max'=>'Document exceeded maxlength',
                        'letter_upload_tr.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $pdf_file = '';
                    $file = $request->file('letter_upload_tr');
                    if(isset($file) || $file != null){
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_tr')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_tr')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                    }else{
                        $pdf_file = '';
                    }
                    $trainee_insert = DB::table('employee__category')->insertGetId([
                        'pk_id'=>NULL,
                        'emp_id' =>$id,
                        'emp_cat_id'=>$cat_type_id->id,
                        'cat_date'=>date("Y-m-d", strtotime($request->input('t_app_date'))),
                        'stipend'=>$request->input('stipend'),
                        'is_letter_issue'=>$request->input('letter_issue_tr'),
                        'apt_letter'=>$pdf_file,
                        'created_by' => Auth::id(),
                        'created_at' => $timestamp
                    ]);
                    if($trainee_insert==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Created Trainee Appointment Letter.');      
                    }
                }
                if($request->input('cat_type')=="Probation Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'prob_design'=>'required',
                        'prob_sal'=>'required',
                        'prob_date'=>'required',
                        'letter_issue_pr'=>'required',
                        'letter_upload_pr'=>'required_if:letter_issue_pr,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        'prob_design.required'=>'This field is required',
                        'prob_sal.required'=>'This field is required',
                        'prob_date.required'=>'This field is required',
                        'letter_issue_pr.required'=>'This field is required',
                        'letter_upload_pr.mimes'=>'Document required and accept pdf',
                        'letter_upload_pr.max'=>'Document exceeded maxlength',
                        'letter_upload_pr.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $pdf_file = '';
                    $file = $request->file('letter_upload_pr');
                    if(isset($file) || $file != null){
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_pr')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_pr')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                    }else{
                        $pdf_file = '';
                    }
                    $probation_insert = DB::table('employee__category')->insertGetId([
                        'pk_id'=>NULL,
                        'emp_id' =>$id,
                        'emp_cat_id'=>$cat_type_id->id,
                        'cat_date'=>date("Y-m-d", strtotime($request->input('prob_date'))),
                        'cat_designation'=>$request->input('prob_design'),
                        'stipend'=>$request->input('prob_sal'),
                        'is_letter_issue'=>$request->input('letter_issue_pr'),
                        'apt_letter'=>$pdf_file,
                        'created_by' => Auth::id(),
                        'created_at' => $timestamp
                    ]);
                    if($probation_insert==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Created Probation Appointment Letter.');      
                    }
                }
                if($request->input('cat_type')=="Confirmation Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'conf_desig'=>'required',
                        'conf_date'=>'required',
                        'letter_issue_co'=>'required',
                        'letter_upload_co'=>'required_if:letter_issue_co,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        'conf_desig.required'=>'This field is required',
                        'conf_date.required'=>'This field is required',
                        'letter_issue_co.required'=>'This field is required',
                        'letter_upload_co.mimes'=>'Document required and accept pdf',
                        'letter_upload_co.max'=>'Document exceeded maxlength',
                        'letter_upload_co.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $pdf_file = '';
                    $file = $request->file('letter_upload_co');
                    if(isset($file) || $file != null){
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_co')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_co')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                    }else{
                        $pdf_file = '';
                    }
                    $conf_insert = DB::table('employee__category')->insertGetId([
                        'pk_id'=>NULL,
                        'emp_id' =>$id,
                        'emp_cat_id'=>$cat_type_id->id,
                        'cat_date'=>date("Y-m-d", strtotime($request->input('conf_date'))),
                        'cat_designation'=>$request->input('conf_desig'),
                        'is_letter_issue'=>$request->input('letter_issue_co'),
                        'apt_letter'=>$pdf_file,
                        'created_by' => Auth::id(),
                        'created_at' => $timestamp 
                    ]);
                    if($conf_insert==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Created Confirmation Letter.');      
                    }
                }
                if($request->input('cat_type')=="Fixed Term Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'fx_date'=>'required',
                        'fx_desig'=>'required',
                        'fx_sal'=>'required',
                        'fx_per_date'=>'required',
                        'fx_date_six'=>'required',
                        'letter_issue_fx'=>'required',
                        'letter_upload_fx'=>'required_if:letter_issue_fx,1|mimes:pdf|max:'.CustomHelpers::getfilesize()
                    ];
                    $validmsgarr =[
                        'cat_type.required'=>'This field is required',
                        'fx_date.required'=>'This field is required',
                        'fx_desig.required'=>'This field is required',
                        'fx_per_date.required'=>'This field is required',
                        'fx_date_six.required'=>'This field is required',
                        'fx_sal.required'=>'This field is required',
                        'letter_issue_fx.required'=>'This field is required',
                        'letter_upload_fx.mimes'=>'Document required and accept pdf',
                        'letter_upload_fx.max'=>'Document exceeded maxlength',
                        'letter_upload_fx.required_if'=>'Document required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);
                    $pdf_file = '';
                    $file = $request->file('letter_upload_fx');
                    if(isset($file) || $file != null){
                        $destinationPath = public_path().'/upload/appointment_letter/';
                        $filenameWithExt = $request->file('letter_upload_fx')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('letter_upload_fx')->getClientOriginalExtension();
                        $pdf_file = $filename.'_'.time().'.'.$extension;
                        $path = $file->move($destinationPath, $pdf_file);
                    }else{
                        $pdf_file = '';
                    }
                    $fixed_insert = DB::table('employee__category')->insertGetId([
                        'pk_id'=>NULL,
                        'emp_id' =>$id,
                        'emp_cat_id'=>$cat_type_id->id,
                        'cat_date'=>date("Y-m-d", strtotime($request->input('fx_date'))),
                        'cat_designation'=>$request->input('fx_desig'),
                        'period_date'=>date("Y-m-d", strtotime($request->input('fx_per_date'))),
                        'p_six_date'=>date("Y-m-d", strtotime($request->input('fx_date_six'))),
                        'stipend'=>$request->input('fx_sal'),
                        'is_letter_issue'=>$request->input('letter_issue_fx'),
                        'apt_letter'=>$pdf_file,
                        'created_by' => Auth::id(),
                        'created_at' => $timestamp 
                    ]);
                    if($fixed_insert==NULL){
                        DB::rollback();
                        return redirect('/employee/category/update/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/category/update/'.$id)->with('success','Successfully Created Fixed Term Appointment Letter.');      
                    }
                }
            }
            
        }catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/category/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
    }

    public function appointment_format()
    {
        $content_check =EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
        ->select('employee__appointment_format.content','employee__category_master.name')->get()->toArray();
        
        $content = array();
        if($content_check){
            foreach($content_check as $con){
                $content[$con['name']]=$con;
            }
        }
        $data = array(
            'layout' => 'layouts.main',
            'let_cont'=>$content
        );
        return view('employee.emp_appointment_frmt',$data);
    }
    public function appointment_format_DB(Request $request)
    {
        try{
            
            $timestamp = date('Y-m-d G:i:s');
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$request->input('cat_type'))->first("id");
            $check_id = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->toArray();
            
            if($check_id){

                if($request->input('cat_type')=="Offer Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_of'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_of.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_update = EmployeeAppoint::where('letter_type',$cat_type_id->id)
                        ->update([
                            'content' =>$request->input('let_content_of'),
                            'updated_at'=>$timestamp
                        ]);
                        
                        if($content_update==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Offer Letter.');      
                        }
                }
                if($request->input('cat_type')=="Trainee Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_tr'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_tr.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_update = EmployeeAppoint::where('letter_type',$cat_type_id->id)
                        ->update([
                            'content' =>$request->input('let_content_tr'),
                            'updated_at'=>$timestamp
                        ]);
                        
                        if($content_update==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Trainee Appointment Letter.');      
                        }
                }
                if($request->input('cat_type')=="Probation Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_pr'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_pr.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_update = EmployeeAppoint::where('letter_type',$cat_type_id->id)
                        ->update([
                            'content' =>$request->input('let_content_pr'),
                            'updated_at'=>$timestamp
                        ]);
                        
                        if($content_update==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Probation Appointment Letter.');      
                        }
                }
                if($request->input('cat_type')=="Confirmation Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_co'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_co.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_update = EmployeeAppoint::where('letter_type',$cat_type_id->id)
                        ->update([
                            'content' =>$request->input('let_content_co'),
                            'updated_at'=>$timestamp
                        ]);
                        
                        if($content_update==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Confirmation Letter.');      
                        }
                }
                if($request->input('cat_type')=="Fixed Term Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_fx'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_fx.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_update = EmployeeAppoint::where('letter_type',$cat_type_id->id)
                        ->update([
                            'content' =>$request->input('let_content_fx'),
                            'updated_at'=>$timestamp
                        ]);
                        
                        if($content_update==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Fixed Term Appointment Letter.');      
                        }
                }
                if($request->input('cat_type')=="Employee Relieving Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_rl'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_rl.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_update = EmployeeAppoint::where('letter_type',$cat_type_id->id)
                        ->update([
                            'content' =>$request->input('let_content_rl'),
                            'updated_at'=>$timestamp
                        ]);
                        
                        if($content_update==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Employee Relieving Letter.');      
                        }
                }
                if($request->input('cat_type')=="Hindi No Dues Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_ndh'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_ndh.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_update = EmployeeAppoint::where('letter_type',$cat_type_id->id)
                        ->update([
                            'content' =>$request->input('let_content_ndh'),
                            'updated_at'=>$timestamp
                        ]);
                        
                        if($content_update==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Hindi No Dues Letter.');      
                        }
                }
                if($request->input('cat_type')=="English No Dues Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_nde'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_nde.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_update = EmployeeAppoint::where('letter_type',$cat_type_id->id)
                        ->update([
                            'content' =>$request->input('let_content_nde'),
                            'updated_at'=>$timestamp
                        ]);
                        
                        if($content_update==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated English No Dues Letter.');      
                        }
                }
            }else{
                if($request->input('cat_type')=="Offer Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_of'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_of.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_insert_of = EmployeeAppoint::insertGetId([
                            'id'=>NULL,
                            'letter_type'=>$cat_type_id->id,
                            'content' =>$request->input('let_content_of'),
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
                        ]);
                        
                        if($content_insert_of==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Inserted Offer Letter.');      
                        }
                }
                if($request->input('cat_type')=="Trainee Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_tr'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_tr.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_insert_tr = EmployeeAppoint::insertGetId([
                            'id'=>NULL,
                            'letter_type'=>$cat_type_id->id,
                            'content' =>$request->input('let_content_tr'),
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
                        ]);
                        
                        if($content_insert_tr==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Inserted Trainee Appointment Letter.');      
                        }
                }
                if($request->input('cat_type')=="Probation Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_pr'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_pr.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_insert_pr = EmployeeAppoint::insertGetId([
                            'id'=>NULL,
                            'letter_type'=>$cat_type_id->id,
                            'content' =>$request->input('let_content_pr'),
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
                        ]);
                        
                        if($content_insert_pr==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Inserted Probation Appointment Letter.');      
                        }
                }
                if($request->input('cat_type')=="Confirmation Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_co'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_co.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_insert_co = EmployeeAppoint::insertGetId([
                            'id'=>NULL,
                            'letter_type'=>$cat_type_id->id,
                            'content' =>$request->input('let_content_co'),
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
                        ]);
                        
                        if($content_insert_co==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Inserted Confirmation Letter.');      
                        }
                }
                if($request->input('cat_type')=="Fixed Term Appointment Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_fx'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_fx.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_insert_fx = EmployeeAppoint::insertGetId([
                            'id'=>NULL,
                            'letter_type'=>$cat_type_id->id,
                            'content' =>$request->input('let_content_fx'),
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
                        ]);
                        
                        if($content_insert_fx==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully inserted Fixed Term Appointment Letter.');      
                        }
                }
                if($request->input('cat_type')=="Employee Relieving Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_rl'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_rl.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_insert_rl = EmployeeAppoint::insertGetId([
                            'id'=>NULL,
                            'letter_type'=>$cat_type_id->id,
                            'content' =>$request->input('let_content_rl'),
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
                        ]);
                        
                        if($content_insert_rl==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Employee Relieving Letter.');      
                        }
                }
                if($request->input('cat_type')=="Hindi No Dues Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_ndh'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_ndh.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_insert_ndh = EmployeeAppoint::insertGetId([
                            'id'=>NULL,
                            'letter_type'=>$cat_type_id->id,
                            'content' =>$request->input('let_content_ndh'),
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
                        ]);
                        
                        if($content_insert_ndh==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated Hindi No Dues Letter.');      
                        }
                }
                if($request->input('cat_type')=="English No Dues Letter"){
                    $validerrarr =[
                        'cat_type'=>'required',
                        'let_content_nde'=>'required'
                        ];
                        $validmsgarr =[
                            'cat_type.required'=>'This field is required',
                            'let_content_nde.required'=>'This field is required'
                        ];
                        $this->validate($request,$validerrarr,$validmsgarr);
                        $content_insert_nde = EmployeeAppoint::insertGetId([
                            'id'=>NULL,
                            'letter_type'=>$cat_type_id->id,
                            'content' =>$request->input('let_content_nde'),
                            'created_by'=>Auth::id(),
                            'created_at'=>$timestamp
                        ]);
                        
                        if($content_insert_nde==NULL){
                            DB::rollback();
                            return redirect('/employee/appointment/format/')->with('error','Some Unexpected Error occurred.');
                        }
                        else{  
                            return redirect('/employee/appointment/format/')->with('success','Successfully Updated English No Dues Letter.');      
                        }
                }
            }
        }catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/appointment/format/')->with('error','some error occurred'.$ex->getMessage());
        }  
    }
    public function update_document($id) {

        $document = EmployeeDocument::where('emp_id',$id)->get();
        if($document)
        {
            $document = $document->toArray();
        }
        $doc_data =array();
        foreach ($document as $key => $value) {
            $doc_data[$value['document_name']] = $value;
        }
        $data = array(
            'document'=>$doc_data,
            'id'=>$id,
            'layout' => 'layouts.main'
        );
        return view('employee.update_document',$data);
    }

    public function update_documentDB(Request $request, $id)
     {
         //print_r($request->hasFile('aadhar_file'));die;
       // DB::enableQueryLog();
        try {
             $this->validate($request, [
                'file.*'      => 'mimes:jpeg,png,jpg,pdf|max:'.CustomHelpers::getfilesize(),
                ],[
                'file.*.mimes'      => 'Document required only jpeg,png,jpg,pdf format',
                'file.*.max'      => 'Document exceeded maxSize ',
                ]);
       
           if ($request->file('file')) {
               $files = $request->file('file');
               foreach ($files as $key=>$value) {
                    $file = $value;
                    $destinationPath = public_path().'/upload/employee/'.$id.'/document/';     
                    $filenameWithExt = $value->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);   
                    $extension = $value->getClientOriginalExtension();
                    $name = $filename.'_'.time().'.'.$extension;  
                    $path = $file->move($destinationPath, $name);
                    $document = EmployeeDocument::where('emp_id',$id)->where('document_name',$key)->first();
                   if (!empty($document)) { 
                    File::delete($destinationPath.$document['document_file']);  
                       $doc = EmployeeDocument::where('emp_id',$id)->where('document_name',$key)->update([
                            'document_file' => $name,
                            'created_by' => Auth::id(),
                            
                       ]);
                       
                    }else{
                        
                        $doc = EmployeeDocument::insertGetId([
                            'emp_id' => $id,
                            'document_name' => $key,
                            'document_file' => $name,
                            'created_by' => Auth::id(),
                            
                       ]);
                    }
                     if($doc == NULL){
                    //DB::rollback();
                        return redirect('/employee/document/update/'.$id)->with('error','Some Unexpected Error occurred.');  
                    }
                }
                CustomHelpers::userActionLog($request->input()['update_reason'],$id,"Employee Document Updated");
                return redirect('/employee/document/update/'.$id)->with("success","Employee Document Updated Successfully .");
            }else{ 
                return redirect('/employee/document/update/'.$id)->with('error','Please select atleast one document.');
            }  
         
        } 
        catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/document/update/'.$id)->with('error','some error occurred'.$ex->getMessage());
        } 
    }
    public function bday_anniversaryList(){
        $data = array(
            'layout' => 'layouts.main'
        );
        return view('employee.emp_bday',$data);        
    }
    public function bday_List_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $bd_month = $request->input('bdaymonth');
        
        $userlog = EmployeeProfile::leftJoin('department','department.id','employee__profile.department_id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select('employee__profile.id',
        'employee__profile.name',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        'employee__profile.designation',
        'department.department',DB::raw("DATE_FORMAT(employee__profile.dob, '%d-%b-%Y') as dob"));
        if(!empty($bd_month))
        { $userlog->where(function($query) use ($bd_month){
                $query->whereRaw('DATE_FORMAT(dob, "%m") = "'.$bd_month.'"');
        });        
        }else{
             $userlog->where(function($query){
                $query->whereRaw('MONTH(dob) = MONTH(NOW())');
            }); 
        }
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%");
                    }); 
        }
       
        $count = count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'department.department','dob',];
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
    public function anniversary_List_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $an_month = $request->input('annimonth');
        
        $userlog = EmployeeProfile::leftJoin('department','department.id','employee__profile.department_id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select('employee__profile.id',
        'employee__profile.name',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        'employee__profile.designation',
        'department.department',DB::raw("DATE_FORMAT(employee__profile.doj, '%d-%b-%Y') as doj"));
        if(!empty($an_month))
        { $userlog->where(function($query) use ($an_month){
                $query->whereRaw('DATE_FORMAT(doj, "%m") = "'.$an_month.'"');
        });        
        }else{
             $userlog->where(function($query){
                $query->whereRaw('MONTH(doj) = MONTH(NOW())');
            }); 
        }
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%");
                    }); 
        }
       
        $count = count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'department.department','doj'];
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
    public function employee_compl_oneyear(){
         $data = array(
            'layout' => 'layouts.main'
        );
        return view('employee.oneyear_emp',$data);
    }
    public function employee_compl_oneyear_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $from = $request->input('from');
        $to = $request->input('to');
        
        
        $userlog = EmployeeProfile::leftJoin('department','department.id','employee__profile.department_id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select('employee__profile.id',
        'employee__profile.name',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        'employee__profile.designation',
        'department.department',DB::raw("DATE_FORMAT(employee__profile.doj, '%d-%b-%Y') as doj"),
         DB::raw('TIMESTAMPDIFF( YEAR, employee__profile.doj, now() ) as year'),
        DB::raw('TIMESTAMPDIFF( MONTH, employee__profile.doj, now() ) % 12 as month'))
        // ->get()
        ;
        // DB::enableQueryLog();
        // print_r( DB::getQueryLog());die(); 
        // && empty($year2) && empty($month2)
        if($to==0 && $from>=0){
            $userlog->where(function($query) use ($from){
                $query->whereRaw('TIMESTAMPDIFF( MONTH, employee__profile.doj, now() ) >='.$from.'');   
            });
        }else if($to>0 && $from==0){
            $userlog->where(function($query) use ($from,$to){
                $query->whereRaw('TIMESTAMPDIFF( MONTH, employee__profile.doj, now() ) <='.$to.'');   
            });
        }else if($to>0 && $from >0)
        { 
            $userlog->where(function($query) use ($from,$to){
                $query->whereRaw('TIMESTAMPDIFF( MONTH, employee__profile.doj, now() ) BETWEEN '.$from.' and '.$to.'');   
            });        
        }
        
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%");
                    }); 
        }
       
        $count = count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'department.department','doj'];
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
    public function emp_compl_sixmonth(){
         $data = array(
            'layout' => 'layouts.main'
        );
        return view('employee.sixmonth_emp',$data);
    }
    public function emp_compl_sixmonth_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = EmployeeProfile::leftJoin('department','department.id','employee__profile.department_id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->where(DB::raw('TIMESTAMPDIFF( YEAR, employee__profile.doj, now() )'),'=',0)
        ->where(DB::raw('TIMESTAMPDIFF( MONTH, employee__profile.doj, now() ) % 12'),'=',6)
        ->select('employee__profile.id',
        'employee__profile.name',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        'employee__profile.designation',
        'department.department',DB::raw("DATE_FORMAT(employee__profile.doj, '%d-%b-%Y') as doj"),
        DB::raw('TIMESTAMPDIFF( YEAR, employee__profile.doj, now() ) as year'),
         DB::raw('TIMESTAMPDIFF( MONTH, employee__profile.doj, now() ) % 12 as month'),
         DB::raw('ROUND(TIMESTAMPDIFF( DAY, employee__profile.doj, now() ) % 30.4375) as day'));
        
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%");
                    }); 
        }
       
        $count = count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'department.department','doj'];
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
    //view employee
    public function view_employee($id){
        $emp=EmployeeProfile::where('employee__profile.id',$id)
        ->leftJoin('department','department.id','employee__profile.department_id')
        ->leftJoin('employee__bank','employee__bank.emp_id','employee__profile.id')
        ->leftJoin('users','employee__profile.reporting','users.id')
        ->select('employee__profile.*','department.department as dept_name','employee__bank.*','users.name as reportingH')
        ->get()->first();

        $pfesi=EmployeePFESI::where('emp_id',$id)->get()->first();

        $cat=EmployeeCategory::where('emp_id',$id)->leftJoin('employee__category_master','employee__category_master.id','employee__category.emp_cat_id')
        ->select('employee__category.*','employee__category_master.name as cat')->get();

        $emp_pic=EmployeeDocument::where('emp_id',$id)->where('document_name','photo')->get()->first();

        $doc=EmployeeDocument::where('emp_id',$id)->get();
        $nodues =EmployeeNodues::where('emp_id',$id)->get();
        $relieve = EmployeeRelieving::where('emp_id',$id)->get()->first();
        $asset= Assets::whereIn('asset_id',explode(',',$relieve['leaving_assets']))->select('name')->get()->toArray();
        $len_asset= count($asset);
        $str_asset =array();
        foreach ($asset as $key => $value) {
            $str_asset[$key]= $value['name'];
        }
        // print_r($relieve);die();
        // return $pfesi;
        $data = array(
            'layout' => 'layouts.main',
            'emp'=>$emp,
            'pfesi'=>$pfesi,
            'cat'=>$cat,
            'doc'=>$doc,
            'emp_pic'=>$emp_pic,
            'relieve'=>$relieve,
            'nodues'=> $nodues,
            'asset'=>implode(',', $str_asset) 
        );
        return view('employee.view_employee',$data);
    }

    public function not_pfesi_report(){
        
        $data=array('layout' => 'layouts.main');
        return view('employee.pfesi_report',$data);
    }
    public function not_pfesi_report_api(Request $request){

        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $notpf_emp= EmployeeProfile::whereNull('employee__pfesi.emp_id')
        ->leftJoin('employee__pfesi','employee__profile.id','employee__pfesi.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select(
            'employee__profile.employee_number',
            'employee__profile.name',
            'employee__profile.mobile',
            DB::raw('DATE_FORMAT(employee__profile.doj,"%d-%m-%Y") as doj'),
            'employee__profile.designation',
            
            'employee__pfesi.emp_id',
            'employee__pfesi.pf',
            'employee__pfesi.esi',
            'department.department'
        )
            ;

        if(!empty($serach_value)){
             $notpf_emp = $notpf_emp->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('doj','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department.department','LIKE',"%".$serach_value."%")
                        ;
        }
        $count = $notpf_emp->count();
        $notpf_emp = $notpf_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id','mobile', 'employee_number','name','doj','designation','department'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $notpf_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $notpf_emp->orderBy('employee__profile.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $notpf_emp->get(); 
        return json_encode($array);

    }
    public function in_pf_report(){
        
        $data=array('layout' => 'layouts.main');
        return view('employee.pf_report',$data);
    }
    public function in_pf_report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $notpf_emp= EmployeeProfile::whereNotNull('employee__pfesi.emp_id')
        ->whereNotNull('employee__pfesi.pf')
        ->leftJoin('employee__pfesi','employee__profile.id','employee__pfesi.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select(
            'employee__profile.employee_number',
            'employee__profile.name',
            'employee__profile.mobile',
            DB::raw('DATE_FORMAT(employee__profile.doj,"%d-%m-%Y") as doj'),
            'employee__profile.designation',
            
            'employee__pfesi.emp_id',
            'employee__pfesi.pf',
            'employee__pfesi.pf_no',
            DB::raw('DATE_FORMAT(employee__pfesi.enroll_date_pf,"%d-%m-%Y") as pf_date'),
            'department.department'
        )
        ;
        
        if(!empty($serach_value)){

             $notpf_emp =  $notpf_emp->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('doj','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department.department','LIKE',"%".$serach_value."%")
                        ;
                });
            }
        $count = $notpf_emp->count();
        $notpf_emp = $notpf_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id','mobile', 'employee_number','name','doj','designation','department','pf_no','pf_date'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $notpf_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $notpf_emp->orderBy('employee__profile.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $notpf_emp->get(); 
        return json_encode($array);

    }
    public function in_esi_report(){
        
        $data=array('layout' => 'layouts.main');
        return view('employee.esi_report',$data);
    }
    public function in_esi_report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $notesi_emp= EmployeeProfile::whereNotNull('employee__pfesi.emp_id')
        ->whereNotNull('employee__pfesi.esi')
        ->leftJoin('employee__pfesi','employee__profile.id','employee__pfesi.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select(
            'employee__profile.employee_number',
            'employee__profile.name',
            'employee__profile.mobile',
            DB::raw('DATE_FORMAT(employee__profile.doj,"%d-%m-%Y") as doj'),
            'employee__profile.designation',
            
            'employee__pfesi.emp_id',
            'employee__pfesi.esi',
            DB::raw('DATE_FORMAT(employee__pfesi.enroll_date_esi,"%d-%m-%Y") as esi_date'),
            'employee__pfesi.esi_no',
            'department.department'
        )
            ;

        if(!empty($serach_value)){
            $notesi_emp =  $notesi_emp->where(function($query) use ($serach_value){
                    $query->where('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.doj','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department.department','LIKE',"%".$serach_value."%")
                        ;
                });
        }
        $count = $notesi_emp->count();
        $notesi_emp = $notesi_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id','mobile', 'employee_number','name','doj','designation','department','esi_no','esi_date'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $notesi_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $notesi_emp->orderBy('employee__profile.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $notesi_emp->get(); 
        return json_encode($array);
    }
    public function not_pf_report(){
        
        $data=array('layout' => 'layouts.main');
        return view('employee.not_pf_report',$data);
    }
    public function not_pf_report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $notpf_emp= EmployeeProfile::whereNotNull('employee__pfesi.emp_id')
        ->whereNull('employee__pfesi.pf')
        ->leftJoin('employee__pfesi','employee__profile.id','employee__pfesi.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select(
            'employee__profile.employee_number',
            'employee__profile.name',
            'employee__pfesi.leave_date_pf',
            'employee__profile.mobile',
            DB::raw('DATE_FORMAT(employee__profile.doj,"%d-%m-%Y") as doj'),
            'employee__profile.designation',
            
            'employee__pfesi.emp_id',
            'employee__pfesi.pf',
            'employee__pfesi.pf_no',
            DB::raw('DATE_FORMAT(employee__pfesi.enroll_date_pf,"%d-%m-%Y") as pf_date'),
            'department.department'
        )
        ;
        
        if(!empty($serach_value)){

             $notpf_emp =  $notpf_emp->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('doj','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department.department','LIKE',"%".$serach_value."%")
                        ;
                });
            }
        $count = $notpf_emp->count();
        $notpf_emp = $notpf_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id','mobile', 'employee_number','name','doj','designation','department','pf_no','pf_date'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $notpf_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $notpf_emp->orderBy('employee__profile.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $notpf_emp->get(); 
        return json_encode($array);

    }
    public function not_esi_report(){
        
        $data=array('layout' => 'layouts.main');
        return view('employee.not_esi_report',$data);
    }
    public function not_esi_report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $notesi_emp= EmployeeProfile::whereNotNull('employee__pfesi.emp_id')
        ->whereNull('employee__pfesi.esi')
        ->leftJoin('employee__pfesi','employee__profile.id','employee__pfesi.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select(
            'employee__profile.employee_number',
            'employee__profile.name',
            'employee__profile.mobile',
            'employee__pfesi.leave_date_esi',
            DB::raw('DATE_FORMAT(employee__profile.doj,"%d-%m-%Y") as doj'),
            'employee__profile.designation',
            
            'employee__pfesi.emp_id',
            'employee__pfesi.esi',
            DB::raw('DATE_FORMAT(employee__pfesi.enroll_date_esi,"%d-%m-%Y") as esi_date'),
            'employee__pfesi.esi_no',
            'department.department'
        )
            ;

        if(!empty($serach_value)){
            $notesi_emp =  $notesi_emp->where(function($query) use ($serach_value){
                    $query->where('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.doj','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department.department','LIKE',"%".$serach_value."%")
                        ;
                });
        }
        $count = $notesi_emp->count();
        $notesi_emp = $notesi_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id','mobile', 'employee_number','name','doj','designation','department','esi_no','esi_date'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $notesi_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $notesi_emp->orderBy('employee__profile.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $notesi_emp->get(); 
        return json_encode($array);
    }
    public function nodues_print(){
        $data=array('layout' => 'layouts.main');
        return view('employee.nodues_print',$data);
    }
    public function nodues_upload(){
        $employee =EmployeeProfile::all();
        $data=array('layout' => 'layouts.main',
            'employee'=>$employee);
        return view('employee.nodues_upload',$data);
    }
    public function nodues_upload_db(Request $request){
        try {
            $this->validate($request,
                [
                'empName'=>'required',
                'letter_upload_pr'=>'required'
                ],[
                    'empName.required'=>'Employee is required',
                    'letter_upload_pr.required'=>'Employee is required'
                ]);

                $file = $request->file('letter_upload_pr');
                // if(isset($file) || $file != null){
                    $destinationPath = public_path().'/upload/nodues_form/';
                    $filenameWithExt = $request->file('letter_upload_pr')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('letter_upload_pr')->getClientOriginalExtension();
                    $pdf_file = $filename.'_'.time().'.'.$extension;
                    $path = $file->move($destinationPath, $pdf_file);
                // }else{
                //     $pdf_file = '';
                // }

                 $timestamp = date('Y-m-d G:i:s');
                    $nodue = EmployeeNodues::insertGetId([
                        'id'=>NULL,
                        'emp_id'=>$request->input('empName'),
                        'format'=>$pdf_file,
                        'upload_start_date'=> date("Y-m-d",strtotime($request->input('letter_start_date'))),
                        'created_by'=>Auth::id(),
                        'created_at'=>$timestamp
                    ]);
                    
                    if($nodue==NULL) 
                    {
                       DB::rollback();
                        return redirect('/employee/nodues/upload')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                           
                        return redirect('/employee/nodues/upload')->with('success','No Dues Uploaded Successfully.');
                    }
 
        } catch (\Illuminate\Database\QueryException $ex) {
             return redirect('/employee/nodues/upload')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function employee_category_report(){
        $data=array('layout' => 'layouts.main');
        return view('employee.em_category_report',$data);
    }
    public function empl_category_report_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
       
        $notesi_emp= EmployeeCategory::leftjoin('employee__profile','employee__profile.id','employee__category.emp_id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.emp_id',null)
        ->select('employee__profile.employee_number',
            'employee__profile.name as emp_name',
            'employee__category.emp_id','employee__profile.designation',
            'employee__category.emp_cat_id',
            DB::raw('GROUP_CONCAT(employee__category_master.name) as category_name'),
            DB::raw('GROUP_CONCAT(Concat(employee__category_master.name,":","Cat_date",":",DATE_FORMAT(employee__category.cat_date,"%d-%m-%Y"),":","tenure",":",ifnull(DATE_FORMAT(employee__category.period_date,"%d-%m-%Y"),0),":","jndate:",ifnull(DATE_FORMAT(employee__category.joining,"%d-%m-%Y"),0))) as list'),
            DB::raw('GROUP_CONCAT(employee__category.cat_designation) as cat_designation'),
            DB::raw('GROUP_CONCAT(DATE_FORMAT(employee__category.period_date,"%d-%m-%Y")) as period_date'),
            DB::raw('GROUP_CONCAT(DATE_FORMAT(employee__category.p_six_date,"%d-%m-%Y")) as p_six_date'))
        ->leftJoin('employee__category_master',function($join){
            $join->on(DB::raw("find_in_set(employee__category_master.id,employee__category.emp_cat_id)"),'>',DB::raw("0"));
        })->groupby('employee__category.emp_id');
            
        if(!empty($serach_value)){
            $notesi_emp =  $notesi_emp->where(function($query) use ($serach_value){
                    $query->where('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__category_master.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.designation','LIKE',"%".$serach_value."%")
                        ;
                });
        }
        $count = $notesi_emp->count();
        $notesi_emp = $notesi_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__category.emp_id','employee_number', 'emp_name','category_name','designation','category_name','list','list','list','list','list'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $notesi_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $notesi_emp->orderBy('employee__category.emp_id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $notesi_emp->get(); 
        return json_encode($array);
        
    }

    public function employee_fnf_settlement() {
        $data = [
            'layout' => 'layouts.main'
        ];
        return view('employee.employee_fnf_settlement',$data);
    }

     public function employee_fnf_settlement_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = EmployeeFnF::leftJoin('employee__profile','employee_fnf.emp_id','employee__profile.id')
        ->leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->select('employee__profile.id',
        DB::raw('Concat(employee__profile.name,"-",employee__profile.employee_number) as emp_name'),
        'employee__profile.doj',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        DB::raw('DATE_FORMAT(employee__relieving.leaving_date,"%d-%m-%Y") as leaving_date'),
        'employee_fnf.total_amount',
        'employee__profile.designation');

        if(!empty($serach_value))
        {
            $userlog = $userlog->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('mobile','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('total_amount','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'employee_fnf.total_amount'];
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

    public function employee_fnf_settlement_details($id){
       $fnfData = EmployeeFnF::where('emp_id',$id)->get()->first();
       $data = [
            'details' => $fnfData
       ];
       return view('employee.fnf_detail',$data);
    }
}

