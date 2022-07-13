<?php

namespace App;
namespace App\Http\Controllers;
use App\Model\ItemCategory;
use App\Model\jobDetails;
use Route;
use App\Model\SectionRight;
use App\Model\UserSectionRight;
use DateTime;
use App\Imports\Import;
use Illuminate\Validation\Rule;
use App\Model\IoType;
use App\Model\HR\HR_Leave;
use App\Model\HR\HR_Events;
use App\Model\Vehicle;
use App\Model\PoNumber;
use App\Model\Client_po_party;
use App\Model\Printing;
use App\Model\Tax_Invoice;
use App\Model\Tax_Dispatch;
use App\Model\HR\Announcements;
use App\Model\DispatchPlan;
use App\Model\Holiday;
use App\Model\Dispatch_mode;
use App\Model\RequestPermission;
use App\Model\WidgetPhotos;
use App\Model\Payment;
use App\Model\Tax;
use App\Model\IORequestEdit;
use App\Model\Tax_Print;
use App\Model\Unit_of_measurement;
use App\Model\Challan_per_io;
use App\Model\Delivery_challan;
use App\Model\Goods_Dispatch;
use App\Model\Settings;
use App\Model\ElementFeeder;
use App\Model\Hsn;
use App\Model\Users;
use App\Model\Client_po;
use App\Model\advanceIO;
use App\Model\Client_po_consignee;
use App\Model\Consignee;
use App\Model\Binding_detail;
use App\Model\Raw_Material;
use App\Model\PaperType;
use App\Model\ElementType;
use App\Model\InternalOrder;
use App\Model\Binding_form_labels;
use App\Model\Binding_item;
use App\Model\JobCard;
use App\Model\Widget;
use App\Model\Album;
use App\Model\WidgetSetting;
use App\Model\Party;
use App\Model\Employee\EmployeeProfile;
use App\Model\Department;
use App\Model\Employee\Delegates;
use App\Model\Employee\EmployeeTaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use File;
use Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use Calendar;
use App\Event;
use App\dkerp;
use App\Model\Reference;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Model\Employee\EmployeeRelieving;
use App\Custom\CustomHelpers;
use App\Model\Design\DesignOrder;
use \Carbon\Carbon;
use App\Model\TaxPercentageApplicable;
class WidgetAll extends Controller
{
public function widgets(){
    //employee
    $dept=Auth::user()->department_id;
    $setting=WidgetSetting::where('user_id',Auth::id())
    ->leftJoin('widgets','widgets_setting.widget_id','widgets.id')
    ->select('widget_id','widgets.template_name')->orderBy('widgets.id')->get();
    $total=EmployeeProfile::select(DB::raw('COUNT(id) as total'))->get()->first();
    $leaved=EmployeeRelieving::select(DB::raw('COUNT(id) as total'))->get()->first();
    $do_tot=DesignOrder::select(DB::raw('COUNT(id) as total'))->get()->first();
    $do_open=DesignOrder::where('status','Open')->select(DB::raw('COUNT(id) as total'))->get()->first();
    // return $do_open;
    $party=Party::select(DB::raw('COUNT(id) as total'))->get()->first();
    $consignee=Consignee::select(DB::raw('COUNT(id) as total'))->get()->first();
    $date=date('Y-m-d');

    // DB::enableQueryLog();
    $leave=HR_Leave::leftJoin('employee__profile',function($join){
        $join->on('employee__profile.id','hr__leave.employee');
    })
    ->  leftJoin('employee__profile as rep',function($join){
        $join->on('employee__profile.reporting','rep.reporting');
        $join->where('rep.id','hr__leave.employee');
    })
    ->leftJoin('users as level1','level1.id','hr__leave.status_level1_by')
    ->leftJoin('users as level2','level2.id','hr__leave.status_level2_by')
    ->whereRaw('end_date >= "'.$date.'"')
    ->select( 'hr__leave.id',
    'employee__profile.name as emp',
    'employee__profile.reporting',
    'employee__profile.department_id',
    'employee__profile.employee_number',
    DB::raw('DATE_FORMAT(leave_apply_date,"%d-%m-%Y") as leave_apply_date'),
    'leave_apply_date',
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
    );
    
    // return json_encode($leave);whereRaw('MONTH(dob) = MONTH(NOW())')->
    $emp=EmployeeProfile::select('employee__profile.id',
    'employee__profile.name',
    'employee__profile.employee_number',
    DB::raw("CONCAT(employee__profile.name,'(',employee__profile.employee_number,')') as empcode"),
    DB::raw("DATE_FORMAT(employee__profile.dob, '%d-%m-%Y') as original"),
    DB::raw("CONCAT(DATE_FORMAT(employee__profile.dob, '%d-%m'),'-',YEAR(CURDATE())) as dob"))->get();
    $annv=EmployeeProfile::select('employee__profile.id',
    'employee__profile.name',
    'employee__profile.employee_number',
    DB::raw("CONCAT(employee__profile.name,'(',employee__profile.employee_number,')') as empcode"),
    DB::raw("DATE_FORMAT(employee__profile.doj, '%d-%m-%Y') as original"),
    DB::raw("CONCAT(DATE_FORMAT(employee__profile.doj, '%d-%m'),'-',YEAR(CURDATE())) as doj"))->get();
    
    $firstemp=EmployeeProfile::whereRaw("MONTH(dob) = MONTH(NOW()) and DAY(dob) = DAY(NOW())")->select('employee__profile.id',
    'employee__profile.name',
    'employee__profile.employee_number',
    DB::raw("CONCAT(employee__profile.name,'(',employee__profile.employee_number,')') as empcode"),
    DB::raw("DATE_FORMAT(employee__profile.dob, '%d-%m-%Y') as original"),
    DB::raw("CONCAT(DATE_FORMAT(employee__profile.dob, '%d-%m'),'-',YEAR(CURDATE())) as dob"))->get();
    
    $firstannv=EmployeeProfile::whereRaw("MONTH(doj) = MONTH(NOW()) and DAY(doj) = DAY(NOW())")->select('employee__profile.id',
    'employee__profile.name',
    'employee__profile.employee_number',
    DB::raw("CONCAT(employee__profile.name,'(',employee__profile.employee_number,')') as empcode"),
    DB::raw("DATE_FORMAT(employee__profile.doj, '%d-%m-%Y') as original"),
    DB::raw("CONCAT(DATE_FORMAT(employee__profile.doj, '%d-%m'),'-',YEAR(CURDATE())) as doj"))->get();
    $user=Auth::user()->user_type;
    $auth=Auth::id();

    if($user!="superadmin"){
        
        $firstevent=HR_Events::whereRaw("date = '".$date."'")->select(
            DB::raw("DATE_FORMAT(date, '%d-%m-%Y') as date"),'events','id'
        )->get();
        $leave = $leave->whereRaw('employee__profile.department_id = \''.$dept.'\' OR employee__profile.reporting = rep.reporting')->get();
    }
    else{
       $firstevent=HR_Events::HavingRaw('FIND_IN_SET(\''.$dept.'\' ,department)  OR ISNULL(department)')
        ->where('date','=',$date)->get(); 
        $leave = $leave->get();
    }
    // print_r($leave);
    // print_r( DB::getQueryLog());die();
    
    if($user!="superadmin"){
        $event=HR_Events::select(
            DB::raw("DATE_FORMAT(date, '%d-%m-%Y') as date"),'events','id'
        )->get();
        $announcements=Announcements::where('date','>=',$date)->select(
            DB::raw("DATE_FORMAT(date, '%d-%m-%Y') as date"),'announcements','id'
        )->get();
    }
    else{
       $event=HR_Events::HavingRaw('FIND_IN_SET(\''.$dept.'\' ,department)  OR ISNULL(department)')
       ->where('date','=',$date)->get();  
        $announcements=Announcements::HavingRaw('FIND_IN_SET(\''.$dept.'\' ,department)  OR ISNULL(department)')->where('date','>=',$date)->get();   
    }

    $checklist = EmployeeTaskStatus::where("users.emp_id",Auth::id())
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
        )->get();

        $emp_id = Auth::user()->emp_id;
        $delegation = Delegates::leftjoin('employee__profile','employee__profile.id','delegation.emp_id')
        ->leftjoin('delegation_completion_date','delegation_completion_date.delegate_id','delegation.id')
        ->leftJoin('users','users.emp_id','employee__profile.id')
        ->where('delegation.emp_id',$emp_id)
         ->where('delegation_completion_date.final_status','<>','completed')
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
        ->get();

    $hr=Settings::where('name','HR_Leave_Level1')->select('value','name')->get()->first();
    $doc = WidgetPhotos::orderBy('id','desc')->take(2)->select('name')->get();
    $events = [];
    $bday=[];
    $anniver=[];
    if($event->count()) {
        foreach ($event as $key => $value) {
            $events[] = Calendar::event(
                $value->events,
                true,
                new \DateTime($value->date),
                new \DateTime($value->date.' +1 day'),
                $value->id,
                // Add color and link on event
                [
                    'color' => '#f05050',
                   
                    
                ]
            );
        }
    }
    if($emp->count()) {
        foreach ($emp as $key => $value) {
            $bday[] = Calendar::event(
                $value->empcode,
                true,
                new \DateTime($value->dob),
                new \DateTime($value->dob.' +1 day'),
                $value->id,
                // Add color and link on event
                [
                    'color' => '#8c14f9',
                ]
            );
        }
    }
    if($annv->count()) {
        foreach ($annv as $key => $value) {
            $anniver[] = Calendar::event(
                $value->empcode,
                true,
                new \DateTime($value->doj),
                new \DateTime($value->doj.' +1 day'),
                $value->id,
                // Add color and link on event
                [
                    'color' => '#f95e14',
                        'height' => '50px',
                        'columnHeader' => false,
                        'aspectRatio' => 1
                    
                ]
            );
        }
    }
    $color_calender =array_merge($events,$bday,$anniver);
    // print_r($color_calender);die();
    // $calendar = Calendar::addEvents($events);
    $calendar = \Calendar::addEvents($color_calender)
    ->setCallbacks([
        'eventRender'=> 'function (event){
            
              var start = moment(event.start).format("YYYY-MM-DD"); 
              var end = moment(event.end).format("YYYY-MM-DD");
              $(document).find($(".fc-day[data-date="+ start + "]")).css("background-color", "#FAA732");
              $(document).find($(".fc-day[data-date="+ start + "]")).css("cursor", "pointer");
              $(document).find($(".fc-day-number[data-date="+ start + "]")).css("cursor", "pointer");
                $(".fc-day[data-date="+ start + "]").click(function(){
                    fetchdata(start);
                });
                $(".fc-day-number[data-date="+ start + "]").click(function(){
                    fetchdata(start);
                });
        }'
    ]);
    $holiday = Holiday::select(DB::raw('GROUP_CONCAT(date)as fulldate'))->get();
    $data=array(
        'layout' => 'layouts.main',
        'total' => $total['total'],
        'leaved' => $leaved['total'],
        'do_tot' => $do_tot['total'],
        'do_open' => $do_open['total'],
        'party' => $party['total'],
        'consignee' => $consignee['total'],
        'setting' => $setting,
        'leave' => $leave,
        'checklist' => $checklist,
        'delegation' => $delegation,
        'emp' => $emp,
        'event' => $event,
        'annv'=>$annv,
        'auth'=>$auth,
        'hr'=>$hr,
        'calendar'=>$calendar,
        'announcements'=>$announcements,
        'doc' => $doc,
        'fbday'=>$firstemp,
        'fann'=>$firstannv,
        'feve'=>$firstevent,
        'holiday'=>$holiday[0]['fulldate'],
        'color_calender'=>json_encode($color_calender)
    );
    // return $emp;
    return view('sections.general_dashboard',$data);
}
public function dash_all(){
    $data=array(
        'layout' => 'layouts.main',
    );
    // return $emp;
    return view('sections.dash_all',$data);
}
public function datewise_event(Request $request){
    $selected_date=$request->input('selecteddate');
    // print_r($request->input('selecteddate'));die();
    $dept=Auth::user()->department_id;
    $emp=EmployeeProfile::whereRaw("MONTH(dob) = MONTH('".$selected_date."') and DAY(dob) = DAY('".$selected_date."')")->select('employee__profile.id',
    'employee__profile.name',
    'employee__profile.employee_number',
    DB::raw("CONCAT(employee__profile.name,'(',employee__profile.employee_number,')') as empcode"),
    DB::raw("DATE_FORMAT(employee__profile.dob, '%d-%m-%Y') as original"),
    DB::raw("CONCAT(DATE_FORMAT(employee__profile.dob, '%d-%m'),'-',YEAR(CURDATE())) as dob"))->get();
    
    $annv=EmployeeProfile::whereRaw("MONTH(doj) = MONTH('".$selected_date."') and DAY(doj) = DAY('".$selected_date."')")->select('employee__profile.id',
    'employee__profile.name',
    'employee__profile.employee_number',
    DB::raw("CONCAT(employee__profile.name,'(',employee__profile.employee_number,')') as empcode"),
    DB::raw("DATE_FORMAT(employee__profile.doj, '%d-%m-%Y') as original"),
    DB::raw("CONCAT(DATE_FORMAT(employee__profile.doj, '%d-%m'),'-',YEAR(CURDATE())) as doj"))->get();
    
    $user=Auth::user()->user_type;
    $auth=Auth::id();
    $event=[];
    if($user=="superadmin"){
        
        $event=HR_Events::whereRaw("date = '".$selected_date."'")->select(
            DB::raw("DATE_FORMAT(date, '%d-%m-%Y') as date"),'events','id'
        )->get();
    }
    else{
       $event=HR_Events::HavingRaw('department = \''.$dept.'\' OR ISNULL(department)')->where('date','=',$selected_date)->get();     
    }

    $data = array('Birthday'=>$emp,
    'Anniversary'=>$annv,
    'Event'=>$event);
    // print_r($data);
    // die();
    return response()->json($data);
}
public function staff(){
  
    $data=array(
        'layout' => 'layouts.main',  
    );
    // return $emp;
    return view('Widget.staff_all',$data);
}
public function staff_api(Request $request){
    $search = $request->input('search');
    $serach_value = $search['value'];
    $start = $request->input('start');
    $limit = $request->input('length');
    $offset = empty($start) ? 0 : $start ;
    $limit =  empty($limit) ? 10 : $limit ;
    $jobdata =EmployeeProfile::where("is_active",1)->leftJoin('department','department.id','employee__profile.department_id')->select(
        'employee__profile.name',
        'employee__profile.employee_number',
        'employee__profile.mobile',
        'employee__profile.designation',
        'department.department'
    );
    if(!empty($serach_value))
    {
        $jobdata->where(function($query) use ($serach_value){
            $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
            ->orWhere('employee_number','LIKE',"%".$serach_value."%")
            ->orWhere('mobile','LIKE',"%".$serach_value."%")
            ->orWhere('designation','LIKE',"%".$serach_value."%")
            ->orWhere('department','LIKE',"%".$serach_value."%")
            ;
          });
 
    }
    
    if(isset($request->input('order')[0]['column']))
    {
        $data = [
            'employee__profile.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'employee__profile.mobile',
            'employee__profile.designation',
            'department.department'
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
public function photos_insert(){
    $album=Album::all();
    $data=array(
        'album'=>$album,
        'layout' => 'layouts.main'
    );
    return view('Widget.photos_create',$data);
}
public function photos(Request $request){
    try {
        $this->validate($request, [
            'file.*'      => 'mimes:jpeg,png,jpg|max:'.CustomHelpers::getfilesize(),
            ],[
            'file.*.mimes'      => 'Document required only jpeg,png,jpg format',
            'file.*.max'      => 'Document exceeded maxSize ',
            ]);
   
            if ($request->file('file')) {
                $files = $request->file('file');
                foreach ($files as $key=>$value) {
                     $file = $value;
                     $destinationPath = public_path().'/upload/photos/';     
                     $filenameWithExt = $value->getClientOriginalName();
                     $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);   
                     $extension = $value->getClientOriginalExtension();
                     $name = $filename.'_'.rand(0,99).'_'.time().'.'.$extension;  
                     $path = $file->move($destinationPath, $name);
                     $document = WidgetPhotos::where('name',$key)->first();
                   
                         $doc = WidgetPhotos::insertGetId([
                            'album_id'=>$request->input('album'),
                             'name' => $name
                            
                             
                        ]);
                    
                      if($doc == NULL){
                     //DB::rollback();
                         return redirect('/dashboard/photos/insert')->with('error','Some Unexpected Error occurred.');  
                     }
                 }
                
                 return redirect('/dashboard/photos/insert')->with("success","Photos Uploaded Successfully .");
             }else{ 
                return redirect('/dashboard/photos/insert')->with('error','Please select atleast one photo.');
             }  
          
         } 
         catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/dashboard/photos/insert')->with('error','some error occurred'.$ex->getMessage());
         } 
}
public function album_insert(){
    $data=array(
        'layout' => 'layouts.main'
    );
    return view('Widget.album_create',$data);
}
public function album(Request $request){
    try {
        $this->validate($request, [
            'album'=>'required|unique:album,name'
            ],[
            'album.required'      => 'This Fiels Is Required ',
            'album.unique'      => 'This Album Name Is Already Taken '
            ]);
   
         
            $doc = Album::insertGetId([            
                'name' => $request->input('album')
                  
           ]);
       
         if($doc == NULL){
            return redirect('/dashboard/album/create')->with('error','Some Unexpected Error occurred.');  
        }
        else{
            return redirect('/dashboard/album/create')->with('success','Album is successfully created.');   
        }
          
         } 
         catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/dashboard/photos/insert')->with('error','some error occurred'.$ex->getMessage());
         } 
}
public function photos_gallary(){
    $doc = WidgetPhotos::leftJoin('album','album.id','widget_photos.album_id')
    ->select(DB::raw('group_concat(widget_photos.name) as name'),'album_id','album.name as album')->groupBy('album_id')->get();
    $data=array(
        'layout' => 'layouts.main',
        'doc' => $doc
    );
    // return $doc;
    return view('Widget.gallary',$data);
}
public function gallary($id){
    $x=Album::where('id',$id)->get()->first();
    if($x){
        $doc = WidgetPhotos::where('album_id',$id)
        ->leftJoin('album','album.id','widget_photos.album_id')
        ->select('widget_photos.name as name','album_id','album.name as album')->get();
        $data=array(
            'layout' => 'layouts.main',
            'doc' => $doc
        );
        // return $doc;
        return view('Widget.gallaryall',$data);
    }
    else{
        return redirect('/dashboard/photos/gallary')->with('error','This Album Doesnt Exist');
    }
   
}
public function permission($id){

    if($id!=Auth::id())
    {
        $menudata = Widget::leftJoin('widgets_setting',function($join) use ($id){
            $join->on(DB::raw('widgets_setting.user_id = '.$id.' and widgets.id'),'=','widgets_setting.widget_id');
        })

        ->select(['widgets.id as pid','widgets.name','widgets_setting.user_id'])
        ->get()->toarray();
        $user = Users::where('id',$id)->get()->first();
        $menudata = CustomHelpers::menuTree($menudata);
            $data=array(
                'layout'=>'layouts.main',
                'id'=>$id,
                'menudata'=>$menudata,
                'user'=>$user
            );
            return view('Widget.widget_setting',$data);
    }
    else
        return abort('403',"You can not change your own Access Rigths.");
}
public function getadminpermission($id){
    DB::enableQueryLog();
    $menudata = Widget::leftJoin('widgets_setting',function($join) use ($id){
        $join->on(DB::raw('widgets_setting.user_id = '.$id.' and widgets.id'),'=','widgets_setting.widget_id');
    })
    ->select([
        'widgets.id as pid',
        'widgets.name',
        'widgets_setting.user_id',
        'widgets_setting.widget_id'
        ])
        ->orderBy('pid')
    ->get()->toarray();
    // print_r($menudata);die;
    // $menudata = CustomHelpers::menuTree($menudata);
    $resp = '<table class="table table-condensed ">
        <thead>
            <tr>
                <th class="info">Name</th>
                <th class="info">View</th>
            </tr>
        </thead>
    <tbody>';
        foreach($menudata as $key){
            $checked1='';
            if(($key['widget_id'] !=NULL ) && ($key['pid'] == $key['widget_id']))
                 $checked1='Checked';
            $resp=$resp.'<tr><th>';
            $resp=$resp.$key['name'];
            $resp=$resp.'</th><th><input type="checkbox" name="menu[]" value="'.$key['pid'].'" '.$checked1.'></th></tr>';
        }
    $resp = $resp.'</tbody>
                </table>';
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
            // print_r($menudata);die;
            WidgetSetting::where('user_id','=',$id)
                ->whereNotIn('widget_id',$menudata)
                ->delete();
            $added_section_rights = WidgetSetting::where('user_id','=',$id)->get('widget_id')->toArray();
            $array = array_column($added_section_rights, 'widget_id');
            //$menudata = array_diff($menudata,$array);
            

            foreach ($menudata as $key => $val) {
                // $str = explode(',',$value);
                // foreach($str as $key1=>$val)
                // {
                    if(!in_array($val,$array))
                    {
                        $insert_array['widget_id'] = $val;
                        $insert_array['user_id'] = $id;
                        $insert_data[] = $insert_array;
                        // $userlayout = UserSectionRight::Create(['section_id'=>$val,'user_id'=>$id]);
                        // $userlayout->section_id = $val;
                        // $userlayout->user_id = $id;
                        // $userlayout->allowed = 1;
                        // $userlayout->save();
                        
                    }
                }
                if(!empty($insert_data))
                $userlayout = WidgetSetting::insert($insert_data);
            //}
        }
        else {
            WidgetSetting::where('user_id','=',$id)
            ->delete();    
        }
            
            DB::commit();
            return redirect('/widget/permission/'.$id)->with('success','Permission has been set successfully.'); 
    }catch(\Illuminate\Database\QueryException $ex) {
        DB::rollback();
        return redirect('/widget/permission/'.$id)->with('error','Something went wrong.'.$ex->getMessage());
    }
}

}