@extends($layout)
 
@section('title', 'Salary C Calculation')
 
@section('user', Auth::user()->name)
 
@section('breadcrumb')
    <li><a href="#"><i class=""></i> Salary C Calculation</a></li>
   

@endsection
@section('css')

@endsection

@section('js')
    <script>
    
   $(document).ready(function() {
    var date = new Date();
    var year=date.getFullYear(); //get year
var month=date.getMonth(); //get month
    $('.date').datepicker({
        format: "mm-yyyy",
        autoclose: true,
        viewMode: "months", 
        minViewMode: "months",
        startDate: new Date(year, month-1, '01'), //set it here
        endDate: new Date(year, month, '01')
    }).datepicker("setDate", new Date());
     });  
   
    $('.advance').on('change',function(e){
        var emp=$('.employee').val();
        var mon=$('.date').val();
        if((mon=="") || (emp="")){
            e.preventDefault();
        }
            
        else{
            getData(1);
        }
        
    });
    $('.leave_adjusted').on('change',function(e){
        var emp=$('.employee').val();
        var mon=$('.date').val();
        if((mon=="") || (emp="")){
            e.preventDefault();
        }
            
        else{
            getData(1);
        }
       
    });
    $('.date').on('change',function(e){
            var emp=$('.employee').val();
            var mon=$('.date').val();
            if(mon=="")
                e.preventDefault();
            else 
                getData(0);
    });

    $('.employee').on('change',function(e){
        var emp=$('.employee').val();
        var mon=$('.date').val();
        if(mon=="")
            e.preventDefault();
        else 
            getData(0);
    });
    function getData(counter){
        var mon=$('.date').val();
        
        var emp=$('.employee').val();
        $('#ajax_loader_div').css('display','block');
        $.ajax({
        type:"GET",
        url:"/salaryC/calculation/api",
        data:{'emp':emp,'mon':mon},
        success: function(result){
            if (result) {

        var emp_name=result.employee.emp_name;
        var employee_number=result.employee.employee_number;
        var id=result.employee.id;
        var carried_leave=result.employee.carried_leave;
        var advance_in_month=result.employee.advance_in_month;
        var balance_advance=result.employee.balance_advance;
        var opening_advance=result.employee.opening_advance;

        var pf=result.employee.pf;
        var esi=result.employee.esi;

        $('.emp').val(id);
        $('.mon').val(mon);

        $('.emp_name').text(emp_name);
        $('.emp_code').text(employee_number);
        $('.adv_mon').text(advance_in_month);
       
               
       

            
        var total_days=result.dd;
        var total_a=result.employee.total_absent_current;
        var total_p=result.employee.total_present_current;
        var total_salaryC=result.employee.total_salaryC;
        var overtime_salaryA=result.employee.overA;
        var overtime_salaryB=result.employee.overB;
        var ot=result.employee.ot;
        var late_by=result.employee.late_by;
        var basic_salaryC=result.employee.basic_salaryC;
        var da_salaryC=result.employee.DA_C;

        var advance_left=result.employee.advance_to_be_deducted;
        var installment_left=result.employee.installment_left;
        var installment=result.employee.installment;
        var advance_to_install=result.employee.advance_to_install;
        var advance_after_install=advance_to_install/installment;   
        var leave_adjusted=result.employee.leave_adjusted;
        var leave=result.employee.leave_adjusted;
        if(advance_left<=advance_after_install){
            var ad=advance_left;
            // ad=ad.toFixed(2)
        }
        else{
            var ad=advance_after_install;
            // ad=ad.toFixed(2)
        }
        if(counter==1){
            var leave_adjusted=$('.leave_adjusted').val();
            var ad=$('.advance').val();
            if(parseFloat(ad)>parseFloat(balance_advance)){
                alert(ad+" "+balance_advance+'Advance Deducted is greater than Balance Advance. It must be less than or equal to balance advance');
                getData(0);
            }
            
            if(parseFloat(leave_adjusted)>parseFloat(leave)){
                alert('Leave Adjusted is greater than Actual Leave Adjust Balance. It must be less than or equal to Actual Leave Adjust Balance');
                $('#ajax_loader_div').css('display','none');
                getData(0);
            }
           
        }
        
        if (isNaN(ad)) ad = 0; 
       console.log("ad = "+ad);
       
        $('.advance').val(ad);
        $('.advance ').attr('placeholder',("Max:" + balance_advance));
        $('.advance ').attr('max',balance_advance);
      
        $('.days').text(total_days);
        $('.tot_p').text(total_p);
        $('.tot_a').text(total_a);
        $('.days').val(total_days);
        $('.tot_p').val(total_p);
        $('.tot_a').val(total_a);
        $('.tot_sal').text(total_salaryC);
        $('.leave_adjusted ').val(leave_adjusted);
        $('.leave_adjusted ').attr('placeholder',("Max:" + leave_adjusted));
        $('.leave_adjusted ').attr('max',leave_adjusted);
        $('.submit_div').empty();
       var flag=result.flag;
       var date = new Date();
        var year=date.getFullYear(); //get year
        var month=date.getMonth()+1; //get month

        mons=mon.split('-');
        console.log(mons[0]);
        console.log(month);
        
       if(flag==0 && mons[0]!=month){
           $('.submit_div').append('<input type="submit" class="btn btn-primary" style="float:right" value="Submit">');
       }

        var total_present_pre=result.employee.total_present_pre;
        var year_d=result.d_y;
        var total_p_y=total_present_pre;
            total_p_y=total_p_y/20;
            total_p_y=total_p_y+carried_leave;
            if (isNaN(total_p_y)) total_p_y = 0; 
        $('.open_leave').text(parseInt(total_p_y));

                            var mo_tot=0;
                            var total_leaves_till_now=result.employee.total_leaves_till_now;
                            var total_p_y=total_present_pre;
                                total_p_y=total_p_y/20;
                                total_p_y=total_p_y+carried_leave;
                                total_p_y=parseInt(total_p_y);
                                mo_tot=total_p_y-total_leaves_till_now;

         $('.ot').val(parseInt(ot));
        $('.ot').text(parseInt(ot));  
        $('.late').val(parseInt(late_by));
        $('.late').text(parseInt(late_by));                    
        var ot_late=parseInt(ot)-parseInt(late_by);
        if (isNaN(mo_tot)) mo_tot = 0;   
        if (isNaN(ot_late)) ot_late = 0;   
        
            
        $('.close_leave').text(mo_tot);
        $('.ot_late').text(ot_late);
        $('.open_adv').text(opening_advance);
        $('.bal_adv').text(balance_advance);
        //leave deduction 
        var leave_ded=(parseFloat(total_salaryC)/parseFloat(total_days))*parseFloat(leave_adjusted);
        $('.leave_ded').val(leave_ded.toFixed(2));
        $('.leave_ded').text(leave_ded.toFixed(2));

        //effective present
        var effective_present=parseFloat(total_p)+parseFloat(leave_adjusted);
        if (isNaN(effective_present)) effective_present = 0;    
        $('.effective_present').text(effective_present);
        $('.effective_present').val(effective_present);


        //effective absent
        var effective_absent=parseFloat(total_days)-parseFloat(effective_present);
        if (isNaN(effective_absent)) effective_absent = 0; 
        $('.effective_absent').text(effective_absent);
        $('.effective_absent').val(effective_absent);

        var other_time_deduction=result.employee.other_time_deduction;
       
       var hd=result.employee.half_day;
       if(hd>0){
           hd=parseFloat(hd)/2;
       }
       $('.hd').text(result.employee.half_day);
       $('.hd').val(result.employee.half_day);
       var half_day=(parseFloat(total_salaryC)/parseFloat(total_days))*parseFloat(hd);
       $('.hd_deduction').text(half_day.toFixed(2));
       $('.hd_deduction').val(half_day.toFixed(2));

       $('.od').text(other_time_deduction);
       var other_deduction=((parseFloat(total_salaryC)/parseFloat(total_days/360))*parseFloat(other_time_deduction));
       if (isNaN(other_deduction)) other_deduction = 0; 
       $('.other_deduction').text(other_deduction.toFixed(2));
       $('.other_deduction').val(other_deduction.toFixed(2));

        //salary calculated

        var salary_ctc=(parseFloat(total_salaryC)/parseFloat(total_days))*parseFloat(effective_present);
        if (isNaN(salary_ctc)) salary_ctc = 0; 
        $('.salary_ctc').text(salary_ctc.toFixed(2));
        $('.salary_ctc').val(salary_ctc.toFixed(2));

        //ot calculated

        var ot_ctc=(parseFloat(overtime_salaryA) + parseFloat(overtime_salaryB));
        if (isNaN(ot_ctc)) ot_ctc = 0; 
        $('.ot_ctc').text(ot_ctc.toFixed(2));
        $('.ot_ctc').val(ot_ctc.toFixed(2));

        //pf deduction
        console.log(total_days);
            if(pf=="No"){
                var pf_ded=0;
            }
            else{
                var pf_ded=(parseFloat(basic_salaryC)+parseFloat(da_salaryC))/total_days*0.12;
                pf_ded=parseFloat(pf_ded)*effective_present;
                pf_ded=pf_ded;
            }
            if (isNaN(pf_ded)) pf_ded = 0; 
        $('.pf_ded').text(pf_ded.toFixed(2));
        $('.pf_ded').val(pf_ded.toFixed(2));

        //esi deduction 
        if(esi=="No"){
                var esi_ded=0;
            }
            else{
                var esi_ded=((parseFloat(basic_salaryC)+parseFloat(da_salaryC))/parseFloat(total_days))*(parseFloat(effective_present)*0.0075);
                esi_ded=esi_ded;
            }
        
            if (isNaN(esi_ded)) esi_ded = 0; 
        $('.esi_ded').text(esi_ded.toFixed(2));
        $('.esi_ded').val(esi_ded.toFixed(2));

        var salary=parseFloat(salary_ctc)+parseFloat(ot_ctc)-parseFloat(pf_ded)-parseFloat(esi_ded)-parseFloat(ad);
        if (isNaN(salary)) salary = 0; 
        $('.salary').text(salary.toFixed(2));
        $('.salary').val(salary.toFixed(2)); 
            console.log(result);
            $('#ajax_loader_div').css('display','none');
            }
        }
        
        });
    }
    </script>
 
@endsection
@section('main_section')
    <section class="content">
                <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                </div>
        <!-- Default box -->
        <form action="/salaryC/create" method="post">
        @csrf
         
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
               
                <div class="container-fluid">
                <div class="row">
                        
                        <div class="col-md-6">
                            <label for="">Select Employee</label>

                            <select name="employee" id="" class="select2 input-css employee">
                            <option value="">Select Employee</option>
                            @foreach($employee as $key)
                                <option value="{{$key['id']}}">{{$key['name']." - ".$key['employee_number']}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                          <label for="">Select Month</label>
                          <input type="text" autocomplete="off" class="date input-css datepickerss" name="date">
                        </div>
                </div><br><br>
                <div class="row">
                <input type="hidden" name="emp_id" class="emp">
                <input type="hidden" name="month" class="mon">
                        <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    
                                        <tr>
                                            <th colspan="3">Employee Name:</th>
                                            <td ><label for="" class="emp_name"></label></td>
                                            
                                            <th colspan="3">Employee Code:</th>
                                            <td><label for="" class="emp_code"></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Total Salary C:</th>
                                            <td ><label for="" class="tot_sal"></td>
                                            
                                            
                                        </tr>
                                        <tr>
                                            <th colspan="3">No of working days:</th>
                                            <td ><input type="hidden" name="days" class="days input-css"><label for="" class="days"></td>
                                            
                                            <th colspan="3">Total Present days:</th>
                                            <td><input type="hidden" name="tot_p" class="tot_p input-css"><label for="" class="tot_p"></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Total absent days:</th>
                                            <td ><input type="hidden" name="tot_a" class="tot_a input-css"><label for="" class="tot_a"></td>
                                            
                                            <th colspan="3">Opening Leave balance:</th>
                                            <td> <label for="" class="open_leave"></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Leave adjusted:</th>
                                            <td >
                                            <input type="number" step="none" name="leave_adjusted" min="0"   class="leave_adjusted input-css">
                                            </td>
                                            
                                            <th colspan="3">Closing Leave balance:</th>
                                            <td><label for="" class="close_leave"></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Effective present days:</th>
                                            <td><input type="hidden" name="effective_present" class="effective_present input-css"><label class="effective_present"><label></td>
                                            
                                            <th colspan="3">Effective absent days:</th>
                                            <td><input type="hidden" name="effective_absent" class="effective_absent input-css"><label class="effective_absent"></label></td>
                                        </tr>
                                        <th colspan="3">No. of Half Day Taken:</th>
                                            <td><input type="hidden" name="hd" class="hd input-css"><label for="" class="hd"></td>
                                            <th colspan="3">Half Day Deduction calculated:</th>
                                            <td ><input type="hidden" name="hd_deduction" class="hd_deduction input-css"> <label  class="hd_deduction"></label</td>
                                            
                                        </tr>
                                        <tr>
                                        <th colspan="3">OT (in min):</th>
                                            <td ><input type="hidden" name="ot" class="ot input-css"> <label  class="ot"></label</td>
                                            <th colspan="3">Late (in min):</th>
                                            <td ><input type="hidden" name="late" class="late input-css"> <label  class="late"></label</td>
                                            
                                        </tr>
                                        <tr>
                                            <th colspan="3">OT-Late (in minutes):</th>
                                            <td ><label for="" class="ot_late"></td>
                                            <th colspan="3">OT calculated:</th>
                                            <td ><input type="hidden" name="ot_ctc" class="ot_ctc input-css"> <label  class="ot_ctc"></label</td>
                                            
                                             </tr>
                                        <tr>
                                        <th colspan="3">Other Time Deduction (in min):</th>
                                            <td><label for="" class="od"></td>
                                            <th colspan="3">Other Time Deduction calculated:</th>
                                            <td ><input type="hidden" name="other_deduction" class="other_deduction input-css"> <label  class="other_deduction"></label</td>
                                            
                                            </tr>
                                        <tr>
                                        <tr><th colspan="3">Salary calculated:</th>
                                            <td><input type="hidden" name="salary_ctc" class="salary_ctc input-css"><label class="salary_ctc"></label></td>
                                       
                                            <th colspan="3">Leave Deduction:</th>
                                            <td> <input type="hidden" name="leave_ded" class="leave_ded input-css">
                                            <label class="leave_ded"></label></td>
                                       </tr>
                                        <th colspan="3">PF Deduction:</th>
                                            <td> <input type="hidden" name="pf" class="pf_ded input-css"><label class="pf_ded"></label></td>
                                        
                                            <th colspan="3">ESI Deduction:</th>
                                            <td ><input type="hidden" name="esi" class="esi_ded input-css"><label class="esi_ded"></label></td>
                                            
                                            
                                        </tr>
                                        <tr>
                                        <th colspan="3">Opening advance:</th>
                                            <td><label for="" class="open_adv"></td>
                                            <th colspan="3">Advance taken in the month:</th>
                                            <td ><label for="" class="adv_mon"></td>
                                            
                                           
                                        </tr>
                                        <tr>
                                        <th colspan="3">Advance deducted:</th>
                                            <td>
                                            <input type="number" name="advance" id="" class="advance input-css" min="0">
                                                </td>
                                            <th colspan="3">Balance Advance:</th>
                                            <td ><label for="" class="bal_adv"></td>
                                            
                                           
                                        </tr>
                                        <tr>
                                       
                                            <th colspan="3">Total Salary Payable:</th>
                                            <td> <input type="hidden" name="salary" class="salary input-css">
                                            <label class="salary"></label></td>
                                        </tr>
                                       
                                      
                                        
                                    </table> 
                                    
                        </div>
                    </div>
                 
                   
                </div>
            </div>
        </div>
        <div class="row">
                <div class="col-md-12 submit_div">
                  
                     
                </div>
            </div>
        </form>
      
      </section>
@endsection
 

