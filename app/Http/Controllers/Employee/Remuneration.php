<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Employee\EmployeeProfile;
use App\Model\HR\HR_Leave;
use App\Model\HR\HR_LeaveDetails;
use App\Model\Employee\EmployeeIncrement;
use App\Model\Employee\EmployeeRelieving;
use App\Model\Employee\EmployeeSalary;
use App\Model\Employee\EmployeeFnF;
use App\Model\Employee\NetSalary;
use App\Model\Employee\NetSalaryDetails;
use App\Model\Employee\IncrementDA;
use App\Model\Holiday;
use App\Model\Employee\Advance;
use App\Model\Employee\PaidAdvance;
use App\Model\FinancialYear;
use App\Model\Employee\Attendance;
use App\Model\EmployeeBank;
use App\Model\Employee\PayrollPayment;
use \Carbon\Carbon;
use App\Model\Settings;
use App\Custom\CustomHelpers;
use App\Model\Users;
use PDF;  
use Auth;
use File;
use DB;
use DateTime;

class Remuneration extends Controller
{

    //salary register
    public function full_final_settlement(){
        $emp=EmployeeRelieving::leftJoin('employee__profile','employee__profile.id','employee__relieving.emp_id')
        ->leftJoin('employee_fnf','employee__profile.id','employee_fnf.emp_id')
        ->where('employee_fnf.emp_id',NULL)
        ->select('name','employee__profile.id','employee_number')
        ->get();
        $data = array(
            'emp'=>$emp,
			'layout' => 'layouts.main');
		return view('employee.Remuneration.full_final_settlement',$data);
    }
    public function full_final_settlement_api(Request $request){
        $emp=$request->input('emp');
        $employee=EmployeeRelieving::where('emp_id',$emp)
        ->leftJoin('employee__profile','employee__profile.id','employee__relieving.emp_id')
        ->select('name','employee__profile.id','employee_number',DB::raw('DATE_FORMAT(leaving_date,"%d-%m-%Y") as leaving_date')
        ,  DB::raw('TIMESTAMPDIFF(YEAR,employee__profile.doj, employee__relieving.leaving_date) as diff'),'employee__profile.doj')
        ->get()->first();
        $employee['leaving_date'] = date('Y-m-d',strtotime($employee['leaving_date']));
        $cc = date('m',strtotime($employee['leaving_date']));
        $yy = date('Y',strtotime($employee['leaving_date']));
        $mons = date('m',strtotime($employee['leaving_date']));
        // print_r($cc);die;
        // $pp=EmployeeProfile::where('employee__profile.id',$emp)
        // ->leftJoin('payroll', function($join){
        //     $join->on('payroll.emp_id','=','employee__profile.id');
        //     $join->where('payroll.salary_type','=','SalaryA');
            
        //     })
        // ->select( DB::raw('IFNULL(da_category,"0") as da_category'))->get()->first();
        // $pay_da=IncrementDA::where('payroll__da.added_insalary','=',0)
        // ->where('payroll__da.sal_cat','=',"SalaryA")
        // ->whereDate('payroll__da.created_at','<',$employee['leaving_date'])
        // ->where('payroll__da.da_cat','=',$pp['da_category'])
        // ->select(
        //     DB::raw('IFNULL(SUM(amount_inc),"0") as amount_inc'))->get()->first();
        $a_date =  $yy;
        $less_yr=$a_date-1;
        $mon = ['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Decem'];
        for ($j = 1; $j <= $cc ; $j++) {
            $md=$mon[$j];
            $query[$j] = "IFNULL((SELECT count(att.status) FROM payroll__attendance att 
            WHERE  att.emp_id=".$emp.". AND att.status<>'A' AND YEAR(att.date)=".$a_date.".  AND MONTH(att.date) = ".$j." ),'') as ".$mon[$j]." ";    
        }
        $query = join(",",$query);
        $leave_encashment=EmployeeProfile::where('employee__profile.id',$emp)
       
            ->leftJoin('payroll__attendance', function($join) use ($a_date){
                $join->on('payroll__attendance.emp_id','=','employee__profile.id');
                $join->WhereYear('payroll__attendance.date',$a_date);
                $join ->where('payroll__attendance.status','!=',"A");
            })
                ->leftJoin('leave__enhancement', function($join) use ($a_date){
                    $join->on('leave__enhancement.emp_id','=','employee__profile.id');
                    $join->Where('leave__enhancement.year',$a_date);
                    
            })
            ->leftJoin('payroll', function($join) use ($a_date,$mon){
                $join->on('payroll.emp_id','=','employee__profile.id');
                $join->where('payroll.salary_type','=','SalaryA');
                })
          
            ->leftJoin('hr__leave_details', function($join) use ($a_date){
                $join->on('hr__leave_details.emp_id','=','employee__profile.id');
                $join->whereYear('hr__leave_details.date','=',$a_date);
                $join->where('hr__leave_details.is_adjusted','=',"1");
                $join->where('hr__leave_details.status','=',"Approved");
                })
                ->leftJoin('payroll__advance', function($join){
                    $join->on('payroll__advance.emp_id','=','employee__profile.id');
                   
                    
                })
                ->select(
                    'employee__profile.id',
                    'employee__profile.name as emp_name',
                    'employee__profile.employee_number',
                    // DB::raw('DATE_FORMAT(leaving_date,"%d-%m-%Y") as leaving_date'),

                DB::raw('IFNULL(basic_salary+dearness_allowance,"0") as total_salaryA'),
                
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM hr__leave_details m 
                    WHERE employee__profile.id=m.emp_id
                    AND m.status="Approved"
                    AND m.is_adjusted="1"
                    AND YEAR(m.date)='.$a_date.'
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_leaves'),
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
                        AND m.status<>"A"
                        AND YEAR(m.date)='.$less_yr.'
                            GROUP BY employee__profile.id) ,"0" ) 
                        ) as total_present'),
                        DB::raw('(IFNULL(
                            (SELECT sum(m.advance_paid) FROM payroll__advance m 
                            WHERE employee__profile.id=m.emp_id
                                GROUP BY employee__profile.id) ,"0" ) 
                            ) as balance_advance'),
                        DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                    DB::raw($query)
            )->GroupBy('employee__profile.id')->get()->first();

        //bonus
        $to=date('Y-m-d',strtotime($employee['leaving_date']));
        $from=date('Y-m-d',strtotime("-12 month",strtotime($employee['leaving_date'])));
        // print($from);die;
        $fin_year=CustomHelpers::getFinancialFromDate($employee['leaving_date']);

        $datef_s_e =FinancialYear::where('financial_year','=',$fin_year)->get()->first();
        $bon_curr=$datef_s_e['bonus_per'];
        $y=date('Y',strtotime($datef_s_e['from']));
       
        $doj=$employee['doj'];
        $ll=$employee['leaving_date'];
        $levae=date('Y-m-d',strtotime($employee['leaving_date']));
        $date_j=date('Y-m-d',strtotime($employee['doj']));
        $doj_year=CustomHelpers::getFinancialFromDate($employee['doj']);
        $employee['leaving_date']=date('Y-m-t',strtotime($employee['leaving_date']));
        $date_join=date('Y-m-01',strtotime($doj));
        $lea=$employee['leaving_date'];
        $months = array();
        if($fin_year==$doj_year){
            $start = $month = strtotime($doj);
            $counter=0;
            $end = strtotime($employee['leaving_date']);
            while($month < $end)
            {
               
                $months[$counter]=date('F', $month);
                $month = strtotime("+1 month", $month);
                $counter++;
            }
            $months = implode(",", $months);
         
        }
        else{
            $cur_month = date('n',strtotime($employee['leaving_date']));
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
    
            for($i =($num+1); $i <= $cur_month; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
       
        // print_r($y);die;
        $bonus_calculated = EmployeeProfile:: where('employee__profile.id',$emp)
        ->leftJoin('payroll', function($join) use ($a_date,$mon){
            $join->on('payroll.emp_id','=','employee__profile.id');
            $join->where('payroll.salary_type','=','SalaryA');
            })
            ->leftJoin('payroll__salary', function($join) use ($a_date,$mon){
                $join->on('payroll__salary.emp_id','=','employee__profile.id');
                $join->where('payroll__salary.salary_type','=','SalaryA');
                })
        ->select(
            'employee__profile.name',
            'employee__profile.employee_number',
            'payroll.emp_id',
            DB::raw('(payroll.basic_salary + payroll.dearness_allowance)as total_sal_a'),
            DB::raw('IFNULL(payroll.bonus,"0") as bonus'),
            DB::raw('DATE_FORMAT(doj,"%d-%m-%Y") as doj'),

            DB::raw('MONTHNAME(employee__profile.doj) as join_month'),

            DB::raw('TIMESTAMPDIFF(MONTH,employee__profile.doj, "'.$levae.'") as diff'),

            // DB::raw('CASE WHEN (employee__profile.doj < "'.$datef_s_e["from"].'-01") then TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$lea.'" ELSE TIMESTAMPDIFF(MONTH,employee__profile.doj, "'.$lea.'"  End as checkdiff'),
            DB::raw('CASE WHEN (employee__profile.doj < "'.$datef_s_e["from"].'-01") then TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$lea.'")+1 ELSE TIMESTAMPDIFF(MONTH,"'.$date_join.'", "'.$lea.'")+1  End as checkdiff'),
            // DB::raw('(select GROUP_CONCAT(Concat(ida.month_name,":",ida.amount_inc)) FROM payroll__da ida where ida.sal_cat = salary_type and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31")) as payroll_month'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$datef_s_e["from"].'-01") then 
                    CASE When ("'.$lea.'" >= "'.$datef_s_e["from"].'-01") and ("'.$lea.'" <= "'.$y.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$lea.'")+1
                    ELSE
                    "6"
                    END
                 ELSE
                  CASE When ("'.$lea.'" >= "'.$datef_s_e["from"].'-01") and ("'.$lea.'" <= "'.$y.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$date_join.'", "'.$lea.'")+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,"'.$date_join.'", "'.$y.'-09-31")+1
                    END
                 END as apr_sep'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$y.'-10-01") then 
                    CASE When ("'.$lea.'" >= "'.$y.'-10-01") and ("'.$lea.'" <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$y.'-10-01", "'.$lea.'")+1
                    ELSE
                    "0"
                    END
                WHEN (`employee__profile`.`doj` >= "'.$y.'-10-01") THEN
                  CASE When ("'.$lea.'" >= "'.$y.'-10-01") and ("'.$lea.'" <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$date_join.'", "'.$lea.'")+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,"'.$date_join.'", "'.$datef_s_e["to"].'-31")+1
                    END
                ElSE
                     0
                 END as oct_mar'),
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = "'.$emp.'" and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September")) as tot_sal_pre_apr'),
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = "'.$emp.'" and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March")) as tot_sal_pre_oct'),
            
            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September"))as payroll_apr'),

            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March"))as payroll_oct')
        )->get()->first();  
  
        // print_r($sal_gen);die;
        // $salA= NetSalary::where('emp_id',$emp)->where('salary_type','=',"SalaryA")->where()
        

        $net_salaryA = NetSalary::where('emp_id',$emp)->where('salary_type','=',"SalaryA")
        ->where('is_credit','=',"No")
        ->whereRaw('(payroll__salary.month BETWEEN "'.$from.'" And "'.$to.'")')
        ->select(DB::raw('IFNULL(SUM(net_salary),0) as net_salary'))->get()->first();

        $net_salaryB = NetSalary::where('emp_id',$emp)->where('salary_type','=',"SalaryB")
        ->where('is_credit','=',"No")
        ->whereRaw('(payroll__salary.month BETWEEN "'.$from.'" And "'.$to.'")')
        ->select(DB::raw('IFNULL(SUM(net_salary),0) as net_salary'))->get()->first();
        
        //previous year bonus
        $months = array();
        $finan = $fin_year;
        $finan=explode('-',$finan);
        $finan[0]=$finan[0]-1;
        $finan[1]=$finan[1]-1;
        $f=$finan[0];
        $finan=$finan[0]."-".$finan[1];
        $dj=explode('-',$doj_year);
        $dj=$dj[0];
        // DB::EnablequeryLog();
     
        $datef_s_e = FinancialYear::where('financial_year',$finan)->get()->first();
        $bon_pre=$datef_s_e['bonus_per'];
        $last_date=date('Y-m-t',strtotime($datef_s_e['to'].'-01'));
        $start_date=date('Y-m-t',strtotime($datef_s_e['from'].'-01'));
        $start_month=date('m',strtotime($datef_s_e['from'].'-01'));
        $yr=explode('-',$datef_s_e['financial_year']);
        $yr=$yr[0];
        $dt = DateTime::createFromFormat('y', $yr);
        $dt=$dt->format('Y'); // output: 2013
        $flag=0;
        if($finan==$doj_year){
            $start = $month = strtotime($doj);
            $counter=0;
            $curr_date=$datef_s_e["to"].'-01';
            $curr_date=date('Y-m-t',strtotime($curr_date));
            $end = strtotime($curr_date);
            while($month < $end)
            {
               
                $months[$counter]=date('F', $month);
                $month = strtotime("+1 month", $month);
                $counter++;
            }
            $months = implode(",", $months);
         
        }
        elseif($f<$dj){
            $flag=1;
            $curr_date=$datef_s_e["to"].'-01';
            $curr_date=date('Y-m-t',strtotime($curr_date));
            $cur_month = date('n',strtotime($curr_date));
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
    
            for($i =($num+1); $i <= 15; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
        else{
            $curr_date=$datef_s_e["to"].'-01';
            $curr_date=date('Y-m-t',strtotime($curr_date));
            $cur_month = date('n',strtotime($curr_date));
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
    
            for($i =($num+1); $i <= 15; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
      
        
        $bonus_pre = EmployeeProfile::where('employee__profile.id','=',$emp)
        // ->where('employee__relieving.id',NULL)
        // ->orwhereBetween('employee__relieving.leaving_date',[$start_date,$last_date])
        ->leftJoin('payroll',function($join) use ($last_date){
            $join->on('payroll.emp_id','=','employee__profile.id');
            $join->where('payroll.salary_type','=','SalaryA');
        })
        ->leftJoin('payroll__salary',function($join){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','=',"SalaryA");
        })
        ->leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->select(
            'employee_number',
            DB::raw('(Select IFNULL(SUM(inc_da.amount_inc),0)
                FROM payroll__da as inc_da 
                WHERE inc_da.added_insalary=0 and FIND_IN_SET(inc_da.month_name, "'.$months.'")and (inc_da.created_at BETWEEN "'.$datef_s_e["from"].'-01" AND "'.$datef_s_e["to"].'-31"))as inc_da_t_d'),

            DB::raw('(IFNULL(payroll.basic_salary + payroll.dearness_allowance ,"0") )as total_sal_a'),

            DB::raw('IFNULL(payroll.bonus,"0") as bonus'),'employee__profile.doj',

            DB::raw('MONTHNAME(employee__profile.doj) as join_month'),

            DB::raw('TIMESTAMPDIFF(MONTH,employee__profile.doj, CURDATE()) as diff'),

            DB::raw('CASE WHEN (employee__profile.doj < "'.$datef_s_e["from"].'-01") then TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$curr_date.'")+1 ELSE TIMESTAMPDIFF(MONTH,employee__profile.doj, "'.$curr_date.'")+1  End as checkdiff'),

            // DB::raw('(select GROUP_CONCAT(Concat(ida.month_name,":",ida.amount_inc)) FROM payroll__da ida where ida.sal_cat = salary_type and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31")) as payroll_month'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$datef_s_e["from"].'-01") then 
                    CASE When ("'.$curr_date.'" >= "'.$datef_s_e["from"].'-01") and ("'.$curr_date.'" <= "'.$dt.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$curr_date.'")+1
                    ELSE
                    "6"
                    END
                 ELSE
                  CASE When ("'.$curr_date.'" >= "'.$datef_s_e["from"].'-01") and ("'.$curr_date.'" <= "'.$dt.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$curr_date.'")+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$dt.'-09-31")+1
                    END
                 END as apr_sep'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$dt.'-10-01") then 
                    CASE When ("'.$curr_date.'" >= "'.$dt.'-10-01") and ("'.$curr_date.'" <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$dt.'-10-01", "'.$curr_date.'")+1
                    ELSE
                    "0"
                    END
                WHEN (`employee__profile`.`doj` >= "'.$dt.'-10-01") THEN
                  CASE When ("'.$curr_date.'" >= "'.$dt.'-10-01") and ("'.$curr_date.'" <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$curr_date.'")+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$datef_s_e["to"].'-31")+1
                    END
                ElSE
                     0
                 END as oct_mar'),

                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = "'.$emp.'" and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September")) as tot_sal_pre_apr'),
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = "'.$emp.'" and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March")) as tot_sal_pre_oct'),
            
                 DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September"))as payroll_apr'),
            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March"))as payroll_oct')
        )->groupBy('employee__profile.id')->get()->first();

        // print($flag);die;
        $begin = new DateTime($from);
        $end = new DateTime($to);
        $month_no_paid=array();
        $con=0;
        for($i = $begin; $i <= $end; $i->modify('+1 month')){
            $month_no_paid[$con]=$i->format("M_Y");
            $j=$i->format("M_Y");
            $mont=$i->format("m");
            $yrr=$i->format("Y");
            $querys[$con]="IFNULL((SELECT concat(att.net_salary,' <span class=',is_credit,'>   ',is_credit, ' </span>') FROM payroll__salary att 
            WHERE att.salary_type='SalaryA' AND att.emp_id=".$emp.".  AND YEAR(att.month)=".$yrr.".  AND MONTH(att.month) = ".$mont." ),'-') as ".$j." ";    
            $queryss[$con]="IFNULL((SELECT concat(att.net_salary,'  <span>  ',is_credit, '</span>') FROM payroll__salary att 
            WHERE att.salary_type='SalaryB' AND att.emp_id=".$emp.".  AND YEAR(att.month)=".$yrr.".  AND MONTH(att.month) = ".$mont." ),'-') as ".$j." ";    
            $con++;
        }
        $querys=join(",",$querys);
        $queryss=join(",",$queryss);
        $all_month_salary= EmployeeProfile::where('employee__profile.id',$emp)
        ->leftJoin('payroll__salary',function($join) use ($from,$to){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','=',"SalaryA");
            $join->whereRaw('(payroll__salary.month BETWEEN "'.$from.'" And "'.$to.'")');
        })
        
        // ->where('is_credit','=',"No")
        ->select( 'name',DB::raw($querys))->get()->first();

        $all_month_salaryB=EmployeeProfile::where('employee__profile.id',$emp)
        ->leftJoin('payroll__salary',function($join) use ($from,$to){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','=',"SalaryB");
            $join->whereRaw('(payroll__salary.month BETWEEN "'.$from.'" And "'.$to.'")');
        })
        
        // ->where('is_credit','=',"No")
        ->select( 'name',DB::raw($querys))->get()->first();
        $dd=cal_days_in_month(CAL_GREGORIAN, $mons, $a_date);
        $data=[
           
           'encashment'=>$leave_encashment,
           'bonus_ctc'=>$bonus_calculated,
           'bonus_pre'=>$bonus_pre,
           'flag'=>$flag,
           'cc'=>$cc,
           'dd'=>$dd,
           'bon_curr'=>$bon_curr,
           'bon_pre'=>$bon_pre,
           'from'=>date('d-m-M',strtotime($from)),
           'to'=>date('d-m-M',strtotime($to)),
           'net_salaryA'=>$net_salaryA,
           'net_salaryB'=>$net_salaryB,
           'levae'=>$levae,
           'date_j'=>$date_j,
           'employ'=>$employee,
           'all_month_salary'=>$all_month_salary,
           'all_month_salaryB'=>$all_month_salaryB,
           'mon'=>$mon,
           'month_no_paid'=>$month_no_paid,
           'leaving_date'=>$ll
        //    'balance_sal'=>$balance_sal
       ];
       return $data;
    }
    public function full_final_settlement_create(Request $request){
        try {
        //    print_r($request->input());die;
           $fnf=EmployeeFnF::insertGetId([
                'emp_id'=>$request->input('employee'),
                'total_amount'=>$request->input('full_final'),
                'fnf_date'=>date('Y-m-d',strtotime($request->input('fnfdate'))),
                'leaves_encashment'=>$request->input('leaves_enc'),
                'enc_sal'=>$request->input('salA'),
                'bonus'=>$request->input('bonusA'),
                'bonus_ctc'=>$request->input('bonus_ctc'),
                'gratuity_sal'=>$request->input('gratuity_salA'),
                'bal_SalA'=>$request->input('bal_SalA'),
                'bal_SalB'=>$request->input('bal_SalB'),
                'advance'=>$request->input('adv'),
                'gratuity'=>$request->input('gratuity')
           ]);
           if($fnf=="NULL"){
            return redirect('/employee/full&final/settlement')->with('error','some error occurred');
           }
           else{
            return redirect('/employee/full&final/settlement')->with('success','Successfully Created Full And Final Settlement.');
           }
        } catch (Exception $e) {
            return redirect('/employee/full&final/settlement')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function salary_register_list(){
        $days=date('t');
        $month_name=date('M');
        $mon=date('m');
        $year=date('Y');
        // print_r($a_date);die;
        $days_arr=Array();
        for ($j = 1; $j <= $days ; $j++) {
            // $emp_id = $user[$j]['party_id'];
            if($j<10){$md="0".$j."_".$month_name;}  
            else{$md=$j."_".$month_name;}
                
            $date=$j."-".$mon."-".$year;
            $date=date('Y-m-d',strtotime($date));
           $days_arr[$j]=$md;

        }
        $data=[
            'days'=>$days_arr,
            'layout' => 'layouts.main'
        ];
        return view('hr.salary_register',$data);
    }

    public function salary_register_list_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $sort_data_query = array();
        $emp =  $request->input('emp');
        $yr=$request->input('year');
        $yr="01-".$yr;
        $days=date('t',strtotime($yr));
        $month_name=date('M',strtotime($yr));
        $mon=date('m',strtotime($yr));
        $year=date('Y',strtotime($yr));
        // print_r($days);die;
        $leavess=Array();
        for ($j = 1; $j <= $days ; $j++) {
            // $emp_id = $user[$j]['party_id'];
            if($j<10){$md="0".$j."_".$month_name;}  
            else{$md=$j."_".$month_name;}
                
            $date=$j."-".$mon."-".$year;
            $date=date('Y-m-d',strtotime($date));
            // print_r($emp);die;
            $query[$j] = "IFNULL((SELECT att.status FROM payroll__attendance att WHERE  att.emp_id = payroll__attendance.emp_id AND YEAR(att.date)=".$year.".  AND att.date = '".$date."' ),'') as ".$md." ";
            $leave=HR_LeaveDetails::where('emp_id',$emp)
            ->where('date','=',$date)
            ->where('is_adjusted','=','1')
            ->select('date as '.$md.'')
            ->get()->first();
            $leavess[$md]=$leave[$md];

        }
        $leavess = array_filter($leavess); 
        $arr_leaves=Array();
        foreach($leavess as $key=>$value){
            $arr_leaves[$key]="L";
        }
        
        $query = join(",",$query);
        $holiday=Holiday::whereMonth('holiday.start_date', '<=', $mon)
        ->whereMonth('holiday.end_date', '>=', $mon)
        ->whereYear('holiday.start_date', '<=', $year)
        ->whereYear('holiday.end_date', '>=', $year)
        ->select('name','start_date','end_date')
        ->get();

        $arr=Array();
        foreach($holiday as $key){
            $diff = strtotime($key['end_date']) - strtotime($key['start_date']);
            $diff=abs(round($diff / 86400)) + 1;
            
            $date=date('Y-m-d', strtotime('-1 day', strtotime($key['start_date']))); 
           
            for($i=0;$i<$diff;$i++){
                $date=date('Y-m-d', strtotime('+1 day', strtotime($date)));
                $get_mon=date('m', strtotime($date));
                $Mon=date('M', strtotime($date));
                $get_day=date('d', strtotime($date));
                
                $x=$get_day."_".$Mon;
                
                if($get_mon==$mon){
                    $arr[$x]=$key['name'];
                }
                
               
            }
            
        }

        // print_r($arr);die;
        $jobdata=EmployeeProfile::leftJoin('payroll__attendance', function($join) use ($year,$mon){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date','=',$year);
            $join->WhereMonth('payroll__attendance.date','=',$mon);
       })
       ->leftJoin('department','department.id','employee__profile.department_id')
        ->leftJoin('payroll__advance', function($join) use ($year,$mon){
            $join->on('payroll__advance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__advance.given_date','=',$year);
            $join->WhereMonth('payroll__advance.given_date','=',$mon);
            
        })
        ->leftJoin('payroll__salary', function($join) use ($year,$mon){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__salary.month','=',$year);
            $join->WhereMonth('payroll__salary.month','=',$mon);
            $join->Where('payroll__salary.salary_type','=',"SalaryC");
            
        })
        ->leftJoin('payroll_salary_detail as pf_ded', function($join) use ($year,$mon){
            $join->on('pf_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('pf_ded.name','=',"PF");
            
        })
        ->leftJoin('payroll_salary_detail as esi_ded', function($join) use ($year,$mon){
            $join->on('esi_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('esi_ded.name','=',"ESI");
            
        })
        ->leftJoin('payroll_salary_detail as adv_ded', function($join) use ($year,$mon){
            $join->on('adv_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('adv_ded.name','=',"Advance");
            
        })
        ->leftJoin('payroll__paid_advance', function($join) use ($year,$mon){
            $join->on('payroll__paid_advance.advance_id','=','payroll__advance.id');
        })
        ->select(
            'employee__profile.id',
            'employee__profile.name as emp_name',
            'employee__profile.employee_number',
            'employee__profile.designation',
        'department.department',
        'father_name','local_address',
            DB::raw('IFNULL(payroll__salary.net_salary,"0") as total_salaryC'),

            DB::raw('IFNULL(pf_ded.amount,"0") as pf_ded'),
            DB::raw('IFNULL(esi_ded.amount,"0") as esi_ded'),
            DB::raw('IFNULL(adv_ded.amount,"0") as adv_ded'),

            DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$year.'
                AND MONTH(m.date)='.$mon.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_absent_current'),
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    AND m.status<>"A"
                    AND YEAR(m.date)='.$year.'
                    AND MONTH(m.date)='.$mon.'
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_present_current'),
               
                            DB::raw('(IFNULL(
                                (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                WHERE employee__profile.id=m.emp_id
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as opening_advance'),
                                DB::raw('(IFNULL(
                                    (SELECT sum(m.advance_paid) FROM payroll__advance m 
                                    WHERE employee__profile.id=m.emp_id
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as balance_advance'),

                                    DB::raw($query)
                                 
                                      


        )->GroupBy('employee__profile.id');

        if($emp!=0)
        {
            $jobdata->where(function($query) use ($emp){
                $query->where('employee__profile.id','=',$emp);
            });
        } 
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
        $jobdata=$jobdata->get()->toArray();
        $jj=$jobdata;
        if(count($arr_leaves)!=0){
            $arr=$arr_leaves+$arr;
        }
   
        foreach($jobdata as $key=>$value){

            $jj[$key]=$arr+$jj[$key];
        }

        $jobdata=$jj;
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $jobdata;
        return json_encode($array);
    }


    //salaryA calculation
    public function salaryA_cal_form(){
        $d=date('Y-m-d');
        $mon=date('m');
        $employee=EmployeeProfile::leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->leftJoin('employee_fnf','employee_fnf.emp_id','employee__profile.id')
        // ->where('employee_fnf.leaving_date','>=',$d)
        ->Where('employee_fnf.emp_id',NULL)
        
        ->select(DB::raw('IFNULL(DATE_FORMAT(leaving_date,"%m"),"12") as leaving_date'),'employee__profile.id','name','employee_number')
        ->get();
        $data=[
            'layout' => 'layouts.main',
            'employee'=>$employee,
        ];
        // return $flag;
        
        return view('employee.Remuneration.salaryA_calculation',$data);
    }
    public function salaryA_cal(Request $request){
        
        $mo=explode('-',$request->input('mon'));
        $mon=$mo[0];
        $a_date=$mo[1];
       
        $emp=$request->input('emp');
        $less_yr=$a_date-1;
        $employee=EmployeeProfile::where('employee__profile.id',$emp)
        ->leftJoin('employee__pfesi','employee__pfesi.emp_id','employee__profile.id')
        ->leftJoin('payroll__attendance', function($join) use ($a_date,$mon){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date','=',$a_date);
            $join->WhereMonth('payroll__attendance.date','=',$mon);
       })
       ->leftJoin('payroll', function($join) use ($a_date,$mon){
        $join->on('payroll.emp_id','=','employee__profile.id');
        $join->where('payroll.salary_type','=','SalaryA');
        })
        ->leftJoin('leave__enhancement', function($join) use ($a_date,$mon){
            $join->on('leave__enhancement.emp_id','=','employee__profile.id');
            $join->WhereYear('leave__enhancement.year','=',$a_date);
            
        })
        ->leftJoin('hr__leave_details', function($join) use ($a_date,$mon){
            $join->on('hr__leave_details.emp_id','=','employee__profile.id');
            $join->whereMonth('hr__leave_details.date', '=', $mon);
            $join->whereYear('hr__leave_details.date', '=', $a_date);  
            $join->where('hr__leave_details.status', '=', "Approved"); 
        })
        ->leftJoin('payroll__advance', function($join) use ($a_date,$mon){
            $join->on('payroll__advance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__advance.given_date','=',$a_date);
            $join->WhereMonth('payroll__advance.given_date','=',$mon);
            
        })
        ->leftJoin('payroll__paid_advance', function($join) use ($a_date,$mon){
            $join->on('payroll__paid_advance.advance_id','=','payroll__advance.id');
        })
        ->select(
            'employee__profile.id',
            'employee__profile.name as emp_name',
            'employee__profile.employee_number',
            // DB::raw('IFNULL(employee__pfesi.pf,"0") as pf'),
            DB::raw('(IFNULL(
                (SELECT m.pf_applicable FROM payroll m 
                WHERE employee__profile.id=m.emp_id
                AND m.salary_type="SalaryC"
                    GROUP BY employee__profile.id) ,"No" ) 
                ) as pf'),
                DB::raw('(IFNULL(
                    (SELECT m.esi_applicable FROM payroll m 
                    WHERE employee__profile.id=m.emp_id
                    AND m.salary_type="SalaryC"
                        GROUP BY employee__profile.id) ,"No" ) 
                    ) as esi'),
            // DB::raw('IFNULL(employee__pfesi.esi,"0") as esi'),
            DB::raw('IFNULL(SUM(basic_salary+dearness_allowance+hra+conveyance+telephone+other),"0") as total_salaryA'),
            DB::raw('IFNULL(payroll.overtime ,"0") as overtime_salaryA'),
            DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$a_date.'
                AND MONTH(m.date)='.$mon.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_absent_current'),
                DB::raw('IFNULL(group_concat(hr__leave_details.date),"-") as leave_dates'),
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    AND m.status="A"
                    AND YEAR(m.date)='.$less_yr.'
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_absent_pre'),
                    DB::raw('(IFNULL(
                        (SELECT count(m.id) FROM payroll__attendance m 
                        WHERE employee__profile.id=m.emp_id
                        
                        AND YEAR(m.date)='.$a_date.'
                        AND MONTH(m.date)='.$mon.'
                        AND m.status <> "A"
                            GROUP BY employee__profile.id) ,"0" ) 
                        ) as total_present_current'),
                    DB::raw('(IFNULL(
                        (SELECT m.basic_salary FROM payroll m 
                        WHERE employee__profile.id=m.emp_id
                        AND m.salary_type="SalaryC"
                            GROUP BY employee__profile.id) ,"0" ) 
                        ) as basic_salaryC'),
                        DB::raw('(IFNULL(
                            (SELECT m.dearness_allowance FROM payroll m 
                            WHERE employee__profile.id=m.emp_id
                            AND m.salary_type="SalaryC"
                                GROUP BY employee__profile.id) ,"0" ) 
                            ) as DA_C'),
                            DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                    // DB::raw('IFNULL(SUM(TIME_TO_SEC(payroll__attendance.late_by)/60),"0") as late_by'),
                    // DB::raw('IFNULL(SUM(TIME_TO_SEC(payroll__attendance.ot)/60),"0") as ot'),
                    DB::raw('(IFNULL(
                        (SELECT SUM(TIME_TO_SEC(m.ot)/60) FROM payroll__attendance m 
                        WHERE employee__profile.id=m.emp_id
                        AND m.status="P"
                        AND YEAR(m.date)='.$a_date.'
                        AND MONTH(m.date)='.$mon.'
                            GROUP BY employee__profile.id) ,"0" ) 
                        ) as ot'),
                        DB::raw('(IFNULL(
                            (SELECT SUM(TIME_TO_SEC(m.late_by)/60) FROM payroll__attendance m 
                            WHERE employee__profile.id=m.emp_id
                            AND m.status="P"
                            AND YEAR(m.date)='.$a_date.'
                            AND MONTH(m.date)='.$mon.'
                                GROUP BY employee__profile.id) ,"0" ) 
                            ) as late_by'),
                            DB::raw('(IFNULL(
                                (SELECT SUM(TIME_TO_SEC(m.other_time_deduction)/60) FROM payroll__attendance m 
                                WHERE employee__profile.id=m.emp_id
                                AND m.status="P"
                                AND YEAR(m.date)='.$a_date.'
                                AND MONTH(m.date)='.$mon.'
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as other_time_deduction'),

                                DB::raw('(IFNULL(
                                    (SELECT count(m.id) FROM payroll__attendance m 
                                    WHERE employee__profile.id=m.emp_id
                                    AND m.status="P"
                                    AND m.half_day="Yes"
                                    AND YEAR(m.date)='.$a_date.'
                                    AND MONTH(m.date)='.$mon.'
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as half_day'),
                    DB::raw('IFNULL(payroll__advance.advance_amount,"0") as advance_amount'),
                    DB::raw('IFNULL(payroll__advance.advance_paid,"0") as advance_deducted'),
                    DB::raw('count(hr__leave_details.id) as no_of_leaves'),
                    DB::raw('(IFNULL(
                        (SELECT count(m.id) FROM hr__leave_details m 
                        WHERE employee__profile.id=m.emp_id
                        AND m.status="Approved"
                        AND m.is_adjusted="0"
                            GROUP BY employee__profile.id) ,"0" ) 
                        ) as leave_adjusted'),
                        DB::raw('(IFNULL(
                            (SELECT count(m.id) FROM hr__leave_details m 
                            WHERE employee__profile.id=m.emp_id
                            AND m.status="Approved"
                            AND YEAR(m.date)='.$a_date.'
                            AND m.is_adjusted="1"
                            AND MONTH(m.date)<='.$mon.'
                                GROUP BY employee__profile.id) ,"0" ) 
                            ) as total_leaves_till_now'),
                            DB::raw('(IFNULL(
                                (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                WHERE employee__profile.id=m.emp_id
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as opening_advance'),
                                DB::raw('(IFNULL(
                                    (SELECT sum(m.advance_paid) FROM payroll__advance m 
                                    WHERE employee__profile.id=m.emp_id
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as balance_advance'),
                                    DB::raw('(IFNULL(
                                        (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                        WHERE employee__profile.id=m.emp_id
                                        AND YEAR(m.given_date)='.$a_date.'
                                        AND MONTH(m.given_date)='.$mon.'
                                            GROUP BY employee__profile.id) ,"0" ) 
                                        ) as advance_in_month'),
                                        DB::raw('(IFNULL(
                                            (SELECT m.advance_amount-m.advance_paid FROM payroll__advance m 
                                            WHERE employee__profile.id=m.emp_id
                                            AND  m.advance_amount <> m.advance_paid
                                             GROUP BY employee__profile.id ) ,"0" ) 
                                            )  as advance_to_be_deducted'),
                                            DB::raw('(IFNULL(
                                                (SELECT installment FROM payroll__advance m 
                                                WHERE employee__profile.id=m.emp_id
                                                AND  m.advance_amount <> m.advance_paid
                                                 GROUP BY employee__profile.id ) ,"0" ) 
                                                )  as installment'),
                                                DB::raw('(IFNULL(
                                                    (SELECT m.installment_done FROM payroll__advance m 
                                                    WHERE employee__profile.id=m.emp_id
                                                    AND  m.advance_amount <> m.advance_paid
                                                     GROUP BY employee__profile.id ) ,"0" ) 
                                                    )  as installment_left'),
                                                    DB::raw('(IFNULL(
                                                        (SELECT m.advance_amount FROM payroll__advance m 
                                                        WHERE employee__profile.id=m.emp_id
                                                        AND  m.advance_amount <> m.advance_paid
                                                         GROUP BY employee__profile.id ) ,"0" ) 
                                                        )  as advance_to_install')


        )->GroupBy('employee__profile.id')->get()->first();
        $dd=cal_days_in_month(CAL_GREGORIAN, $mon, $a_date);
        $d_y=0; 
        for($month=1;$month<=12;$month++){ 
            $d_y = $d_y + cal_days_in_month(CAL_GREGORIAN,$month,$less_yr);
         }
         $month=['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'];
    
 
         $sal=NetSalary::where('emp_id',$emp)->whereMonth('month',$mon)->whereYear('month',$a_date)
         ->where('salary_type','=',"SalaryA")
         ->get()->first();
         if($sal){
             $flag=1;
         }
         else{
             $flag=0;
         }
        $data=[
            'employee'=>$employee,
            'dd'=>$dd,
            'd_y'=>$d_y,
            'mon'=>$mon,
            'month'=>$month,
            'flag'=>$flag
        ];
        // return $flag;
        
        return $data;

    }

    public function salaryA(Request $request){
       
        
        try {
            $pay=NetSalary::where('emp_id',$request->input('employee'))->where('payroll__salary.salary_type','=','SalaryA')->get()->first();
            if($pay!=NULL){
                return redirect('/salaryA/calculation')->with('error','Salary For this month has already calculated. ');
            }
            $adv=$request->input('advance');
            $timestamp = date('Y-m-d G:i:s');
            $advance=Advance::where('emp_id',$request->input('employee'))
            ->whereRaw('advance_amount != advance_paid')
            ->select('id','advance_paid','advance_amount',DB::raw('(advance_amount-advance_paid) as left_amt'))->get();
    
            foreach($advance as $key){
                if($key['left_amt']<=$adv && $adv>0){
                    $ad=Advance::where('emp_id',$request->input('employee'))
                    ->where('id',$key['id'])
                    ->update([
                        'installment_done'=>DB::raw('installment_done - 1'),
                        'advance_paid'=>DB::raw('advance_paid + '.$key['left_amt']),
                        'updated_at'=>$timestamp
                        ]);
                        if($ad==NULL){
                            DB::rollback();
                            return redirect('/salaryA/calculation')->with('error','some error occurred');
                        }
                        else{
                            $paid=PaidAdvance::insertGetId([
                                'advance_id'=>$key['id'],
                                'amount_paid'=>$adv,
                                'paid_category'=>'bySalary',
                                'created_by'=>Auth::id()
                            ]);
                            if($paid==NULL){
                                DB::rollback();
                                return redirect('/salaryA/calculation')->with('error','some error occurred');
                            }
                            else{
                                $adv=$adv-$key['left_amt'];
                            }
                        }

    
                  
                   
                }
                else if($key['left_amt']>$adv && $adv>0){
                    $ad=Advance::where('emp_id',$request->input('employee'))
                    ->where('id',$key['id'])->update([
                        'installment_done'=>DB::raw('installment_done - 1'),
                        'advance_paid'=>DB::raw('advance_paid + '.$adv),
                        'updated_at'=>$timestamp
                        
                        ]);
                        if($ad==NULL){
                            DB::rollback();
                            return redirect('/salaryA/calculation')->with('error','some error occurred');
                        }
                        else{
                            $paid=PaidAdvance::insertGetId([
                                'advance_id'=>$key['id'],
                                'amount_paid'=>$adv,
                                'paid_category'=>'bySalary',
                                'created_by'=>Auth::id()
                            ]);
                            if($paid==NULL){
                                DB::rollback();
                                return redirect('/salaryA/calculation')->with('error','some error occurred');
                            }
                            else{
                                $adv=0;
                            }
                        }
    
                    
                   
                }
                
            }
            
            $leave_adjusted=$request->input('leave_adjusted');
            $leave=HR_LeaveDetails::where('emp_id',$request->input('employee'))->where('is_adjusted',0)
            ->where('status','=',"Approved")
            ->limit($leave_adjusted)->update(['is_adjusted'=>2]);
           $payroll_salary=EmployeeProfile::where('employee__profile.id',$request->input('employee'))
           ->leftJoin('payroll', function($join) {
            $join->on('payroll.emp_id','=','employee__profile.id');
            $join->where('payroll.salary_type','=','SalaryA');
            })
           ->select( 'name','employee_number',DB::raw('IFNULL(payroll.basic_salary ,"0") as basic_salary'),
           DB::raw('IFNULL(payroll.dearness_allowance ,"0") as dearness_allowance'),
           DB::raw('IFNULL(payroll.overtime ,"0") as overtime'),
           DB::raw('IFNULL(payroll.bonus ,"0") as bonus'),
           DB::raw('IFNULL(payroll.hra ,"0") as hra'),
           DB::raw('IFNULL(payroll.conveyance ,"0") as conveyance'),
           DB::raw('IFNULL(payroll.telephone ,"0") as telephone'),
           DB::raw('IFNULL(payroll.other ,"0") as other'))
           ->get()->first();
           
            $date="01-".$request->input('date');
            $date=date('Y-m-d',strtotime($date));
            
            $salary=NetSalary::insertGetId([
                'emp_id'=>$request->input('employee'),
                'salary_type'=>"SalaryA",
                'month'=>$date,
                'month_name'=>date('M',strtotime($date)),
                'net_salary'=>$request->input('salary'),
                'ot_in_min'=>$request->input('ot')   ,
                'late_in_min'=>$request->input('late')   ,
                'no_working_days'=>$request->input('days'),
                'no_half_day'=>$request->input('hd'),
                'total_p'=>$request->input('tot_p'),
                'total_a'=>$request->input('tot_a'),
                'effective_present'=>$request->input('effective_present'),
                'effective_absent'=>$request->input('effective_absent'),
                'leave_adjusted'=>$request->input('leave_adjusted'),
                'basic_salary'=>$payroll_salary['basic_salary'],
                'dearness_allowance'=>$payroll_salary['dearness_allowance'],
                'overtime'=>$payroll_salary['overtime'],
                'bonus'=>$payroll_salary['bonus'],
                'hra'=>$payroll_salary['hra'],
                'conveyance'=>$payroll_salary['conveyance'],
                'telephone'=>$payroll_salary['telephone'],
                'other'=>$payroll_salary['other']
            ]);
            if($salary==NULL){
                DB::rollback();
                return redirect('/salaryA/calculation')->with('error','some error occurred');
            }
          
                $ss=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'PF',
                    'amount'=>$request->input('pf'),
                    'type'=>'DR',
                ]);
                if($ss==NULL){
                    DB::rollback();
                    return redirect('/salaryA/calculation')->with('error','some error occurred');
                }
                $ss1=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'ESI',
                    'amount'=>$request->input('esi'),
                    'type'=>'DR',
                ]);
                if($ss1==NULL){
                    DB::rollback();
                    return redirect('/salaryA/calculation')->with('error','some error occurred');
                }
                $ot=$request->input('ot');
                $late=$request->input('late');
                if($late>$ot){
                    $type="DR";
                }
                else{
                    $type="CR";
                }
                $ss2=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'OT',
                    'amount'=>$request->input('ot_ctc'),
                    'type'=>$type,
                ]);
                if($ss2==NULL){
                    DB::rollback();
                    return redirect('/salaryA/calculation')->with('error','some error occurred');
                }
                $ss3=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Basic Salary',
                    'amount'=>$request->input('salary_ctc'),
                    'type'=>'CR',
                ]);
                if($ss3==NULL){
                    DB::rollback();
                    return redirect('/salaryA/calculation')->with('error','some error occurred');
                }
                $ss4=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Advance',
                    'amount'=>$request->input('advance'),
                    'type'=>'DR',
                ]);
                if($ss4==NULL){
                    DB::rollback();
                    return redirect('/salaryA/calculation')->with('error','some error occurred');
                }
                $ss5=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Leave',
                    'amount'=>$request->input('leave_ded'),
                    'type'=>'DR',
                ]);
                if($ss5==NULL){
                    DB::rollback();
                    return redirect('/salaryA/calculation')->with('error','some error occurred');
                }
                $ss6=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Half Day',
                    'amount'=>$request->input('hd_deduction'),
                    'type'=>'DR',
                ]);
                if($ss6==NULL){
                    DB::rollback();
                    return redirect('/salaryA/calculation')->with('error','some error occurred');
                }
                $ss7=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Other Deduction',
                    'amount'=>$request->input('other_deduction'),
                    'type'=>'DR',
                ]);
                if($ss7==NULL){
                    DB::rollback();
                    return redirect('/salaryA/calculation')->with('error','some error occurred');
                }
                return redirect('/salaryA/calculation')->with('success','Successfully Salary Record Inserted And Generated');

        } catch (Exception $e) {
            return redirect('/salaryA/calculation')->with('error','some error occurred'.$ex->getMessage());
        }
        
    }

     //salaryB calculation
     public function salaryB_cal_form(){
        $d=date('Y-m-d');
        $mon=date('m');
        $employee=EmployeeProfile::leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->leftJoin('employee_fnf','employee_fnf.emp_id','employee__profile.id')
        // ->where('employee_fnf.leaving_date','>=',$d)
        ->Where('employee_fnf.emp_id',NULL)
        ->select(DB::raw('IFNULL(DATE_FORMAT(leaving_date,"%m"),"12") as leaving_date'),'employee__profile.id','name','employee_number')
        ->get();
        $data=[
            'layout' => 'layouts.main',
            'employee'=>$employee,
        ];
        // return $flag;
        
        return view('employee.Remuneration.salaryB_calculation',$data);
    }
    public function salaryB_cal(Request $request){
        
        $mo=explode('-',$request->input('mon'));
        $mon=$mo[0];
        $a_date=$mo[1];
       
        $emp=$request->input('emp');
        $less_yr=$a_date-1;
        $employee=EmployeeProfile::where('employee__profile.id',$emp)
        ->leftJoin('employee__pfesi','employee__pfesi.emp_id','employee__profile.id')
        ->leftJoin('payroll__attendance', function($join) use ($a_date,$mon){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date','=',$a_date);
            $join->WhereMonth('payroll__attendance.date','=',$mon);
       })
       ->leftJoin('payroll', function($join) use ($a_date,$mon){
        $join->on('payroll.emp_id','=','employee__profile.id');
        $join->where('payroll.salary_type','=','SalaryB');
        })
        ->leftJoin('leave__enhancement', function($join) use ($a_date,$mon){
            $join->on('leave__enhancement.emp_id','=','employee__profile.id');
            $join->WhereYear('leave__enhancement.year','=',$a_date);
            
        })
        ->leftJoin('hr__leave_details', function($join) use ($a_date,$mon){
            $join->on('hr__leave_details.emp_id','=','employee__profile.id');
            $join->whereMonth('hr__leave_details.date', '=', $mon);
            $join->whereYear('hr__leave_details.date', '=', $a_date);  
            $join->where('hr__leave_details.status', '=', "Approved"); 
        })
        ->leftJoin('payroll__advance', function($join) use ($a_date,$mon){
            $join->on('payroll__advance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__advance.given_date','=',$a_date);
            $join->WhereMonth('payroll__advance.given_date','=',$mon);
            
        })
        ->leftJoin('payroll__paid_advance', function($join) use ($a_date,$mon){
            $join->on('payroll__paid_advance.advance_id','=','payroll__advance.id');
        })
        ->select(
            'employee__profile.id',
            'employee__profile.name as emp_name',
            'employee__profile.employee_number',
            
            DB::raw('IFNULL(SUM(basic_salary+dearness_allowance+hra+conveyance+telephone+other),"0") as total_salaryB'),
            DB::raw('payroll.overtime as overtime_salaryB'),
            DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$a_date.'
                AND MONTH(m.date)='.$mon.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_absent_current'),
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    
                    AND YEAR(m.date)='.$a_date.'
                    AND MONTH(m.date)='.$mon.'
                    AND m.status <> "A"
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_present_current'),
                DB::raw('IFNULL(group_concat(hr__leave_details.date),"-") as leave_dates'),
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    AND YEAR(m.date)='.$less_yr.'
                    AND m.status <> "A"
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_present_pre'),
                   
                            DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                            DB::raw('(IFNULL(
                                (SELECT SUM(TIME_TO_SEC(m.ot)/60) FROM payroll__attendance m 
                                WHERE employee__profile.id=m.emp_id
                                AND m.status="P"
                                AND YEAR(m.date)='.$a_date.'
                                AND MONTH(m.date)='.$mon.'
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as ot'),
                                DB::raw('(IFNULL(
                                    (SELECT SUM(TIME_TO_SEC(m.late_by)/60) FROM payroll__attendance m 
                                    WHERE employee__profile.id=m.emp_id
                                    AND m.status="P"
                                    AND YEAR(m.date)='.$a_date.'
                                    AND MONTH(m.date)='.$mon.'
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as late_by'),
                                    DB::raw('(IFNULL(
                                        (SELECT SUM(TIME_TO_SEC(m.other_time_deduction)/60) FROM payroll__attendance m 
                                        WHERE employee__profile.id=m.emp_id
                                        AND m.status="P"
                                        AND YEAR(m.date)='.$a_date.'
                                        AND MONTH(m.date)='.$mon.'
                                            GROUP BY employee__profile.id) ,"0" ) 
                                        ) as other_time_deduction'),
                                        DB::raw('(IFNULL(
                                            (SELECT count(m.id) FROM payroll__attendance m 
                                            WHERE employee__profile.id=m.emp_id
                                            AND m.status="P"
                                            AND m.half_day="Yes"
                                            AND YEAR(m.date)='.$a_date.'
                                            AND MONTH(m.date)='.$mon.'
                                                GROUP BY employee__profile.id) ,"0" ) 
                                            ) as half_day'),
                    DB::raw('IFNULL(payroll__advance.advance_amount,"0") as advance_amount'),
                    DB::raw('IFNULL(payroll__advance.advance_paid,"0") as advance_deducted'),
                    DB::raw('count(hr__leave_details.id) as no_of_leaves'),
                    DB::raw('(IFNULL(
                        (SELECT count(m.id) FROM hr__leave_details m 
                        WHERE employee__profile.id=m.emp_id
                        AND m.status="Approved"
                        AND m.is_adjusted="0"
                            GROUP BY employee__profile.id) ,"0" ) 
                        ) as leave_adjusted'),
                        DB::raw('(IFNULL(
                            (SELECT count(m.id) FROM hr__leave_details m 
                            WHERE employee__profile.id=m.emp_id
                            AND m.status="Approved"
                            AND YEAR(m.date)='.$a_date.'
                            AND m.is_adjusted="1"
                            AND MONTH(m.date)<='.$mon.'
                                GROUP BY employee__profile.id) ,"0" ) 
                            ) as total_leaves_till_now'),
                            DB::raw('(IFNULL(
                                (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                WHERE employee__profile.id=m.emp_id
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as opening_advance'),
                                DB::raw('(IFNULL(
                                    (SELECT sum(m.advance_amount)-sum(m.advance_paid) FROM payroll__advance m 
                                    WHERE employee__profile.id=m.emp_id
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as balance_advance'),
                                    DB::raw('(IFNULL(
                                        (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                        WHERE employee__profile.id=m.emp_id
                                        AND YEAR(m.given_date)='.$a_date.'
                                        AND MONTH(m.given_date)='.$mon.'
                                            GROUP BY employee__profile.id) ,"0" ) 
                                        ) as advance_in_month'),
                                        DB::raw('(IFNULL(
                                            (SELECT m.advance_amount-m.advance_paid FROM payroll__advance m 
                                            WHERE employee__profile.id=m.emp_id
                                            AND  m.advance_amount <> m.advance_paid
                                             GROUP BY employee__profile.id ) ,"0" ) 
                                            )  as advance_to_be_deducted'),
                                            DB::raw('(IFNULL(
                                                (SELECT installment FROM payroll__advance m 
                                                WHERE employee__profile.id=m.emp_id
                                                AND  m.advance_amount <> m.advance_paid
                                                 GROUP BY employee__profile.id ) ,"0" ) 
                                                )  as installment'),
                                                DB::raw('(IFNULL(
                                                    (SELECT m.installment_done FROM payroll__advance m 
                                                    WHERE employee__profile.id=m.emp_id
                                                    AND  m.advance_amount <> m.advance_paid
                                                     GROUP BY employee__profile.id ) ,"0" ) 
                                                    )  as installment_left'),
                                                    DB::raw('(IFNULL(
                                                        (SELECT m.advance_amount FROM payroll__advance m 
                                                        WHERE employee__profile.id=m.emp_id
                                                        AND  m.advance_amount <> m.advance_paid
                                                         GROUP BY employee__profile.id ) ,"0" ) 
                                                        )  as advance_to_install')


        )->GroupBy('employee__profile.id')->get()->first();
        $dd=cal_days_in_month(CAL_GREGORIAN, $mon, $a_date);
        $d_y=0; 
        for($month=1;$month<=12;$month++){ 
            $d_y = $d_y + cal_days_in_month(CAL_GREGORIAN,$month,$less_yr);
         }
         $month=['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'];
    
 
         $sal=NetSalary::where('emp_id',$emp)->whereMonth('month',$mon)
         ->whereYear('month',$a_date)
         ->where('salary_type','=',"SalaryB")
         ->get()->first();
         if($sal){
             $flag=1;
         }
         else{
             $flag=0;
         }
        $data=[
            'employee'=>$employee,
            'dd'=>$dd,
            'd_y'=>$d_y,
            'mon'=>$mon,
            'month'=>$month,
            'flag'=>$flag
        ];
        return $data;
        
        // return view('employee.Remuneration.salaryA_calculation',$data);

    }

    public function salaryB(Request $request){
       
        // print_r();die;
        try {
            $pay=NetSalary::where('emp_id',$request->input('employee'))->where('payroll__salary.salary_type','=','SalaryB')->get()->first();
            if($pay!=NULL){
                return redirect('/salaryB/calculation')->with('error','Salary For this month has already calculated. ');
            }
            $adv=$request->input('advance');
            $timestamp = date('Y-m-d G:i:s');
            $advance=Advance::where('emp_id',$request->input('employee'))
            ->whereRaw('advance_amount != advance_paid')
            ->select('id','advance_paid','advance_amount',DB::raw('(advance_amount-advance_paid) as left_amt'))->get();
    
            foreach($advance as $key){
                if($key['left_amt']<=$adv && $adv>0){
                    $ad=Advance::where('emp_id',$request->input('employee'))
                    ->where('id',$key['id'])
                    ->update([
                        'installment_done'=>DB::raw('installment_done - 1'),
                        'advance_paid'=>DB::raw('advance_paid + '.$key['left_amt']),
                        'updated_at'=>$timestamp
                        ]);
                        if($ad==NULL){
                            DB::rollback();
                            return redirect('/salaryB/calculation')->with('error','some error occurred');
                        }
                        else{
                            $paid=PaidAdvance::insertGetId([
                                'advance_id'=>$key['id'],
                                'amount_paid'=>$adv,
                                'paid_category'=>'bySalary',
                                'created_by'=>Auth::id()
                            ]);
                            if($paid==NULL){
                                DB::rollback();
                                return redirect('/salaryB/calculation')->with('error','some error occurred');
                            }
                            else{
                                $adv=$adv-$key['left_amt'];
                            }
                        }

    
                  
                   
                }
                else if($key['left_amt']>$adv && $adv>0){
                    $ad=Advance::where('emp_id',$request->input('employee'))
                    ->where('id',$key['id'])->update([
                        'installment_done'=>DB::raw('installment_done - 1'),
                        'advance_paid'=>DB::raw('advance_paid + '.$adv),
                        'updated_at'=>$timestamp
                        
                        ]);
                        if($ad==NULL){
                            DB::rollback();
                            return redirect('/salaryB/calculation')->with('error','some error occurred');
                        }
                        else{
                            $paid=PaidAdvance::insertGetId([
                                'advance_id'=>$key['id'],
                                'amount_paid'=>$adv,
                                'paid_category'=>'bySalary',
                                'created_by'=>Auth::id()
                            ]);
                            if($paid==NULL){
                                DB::rollback();
                                return redirect('/salaryB/calculation')->with('error','some error occurred');
                            }
                            else{
                                $adv=0;
                            }
                        }
    
                    
                   
                }
                
            }

           
            $payroll_salary=EmployeeProfile::where('employee__profile.id',$request->input('employee'))
           ->leftJoin('payroll', function($join) {
            $join->on('payroll.emp_id','=','employee__profile.id');
            $join->where('payroll.salary_type','=','SalaryB');
            })
           ->select( 'name','employee_number',DB::raw('IFNULL(payroll.basic_salary ,"0") as basic_salary'),
           DB::raw('IFNULL(payroll.dearness_allowance ,"0") as dearness_allowance'),
           DB::raw('IFNULL(payroll.overtime ,"0") as overtime'),
           DB::raw('IFNULL(payroll.bonus ,"0") as bonus'),
           DB::raw('IFNULL(payroll.hra ,"0") as hra'),
           DB::raw('IFNULL(payroll.conveyance ,"0") as conveyance'),
           DB::raw('IFNULL(payroll.telephone ,"0") as telephone'),
           DB::raw('IFNULL(payroll.other ,"0") as other'))
           ->get()->first();

            $date="01-".$request->input('date');
            $date=date('Y-m-d',strtotime($date));
            $salary=NetSalary::insertGetId([
                'emp_id'=>$request->input('employee'),
                'salary_type'=>"SalaryB",
                'month'=>$date,
                'month_name'=>date('M',strtotime($date)),
                'net_salary'=>$request->input('salary'),
                'ot_in_min'=>$request->input('ot')   ,
                'late_in_min'=>$request->input('late')   ,
                'no_working_days'=>$request->input('days'),
                'no_half_day'=>$request->input('hd'),
                'total_p'=>$request->input('tot_p'),
                'total_a'=>$request->input('tot_a'),
                'effective_present'=>$request->input('effective_present'),
                'effective_absent'=>$request->input('effective_absent'),
                'leave_adjusted'=>0,
                'basic_salary'=>$payroll_salary['basic_salary'],
                'dearness_allowance'=>$payroll_salary['dearness_allowance'],
                'overtime'=>$payroll_salary['overtime'],
                'bonus'=>$payroll_salary['bonus'],
                'hra'=>$payroll_salary['hra'],
                'conveyance'=>$payroll_salary['conveyance'],
                'telephone'=>$payroll_salary['telephone'],
                'other'=>$payroll_salary['other']
            
            ]);
            if($salary==NULL){
                DB::rollback();
                return redirect('/salaryB/calculation')->with('error','some error occurred');
            }
          
                
            $ot=$request->input('ot');
            $late=$request->input('late');
            if($late>$ot){
                $type="DR";
            }
            else{
                $type="CR";
            }
            $ss2=NetSalaryDetails::insertGetId([
                'payroll_salary_id'=>$salary,
                'name'=>'OT',
                'amount'=>$request->input('ot_ctc'),
                'type'=>$type,
            ]);
                if($ss2==NULL){
                    DB::rollback();
                    return redirect('/salaryB/calculation')->with('error','some error occurred');
                }
                $ss3=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Basic Salary',
                    'amount'=>$request->input('salary_ctc'),
                    'type'=>'CR',
                ]);
                if($ss3==NULL){
                    DB::rollback();
                    return redirect('/salaryB/calculation')->with('error','some error occurred');
                }
                $ss4=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Advance',
                    'amount'=>$request->input('advance'),
                    'type'=>'DR',
                ]);
                if($ss4==NULL){
                    DB::rollback();
                    return redirect('/salaryB/calculation')->with('error','some error occurred');
                }
                $ss6=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Half Day',
                    'amount'=>$request->input('hd_deduction'),
                    'type'=>'DR',
                ]);
                if($ss6==NULL){
                    DB::rollback();
                    return redirect('/salaryB/calculation')->with('error','some error occurred');
                }
                $ss7=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Other Deduction',
                    'amount'=>$request->input('other_deduction'),
                    'type'=>'DR',
                ]);
                if($ss7==NULL){
                    DB::rollback();
                    return redirect('/salaryB/calculation')->with('error','some error occurred');
                }
                
                return redirect('/salaryB/calculation')->with('success','Successfully Salary Record Inserted And Generated');

        } catch (Exception $e) {
            return redirect('/salaryB/calculation')->with('error','some error occurred'.$ex->getMessage());
        }
        
    }

      //salaryA calculation
      public function salaryC_cal_form(){
        $d=date('Y-m-d');
        $mon=date('m');
        $employee=EmployeeProfile::leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->leftJoin('employee_fnf','employee_fnf.emp_id','employee__profile.id')
        // ->where('employee_fnf.leaving_date','>=',$d)
        ->Where('employee_fnf.emp_id',NULL)
        ->select(DB::raw('IFNULL(DATE_FORMAT(leaving_date,"%m"),"12") as leaving_date'),'employee__profile.id','name','employee_number')
        ->get();
        $data=[
            'layout' => 'layouts.main',
            'employee'=>$employee,
        ];
        // return $flag;
        
        return view('employee.Remuneration.salaryC_calculation',$data);
    }
    public function salaryC_cal(Request $request){
        
        $mo=explode('-',$request->input('mon'));
        $mon=$mo[0];
        $a_date=$mo[1];
       
        $emp=$request->input('emp');
        $less_yr=$a_date-1;
        $employee=EmployeeProfile::where('employee__profile.id',$emp)
        ->leftJoin('employee__pfesi','employee__pfesi.emp_id','employee__profile.id')
        ->leftJoin('payroll__attendance', function($join) use ($a_date,$mon){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date','=',$a_date);
            $join->WhereMonth('payroll__attendance.date','=',$mon);
       })
       ->leftJoin('payroll', function($join) use ($a_date,$mon){
        $join->on('payroll.emp_id','=','employee__profile.id');
        $join->where('payroll.salary_type','=','SalaryC');
        })
        ->leftJoin('leave__enhancement', function($join) use ($a_date,$mon){
            $join->on('leave__enhancement.emp_id','=','employee__profile.id');
            $join->WhereYear('leave__enhancement.year','=',$a_date);
            
        })
        ->leftJoin('hr__leave_details', function($join) use ($a_date,$mon){
            $join->on('hr__leave_details.emp_id','=','employee__profile.id');
            $join->whereMonth('hr__leave_details.date', '=', $mon);
            $join->whereYear('hr__leave_details.date', '=', $a_date);  
            $join->where('hr__leave_details.status', '=', "Approved"); 
        })
        ->leftJoin('payroll__advance', function($join) use ($a_date,$mon){
            $join->on('payroll__advance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__advance.given_date','=',$a_date);
            $join->WhereMonth('payroll__advance.given_date','=',$mon);
            
        })
        ->leftJoin('payroll__salary as salA', function($join) use ($a_date,$mon){
            $join->on('salA.emp_id','=','employee__profile.id');
            $join->WhereYear('salA.month','=',$a_date);
            $join->WhereMonth('salA.month','=',$mon);
            $join->Where('salA.salary_type','=',"SalaryA");
            
        })
        ->leftJoin('payroll_salary_detail as overA', function($join) use ($a_date,$mon){
            $join->on('overA.payroll_salary_id','=','salA.id');
            $join->Where('overA.name','=',"OT");
            
        })

        ->leftJoin('payroll__salary as salB', function($join) use ($a_date,$mon){
            $join->on('salB.emp_id','=','employee__profile.id');
            $join->WhereYear('salB.month','=',$a_date);
            $join->WhereMonth('salB.month','=',$mon);
            $join->Where('salB.salary_type','=',"SalaryB");
            
        })
        ->leftJoin('payroll_salary_detail as overB', function($join) use ($a_date,$mon){
            $join->on('overB.payroll_salary_id','=','salB.id');
            $join->Where('overB.name','=',"OT");
            
        })

        ->leftJoin('payroll__paid_advance', function($join) use ($a_date,$mon){
            $join->on('payroll__paid_advance.advance_id','=','payroll__advance.id');
        })
        ->select(
            'employee__profile.id',
            'employee__profile.name as emp_name',
            'employee__profile.employee_number',
            DB::raw('IFNULL(overA.amount,"0") as overA'),
            DB::raw('IFNULL(overB.amount,"0") as overB'),
            DB::raw('(IFNULL(
                (SELECT m.pf_applicable FROM payroll m 
                WHERE employee__profile.id=m.emp_id
                AND m.salary_type="SalaryC"
                    GROUP BY employee__profile.id) ,"No" ) 
                ) as pf'),
                DB::raw('(IFNULL(
                    (SELECT m.esi_applicable FROM payroll m 
                    WHERE employee__profile.id=m.emp_id
                    AND m.salary_type="SalaryC"
                        GROUP BY employee__profile.id) ,"No" ) 
                    ) as esi'),
            // DB::raw('IFNULL(employee__pfesi.pf,"0") as pf'),
            // DB::raw('IFNULL(employee__pfesi.esi,"0") as esi'),
            // DB::raw('IFNULL(SUM(basic_salary+dearness_allowance+hra+conveyance+telephone+other),"0") as total_salaryC'),
            DB::raw('(IFNULL(
                (SELECT sum(basic_salary+dearness_allowance+hra+conveyance+telephone+other) FROM payroll m 
                WHERE employee__profile.id=m.emp_id
                AND m.salary_type="SalaryC"
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_salaryC'),
            
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    
                    AND YEAR(m.date)='.$a_date.'
                    AND MONTH(m.date)='.$mon.'
                    AND m.status <> "A"
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_present_current'),
            DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$a_date.'
                AND MONTH(m.date)='.$mon.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_absent_current'),
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    
                    AND YEAR(m.date)='.$a_date.'
                    AND MONTH(m.date)='.$mon.'
                    AND m.status <> "A"
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_present_current'),
                DB::raw('IFNULL(group_concat(hr__leave_details.date),"-") as leave_dates'),
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    AND YEAR(m.date)='.$less_yr.'
                    AND m.status <> "A"
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_present_pre'),
                    DB::raw('(IFNULL(
                        (SELECT m.basic_salary FROM payroll m 
                        WHERE employee__profile.id=m.emp_id
                        AND m.salary_type="SalaryC"
                            GROUP BY employee__profile.id) ,"0" ) 
                        ) as basic_salaryC'),
                        DB::raw('(IFNULL(
                            (SELECT m.dearness_allowance FROM payroll m 
                            WHERE employee__profile.id=m.emp_id
                            AND m.salary_type="SalaryC"
                                GROUP BY employee__profile.id) ,"0" ) 
                            ) as DA_C'),
                            DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
                            DB::raw('(IFNULL(
                                (SELECT SUM(TIME_TO_SEC(m.ot)/60) FROM payroll__attendance m 
                                WHERE employee__profile.id=m.emp_id
                                AND m.status="P"
                                AND YEAR(m.date)='.$a_date.'
                                AND MONTH(m.date)='.$mon.'
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as ot'),
                                DB::raw('(IFNULL(
                                    (SELECT count(m.id) FROM payroll__attendance m 
                                    WHERE employee__profile.id=m.emp_id
                                    AND m.status="P"
                                    AND m.half_day="Yes"
                                    AND YEAR(m.date)='.$a_date.'
                                    AND MONTH(m.date)='.$mon.'
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as half_day'),
                                DB::raw('(IFNULL(
                                    (SELECT SUM(TIME_TO_SEC(m.late_by)/60) FROM payroll__attendance m 
                                    WHERE employee__profile.id=m.emp_id
                                    AND m.status="P"
                                    AND YEAR(m.date)='.$a_date.'
                                    AND MONTH(m.date)='.$mon.'
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as late_by'),
                                    DB::raw('(IFNULL(
                                        (SELECT SUM(TIME_TO_SEC(m.other_time_deduction)/60) FROM payroll__attendance m 
                                        WHERE employee__profile.id=m.emp_id
                                        AND m.status="P"
                                        AND YEAR(m.date)='.$a_date.'
                                        AND MONTH(m.date)='.$mon.'
                                            GROUP BY employee__profile.id) ,"0" ) 
                                        ) as other_time_deduction'),
                    DB::raw('IFNULL(payroll__advance.advance_amount,"0") as advance_amount'),
                    DB::raw('IFNULL(payroll__advance.advance_paid,"0") as advance_deducted'),
                    DB::raw('count(hr__leave_details.id) as no_of_leaves'),
                    DB::raw('(IFNULL(
                        (SELECT count(m.id) FROM hr__leave_details m 
                        WHERE employee__profile.id=m.emp_id
                        AND m.status="Approved"
                        AND m.is_adjusted="0"
                            GROUP BY employee__profile.id) ,"0" ) 
                        ) as leave_adjusted'),
                        DB::raw('(IFNULL(
                            (SELECT count(m.id) FROM hr__leave_details m 
                            WHERE employee__profile.id=m.emp_id
                            AND m.status="Approved"
                            AND YEAR(m.date)='.$a_date.'
                            AND m.is_adjusted="1"
                            AND MONTH(m.date)<='.$mon.'
                                GROUP BY employee__profile.id) ,"0" ) 
                            ) as total_leaves_till_now'),
                            DB::raw('(IFNULL(
                                (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                WHERE employee__profile.id=m.emp_id
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as opening_advance'),
                                DB::raw('(IFNULL(
                                    (SELECT sum(m.advance_amount)-sum(m.advance_paid) FROM payroll__advance m 
                                    WHERE employee__profile.id=m.emp_id
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as balance_advance'),
                                    DB::raw('(IFNULL(
                                        (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                        WHERE employee__profile.id=m.emp_id
                                        AND YEAR(m.given_date)='.$a_date.'
                                        AND MONTH(m.given_date)='.$mon.'
                                            GROUP BY employee__profile.id) ,"0" ) 
                                        ) as advance_in_month'),
                                        DB::raw('(IFNULL(
                                            (SELECT m.advance_amount-m.advance_paid FROM payroll__advance m 
                                            WHERE employee__profile.id=m.emp_id
                                            AND  m.advance_amount <> m.advance_paid
                                             GROUP BY employee__profile.id ) ,"0" ) 
                                            )  as advance_to_be_deducted'),
                                            DB::raw('(IFNULL(
                                                (SELECT installment FROM payroll__advance m 
                                                WHERE employee__profile.id=m.emp_id
                                                AND  m.advance_amount <> m.advance_paid
                                                 GROUP BY employee__profile.id ) ,"0" ) 
                                                )  as installment'),
                                                DB::raw('(IFNULL(
                                                    (SELECT m.installment_done FROM payroll__advance m 
                                                    WHERE employee__profile.id=m.emp_id
                                                    AND  m.advance_amount <> m.advance_paid
                                                     GROUP BY employee__profile.id ) ,"0" ) 
                                                    )  as installment_left'),
                                                    DB::raw('(IFNULL(
                                                        (SELECT m.advance_amount FROM payroll__advance m 
                                                        WHERE employee__profile.id=m.emp_id
                                                        AND  m.advance_amount <> m.advance_paid
                                                         GROUP BY employee__profile.id ) ,"0" ) 
                                                        )  as advance_to_install')


        )->GroupBy('employee__profile.id')->get()->first();
        $dd=cal_days_in_month(CAL_GREGORIAN, $mon, $a_date);
        $d_y=0; 
        for($month=1;$month<=12;$month++){ 
            $d_y = $d_y + cal_days_in_month(CAL_GREGORIAN,$month,$less_yr);
         }
         $month=['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'];
    
 
         $sal=NetSalary::where('emp_id',$emp)->whereMonth('month',$mon)->whereYear('month',$a_date)
         ->where('salary_type','=',"SalaryC")
         ->get()->first();
         if($sal){
             $flag=1;
         }
         else{
             $flag=0;
         }
        $data=[
            'employee'=>$employee,
            'dd'=>$dd,
            'd_y'=>$d_y,
            'mon'=>$mon,
            'month'=>$month,
            'flag'=>$flag
        ];
        return $data;
        
        // return view('employee.Remuneration.salaryA_calculation',$data);

    }

    public function salaryC(Request $request){
       
        // print_r();die;
        $payA=NetSalary::where('emp_id',$request->input('employee'))->where('payroll__salary.salary_type','=','SalaryA')->get()->first();
        $payB=NetSalary::where('emp_id',$request->input('employee'))->where('payroll__salary.salary_type','=','SalaryB')->get()->first();
        
        if($payA==NULL && $payB==NULL){
            return redirect('/salaryC/calculation')->with('error','SalaryA and SalaryB For this month has not been calculated. ');
        }
        else{
            if($payA==NULL){
                return redirect('/salaryC/calculation')->with('error','SalaryA  For this month has not been calculated. ');
            }
            if($payB==NULL){
                return redirect('/salaryC/calculation')->with('error','SalaryB For this month has not been calculated. ');
            } 
        }

        $pay=NetSalary::where('emp_id',$request->input('employee'))->where('payroll__salary.salary_type','=','SalaryC')->get()->first();
        if($pay!=NULL){
            return redirect('/salaryC/calculation')->with('error','Salary For this month has already calculated. ');
        }
        try {
            $adv=$request->input('advance');
            $timestamp = date('Y-m-d G:i:s');
            $advance=Advance::where('emp_id',$request->input('employee'))
            ->whereRaw('advance_amount != advance_paid')
            ->select('id','advance_paid','advance_amount',DB::raw('(advance_amount-advance_paid) as left_amt'))->get();
    
            foreach($advance as $key){
                if($key['left_amt']<=$adv && $adv>0){
                    $ad=Advance::where('emp_id',$request->input('employee'))
                    ->where('id',$key['id'])
                    ->update([
                        'installment_done'=>DB::raw('installment_done - 1'),
                        'advance_paid'=>DB::raw('advance_paid + '.$key['left_amt']),
                        'updated_at'=>$timestamp
                        ]);
                        if($ad==NULL){
                            DB::rollback();
                            return redirect('/salaryC/calculation')->with('error','some error occurred');
                        }
                        else{
                            $paid=PaidAdvance::insertGetId([
                                'advance_id'=>$key['id'],
                                'amount_paid'=>$adv,
                                'paid_category'=>'bySalary',
                                'created_by'=>Auth::id()
                            ]);
                            if($paid==NULL){
                                DB::rollback();
                                return redirect('/salaryC/calculation')->with('error','some error occurred');
                            }
                            else{
                                $adv=$adv-$key['left_amt'];
                            }
                        }

    
                  
                   
                }
                else if($key['left_amt']>$adv && $adv>0){
                    $ad=Advance::where('emp_id',$request->input('employee'))
                    ->where('id',$key['id'])->update([
                        'installment_done'=>DB::raw('installment_done - 1'),
                        'advance_paid'=>DB::raw('advance_paid + '.$adv),
                        'updated_at'=>$timestamp
                        
                        ]);
                        if($ad==NULL){
                            DB::rollback();
                            return redirect('/salaryA/calculation')->with('error','some error occurred');
                        }
                        else{
                            $paid=PaidAdvance::insertGetId([
                                'advance_id'=>$key['id'],
                                'amount_paid'=>$adv,
                                'paid_category'=>'bySalary',
                                'created_by'=>Auth::id()
                            ]);
                            if($paid==NULL){
                                DB::rollback();
                                return redirect('/salaryC/calculation')->with('error','some error occurred');
                            }
                            else{
                                $adv=0;
                            }
                        }
    
                    
                   
                }
                
            }

           

            $payroll_salary=EmployeeProfile::where('employee__profile.id',$request->input('employee'))
           ->leftJoin('payroll', function($join) {
            $join->on('payroll.emp_id','=','employee__profile.id');
            $join->where('payroll.salary_type','=','SalaryC');
            })
           ->select( 'name','employee_number',DB::raw('IFNULL(payroll.basic_salary ,"0") as basic_salary'),
           DB::raw('IFNULL(payroll.dearness_allowance ,"0") as dearness_allowance'),
           DB::raw('IFNULL(payroll.overtime ,"0") as overtime'),
           DB::raw('IFNULL(payroll.bonus ,"0") as bonus'),
           DB::raw('IFNULL(payroll.hra ,"0") as hra'),
           DB::raw('IFNULL(payroll.conveyance ,"0") as conveyance'),
           DB::raw('IFNULL(payroll.telephone ,"0") as telephone'),
           DB::raw('IFNULL(payroll.other ,"0") as other'))
           ->get()->first();

            $leave_adjusted=$request->input('leave_adjusted');
            $leave=HR_LeaveDetails::where('emp_id',$request->input('employee'))->where('is_adjusted',0)
            ->where('status','=',"Approved")
            ->limit($leave_adjusted)->update(['is_adjusted'=>2]);
           
            $date="01-".$request->input('date');
            $date=date('Y-m-d',strtotime($date));
            $salary=NetSalary::insertGetId([
                'emp_id'=>$request->input('employee'),
                'salary_type'=>"SalaryC",
                'month'=>$date,
                'month_name'=>date('M',strtotime($date)),
                'net_salary'=>$request->input('salary'),
                'ot_in_min'=>$request->input('ot')   ,
                'late_in_min'=>$request->input('late')   ,
                'no_working_days'=>$request->input('days'),
                'no_half_day'=>$request->input('hd'),
                'total_p'=>$request->input('tot_p'),
                'total_a'=>$request->input('tot_a'),
                'effective_present'=>$request->input('effective_present'),
                'effective_absent'=>$request->input('effective_absent'),
                'leave_adjusted'=>$request->input('leave_adjusted'),
                'basic_salary'=>$payroll_salary['basic_salary'],
                'dearness_allowance'=>$payroll_salary['dearness_allowance'],
                'overtime'=>$payroll_salary['overtime'],
                'bonus'=>$payroll_salary['bonus'],
                'hra'=>$payroll_salary['hra'],
                'conveyance'=>$payroll_salary['conveyance'],
                'telephone'=>$payroll_salary['telephone'],
                'other'=>$payroll_salary['other']
            
            ]);
            if($salary==NULL){
                DB::rollback();
                return redirect('/salaryC/calculation')->with('error','some error occurred');
            }
          
                $ss=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'PF',
                    'amount'=>$request->input('pf'),
                    'type'=>'DR',
                ]);
                if($ss==NULL){
                    DB::rollback();
                    return redirect('/salaryC/calculation')->with('error','some error occurred');
                }
                $ss1=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'ESI',
                    'amount'=>$request->input('esi'),
                    'type'=>'DR',
                ]);
                if($ss1==NULL){
                    DB::rollback();
                    return redirect('/salaryC/calculation')->with('error','some error occurred');
                }
                
                $ss2=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'OT',
                    'amount'=>$request->input('ot_ctc'),
                    'type'=>'CR',
                ]);
                if($ss2==NULL){
                    DB::rollback();
                    return redirect('/salaryC/calculation')->with('error','some error occurred');
                }
                $ss3=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Basic Salary',
                    'amount'=>$request->input('salary_ctc'),
                    'type'=>'CR',
                ]);
                if($ss3==NULL){
                    DB::rollback();
                    return redirect('/salaryC/calculation')->with('error','some error occurred');
                }
                $ss4=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Advance',
                    'amount'=>$request->input('advance'),
                    'type'=>'DR',
                ]);
                if($ss4==NULL){
                    DB::rollback();
                    return redirect('/salaryC/calculation')->with('error','some error occurred');
                }
                $ss5=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Leave',
                    'amount'=>$request->input('leave_ded'),
                    'type'=>'DR',
                ]);
                if($ss5==NULL){
                    DB::rollback();
                    return redirect('/salaryC/calculation')->with('error','some error occurred');
                }
                $ss6=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Half Day',
                    'amount'=>$request->input('hd_deduction'),
                    'type'=>'DR',
                ]);
                if($ss6==NULL){
                    DB::rollback();
                    return redirect('/salaryC/calculation')->with('error','some error occurred');
                }
                $ss7=NetSalaryDetails::insertGetId([
                    'payroll_salary_id'=>$salary,
                    'name'=>'Other Deduction',
                    'amount'=>$request->input('other_deduction'),
                    'type'=>'DR',
                ]);
                if($ss7==NULL){
                    DB::rollback();
                    return redirect('/salaryC/calculation')->with('error','some error occurred');
                }
                return redirect('/salaryC/calculation')->with('success','Successfully Salary Record Inserted And Generated');

        } catch (Exception $e) {
            return redirect('/salaryC/calculation')->with('error','some error occurred'.$ex->getMessage());
        }
        
    }
    //------------------------------------------------------------
	public function emp_list_for_salary(){
		$data = array(
			'layout' => 'layouts.main');
		return view('employee.Remuneration.emp_list_salary',$data);
	}
	public function working_employee_api(Request $request){
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
	public function salary_form($id){
		//employee details
		$emp = EmployeeProfile::where('id',$id)->get()->first();
		//employee salary details
		$employee_sal_all = EmployeeSalary::where('emp_id',$id)->get()->toArray();
		//sorting data salary type 
        $salary_gen = NetSalary::where("emp_id",$id)->get()->toArray();

		$salcategory = array();
        if($employee_sal_all){
            foreach($employee_sal_all as $emp_sal){
                $salcategory[$emp_sal['salary_type']]=$emp_sal;
            }
        }

		$data = array('emp'=>$emp,
			'layout' => 'layouts.main',
			'id'=>$id,
			'sal_data'=>$salcategory,
            'sal_given'=>count($salary_gen)
		);

		return view('employee.Remuneration.salary_form',$data);
	}
	public function salary_form_db(Request $request,$id){
		try {
			$timestamp = date('Y-m-d G:i:s');
			$sal_type = $request->input('sal_type');
            $salary_array = array('Salary A'=>'SalaryA','Salary B'=>'SalaryB','Salary C'=>'SalaryC');
            $salary_type =$salary_array[$sal_type];

			$employee_exist = EmployeeSalary::where('emp_id',$id)->where('salary_type',$salary_type)
            ->get()->toArray();
            
            if($employee_exist){
            	
            	if($salary_type=="SalaryA"){
                    $validerrarr =[
                        'basic_sal_a'=>'required',
                        'da_sal_a'=>'required',
                        'overtime_sal_a'=>'required',
                        'bonus_sal_a'=>'required',
                        'da_cat_sal_a'=>'required',
                        'update_reason_a'=>'required'
                    ];
                    $validmsgarr =[
                        'basic_sal_a.required'=>'This field is required',
                        'da_sal_a.required'=>'This field is required',
                        'overtime_sal_a.required'=>'This field is required',
                        'bonus_sal_a.required'=>'This field is required',
                        'da_cat_sal_a.required'=>'This field is required',
                        'update_reason_a.required'=>'This field is required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);

                    $sal_a_update = EmployeeSalary::where('emp_id',$id)->where('salary_type',$salary_type)->update([
                    	
                    	'basic_salary'=>$request->input('basic_sal_a'),
                    	'dearness_allowance'=>$request->input('da_sal_a'),
                    	'overtime'=>$request->input('overtime_sal_a'),
                    	'bonus'=>$request->input('bonus_sal_a'),
                    	'da_category'=>$request->input('da_cat_sal_a'),
                    	// 'update_reason'=>$request->input('update_reason_a'),
                    	'updated_at'=>$timestamp
                    ]);
                    if($sal_a_update==NULL){
                        DB::rollback();
                        return redirect('/employee/salary/form/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/salary/form/'.$id)->with('success','Successfully Updated Salary A.');      
                    }
                }
                if($salary_type=="SalaryB"){
                    $validerrarr =[
                        'overhead_sal_b'=>'required',
                        'overtime_sal_b'=>'required',
                        'update_reason_b'=>'required'
                    ];
                    $validmsgarr =[
                        'overhead_sal_b.required'=>'This field is required',
                        'overtime_sal_b.required'=>'This field is required',
                        'update_reason_b.required'=>'This field is required'                
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);

                    $sal_b_update = EmployeeSalary::where('emp_id',$id)->where('salary_type',$salary_type)->update([
                    	
                    	'basic_salary'=>$request->input('overhead_sal_b'),
                    	'overtime'=>$request->input('overtime_sal_b'),
                    	// 'update_reason'=>$request->input('update_reason_b'),
                    	'updated_at'=>$timestamp
                    ]);
                    if($sal_b_update==NULL){
                        DB::rollback();
                        return redirect('/employee/salary/form/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/salary/form/'.$id)->with('success','Successfully Updated Salary B.');      
                    }
                }
                if($salary_type=="SalaryC"){
                    $validerrarr =[
                        'basic_sal_c'=>'required',
                        'da_sal_c'=>'required',
                        'hra_sal_c'=>'required',
                        'conveyance_sal_c'=>'required',
                        'phone_sal_c'=>'required',
                        'other_sal_c'=>'required',
                        'bonus_sal_c'=>'required',
                        'update_reason_c'=>'required',
                        'pf_applicable_sal_c'=>'required',
                        'esi_applicable_sal_c'=>'required',
                        'wc_sal_c'=>'required',
                    ];
                    $validmsgarr =[
                        'basic_sal_c.required'=>'This field is required',
                        'da_sal_c.required'=>'This field is required',
                        'hra_sal_c.required'=>'This field is required',
                        'conveyance_sal_c.required'=>'This field is required',
                        'phone_sal_c.required'=>'This field is required',
                        'other_sal_c.required'=>'This field is required',
                        'bonus_sal_c.required'=>'This field is required',
                        'update_reason_c.required'=>'This field is required',                
                        'pf_applicable_sal_c.required'=>'This field is required',                
                        'esi_applicable_sal_c.required'=>'This field is required',                
                        'wc_sal_c.required'=>'This field is required'           

                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);

                    $sal_c_update = EmployeeSalary::where('emp_id',$id)->where('salary_type',$salary_type)->update([
                    	
                    	'basic_salary'=>$request->input('basic_sal_c'),
                    	'dearness_allowance'=>$request->input('da_sal_c'),
                    	'hra'=>$request->input('hra_sal_c'),
                    	'conveyance'=>$request->input('conveyance_sal_c'),
                    	'telephone'=>$request->input('phone_sal_c'),
                    	'bonus'=>$request->input('bonus_sal_c'),
                    	'other'=>$request->input('other_sal_c'),
                    	'pf_applicable'=>$request->input('pf_applicable_sal_c'),
                        'esi_applicable'=> $request->input('esi_applicable_sal_c'),
                        'wc_premium_amount'=> $request->input('wc_sal_c'),
                    	'updated_at'=>$timestamp
                    ]);
                    if($sal_c_update==NULL){
                        DB::rollback();
                        return redirect('/employee/salary/form/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/salary/form/'.$id)->with('success','Successfully Updated Salary C.');      
                    }
                }
            }else{

            	if($salary_type=="SalaryA"){
                    $validerrarr =[
                        'basic_sal_a'=>'required',
                        'da_sal_a'=>'required',
                        'overtime_sal_a'=>'required',
                        'bonus_sal_a'=>'required',
                        'da_cat_sal_a'=>'required'
                    ];
                    $validmsgarr =[
                        'basic_sal_a.required'=>'This field is required',
                        'da_sal_a.required'=>'This field is required',
                        'overtime_sal_a.required'=>'This field is required',
                        'bonus_sal_a.required'=>'This field is required',
                        'da_cat_sal_a.required'=>'This field is required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);

                    $sal_a_insert = EmployeeSalary::insertGetId([
                    	'emp_id'=>$id,
                    	'salary_type'=>$salary_type,
                    	'basic_salary'=>$request->input('basic_sal_a'),
                    	'dearness_allowance'=>$request->input('da_sal_a'),
                    	'overtime'=>$request->input('overtime_sal_a'),
                    	'bonus'=>$request->input('bonus_sal_a'),
                    	'da_category'=>$request->input('da_cat_sal_a'),
                    	'created_by'=>Auth::id(),
                    	'created_at'=>$timestamp
                    ]);
                    if($sal_a_insert==NULL){
                        DB::rollback();
                        return redirect('/employee/salary/form/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/salary/form/'.$id)->with('success','Successfully Created Salary A.');      
                    }
                }
                if($salary_type=="SalaryB"){
                    $validerrarr =[
                        'overhead_sal_b'=>'required',
                        'overtime_sal_b'=>'required'
                    ];
                    $validmsgarr =[
                        'overhead_sal_b.required'=>'This field is required',
                        'overtime_sal_b.required'=>'This field is required'                
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);

                    $sal_b_insert = EmployeeSalary::insertGetId([
                    	'emp_id'=>$id,
                    	'salary_type'=>$salary_type,
                    	'basic_salary'=>$request->input('overhead_sal_b'),
                    	'overtime'=>$request->input('overtime_sal_b'),
                    	'created_by'=>Auth::id(),
                    	'created_at'=>$timestamp
                    ]);
                    if($sal_b_insert==NULL){
                        DB::rollback();
                        return redirect('/employee/salary/form/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/salary/form/'.$id)->with('success','Successfully Created Salary B.');      
                    }
                }
                if($salary_type=="SalaryC"){
                    $validerrarr =[
                        'basic_sal_c'=>'required',
                        'da_sal_c'=>'required',
                        'hra_sal_c'=>'required',
                        'conveyance_sal_c'=>'required',
                        'phone_sal_c'=>'required',
                        'other_sal_c'=>'required',
                        'bonus_sal_c'=>'required',
                        'pf_applicable_sal_c'=>'required',
                        'esi_applicable_sal_c'=>'required',
                        'wc_sal_c'=>'required'
                    ];
                    $validmsgarr =[
                        'basic_sal_c.required'=>'This field is required',
                        'da_sal_c.required'=>'This field is required',
                        'hra_sal_c.required'=>'This field is required',
                        'conveyance_sal_c.required'=>'This field is required',
                        'phone_sal_c.required'=>'This field is required',
                        'other_sal_c.required'=>'This field is required',
                        'bonus_sal_c.required'=>'This field is required',
                        'pf_applicable_sal_c.required'=>'This field is required',
                        'esi_applicable_sal_c.required'=>'This field is required',
                        'wc_sal_c.required'=>'This field is required'
                    ];
                    $this->validate($request,$validerrarr,$validmsgarr);

                    $sal_c_insert = EmployeeSalary::insertGetId([
                    	'emp_id'=>$id,
                    	'salary_type'=>$salary_type,
                    	'basic_salary'=>$request->input('basic_sal_c'),
                    	'dearness_allowance'=>$request->input('da_sal_c'),
                    	'hra'=>$request->input('hra_sal_c'),
                    	'conveyance'=>$request->input('conveyance_sal_c'),
                    	'telephone'=>$request->input('phone_sal_c'),
                    	'bonus'=>$request->input('bonus_sal_c'),
                    	'other'=>$request->input('other_sal_c'),
                        'pf_applicable'=>$request->input('pf_applicable_sal_c'),
                        'esi_applicable'=> $request->input('esi_applicable_sal_c'),
                        'wc_premium_amount'=> $request->input('wc_sal_c'),
                    	'created_by'=>Auth::id(),
                    	'created_at'=>$timestamp
                    ]);
                    if($sal_c_insert==NULL){
                        DB::rollback();
                        return redirect('/employee/salary/form/'.$id)->with('error','Some Unexpected Error occurred.');
                    }
                    else{  
                        return redirect('/employee/salary/form/'.$id)->with('success','Successfully Created Salary C.');      
                    }
                }
            }

		} catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/employee/salary/form/'.$id)->with('error','some error occurred'.$ex->getMessage());
        }
	}
	public function salary_list_a_b(){
		$data = array(
			'layout' => 'layouts.main');
		return view('employee.Remuneration.salary_ab',$data);
	}
	public function salary_list_a_b_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = EmployeeSalary::leftJoin('employee__profile','employee__profile.id','payroll.emp_id')
	        ->whereIn('payroll.salary_type',array('SalaryA','SalaryB'))
	        ->select(
	        'employee__profile.name',
	        'employee__profile.employee_number',
	        DB::raw('GROUP_CONCAT(
	        CASE 
	        	WHEN 
		        	payroll.salary_type = "SalaryA" THEN CONCAT("a",":",
		            (
		                payroll.basic_salary + payroll.dearness_allowance 
		            ),":",payroll.overtime) 
	            WHEN 
	            	payroll.salary_type = "SalaryB" THEN CONCAT("b",":",
		            (
		                payroll.basic_salary 
		            ),":",payroll.overtime) 
	            ELSE 
	            	NULL
	    	END
			) AS total_salary'),DB::raw('SUM(payroll.overtime) as total_overtime'),
			DB::raw('(Sum(payroll.basic_salary) + SUM(payroll.dearness_allowance) ) total_ab')
	    )->groupby('payroll.emp_id');
	        
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%");
                    });
                        
        }

        $count = count($userlog->get());
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['payroll.emp_id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'total_salary',
            'total_salary',
            'total_salary',
            'total_salary',
            'total_overtime',
            'total_ab'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('emp_id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
  
    }
    public function salary_list_c(){
    	$data = array(
			'layout' => 'layouts.main');
		return view('employee.Remuneration.salary_c',$data);
    }
    public function salary_list_c_api(Request $request){
    	$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = EmployeeSalary::leftJoin('employee__profile','employee__profile.id','payroll.emp_id')
	        ->where('payroll.salary_type','SalaryC')
	        ->select('payroll.id',
	        'employee__profile.name',
	        'employee__profile.employee_number',
	        'payroll.basic_salary',
	        'payroll.dearness_allowance',
	        'payroll.hra',
	        'payroll.conveyance',
	        'payroll.telephone',
	        'payroll.other',
	        DB::raw('(payroll.basic_salary + payroll.dearness_allowance + payroll.hra + payroll.conveyance + payroll.telephone + payroll.other) as total_c')
	    );

        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%");
                    });
                        
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['payroll.id',
            'employee__profile.name',
            'employee__profile.employee_number',
            'basic_salary',
            'dearness_allowance',
            'hra',
            'conveyance',
            'telephone',
            'other','total_c'];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('payroll.id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }

    public function ctc_calculator(Request $request){
    	$emp_id= $request->input('empid');
    	$salary_c = EmployeeSalary::where('payroll.salary_type','SalaryC')
    	->where('emp_id',$emp_id)
	        ->select(
	        'payroll.basic_salary',
	        'payroll.dearness_allowance',
	        'payroll.hra',
	        'payroll.conveyance',
	        'payroll.telephone',
            'payroll.other',
	        'payroll.wc_premium_amount',
	        'bonus'
	    )->get()->first();
        $data = EmployeeSalary::where('payroll.salary_type','SalaryC')->where('emp_id',$emp_id)->get()->first();
        if($data['pf_applicable'] == 'Yes'){
	       $pf = round(($salary_c['basic_salary']+$salary_c['dearness_allowance'])*0.12,2);
        }else{
            $pf = 00.00;
        }
         if($data['esi_applicable'] == 'Yes'){
           $esi = round(($salary_c['basic_salary']+$salary_c['dearness_allowance'])*0.0325,2);
        }else{
            $esi = 00.00;
        }
	    
	    $leave_encash = round(((($salary_c['basic_salary']+$salary_c['dearness_allowance'])/30)*18)/12,2);
	    $bonus = round(($salary_c['bonus'])*0.12,2);
	    $gratuity = round((($salary_c['basic_salary']+$salary_c['dearness_allowance'])*15/26)/12,2);
	    $month_ctc = ($salary_c['basic_salary'] + $salary_c['hra'] + $salary_c['conveyance'] + $salary_c['telephone'] +$salary_c['other'] + $pf + $esi + $leave_encash + $bonus + $gratuity);
	    $year_ctc = $month_ctc*12;

	    $data = array('salary_c'=>$salary_c,
		'pf'=>$pf,
		'esi'=>$esi,
		'leave_encash'=>$leave_encash,
		'bonus'=>$bonus,
		'gratuity'=>$gratuity,
		'month_ctc'=>round($month_ctc, 2),
		'year_ctc'=>round($year_ctc, 2)
		);
	    return json_encode($data);
    }
    public function add_increment(){
    	$emp = EmployeeSalary::leftjoin('employee__profile','payroll.emp_id','employee__profile.id')
        ->select('employee__profile.*')->groupby('employee__profile.id')->get();

    	$data = array('employee'=>$emp,
			'layout' => 'layouts.main');
		return view('employee.Remuneration.add_increment',$data);
    }

    public function add_increment_db(Request $request){

    	try {
			$timestamp = date('Y-m-d G:i:s');
			
            $validerrarr =[
                'emp_name'=>'required',
                'month_name'=>'required',
                'amount_type'=>'required',
                'amount'=>'required',
                'incr_cat'=>'required',
                'incr_adjust_c'=>'required_if:incr_cat,"A basic"|required_if:selection,"B basic"'
            ];
            $validmsgarr =[
                'emp_name.required'=>'This field is required',
                'month_name.required'=>'This field is required',
                'amount_type.required'=>'This field is required',
                'amount.required'=>'This field is required',
                'incr_cat.required'=>'This field is required',
                'incr_adjust_c.required_if'=>'This field is required'
                
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            $present_month = date('F');
            $month_got = $request->input('month_name');
            $bit = 0;
            $get_all_salary =EmployeeSalary::where('emp_id',$request->input('emp_name'));
            $get_c =EmployeeSalary::where('emp_id',$request->input('emp_name'))->where("salary_type","SalaryC")->get()->first();
            //if increment in present month
            if($present_month == $month_got){

                if($request->input('incr_cat') == "A basic"){
                    $get_a_basic = $get_all_salary->where("salary_type","SalaryA")->get()->first();
                        // print_r($get_a_basic);
                    if($get_a_basic){
                        if($request->input('amount_type') == "cr"){
                            $final_a = $get_a_basic['basic_salary'] + $request->input('amount');
                        }else{
                            $final_a = $get_a_basic['basic_salary'] - $request->input('amount');
                        }
                        
                        $upd_salary = EmployeeSalary::where("id",$get_a_basic["id"])->update([
                            'basic_salary'=>$final_a
                        ]);
                    }
                    if($get_c){
                        // print_r($request->input('incr_adjust_c'));
                        if($request->input('incr_adjust_c') == "basic"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['basic_salary'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['basic_salary'] - $request->input('amount');
                            }
                           
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'basic_salary'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="da"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['dearness_allowance'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['dearness_allowance'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'dearness_allowance'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="hra"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['hra'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['hra'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'hra'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="conveyance"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['conveyance'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['conveyance'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'conveyance'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="telephone"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['telephone'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['telephone'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'telephone'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="other"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['other'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['other'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'other'=>$get_c_amt
                            ]);
                        }
                        
                    }
                    
                }else if($request->input('incr_cat') == "B basic"){
                    $get_b_basic = $get_all_salary->where("salary_type","SalaryB")->get()->first();
                    // print_r($get_b_basic);
                    if($get_b_basic){
                        if($request->input('amount_type') == "cr"){
                            $final_a = $get_b_basic['basic_salary'] + $request->input('amount');
                        }else{
                            $final_a = $get_b_basic['basic_salary'] - $request->input('amount');
                        }

                        $upd_salary = EmployeeSalary::where("id",$get_b_basic["id"])->update([
                            'basic_salary'=>$final_a
                        ]);
                    }
                    if($get_c){
                        if($request->input('incr_adjust_c') =="basic"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['basic_salary'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['basic_salary'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'basic_salary'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="da"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['dearness_allowance'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['dearness_allowance'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'dearness_allowance'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="hra"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['hra'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['hra'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'hra'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="conveyance"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['conveyance'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['conveyance'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'conveyance'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="telephone"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['telephone'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['telephone'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'telephone'=>$get_c_amt
                            ]);
                        }else if($request->input('incr_adjust_c') =="other"){
                            if($request->input('amount_type') == "cr"){
                                $get_c_amt = $get_c['other'] + $request->input('amount');
                            }else{
                                $get_c_amt = $get_c['other'] - $request->input('amount');
                            }
                            $upd_sal_c = EmployeeSalary::where("id",$get_c["id"])->update([
                                'other'=>$get_c_amt
                            ]);
                        }
                        
                    }   
                }else if($request->input('incr_cat') == "A overtime"){
                    $get_a_basic = $get_all_salary->where("salary_type","SalaryA")->get()->first();
                    // print_r($get_a_basic);
                    if($get_a_basic){
                        if($request->input('amount_type') == "cr"){
                            $final_a = $get_a_basic['overtime'] + $request->input('amount');
                        }else{
                            $final_a = $get_a_basic['overtime'] - $request->input('amount');
                        }

                        $upd_salary = EmployeeSalary::where("id",$get_a_basic["id"])->update([
                            'overtime'=>$final_a
                        ]);
                    }
                      
                }else if($request->input('incr_cat') == "B overtime"){
                    $get_b_basic = $get_all_salary->where("salary_type","SalaryB")->get()->first();
                    // print_r($get_b_basic);
                    if($get_b_basic){
                        if($request->input('amount_type') == "cr"){
                            $final_a = $get_b_basic['overtime'] + $request->input('amount');
                        }else{
                            $final_a = $get_b_basic['overtime'] - $request->input('amount');
                        }

                        $upd_salary = EmployeeSalary::where("id",$get_b_basic["id"])->update([
                            'overtime'=>$final_a
                        ]);
                    }
                     
                }
            
                if($upd_salary){
                    $bit =1;
                }
            }
            
            $increment = EmployeeIncrement::insertGetId([
            	'emp_id'=>$request->input('emp_name'),
            	'month_name'=>$request->input('month_name'),
            	'amount_type'=>$request->input('amount_type'),
            	'amount'=>$request->input('amount'),
            	'increment_cat'=>$request->input('incr_cat'),
            	'increment_adjust_c'=>$request->input('incr_adjust_c'),
                'added_insalary'=> $bit,
            	'created_by'=>Auth::id(),
            	'created_at'=>$timestamp
            ]);
            if($increment==NULL){
                DB::rollback();
                return redirect('/remuneration/increment/add')->with('error','Some Unexpected Error occurred.');
            }
            else{  
                return redirect('/remuneration/increment/add')->with('success','Successfully Created Increment.');      
            }

		} catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/remuneration/increment/add')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function increment_history(){
    	$emp = EmployeeSalary::leftjoin('employee__profile','payroll.emp_id','employee__profile.id')
        ->select('employee__profile.*')->groupby('employee__profile.id')->get();
    	$data = array('employee'=>$emp,'layout' => 'layouts.main');
		return view('employee.Remuneration.increment_history',$data);
    }
    public function increment_history_api(Request $request){
    	
    	$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $emp = $request->input("emp");
        // DB::EnablequeryLog();
        $userlog = EmployeeIncrement::leftJoin('payroll','payroll.emp_id','payroll__increment.emp_id')
	        ->select('payroll__increment.id',
	        'payroll__increment.emp_id',
	        'payroll__increment.month_name',
	        'payroll__increment.amount_type',
	        'payroll__increment.amount',
	        DB::raw('Concat(Upper(amount_type),"  ", amount)as his_amt'),
	        DB::raw('Case When payroll__increment.increment_cat = "A basic" THEN "Salary A Basic"
	        	When payroll__increment.increment_cat ="B basic" THEN "Salary B Basic"
	        	When payroll__increment.increment_cat ="A overtime" THEN "Overtime eligible on salary A"
	        	When payroll__increment.increment_cat ="B overtime" THEN "Overtime eligible on salary B"
	        	ELSE NULL
	        	END as increment_cat'),
	        DB::raw('Case When payroll__increment.increment_adjust_c = "basic" THEN "Basic"
                When payroll__increment.increment_adjust_c ="da" THEN "DA"
                When payroll__increment.increment_adjust_c ="conveyance" THEN "Conveyance Allowance"
                When payroll__increment.increment_adjust_c ="hra" THEN "HRA"
                When payroll__increment.increment_adjust_c ="telephone" THEN "Telephone Allowance"
                When payroll__increment.increment_adjust_c ="other" THEN "Other Allowance"
                ELSE NULL
                END as increment_adjust_c'),
	        DB::raw('GROUP_CONCAT(CASE 
	        	WHEN 
		        	payroll.salary_type = "SalaryA" THEN CONCAT("a",":",
		            (
		                payroll.basic_salary + payroll.dearness_allowance
		            ),":",payroll.overtime) 
	            WHEN 
	            	payroll.salary_type = "SalaryB" THEN CONCAT("b",":",
		            (
		                payroll.basic_salary
		            ),":",payroll.overtime) 
	            ELSE 
	            	NULL
	    	END)as total_sal'),'payroll__increment.added_insalary'
	    )->groupby('payroll__increment.id');
            // print_r(DB::getquerylog());die();
	    if(!empty($emp)){
	    	$userlog = $userlog->where(function($query) use ($emp){
                        $query->where('payroll__increment.emp_id',$emp);
                    });
	    }

        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('month_name','LIKE',"%".$serach_value."%")
                        ->orwhere('amount_type','LIKE',"%".$serach_value."%");
                    });
                        
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['payroll__increment.id',
            'payroll__increment.month_name',
            'his_amt',
            'increment_cat',
            'increment_adjust_c',
            'total_sal',
            'total_sal',
            'total_sal',
            'total_sal',
            'total_sal'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('payroll__increment.id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function increment_month_report(){
    	$data = array('layout' => 'layouts.main');
		return view('employee.Remuneration.increment_month_report',$data);
    }
    public function increment_month_report_api(Request $request){
    	
    	$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $month = $request->input("month");
        $year = $request->input("year");
        $userlog = EmployeeIncrement::leftJoin('payroll','payroll.emp_id','payroll__increment.emp_id')
        ->leftjoin('employee__profile','employee__profile.id','payroll__increment.emp_id')
	        ->select('payroll__increment.id',
	        	'employee__profile.name',
	        	'employee__profile.employee_number',
	        'payroll__increment.emp_id',
	        'payroll__increment.month_name',
	        'payroll__increment.amount_type',
	        'payroll__increment.amount',
	        DB::raw('Concat(Upper(amount_type),"  ", amount)as his_amt'),
	        DB::raw('Case When payroll__increment.increment_cat = "A basic" THEN "Salary A Basic"
	        	When payroll__increment.increment_cat ="B basic" THEN "Salary B Basic"
	        	When payroll__increment.increment_cat ="A overtime" THEN "Overtime eligible on salary A"
	        	When payroll__increment.increment_cat ="B overtime" THEN "Overtime eligible on salary B"
	        	ELSE NULL
	        	END as increment_cat'),
	        DB::raw('Case When payroll__increment.increment_adjust_c = "basic" THEN "Basic"
                When payroll__increment.increment_adjust_c ="da" THEN "DA"
                When payroll__increment.increment_adjust_c ="conveyance" THEN "Conveyance Allowance"
                When payroll__increment.increment_adjust_c ="hra" THEN "HRA"
                When payroll__increment.increment_adjust_c ="telephone" THEN "Telephone Allowance"
                When payroll__increment.increment_adjust_c ="other" THEN "Other Allowance"
                ELSE NULL
                END as increment_adjust_c'),
	        DB::raw('GROUP_CONCAT(CASE 
	        	WHEN 
		        	payroll.salary_type = "SalaryA" THEN CONCAT("a",":",
		            (
		                payroll.basic_salary + payroll.dearness_allowance
		            ),":",payroll.overtime) 
	            WHEN 
	            	payroll.salary_type = "SalaryB" THEN CONCAT("b",":",
		            (
		                payroll.basic_salary
		            ),":",payroll.overtime) 
	            ELSE 
	            	NULL
	    	END)as total_sal'),'payroll__increment.added_insalary'
	    )->groupby('payroll__increment.id');

	    if(!empty($month)){
	    	$userlog = $userlog->where(function($query) use ($month){
                        $query->where('payroll__increment.month_name',$month);
                    });
	    }
	    if(!empty($year)){
	    	$userlog = $userlog->where(function($query) use ($year){
                        $query->whereRaw('DATE_FORMAT(payroll__increment.created_at,"%Y")="'.$year.'"');
                    });
	    }
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('month_name','LIKE',"%".$serach_value."%")
                        ->orwhere('amount_type','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ;
                    });
                        
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['payroll__increment.id',
            'employee__profile.employee_number',
            'employee__profile.name',
            'payroll__increment.month_name',
            'his_amt',
            'increment_cat',
            'total_sal',
            'total_sal',
            'total_sal',
            'total_sal',
            'total_sal'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('payroll__increment.id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function inc_salary_c_report(){
    	$emp = EmployeeSalary::leftjoin('employee__profile','payroll.emp_id','employee__profile.id')
        ->select('employee__profile.*')->groupby('employee__profile.id')->get();
    	$data = array('employee'=>$emp,'layout' => 'layouts.main');
		return view('employee.Remuneration.inc_salary_c_report',$data);
    }
    public function inc_salary_c_report_api(Request $request){

    	$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $emp = $request->input("emp");
        
        $userlog = EmployeeIncrement::leftJoin('payroll','payroll.emp_id','payroll__increment.emp_id')
            ->where('payroll__increment.increment_cat',"A basic")
            ->orwhere('payroll__increment.increment_cat',"B basic")
	        ->select('payroll__increment.id',
	        'payroll__increment.emp_id',
	        'payroll__increment.month_name',
	        'payroll__increment.amount_type',
	        'payroll__increment.amount',
	        DB::raw('Concat(Upper(amount_type),"  ", amount)as his_amt'),
	        DB::raw('Case When payroll__increment.increment_cat = "A basic" THEN "Salary A Basic"
	        	When payroll__increment.increment_cat ="B basic" THEN "Salary B Basic"
	        	When payroll__increment.increment_cat ="A overtime" THEN "Overtime eligible on salary A"
	        	When payroll__increment.increment_cat ="B overtime" THEN "Overtime eligible on salary B"
	        	ELSE NULL
	        	END as increment_cat'),
            DB::raw('Case When payroll__increment.increment_adjust_c = "basic" THEN "Basic"
                When payroll__increment.increment_adjust_c ="da" THEN "DA"
                When payroll__increment.increment_adjust_c ="conveyance" THEN "Conveyance Allowance"
                When payroll__increment.increment_adjust_c ="hra" THEN "HRA"
                When payroll__increment.increment_adjust_c ="telephone" THEN "Telephone Allowance"
                When payroll__increment.increment_adjust_c ="other" THEN "Other Allowance"
                ELSE NULL
                END as increment_adjust_c')
	    )->groupby('payroll__increment.id');

	    if(!empty($emp)){
	    	$userlog = $userlog->where(function($query) use ($emp){
                        $query->where('payroll__increment.emp_id',$emp);
                    });
	    }
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('month_name','LIKE',"%".$serach_value."%")
                        ->orwhere('amount_type','LIKE',"%".$serach_value."%")
                        ->orwhere('increment_adjust_c','LIKE',"%".$serach_value."%")
                        ->orwhere('increment_cat','LIKE',"%".$serach_value."%")
                        ;
                    });
                        
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['payroll__increment.id',
            'payroll__increment.month_name',
            'his_amt',
            'increment_adjust_c'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $userlog->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $userlog->orderBy('payroll__increment.id','desc');
        }
        $userlogdata = $userlog->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $userlogdata; 
        return json_encode($array);
    }
    public function add_da_increment(){
    	$data = array('layout' => 'layouts.main');
		return view('employee.Remuneration.add_da_increment',$data);
    }
   	public function add_da_increment_db(Request $request){
    	try {
			$timestamp = date('Y-m-d G:i:s');
			
            $validerrarr =[
                
                'month_name'=>'required',
                'da_cat'=>'required',
                'sal_cat'=>'required',
                'amount'=>'required'
                
            ];
            $validmsgarr =[
                'month_name.required'=>'This field is required',
                'da_cat.required'=>'This field is required',
                'sal_cat.required'=>'This field is required',
                'amount.required'=>'This field is required'
                
            ];
            $this->validate($request,$validerrarr,$validmsgarr);

            $present_month = date('F');
            $month_got = $request->input('month_name');
            $bit = 0;
            $salary_category =$request->input('sal_cat');
            $da_category =$request->input('da_cat');
            

            if($present_month == $month_got){

                if($salary_category == "SalaryA"){
                    $get_sal_a = EmployeeSalary::where('salary_type',$salary_category)->where('da_category',$da_category)->get()->toArray();
                   
                    if($da_category == "skilled"){
                        foreach ($get_sal_a as $key) {
                            $da_val = $key['dearness_allowance'] + $request->input('amount');
                            $upd_salary_da = EmployeeSalary::where('id',$key['id'])->update([
                                'dearness_allowance'=>$da_val
                            ]);
                        }
                        
                    }else if($da_category == "semi-skilled"){
                        foreach ($get_sal_a as $key) {
                            $da_val = $key['dearness_allowance'] + $request->input('amount');
                            $upd_salary_da = EmployeeSalary::where('id',$key['id'])->update([
                                'dearness_allowance'=>$da_val
                            ]);
                        }
                    }else if($da_category == "unskilled"){
                        foreach ($get_sal_a as $key) {
                            $da_val = $key['dearness_allowance'] + $request->input('amount');
                            $upd_salary_da = EmployeeSalary::where('id',$key['id'])->update([
                                'dearness_allowance'=>$da_val
                            ]);
                        }
                    }

                }else if($salary_category == "SalaryC"){
                    $get_sal_c = EmployeeSalary::where('salary_type',$salary_category)->get()->toArray();
                    print_r($get_sal_c);
                    foreach ($get_sal_c as $key) {
                        $da_val = $key['dearness_allowance'] + $request->input('amount');
                        $upd_salary_da = EmployeeSalary::where('id',$key['id'])->update([
                            'dearness_allowance'=>$da_val
                        ]);
                    }
                }

                if(isset($upd_salary_da)){
                    $bit=1;
                }
            }
            
            $increment = IncrementDA::insertGetId([
            	'month_name'=>$request->input('month_name'),
            	'da_cat'=>$request->input('da_cat'),
            	'sal_cat'=>$request->input('sal_cat'),
            	'amount_inc'=>$request->input('amount'),
                'added_insalary'=>$bit,
            	'created_by'=>Auth::id(),
            	'created_at'=>$timestamp
            ]);
            if($increment==NULL){
                DB::rollback();
                return redirect('/da/increment/add')->with('error','Some Unexpected Error occurred.');
            }
            else{  
                return redirect('/da/increment/add')->with('success','Successfully Created DA Increment.');      
            }

		} catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/da/increment/add')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function da_increment_summary(){
    	$data = array('layout' => 'layouts.main');
		return view('employee.Remuneration.da_increment_summary',$data);
    }
    public function da_increment_summary_api(Request $request){
    	$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        
        $userlog = IncrementDA::select('payroll__da.*');
	    
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('month_name','LIKE',"%".$serach_value."%")
                        ->orwhere('da_cat','LIKE',"%".$serach_value."%")
                        ->orwhere('sal_cat','LIKE',"%".$serach_value."%")
                        ;
                    });
                        
        }
        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['id',
            'month_name',
            'da_cat',
            'sal_cat',
            'amount_inc'
            ];
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
    public function bonus_calculator(){
    	$finan = FinancialYear::all();
    	$data = array('finan'=>$finan,'layout' => 'layouts.main');
		return view('employee.Remuneration.bonus_calculator',$data);
    }
    public function bonus_get($finan){
        $datef_s_e = FinancialYear::where('id',$finan)->get()->first();
        $bonus_ctc=$datef_s_e['bonus_per'];
        return $bonus_ctc;
    }
    public function bonus_calculator_api(Request $request){
    	$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $finan = $request->input('fyear');
<<<<<<< HEAD
        DB::EnablequeryLog();
=======
        // DB::EnablequeryLog();
        $d=date('Y-m-d');
        $curr_fin=CustomHelpers::getFinancialFromDate($d);
        
>>>>>>> 6e254f2a84f09a2aecc6a64a2b90565ef11ed767
        $datef_s_e = FinancialYear::where('id',$finan)->get()->first();
        $last_date=date('Y-m-t',strtotime($datef_s_e['to'].'-01'));
        $start_date=date('Y-m-01',strtotime($datef_s_e['from'].'-01'));
        $start_month=date('m',strtotime($datef_s_e['from'].'-01'));

        $bonus_ctc=$datef_s_e['bonus_per'];
       
        if($datef_s_e['financial_year']==$curr_fin){
            $cur_month = date('n');
            // print_r($cur_month);die;
            $months = array();
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
            $yr=explode('-',$datef_s_e['financial_year']);
            $yr=$yr[0];
            $dt = DateTime::createFromFormat('y', $yr);
            $dt=$dt->format('Y'); // output: 2013
            $curr_date=date('Y-m-d');
            for($i =($num+1); $i <= $cur_month; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
        else{
            $yr=explode('-',$datef_s_e['financial_year']);
            $yr=$yr[0];
            $dt = DateTime::createFromFormat('y', $yr);
            $dt=$dt->format('Y'); // output: 2013
            $curr_date=$datef_s_e["to"].'-01';
            $curr_date=date('Y-m-t',strtotime($curr_date));
            $cur_month = date('n',strtotime($curr_date));
            $months = array();
            
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
    
            for($i =($num+1); $i <= 15; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
       
        $bonus = EmployeeProfile::leftjoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->whereNotBetween('employee__relieving.leaving_date',[$start_date,$last_date])
        ->orwhere('employee__relieving.id',NULL)
        ->where('employee__profile.doj','<=',$last_date)
        ->leftJoin('payroll',function($join) use ($last_date){
            $join->on('payroll.emp_id','=','employee__profile.id');
            $join->where('payroll.salary_type','=','SalaryA');
        })
        ->leftJoin('payroll__salary', function($join){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','=','SalaryA');
            })
        ->select(
            'employee__profile.name',
            'employee__profile.employee_number',
            'payroll.emp_id',

            DB::raw('(Select IFNULL(SUM(inc_da.amount_inc),0)
                FROM payroll__da as inc_da 
                WHERE inc_da.added_insalary=0 and FIND_IN_SET(inc_da.month_name, "'.$months.'")and (inc_da.created_at BETWEEN "'.$datef_s_e["from"].'-01" AND "'.$datef_s_e["to"].'-31"))as inc_da_t_d'),

            DB::raw('(IFNULL(payroll.basic_salary + payroll.dearness_allowance ,"0") )as total_sal_a'),

            DB::raw('IFNULL(payroll.bonus,"0") as bonus'),'employee__profile.doj',

            DB::raw('MONTHNAME(employee__profile.doj) as join_month'),

            DB::raw('TIMESTAMPDIFF(MONTH,employee__profile.doj, CURDATE()) as diff'),

            DB::raw('CASE WHEN (employee__profile.doj < "'.$datef_s_e["from"].'-01") then TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$curr_date.'")+1 ELSE TIMESTAMPDIFF(MONTH,employee__profile.doj, "'.$curr_date.'")+1  End as checkdiff'),

            DB::raw('(select GROUP_CONCAT(Concat(ida.month_name,":",ida.amount_inc)) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31")) as payroll_month'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$datef_s_e["from"].'-01") then 
                    CASE When ("'.$curr_date.'" >= "'.$datef_s_e["from"].'-01") and ("'.$curr_date.'" <= "'.$dt.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$curr_date.'")+1
                    ELSE
                    "6"
                    END
                 ELSE
                  CASE When ("'.$curr_date.'" >= "'.$datef_s_e["from"].'-01") and ("'.$curr_date.'" <= "'.$dt.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$curr_date.'")+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$dt.'-09-31")+1
                    END
                 END as apr_sep'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$dt.'-10-01") then 
                    CASE When ("'.$curr_date.'" >= "'.$dt.'-10-01") and ("'.$curr_date.'" <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$dt.'-10-01", "'.$curr_date.'")+1
                    ELSE
                    "0"
                    END
                WHEN (`employee__profile`.`doj` >= "'.$dt.'-10-01") THEN
                  CASE When ("'.$curr_date.'" >= "'.$dt.'-10-01") and ("'.$curr_date.'" <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$curr_date.'")+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$datef_s_e["to"].'-31")+1
                    END
                ElSE
                     0
                 END as oct_mar'),
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = employee__profile.id and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September")) as tot_sal_pre_apr'),
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = employee__profile.id and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March")) as tot_sal_pre_oct'),
           DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September"))as payroll_apr'),

            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March"))as payroll_oct')
        )->groupBy('employee__profile.id');
     //    ->get();
	    // print_r(DB::getqueryLog());die();

	    // if(!empty($datef_s_e)){
	    // 	$bonus = $bonus->where(function($query) use ($datef_s_e){
        //                 $query->whereRaw('(payroll.created_at BETWEEN "'.$datef_s_e["from"].'-01" And "'.$datef_s_e["to"].'-31")');
        //             });
	    // }
	  
        if(!empty($serach_value))
        {
            $bonus = $bonus->where(function($query) use ($serach_value){
                        $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                    
                        ;
                    });
                        
        }

        $count = count($bonus->get());
        $bonus = $bonus->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['payroll.emp_id',
            'employee_number',
            'name',
            'total_sal_a',
            'bonus',
            'checkdiff',
            'apr_sep',
            'oct_mar',
            'payroll_apr'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $bonus->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $bonus->orderBy('payroll.emp_id','desc');
        }
        $bonusdata = $bonus->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $bonusdata; 
        return json_encode($array);
    }
    public function bonus_calculator_left(){
    	$finan = FinancialYear::all();
    	$data = array('finan'=>$finan,'layout' => 'layouts.main');
		return view('employee.Remuneration.bonus_calculator_left',$data);
    }
    public function bonus_calculator_left_api(Request $request){
    	$search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $finan = $request->input('fyear');
        // DB::EnablequeryLog();
        $d=date('Y-m-d');
        $curr_fin=CustomHelpers::getFinancialFromDate($d);
        
        $datef_s_e = FinancialYear::where('id',$finan)->get()->first();
        $last_date=date('Y-m-t',strtotime($datef_s_e['to'].'-01'));
        $start_date=date('Y-m-t',strtotime($datef_s_e['from'].'-01'));
        $start_month=date('m',strtotime($datef_s_e['from'].'-01'));
       
        if($datef_s_e['financial_year']==$curr_fin){
            $cur_month = date('n');
            // print_r($cur_month);die;
            $months = array();
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
            $yr=explode('-',$datef_s_e['financial_year']);
            $yr=$yr[0];
            $dt = DateTime::createFromFormat('y', $yr);
            $dt=$dt->format('Y'); // output: 2013
            $curr_date=date('Y-m-d');
            for($i =($num+1); $i <= $cur_month; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
        else{
            $yr=explode('-',$datef_s_e['financial_year']);
            $yr=$yr[0];
            $dt = DateTime::createFromFormat('y', $yr);
            $dt=$dt->format('Y'); // output: 2013
            $curr_date=$datef_s_e["to"].'-01';
            $curr_date=date('Y-m-t',strtotime($curr_date));
            $cur_month = date('n',strtotime($curr_date));
            $months = array();
            
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
    
            for($i =($num+1); $i <= 15; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
        // $ss=$start_date+1;
        // print_r($start_date);
    
        $bonus = EmployeeRelieving::leftjoin('employee__profile','employee__relieving.emp_id','employee__profile.id')
        ->whereBetween('employee__relieving.leaving_date',[$start_date,$last_date])
        ->where('employee__relieving.leaving_date','!=',NULL)
        ->where('employee__profile.doj','<=',$last_date)
        ->leftJoin('payroll',function($join) use ($last_date){
            $join->on('payroll.emp_id','=','employee__profile.id');
            $join->where('payroll.salary_type','SalaryA');
        })
        ->leftJoin('payroll__salary', function($join){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','=','SalaryA');
            })
       ->select(
            'employee__profile.name',
            'employee__profile.employee_number',
            'payroll.emp_id',

            DB::raw('(Select IFNULL(SUM(inc_da.amount_inc),0)
                FROM payroll__da as inc_da 
                WHERE inc_da.added_insalary=0 and FIND_IN_SET(inc_da.month_name, "'.$months.'")and (inc_da.created_at BETWEEN "'.$datef_s_e["from"].'-01" AND "'.$datef_s_e["to"].'-31"))as inc_da_t_d'),

            DB::raw('(IFNULL(payroll.basic_salary + payroll.dearness_allowance ,"0") )as total_sal_a'),

            DB::raw('IFNULL(payroll.bonus,"0") as bonus'),'employee__profile.doj',

            DB::raw('MONTHNAME(employee__profile.doj) as join_month'),

            DB::raw('TIMESTAMPDIFF(MONTH,employee__profile.doj, employee__relieving.leaving_date) as diff'),

            DB::raw('CASE WHEN (employee__profile.doj < "'.$datef_s_e["from"].'-01") then TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", employee__relieving.leaving_date )+1 ELSE TIMESTAMPDIFF(MONTH,employee__profile.doj, employee__relieving.leaving_date )+1  End as checkdiff'),

            DB::raw('(select GROUP_CONCAT(Concat(ida.month_name,":",ida.amount_inc)) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and employee__relieving.leaving_date)) as payroll_month'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$datef_s_e["from"].'-01") then 
                    CASE When (employee__relieving.leaving_date >= "'.$datef_s_e["from"].'-01") and (employee__relieving.leaving_date <= "'.$dt.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", employee__relieving.leaving_date)+1
                    ELSE
                    "6"
                    END
                 ELSE
                  CASE When (employee__relieving.leaving_date >= "'.$datef_s_e["from"].'-01") and (employee__relieving.leaving_date <= "'.$dt.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, employee__relieving.leaving_date)+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$dt.'-09-31")+1
                    END
                 END as apr_sep'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$dt.'-10-01") then 
                    CASE When (employee__relieving.leaving_date >= "'.$dt.'-10-01") and (employee__relieving.leaving_date <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$dt.'-10-01", employee__relieving.leaving_date)+1
                    ELSE
                    "0"
                    END
                WHEN (`employee__profile`.`doj` >= "'.$dt.'-10-01") THEN
                  CASE When (employee__relieving.leaving_date >= "'.$dt.'-10-01") and (employee__relieving.leaving_date <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, employee__relieving.leaving_date)+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$datef_s_e["to"].'-31")+1
                    END
                ElSE
                     0
                 END as oct_mar'),
<<<<<<< HEAD

            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September"))as payroll_apr'),

            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March"))as payroll_oct')
        );
        

	    if(!empty($datef_s_e)){
	    	$bonus = $bonus->where(function($query) use ($datef_s_e){
                        $query->whereRaw('(payroll.created_at BETWEEN "'.$datef_s_e["from"].'-01" And "'.$datef_s_e["to"].'-31")');
                    });
	    }
	       $bonus->get();
        print_r(DB::getqueryLog());die();
=======
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = employee__profile.id and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September")) as tot_sal_pre_apr'),
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = employee__profile.id and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March")) as tot_sal_pre_oct'),
           
            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September"))as payroll_apr'),
            DB::raw('IFNULL((SELECT SUM(payroll__advance.advance_amount)-Sum(payroll__advance.advance_paid) from payroll__advance WHERE payroll__advance.emp_id =payroll.emp_id),0) as advance'),
            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = payroll.salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March"))as payroll_oct')
        )->groupBy('employee__profile.id');
     //    ->get();
	    // print_r(DB::getqueryLog());die();

	    // if(!empty($datef_s_e)){
	    // 	$bonus = $bonus->where(function($query) use ($datef_s_e){
        //                 $query->whereRaw('(payroll.created_at BETWEEN "'.$datef_s_e["from"].'-01" And "'.$datef_s_e["to"].'-31")');
        //             });
	    // }
	  
>>>>>>> 6e254f2a84f09a2aecc6a64a2b90565ef11ed767
        if(!empty($serach_value))
        {
            $bonus = $bonus->where(function($query) use ($serach_value){
                        $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        // ->orwhere('bonus','LIKE',"%".$serach_value."%")
                        ;
                    });
                        
        }

        $count = count($bonus->get());
        $bonus = $bonus->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['payroll.emp_id',
            'employee_number',
            'name',
            'total_sal_a',
            'bonus',
            'checkdiff',
            'apr_sep',
            'oct_mar',
            'payroll_apr'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $bonus->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $bonus->orderBy('payroll.emp_id','desc');
        }
        $bonusdata = $bonus->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $bonusdata; 
        return json_encode($array);
    }
    public function twelve_month_salary($empid,Request $request){
        $fyear =$request->input('fyear');
        $datef_s_e = FinancialYear::where('id',$fyear)->get()->first();
        $all_month_salary = NetSalary::where('emp_id',$empid)->whereRaw('(payroll__salary.month BETWEEN "'.$datef_s_e["from"].'-01" And "'.$datef_s_e["to"].'-31")')->select('payroll__salary.*',DB::raw('MonthName(`month`)as month_name'))->get()->toArray();
        // $emp_sal =EmployeeSalary::where('emp_id',$empid)->whereRaw('(payroll.created_at BETWEEN "'.$datef_s_e["from"].'-01" And "'.$datef_s_e["to"].'-31")')->where('salary_type','SalaryA')->select('payroll.*',DB::raw('payroll.basic_salary + payroll.dearness_allowance + payroll.overtime as total_sal'))
        // ->get()->first();
        // $da_for_a = IncrementDA::where('sal_cat',"SalaryA")->whereRaw('(created_at BETWEEN "'.$datef_s_e["from"].'-01" And "'.$datef_s_e["to"].'-31")')->where('added_insalary',0)->get()->toArray();
        // // DB::enableQuerylog();
        // $inc_of_a = EmployeeIncrement::whereRaw('(increment_cat="A basic" or increment_cat="A overtime") and (created_at BETWEEN "'.$datef_s_e["from"].'-01" And "'.$datef_s_e["to"].'-31") and emp_id='.$empid.' and added_insalary=0')->get()->toArray();
        // // print_r(DB::getQueryLog());
        $data=[
            'salary'=>$all_month_salary
            // 'da'=>$da_for_a,
            // 'increment'=>$inc_of_a
        ];

        // print_r($emp_sal);
        // print_r($da_for_a);
        // print_r($all_month_salary);
        // die();

        return view('employee.Remuneration.twelve_month_salary',$data);
    }
    public function advance_create(){
        $emp = EmployeeSalary::leftjoin('employee__profile','payroll.emp_id','employee__profile.id')
        ->select('employee__profile.*')->groupby('employee__profile.id')->get();
        $data = array('employee'=>$emp,'layout' => 'layouts.main');
        return view('employee.Remuneration.advance_create',$data);
    }
    public function advance_create_db(Request $request){
        try {
            $timestamp = date('Y-m-d G:i:s');
            
            $validerrarr =[
                
                'emp_name'=>'required',
                'adv_amt'=>'required',
                'given_date'=>'required',
                'deduct'=>'required',
                'adv_reason'=>'required',
                
            ];
            $validmsgarr =[
                'emp_name.required'=>'This field is required',
                'adv_amt.required'=>'This field is required',
                'given_date.required'=>'This field is required',
                'deduct.required'=>'This field is required',
                'adv_reason.required'=>'This field is required',
                
            ];
            $this->validate($request,$validerrarr,$validmsgarr);

            $advance = Advance::insertGetId([
                'emp_id'=>$request->input('emp_name'),
                'requested_amount'=>$request->input('adv_amt'),
                'given_date'=>date("Y-m-d",strtotime($request->input('given_date'))),
                'installment'=>$request->input('deduct'),
                'installment_done'=>$request->input('deduct'),
                'reason'=>$request->input('adv_reason'),
                'created_by'=>Auth::id(),
                'created_at'=>$timestamp
            ]);
            if($advance==NULL){
                DB::rollback();
                return redirect('/advance/create')->with('error','Some Unexpected Error occurred.');
            }
            else{  
                return redirect('/advance/create')->with('success','Successfully Created Advance.');      
            }

        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/advance/create')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function advance_summary(){
        $data = array('layout' => 'layouts.main');
        return view('employee.Remuneration.advance_summary',$data);
    }
    public function advance_summary_close_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQuerylog();
        $userlog = Advance::leftjoin('employee__profile','employee__profile.id','payroll__advance.emp_id')->select('payroll__advance.id',DB::raw('Concat(employee__profile.name,employee__profile.employee_number)as emp_name'),'payroll__advance.advance_amount',
            DB::raw('Date_Format(payroll__advance.given_date,"%d-%m-%Y")as given_date'),'payroll__advance.installment','payroll__advance.reason',
            DB::raw('(payroll__advance.advance_amount - payroll__advance.advance_paid) as advance_balance')
        )->whereRaw('advance_paid = advance_amount')->where('advance_amount','<>',0);
        // print_r(DB::getQuerylog()); die();
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('advance_amount','LIKE',"%".$serach_value."%")
                        ;
                    });
                        
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['id',
            'emp_name',
            'advance_amount',
            'advance_balance',
            'installment',
            'reason'
            ];
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
    public function advance_summary_open_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        // DB::enableQuerylog();
        $userlog = Advance::leftjoin('employee__profile','employee__profile.id','payroll__advance.emp_id')->select('payroll__advance.id',DB::raw('Concat(employee__profile.name,employee__profile.employee_number)as emp_name'),'payroll__advance.advance_amount','payroll__advance.requested_amount',
            DB::raw('Date_Format(payroll__advance.given_date,"%d-%m-%Y")as given_date'),'payroll__advance.installment','payroll__advance.reason',
            DB::raw('(payroll__advance.advance_amount - payroll__advance.advance_paid) as advance_balance')
        )->whereRaw('advance_paid <> advance_amount')->orwhere('advance_amount',0);
        // print_r($userlog); die();
        if(!empty($serach_value))
        {
            $userlog = $userlog->where(function($query) use ($serach_value){
                        $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee__profile.employee_number','LIKE',"%".$serach_value."%")
                        ->orwhere('advance_amount','LIKE',"%".$serach_value."%")
                        ;
                    });
                        
        }

        $count = $userlog->count();
        $userlog = $userlog->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['id',
            'emp_name',
            'requested_amount',
            'advance_amount',
            'advance_balance',
            'installment',
            'reason'
            ];
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
    public function advance_deduction(){
        $employee = Advance::whereRaw('advance_paid <> advance_amount')->leftjoin('employee__profile','employee__profile.id','payroll__advance.emp_id')->select(DB::raw('Concat(employee__profile.name,"(",employee__profile.employee_number,")")as emp_name'),'employee__profile.id as emp_id')->groupby('employee__profile.id')->get();
        $data = array('employee'=>$employee,'layout' => 'layouts.main');
        return view('employee.Remuneration.advance_deduction',$data);
    }
    public function advance_fetch_record(Request $request){
        $emp =$request->input('empid');
        // db::enableQuerylog();
        $record = Advance::where('emp_id',$emp)->whereRaw('advance_paid <> advance_amount')->select('payroll__advance.*',DB::raw('(payroll__advance.advance_amount - payroll__advance.advance_paid) as advance_balance'))->get()->first();
        // print_r(db::getQuerylog());die();
        
        return json_encode($record);
    }
    public function advance_deduction_db(Request $request){
        try {
            $timestamp = date('Y-m-d G:i:s');
            
            $validerrarr =[
                
                'emp_name'=>'required',
                'deduct_adv'=>'required'
                
                
            ];
            $validmsgarr =[
                'emp_name.required'=>'This field is required',
                'deduct_adv.required'=>'This field is required',
                
                
            ];
            $this->validate($request,$validerrarr,$validmsgarr);

            $paid_adv = PaidAdvance::insertGetId([
                'advance_id'=>$request->input('adv_id'),
                'amount_paid'=>$request->input('deduct_adv'),
                'paid_category'=>"byHand",
                'created_by'=>Auth::id(),
                'created_at'=>$timestamp
            ]);

            $get_ded_paid = Advance::where('id',$request->input('adv_id'))->get()->first();
            $amount = $get_ded_paid['advance_paid']+$request->input('deduct_adv');
            
            $deduct = Advance::where('id',$request->input('adv_id'))->update([
                'advance_paid'=>$amount,
                'updated_at'=>$timestamp
            ]);
            if($deduct==NULL){
                DB::rollback();
                return redirect('/advance/deduction')->with('error','Some Unexpected Error occurred.');
            }
            else{  
                return redirect('/advance/deduction')->with('success','Successfully Deduction Done.');      
            }

        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/advance/deduction')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function bonus_to_be_paid(){
        $finan = FinancialYear::all();
        $data = array('finan'=>$finan,'layout' => 'layouts.main');
        return view('employee.Remuneration.bonus_to_be_paid',$data);
    }
    public function bonus_to_be_paid_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $finan = $request->input('fyear');
        
        $d=date('Y-m-d');
        $curr_fin=CustomHelpers::getFinancialFromDate($d);
        
        $datef_s_e = FinancialYear::where('id',$finan)->get()->first();
        $last_date=date('Y-m-t',strtotime($datef_s_e['to'].'-01'));
        $start_date=date('Y-m-t',strtotime($datef_s_e['from'].'-01'));
        $start_month=date('m',strtotime($datef_s_e['from'].'-01'));
       
        if($datef_s_e['financial_year']==$curr_fin){
            $cur_month = date('n');
            // print_r($cur_month);die;
            $curr=date('Y-m-d');
            $months = array();
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
            $yr=explode('-',$datef_s_e['financial_year']);
            $yr=$yr[0];
            $dt = DateTime::createFromFormat('y', $yr);
            $dt=$dt->format('Y'); // output: 2013
            $curr_date=date('Y-m-d');
            for($i =($num+1); $i <= $cur_month; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
        else{
            $yr=explode('-',$datef_s_e['financial_year']);
            $yr=$yr[0];
            $dt = DateTime::createFromFormat('y', $yr);
            $dt=$dt->format('Y'); // output: 2013
            $curr_date=$datef_s_e["to"].'-01';
            $curr_date=date('Y-m-t',strtotime($curr_date));
            $cur_month = date('n',strtotime($curr_date));
            $months = array();
            $curr=$last_date;
            $num = date("n",strtotime($datef_s_e["from"].'-01'));
            array_push($months, date("F", strtotime($datef_s_e["from"].'-01')));
    
            for($i =($num+1); $i <= 15; $i++){
                $dateObj = DateTime::createFromFormat('!m', $i);
                array_push($months, $dateObj->format('F'));
            }
            $months = implode(",", $months);
        }
      
        $bonus = EmployeeProfile::where('employee__profile.doj','<=',$last_date)
        ->leftJoin('employee__relieving','employee__relieving.emp_id','employee__profile.id')
        ->where('employee__relieving.id',NULL)
        ->orwhereNotBetween('employee__relieving.leaving_date',[$start_date,$curr])
        ->leftJoin('payroll',function($join) use ($last_date){
            $join->on('payroll.emp_id','=','employee__profile.id');
            $join->where('salary_type','SalaryA');
        })
       
        ->select(
            'employee__profile.name',
            'employee__profile.employee_number',
            'payroll.emp_id',
            DB::raw('(Select IFNULL(SUM(inc_da.amount_inc),0)
                FROM payroll__da as inc_da 
                WHERE inc_da.added_insalary=0 and FIND_IN_SET(inc_da.month_name, "'.$months.'")and (inc_da.created_at BETWEEN "'.$datef_s_e["from"].'-01" AND "'.$datef_s_e["to"].'-31"))as inc_da_t_d'),

            DB::raw('(IFNULL(payroll.basic_salary + payroll.dearness_allowance ,"0") )as total_sal_a'),

            DB::raw('IFNULL(payroll.bonus,"0") as bonus'),'employee__profile.doj',

            DB::raw('MONTHNAME(employee__profile.doj) as join_month'),

            DB::raw('TIMESTAMPDIFF(MONTH,employee__profile.doj, CURDATE()) as diff'),

            DB::raw('CASE WHEN (employee__profile.doj < "'.$datef_s_e["from"].'-01") then TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$curr_date.'")+1 ELSE TIMESTAMPDIFF(MONTH,employee__profile.doj, "'.$curr_date.'")+1  End as checkdiff'),

            DB::raw('(select GROUP_CONCAT(Concat(ida.month_name,":",ida.amount_inc)) FROM payroll__da ida where ida.sal_cat = salary_type and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31")) as payroll_month'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$datef_s_e["from"].'-01") then 
                    CASE When ("'.$curr_date.'" >= "'.$datef_s_e["from"].'-01") and ("'.$curr_date.'" <= "'.$dt.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$datef_s_e["from"].'-01", "'.$curr_date.'")+1
                    ELSE
                    "6"
                    END
                 ELSE
                  CASE When ("'.$curr_date.'" >= "'.$datef_s_e["from"].'-01") and ("'.$curr_date.'" <= "'.$dt.'-09-31") THEN
                    TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$curr_date.'")+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$dt.'-09-31")+1
                    END
                 END as apr_sep'),

            DB::raw('CASE WHEN (`employee__profile`.`doj` < "'.$dt.'-10-01") then 
                    CASE When ("'.$curr_date.'" >= "'.$dt.'-10-01") and ("'.$curr_date.'" <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,"'.$dt.'-10-01", "'.$curr_date.'")+1
                    ELSE
                    "0"
                    END
                WHEN (`employee__profile`.`doj` >= "'.$dt.'-10-01") THEN
                  CASE When ("'.$curr_date.'" >= "'.$dt.'-10-01") and ("'.$curr_date.'" <= "'.$datef_s_e["to"].'-31") THEN
                    TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$curr_date.'")+1
                    ELSE
                        TIMESTAMPDIFF(MONTH,`employee__profile`.`doj`, "'.$datef_s_e["to"].'-31")+1
                    END
                ElSE
                     0
                 END as oct_mar'),
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = employee__profile.id and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September")) as tot_sal_pre_apr'),
                 DB::raw('(select IFNULL(group_concat(DISTINCT(ida.basic_salary+ida.dearness_allowance)),0) FROM payroll__salary ida where ida.emp_id = employee__profile.id and ida.salary_type="SalaryA" and (ida.month BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March")) as tot_sal_pre_oct'),
          

            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "April,May,June,July,August,September"))as payroll_apr'),

            DB::raw('(select IFNULL(SUM(ida.amount_inc),0) FROM payroll__da ida where ida.sal_cat = salary_type and ida.added_insalary=0 and (ida.created_at BETWEEN "'.$datef_s_e["from"].'-01" and "'.$datef_s_e["to"].'-31") and FIND_IN_SET(ida.month_name, "October,November,December,January,February,March"))as payroll_oct'),
            DB::raw('IFNULL((SELECT SUM(payroll__advance.advance_amount)-Sum(payroll__advance.advance_paid) from payroll__advance WHERE payroll__advance.emp_id =payroll.emp_id),0) as advance')
            )->groupBy('employee__profile.id');
        // ->get();
        // print_r(DB::getqueryLog());die();

        // if(!empty($datef_s_e)){
        //     $bonus = $bonus->where(function($query) use ($datef_s_e){
        //                 $query->whereRaw('(payroll.created_at BETWEEN "'.$datef_s_e["from"].'-01" And "'.$datef_s_e["to"].'-31")');
        //             });
        // }
      
        if(!empty($serach_value))
        {
            $bonus = $bonus->where(function($query) use ($serach_value){
                        $query->where('employee__profile.name','LIKE',"%".$serach_value."%")
                        ->orwhere('employee_number','LIKE',"%".$serach_value."%")
                        
                        ;
                    });
                        
        }

        $count = count($bonus->get());
        $bonus = $bonus->offset($offset)->limit($limit);

        if(isset($request->input('order')[0]['column'])){
            $data = ['payroll.emp_id',
            'employee_number',
            'name',
            'bonus',
            'checkdiff',
            'apr_sep',
            'oct_mar',
            'payroll_apr'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $bonus->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
        {
            $bonus->orderBy('payroll.emp_id','desc');
        }
        $bonusdata = $bonus->get();
        
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count ;
        $array['data'] = $bonusdata; 
        return json_encode($array);
    }
    public function advance_approval(Request $request){
        $error=[];
        if(empty($request->input('amt'))){
            $error = array_merge($error,array('Amount is Required'));
        }
        if(count($error)>0){
            $data = [
            'error'=>$error];
            return response()->json($data);
        }
        $timestamp = date('Y-m-d G:i:s');
        $deduct = Advance::where('id',$request->input('id'))->update([
                'advance_amount'=>$request->input('amt'),
                'approved_by'=>Auth::id(),
                'updated_at'=>$timestamp
        ]);

        if($deduct != null){
            $msg =  ["Successfully Approved Amount ". $request->input('amt')]; 
        }else{
            $error = array_merge($error,array('Some Unexpected Error occurred.'));
        }
        $data = ['success'=>$msg,
                'error'=>$error];
        return response()->json($data);
    }
    public function advance_paid_summary(Request $request){

        $list = PaidAdvance::where("advance_id",$request->input('id'))->select('amount_paid','paid_category',DB::raw('Date_Format(created_at,"%d-%m-%Y") as created_date'))->get()->toArray();

        return json_encode($list);
    }
    public function emp_attendance_summary(){
        $employee = EmployeeProfile::where('is_active',1)->get();
        $data = array('employee'=>$employee,'layout' => 'layouts.main');
        return view('employee.Attendance.emp_attendance_list',$data);
    }
    public function emp_attendance_summary_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;

        $emp_name = $request->input('emp_name');
        $month = $request->input('month');
        $year = $request->input('year');
        $a_date = date('Y-m-d');
        
        if ($month) {
            $month_year = date('Y-'.$month);
        }else if ($month && $year) {
            $month_year = date($year.'-'.$month);
        }else{
            $month_year = date('Y-m');
        }

        if ($month) {
            $month = $month;
        }else{
            $month = date('m');
        }

        if ($year) {
            $year = $year;
        }else{
            $year = date('Y');
        }
        $leavess=Array();
        $date = new DateTime($a_date);
        $date->modify('last day of this month');
        $last_day = $date->format('d'); 
        $sort_data_query = array();

        for ($j = 1; $j <= $last_day ; $j++) {
            $md = 'd'.$j;
            $mdate = '"'.$month_year.'-'.$j.'"';
            $query[$j] = "IFNULL((SELECT CONCAT(att.status,',',att.id) FROM payroll__attendance att WHERE att.emp_id = payroll__attendance.emp_id AND att.date = ".$mdate."),'') as d".$j." ";
             $leave = HR_LeaveDetails::where('emp_id',69)
                ->where('date','=',$mdate)
                ->where('is_adjusted','=','1')
                ->select('date as d'.$j.'')
                ->get()->first();
            $leavess[$md]=$leave[$md];
        }
        $leavess = array_filter($leavess); 
        $arr_leaves=Array();
            foreach($leavess as $key=>$value){
                $arr_leaves[$key]="L";
            }
        $query = join(",",$query);

        $holiday=Holiday::whereMonth('holiday.start_date', '<=', $month)
        ->whereMonth('holiday.end_date', '>=', $month)
        ->whereYear('holiday.start_date', '<=', $year)
        ->whereYear('holiday.end_date', '>=', $year)
        ->select('name','start_date','end_date')
        ->get();

        $arr=Array();
        foreach($holiday as $key){
            $diff = strtotime($key['end_date']) - strtotime($key['start_date']);
            $diff=abs(round($diff / 86400)) + 1;
            
            $date=date('Y-m-d', strtotime('-1 day', strtotime($key['start_date']))); 
           
            for($i=0;$i<$diff;$i++){
                $date=date('Y-m-d', strtotime('+1 day', strtotime($date)));
                $get_mon=date('m', strtotime($date));
                $Mon=date('M', strtotime($date));
                $get_day = date('d', strtotime($date));
                if ($get_day < 10) {
                    $get_day = ltrim($get_day, '0');
                }else{
                   $get_day = $get_day; 
                }
                $x = 'd'.$get_day;
                
                if($get_mon==$month){
                    $arr[$x]=$key['name'];
                }
                
               
            }
            
        }


        $date = Carbon::now();
        if ($month) {
            $month_name = $date->format($month);
        }else{
            $month_name = date('m');  
        }
        $listing = Attendance::leftJoin('employee__profile','payroll__attendance.emp_id','employee__profile.id')
            ->leftJoin('department','payroll__attendance.department_id','department.id')
            ->whereMonth('payroll__attendance.date', date($month_name))
            ->select(
                'employee__profile.name',
                'employee__profile.id',
                'employee__profile.employee_number',
                'payroll__attendance.status',
                DB::raw('DATE_FORMAT(payroll__attendance.date,"%Y-%m") as date'),
                'department.department',
                DB::raw($query)
            )->groupBy('payroll__attendance.emp_id');

        if(!empty($serach_value)) {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('department.department','like',"%".$serach_value."%")
                ->orwhere('payroll__attendance.status','like',"%".$serach_value."%")
                ;
            });
        }
        
        if(isset($emp_name)) {
            $listing->where(function($query) use ($emp_name){
                $query->where('payroll__attendance.emp_id',$emp_name);
            });               
        }
        if(isset($year) ) {
            $listing->where(function($query) use ($year){
                $query->whereYear('payroll__attendance.date', date($year));
            });
        }
        if(isset($month) ) {
            $listing->where(function($query) use ($month){
                $query->whereMonth('payroll__attendance.date', date($month));
            });
        }
        if(isset($request->input('order')[0]['column'])) {

            $data = [
                'employee__profile.emp_id',
                'employee__profile.name',
                'payroll__attendance.status',
                'payroll__attendance.date',
                'department.department'
            ];

            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else{
            $listing->orderBy('payroll__attendance.id','desc');
        }
        $count = count( $listing->get()->toArray());
        $listing = $listing->offset($offset)->limit($limit)->get()->toArray(); 
        $jj = $listing;
            if(count($arr_leaves)!=0){
                $arr=$arr_leaves+$arr;
            }
       
            foreach($listing as $key=>$value){

                $jj[$key]=$arr+$jj[$key];
            }

            $listing=$jj;
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

    public function emp_attendance_create_summary(){
        $employee = EmployeeProfile::where('is_active',1)->get();
        $data = array('employee'=>$employee,'layout' => 'layouts.main');
        return view('employee.Attendance.create_attendance',$data);
    }
    public function emp_attendance_create_summary_api(Request $request){
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;

       
        $year = date('Y-m-d',strtotime($request->input('year')));
        
       
       
        $listing = EmployeeProfile::leftJoin('payroll__attendance',function($join) use ($year){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->where('payroll__attendance.date','=',$year);
        })
        ->where('payroll__attendance.date','=',NULL)
            ->leftJoin('department','employee__profile.department_id','department.id')
            ->select(
                'employee__profile.name',
                'employee__profile.id',
                'employee__profile.employee_number',
                'department.department'
            )->groupBy('employee__profile.id');

      
        if(!empty($serach_value))
        {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('department.department','like',"%".$serach_value."%")
                ->orwhere('payroll__attendance.status','like',"%".$serach_value."%")
                ;
            });
        }
        if(isset($request->input('order')[0]['column']))
        {
            $data = [
                'employee__profile.id',
                'employee__profile.name',
                'department.department'
            ];
            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $listing->orderBy('employee__profile.id','desc');      
  
    $count = count($listing->get()->toArray());
    $listing = $listing->offset($offset)->limit($limit)->get()->toArray();
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

    public function get_emp_attendance($id){
        $lunch_to='';
        $lunch_from='';
        $other_time_deduction='';
        $data = Attendance::where('id',$id)->get()->first();
        $in_time =  date("H : i A", strtotime($data['in_time']));
        $out_time =  date("H : i A", strtotime($data['out_time']));
        if($data['lunch_from']!="00:00:00"){
            $lunch_from =  date("H : i A", strtotime($data['lunch_from']));
        }
        if($data['lunch_to']!="00:00:00"){
            $lunch_to =  date("H : i A", strtotime($data['lunch_to']));
        }
        if($data['other_time_deduction']!="00:00:00"){
            $other_time_deduction =  date("H : i A", strtotime($data['other_time_deduction']));
        }
        
       $res = array(
            'id' => $data['id'],
            'in_time' => $in_time,
            'out_time' => $out_time,
            'lunch_to' => $lunch_to,
            'lunch_from' => $lunch_from,
            'data' => $data,
            'deduction' => $other_time_deduction,
            'half_day' => $data['half_day'],
            'status' => $data['status']
        );
         return response()->json($res);
        // return $data;
    }

    public function emp_attendance_update(Request $request)  {
        try {
            $id = $request->input('id');
            $status = $request->input('status');
            $half_day = $request->input('half_day');
            $in_time = CustomHelpers::ConvertTime($request->input('in_time'));
            $out_time = CustomHelpers::ConvertTime($request->input('out_time'));
            $lunch_in_time = CustomHelpers::ConvertTime($request->input('lunch_in_time'));
            $lunch_out_time = CustomHelpers::ConvertTime($request->input('lunch_out_time'));
            $deduction = CustomHelpers::ConvertTime($request->input('deduction'));
            if (empty($id) || empty($in_time) || empty($out_time) || empty($status) || empty($lunch_in_time) || empty($lunch_out_time) || empty($deduction) || empty($half_day)) {

                //  return array('type' => 'error', 'msg' => 'Please fill the blank field.');
                 return redirect('/hr/attendance/summary')->with('error','Please fill the blank field.');
            }
            $update = Attendance::where('id',$id)->update([
                'in_time'=>$in_time,
                'out_time'=>$out_time,
                'lunch_from'=>$lunch_in_time,
                'lunch_to'=>$lunch_out_time,
                'other_time_deduction'=>$deduction,
                'half_day'=>$half_day,
                'status' => $status
            ]);
            if($update==NULL){
                //  return array('type' => 'error', 'msg' => 'Something went wrong.');
                 return redirect('/hr/attendance/summary')->with('error','Something went wrong.');
            }
            else{  
                // return array('type' => 'success', 'msg' => 'Attendance updated Successfully.');  
                return redirect('/hr/attendance/summary')->with('success','Attendance updated Successfully..');
   
            }

        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/hr/attendance/summary')->with('error','Something went wrong.');
        }
    }

    public function emp_attendance_create(Request $request)  {
        try {
            $id = $request->input('id');
            $dept=EmployeeProfile::where('id',$id)->select('department_id')->get()->first();
            $status = $request->input('status');
            if($status=="P"){
                $half_day = $request->input('half_day');
                $in_time = CustomHelpers::ConvertTime($request->input('in_time'));
                $out_time = CustomHelpers::ConvertTime($request->input('out_time'));
                $lunch_in_time = CustomHelpers::ConvertTime($request->input('lunch_in_time'));
                $lunch_out_time = CustomHelpers::ConvertTime($request->input('lunch_out_time'));
                $deduction = CustomHelpers::ConvertTime($request->input('deduction'));
    
                $duration = CustomHelpers::ConvertTime($request->input('duration'));
                $late_by = CustomHelpers::ConvertTime($request->input('late_by'));
                $early_by = CustomHelpers::ConvertTime($request->input('early_by'));
                $ot = CustomHelpers::ConvertTime($request->input('ot'));
                $shift = $request->input('shift');
                
                if (empty($id) || empty($shift) || empty($in_time) || empty($out_time) || empty($status) || empty($lunch_in_time) || empty($lunch_out_time) || empty($deduction) || empty($half_day)) {
    
                     return redirect('/hr/attendance/create/summary')->with('error','Some Unexpected Error occurred.');  
                }
                $update = Attendance::insertGetId([
                    'emp_id'=>$id,
                    'department_id'=>$dept['department_id'],
                    'date'=>date('Y-m-d',strtotime($request->input('date'))),
                    'in_time'=>$in_time,
                    'out_time'=>$out_time,
                    'lunch_from'=>$lunch_in_time,
                    'lunch_to'=>$lunch_out_time,
                    'other_time_deduction'=>$deduction,
                    'half_day'=>$half_day,
                    'status' => $status,
                    'duration'=>$duration,
                    'late_by'=>$late_by,
                    'early_by'=>$early_by,
                    'ot'=>$ot,
                    'shift'=>$shift
                ]);
            }
            else{
                $update = Attendance::insertGetId([
                    'emp_id'=>$id,
                    'department_id'=>$dept['department_id'],
                    'date'=>date('Y-m-d',strtotime($request->input('date'))),
                    'in_time'=>"00:00:00",
                    'out_time'=>"00:00:00",
                    'lunch_from'=>"00:00:00",
                    'lunch_to'=>"00:00:00",
                    'other_time_deduction'=>"00:00:00",
                    'half_day'=>"No",
                    'status' => $status,
                    'duration'=>"00:00:00",
                    'late_by'=>"00:00:00",
                    'early_by'=>"00:00:00",
                    'ot'=>"00:00:00",
                    'shift'=>NULL
                ]);
            }
           
            if($update==NULL){
                return redirect('/hr/attendance/create/summary')->with('error','Some Unexpected Error occurred.');  
            }
            else{  
                return redirect('/hr/attendance/create/summary')->with('success','Attendance updated Successfully.');  
                   
            }

        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect('/hr/attendance/create/summary')->with('success','Attendance updated Successfully.');  
        }
    }

    public function emp_attendance_overtime_summary() {
        $employee = EmployeeProfile::where('is_active',1)->get();
        $data = array('employee'=>$employee,'layout' => 'layouts.main');
        return view('employee.Attendance.emp_attendance_overtime_list',$data);
    }

    public function emp_attendance_overtime_summary_api(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $listing = Attendance::leftJoin('employee__profile','payroll__attendance.emp_id','employee__profile.id')
            ->leftJoin('department','payroll__attendance.department_id','department.id')
            ->where('payroll__attendance.status','P')
            ->where('employee__profile.is_OT','Yes')
            ->where(DB::raw("TIMESTAMPDIFF(hour,payroll__attendance.in_time,payroll__attendance.out_time)") ,'>','0')
            ->where(DB::raw("TIMESTAMPDIFF(hour,employee__profile.shift_from,employee__profile.shift_to)") ,'<',DB::raw("TIMESTAMPDIFF(hour,payroll__attendance.in_time,payroll__attendance.out_time)"))
            ->select(
                'employee__profile.name',
                'payroll__attendance.emp_id',
                'employee__profile.employee_number',
                'payroll__attendance.status',
                'payroll__attendance.in_time',
                'payroll__attendance.out_time',
                'employee__profile.shift_from',
                'employee__profile.shift_to',
                DB::raw("TIMESTAMPDIFF(hour,employee__profile.shift_from,employee__profile.shift_to) as time"),
                DB::raw("TIMESTAMPDIFF(hour,payroll__attendance.in_time,payroll__attendance.out_time) as duration"),
                'payroll__attendance.date',
                'department.department'
            );

        if(!empty($serach_value)) {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('employee__profile.shifting_timing','like',"%".$serach_value."%")
                ->orwhere('department.department','like',"%".$serach_value."%")
                ->orwhere('payroll__attendance.status','like',"%".$serach_value."%")
                ->orwhere('payroll__attendance.date','like',"%".$serach_value."%")
                ->orwhere('payroll__attendance.in_time','like',"%".$serach_value."%")
                ->orwhere('payroll__attendance.out_time','like',"%".$serach_value."%")
                ;
            });
        }
        if(isset($request->input('order')[0]['column'])) {

            $data = [
                'payroll__attendance.emp_id',
                'employee__profile.name',
                'payroll__attendance.date',
                'payroll__attendance.in_time',
                'payroll__attendance.out_time',
                'employee__profile.shifting_timing',
                'payroll__attendance.status',
                'duration',
                'overtime'
            ];

            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else
            $listing->orderBy('payroll__attendance.id','desc');
       
        $count = count( $listing->get()->toArray());
        $listing = $listing->offset($offset)->limit($limit)->get()->toArray();
        $i = -1;
         foreach ($listing as $value) {
            $i++;
            $time = $value['time']; 
            $str = $value['duration'];
            $duration = ltrim($str, '0'); 
            $overtime = $str - $time;
               $listing[$i] = array_replace($value,[
                    'duration' => $duration,
                ]);
                $listing[$i] = array_merge($value,[
                    'overtime' => $overtime
                ]);                 
          } 
        
        $listing = array_values($listing);
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

    public function emp_attendance_late_summary() {
        $employee = EmployeeProfile::where('is_active',1)->get();
        $data = array('employee'=>$employee,'layout' => 'layouts.main');
        return view('employee.Attendance.emp_attendance_late_list',$data);
    }

    public function emp_attendance_late_summary_api(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $listing = Attendance::leftJoin('employee__profile','payroll__attendance.emp_id','employee__profile.id')
            ->leftJoin('department','payroll__attendance.department_id','department.id')
            ->where('payroll__attendance.status','P')
            ->where(DB::raw("TIMESTAMPDIFF(hour,payroll__attendance.in_time,payroll__attendance.out_time)") ,'>','0')
            ->where(DB::raw("TIMESTAMPDIFF(hour,employee__profile.shift_from,employee__profile.shift_to)") ,'>',DB::raw("TIMESTAMPDIFF(hour,payroll__attendance.in_time,payroll__attendance.out_time)"))
            ->select(
                'employee__profile.name',
                'employee__profile.id',
                'payroll__attendance.emp_id',
                'employee__profile.employee_number',
                'payroll__attendance.status',
                'payroll__attendance.in_time',
                'payroll__attendance.out_time',
                'employee__profile.shift_from',
                'employee__profile.shift_to',
                DB::raw("TIMESTAMPDIFF(hour,payroll__attendance.in_time,payroll__attendance.out_time) as duration"),
                DB::raw("TIMESTAMPDIFF(hour,employee__profile.shift_from,employee__profile.shift_to) as time"),
                DB::raw('DATE_FORMAT(payroll__attendance.date,"%Y-%m-%d") as date'),
                'department.department'
            );

        if(!empty($serach_value)) {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('department.department','like',"%".$serach_value."%")
                ->orwhere('payroll__attendance.status','like',"%".$serach_value."%")
                ;
            });
        }
        
       
        if(isset($request->input('order')[0]['column'])) {

            $data = [
                'employee__profile.employee_number',
                'employee__profile.name',
                'payroll__attendance.status',
                'payroll__attendance.date',
                'department.department',
                'payroll__attendance.in_time',
                'payroll__attendance.out_time',
                'employee__profile.shift_from',
                'employee__profile.shift_to'
            ];

            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else{
            $listing->orderBy('payroll__attendance.id','desc');
        }
        $count = count( $listing->get()->toArray());
        $listing = $listing->offset($offset)->limit($limit)->get()->toArray();
         
        $i = -1;
         foreach ($listing as $value) {
            $i++;
            $time = $value['time']; 
            $str = $value['duration'];
            $duration = ltrim($str, '0'); 
            $late =  $time - $str;

            $listing[$i] = array_replace($value,[
                'duration' => $duration,
            ]);

            $listing[$i] = array_merge($value,[
                'late' => $late
            ]);  
          }
        $listing = array_values($listing);
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

    public function SalaryListA() {
        $employee = EmployeeProfile::where('is_active',1)->get();
        $data = array(
            'employee' => $employee,
            'layout' => 'layouts.main'
        );
        return view('employee.Remuneration.emp_salary_list_a',$data);
    }

    public function SalaryListAapi(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $emp_name = $request->input('emp_name');
        $month = $request->input('month');
        $yearstr = $request->input('year');
        $getyear = substr($yearstr, -4);
        if ($getyear) {
            $year = $getyear;
        }else{
            $year = date('Y');
        }
        $date = Carbon::now();
        if ($month) {
             $month_name = date('m',strtotime($month));
        }else{
            $month_name = date('m');  
        }
        $listing = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
            ->leftJoin('payroll_payment',function($join){
                            $join->on('payroll_payment.payroll_salary_id','payroll__salary.id');
                        })
            ->whereMonth('payroll__salary.month', date($month_name))
            ->whereYear('payroll__salary.month', date($year))
            ->where('payroll__salary.salary_type','SalaryA')
            ->select(
                'employee__profile.name',
                'employee__profile.id as emp_id',
                'employee__profile.employee_number',
                'employee__bank.bank_status',
                'employee__bank.acc_name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary',
                'payroll__salary.id',
                DB::raw('SUM(payroll_payment.amount) as paid')
            )->groupBy('payroll__salary.id');

        if(!empty($serach_value)) {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('employee__bank.acc_number','like',"%".$serach_value."%")
                ->orwhere('employee__bank.acc_ifsc','like',"%".$serach_value."%")
                ->orwhere('payroll__salary.net_salary','like',"%".$serach_value."%")
                ;
            });
        }
        
        if(isset($emp_name)) {
            $listing->where(function($query) use ($emp_name){
                $query->where('payroll__salary.emp_id',$emp_name);
            });               
        }
        if(isset($month_name) ) {
            $listing->where(function($query) use ($month_name){
                $query->whereMonth('payroll__salary.month', date($month_name));
            });
        }
         if(isset($year) ) {
            $listing->where(function($query) use ($year){
                $query->whereYear('payroll__salary.month', date($year));
            });
        }
        if(isset($request->input('order')[0]['column'])) {

            $data = [
                'employee__profile.name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary',
                'payroll_payment.amount',
                'payroll_payment.amount'
            ];

            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else{
            $listing->orderBy('payroll__salary.emp_id','desc');
        }
        $count = count( $listing->get()->toArray());
        $listing = $listing->offset($offset)->limit($limit)->get()->toArray(); 
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

     public function SalryListB() {
        $employee = EmployeeProfile::where('is_active',1)->get();
        $data = array(
            'employee' => $employee,
            'layout' => 'layouts.main'
        );
        return view('employee.Remuneration.emp_salary_list_b',$data);
    }

    public function SalryListBapi(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $emp_name = $request->input('emp_name');
        $month = $request->input('month');
        $yearstr = $request->input('year');
        $getyear = substr($yearstr, -4);
        if ($getyear) {
            $year = $getyear;
        }else{
            $year = date('Y');
        }
        $date = Carbon::now();
        if ($month) {
             $month_name = date('m',strtotime($month));
        }else{
            $month_name = date('m');  
        }
        $listing = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
            ->leftJoin('payroll_payment',function($join){
                            $join->on('payroll_payment.payroll_salary_id','payroll__salary.id');
                        })
            ->whereMonth('payroll__salary.month', date($month_name))
            ->whereYear('payroll__salary.month', date($year))
            ->where('payroll__salary.salary_type','SalaryB')
            ->select(
                'employee__profile.name',
                'employee__profile.id as emp_id',
                'employee__profile.employee_number',
                'employee__bank.bank_status',
                'employee__bank.acc_name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary',
                'payroll__salary.id',
                DB::raw('SUM(payroll_payment.amount) as paid')
            )->groupBy('payroll__salary.id');

        if(!empty($serach_value)) {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('employee__bank.acc_number','like',"%".$serach_value."%")
                ->orwhere('employee__bank.acc_ifsc','like',"%".$serach_value."%")
                ->orwhere('payroll__salary.net_salary','like',"%".$serach_value."%")
                ->orwhere('payroll_payment.amount' ,'like',"%".$serach_value."%")
                ;
            });
        }
        
        if(isset($emp_name)) {
            $listing->where(function($query) use ($emp_name){
                $query->where('payroll__salary.emp_id',$emp_name);
            });               
        }
         if(isset($month_name) ) {
            $listing->where(function($query) use ($month_name){
                $query->whereMonth('payroll__salary.month', date($month_name));
            });
        }

         if(isset($year) ) {
            $listing->where(function($query) use ($year){
                $query->whereYear('payroll__salary.month', date($year));
            });
        }
        if(isset($request->input('order')[0]['column'])) {

            $data = [
                'employee__profile.name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary',
                'payroll_payment.amount',
                'payroll_payment.amount'
            ];

            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else{
            $listing->orderBy('payroll__salary.emp_id','desc');
        }
        $count = count( $listing->get()->toArray());
        $listing = $listing->offset($offset)->limit($limit)->get()->toArray(); 
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

     public function SalryListC() {
        $employee = EmployeeProfile::where('is_active',1)->get();
        $data = array(
            'employee' => $employee,
            'layout' => 'layouts.main'
        );
        return view('employee.Remuneration.emp_salary_list_c',$data);
    }

    public function SalryListCapi(Request $request) {
        $search = $request->input('search');
        $serach_value = $search['value'];
        $start = $request->input('start');
        $limit = $request->input('length');
        $offset = empty($start) ? 0 : $start ;
        $limit =  empty($limit) ? 10 : $limit ;
        $emp_name = $request->input('emp_name');
        $month = $request->input('month');
        $yearstr = $request->input('year');
        $getyear = substr($yearstr, -4);
        if ($getyear) {
            $year = $getyear;
        }else{
            $year = date('Y');
        }
        $date = Carbon::now();
        if ($month) {
             $month_name = date('m',strtotime($month));
        }else{
            $month_name = date('m');  
        }
        $listing = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
            ->leftJoin('payroll_payment',function($join){
                            $join->on('payroll_payment.payroll_salary_id','payroll__salary.id');
                        })
            ->whereMonth('payroll__salary.month', date($month_name))
            ->whereYear('payroll__salary.month', date($year))
            ->where('payroll__salary.salary_type','SalaryC')
            ->select(
                'employee__profile.name',
                'employee__profile.id as emp_id',
                'employee__profile.employee_number',
                'employee__bank.bank_status',
                'employee__bank.acc_name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary',
                'payroll__salary.id',
                DB::raw('SUM(payroll_payment.amount) as paid')
            )->groupBy('payroll__salary.id');

        if(!empty($serach_value)) {
            $listing->where(function($query) use ($serach_value){
                $query->where('employee__profile.name','like',"%".$serach_value."%")
                ->orwhere('employee__bank.acc_number','like',"%".$serach_value."%")
                ->orwhere('employee__bank.acc_ifsc','like',"%".$serach_value."%")
                ->orwhere('payroll__salary.net_salary','like',"%".$serach_value."%")
                ;
            });
        }
        
        if(isset($emp_name)) {
            $listing->where(function($query) use ($emp_name){
                $query->where('payroll__salary.emp_id',$emp_name);
            });               
        }
        if(isset($month_name) ) {
            $listing->where(function($query) use ($month_name){
                $query->whereMonth('payroll__salary.month', date($month_name));
            });
        }

         if(isset($year) ) {
            $listing->where(function($query) use ($year){
                $query->whereYear('payroll__salary.month', date($year));
            });
        }
        if(isset($request->input('order')[0]['column'])) {

            $data = [
                'employee__profile.name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary',
                'payroll_payment.amount',
                'payroll_payment.amount'
            ];

            $by = ($request->input('order')[0]['dir'] == 'desc')? 'desc': 'asc';
            $listing->orderBy($data[$request->input('order')[0]['column']], $by);
        }
        else{
            $listing->orderBy('payroll__salary.emp_id','desc');
        }
        $count = count( $listing->get()->toArray());
        $listing = $listing->offset($offset)->limit($limit)->get()->toArray(); 
        $array['recordsTotal'] = $count;
        $array['recordsFiltered'] = $count;
        $array['data'] = $listing; 
        return json_encode($array);
    }

      public function salary_register_print($id,$yr){
        $sort_data_query = array();
        $emp =  $id;
        $yr="01-".$yr;
        $days=date('t',strtotime($yr));
        $month_name=date('M',strtotime($yr));
        $mon=date('m',strtotime($yr));
        $year=date('Y',strtotime($yr));
        $leavess=Array();
        for ($j = 1; $j <= $days ; $j++) {
            // $emp_id = $user[$j]['party_id'];
            if($j<10){$md="0".$j."_".$month_name;}  
            else{$md=$j."_".$month_name;}
            $date=$j."-".$mon."-".$year;
            $date=date('Y-m-d',strtotime($date));
            $query[$j] = "IFNULL((SELECT att.status FROM payroll__attendance att WHERE  att.emp_id = payroll__attendance.emp_id AND YEAR(att.date)=".$year.".  AND att.date = '".$date."' ),'') as ".$md." ";
            $leave=HR_LeaveDetails::where('emp_id',$emp)
            ->where('date','=',$date)
            ->where('is_adjusted','=','1')
            ->select('date as '.$md.'')
            ->get()->first();
            $leavess[$md]=$leave[$md];

        }
        $leavess = array_filter($leavess); 
        $arr_leaves=Array();
        foreach($leavess as $key=>$value){
            $arr_leaves[$key]="L";
        }
        
        $query = join(",",$query);
        $holiday=Holiday::whereMonth('holiday.start_date', '<=', $mon)
        ->whereMonth('holiday.end_date', '>=', $mon)
        ->whereYear('holiday.start_date', '<=', $year)
        ->whereYear('holiday.end_date', '>=', $year)
        ->select('name','start_date','end_date')
        ->get();
        $arr=Array();
        foreach($holiday as $key){
            $diff = strtotime($key['end_date']) - strtotime($key['start_date']);
            $diff=abs(round($diff / 86400)) + 1;
            
            $date=date('Y-m-d', strtotime('-1 day', strtotime($key['start_date']))); 
           
            for($i=0;$i<$diff;$i++){
                $date=date('Y-m-d', strtotime('+1 day', strtotime($date)));
                $get_mon=date('m', strtotime($date));
                $Mon=date('M', strtotime($date));
                $get_day=date('d', strtotime($date));
                
                $x=$get_day."_".$Mon;
                if($get_mon==$mon){
                    $arr[$x]=$key['name'];
                }
                
               
            }
            
        }
        $jobdata=EmployeeProfile::leftJoin('payroll__attendance', function($join) use ($year,$mon){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date','=',$year);
            $join->WhereMonth('payroll__attendance.date','=',$mon);
       })
       ->leftJoin('department','department.id','employee__profile.department_id')
        ->leftJoin('payroll__advance', function($join) use ($year,$mon){
            $join->on('payroll__advance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__advance.given_date','=',$year);
            $join->WhereMonth('payroll__advance.given_date','=',$mon);
            
        })
        ->leftJoin('payroll__salary', function($join) use ($year,$mon){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__salary.month','=',$year);
            $join->WhereMonth('payroll__salary.month','=',$mon);
            $join->Where('payroll__salary.salary_type','=',"SalaryC");
            
        })
        ->leftJoin('payroll_salary_detail as pf_ded', function($join) use ($year,$mon){
            $join->on('pf_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('pf_ded.name','=',"PF");
            
        })
        ->leftJoin('payroll_salary_detail as esi_ded', function($join) use ($year,$mon){
            $join->on('esi_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('esi_ded.name','=',"ESI");
            
        })
        ->leftJoin('payroll_salary_detail as adv_ded', function($join) use ($year,$mon){
            $join->on('adv_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('adv_ded.name','=',"Advance");
            
        })
        ->leftJoin('payroll__paid_advance', function($join) use ($year,$mon){
            $join->on('payroll__paid_advance.advance_id','=','payroll__advance.id');
        })
        ->where('employee__profile.id',$emp)
        ->select(
            'employee__profile.id',
            'employee__profile.name as emp_name',
            'employee__profile.employee_number',
            'employee__profile.designation',
        'department.department',
        'father_name','local_address',
            DB::raw('IFNULL(payroll__salary.net_salary,"0") as total_salaryC'),

            DB::raw('IFNULL(pf_ded.amount,"0") as pf_ded'),
            DB::raw('IFNULL(esi_ded.amount,"0") as esi_ded'),
            DB::raw('IFNULL(adv_ded.amount,"0") as adv_ded'),

            DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$year.'
                AND MONTH(m.date)='.$mon.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_absent_current'),
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    AND m.status<>"A"
                    AND YEAR(m.date)='.$year.'
                    AND MONTH(m.date)='.$mon.'
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_present_current'),
               
                            DB::raw('(IFNULL(
                                (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                WHERE employee__profile.id=m.emp_id
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as opening_advance'),
                                DB::raw('(IFNULL(
                                    (SELECT sum(m.advance_paid) FROM payroll__advance m 
                                    WHERE employee__profile.id=m.emp_id
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as balance_advance'),

                                    DB::raw($query)
        )->GroupBy('employee__profile.id');
        $jobdata=$jobdata->get()->toArray();
        $jj=$jobdata;
        if(count($arr_leaves)!=0){
            $arr=$arr_leaves+$arr;
        }
   
        foreach($jobdata as $key=>$value){

            $jj[$key]=$arr+$jj[$key];
        }

        $jobdata=$jj;
        if($jobdata != null){
            $data = [
                'jobdata' => $jobdata,
                'year' => $yr,
                'month_name' => $month_name
                ];
            $pdfFilePath = "Salary Register.pdf";
            $pdf = PDF::loadView('hr.salary_reg_print', $data);
            return $pdf->stream($pdfFilePath);
            return view('hr.salary_reg_print',$data);
        }
        else{
            $message="No Salary Register Exist!!";
            return redirect('/hr/salary/register')->with('error',$message);
        }
    }

    public function GetPayDetails_Salary($id,$type) {
        $data = NetSalary::where('payroll__salary.id',$id)->where('payroll__salary.salary_type',$type)
        
        ->get()->first();
        $payment  = PayrollPayment::where('payroll_salary_id',$id)->sum('amount');
        if ($data == null) {
           $msg = 'error';
        }else{
            $msg = 'success';
        }
        $pp=PayrollPayment::where('payroll_salary_id',$id)->select('payment_mode','amount',DB::raw('DATE_FORMAT(payment_date,"%d %M %Y") as payment_date'))->get();
        $array = ['salary' => $data, 'payment' => $payment, 'msg' => $msg,'pp'=>$pp];
        return response()->json($array);
    }

    public function GetPayDetails($id) {
        $pp=PayrollPayment::where('payroll_salary_id',$id)->select('payment_mode','amount',DB::raw('DATE_FORMAT(payment_date,"%d %M %Y") as payment_date'),
        DB::raw('IFNULL(utr_no,"-") as utr_no'))->get();
        $data = array(
            'pp' => $pp,
            'layout' => 'layouts.main'
        );
        return view('employee.Remuneration.payment_details',$data);
    }
    public function Payment_Salary(Request $request) {
      try {
         DB::beginTransaction();
         $id = $request->input('id');
         $payment_mode = $request->input('payment_mode');
         $amount = $request->input('amount');
         $utr_no = $request->input('utr_no');
         $type = $request->input('type');
         if (empty($payment_mode)){
             return 'Payment mode field is required.';
         }
         if(empty($amount)) {
             return 'Amount field is required.';
         }
         if ($payment_mode == 'Cheque' && empty($utr_no)) {
               return 'UTR number is required.';
         }
         $data = NetSalary::where('id',$id)->where('salary_type',$type)->get()->first();
         if ($data == null) {
            return 'Something went wrong.';
        }
         $payment  = PayrollPayment::where('payroll_salary_id',$id)->sum('amount');
         if ($data['net_salary'] < $payment+$amount) {
             return 'Your amount is too more.';
         }elseif ($amount == 0) {
             return 'Please enter the correct amount.';
         }else{
            $add = PayrollPayment::insertGetId([
                'payroll_salary_id' => $id,
                'amount' => $amount,
                'utr_no' => $utr_no,
                'payment_mode' => $payment_mode,
                'payment_date' => date('Y-m-d')
            ]);
            if ($add == null) {
                DB::rollback();
                return 'Something went wrong.';
            }else{
                $update = NetSalary::where('id',$id)->update([
                    'is_credit' => 'Yes'
                ]);
                if ($update == null) {
                    DB::rollback();
                    return 'Something went wrong.';
                }else{
                    DB::commit();
                    return 'success';
                }
            }
         }

          
      } catch (Exception $e) {
            return 'Something went wronge.';
          
      }
    }
}

?>