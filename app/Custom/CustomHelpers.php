<?php 

namespace App\Custom;
use App\Model\Userlog;
use App\Model\Users;
use App\Model\Settings;
use App\Model\Employee\EmployeeProfile;
use App\Model\Employee\EmployeeTask;
use App\Model\Employee\EmployeeTaskStatus;
use App\Model\FinancialYear;
use DB;
use Auth;
use DateTime;
use \Carbon\Carbon;

class CustomHelpers{

	/*save user log action */

	public static function getfilesize(){
		$size=2048;
		return $size;
	}
	public static function getFinancialFromDate($date){
		$fromDate=date('Y-m',strtotime($date));
             $finan=FinancialYear::where('from', '<=', $fromDate)
             ->where('to', '>=', $fromDate)->first();
         if($finan){
         	return $year=$finan->financial_year;
         }else
         {
         	return false;
         }
	}
	public static function getFinancialYear($date){
		if (date_format($date,"m") >= 4) {//On or After April (FY is current year - next year)
			$financial_year = (date_format($date,"y")) . '-' . (date_format($date,"y")+1);
		} else {//On or Before March (FY is previous year - current year)
			$financial_year = (date_format($date,"y")-1) . '-' . date_format($date,"y");
		}
		return $financial_year;
	}
	public static function checkUserIp()
	{
		$allowed_ip = explode(',',Settings::where('name','like','login_allowed_ip')
		->select('value')->get()->first()->value);
		
		if($allowed_ip[0] =='' || array_search($_SERVER['REMOTE_ADDR'],$allowed_ip))
		{
			return true;
		}
		else 
			return false; 
	}
	public static function getDepartmentName($user)
	{
		return Users::leftJoin('department',function($join){
			$join->on('department.id','users.department_id');
		})->where('users.id','=',$user)
		->get('department.department')->first()->department ;
	}
	public static function getUserJoinDate($user)
	{
		return Users::where('users.id','=',$user)
		->get('users.created_at as a')->first()->a ;
	}

	public static function userLog($text='',$data_id='',$action='',$log_array=array())
    {
    	
    	$page = \Request::fullUrl();
        if(empty($action))
        {
            $action = explode('/',$page);
            $action = implode(' ',$action);
        }
        $action_name = $action;
        $data_id = $data_id>0?$data_id:0;
    	$id = Auth::id();

    	$prev_data = Userlog::select('*')->where("data_id",$data_id)->get()->last();
    	$prev_data_content = json_decode($prev_data['content'],true);

    	if($prev_data)
    	{
    		$prev_data_array=array();
			$diff = array_diff_assoc(array_map('json_encode', $log_array), array_map('json_encode', $prev_data_content));
				$diff = array_map('json_decode', $diff);
	    	$diff_data = json_encode($diff);
   		}
   		else
   		{
   			$diff_data=array();
   			$diff_data = json_encode($diff_data);
   		}
   		$posted_data = json_encode($log_array);

    	$insertid = Userlog::insert([
    		'userid'=>$id,
    		'page'=>$page,
    		'action'=>$action_name,
    		'data_id'=>$data_id,
    		'description'=>$text,
    		'content_changes'=>$diff_data,
    		'content'=>$posted_data
    	]);
	}
    public static function userActionLog($text='',$data_id='',$action='',$log_array=array(),$content_changes="",$removekeys="")
    {
    	
    	$page = \Request::fullUrl();

    	$posted_data = \Request::input();
    	unset($posted_data['userAlloweds']);
    	unset($posted_data['_token']);
    	unset($posted_data['update_reason']);

        if(empty($action))
        {
            $action = explode('/',$page);
            $action = implode(' ',$action);
        }
        $action_name = $action;
        $data_id = $data_id>0?$data_id:0;
    	$id = Auth::id();

    	$prev_data = Userlog::select('*')->where("data_id",$data_id)->get()->last();
    	$prev_data_content = json_decode($prev_data['content'],true);

    	if(!empty($removekeys))
    	{
    		$posted_data = array_diff_assoc($posted_data,$removekeys);
    	}
    	$posted_data_encode="";
    	if($prev_data)
    	{
    		$prev_data_array=array();
    		if(!empty($log_array))
			{
				foreach ($posted_data as $key => $value) {
					$nkey = @$log_array[$key];
					if($nkey!='')
					{
						$posted_data_array[$nkey] = $value;
					}
					else
					{
						$posted_data_array[$key] = $value;
					}
					if(array_key_exists($key,$content_changes))
					{
						$posted_data_array[$nkey] = $content_changes[$key];
					}
				}
				foreach ($prev_data_content as $key => $value) {
					$nkey = @$log_array[$key];
					if($nkey!='')
					{
						$prev_data_array[$nkey] = $value;
					}
					else
					{
						$prev_data_array[$key] = $value;
					}
				}
				$prev_data_content = $prev_data_array;
			}
			else
			{
				$posted_data_array = $posted_data;
				if(!empty($content_changes))
				{
					foreach ($content_changes as $key => $value) {
					
						if(array_key_exists($key,$posted_data_array))
						{
							$posted_data_array[$key] = $value;
						}
					}
				}
			}
			$posted_data_content = $posted_data_array;
			
			$diff = array_diff_assoc(array_map('json_encode', $posted_data_content), array_map('json_encode', $prev_data_content));
				$diff = array_map('json_decode', $diff);

	    	//$diff = array_diff_assoc($posted_data_content,$prev_data_content);
	    	
	    	$diff_data = json_encode($diff);
   		}
   		else
   		{
   			$diff_data=array();
   			$diff_data = json_encode($diff_data);
   			$posted_data_array = $posted_data;
   		}
   		$posted_data = json_encode($posted_data_array);

    	$insertid = Userlog::insert([
    		'userid'=>$id,
    		'page'=>$page,
    		'action'=>$action_name,
    		'data_id'=>$data_id,
    		'description'=>$text,
    		'content_changes'=>$diff_data,
    		'content'=>$posted_data
    	]);
	}
	public static function showDate($date,$format='d-M-Y')
    {
		try
		{
			$date = Carbon::parse($date)->format($format);
			return $date;
		}
		catch(Exception $e)
		{
			return "Error";
		}
	}
	public static function menuTree(array &$elements, $parentId = 0) {

        $branch = array();    
        foreach ($elements as $element) {
            if ($element['pid'] == $parentId) {
                $children = self::menuTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[$element['id']] = $element;
            }
        }
        return $branch;
    }

    public static function employee_assets($id)
	{
		$emp = EmployeeProfile::where('id',$id)->select('assets')->first();
		return explode(',',$emp['assets']);
	}

	public static function getDay($fdate,$todate) {
		try{
			
			$to = \Carbon\Carbon::createFromFormat('Y-m-d',$fdate);
            $from = \Carbon\Carbon::createFromFormat('Y-m-d',$todate);
			$days = $to->diffInDays($from);

            return $days;			
		}
		catch(Exception $e)
		{
			return 'Error';
		}
	}

	public static function GetHourFromTime($time) {
			$from_time = trim(substr($time, 0, strpos($time, "to"))); 
            $to_time = substr($time, strpos($time, "to") + 2); 
            $from = str_replace(' ', '', $from_time);
            $to = str_replace(' ', '', $to_time);
            $datetime1 = new DateTime($from);
            $datetime2 = new DateTime($to);
            $interval = $datetime1->diff($datetime2);
            return $timeing =  $interval->format('%h');
	}

	public static function TimeConvert($time) {
        $convert = str_replace(' ', '', $time);
        $updatedtime = date("H:i:s", strtotime($convert));
        return $updatedtime;
	}

	public static function ConvertTime($time) {
            $time1 = str_replace(' ', '', $time);
            $convert = date("H:i:s", strtotime($time1));
            return $convert;
	}
	
}