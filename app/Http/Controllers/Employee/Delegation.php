<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Employee\Delegates;
use App\Model\Employee\EmployeeProfile;
use App\Model\Employee\DelegationCompletion;
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

class Delegation extends Controller
{
	public function create_delegation(){
		$employee =EmployeeProfile::all();
        $data=array('layout' => 'layouts.main',
            'employee'=>$employee);
        return view('employee.Delegation.create_delegation',$data);
	}
	public function create_delegation_db(Request $request){
		try {

            $this->validate($request,
            [
                'empName'=>'required',
                'task_detail'=>'required',
                'assignby'=>'required',
                'assignonDate'=>'required',
                // 'deadline'=>'required'
                
            ],
            [
                'empName.required'=>'This Field is Required',
                'task_detail.required'=>'This Field is Required',
                'assignby.required'=>'This Field is Required',
                'assignonDate.required'=>'This Field is Required',
                // 'deadline.required'=>'This Field is Required'
            ]
        	);
            
            $timestamp = date('Y-m-d G:i:s');
            $del = Delegates::insertGetId(
                [
                    'id' => NULL,
                    'emp_id' =>$request->input('empName'),
                    'task_detail' => $request->input('task_detail'),
                    'assign_by' => $request->input('assignby'),
                    'assign_date'=>date('Y-m-d',strtotime($request->input('assignonDate'))),
                    'deadline'=>date('Y-m-d',strtotime($request->input('deadline'))),
                    'requirements'=>$request->input('spc_req'),
                    'created_by' => Auth::id(),
                    'created_at' => $timestamp
                ]
            );

            if(!empty($request->input('deadline'))){
                DelegationCompletion::insertGetId([
                    'id' => NULL,
                    'delegate_id' =>$del,
                    'completion_date' => date("Y-m-d",strtotime($request->input('deadline'))),
                    'score'=>1,
                    'created_by' => Auth::id(),
                    'created_at' => $timestamp
                ]);
            }

            if($del==NULL){
                DB::rollback();
                return redirect('/create/delegation')->with('error','Some Unexpected Error occurred.');  
            }
            return redirect('/create/delegation')->with('success','Delegation created successfully.');
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            return redirect('/create/delegation')->with('error','some error occurred'.$ex->getMessage());
        }
	}
	public function delegation_summary(){
        $data=array('layout' => 'layouts.main');
        return view('employee.Delegation.delegation_list',$data);
	}
	public function delegation_summary_api(Request $request){
		$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $user=Auth::user()->emp_id;
        // print_r($user);die;
        $userlog = Delegates::leftjoin('employee__profile','employee__profile.id','delegation.emp_id')
        ->leftjoin('delegation_completion_date','delegation_completion_date.delegate_id','delegation.id')
        
        ->select('delegation.id',
        	'delegation.task_detail',
        	'delegation.assign_date',
        	'delegation.deadline',
        	'delegation.requirements',
        	DB::raw('Concat(employee__profile.name,"(",employee__profile.employee_number,")")as empName'),DB::raw('(select Concat(e.name,"(",e.employee_number,
        		")") as assignbemp from employee__profile e where delegation.assign_by = e.id)as ass'),
            DB::raw('GROUP_CONCAT(completion_date ORDER BY delegation_completion_date.id DESC)as completion_date'),
            DB::raw('GROUP_CONCAT(delegation_status ORDER BY delegation_completion_date.id DESC)as dele_stat'),
            DB::raw('GROUP_CONCAT(final_status ORDER BY delegation_completion_date.id DESC)as final_stat')
        )->groupby('delegation.id')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value))
        {
            $userlog = $userlog->where('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation.task_detail','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation.assign_date','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation.deadline','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation.requirements','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['delegation.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'delegation.task_detail',
            'delegation.assign_date',
            'delegation.deadline',
            'delegation.requirements'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('delegation.id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function delegation_emp_summary(){
        $data=array('layout' => 'layouts.main');
        return view('employee.Delegation.delegation_emp_list',$data);
	}
	public function delegation_emp_summary_api(Request $request){
		$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $user=Auth::user()->emp_id;
        // print_r($user);die;
        $userlog = Delegates::leftjoin('employee__profile','employee__profile.id','delegation.emp_id')
        ->leftjoin('delegation_completion_date','delegation_completion_date.delegate_id','delegation.id')
        ->leftJoin('users','users.emp_id','employee__profile.id')
        ->where('delegation.emp_id',$user)
        ->select('delegation.id',
        	'delegation.task_detail',
        	'delegation.assign_date',
        	'delegation.deadline',
        	'delegation.requirements',
        	DB::raw('Concat(employee__profile.name,"(",employee__profile.employee_number,")")as empName'),DB::raw('(select Concat(e.name,"(",e.employee_number,
        		")") as assignbemp from employee__profile e where delegation.assign_by = e.id)as ass'),
            DB::raw('GROUP_CONCAT(completion_date ORDER BY delegation_completion_date.id DESC)as completion_date'),
            DB::raw('GROUP_CONCAT(delegation_status ORDER BY delegation_completion_date.id DESC)as dele_stat'),
            DB::raw('GROUP_CONCAT(final_status ORDER BY delegation_completion_date.id DESC)as final_stat')
        )->groupby('delegation.id')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value))
        {
            $userlog = $userlog->where('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation.task_detail','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation.assign_date','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation.deadline','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation.requirements','LIKE',"%".$serach_value."%")
                        ;
        }

        $count = count($userlog->get()->toArray());
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['delegation.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'delegation.task_detail',
            'delegation.assign_date',
            'delegation.deadline',
            'delegation.requirements'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('delegation.id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
	}
    public function completion_details($id){
        // DB::enableQueryLog();DISTINCT
        $del_detail = DelegationCompletion::where('delegate_id',$id)
        ->leftjoin('delegation','delegation.id','delegation_completion_date.delegate_id')
        ->select(DB::raw('GROUP_CONCAT((completion_date)) As completion_date'),DB::raw('GROUP_CONCAT(delegation_status)As completion_status'),DB::raw('GROUP_CONCAT(score) As completion_score'),'delegation.assign_date',DB::raw('YEARWEEK(delegation_completion_date.updated_at,3) AS UWEEK'),DB::raw('YEARWEEK(completion_date,3) AS WEEK'))->groupby('WEEK')
        ->get()->toArray();
        // print_r(DB::getQueryLog());die();
        $data=[
            'completion'=>$del_detail
        ];
        
        return view('employee.Delegation.completion_date',$data);
    }
    public function completion_date_db(Request $request){
        
            $error =[];

            if(empty($request->input('c_date'))){
                $error = array_merge($error,array('Completion Date is Required'));
            }
            if(count($error)>0){
                $data = [
                'error'=>$error];
                return json_encode($data);
            }
            $msg = [];
            // $error = array_merge($error,array($validation));
            $count_del_week = DelegationCompletion::where('delegate_id',$request->input('del_id'))->WhereRaw('YEARWEEK(completion_date,3) = YEARWEEK(NOW(),3)')->select('delegation_completion_date.*',DB::raw('YEARWEEK(completion_date,3) AS WEEK'))->get()->toArray();
            
            $timestamp = date('Y-m-d G:i:s');
            $presentweek =date("W");
            $enterdateweek =date("W",strtotime($request->input('c_date')));
            // $score = ($presentweek==$enterdateweek) ? '0' : '3';
            if(count($count_del_week) == 0){
                $score=1;
            }elseif (count($count_del_week) == 1) {
                $score=2;
            }elseif(count($count_del_week) >= 2){
                $score=3;
            }
            if($presentweek != $enterdateweek ){
                // && count($count_del_week) != 0
                $score = 1;
            }
            // if(count($count_del_week)>0){
            //     if($count_del_week[count($count_del_week)-1]['completion_date'] == date("Y-m-d",strtotime($request->input('c_date'))))
            //     {
            //         $score = 1 ;
            //     }
            // }
            $del = DelegationCompletion::insertGetId(
                [
                    'id' => NULL,
                    'delegate_id' =>$request->input('del_id'),
                    'completion_date' => date("Y-m-d",strtotime($request->input('c_date'))),
                    'score' => $score,
                    'created_by' => Auth::id(),
                    'created_at' => $timestamp
                ]
            );
            if($del==NULL){
                DB::rollback();
                $error = array_merge($error,array('Some Unexpected Error occurred.'));
            }else{
                $msg =['Completion Date Added successfully.'];
            }
            
            $data = ['msg'=>$msg,
            'error'=>$error];
           
            return json_encode($data);
        
    }
    public function completion_status_db(Request $request){
        try {

            $get_completion = DelegationCompletion::where('delegate_id',$request->input('stat_del_id'))->select('id')->orderBy('id', 'DESC')->first();
            $count_completion = DelegationCompletion::where('delegate_id',$request->input('stat_del_id'))->WhereRaw('YEARWEEK(completion_date,3) = YEARWEEK(NOW(),3)')->select('delegation_completion_date.*',DB::raw('YEARWEEK(completion_date,3) AS WEEK'))->get()->toArray();
            
            $error =[];
            if(empty($request->input('status'))){
                $error = array_merge($error,array('Status is Required'));
            }
            if(count($error)>0){
                $data = [
                'error'=>$error];
                return response()->json($data);
            }
            // $this->validate($request,['status'=>'required'],
            //     ['status.required'=> 'This field is required.']);
            if($request->input('status') == 'completed'){
                
                if(empty($request->input('detail'))){
                    $error = array_merge($error,array('Detail is Required'));
                }
                if(empty($request->file('img'))){
                    $error = array_merge($error,array('Image of Job Completion is Required'));
                }

                if(count($error)>0){
                    $data = [
                    'error'=>$error];
                    return response()->json($data);
                }
                // $this->validate($request,[
                //     'stat_del_id'=>'required',
                //     'detail'=>'required',
                //     'img'=>'required|max:'.CustomHelpers::getfilesize(),
                // ],[
                //     'stat_del_id.required'=>'This field is required',
                //     'detail.required'=> 'This Field is required.',
                //     'img.required'=> 'This Field is required.',
                //     'img.mimes'=> 'This Field accepts jpeg,png,jpg.'
                // ]);
                
                $score = '';
                if(count($count_completion)== 1){
                    $score = '1';
                }else if(count($count_completion)== 2){
                    $score = '2';
                }else if(count($count_completion)>= 3){
                    $score = '3';
                }
                
                $timestamp = date('Y-m-d G:i:s');
                $file = $request->file('img');

                $job_image ='';
                if(isset($file) || $file != null){
                    $destinationPath = public_path().'/upload/completed_job_image/';
                    $filenameWithExt = $request->file('img')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('img')->getClientOriginalExtension();
                    $job_image = $filename.'_'.time().'.'.$extension;
                    $path = $file->move($destinationPath, $job_image);
                }else{
                    $job_image = '';
                }

                $completed = DelegationCompletion::where('id',$get_completion['id'])->update([
                    'delegation_status'=>$request->input('status'),
                    'detail'=>$request->input('detail'),
                    'job_image'=>$job_image,
                    // 'score' => $score,
                    'updated_at' => $timestamp
                ]);
                if($completed == null){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));
                }else{
                    $msg =['Successfully Completion of your delegated task updated.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
               
                return response()->json($data);
            }else if($request->input('status') == 'not completed'){

                if(empty($request->input('new_c_date'))){
                    $error = array_merge($error,array('New Promised Completion Date is Required'));
                }

                if(count($error)>0){
                    $data = [
                    'error'=>$error];
                    return response()->json($data);
                }
                // $this->validate($request,
                //     ['new_c_date'=>'required'],[
                //     'new_c_date.required'=>'This field is required'
                // ]);
                $presentweek = date("W");
                $enterdateweek = date("W",strtotime($request->input('new_c_date')));
                if(count($count_completion) == 1){
                    $score=2;
                    $week_score=0;
                }elseif (count($count_completion) >= 2) {
                    $score=3;
                    $week_score=0;
                }
                if($presentweek != $enterdateweek){
                    $week_score=3;
                    $score =1;
                }
                // if($count_completion[count($count_completion)-1]['completion_date'] == date("Y-m-d",strtotime($request->input('new_c_date')))){
                //     $score=1;
                //     $week_score=0;
                // }                // $score = ($presentweek==$enterdateweek) ? '0' : '3';
                $timestamp = date('Y-m-d G:i:s');
                DelegationCompletion::where('id',$get_completion['id'])->update([
                    'delegation_status'=>$request->input('status'),
                    'week_score' => $week_score,
                    'updated_at' => $timestamp
                ]);
                $del = DelegationCompletion::insertGetId(
                    [
                        'id' => NULL,
                        'delegate_id' => $request->input('stat_del_id'),
                        'completion_date' => date("Y-m-d",strtotime($request->input('new_c_date'))),
                        'score' => $score,
                        'created_by' => Auth::id(),
                        'created_at' => $timestamp
                    ]
                );
                if($del==NULL){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));  
                }else{
                    $msg =['New Completion Date Added successfully.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
               
                return response()->json($data);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/delegation/summary')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function delegation_score(){
        $data=array('layout' => 'layouts.main');
        return view('employee.Delegation.delegation_score',$data);
    }
    public function delegation_score_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $week = $request->input('week');
        // DB::enableQueryLog();
        $task_score= DelegationCompletion::leftjoin('delegation','delegation.id','delegation_completion_date.delegate_id')
        ->leftJoin('employee__profile','employee__profile.id','delegation.emp_id')
        ->select(
            'delegation_completion_date.delegate_id',
            'employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail',
            DB::raw('((SUM(delegation_completion_date.week_score  = "1") / COUNT(delegation_completion_date.delegate_id)) * 100) as green'),
            DB::raw('((SUM(delegation_completion_date.week_score  = "2") / COUNT(delegation_completion_date.delegate_id)) * 100) as yellow'),
            DB::raw('((SUM(delegation_completion_date.week_score  = "3") / COUNT(delegation_completion_date.delegate_id)) * 100) as red'),
            DB::raw('COUNT(delegation_completion_date.delegate_id) as totaltask'),
            'delegation_completion_date.completion_date',
            DB::raw('YEARWEEK(delegation_completion_date.completion_date,3) AS WEEK'))->groupby('WEEK')->groupby('delegation.emp_id')
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($week) && $week!=""){
                $task_score->WhereRaw('YEARWEEK(delegation_completion_date.completion_date,3) = '.$week.'')
                ;    
        }else{
            $task_score->WhereRaw('YEARWEEK(delegation_completion_date.completion_date,3) = YEARWEEK(NOW(),3)')
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
            $data = ['WEEK','delegation_completion_date.delegate_id','employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail','comp','N_c','totaltask'
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
    public function report_bycompletiondate(){
        $data=array('layout' => 'layouts.main');
        return view('employee.Delegation.report_bycompletiondate',$data);
    }
    public function report_bycompletiondate_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $c_date = $request->input('c_date');

        // DB::enableQueryLog();
        $report= DelegationCompletion::leftjoin('delegation','delegation.id','delegation_completion_date.delegate_id')
        ->leftJoin('employee__profile','employee__profile.id','delegation.emp_id')
        ->select(
            'delegation_completion_date.delegate_id',
            'employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail',
            DB::raw('date_format(delegation.assign_date, "%d-%m-%Y")as assign_date'),
            DB::raw('date_format(delegation.deadline, "%d-%m-%Y")as deadline'),
            'delegation.requirements',
            DB::raw('date_format(delegation_completion_date.completion_date, "%d-%m-%Y")as completion_date'),
            'delegation_completion_date.delegation_status'
            )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($c_date) && $c_date!=""){
                $report->WhereRaw('date_format(delegation_completion_date.completion_date,"%d-%m-%Y") = "'.$c_date.'"')
                ;    
        }else{
            $report->WhereRaw('completion_date = CURDATE()')
            ;
        }
        if(!empty($serach_value)){
             $report =$report->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('task_detail','LIKE',"%".$serach_value."%")
                        ->orwhere('requirements','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation_status','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($report->get()->toArray());
        $report = $report->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['delegation_completion_date.delegate_id','delegation_completion_date.delegate_id','employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail','assign_date','deadline','requirements','completion_date','delegation_status'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $report->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $report->orderBy('delegation_completion_date.delegate_id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $report->get(); 
        return json_encode($array);
    }
    public function completed_evaluation(){
        $data=array('layout' => 'layouts.main');
        return view('employee.Delegation.evaluation_summary',$data);
    }
    public function pending_evaluation_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $report= DelegationCompletion::where('delegation_completion_date.delegation_status','completed')
        ->where('final_status','pending')
        ->leftjoin('delegation','delegation.id','delegation_completion_date.delegate_id')
        ->leftJoin('employee__profile','employee__profile.id','delegation.emp_id')
        ->select(
            'delegation_completion_date.id',
            'delegation_completion_date.delegate_id',
            'employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail',
            'delegation_completion_date.detail',
            'delegation_completion_date.job_image',
            DB::raw('date_format(delegation.assign_date, "%d-%m-%Y")as assign_date'),
            DB::raw('date_format(delegation.deadline, "%d-%m-%Y")as deadline'),
            'delegation.requirements',
            DB::raw('date_format(delegation_completion_date.completion_date, "%d-%m-%Y")as completion_date'),
            'delegation_completion_date.delegation_status'
            )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value)){
             $report =$report->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('task_detail','LIKE',"%".$serach_value."%")
                        ->orwhere('requirements','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation_status','LIKE',"%".$serach_value."%")
                        ->orwhere('detail','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($report->get()->toArray());
        $report = $report->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['delegation_completion_date.id','delegation_completion_date.delegate_id','delegation_completion_date.delegate_id','employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail','assign_date','deadline','requirements','completion_date','delegation_status','detail'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $report->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $report->orderBy('delegation_completion_date.id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $report->get(); 
        return json_encode($array);
    }
    public function done_evaluated_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $report= DelegationCompletion::where('final_status','<>','pending')
        ->leftjoin('delegation','delegation.id','delegation_completion_date.delegate_id')
        ->leftJoin('employee__profile','employee__profile.id','delegation.emp_id')
        ->select(
            'delegation_completion_date.delegate_id',
            'employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail',
            'delegation_completion_date.detail',
            'delegation_completion_date.job_image',
            DB::raw('date_format(delegation.assign_date, "%d-%m-%Y")as assign_date'),
            DB::raw('date_format(delegation.deadline, "%d-%m-%Y")as deadline'),
            'delegation.requirements',
            DB::raw('date_format(delegation_completion_date.completion_date, "%d-%m-%Y")as completion_date'),
            'delegation_completion_date.delegation_status','delegation_completion_date.final_status','delegation_completion_date.evaluation_reason'
            )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value)){
             $report =$report->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('task_detail','LIKE',"%".$serach_value."%")
                        ->orwhere('requirements','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation_status','LIKE',"%".$serach_value."%")
                        ->orwhere('detail','LIKE',"%".$serach_value."%")
                        ->orwhere('job_image','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($report->get()->toArray());
        $report = $report->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['delegation_completion_date.delegate_id','delegation_completion_date.delegate_id','employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail','assign_date','deadline','requirements','completion_date','delegation_status','job_image','detail'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $report->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $report->orderBy('delegation_completion_date.delegate_id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $report->get(); 
        return json_encode($array);
    }
    public function update_evaluated_db(Request $request){
        try {

            $error =[];

            if(empty($request->input('status'))){
                $error = array_merge($error,array('Status is Required'));
            }
            if(count($error)>0){
                $data = [
                'error'=>$error];
                return response()->json($data);
            }
            $timestamp = date('Y-m-d G:i:s');
            if($request->input('status') == 'completed'){
                $data = DelegationCompletion::where('id',$request->input('comp_id'))->get()->first();
                // $count_completion = DelegationCompletion::where('delegate_id',$data->delegate_id)->WhereRaw('YEARWEEK(completion_date,3) = YEARWEEK(NOW(),3)')->select('delegation_completion_date.*',DB::raw('YEARWEEK(completion_date,3) AS WEEK'))->get()->toArray();
                // if(count($count_completion)== 1){
                //     $score = '1';
                // }else if(count($count_completion)== 2){
                //     $score = '2';
                // }else if(count($count_completion)>= 3){
                //     $score = '3';
                // }
                
                $completed = DelegationCompletion::where('id',$request->input('comp_id'))->update([
                    'final_status'=>$request->input('status'),
                    'week_score'=>$data->score,
                    'updated_at' => $timestamp
                ]);
                if($completed == null){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));
                }else{
                    $msg =['Successfully Completion of delegated task updated.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
                return response()->json($data);
            }else if($request->input('status') == 'not completed'){
                
                if(empty($request->input('reason'))){
                    $error = array_merge($error,array('Reason is Required'));
                }
                if(count($error)>0){
                    $data = ['error'=>$error];
                    return response()->json($data);
                }
                
                $completed = DelegationCompletion::where('id',$request->input('comp_id'))
                ->update([
                    'final_status' => $request->input('status'),
                    'evaluation_reason' => $request->input('reason'),
                    'updated_at' => $timestamp
                ]);
                
                
                if($completed == null){
                    DB::rollback();
                    $error = array_merge($error,array('Some Unexpected Error occurred.'));
                }else{
                    $msg =['Successfully Status of delegated task updated.'];
                }
                $data = ['msg'=>$msg,
                'error'=>$error];
               
                return response()->json($data);
            }

        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/delegation/evaluation/summary')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function status_details($id){
        $data=array('layout' => 'layouts.main','id'=>$id);
        return view('employee.Delegation.status_details',$data);
    }
    public function status_details_api($id,Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQueryLog();
        $report= DelegationCompletion::where('delegate_id',$id)
        ->leftjoin('delegation','delegation.id','delegation_completion_date.delegate_id')
        ->leftJoin('employee__profile','employee__profile.id','delegation.emp_id')
        ->select('delegation_completion_date.id',
            'delegation_completion_date.delegate_id',
            'employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail',
            'delegation_completion_date.detail',
            'delegation_completion_date.job_image',
            DB::raw('date_format(delegation.assign_date, "%d-%m-%Y")as assign_date'),
            DB::raw('date_format(delegation.deadline, "%d-%m-%Y")as deadline'),
            'delegation.requirements',
            DB::raw('date_format(delegation_completion_date.completion_date, "%d-%m-%Y")as completion_date'),
            'delegation_completion_date.delegation_status','delegation_completion_date.final_status','delegation_completion_date.evaluation_reason'
            )
        // ->get()
        ;
        // print_r( DB::getQueryLog());die();
        if(!empty($serach_value)){
             $report =$report->where(function($query) use ($serach_value){
                    $query->where('employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('name','LIKE',"%".$serach_value."%")
                        ->orwhere('task_detail','LIKE',"%".$serach_value."%")
                        ->orwhere('requirements','LIKE',"%".$serach_value."%")
                        ->orwhere('delegation_status','LIKE',"%".$serach_value."%")
                        ->orwhere('detail','LIKE',"%".$serach_value."%")
                        ->orwhere('job_image','LIKE',"%".$serach_value."%")
                        ;
                });
         }
        $count = count($report->get()->toArray());
        $report = $report->offset($offset)->limit($limit);
        if(isset($request->input('order')[0]['column'])){
            $data = ['delegation_completion_date.delegate_id','delegation_completion_date.delegate_id','employee__profile.employee_number',
            'employee__profile.name',
            'delegation.task_detail','assign_date','deadline','requirements','completion_date','delegation_status','job_image','detail'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $report->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $report->orderBy('delegation_completion_date.delegate_id', 'desc');      
        }
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $report->get(); 
        return json_encode($array);
    }
}