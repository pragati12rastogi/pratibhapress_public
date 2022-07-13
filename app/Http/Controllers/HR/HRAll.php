<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Employee\Assets;
use App\Model\Employee\EmployeeProfile;
use App\Model\Employee\EmployeePFESI;
use App\Model\HR\HR_Leave;
use App\Model\HR\HR_Recruitment;
use App\Model\HR\HR_Recruit_Assess;
use App\Model\HR\Announcements;
use App\Model\HR\HR_Events;
use App\Model\Employee\EmployeeIncrement;
use App\Model\Employee\EmployeeSalary;
use App\Model\Employee\NetSalary;
use App\Model\Employee\IncrementDA;
use App\Model\Employee\Advance;
use App\Model\Employee\PaidAdvance;
use App\Model\HR\HR_LeaveDetails;
use App\Model\Holiday;
use App\Model\Month;
use App\Model\Settings;
use App\Model\Department;
use App\Model\Users;
use App\Model\Att_Enhansement;
use App\Model\Employee\Attendance;
use App\Model\Att_Balance;
use \Carbon\Carbon;
use Mail;
use Calendar;
use App\Event;
use App\Custom\CustomHelpers;
use App\Model\Employee\EmployeeAppoint;

use PDF;  
use Auth;
use File;
use DB;
use DateTime;

class HRAll extends Controller
{
    public function create_leave(){
        $emp=EmployeeProfile::select('id','name')->get();
        $data=[
            'layout' => 'layouts.main',
            'emp'=>$emp
        ];
        // return $user;
        return view('hr.leave_create',$data);
    }
    public function create_leaveDb(Request $request){
        try {
            $validerrarr =[
                'employee_name'=>'required',
                'email'=>'required',
                'contact'=>'required',
                'start_date'=>'required',
                'end_date'=>'required', 
                'reason'=>'required',
                'adjust_leave'=>'required'
            ];
            $validmsgarr =[
                'employee_name.required'=>'This field is required',
                'email.required'=>'This field is required',
                'contact.required'=>'This field is required',
                'start_date.required'=>'This field is required',
                'end_date.required'=>'This field is required',
                'reason.required'=>'This field is required',
                'adjust_leave.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            $d=date('Y',strtotime($request->input('start_date')));
    
            $bal=Att_Balance::where('emp_id',$request->input('employee_name'))
            ->where('year',$d)
            ->get()->first();
            $adjust=$request->input('adjust_leave');
            $start_date=date('Y-m-d',strtotime($request->input('start_date')));
            $end_date=date('Y-m-d',strtotime($request->input('end_date')));
            $diff = strtotime($end_date) - strtotime($start_date); 
            $diff=abs(round($diff / 86400)) + 1;
            if($request->input('adjust_leave')==1){
                if($bal==NULL){
                    return redirect('/hr/leave/create')->with('error','Leave Encashment For this employee for this year has not been filled.')->withInput();
                }
                // else if(($bal['balance_leave'])<=0){
                //     return redirect('/hr/leave/create')->with('error','Your leaves are not available')->withInput();
                // }
   
            }
        
          

           
          
            $hr=HR_Leave::insertGetId([
                'id'=>NULL,
                'employee'=>$request->input('employee_name'),
                'leave_apply_date'=>date('Y-m-d'),
                'email'=>$request->input('email'),
                'contact'=>$request->input('contact'),
                'start_date'=>date('Y-m-d',strtotime($request->input('start_date'))),
                'end_date'=>date('Y-m-d',strtotime($request->input('end_date'))),
                'reason'=>$request->input('reason'),
                'adjust_leave'=>$adjust,
                // 'adjust_leave'=>$request->input('to_adj'),
                'created_by'=>Auth::id(),

            ]);

            if($hr==NULL){
                return redirect('/hr/leave/create')->with('error','some error occurred')->withInput();
            }
            else{
            if($request->input('adjust_leave')==1){
                if($diff>$bal['balance_leave']){
                    $is_adjust_0=$diff-$bal['balance_leave'];
                    $is_adjust_1=$bal['balance_leave'];
                    $start_date=date('Y-m-d',strtotime($request->input('start_date')));
                    $end_date=date('Y-m-d',strtotime($request->input('end_date')));
                    $date=date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
                   for($i=0;$i<$is_adjust_1;$i++){
                    $date=date('Y-m-d', strtotime('+1 day', strtotime($date)));
                        HR_LeaveDetails::insertGetId([
                            'id'=>NULL,
                            'emp_id'=>$request->input('employee_name'),
                            'leave_id'=>$hr,
                            'date'=>$date,
                            'is_adjusted'=>1
                        ]);
                   }
                   for($i=0;$i<$is_adjust_0;$i++){
                    $date=date('Y-m-d', strtotime('+1 day', strtotime($date)));
                        HR_LeaveDetails::insertGetId([
                            'id'=>NULL,
                            'emp_id'=>$request->input('employee_name'),
                            'leave_id'=>$hr,
                            'date'=>$date,
                            'is_adjusted'=>0
                        ]);
                   }
                }
                else{
                    $begin = new DateTime($start_date);
                    $end = new DateTime($end_date);
                    for($i = $begin; $i <= $end; $i->modify('+1 day')){
                        HR_LeaveDetails::insertGetId([
                            'id'=>NULL,
                            'emp_id'=>$request->input('employee_name'),
                            'leave_id'=>$hr,
                            'date'=>$i->format("Y-m-d"),
                            'is_adjusted'=>1
                        ]);
                    }
                }
            }
            else{
                $begin = new DateTime($start_date);
                $end = new DateTime($end_date);
                for($i = $begin; $i <= $end; $i->modify('+1 day')){
                    HR_LeaveDetails::insertGetId([
                        'id'=>NULL,
                        'emp_id'=>$request->input('employee_name'),
                        'leave_id'=>$hr,
                        'date'=>$i->format("Y-m-d"),
                        'is_adjusted'=>0
                    ]);
                }
            }
                
                
                $format=HR_Leave::where('hr__leave.id',$hr)->leftJoin('employee__profile','employee__profile.id','hr__leave.employee')
                ->leftJoin('users as level1','level1.id','hr__leave.status_level1_by')
                ->leftJoin('users as level2','level2.id','hr__leave.status_level2_by')
                ->select(
                    'hr__leave.id',
                    'employee__profile.name as emp',
                    DB::raw('DATE_FORMAT(leave_apply_date,"%d/%m/%Y") as leave_d'),
                    // 'leave_apply_date',
                    'hr__leave.email',
                    'contact',
                    DB::raw('DATE_FORMAT(start_date,"%d-%m-%Y") as start_date'),
                    DB::raw('DATE_FORMAT(end_date,"%d-%m-%Y") as end_date'),
                    'reason',
                    'status_level1',
                    'status_level2',
                    'remark',
                    DB::raw('DATE_FORMAT(status_level1_date,"%d-%m-%Y") as status_level1_date'),
                    DB::raw('DATE_FORMAT(status_level2_date,"%d-%m-%Y") as status_level2_date'),
                    'level1.name as level1',
                    'level2.name as level2',
                    DB::raw('(DATEDIFF(end_date,start_date)) as st_date')
          
                )->get()->first()
                ;
                
                
                // $bal=Att_Balance::where('emp_id',$request->input('employee_name'))
                // ->where('year',$d)
                // ->update(['balance_leave'=>DB::raw('balance_leave - '.$diff)]);
                $datas = [
                    'format' => $format
                    ];
                $pdfFilePath = "Leave Application.pdf";
                $pdf = PDF::loadView('hr.leave_pdf', $datas);
                    $reporting=EmployeeProfile::where('employee__profile.id',$request->input('employee_name'))
                    ->leftJoin('users','users.id','employee__profile.reporting')
                    ->select('users.email','employee__profile.name')->get()->first();

                    $jobdata = Settings::leftJoin('users as level1',function($join){
                        $join->on(DB::raw("find_in_set(level1.id,settings.value)"),'>',DB::raw("0"));
                        $join->where('settings.name','HR_Leave_Level1');
                    })
                    ->select(DB::raw('group_concat(level1.email) as email'))->get()->first();

                    $jobdata=explode(',',$jobdata['email']);
                    $report[0]=$reporting['email'];
                    $tos[0]=$request->input('email');
                    $emails=array_merge($jobdata,$report,$tos);
                    $emails=array_unique($emails);

                    // print_r($emails);die;

                    
                    $admin_email=$emails;
                    $name ="";
                    $to=$request->input('email');
                    
                    $data=[ 'start_date'=>$request->input('start_date'),
                            'end_date'=>$request->input('end_date'),
                            'reason'=>$request->input('reason'),
                            'employee'=>$reporting['name']];

                    foreach($admin_email as $key=>$value){
                       
                        Mail::send('email.leavetemplate', $data, function($message) use ($value, $name,$to,$pdf)
                        {
                            $message->to($value, $name)->subject('Leave Application')->from($to, 'Leave Application')->attachData($pdf->output(), "leave.pdf");
                        });
                    }
                    
                  
                    
                return redirect('/hr/leave/create')->with('success','Successfully Leave Applied.');
            }
        } 
        catch(\Illuminate\Database\QueryException $ex) 
        {
            return redirect('/hr/leave/create')->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }
    public function addleave(){
        $hr=HR_Leave::all();
        foreach($hr as $key){
            $begin = new DateTime($key['start_date']);
                    $end = new DateTime($key['end_date']);
                    for($i = $begin; $i <= $end; $i->modify('+1 day')){
                        HR_LeaveDetails::insertGetId([
                            'id'=>NULL,
                            'emp_id'=>$key['employee'],
                            'leave_id'=>$key['id'],
                            'date'=>$i->format("Y-m-d"),
                            'is_adjusted'=>$key['adjust_leave'],
                            'status'=>$key['status_level1']
                        ]);
                    }
        }
    }
    public function emp_shift(Request $request){
        $emp_id = $request->input('emp');
        $emp=EmployeeProfile::where('id',$emp_id)->get()->first();
        return response()->json($emp);
    }
    //----------------leave list-------------------------------------
    public function leave_list(){
        $hr1=Settings::where('name','HR_Leave_Level1')->select('value','name')->get()->first();
        $hr2=Settings::where('name','HR_Leave_Level2')->select('value','name')->get()->first();
        $hr=$hr1['value'].','.$hr2['value'];
        $data=[
            'layout' => 'layouts.main',
            'hr'=>$hr,
        ];
        // return $data;
        return view('hr.leave_summary',$data);
    }
    public function leave_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $jobdata = HR_Leave::leftJoin('employee__profile','employee__profile.id','hr__leave.employee')
        ->leftJoin('users as level1','level1.id','hr__leave.status_level1_by')
        ->leftJoin('users as level2','level2.id','hr__leave.status_level2_by')
        ->select(
            'hr__leave.id',
            'employee__profile.name as emp',
            DB::raw('DATE_FORMAT(leave_apply_date,"%d-%m-%Y") as leave_apply_date'),
            // 'leave_apply_date',
            'hr__leave.email',
            'contact',
            DB::raw('DATE_FORMAT(start_date,"%d-%m-%Y") as start_date'),
            DB::raw('DATE_FORMAT(end_date,"%d-%m-%Y") as end_date'),
            'reason',
            'status_level1',
            'status_level2',
            'level1.name as level1',
            'level2.name as level2',
            'employee__profile.reporting'

        )
        ;
        if(!empty($serach_value))
        {
            $jobdata->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
                ->orWhere('leave_apply_date','LIKE',"%".$serach_value."%")
                ->orWhere('hr__leave.email','LIKE',"%".$serach_value."%")
                ->orWhere('contact','LIKE',"%".$serach_value."%")
                ->orWhere('start_date','LIKE',"%".$serach_value."%")
                ->orWhere('end_date','LIKE',"%".$serach_value."%")
                ->orWhere('reason','LIKE',"%".$serach_value."%")
                ->orWhere('level1.name','LIKE',"%".$serach_value."%")
                ->orWhere('level2.name','LIKE',"%".$serach_value."%")
                ;
              });
     
        }
        
        if(isset($request->input('order')[0]['column']))
        {
            $data = [
            'hr__leave.id',
            'employee__profile.name',
            'leave_apply_date',
            'hr__leave.email',
            'contact',
            'start_date',
            'end_date',
            'reason',
            'status_level1',
            'status_level2',
            'level1.name',
            'level2.name'
                ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $jobdata->orderBy('hr__leave.id','desc');

        $count = count($jobdata->get()->toArray());
        $jobdata = $jobdata->offset($offset)->limit($limit);
        $jobdata=$jobdata->get();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $jobdata;
        return json_encode($array);
    }
    public function setting(){
        $emp=Users::select('id','name')->get();
        $hr1=Settings::where('name','HR_Leave_Level1')->select('value','name')->get()->first();

        $data=[
            'layout' => 'layouts.main',
            'emp'=>$emp,
            'hr1'=>$hr1,
        ];
        // return $user;
        return view('hr.setting',$data);
    }
    public function setting_Db(Request $request){
        try {
            // print_r($request->input());die;
            $validerrarr =[
                'name1.*'=>'required',
              //  'level1'=>'required',
               // 'name2.*'=>'required',
                //'level2'=>'required'
            ];
            $validmsgarr =[
                'name1.*.required'=>'This field is required',
               // 'level1.required'=>'This field is required',
                //'name2.*.required'=>'This field is required',
               // 'level2.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            // $x=array_intersect($request->input('name1'),$request->input('name2'));
              // $x=$request->input('name1');
            // if(count($x)!=0){
            //     return redirect('/hr/setting')->with('error','One User Cannot have both levels authority')->withInput();
            // }
                $hr1=Settings::where('name','HR_Leave_Level1')->update([
                    'value'=>implode(',',$request->input('name1')),
                ]);
                // $hr2=Settings::where('name','HR_Leave_Level2')->update([
                //     'value'=>implode(',',$request->input('name2')),
                // ]);
            if($hr1==NULL){
                return redirect('/hr/setting')->with('error','some error occurred');
            }
            else{
                return redirect('/hr/setting')->with('success','Successfully Level Auhority for Leaves Created.');
            }
        } 
        catch(\Illuminate\Database\QueryException $ex) 
        {
            return redirect('/hr/setting')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function leave_approve($id,Request $request){
        $hr1=Settings::where('name','HR_Leave_Level1')->select('value','name')->get()->first();
        // $hr2=Settings::where('name','HR_Leave_Level2')->select('value','name')->get()->first();
        $level1=explode(',',$hr1['value']);
        $status_level_2=$request->input('repoting');
        // $level2=explode(',',$hr2['value']);
        $hrs=HR_Leave::where('hr__leave.id',$request->input('leave_id'))
        ->leftJoin('employee__profile','employee__profile.id','hr__leave.employee')
        ->select('employee__profile.name','hr__leave.*', DB::raw('(DATEDIFF(end_date,start_date)) as st_date'))
        ->get()->first();
         if($request->input('status')){  
            $status=$request->input('status');    
            if($status=="Approved" && $hrs['adjust_leave']==1){
                $hr_de=HR_LeaveDetails::where('leave_id',$request->input('leave_id'))->update(
                    [
                        'status'=>$status
                    ]
                    );
                $hr_details=HR_LeaveDetails::where('leave_id',$request->input('leave_id'))
                ->where('is_adjusted',1)->select('id')->get();
                $cc=count($hr_details);
                // print_r($cc);die;
                $d=date('Y',strtotime($hrs['start_date']));
                $bal=Att_Balance::where('emp_id',$hrs['employee'])
                ->where('year',$d)
                ->get()->first();
                // print_r($hrs['st_date']);die;
                $dd=$hrs['st_date']+1;
                if($bal){
                    $bal=Att_Balance::where('emp_id',$hrs['employee'])
                    ->where('year',$d)
                    ->update(['balance_leave'=>DB::raw('balance_leave - '.$cc)]);
                }
               
            } 
           foreach ($level1 as $key => $value) {
            if($value==$id){
                $lev="Level1";
                $arr=[
                'status_level1'=>$request->input('status'),
                'status_level1_by'=>$id,
                'status_level1_date'=>date('Y-m-d'),
                'remark'=>$request->input('remark')

                ];
              } 
            }
            // print_r($arr);die;
            $by=Users::where('id',$id)->select('name','email')->get()->first();
            $data=[
                'employee'=>$hrs['name'],
                'leave_apply_date'=>$hrs['leave_apply_date'],
                'email'=>$hrs['email'],
                'start_date'=>$hrs['start_date'],
                'end_date'=>$hrs['end_date'],
                'reason'=>$hrs['reason'],
                'status'=>$request->input('status'),
                'by'=>$by['name']];
                $admin_email=$hrs['email'];
                $to=$by['email'];
                $name='';
            // Mail::send('email.leavestatus', $data, function($message) use ($admin_email, $name,$to)
            // {
            //     $message->to($admin_email, $name)->subject('Leave Application Status')->from($to, 'Leave Application Status');
            // });
             $hr=HR_Leave::where('id',$request->input('leave_id'))->update($arr);

         }
         elseif($request->input('status1'))
         {
             $status=$request->input('status1');
             if($status=="Rejected"){
                $hr_de=HR_LeaveDetails::where('leave_id',$request->input('leave_id'))->update(
                    [
                        'status'=>$status
                    ]
                    );
             }
            $arr=[
                'status_level2'=>$request->input('status1'),
                'status_level2_by'=>$request->input('repoting'),
                'status_level2_date'=>date('Y-m-d'),
                'remark'=>$request->input('remark')

                ];
                $by=Users::where('id',$request->input('repoting'))->select('name','email')->get()->first();
            $data=[
                'employee'=>$hrs['name'],
                'leave_apply_date'=>$hrs['leave_apply_date'],
                'email'=>$hrs['email'],
                'start_date'=>$hrs['start_date'],
                'end_date'=>$hrs['end_date'],
                'reason'=>$hrs['reason'],
                'status'=>$request->input('status'),
                'by'=>$by['name']];
                $admin_email=$hrs['email'];
                $to=$by['email'];
                $name='';
            // Mail::send('email.leavestatus', $data, function($message) use ($admin_email, $name,$to)
            // {
            //     $message->to($admin_email, $name)->subject('Leave Application Status')->from($to, 'Leave Application Status');
            // });
            $hr=HR_Leave::where('id',$request->input('leave_id'))->update($arr);
         }
     
          
            return redirect('hr/leave/list')->with('success','Leave '.$status.' Successfully');
    }
    public function leave_print($id){
      $format=HR_Leave::where('hr__leave.id',$id)->leftJoin('employee__profile','employee__profile.id','hr__leave.employee')
      ->leftJoin('users as level1','level1.id','hr__leave.status_level1_by')
      ->leftJoin('users as level2','level2.id','hr__leave.status_level2_by')
      ->select(
          'hr__leave.id',
          'employee__profile.name as emp',
          DB::raw('DATE_FORMAT(leave_apply_date,"%d/%m/%Y") as leave_d'),
          // 'leave_apply_date',
          'hr__leave.email',
          'contact',
          DB::raw('DATE_FORMAT(start_date,"%d-%m-%Y") as start_date'),
          DB::raw('DATE_FORMAT(end_date,"%d-%m-%Y") as end_date'),
          'reason',
          'status_level1',
          'status_level2',
          'remark',
          DB::raw('DATE_FORMAT(status_level1_date,"%d-%m-%Y") as status_level1_date'),
          DB::raw('DATE_FORMAT(status_level2_date,"%d-%m-%Y") as status_level2_date'),
          'level1.name as level1',
          'level2.name as level2',
          DB::raw('(DATEDIFF(end_date,start_date)) as st_date')

      )->get()->first()
      ;
    if($format != null){
        $data = [
            'format' => $format
            ];
        $pdfFilePath = "Leave Application.pdf";
        $pdf = PDF::loadView('hr.leave_pdf', $data);
        return $pdf->stream($pdfFilePath);
        return view('hr.leave_pdf',$data);
    }
    else{
        $message="No Leave Application Exist!!";
        return redirect('/hr/leave/list')->with('error',$message);
    }
    }
    public function leave_setting_list(){
        $data=[
            'layout' => 'layouts.main',
          
        ];
        // return $data;
        return view('hr.leave_setting_summary',$data);
    }
    public function leave_setting_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $jobdata = Settings::leftJoin('users as level1',function($join){
            $join->on(DB::raw("find_in_set(level1.id,settings.value)"),'>',DB::raw("0"));
            $join->where('settings.name','HR_Leave_Level1');
        })
        // ->leftJoin('users as level2',function($join){
        //     $join->on(DB::raw("find_in_set(level2.id,settings.value)"),'>',DB::raw("0"));
        //     $join->where('settings.name','HR_Leave_Level2');
        // })
        ->select(
            DB::raw('group_concat(level1.name) as level1')
            // DB::raw('group_concat(level2.name) as level2')
        )
        ;
        if(!empty($serach_value))
        {
            $jobdata->where(function($query) use ($serach_value){
                $query->where('level1.name','LIKE',"%".$serach_value."%")
                // ->orWhere('level2.name','LIKE',"%".$serach_value."%")
                ;
              });
     
        }
        
        if(isset($request->input('order')[0]['column']))
        {
            $data = [
                'level1.name'
                // 'level2.name as level2'
                ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $jobdata->orderBy('settings.id','desc');

        $count = count($jobdata->get()->toArray());
        $jobdata = $jobdata->offset($offset)->limit($limit);
        $jobdata=$jobdata->get()->first();
       
        $jobdata=explode(',',$jobdata['level1']);
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $jobdata; 
        return json_encode($array);
    }
    public function recruitment(){
        $data=[
            'layout' => 'layouts.main',
        ];
        // return $user;
        return view('hr.recruitment_create',$data); 
    }
    public function recruitmentDb(Request $request){
        try {
           
            $validerrarr =[
                'name'=>'required',
                'address'=>'required',
                // 'email'=>'required|unique:hr__recruitment,email',
                'contact'=>'required|unique:hr__recruitment,contact',
                'interview_date'=>'required',
                'reference'=>'required', 
                'position'=>'required',
                'remark'=>'required'
            ];
            $validmsgarr =[
                'name.required'=>'This field is required',
                'address.required'=>'This field is required',
                // 'email.required'=>'This field is required',
                'contact.required'=>'This field is required',
                'interview_date.required'=>'This field is required',
                'reference.required'=>'This field is required',
                'position.required'=>'This field is required',
                'remark.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            if($request->hasFile('resume'))
            {
               
                    $file = $request->file('resume');
                    $destinationPath = public_path().'/upload/recruitment_resume';
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
            $hr=HR_Recruitment::insertGetId([
                'id'=>NULL,
                'name'=>$request->input('name'),
                'address'=>$request->input('address'),
                'interview_date'=>date('Y-m-d'),
                'email'=>$request->input('email'),
                'contact'=>$request->input('contact'),
                'reference_from'=>$request->input('reference'),
                'position_for'=>$request->input('position'),
                'remark'=>$request->input('remark'),
                'resume'=>$resume,
                'created_by'=>Auth::id(),

            ]);
            if($hr==NULL){
                return redirect('/hr/recruitment/data/create')->with('error','some error occurred')->withInput();
            }
            else{
                return redirect('/hr/recruitment/data/create')->with('success','Successfully Recruitment Record Inserted.');
            }
        } 
        catch(\Illuminate\Database\QueryException $ex) 
        {
            return redirect('/hr/recruitment/data/create')->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }

    public function recruitment_not_list(){
        $emp=EmployeeProfile::select('id','name')->get();
        $dep=Department::select('id','department')->get();
        $data=[
            'layout' => 'layouts.main',
            'dep'=>$dep,
            'emp'=>$emp
          
        ];
        // return $data;
        return view('hr.recruitment_not_summary',$data);
    }
    public function recruitment_not_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $pre='Preliminary';
        $fin='Final';
        $jobdata = HR_Recruitment::where('hr__recruitment.final_round_id','=',NULL)
        ->select(
           'hr__recruitment.id',
            'name',
           
            DB::raw('(DATE_FORMAT(interview_date ,"%d-%m-%Y")) as interview_date'),
            'email',
           'contact',
            'reference_from',
           'position_for',
            'remark',
            'resume'
        
        ) 
        
       ->groupBy('hr__recruitment.id')
        ;
        if(!empty($serach_value))
        {
            $jobdata->where(function($query) use ($serach_value){
                $query->where('name','LIKE',"%".$serach_value."%")
                ->orWhere('email','LIKE',"%".$serach_value."%")
                ->orWhere('contact','LIKE',"%".$serach_value."%")
                ->orWhere('interview_date','LIKE',"%".$serach_value."%")
                ->orWhere('reference_from','LIKE',"%".$serach_value."%")
                ->orWhere('position_for','LIKE',"%".$serach_value."%")
                ->orWhere('remark','LIKE',"%".$serach_value."%");
              });
     
        }
        
        if(isset($request->input('order')[0]['column']))
        {
            $data = [
                'hr__recruitment.id',
                'name',
                'interview_date',
                'email',
                'contact',
                'reference_from',
                'position_for',
                'remark',
                'resume'
                ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $jobdata->orderBy('hr__recruitment.id','desc');

        $count = count($jobdata->get()->toArray());
        $jobdata = $jobdata->offset($offset)->limit($limit);
        $jobdata=$jobdata->get();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $jobdata; 
        return json_encode($array);
    }
    public function recruitment_interview_assess(Request $request){
        try {
           
            $validerrarr =[
                'round'=>'required',

                'prelim_round_by'=>'required_if:round,Preliminary',
                'remark'=>'required_if:round,Preliminary',
                'post_suited'=>'required_if:round,Preliminary',
                'dept'=>'required_if:round,Preliminary',
                // 'salary'=>'required_if:round,Preliminary',

                'final_round_by'=>'required_if:round,Final',
                'finalremark'=>'required_if:round,Final',
                'final_post_suited'=>'required_if:round,Final',
                'final_dept'=>'required_if:round,Final',
                // 'finalsalary'=>'required_if:round,Final',
                // 'finaljoining_date'=>'required_if:round,Final',
                'final_remark'=>'required_if:round,Final'
            ];
            $validmsgarr =[
                'round.required'=>'This field is required',

                'prelim_round_by.required_if'=>'This field is required',
                'remark.required_if'=>'This field is required',
                'post_suited.required_if'=>'This field is required',
                'dept.required_if'=>'This field is required',
                'salary.required_if'=>'This field is required',

                'final_round_by.required_if'=>'This field is required',
                'finalremark.required_if'=>'This field is required',
                'final_post_suited.required_if'=>'This field is required',
                'final_dept.required_if'=>'This field is required',
                // 'finalsalary.required_if'=>'This field is required',
                // 'finaljoining_date.required_if'=>'This field is required',
                'final_remark.required_if'=>'This field is required',
            ];
            $this->validate($request,$validerrarr,$validmsgarr);

            
            if($request->input('finaljoining_date')){
                $date=date('Y-m-d',strtotime($request->input('finaljoining_date')));
            }
            else{
                $date=NULL;
            }
            $round = $request->input('round');
            if(in_array("Preliminary", $round)){
                $rec=HR_Recruit_Assess::where('recruit_id',$request->input('id'))
                ->where('round','Preliminary')->get()->first();
                if($rec){
                    $hr=HR_Recruit_Assess::where('id',$rec['id'])->update([
                       
                        'round_by'=>$request->input('prelim_round_by'),
                        'remarks'=>$request->input('remark'),
                        'post_suited'=>$request->input('post_suited'),
                        'proposed_dept'=>$request->input('dept'),
                        'salary_expect'=>$request->input('salary'),
                        'joining_date'=>$date
                    ]);
                }
                else{
                    $hr=HR_Recruit_Assess::insertGetId([
                    'id'=>NULL,
                    'recruit_id'=>$request->input('id'),
                    'round'=> 'Preliminary',
                    'round_by'=>$request->input('prelim_round_by'),
                    'remarks'=>$request->input('remark'),
                    'post_suited'=>$request->input('post_suited'),
                    'proposed_dept'=>$request->input('dept'),
                    'salary_expect'=>$request->input('salary'),
                    'joining_date'=>$date,
                    'created_by'=>Auth::id()
                ]);
                if($hr){
                    HR_Recruitment::where('id',$request->input('id'))->update([
                        'pre_round_id'=>$hr
                    ]);
                }
                }
                
               
            }
            if(in_array("Final", $round)){
                $rec=HR_Recruit_Assess::where('recruit_id',$request->input('id'))
                ->where('round','Final')->get()->first();
                if($rec){
                    $hr=HR_Recruit_Assess::where('id',$rec['id'])->update([
                       
                        'round_by'=>$request->input('final_round_by'),
                        'remarks'=>$request->input('finalremark'),
                        'post_suited'=>$request->input('final_post_suited'),
                        'proposed_dept'=>$request->input('final_dept'),
                        'salary_expect'=>$request->input('finalsalary'),
                        'joining_date'=>$date,
                        'final_remarks'=>$request->input('final_remark'),
                    ]);
                }
                else{
                    $hr=HR_Recruit_Assess::insertGetId([
                        'id'=>NULL,
                        'recruit_id'=>$request->input('id'),
                        'round'=> 'Final',
                        'round_by'=>$request->input('final_round_by'),
                        'remarks'=>$request->input('finalremark'),
                        'post_suited'=>$request->input('final_post_suited'),
                        'proposed_dept'=>$request->input('final_dept'),
                        'salary_expect'=>$request->input('finalsalary'),
                        'joining_date'=>$date,
                        'final_remarks'=>$request->input('final_remark'),
                        'created_by'=>Auth::id()
                    ]);
                    if($hr){
                        HR_Recruitment::where('id',$request->input('id'))->update([
                            'final_round_id'=>$hr
                        ]);
                    }
                }
             
            }
            
            if($hr==NULL){
                return redirect('/hr/recruitment/not/list')->with('error','some error occurred')->withInput();
            }
            else{
                return redirect('/hr/recruitment/not/list')->with('success','Successfully Interview Assessment Record Inserted.');
            }
        } 
        catch(\Illuminate\Database\QueryException $ex) 
        {
            return redirect('/hr/recruitment/not/list')->with('error','some error occurred'.$ex->getMessage())->withInput();
        } 
    }
    public function recruitment_interview_log(){
     
        $data=[
            'layout' => 'layouts.main',
        ];
        // return $data;
        return view('hr.interview_log_summary',$data);
    }
    public function recruitment_interview_log_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog(); 
        $jobdata = HR_Recruitment::leftJoin('hr__recruitment_assess','hr__recruitment_assess.recruit_id','hr__recruitment.id')
         ->where('hr__recruitment_assess.id','<>',NULL)
        ->select(
            'hr__recruitment.id',
            'name',
            DB::raw('(DATE_FORMAT(interview_date ,"%d-%m-%Y")) as interview_date'),
            'email',
            'contact',
            'reference_from',
            'position_for',
            'remark',
            'status',
            'resume',
            'signed_document'
        )->GroupBy('hr__recruitment.id');
        ;
        
        if(!empty($serach_value))
        {
            $jobdata->where(function($query) use ($serach_value){
                $query->where('name','LIKE',"%".$serach_value."%")
                ->orWhere('email','LIKE',"%".$serach_value."%")
                ->orWhere('contact','LIKE',"%".$serach_value."%")
                ->orWhere('interview_date','LIKE',"%".$serach_value."%")
                ->orWhere('reference_from','LIKE',"%".$serach_value."%")
                ->orWhere('position_for','LIKE',"%".$serach_value."%")
                ->orWhere('remark','LIKE',"%".$serach_value."%")
                ->orWhere('status','LIKE',"%".$serach_value."%");
              });
     
        }
        
        if(isset($request->input('order')[0]['column']))
        {
            $data = [
                'hr__recruitment.id',
                'name',
                'interview_date',
                'email',
                'contact',
                'reference_from',
                'position_for',
                'remark',
                'resume',
                'status',
                'round_by',
                 'remarks','post_suited','proposed_dept','salary_expect','grade','final_remarks'
                ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $jobdata->orderBy('hr__recruitment.id','desc');

        $count = count($jobdata->get()->toArray());
        $jobdata = $jobdata->offset($offset)->limit($limit);
        $jobdata=$jobdata->get();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $jobdata; 
        return json_encode($array);
    }
    public function recruitment_interview_update(Request $request){
        try {
             $this->validate($request,
            [
                'status'=>'required'
            ],
            [
                'status.required'=>'This field is required'
            ]
            );
            if($request->input('finaljoining_date')){
                $date=date('Y-m-d',strtotime($request->input('finaljoining_date')));
            }
            else{
                $date=NULL;
            }
            $hr = HR_Recruitment::where('id',$request->input('id'));
            $up_hr=$hr->update([
                'status'=>$request->input('status')
            ]);
            $get_ids_f_p = $hr->get()->first();
            $asses_update = HR_Recruit_Assess::where('id',$get_ids_f_p['final_round_id'])->update([
                'joining_date'=>$date,
                'salary_expect'=>$request->input('salary')
            ]);
            if($up_hr==NULL){
                return redirect('/hr/recruitment/interview/log')->with('error','some error occurred')->withInput();
            }else{
                return redirect('/hr/recruitment/interview/log')->with('success','Successfully Recruitment Status updated.');
            }
        } catch (Exception $e) {
             return redirect('/hr/recruitment/interview/log')->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }
    public function interview_assess_data($id){
        $rec=HR_Recruit_Assess::where('recruit_id',$id)->leftJoin('employee__profile','employee__profile.id','hr__recruitment_assess.round_by')
        ->leftJoin('department','department.id','hr__recruitment_assess.proposed_dept')
        ->select('employee__profile.name','round',
        'remarks','post_suited','department','salary_expect','grade','final_remarks','joining_date')->get()->toArray();
        $full_rec = array();
        foreach ($rec as $key) {
            $full_rec[$key['round']] =$key;
        }
        // print_r($rec);die();
        $data=[
            'rec'=>$full_rec
        ];
        // return $rec;
        return view('hr.interview_assess',$data);
    }
    public function interview_assess_data_log($id){
        $rec=HR_Recruit_Assess::where('recruit_id',$id)->leftJoin('employee__profile','employee__profile.id','hr__recruitment_assess.round_by')
        ->leftJoin('department','department.id','hr__recruitment_assess.proposed_dept')
        ->select('employee__profile.name','round','round_by','proposed_dept',
        'remarks','post_suited','department','salary_expect','grade','final_remarks','joining_date')->get()->toArray();
        // $full_rec = array();
        // foreach ($rec as $key) {
        //     $full_rec[$key['round']] =$key;
        // }
     
        return $rec;
      
    }
    public function offerletter_generate($id,$name){

        $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
        $format = EmployeeAppoint::where('letter_type',$cat_type_id->id)
        ->first("content");
        $employee = HR_Recruitment::where('id',$id)->get()->first();
        $asses = HR_Recruit_Assess::where('recruit_id',$employee['id'])->select("joining_date")
        ->orderBy('id','desc')->get()->first();
         
        $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Letter_Date}}','{{Designation}}','{{Joining_Date}}');
        $replace = array($employee['name'],nl2br($employee['address']), date("d-m-Y"),$employee['position_for'],date("d-m-Y", strtotime($asses['joining_date'])));
        $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
       
       if($format != null){
            $data = [
                'foo' => 'bar',
                'format' => $replacement,
                
                ];
            $pdfFilePath = $employee['name']."_".$name.".pdf";
            $pdf = PDF::loadView('sections.letter_format', $data);
            return $pdf->stream($pdfFilePath);
            // return view('sections.letter_format',$data);
        }
        else{
            $message="No Appointment Letter exist!!";
            return redirect('/hr/recruitment/interview/log')->with('error',$message);
        }
    }
    public function recruitment_update($id){
        $hr=HR_Recruitment::where('id',$id)->get()->first();
        $data=[
            'layout' => 'layouts.main',
            'hr'=>$hr,
            'id'=>$id
        ];
        // return $user;
        return view('hr.recruitment_update',$data); 
    }
    public function recruitment_updateDb(Request $request,$id){
        try {
        //    print_r($request->input());die;
            $this->validate($request,
            [
                'name'=>'required',
                'address'=>'required',
                'email'=>'required|unique:hr__recruitment,email,'.$id,
                'contact'=>'required|unique:hr__recruitment,contact,'.$id,
                'interview_date'=>'required',
                'reference'=>'required', 
                'position'=>'required',
                'remark'=>'required'
            ],
            [
                'name.required'=>'This field is required',
                'address.required'=>'This field is required',
                'email.required'=>'This field is required',
                'contact.required'=>'This field is required',
                'interview_date.required'=>'This field is required',
                'reference.required'=>'This field is required',
                'position.required'=>'This field is required',
                'remark.required'=>'This field is required'
            ]
            );
            if($request->hasFile('resume'))
            {
               
                    $file = $request->file('resume');
                    $destinationPath = public_path().'/upload/recruitment_resume';
                    $filenameWithExt=$file->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
                    $extension = $file->getClientOriginalExtension();    
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;   
                    $resume=$fileNameToStore;
                    $file->move($destinationPath,$fileNameToStore);
            }
            else{
                $resume=$request->input('resume_old');
            }
            $hr=HR_Recruitment::where('id',$id)->update([
                'name'=>$request->input('name'),
                'address'=>$request->input('address'),
                'interview_date'=>date('Y-m-d'),
                'email'=>$request->input('email'),
                'contact'=>$request->input('contact'),
                'reference_from'=>$request->input('reference'),
                'position_for'=>$request->input('position'),
                'remark'=>$request->input('remark'),
                'resume'=>$resume,

            ]);
            if($hr==NULL){
                return redirect('/hr/recruitment/data/update/'.$id)->with('error','some error occurred')->withInput();
            }
            else{
                return redirect('/hr/recruitment/data/update/'.$id)->with('success','Successfully Recruitment Record updated.');
            }
        } 
        catch(\Illuminate\Database\QueryException $ex) 
        {
            return redirect('/hr/recruitment/data/update/'.$id)->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }
    public function interview_assess_print($id){
        // HR_Recruit_Assess::where('recruit_id',$id)->leftjoin('hr__recruitment','hr__recruitment_assess.recruit_id','hr__recruitment.id')
        // ->leftJoin('employee__profile','employee__profile.id','hr__recruitment_assess.round_by')
        // ->leftJoin('department','department.id','hr__recruitment_assess.proposed_dept')
        // ->select('employee__profile.name as emp','hr__recruitment.name','hr__recruitment.interview_date', 'round',
        // 'remarks','post_suited','department','salary_expect','grade','final_remarks','joining_date')->get()->toArray();
        $format=HR_Recruitment::where("id",$id)->get()->first();
        if($format != null){
            // $full_rec = array();
            // foreach ($format as $key) {
            //     $full_rec[$key['round']] =$key;
            // }
            
            $data = [
                // 'employee'=>$format[0],
                // 'format' => $full_rec
                'emp'=>$format
                ];
            $pdfFilePath = "Interview Assessment.pdf";
            $pdf = PDF::loadView('hr.interview_pdf', $data);
            return $pdf->stream($pdfFilePath);
            
        }
        else{
            $message="No Format Exist!!";
            return redirect('/hr/recruitment/not/list')->with('error',$message);
        }
    }
    //events
    public function create_events(){
        $department=Department::all();
     
        $data=[
            'layout' => 'layouts.main',
            'department'=>$department
        ];
        // return $user;
        return view('hr.events_create',$data);
    }
    public function create_eventsDb(Request $request){
        try {
            $validerrarr =[
                'date'=>'required',
                'event'=>'required',
                // 'dept'=>'required'
            ];
            $validmsgarr =[
                'date.required'=>'This field is required',
                'event.required'=>'This field is required',
                'dept.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            $depart = '';
            if(!empty($request->input('dept'))){
                $depart = implode(',',$request->input('dept'));
            }
            $hr=HR_Events::insertGetId([
                'id'=>NULL,
                'events'=>$request->input('event'),
                'date'=>date('Y-m-d',strtotime($request->input('date'))),
                'department'=>$depart
            ]);
            if($hr==NULL){
                return redirect('/hr/events/create')->with('error','some error occurred')->withInput();
            }
            else{
                return redirect('/hr/events/create')->with('success','Successfully Events Created.');
            }
        } 
        catch(\Illuminate\Database\QueryException $ex) 
        {
            return redirect('/hr/events/create')->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }
    public function index()
    {
        $events = [];
        $data = HR_Events::select('date','events','id')->get();
        if($data->count()) {
            foreach ($data as $key => $value) {
                $events[] = Calendar::event(
                    $value->events,
                    true,
                    new \DateTime($value->date),
                    new \DateTime($value->date.' +1 day'),
                    $value->id,
                    // Add color and link on event
	                [
	                    'color' => '#f05050',
	                    'url' => '#',
	                ]
                );
            }
        }
        $calendar = \Calendar::addEvents($events)
        ->setCallbacks([
            'eventClick' => 'function(event){
                showModal(event.title,event.id);
            }',
        ]);
        return view('hr.fullcalender',array('calendar'=>$calendar,'layout' => 'layouts.main'));
        // return view('hr.fullcalender', compact('calendar'));
    }

    public function create_announcements(){
        $department=Department::all();
        $data=[
            'layout' => 'layouts.main',
            'department'=>$department
        ];
        // return $user;
        return view('hr.announcement_create',$data);
    }
    public function create_announcementsDb(Request $request){
        try {
            $validerrarr =[
                'date'=>'required',
                'event'=>'required',
                // 'dept'=>'required'
            ];
            $validmsgarr =[
                'date.required'=>'This field is required',
                'event.required'=>'This field is required',
                // 'dept.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            if ($request->file('file')) {
                $file = $request->file('file');
                     $destinationPath = public_path().'/upload/announcement/';     
                     $filenameWithExt = $file->getClientOriginalName();
                     $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);   
                     $extension = $file->getClientOriginalExtension();
                     $name = $filename.'_'.rand(0,99).'_'.time().'.'.$extension;  
                     $path = $file->move($destinationPath, $name);
                   
                
            }
            else{
                $name=NULL;
            }

            $depart = '';
            if(!empty($request->input('dept'))){
                $depart = implode(',',$request->input('dept'));
            }
            $hr=Announcements::insertGetId([
                'id'=>NULL,
                'announcements'=>$request->input('event'),
                'date'=>date('Y-m-d',strtotime($request->input('date'))),
                'department'=>$depart,
                'pic'=>$name,
                'created_at'=>date('Y-m-d')
            ]);
            if($hr==NULL){
                return redirect('/hr/announcements/create')->with('error','some error occurred')->withInput();
            }
            else{
                return redirect('/hr/announcements/create')->with('success','Successfully announcements Created.');
            }
        } 
        catch(\Illuminate\Database\QueryException $ex) 
        {
            return redirect('/hr/announcements/create')->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
    }
    public function announcements(){
        $events = [];
        
        $data = Announcements::select('announcements.date','announcements.created_at','announcements.announcements','announcements.id','announcements.pic','announcements.department')->get();
          
        if($data->count()) {
            foreach ($data as $key => $value) {
               
                $events[] = Calendar::event(
                    $value->announcements,
                    true,
                    new \DateTime($value->created_at),
                    new \DateTime($value->date.' +1 day'),
                    $value->id,
                    // Add color and link on event
	                [
	                    'color' => '#f05050',
                        'url' => "#"
	                ]
                );
            }
        }
        
       
        
        // $calendar = Calendar::addEvents($events);
        $calendar = \Calendar::addEvents($events)
        ->setCallbacks([
            'eventClick' => 'function(event){
                showModal(event.title,event.id);
            }',
        ]);
        // return $events;
        return view('hr.announcements',array('calendar'=>$calendar,'layout' => 'layouts.main'));
        // return view('hr.fullcalender', compact('calendar'));
    }
    public function pic($id){
        $dept = Department::all();
        $d_count = count($dept);
        $ann=Announcements::where('announcements.id',$id)->leftJoin('department',function($join){
            $join->on(DB::raw("find_in_set(department.id,announcements.department)"),'>',DB::raw("0"));
        })->select('announcements.pic',DB::raw('CASE WHEN \''.$d_count.'\' = IF(announcements.department, LENGTH(announcements.department) - LENGTH(REPLACE(announcements.department, ",", "")) + 1, 0) THEN "ALL Department" ELSE GROUP_CONCAT(department.department) END AS dept_name'))->GroupBy('announcements.id')->get()->first();
        if($ann['pic']){
            if (file_exists(public_path().'/upload/announcement/'.$ann['pic'])){
                $x= 1;
            }
            else{
                $x= 0;
            }
            
        }
        else{
            $x= 0;
        }
        $data=array('val'=>$x,'pic'=>$ann['pic'],'dept'=>$ann['dept_name']);
        return $data;
    }
    public function department($id){
        $dept = Department::all();
        $d_count = count($dept);
        $ann=HR_Events::where('hr__events.id',$id)->leftJoin('department',function($join){
            $join->on(DB::raw("find_in_set(department.id,hr__events.department)"),'>',DB::raw("0"));
        })->select(DB::raw('CASE WHEN \''.$d_count.'\' = IF(hr__events.department, LENGTH(hr__events.department) - LENGTH(REPLACE(hr__events.department, ",", "")) + 1, 0) THEN "ALL Department" ELSE GROUP_CONCAT(department.department) END AS dept_name'))->GroupBy('hr__events.id')->get()->first();
        
        $data=array('dept'=>$ann['dept_name']);
        return $data;
    }
    public function upload_offer_letter(Request $request){
       try {
            $error =[];
            if(empty($request->file('signed_file'))){
                $error = array_merge($error,array('Upload Signed File is Required'));
            }

            if(count($error)>0){
                $data = [
                'error'=>$error];
                return response()->json($data);
            }
            $file = $request->file('signed_file');
            $job_image ='';
            if(isset($file) || $file != null){
                $destinationPath = public_path().'/upload/signed_offer_letter/';
                $filenameWithExt = $request->file('signed_file')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('signed_file')->getClientOriginalExtension();
                $job_image = $filename.'_'.time().'.'.$extension;
                $path = $file->move($destinationPath, $job_image);
            }else{
                $job_image = '';
            }
            $up_hr=HR_Recruitment::where('id',$request->input('offer_emp_id'))->update([
                'signed_document'=>$job_image
            ]);
            if($up_hr==NULL){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));  
                }else{
                    $msg =['Signed Offer Letter Uploaded Successfully.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
               
                return response()->json($data);
        } catch (Exception $e) {
             return redirect('/hr/recruitment/interview/log')->with('error','some error occurred'.$ex->getMessage())->withInput();
        } 
    }
    public function create_holiday(){
        $data=[
            'layout' => 'layouts.main'
        ];
        return view('hr.holiday_form',$data);
    }
    public function create_holiday_db(Request $request){
        try {
            $validerrarr =[
                'holiday_name'=>'required',
                // 'h_year'=>'required',
                'start_date'=>'required',
                'end_date'=>'required'
            ];
            $validmsgarr =[
                'holiday_name.required'=>'This field is required',
                // 'h_year.required'=>'This field is required',
                'start_date.required'=>'This field is required',
                'end_date.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);

            $st_date = date('Y-m-d',strtotime($request->input('start_date')));
            $en_date = date('Y-m-d',strtotime($request->input('end_date')));
            
            $list_date = array(); 
            $Variable1= strtotime($st_date);
            $Variable2= strtotime($en_date);

            for ($currentDate = $Variable1; $currentDate <= $Variable2; $currentDate += (86400)) {                
                $Store = date('Y-m-d', $currentDate); 
                $list_date[] = $Store; 
            }

            $sting_date = implode(',',$list_date);
            

            $holiday = Holiday::insertGetId([
                'name'=>$request->input('holiday_name'),
                'date'=>$sting_date,
                'start_date'=>$st_date,
                'end_date'=>$en_date,
                // 'year'=>$request->input('h_year'),
                'created_by'=>Auth::id(),
                'created_at'=>date('Y-m-d')
            ]);
            if($holiday==NULL){
                DB::rollback();
                return redirect('/create/holiday')->with('error','Some Unexpected Error occurred.');  
            }else{
                return redirect('/create/holiday')->with("success","Holiday Inserted Successfully.");
            }

        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/create/holiday')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function holiday_summary(){
        $data=[
            'layout' => 'layouts.main'
        ];
        return view('hr.holiday_summary',$data);
    }
    public function holiday_summary_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;

        // DB::enableQueryLog();
        $holiday= Holiday::select('id','name','date',DB::raw('DATE_FORMAT(start_date,"%d-%m-%Y") as start_date'),DB::raw('DATE_FORMAT(end_date,"%d-%m-%Y") as end_date'),DB::raw('(Case When start_date != end_date THEN Concat(DATE_FORMAT(start_date,"%d-%m-%Y")," to ",DATE_FORMAT(end_date,"%d-%m-%Y")) ELSE DATE_FORMAT(start_date,"%d-%m-%Y") END )as sum_date'),'year')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value)){
             $holiday =$holiday->where(function($query) use ($serach_value){
                    $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('year','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($holiday->get()->toArray());
        $holiday = $holiday->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['id','name',
            'date',
            'start_date',
            'end_date',
            'sum_date',
            'year'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $holiday->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $holiday->orderBy('id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $holiday->get(); 
        return json_encode($array);

    }
    public function update_holiday($id){
        $list = Holiday::where('id',$id)->select('id','name','date',DB::raw('DATE_FORMAT(start_date,"%d-%m-%Y") as start_date'),DB::raw('DATE_FORMAT(end_date,"%d-%m-%Y") as end_date'),'year')->get()->first();
        $data=[
            'layout' => 'layouts.main',
            'holiday'=>$list,
            'id' =>$id
        ];
        return view('hr.holiday_update',$data);
    }
    public function update_holiday_db(Request $request,$id){
        try {
            $validerrarr =[
                'holiday_name'=>'required',
                // 'h_year'=>'required',
                'start_date'=>'required',
                'end_date'=>'required',
                'update_reason'=>'required'
            ];
            $validmsgarr =[
                'holiday_name.required'=>'This field is required',
                // 'h_year.required'=>'This field is required',
                'start_date.required'=>'This field is required',
                'end_date.required'=>'This field is required',
                'update_reason.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);

            $st_date = date('Y-m-d',strtotime($request->input('start_date')));
            $en_date = date('Y-m-d',strtotime($request->input('end_date')));
            
            $list_date = array(); 
            $Variable1= strtotime($st_date);
            $Variable2= strtotime($en_date);

            for ($currentDate = $Variable1; $currentDate <= $Variable2; $currentDate += (86400)) {                
                $Store = date('Y-m-d', $currentDate); 
                $list_date[] = $Store; 
            }

            $sting_date = implode(',',$list_date);
            

            $holiday = Holiday::where('id',$id)->update([
                'name'=>$request->input('holiday_name'),
                'date'=>$sting_date,
                'start_date'=>$st_date,
                'end_date'=>$en_date,
                // 'year'=>$request->input('h_year'),
                'update_reason'=>$request->input('update_reason'),
                'updated_at'=>date('Y-m-d')
            ]);
            if($holiday==NULL){
                DB::rollback();
                return redirect('/update/holiday/'.$id)->with('error','Some Unexpected Error occurred.');  
            }else{
                return redirect('/update/holiday/'.$id)->with("success","Holiday Form Updated Successfully.");
            }

        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/update/holiday/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
    }

    public function leave_count_list(){
        $hr1=Settings::where('name','HR_Leave_Level1')->select('value','name')->get()->first();
        $hr2=Settings::where('name','HR_Leave_Level2')->select('value','name')->get()->first();
        $hr=$hr1['value'].','.$hr2['value'];
        $data=[
            'layout' => 'layouts.main',
            'hr'=>$hr,
        ];
        // return $data;
        return view('hr.leave_count_summary',$data);
    }
    public function leave_count_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $sort_data_query = array();
        $a_date =  $request->input('year');
        // print_r($a_date);die;
        $user = Attendance::select('emp_id')->WhereYear('date',$a_date)->get()->toArray();
        
        $mon=['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'];
        for ($j = 1; $j <= 12 ; $j++) {
            // $emp_id = $user[$j]['party_id'];
            $md=$mon[$j];
            $query[$j] = "IFNULL((SELECT count(att.status) FROM payroll__attendance att WHERE att.status<>'A' AND att.emp_id = payroll__attendance.emp_id AND YEAR(att.date)=".$a_date.".  AND MONTH(att.date) = ".$j." ),'') as d".$j." ";

        }

     
        $query = join(",",$query);
        $less_yr=$a_date-1;
        $jobdata = EmployeeProfile::leftJoin('payroll__attendance','payroll__attendance.emp_id','employee__profile.id')
        ->WhereYear('payroll__attendance.date',$a_date)
        ->where('payroll__attendance.status','!=',"A")
        ->leftJoin('leave__enhancement', function($join) use ($less_yr){
            $join->on('leave__enhancement.emp_id','=','employee__profile.id');
            $join->where('leave__enhancement.year','=',$less_yr);
       })
       
        ->orWhere('payroll__attendance.emp_id','=',NULL)
        ->select('name','employee_number',DB::raw('count(payroll__attendance.status) as present_current'),
       
        DB::raw('(IFNULL(
            (SELECT count(m.id) FROM payroll__attendance m 
            WHERE employee__profile.id=m.emp_id
            AND m.status<>"A"
            AND YEAR(m.date)='.$less_yr.'
                GROUP BY employee__profile.id) ,"0" ) 
            ) as total_present'),
        DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$a_date.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as absent'),
                DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                'employee__profile.id as emp_id',
         DB::raw($query)
         )->GroupBy('employee__profile.id');

        //   print($jobdata);die;
        if(!empty($serach_value))
        {
            $jobdata->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
                ->orWhere('employee_number','LIKE',"%".$serach_value."%")
               
                ;
              });
     
        }
        
        if(isset($request->input('order')[0]['column']))
        {
            $data = [
           
            'employee__profile.name',
            'employee_number',
            'employee__profile.id'
                ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $jobdata->orderBy('employee__profile.id','desc');

        $count = count($jobdata->get()->toArray());
        $jobdata = $jobdata->offset($offset)->limit($limit);
        $jobdata=$jobdata->get();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $jobdata;
        return json_encode($array);
    }

    public function leave_enhancement_list(){
   
        $data=[
            'layout' => 'layouts.main'
        ];
        // return $data;
        return view('hr.leave_enhancement_summary',$data);
    }
    public function insertdata(){
        $date='2019-12-31';
     
        for($i=1;$i<=366;$i++){
            $date=date('Y-m-d', strtotime('+1 day', strtotime($date)));
            $day=date('D', strtotime('+1 day', strtotime($date)));
            // print_r($day);die;
            $status="P";
            $in_time="10:00:00";
                $out_time="18:00:00";
            if($i % 17 ==0){
                $status="A";
                $in_time="00:00:00";
                $out_time="00:00:00";
            }
            if($day=="Sat" || $day=="Sun"){
                $status="WO";
                $in_time="00:00:00";
                $out_time="00:00:00";
            }
            $bal=Attendance::insertGetId([
                'emp_id'=>5,
                'date'=>$date,
                'department_id'=>'6',
                'in_time'=>$in_time,
                'out_time'=>$out_time,
                'duration'=>'08:00:00',
                'early_by'=>'08:00:00',
                'late_by'=>'08:00:00',
                'ot'=>'08:00:00',
                'shift'=>'Office',
                'status'=>$status
            ]);
        }

        // for($i=1;$i<=366;$i++){
        //     $date=date('Y-m-d', strtotime('+1 day', strtotime($date)));
         
        //     $bal=Month::insertGetId([
        //        'month_id'=>date('m',strtotime($date)),
        //         'month'=>date('F',strtotime($date)),
        //         'date'=>$date
        //     ]);
        // }

        print_r("all done");
       
    }
    public function leave_enhancement_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $sort_data_query = array();
        $a_date =  $request->input('year');
        $less_yr=$a_date-1;
        $jobdata = EmployeeProfile::leftJoin('payroll__attendance','payroll__attendance.emp_id','employee__profile.id')
        ->WhereYear('payroll__attendance.date',$a_date)
        ->where('payroll__attendance.status',"P")
        ->leftJoin('leave__enhancement', function($join) use ($less_yr){
            $join->on('leave__enhancement.emp_id','=','employee__profile.id');
            $join->where('leave__enhancement.year','=',$less_yr);
       })
       ->leftJoin('payroll', function($join){
        $join->on('payroll.emp_id','=','employee__profile.id');
        $join->where('payroll.salary_type','=',"SalaryA");
   })
        ->orWhere('payroll__attendance.emp_id','=',NULL)
        ->select('name','employee_number',
        DB::raw('count(payroll__attendance.status) as present_current'),
        DB::raw('(IFNULL(payroll.basic_salary + payroll.dearness_allowance ,"0") )as total_sal_a'),
        DB::raw('(IFNULL(
            (SELECT count(m.id) FROM payroll__attendance m 
            WHERE employee__profile.id=m.emp_id
            AND m.status<>"A"
            AND YEAR(m.date)='.$less_yr.'
                GROUP BY employee__profile.id) ,0 ) 
            ) as total_present'),
        DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$a_date.'
                    GROUP BY employee__profile.id) ,0 ) 
                ) as absent'),
                DB::raw('IFNULL(carried_leave,"-") as carried_leave'),
                DB::raw('IFNULL(paid_leave,"-") as paid_leave'),
                DB::raw('IFNULL(amount_paid,"-") as amount_paid'),
                'employee__profile.id as emp_id'
         )->GroupBy('employee__profile.id');

        //   print($jobdata);die;
        if(!empty($serach_value))
        {
            $jobdata->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
                ->orWhere('employee_number','LIKE',"%".$serach_value."%")
               
                ;
              });
     
        }
        
        if(isset($request->input('order')[0]['column']))
        {
            $data = [
           
            'employee__profile.name',
            'employee_number',
            'employee__profile.id'
                ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $jobdata->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $jobdata->orderBy('employee__profile.id','desc');

        $count = count($jobdata->get()->toArray());
        $jobdata = $jobdata->offset($offset)->limit($limit);
        $jobdata=$jobdata->get();

        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $jobdata;
        return json_encode($array);
    }

    public function leave_enhancement_form(Request $request){
        // print_r($request->input());die;
        try {
            $year=$request->input('year');
            $jobdata = EmployeeProfile::leftJoin('payroll__attendance','payroll__attendance.emp_id','employee__profile.id')
            ->WhereYear('payroll__attendance.date',$year)
            ->where('payroll__attendance.status',"P")
            ->leftJoin('leave__enhancement', function($join) use ($year){
                $join->on('leave__enhancement.emp_id','=','employee__profile.id');
                $join->where('leave__enhancement.year','=',$year);
           })
            ->orWhere('payroll__attendance.emp_id','=',$request->input('emp_id'))
            ->select(
            DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    AND m.status="A"
                    AND YEAR(m.date)='.$year.'
                        GROUP BY employee__profile.id) ,0 ) 
                    ) as absent')
             )->GroupBy('employee__profile.id')->get()->first();
            $tax=Att_Enhansement::where('emp_id',$request->input('emp_id'))->where('year',$request->input('year'))
           ->get()->first();
            // print_r($jobdata['absent']);die;
            if($tax){
                // $error = array_merge($error,array('Balance Leave for year '.$request->input('year') . 'of this employee is already entered.'));
                return redirect('/hr/leave/enhancement/list')->with('error','Balance Leave for year '.$request->input('year') . ' of this employee has already entered.');
            }
            $paid=$request->input('paid_l');
            $carried=$request->input('carried_l');
            $closing=$request->input('closing');
            $total_l=$request->input('total_l');
            $available_l=$total_l+$carried;
            $balance=$total_l+$carried-$jobdata['absent'];

            $tot=$paid+$carried;
            // print_r($balance);die;
            if($tot>$closing){
                // $error = array_merge($error,array('Total paid and carried leaves cannot be greater than balance leaves '));
                return redirect('/hr/leave/enhancement/list')->with('error','Total paid and carried leaves cannot be greater than balance leaves ');
            }
        
            
            $timestamp = date('Y-m-d G:i:s');
            $reciept = Att_Enhansement::InsertGetId([        
                    'id'=>null,
                    'emp_id'=> $request->input('emp_id'),
                    'paid_leave'=>$request->input('paid_l'),
                    'carried_leave'=>$request->input('carried_l'),
                    'amount_paid'=>$request->input('amount'),
                    'year'=>$request->input('year'),

                
                   
                ]);
            
                if($reciept == null){
                    DB::rollback();
                    // $error = array_merge($error,array('Some Unexpected Error occurred.'));
                    return redirect('/hr/leave/enhancement/list')->with('error','Some Unexpected Error occurred.');
                }else{
                    $yr=$request->input('year')+1;
                    $bal=Att_Balance::where('emp_id',$request->input('emp_id'))->where('year',$yr)
                    ->get()->first();

                    if($bal==NULL){
                        $bal=Att_Balance::insertGetId([
                            'emp_id'=>$request->input('emp_id'),
                            'available_leave'=>$available_l,
                            'balance_leave'=>$balance,
                            'year'=>$yr
                        ]);
                    }
                    else{
                        $bal=Att_Balance::where('emp_id',$request->input('emp_id'))
                        ->where('year',$request->input('year'))
                        ->update([
                            'available_leave'=>$available_l,
                            'balance_leave'=>$balance,
                            'year'=>$yr
                        ]);
                    }
                    return redirect('/hr/leave/enhancement/list')->with('success','Balance Leave successfully updated');
                }
               
        } catch (\Illuminate\Database\QueryException $ex) {
             return redirect('/hr/leave/enhancement/list')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function get_emp($yr){
        $employee=EmployeeProfile::leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->whereYear('employee__relieving.leaving_date',$yr)
        ->orWhere('employee__relieving.emp_id',NULL)
        ->select('employee__profile.id','name','employee_number')
        ->orderBy('employee__profile.id', 'asc')
        ->get();

        return $employee;
    }
    public function leave_reg_details($emp,$yr){
        $employee=EmployeeProfile::where('employee__profile.id',$emp)
        ->leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->whereYear('employee__relieving.leaving_date',$yr)
        ->orWhere('employee__relieving.emp_id',NULL)
        
        ->select(DB::raw('IFNULL(DATE_FORMAT(leaving_date,"%m"),"12") as leaving_date'))
        ->get()->first();


        
        $cc = $employee['leaving_date'];
        $a_date =  $yr;
        $less_yr=$a_date-1;
        $mon = ['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Decem'];
        for ($j = 1; $j <= $employee['leaving_date'] ; $j++) {
            // $emp_id = $user[$j]['party_id'];
            $md=$mon[$j];
            $query[$j] = "IFNULL((SELECT count(att.status) FROM payroll__attendance att 
            WHERE  att.emp_id=".$emp.". AND att.status<>'A' AND YEAR(att.date)=".$a_date.".  AND MONTH(att.date) = ".$j." ),'') as ".$mon[$j]." ";

            // $qu_start[$j]=HR_Leave::where('employee',$emp)
            // ->whereMonth('hr__leave.start_date','=',$j)
            // ->whereYear('hr__leave.start_date','=',$a_date)
            // ->whereMonth('hr__leave.end_date','!=',$j)
            // ->whereYear('hr__leave.end_date','=',$a_date)
            // ->select(DB::raw('IFNULL(group_concat(concat(start_date,",",end_date)),"-") as '.$mon[$j]))->get()->first();

            // $qu_end[$j]=HR_Leave::where('employee',$emp)
            // ->WhereMonth('hr__leave.end_date','=',$j)
            // ->WhereYear('hr__leave.end_date','=',$a_date)
            // ->whereMonth('hr__leave.start_date','!=',$j)
            // ->whereYear('hr__leave.start_date','=',$a_date)
            // ->select(DB::raw('IFNULL(group_concat(concat(start_date,",",end_date)),"-") as '.$mon[$j]))->get()->first();

            // $qu_eq[$j]=HR_Leave::where('employee',$emp)
            // ->WhereMonth('hr__leave.end_date','=',$j)
            // ->WhereYear('hr__leave.end_date','=',$a_date)
            // ->whereMonth('hr__leave.start_date','=',$j)
            // ->whereYear('hr__leave.start_date','=',$a_date)
            // ->select(DB::raw('IFNULL(group_concat(concat(start_date,",",end_date)),"-") as '.$mon[$j]))->get()->first();

            $leave[$j]=HR_Leave::where('employee',$emp)
            
            ->whereMonth('hr__leave.start_date', '<=', $j)
            ->whereMonth('hr__leave.end_date', '>=', $j)
            ->whereYear('hr__leave.start_date', '<=', $a_date)
            ->whereYear('hr__leave.end_date', '>=', $a_date)
            ->where('hr__leave.adjust_leave','=','1')
            ->where('hr__leave.status_level1','=','Approved')
            ->select(DB::raw('IFNULL(group_concat(concat(start_date,":",end_date)),"-") as '.$mon[$j]))->get()->first();
            // ->leftJoin('month', function($join) use ($j){
                // $join->on('lmonth.month_id','=','employee__profile.id');
            //     $join->whereMonth('hr_leave.start_date', '<=', $j);
            //  $join->whereMonth('hr_leave.end_date', '>=', $j);
        //    })
            
             
        }
        $query = join(",",$query);
        // $leave = join(",",$leave);
        // $qu_end = join(",",$qu_end);
        // $qu_eq = join(",",$qu_eq);
        // print_r($leave);
        // print_r('<br><br><br>');
        // print_r($qu_end);
        // print_r('<br><br><br>');
        // print_r($qu_eq);
        // print_r('<br><br><br>');
        // $mer=array_merge($qu_start,$qu_end);
        //  $mer = join(",",$mer); 
        //  print_r($mer[0]);
        // if($qu_start==$qu_end){
        //     print_r("sME");
        // }
        // die;
        $em=EmployeeProfile::where('employee__profile.id',$emp)
        // ->leftJoin('payroll__attendance','payroll__attendance.emp_id','employee__profile.id')
        // ->WhereYear('payroll__attendance.date',$a_date)
        // ->where('payroll__attendance.status',"P")
        ->leftJoin('payroll__attendance', function($join) use ($a_date){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date',$a_date);
            $join ->where('payroll__attendance.status','!=',"A");
       })
        ->leftJoin('leave__enhancement', function($join) use ($a_date){
            $join->on('leave__enhancement.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date',$a_date);
            
       })
    //    ->leftJoin('hr__leave', function($join) use ($a_date){
    //     $join->on('hr__leave.employee','=','employee__profile.id');
    //     $join->whereMonth('hr__leave.start_date','=',$a_date);
    //     $join->orWhereMonth('hr__leave.end_date','=',$a_date);
    //     })
       
        // ->orWhere('payroll__attendance.emp_id','=',NULL)
        ->select('employee__profile.id',
        'name',
        'employee_number',
        
       
        DB::raw('(IFNULL(
            (SELECT count(m.id) FROM payroll__attendance m 
            WHERE employee__profile.id=m.emp_id
            AND m.status="P"
            AND YEAR(m.date)='.$less_yr.'
                GROUP BY employee__profile.id) ,"0" ) 
            ) as total_present'),
            DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status<>"A"
                AND YEAR(m.date)='.$a_date.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_present_current'),
        DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$a_date.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as absent'),
                DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                DB::raw('IFNULL(paid_leave,"0") as paid_leave'),



        DB::raw($query))->GroupBy('employee__profile.id')->get();
       
        $days=['1'=>'31','2'=>'28','3'=>'31','4'=>'30','5'=>'31','6'=>'30','7'=>'31','8'=>'31','9'=>'30','10'=>'31','11'=>'30','12'=>'31'];
        $data=[
            'emp'=>$em,
            'leave'=>$leave,
            'cc'=>$cc,
            'mon'=>$mon,
            'days'=>$days
        ];
        //  print_r(date('Y-m-t',strtotime('2020-06-15')));die;
        return view('hr.leave_register_data',$data);
    }

    public function leave_register_list(){
       
        $data=[
            'layout' => 'layouts.main'
        ];
        return view('hr.leave_register',$data);
    }

    public function leave_register_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $date=$request->input('date');
        $emp=$request->input('employee');
        $a_date =  $date;
        $less_yr=$a_date-1;
        // DB::enableQueryLog();
        if($emp==0) {
        $employee=EmployeeProfile::leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
       ->whereYear('employee__relieving.leaving_date',$date)
       ->orWhere('employee__relieving.emp_id',NULL)
        //sdsdsad
        ->leftJoin('payroll__attendance', function($join) use ($a_date){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->where('payroll__attendance.date','=',$a_date);
            $join->where('payroll__attendance.status','!=',"A");
            $join->orWhere('payroll__attendance.emp_id','=',NULL);
       })
       ->leftJoin('department','department.id','employee__profile.department_id')
       
        ->leftJoin('leave__enhancement', function($join) use ($a_date){
            $join->on('leave__enhancement.emp_id','=','employee__profile.id');
            $join->where('leave__enhancement.year','=',$a_date);
       })
       
       ->select('name','employee_number', 
       DB::raw('IFNULL(father_name,"-") as father_name'),
       DB::raw('IFNULL(local_address,"-") as local_address'),
       DB::raw('IFNULL(designation,"-") as designation'),
       DB::raw('IFNULL(department.department,"-") as department'),
   
       DB::raw('IFNULL(DATE_FORMAT(leaving_date,"%d-%m-%Y"),"-") as leaving_date'),
        DB::raw('count(payroll__attendance.status) as present_current'),
        DB::raw('(IFNULL(
            (SELECT count(m.id) FROM payroll__attendance m 
            WHERE employee__profile.id=m.emp_id
            AND m.status<>"A"
            AND YEAR(m.date)='.$less_yr.'
                GROUP BY employee__profile.id) ,0 ) 
            ) as total_present'),
        DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$a_date.'
                    GROUP BY employee__profile.id) ,0 ) 
                ) as absent'),
                DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                DB::raw('IFNULL(paid_leave,"-") as paid_leave'),
                DB::raw('IFNULL(amount_paid,"-") as amount_paid'),
                'employee__profile.id as emp_id'
         )->GroupBy('employee__profile.id');
                       
         }
         else{
           
            $employee=EmployeeProfile::leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
            // ->whereYear('employee__relieving.leaving_date',$date)
            ->where('employee__profile.id','=',$emp)
             //sdsdsad
             ->leftJoin('payroll__attendance', function($join) use ($a_date){
                 $join->on('payroll__attendance.emp_id','=','employee__profile.id');
                 $join->where('payroll__attendance.date','=',$a_date);
                 $join->where('payroll__attendance.status','!=',"A");
                 $join->orWhere('payroll__attendance.emp_id','=',NULL);
            })
            ->leftJoin('department','department.id','employee__profile.department_id')
            
             ->leftJoin('leave__enhancement', function($join) use ($a_date){
                 $join->on('leave__enhancement.emp_id','=','employee__profile.id');
                 $join->where('leave__enhancement.year','=',$a_date);
            })
            
            ->select('name','employee_number', 
            DB::raw('IFNULL(father_name,"-") as father_name'),
            DB::raw('IFNULL(local_address,"-") as local_address'),
            DB::raw('IFNULL(designation,"-") as designation'),
            DB::raw('IFNULL(department.department,"-") as department'),
        
            DB::raw('IFNULL(DATE_FORMAT(leaving_date,"%d-%m-%Y"),"-") as leaving_date'),
             DB::raw('count(payroll__attendance.status) as present_current'),
             DB::raw('(IFNULL(
                 (SELECT count(m.id) FROM payroll__attendance m 
                 WHERE employee__profile.id=m.emp_id
                 AND m.status<>"A"
                 AND YEAR(m.date)='.$less_yr.'
                     GROUP BY employee__profile.id) ,0 ) 
                 ) as total_present'),
             DB::raw('(IFNULL(
                     (SELECT count(m.id) FROM payroll__attendance m 
                     WHERE employee__profile.id=m.emp_id
                     AND m.status="A"
                     AND YEAR(m.date)='.$a_date.'
                         GROUP BY employee__profile.id) ,0 ) 
                     ) as absent'),
                     DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                     DB::raw('IFNULL(paid_leave,"-") as paid_leave'),
                     DB::raw('IFNULL(amount_paid,"-") as amount_paid'),
                     'employee__profile.id as emp_id'
              )->GroupBy('employee__profile.id');     
         }
       
       
        // print($employee);die();
        if(!empty($serach_value)){
             $employee =$employee->where(function($query) use ($serach_value){
                    $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('father_name','LIKE',"%".$serach_value."%")
                        ->orwhere('designation','LIKE',"%".$serach_value."%")
                        ->orwhere('department.department','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        //  print_r($emp);die;
        

        $count = count($employee->get()->toArray());
        $employee = $employee->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = [ 'employee__profile.name',
            'employee_number',
            'employee__profile.id','department.department','father_name'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $employee->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $employee->orderBy('employee__profile.id', 'asc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $employee->get(); 
        return json_encode($array);
    }

    public function leave_register_print($id,$yr){
        $a_date =  $yr;
        $less_yr = $a_date-1;
        $format = EmployeeProfile::leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
            ->where('employee__profile.id',$id)
             ->leftJoin('payroll__attendance', function($join) use ($a_date){
                 $join->on('payroll__attendance.emp_id','=','employee__profile.id');
                 $join->where('payroll__attendance.date','=',$a_date);
                 $join->where('payroll__attendance.status','!=',"A");
                 $join->orWhere('payroll__attendance.emp_id','=',NULL);
            })
            ->leftJoin('department','department.id','employee__profile.department_id')
            ->leftJoin('leave__enhancement', function($join) use ($a_date){
                 $join->on('leave__enhancement.emp_id','=','employee__profile.id');
                 $join->where('leave__enhancement.year','=',$a_date);
            })
            ->select('name','employee_number', 
            DB::raw('IFNULL(father_name,"-") as father_name'),
            DB::raw('IFNULL(local_address,"-") as local_address'),
            DB::raw('IFNULL(designation,"-") as designation'),
            DB::raw('IFNULL(department.department,"-") as department'),
            DB::raw('IFNULL(DATE_FORMAT(leaving_date,"%d-%m-%Y"),"-") as leaving_date'),
             DB::raw('count(payroll__attendance.status) as present_current'),
             DB::raw('(IFNULL(
                 (SELECT count(m.id) FROM payroll__attendance m 
                 WHERE employee__profile.id=m.emp_id
                 AND m.status<>"A"
                 AND YEAR(m.date)='.$less_yr.'
                     GROUP BY employee__profile.id) ,0 ) 
                 ) as total_present'),
             DB::raw('(IFNULL(
                     (SELECT count(m.id) FROM payroll__attendance m 
                     WHERE employee__profile.id=m.emp_id
                     AND m.status="A"
                     AND YEAR(m.date)='.$a_date.'
                         GROUP BY employee__profile.id) ,0 ) 
                     ) as absent'),
                     DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                     DB::raw('IFNULL(paid_leave,"-") as paid_leave'),
                     DB::raw('IFNULL(amount_paid,"-") as amount_paid'),
                     'employee__profile.id as emp_id'
              )->GroupBy('employee__profile.id')->get()->first(); 

            
         $employee=EmployeeProfile::where('employee__profile.id',$id)
        ->leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->whereYear('employee__relieving.leaving_date',$yr)
        ->orWhere('employee__relieving.emp_id',NULL)
        
        ->select(DB::raw('IFNULL(DATE_FORMAT(leaving_date,"%m"),"12") as leaving_date'))
        ->get()->first();


        
        $cc = $employee['leaving_date'];
        $a_date =  $yr;
        $less_yr=$a_date-1;
        $mon = ['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Decem'];
        for ($j = 1; $j <= $employee['leaving_date'] ; $j++) {
            $md=$mon[$j];
            $query[$j] = "IFNULL((SELECT count(att.status) FROM payroll__attendance att 
            WHERE  att.emp_id=".$id.". AND att.status<>'A' AND YEAR(att.date)=".$a_date.".  AND MONTH(att.date) = ".$j." ),'') as ".$mon[$j]." ";
            $leave[$j]=HR_Leave::where('employee',$id)
            
            ->whereMonth('hr__leave.start_date', '<=', $j)
            ->whereMonth('hr__leave.end_date', '>=', $j)
            ->whereYear('hr__leave.start_date', '<=', $a_date)
            ->whereYear('hr__leave.end_date', '>=', $a_date)
            
            ->where('hr__leave.status_level1','=','Approved')
            ->select(DB::raw('IFNULL(group_concat(concat(start_date,":",end_date)),"-") as '.$mon[$j]))->get()->first();             
        }
        $query = join(",",$query);
        $em = EmployeeProfile::where('employee__profile.id',$id)
            ->leftJoin('payroll__attendance', function($join) use ($a_date){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date',$a_date);
            $join ->where('payroll__attendance.status','!=',"A");
       })
        ->leftJoin('leave__enhancement', function($join) use ($a_date){
            $join->on('leave__enhancement.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date',$a_date);
            
       })
        ->select('employee__profile.id',
        'name',
        'employee_number',
        DB::raw('(IFNULL(
            (SELECT count(m.id) FROM payroll__attendance m 
            WHERE employee__profile.id=m.emp_id
            AND m.status="P"
            AND YEAR(m.date)='.$less_yr.'
                GROUP BY employee__profile.id) ,"0" ) 
            ) as total_present'),
            DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status<>"A"
                AND YEAR(m.date)='.$a_date.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_present_current'),
        DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$a_date.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as absent'),
                DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                DB::raw('IFNULL(paid_leave,"0") as paid_leave'),



        DB::raw($query))->GroupBy('employee__profile.id')->get();
        $days = ['1'=>'31','2'=>'28','3'=>'31','4'=>'30','5'=>'31','6'=>'30','7'=>'31','8'=>'31','9'=>'30','10'=>'31','11'=>'30','12'=>'31'];
        $details = [
            'emp' => $em,
            'leave' => $leave,
            'cc' => $cc,
            'mon' => $mon,
            'days' => $days
        ];

        if($format != null){
            $data = [
                'format' => $format,
                'details' => $details
                ];
            $pdfFilePath = "Leave Register.pdf";
            $pdf = PDF::loadView('hr.leave_reg_print', $data);
            return $pdf->stream($pdfFilePath);
            return view('hr.leave_reg_print',$data);
        }
        else{
            $message="No Leave Application Exist!!";
            return redirect('/hr/leave/register/list')->with('error',$message);
        }
    }

    public function pf_register() {
        $data = [
            'layout' => 'layouts.main'
        ];
        return view('hr.pf_register',$data);
    }

    public function pf_register_api(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $year = $request->input('year');
        $month = $request->input('month');
        $date = Carbon::now();
        
        $listing = EmployeeProfile::leftJoin('employee__pfesi',function($join){
            $join->on('employee__pfesi.emp_id','=','employee__profile.id');
            $join->where('employee__pfesi.pf','<>',NULL);
        })
        ->leftJoin('payroll__salary',function($join) use($month,$year){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','salaryA');
            $join->whereMonth('payroll__salary.month',$month);
            $join->whereYear('payroll__salary.month',$year);
        })
        ->leftJoin('payroll as salaryA',function($join){
                        $join->on('salaryA.emp_id','=','employee__profile.id');
                        $join->where('salaryA.salary_type','salaryA');
                    })
            ->leftJoin('payroll as salaryC',function($join){
                        $join->on('salaryC.emp_id','=','employee__profile.id');
                        $join->where('salaryC.salary_type','salaryC');
                    })
           
            ->leftJoin('payroll__attendance',function($join) use($month,$year){
                $join->on('payroll__attendance.emp_id','=','employee__profile.id');
                $join->whereMonth('payroll__attendance.date',$month);
                $join->whereYear('payroll__attendance.date',$year);
                $join->where('payroll__attendance.status','P');
            })
            ->where('employee__pfesi.pf','<>',NULL)
            ->select(
                'employee__profile.name',
                'employee__profile.employee_number',
                'employee__pfesi.pf_no',
                DB::raw('(IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0")) as gross_wages '),
                DB::raw('(IFNULL(payroll__salary.effective_present,"0")) as effective_present'),
                DB::raw('(IFNULL(payroll__salary.effective_absent,"0")) as effective_absent'))->groupBy('employee__profile.id');

        if(!empty($serach_value)) {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('employee__pfesi.pf_no','like',"%".$serach_value."%")
                ->orwhere('payroll__salary.effective_absent','like',"%".$serach_value."%")
                ->orwhere('payroll__salary.effective_present','like',"%".$serach_value."%");
            });
        }
        
        if(isset($request->input('order')[0]['column'])) {

            $data = [
                'employee__profile.employee_number',
                'employee__profile.name',
                'employee__pfesi.pf_no',
                'payroll__salary.effective_present',
                'payroll__salary.effective_absent'
             
                
            ];

            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else{
            $listing->orderBy('employee__profile.id','desc');
        }
        $count = count( $listing->get()->toArray());
        $listing = $listing->offset($offset)->limit($limit)->get()->toArray(); 
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

     public function esi_register() {
        $data = [
            'layout' => 'layouts.main'
        ];
        return view('hr.esi_register',$data);
    }

    public function esi_register_api(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $year = $request->input('year');
        $month = $request->input('month');
        $date = Carbon::now();
        
        $listing = EmployeeProfile::leftJoin('employee__pfesi',function($join){
            $join->on('employee__pfesi.emp_id','=','employee__profile.id');
            $join->where('employee__pfesi.esi','<>',NULL);
        })
        ->leftJoin('payroll__salary',function($join) use($month,$year){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','salaryA');
            $join->whereMonth('payroll__salary.month',$month);
            $join->whereYear('payroll__salary.month',$year);
        })
        ->leftJoin('payroll as salaryA',function($join){
                        $join->on('salaryA.emp_id','=','employee__profile.id');
                        $join->where('salaryA.salary_type','salaryA');
                    })
            ->leftJoin('payroll as salaryC',function($join){
                        $join->on('salaryC.emp_id','=','employee__profile.id');
                        $join->where('salaryC.salary_type','salaryC');
                    })
           
            ->leftJoin('payroll__attendance',function($join) use($month,$year){
                $join->on('payroll__attendance.emp_id','=','employee__profile.id');
                $join->whereMonth('payroll__attendance.date',$month);
                $join->whereYear('payroll__attendance.date',$year);
                $join->where('payroll__attendance.status','P');
            })
            ->where('employee__pfesi.esi','<>',NULL)
            ->select(
                'employee__profile.name',
                'employee__profile.id',
                'employee__profile.employee_number',
                DB::raw('(IFNULL(payroll__salary.effective_present,"0")) as effective_present'),
                DB::raw('(IFNULL(payroll__salary.effective_absent,"0")) as effective_absent'),
                DB::raw('IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0") as gross_wages '),
                'employee__pfesi.esi_no')->groupBy('employee__profile.id');

        if(!empty($serach_value)) {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('employee__pfesi.esi_no','like',"%".$serach_value."%")
                ->orwhere('payroll__salary.effective_absent','like',"%".$serach_value."%")
                ->orwhere('payroll__salary.effective_present','like',"%".$serach_value."%");
            });
        }
        
        if(isset($request->input('order')[0]['column'])) {

            $data = [
                'employee__profile.name',
                'employee__profile.id',
                'employee__profile.employee_number',
                'payroll__salary.effective_present',
                'payroll__salary.effective_absent',
                'employee__pfesi.esi_no'
            ];

            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else{
            $listing->orderBy('employee__profile.id','desc');
        }
        $count = count( $listing->get()->toArray());
        $listing = $listing->offset($offset)->limit($limit)->get()->toArray(); 
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

   
}
