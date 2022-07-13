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
use App\Model\Settings;
use App\Custom\CustomHelpers;
use App\Model\Users;
use Auth;
use File;
use DB;
use DateTime;

class Checklist extends Controller
{
	public function tasklist(){
        $employee =EmployeeProfile::all();
        $data=array('layout' => 'layouts.main',
            'employee'=>$employee);
        // $this->employee_task_status();
        return view('employee.checklist.tasklist',$data);
    }
    public function tasklist_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $emplo = $request->input('employee');
        // DB::enableQueryLog();
        $task_emp= EmployeeTask::leftJoin('employee__profile','employee__profile.id','emp_task.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->select(
            'emp_task.id',
            'employee__profile.employee_number',
            'employee__profile.name',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.emp_status',
            'emp_task.month',
            'emp_task.emp_status'
        )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($emplo)){
            $task_emp->where('emp_task.emp_id','=',$emplo)
           ;    
        }
        if(!empty($serach_value)){
             $task_emp =$task_emp->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        // ->orwhere('department','LIKE',"%".$serach_value."%")
                        ->orwhere('task_name','LIKE',"%".$serach_value."%")
                        ->orwhere('frequency','LIKE',"%".$serach_value."%")
                        ->orwhere('day','LIKE',"%".$serach_value."%")
                        ->orwhere('month','LIKE',"%".$serach_value."%")
                        ->orwhere('emp_status','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($task_emp->get()->toArray());
        $task_emp = $task_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['emp_task.id','employee__profile.employee_number',
            'employee__profile.name',
            // 'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month','emp_status'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $task_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $task_emp->orderBy('emp_task.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $task_emp->get(); 
        return json_encode($array);
    }
    public function sup_taskstatus_list(){
        $employee =EmployeeProfile::all();
        $holiday = Holiday::select(DB::raw('GROUP_CONCAT(date)as fulldate'))->get();
        $data=array('layout' => 'layouts.main','employee'=>$employee,
        	'holiday'=>$holiday[0]['fulldate']);
        return view('employee.checklist.task_statuslist',$data);
    }
    public function sup_taskstatus_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $emplo = $request->input('employee');
        // DB::enableQueryLog();
        $task_emp= EmployeeTaskStatus::where("task_status.status","<>","Pending")
        ->leftjoin('emp_task','emp_task.id','task_status.task_id')
        ->leftJoin('employee__profile','employee__profile.id','emp_task.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('users','users.email','employee__profile.email')
        ->select(
            'task_status.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month',
            'task_status.status',
            'task_status.score',
            'task_status.task_date as st_date',
            DB::raw('date(task_status.created_at)as created_at'),
            'users.id as userid'
        )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($emplo)){
            $task_emp->where('emp_task.emp_id','=',$emplo)
           ;    
        }
        if(!empty($serach_value)){
             $task_emp =$task_emp->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%")
                        ->orwhere('task_name','LIKE',"%".$serach_value."%")
                        ->orwhere('frequency','LIKE',"%".$serach_value."%")
                        ->orwhere('day','LIKE',"%".$serach_value."%")
                        ->orwhere('month','LIKE',"%".$serach_value."%")
                        ->orwhere('status','LIKE',"%".$serach_value."%")
                        ->orwhere('score','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($task_emp->get()->toArray());
        $task_emp = $task_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['task_status.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month',
            'task_status.status',
            'task_status.score','created_at','st_date'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $task_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $task_emp->orderBy('task_status.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $task_emp->get(); 
        return json_encode($array); 
    }

    public function sup_taskstatus_pending_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $emplo = $request->input('employee');
        // DB::enableQueryLog();
        $task_emp= EmployeeTaskStatus::where("task_status.status","Pending")
        ->leftjoin('emp_task','emp_task.id','task_status.task_id')
        ->leftJoin('employee__profile','employee__profile.id','emp_task.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('users','users.email','employee__profile.email')
        ->select(
            'task_status.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month',
            'task_status.status',
            'task_status.score',
            'task_status.task_date as st_date',
            DB::raw('date(task_status.created_at)as created_at'),
            'users.id as userid'
        )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($emplo)){
            $task_emp->where('emp_task.emp_id','=',$emplo)
           ;    
        }
        if(!empty($serach_value)){
             $task_emp =$task_emp->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%")
                        ->orwhere('task_name','LIKE',"%".$serach_value."%")
                        ->orwhere('frequency','LIKE',"%".$serach_value."%")
                        ->orwhere('day','LIKE',"%".$serach_value."%")
                        ->orwhere('month','LIKE',"%".$serach_value."%")
                        ->orwhere('status','LIKE',"%".$serach_value."%")
                        ->orwhere('score','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($task_emp->get()->toArray());
        $task_emp = $task_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['task_status.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month',
            'task_status.status',
            'task_status.score','created_at','st_date'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $task_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $task_emp->orderBy('task_status.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $task_emp->get(); 
        return json_encode($array); 
    }
    public function emp_taskstatus_list(){
    	$holiday = Holiday::select(DB::raw('GROUP_CONCAT(date)as fulldate'))->get();
        $data=array('layout' => 'layouts.main','holiday'=>$holiday[0]['fulldate']);
        return view('employee.checklist.emptask_statuslist',$data);
    }
    public function emp_taskstatus_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        // DB::enableQueryLog();
        $task_emp= EmployeeTaskStatus::where("users.emp_id",Auth::id())
        ->where("task_status.status","<>","Pending")
        ->leftjoin('emp_task','emp_task.id','task_status.task_id')
        ->leftJoin('employee__profile','employee__profile.id','emp_task.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('users','users.email','employee__profile.email')
        ->select(
            'task_status.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month',
            'task_status.status',
            'task_status.score','task_status.task_date as st_date',
            DB::raw('date(task_status.created_at)as created_at')
        )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value)){
             $task_emp =$task_emp->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%")
                        ->orwhere('task_name','LIKE',"%".$serach_value."%")
                        ->orwhere('frequency','LIKE',"%".$serach_value."%")
                        ->orwhere('day','LIKE',"%".$serach_value."%")
                        ->orwhere('month','LIKE',"%".$serach_value."%")
                        ->orwhere('status','LIKE',"%".$serach_value."%")
                        ->orwhere('score','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($task_emp->get()->toArray());
        $task_emp = $task_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['task_status.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month',
            'task_status.status',
            'task_status.score','created_at','st_date'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $task_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $task_emp->orderBy('task_status.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $task_emp->get(); 
        return json_encode($array); 
    }
    public function emp_taskstatus_pending_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        // DB::enableQueryLog();
        $task_emp= EmployeeTaskStatus::where("users.emp_id",Auth::id())
        ->where("task_status.status","Pending")
        ->leftjoin('emp_task','emp_task.id','task_status.task_id')
        ->leftJoin('employee__profile','employee__profile.id','emp_task.emp_id')
        ->leftJoin('department','employee__profile.department_id','department.id')
        ->leftjoin('users','users.email','employee__profile.email')
        ->select(
            'task_status.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month',
            'task_status.status',
            'task_status.score','task_status.task_date as st_date',
            DB::raw('date(task_status.created_at)as created_at')
        )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value)){
             $task_emp =$task_emp->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('department','LIKE',"%".$serach_value."%")
                        ->orwhere('task_name','LIKE',"%".$serach_value."%")
                        ->orwhere('frequency','LIKE',"%".$serach_value."%")
                        ->orwhere('day','LIKE',"%".$serach_value."%")
                        ->orwhere('month','LIKE',"%".$serach_value."%")
                        ->orwhere('status','LIKE',"%".$serach_value."%")
                        ->orwhere('score','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($task_emp->get()->toArray());
        $task_emp = $task_emp->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['task_status.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'department.department',
            'emp_task.task_name',
            'emp_task.frequency',
            'emp_task.day',
            'emp_task.task_date',
            'emp_task.month',
            'task_status.status',
            'task_status.score','created_at','st_date'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $task_emp->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $task_emp->orderBy('task_status.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $task_emp->get(); 
        return json_encode($array); 
    }
    public function future_task_status() {
        
        $tasks = EmployeeTask::all();

        // $presentweek = $this->getpresentWeekDates();
        // print_r($presentweek);
        
        $holiday =Holiday::get([DB::raw('GROUP_CONCAT(date)as fulldate')])->toArray();
        $arr_holiday = explode(',', $holiday[0]['fulldate']);
        $tomorrow = date('Y-m-d',strtotime("+1 days"));
        $day_a_tom = date('Y-m-d',strtotime("+2 days"));
        $timestamp = date('Y-m-d G:i:s');

        foreach ($tasks as $task) {
            // DB::enableQueryLog();
            $status = EmployeeTaskStatus::where('task_status.task_id',$task['id'])
            ->select('task_date','status')
            ->orderBy('id', 'DESC')->first();
            // $queries = DB::getQueryLog();
            // print_r($queries);die();
            $status_task_date = $status['task_date'];
            $task_status= $status['status'];
                /**
                * daily
                * checking for daily and emp is active or not
                * if status pending then do nothing
                * else check for last date present if yes
                * check next day of last date not holiday or sunday
                * else check day after next day not holiday or sunday
                *
                * if last date not present in table then check for tomorrow and day after tomorrow
                **/

                /**
                * weekly
                * checking for weekly and emp is active or not
                * if status pending then do nothing
                * else check for last date present if yes
                * check next week date of last date not holiday ->run
                * if holiday ->check prev day of week ,holiday or sunday ,if not run
                * else less 2 days from week
                *
                * if last date not present in table then check for day and get date of present week and next week
                * check if present week date is greater current date
                **/

                if($task['frequency'] == "daily" && $task['emp_status'] == "active"){
                    
                    if($task_status == "Pending"){

                    }else{
                        if($status_task_date){
                            $next_date = date('Y-m-d',strtotime("+1 days", strtotime($status_task_date)));
                            if(!in_array($next_date, $arr_holiday) && date("l",strtotime($next_date)) != 'Sunday'){
                            // insert query tomorrow;
                            $tstatus = EmployeeTaskStatus::insertGetId([
                                'id'=>NULL,
                                'task_id'=>$task['id'],
                                'status'=>'Pending',
                                'score'=>'',
                                'task_date'=>$next_date,
                                'created_at'=>$timestamp
                            ]);
                            }else{
                                $after_next = date('Y-m-d',strtotime("+2 days", strtotime($status_task_date)));
                                if(!in_array($after_next, $arr_holiday) && date("l",strtotime($after_next)) != 'Sunday'){
                                    // insert query day after tomorrow;
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=>$after_next,
                                        'created_at'=>$timestamp
                                    ]);
                                }
                            }
                        }else{
                            if(!in_array($tomorrow, $arr_holiday) && date("l",strtotime($tomorrow)) != 'Sunday'){
                                // insert query tomorrow;
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$tomorrow,
                                    'created_at'=>$timestamp
                                ]);
                            }else{
                                if(!in_array($day_a_tom, $arr_holiday) && date("l",strtotime($day_a_tom)) != 'Sunday'){
                                // insert query day after tomorrow;
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=>$day_a_tom,
                                        'created_at'=>$timestamp
                                    ]);
                                }
                            }
                        }
                        
                    }

                }else if($task['frequency'] == "weekly" && $task['emp_status'] == "active")
                {   
                    if($task_status == "Pending"){

                    }else{
                        if($status_task_date)
                        {
                            // last date
                            $last_date = $status_task_date;
                            $after_week = date('Y-m-d',strtotime("+1 week", strtotime($last_date)));
                            
                            // print_r();die();
                            if(!in_array($after_week, $arr_holiday))
                            {
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$after_week,
                                    'created_at'=>$timestamp
                                ]);
                            }else
                            {
                                $min_one_day = date('Y-m-d',strtotime("-1 days", strtotime($last_date)));
                                if(!in_array($min_one_day, $arr_holiday)&& date("l",strtotime($min_one_day)) != 'Sunday'){
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=> $min_one_day,
                                        'created_at'=>$timestamp
                                    ]);
                                }else{
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=> date('Y-m-d',strtotime("-1 days", strtotime($min_one_day))),
                                        'created_at'=>$timestamp
                                    ]);
                                }
                            }
                             
                        }else
                        {
                            //first time insertion
                            $dayArr =["Mon"=>"Monday",
                                "monday"=>"Monday",
                                "Tue"=>"Tuesday",
                                "tuesday"=>"Tuesday",
                                "Wed"=>"Wednesday",
                                "wednesday"=>"Wednesday",
                                "Thu"=>"Thursday",
                                "thursday"=>"Thursday",
                                "Fri"=>"Friday",
                                "friday"=>"Friday",
                                "Sat"=>"Saturday",
                                "saturday"=>"Saturday"
                            ];
                            $day = $dayArr[$task['day']];
                            $next_day = date("Y-m-d",strtotime("next ".$day.""));
                            $present_day = date("Y-m-d",strtotime("this ".$day.""));
                            
                            if($present_day > $timestamp){
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$present_day,
                                    'created_at'=>$timestamp
                                ]);
                            }else{
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$next_day,
                                    'created_at'=>$timestamp
                                ]);
                            } 
                        }
                    }
                }else if($task['frequency'] == "monthly" && $task['emp_status'] == "active")
                {    
                    if($task_status == "Pending"){

                    }else
                    {   
                        if($status_task_date)
                        {
                            $last_date = $status_task_date;
                            $after_month = date('Y-m-d',strtotime("+1 months", strtotime($last_date)));
                            if(!in_array($after_month, $arr_holiday) && date("l",strtotime($after_month)) != 'Sunday')
                            {
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$after_month,
                                    'created_at'=>$timestamp
                                ]);
                            }else
                            {
                                $one_min_month = date('Y-m-d',strtotime("-1 days", strtotime($after_month)));
                                if(!in_array($one_min_month, $arr_holiday) && date("l",strtotime($one_min_month)) != 'Sunday')
                                {
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=>$one_min_month,
                                        'created_at'=>$timestamp
                                    ]);
                                }else{
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=> date('Y-m-d',strtotime("-1 days", strtotime($one_min_month))),
                                        'created_at'=>$timestamp
                                    ]);
                                }
                                
                            }
                        }else{
                            $month = date('Y-m');
                            $date = $month.'-'.$task['task_date'];
                            
                            $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$date,
                                    'created_at'=>$timestamp
                                ]);
                        }

                    }
                }else if($task['frequency'] == "fortnightly" && $task['emp_status'] == "active")
                {

                    if($task_status == "Pending"){

                    }else{
                        if($status_task_date){
                            $last_date = $status_task_date;
                            $fortnight = date("Y-m-d",strtotime("+2 week", strtotime($last_date)));
                            if(!in_array($fortnight, $arr_holiday) && date("l",strtotime($fortnight)) != 'Sunday')
                            {
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$fortnight,
                                    'created_at'=>$timestamp
                                ]);
                            }else{
                                $one_min_fornight = date("Y-m-d",strtotime("-1 days", strtotime($fortnight)));
                                if(!in_array($one_min_fornight, $arr_holiday) && date("l",strtotime($one_min_fornight)) != 'Sunday')
                                {
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=>$one_min_fornight,
                                        'created_at'=>$timestamp
                                    ]);
                                }else{
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=> date("Y-m-d",strtotime("-1 days", strtotime($one_min_fornight))),
                                        'created_at'=>$timestamp
                                    ]);
                                }
                            }
                        }else{
                            $month = date('Y-m');
                            $date = $month.'-'.$task['task_date'];
                            
                           $tstatus = EmployeeTaskStatus::insertGetId([
                                'id'=>NULL,
                                'task_id'=>$task['id'],
                                'status'=>'Pending',
                                'score'=>'',
                                'task_date'=>$date,
                                'created_at'=>$timestamp
                            ]); 
                        }
                    }
                }
                else if($task['frequency'] == "quarterly" && $task['emp_status'] == "active"){
                // print_r($quarter);die();
                    if($task_status == "Pending"){

                    }else{
                        if($status_task_date){
                            $last_date = $status_task_date;
                            $quarter = date('Y-m-d',strtotime("+3 months", strtotime($last_date)));
                            //quater date not in holiday and dont be sunday
                            if(!in_array($quarter, $arr_holiday) && date("l",strtotime($quarter)) != 'Sunday')
                            {
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$quarter,
                                    'created_at'=>$timestamp
                                ]);
                            }else{
                                // day before
                                $one_min_quater = date('Y-m-d',strtotime("-1 days", strtotime($quarter)));
                                if(!in_array($one_min_quater, $arr_holiday) && date("l",strtotime($one_min_quater)) != 'Sunday')
                                {
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=>$one_min_quater,
                                        'created_at'=>$timestamp
                                    ]);
                                }else{
                                    // before yesterday
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=> date('Y-m-d',strtotime("-1 days", strtotime($one_min_quater))),
                                        'created_at'=>$timestamp
                                    ]);
                                }
                            }
                        }else{
                            // first record
                            $year = date('Y');
                            $nmonth = date("m", strtotime($task['month']));
                            $date = $year.'-'.$nmonth.'-'.$task['task_date'];
                            
                            $tstatus = EmployeeTaskStatus::insertGetId([
                                'id'=>NULL,
                                'task_id'=>$task['id'],
                                'status'=>'Pending',
                                'score'=>'',
                                'task_date'=> $date,
                                'created_at'=>$timestamp
                            ]);
                        }
                    }
                }
                else if($task['frequency'] == "half yearly" && $task['emp_status'] == "active"){
                    
                    if($task_status == "Pending"){

                    }else{
                        if($status_task_date){
                            $last_date = $status_task_date;
                            $half = date('Y-m-d',strtotime("+6 months", strtotime($last_date)));
                            //half date not in holiday and dont be sunday
                            if(!in_array($half, $arr_holiday) && date("l",strtotime($half)) != 'Sunday')
                            {
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$half,
                                    'created_at'=>$timestamp
                                ]);
                            }else{
                                // day before
                                $one_min_half = date('Y-m-d',strtotime("-1 days", strtotime($half)));
                                if(!in_array($one_min_half, $arr_holiday) && date("l",strtotime($one_min_half)) != 'Sunday')
                                {
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=>$one_min_half,
                                        'created_at'=>$timestamp
                                    ]);
                                }else{
                                    // before yesterday
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=> date("Y-m-d",strtotime("-1 days", strtotime($one_min_half))),
                                        'created_at'=>$timestamp
                                    ]);
                                }
                            }
                        }else{
                            // first record
                            $year = date('Y');
                            $nmonth = date("m", strtotime($task['month']));
                            $date = $year.'-'.$nmonth.'-'.$task['task_date'];
                            
                            $tstatus = EmployeeTaskStatus::insertGetId([
                                'id'=>NULL,
                                'task_id'=>$task['id'],
                                'status'=>'Pending',
                                'score'=>'',
                                'task_date'=> $date,
                                'created_at'=>$timestamp
                            ]);
                        }
                    }
                }
                else if($task['frequency'] == "annually" && $task['emp_status'] == "active"){
                    
                    if($task_status == "Pending"){

                    }else{
                        if($status_task_date){
                            $last_date = $status_task_date;
                            $year = date('Y-m-d',strtotime("+12 months", strtotime($last_date)));
                            //yealy date not in holiday and dont be sunday
                            if(!in_array($year, $arr_holiday) && date("l",strtotime($year)) != 'Sunday')
                            {
                                $tstatus = EmployeeTaskStatus::insertGetId([
                                    'id'=>NULL,
                                    'task_id'=>$task['id'],
                                    'status'=>'Pending',
                                    'score'=>'',
                                    'task_date'=>$year,
                                    'created_at'=>$timestamp
                                ]);
                            }else{
                                // day before
                                $one_min_year = date('Y-m-d',strtotime("-1 days", strtotime($year)));
                                if(!in_array($one_min_year, $arr_holiday) && date("l",strtotime($one_min_year)) != 'Sunday')
                                {
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=>$one_min_year,
                                        'created_at'=>$timestamp
                                    ]);
                                }else{
                                    // before yesterday
                                    $tstatus = EmployeeTaskStatus::insertGetId([
                                        'id'=>NULL,
                                        'task_id'=>$task['id'],
                                        'status'=>'Pending',
                                        'score'=>'',
                                        'task_date'=> date('Y-m-d',strtotime("-1 days", strtotime($one_min_year))),
                                        'created_at'=>$timestamp
                                    ]);
                                }
                            }
                        }else{
                            // first record
                            $year = date('Y');
                            $nmonth = date("m", strtotime($task['month']));
                            $date = $year.'-'.$nmonth.'-'.$task['task_date'];
                            
                            $tstatus = EmployeeTaskStatus::insertGetId([
                                'id'=>NULL,
                                'task_id'=>$task['id'],
                                'status'=>'Pending',
                                'score'=>'',
                                'task_date'=> $date,
                                'created_at'=>$timestamp
                            ]);
                        }
                    }
                }
                
        }
        // die();
    }
   
    public function getpresentWeekDates()
    {
        $lastWeek = array();
     
        $prevMon = abs(strtotime("next monday"));
        $currentDate = abs(strtotime("today"));
        $seconds = 86400; //86400 seconds in a day
     
        $dayDiff = ceil( ($currentDate-$prevMon)/$seconds ); 
     
        if( $dayDiff < 7 )
        {
            $dayDiff += 1; //if it's monday the difference will be 0, thus add 1 to it
            $prevMon = strtotime( "next monday", strtotime("+$dayDiff day") );
        }
     
        $prevMon = date("Y-m-d",$prevMon);
     
        // create the dates from Monday to Sunday
        for($i=0; $i<7; $i++)
        {
            $d = date("Y-m-d", strtotime( $prevMon." + $i day") );
            $lastWeek[]=$d;
        }
     
        return $lastWeek;
    }
    public function task_score(){
        $data=array('layout' => 'layouts.main');
        return view('employee.checklist.task_score',$data);
    }
    public function task_score_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $week = $request->input('week');
        // print_r($week);die();
        // DB::SenableQueryLog();
        $task_score= EmployeeTaskStatus::leftjoin('emp_task','emp_task.id','task_status.task_id')
        ->leftJoin('employee__profile','employee__profile.id','emp_task.emp_id')
        ->select(
            'task_status.task_id',
            'employee__profile.employee_number',
            'employee__profile.name',
            'emp_task.task_name',
            DB::raw('((SUM(task_status.score = "Work Done") / COUNT(task_status.task_id)) * 100) as Done'),
            DB::raw('((SUM(task_status.score = "Work Not Done") / COUNT(task_status.task_id)) * 100) as NotDone'),
            DB::raw('((SUM(task_status.score = "Late") / COUNT(task_status.task_id)) * 100) as Late'),
            DB::raw('((SUM(task_status.score = "Not Required") / COUNT(task_status.task_id)) * 100) as N_r'),
            DB::raw('COUNT(task_status.task_id) as totaltask'),
            'task_status.task_date',
            DB::raw('YEARWEEK(task_status.task_date,3) AS WEEK'))->groupby('WEEK')->groupby('emp_task.emp_id')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($week) && $week!=""){
                $task_score->WhereRaw('YEARWEEK(task_status.task_date,3) = '.$week.'')
                ;    
        }else{
            $task_score->WhereRaw('YEARWEEK(task_status.task_date,3) = YEARWEEK(NOW(),3)')
            ;
        }
        if(!empty($serach_value)){
             $task_score =$task_score->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('task_name','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($task_score->get()->toArray());
        $task_score = $task_score->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['WEEK','task_status.task_id','employee__profile.employee_number',
            'employee__profile.name',
            'emp_task.task_name','Done','NotDone','Late','N_r'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $task_score->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $task_score->orderBy('WEEK', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $task_score->get(); 
        return json_encode($array);
    }

    public function update_supstatus($status,$id){
        try {

            $task = EmployeeTaskStatus::where('task_status.id',$id)
            ->leftjoin('emp_task','emp_task.id','task_status.task_id')
            ->leftJoin('employee__profile','employee__profile.id','emp_task.emp_id')
            ->leftJoin('department','employee__profile.department_id','department.id')
            ->leftjoin('users','users.email','employee__profile.email')
            ->select('task_status.task_date','users.id as userid')
            ->get()->first();
            
            
            if($task['userid']!=null){
                $score ="";
                if($status == "Done"){
                    $current= date("Y-m-d");
                    if($current != $task['task_date']){
                        $score = "Late";
                    }else{
                        $score = "Work Done";
                    }
                    }else if($status == "Not Done"){
                        $score = "Work Not Done";
                    }else if($status == "Not Required"){
                        $score = "Not Required";
                    }

                    $timestamp = date('Y-m-d G:i:s');
                    $tstatus = EmployeeTaskStatus::where('id',$id)->update([
                        'status'=>$status,
                        'score'=> $score,
                        'created_by'=>Auth::id(),
                        'updated_at'=>$timestamp
                    ]);
            
                    if($tstatus==NULL) 
                    {
                        DB::rollback();
                        return redirect('/checklist/superadmin/task/status/list')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                   
                        return redirect('/checklist/superadmin/task/status/list')->with('success','Task Status Updated.');
                    }
            }else{
                $score ="";
                    if($status == "Done"){
                        $diff = date_diff(date_create($task['task_date']),date_create(date("Y-m-d")))->format("%a");
                        if($diff > 1){
                         $score = "Late";
                        }else{
                            $score = "Work Done";
                        }
                    }else if($status == "Not Done"){
                        $score = "Work Not Done";
                    }else if($status == "Not Required"){
                        $score = "Not Required";
                    }

                    $timestamp = date('Y-m-d G:i:s');
                    $tstatus = EmployeeTaskStatus::where('id',$id)->update([
                        'status'=>$status,
                        'score'=> $score,
                        'created_by'=>Auth::id(),
                        'updated_at'=>$timestamp
                    ]);
            
                    if($tstatus==NULL) 
                    {
                        DB::rollback();
                        return redirect('/checklist/superadmin/task/status/list')->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                   
                        return redirect('/checklist/superadmin/task/status/list')->with('success','Task Status Updated.');
                    }

            }
            
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/checklist/superadmin/task/status/list')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function update_empstatus($status,$id){
        try {

            $task = EmployeeTaskStatus::where('id',$id)->get()->first();
            // print_r($task->status_date); die();
                $score ="";
                if($status == "Done"){
                    $current= date("Y-m-d");
                if($current != $task['task_date']){
                    $score = "Late";
                }else{
                    $score = "Work Done";
                }
                }else if($status == "Not Done"){
                    $score = "Work Not Done";
                }else if($status == "Not Required"){
                    $score = "Not Required";
                }

                $timestamp = date('Y-m-d G:i:s');
                $tstatus = EmployeeTaskStatus::where('id',$id)->update([
                    'status'=>$status,
                    'score'=> $score,
                    'created_by'=>Auth::id(),
                    'updated_at'=>$timestamp
                ]);
                 
            if($tstatus==NULL) 
            {
               DB::rollback();
                return redirect('/checklist/employee/status/list')->with('error','Some Unexpected Error occurred.');
            }
            else{
                   
                return redirect('/checklist/employee/status/list')->with('success','Task Status Updated.');
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/checklist/employee/status/list')->with('error','some error occurred'.$ex->getMessage());
        }
    }
// function to update task if not done in frequency
    public function Auto_update(){
        // DB::enableQueryLog();
        $pending_task = EmployeeTaskStatus::where("task_status.status","Pending")
        ->whereRaw("DATE(task_status.task_date) <= DATE(NOW())")
        ->leftjoin('emp_task','emp_task.id','task_status.task_id')
        ->leftJoin('employee__profile','employee__profile.id','emp_task.emp_id')
        ->leftjoin('users','users.email','employee__profile.email')
        ->select('task_status.id',
            'users.id as userid',
            'emp_task.frequency',
            'task_status.task_date')
        ->get()->toArray();
        // $queries = DB::getQueryLog();print_r($pending_task);
        $presentweek = $this->getpresentWeekDates();
        $timestamp =date('Y-m-d G:i:s');
        $current = date('Y-m-d');
        foreach ($pending_task as $task) {
            /**
            
            if frequency = daily 
            check for users
                current date is task date 
            check for non user 
                taskdate +1 day 
            if weekly, get week last date ,current date less than week last do nothing else upd
            monthly -> present date >= last month date ->fire
            fortnightly -> (task date+2week)-1 day) =< present date -> fire
            quaterly-> (taskdate+3week)-1day)=< present date ->fire
            **/
            // user
            if($task['userid'] != null){
                if($task['frequency'] == 'daily'&& $task['task_date'] == $current){

                    $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                    'status'=>"Not Done",
                    'score'=> "Work Not Done",
                    'updated_at'=>$timestamp
                    ]);

                }else if($task['frequency'] == 'weekly'){
                    $week_last_date = end($presentweek);
                    if($current < $week_last_date){

                    }else{
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);
                    } 
                }else if($task['frequency'] == 'monthly'){
                    //last date of the month using y-m-t
                    $lastDayThisMonth = date("Y-m-t"); 
                    if($current < $lastDayThisMonth){
                        
                    }else{ 
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);
                    }
                }else if($task['frequency'] == 'fortnightly'){
                    //date= 10-4-20 -> 17-4-20
                    $next_week = strtotime("+1 week", strtotime($task['task_date']));
                    // 17-4-20 ->upcoming sunday date
                    $fortnight = date("Y-m-d",strtotime("next sunday",$next_week));
                   
                    if($current < $fortnight){
                        
                    }else{
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);
                    }
                }else if($task['frequency'] == 'quarterly'){
                    $year = date('Y');
                    $quarter =$year.'-03-31';
                    $quarter2 =$year.'-06-30';
                    $quarter3 =$year.'-09-30';
                    $quarter4 =$year.'-12-31';

                    if($task['task_date'] <= $quarter){
                        if($current < $quarter){
                            
                        }else{
                            
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }else if($task['task_date'] > $quarter && $task['task_date'] <= $quarter2){
                        if($current < $quarter2){
                         
                        }else{
                            
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }else if($task['task_date'] > $quarter2 && $task['task_date'] <= $quarter3){
                        if($current < $quarter3){
                            
                        }else{
                            
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }else if($task['task_date'] > $quarter3 && $task['task_date'] <= $quarter4){
                       if($current < $quarter4){
                            
                        }else{
                            
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        } 
                    }
                }else if($task['frequency'] == 'half yearly'){
                    $year = date('Y');
                    $quarter1 =$year.'-06-30';
                    $quarter2 =$year.'-12-31';
                    if($task['task_date'] <= $quarter1){
                        if($current < $quarter1){
                        
                        }else{
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }else if($task['task_date'] > $quarter1 && $task['task_date'] <= $quarter2){
                        if($current < $quarter2){
                        
                        }else{
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }
                    
                }else if($task['frequency'] == 'annually'){
                    $year = date('Y');
                    $quarter =$year.'-12-31';
                   
                    if($current < $quarter){
                        
                    }else{
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);
                    }
                }
            }else{
                // non-user
                if($task['frequency'] == 'daily'){
                    $add_day = $date =date("Y-m-d",strtotime("+1 days", strtotime($task['task_date'])));
                    if($current<$add_day){

                    }else{
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);  
                    }
                }else if($task['frequency'] == 'weekly'){
                
                    $next_mon = date("Y-m-d",strtotime("next monday"));
                    if($current < $next_mon){

                    }else{
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);
                    }
                }else if($task['frequency'] == 'monthly'){
                    
                    $firstDayNextMonth = date('Y-m-d', strtotime('first day of next month'));
                    if($current < $firstDayNextMonth){

                    }else{
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);
                    }
                }else if($task['frequency'] == 'fortnightly'){
                    //date= 10-4-20 -> 17-4-20
                    $next_week = strtotime("+1 week", strtotime($task['task_date']));
                    // 17-4-20 ->upcoming monday date
                    $fortnight = date("Y-m-d",strtotime("next Monday",$next_week));
                   
                    if($current < $fortnight){
                        
                    }else{
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);
                    }
                }else if($task['frequency'] == 'quarterly'){
                    $year = date('Y');
                    $next_yr = date('Y', strtotime('+1 year'));
                    $quarter =$year.'-04-01';
                    $quarter2 =$year.'-07-01';
                    $quarter3 =$year.'-10-01';
                    $quarter4 =$next_yr.'-01-01';
                    if($task['task_date'] <= $quarter){
                        if($current < $quarter){
                            
                        }else{
                            
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }else if($task['task_date'] > $quarter && $task['task_date'] <= $quarter2){
                        if($current < $quarter2){
                         
                        }else{
                            
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }else if($task['task_date'] > $quarter2 && $task['task_date'] <= $quarter3){
                        if($current < $quarter3){
                            
                        }else{
                            
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }else if($task['task_date'] > $quarter3 && $task['task_date'] <= $quarter4){
                       if($current < $quarter4){
                            
                        }else{
                            
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        } 
                    }
                }else if($task['frequency'] == 'half yearly'){
                    $year = date('Y');
                    $next_yr = date('Y', strtotime('+1 year'));
                    $quarter1 =$year.'-07-01';
                    $quarter2 =$next_yr.'-01-01';
                    if($task['task_date'] <= $quarter1){
                        if($current < $quarter1){
                        
                        }else{
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }else if($task['task_date'] > $quarter1 && $task['task_date'] <= $quarter2){
                        if($current < $quarter2){
                        
                        }else{
                            $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                                'status'=>"Not Done",
                                'score'=> "Work Not Done",
                                'updated_at'=>$timestamp
                            ]);
                        }
                    }
                        
                }else if($task['frequency'] == 'annually'){
                    $next_yr = date('Y', strtotime('+1 year'));
                    $quarter =$next_yr.'-01-01';
                    
                    if($current < $quarter){
                        
                    }else{
                        $tstatus = EmployeeTaskStatus::where('id',$task['id'])->update([
                            'status'=>"Not Done",
                            'score'=> "Work Not Done",
                            'updated_at'=>$timestamp
                        ]);
                    }
                }
            }
        }
       
    }
}