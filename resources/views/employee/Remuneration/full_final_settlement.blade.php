@extends($layout)
 
@section('title', 'Full & Final Settlement')
 
@section('user', Auth::user()->name)
 
@section('breadcrumb')
    <li><a href="#"><i class=""></i> Full & Final Settlement</a></li>
   

@endsection
@section('css')
<style>
.No{
    color:red;
}
.Yes{
    color:green;
}
</style>
@endsection

@section('js')
<script>
var count=1;
$('input[type=radio][name=is_b]').change(function() {
        if (this.value == "Yes"){
          
            $('.bon_pre').show();
            if(count==1){
                var bb=$('input[name=bonus_ctc_pre]').val();
            var tt=$('input[name=full_final]').val();
            console.log(bb);
            console.log(tt);
            
            
            var t=parseFloat(bb)+parseFloat(tt);
            console.log(t);
            

            $('.full_final').text(t);
            $('.full_final').val(t);
            count=count+1;
            }
            
        }
            
        if (this.value == "No"){
            $('.bon_pre').hide();
            if(count>1){
                var bb=$('input[name=bonus_ctc_pre]').val();
            var tt=$('input[name=full_final]').val();
            var t=parseFloat(tt)-parseFloat(bb);
            $('.full_final').text(t);
            $('.full_final').val(t);
            count=1;
            }
        }
           
    });
</script>
    <script>
    var sal_to_be=0;
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
   
    $('.employee').on('change',function(e){
        var emp=$('.employee').val();
        if((emp="")){
            e.preventDefault();
        }   
        else{
            getData(0);
        }   
    });
    $('.salA').on('change',function(e){
        var emp=$('.employee').val();
        if((emp="")){
            e.preventDefault();
        }   
        else{
            getData(1);
        }   
    });
    $('.bonusA').on('change',function(e){
        var emp=$('.employee').val();
        if((emp="")){
            e.preventDefault();
        }   
        else{
            getData(1);
        }   
    });
    $('.gratuity_salA').on('change',function(e){
        var emp=$('.employee').val();
        if((emp="")){
            e.preventDefault();
        }   
        else{
            getData(1);
        }   
    });
    function getData(counter){
        var mon=$('.date').val();
        var emp=$('.employee').val();
        $('#ajax_loader_div').css('display','block');
        $.ajax({
        type:"GET",
        url:"/employee/full&final/settlement/api",
        data:{'emp':emp,'mon':mon},
        success: function(result){
            if (result) {
                console.log(result);
                //calculation of no of present days
                var mon=result.mon;
                var enc=result.encashment;
                var cc=result.cc;
                var leaving_date=result.leaving_date;
                $('.leave_date').text(leaving_date);
                $('.days').empty();
                $('.days').append('<thead><tr>');
                for(var i=1;i<=cc;i++){
                  
                    $('.days').append('<th>'+mon[i]+'</th>'); 
                }
                $('.days').append('</tr></thead>');
                $('.days').append('<tbody><tr>');
                for(var i=1;i<=cc;i++){
                   
                    $('.days').append('<td>'+enc[mon[i]]+'</td>');  
                }
                $('.days').append('</tbody></tr>');
                var twelve=result.all_month_salary;
                var twelveB=result.all_month_salaryB;
                var month_no_paid=result.month_no_paid;
                $('.valuess').empty();
                $('.values').empty();
                $('.salaryA').empty();
                for(var i=0;i<month_no_paid.length;i++){
                    var pp=month_no_paid[i].replace('_','-')
                        $('.salaryA').append(
                            '<th>'+pp+'</th>'
                            
                            );
                            $('.values').append(
                            '<td>'+twelve[month_no_paid[i]]+'</td>'
                            );
                            $('.valuess').append(
                            '<td>'+twelveB[month_no_paid[i]]+'</td>'
                            );
                }
                $('span')
                .filter(function(){ return $(this).text() == 'No'; })
                .css('color','red');
                var balance_advance=result.encashment.balance_advance;
                var net_salaryA=result.net_salaryA.net_salary;
                var net_salaryB=result.net_salaryB.net_salary;
                if (isNaN(net_salaryA)) net_salaryA = 0;
                if (isNaN(net_salaryB)) net_salaryB = 0;
                $('.bal_SalA').text(result.net_salaryA.net_salary);
                $('.bal_SalB').text(result.net_salaryB.net_salary);
                $('.bal_SalA').val(result.net_salaryA.net_salary);
                $('.bal_SalB').val(result.net_salaryB.net_salary);
                if (isNaN(balance_advance)) balance_advance = 0; 
                $('.adv').text(balance_advance);
                $('.adv').val(balance_advance);

                var total_present_current=result.encashment.total_present_current;
                $('.present_days').text(total_present_current);
                var total_leaves=result.encashment.total_leaves;
                var total_salaryA=result.encashment.total_salaryA;
                var total_present=result.encashment.total_present;
                var carried_leave=result.encashment.carried_leave;
                console.log(total_salaryA);
                var tot_leaves_ctc=parseInt(parseInt(total_present)/20);
                $('.leaves_ctc').text(tot_leaves_ctc);
                var present_leaves=parseInt(parseInt(tot_leaves_ctc)-parseInt(total_leaves));
                $('.present_leaves').text(present_leaves);
                total_salaryA=parseFloat(total_salaryA);
                
                if(counter==1){
                    total_salaryA=$('.salA').val();
                }
                var leave_enc=parseFloat(parseInt(parseInt(tot_leaves_ctc)+parseInt(present_leaves))*parseFloat(total_salaryA));
                $('.leaves_enc').text(leave_enc.toFixed(2));
                $('.leaves_enc').val(leave_enc.toFixed(2));
                $('.salA').val(parseFloat(total_salaryA));

                //bonus calculations

                
                var checkdiff=result.bonus_ctc.checkdiff;
                $('.no_bonus').text(checkdiff);
                var apr_sep=result.bonus_ctc.apr_sep;
                if (isNaN(apr_sep)) apr_sep = 0; 
                
                var oct_mar=result.bonus_ctc.oct_mar;
                if (isNaN(oct_mar)) oct_mar = 0; 
                
                var payroll_apr=result.bonus_ctc.payroll_apr;
                if (isNaN(payroll_apr)) payroll_apr = 0; 
                
                var payroll_oct=result.bonus_ctc.payroll_oct;
                if (isNaN(payroll_oct)) payroll_oct = 0; 
                var total_sal_a=result.encashment.total_salaryA;
                if (isNaN(total_sal_a)) total_sal_a = 0; 
                var bonus=result.bonus_ctc.bonus;
                
                if(counter==1){
                    bonus=$('.bonusA').val();
                }
                $('.bonusA').val(bonus);

                var tot_sal_pre_apr_ctc=result.bonus_ctc.tot_sal_pre_apr;
                if(tot_sal_pre_apr_ctc==0){
                    tot_sal_pre_apr_ctc=result.bonus_ctc.total_sal_a;
                }
                var tot_sal_pre_oct_ctc=result.bonus_ctc.tot_sal_pre_oct;
                if(tot_sal_pre_oct_ctc==0){
                    tot_sal_pre_oct_ctc=result.bonus_ctc.total_sal_a;
                }

                
                var total_apr_sep = (parseFloat(tot_sal_pre_apr_ctc)+parseFloat(payroll_apr))*parseFloat(apr_sep);
                if (isNaN(total_apr_sep)) total_apr_sep = 0; 
                var total_oct_mar = (parseFloat(tot_sal_pre_oct_ctc)+parseFloat(payroll_oct))*parseFloat(oct_mar);
                if (isNaN(total_oct_mar)) total_oct_mar = 0; 
                $('.salary_pay1').text(total_apr_sep.toFixed(2));
                $('.salary_pay2').text(total_oct_mar.toFixed(2));
                // alert(total_apr_sep);
                // alert(total_oct_mar);
                var bon_curr=result.bon_curr;
                var bonus_ctc=parseFloat(bonus)*parseFloat(checkdiff)*parseFloat(bon_curr);
                bonus_ctc=bonus_ctc*parseFloat(tot_sal_pre_oct_ctc)/(parseFloat(total_apr_sep)+parseFloat(total_oct_mar));
                if (isNaN(bonus_ctc)) bonus_ctc = 0; 
                $('.bonus_ctc').text(bonus_ctc.toFixed(2));
                $('.bonus_ctc').val(bonus_ctc.toFixed(2));

                

                //previous year bonus
                var fg=result.flag;
                if(fg==0){
                var checkdiff_pre=result.bonus_pre.checkdiff;
                var tot_sal_pre_apr=result.bonus_pre.tot_sal_pre_apr;
                if(tot_sal_pre_apr==0){
                    tot_sal_pre_apr=result.bonus_pre.total_sal_a;
                }
                var tot_sal_pre_oct=result.bonus_pre.tot_sal_pre_oct;
                if(tot_sal_pre_oct==0){
                    tot_sal_pre_oct=result.bonus_pre.total_sal_a;
                }
                var apr_sep_pre=result.bonus_pre.apr_sep;
                if (isNaN(apr_sep_pre)) apr_sep_pre = 0; 
                
                var oct_mar_pre=result.bonus_pre.oct_mar;
                // alert(oct_mar_pre);
                if (isNaN(oct_mar_pre)) oct_mar_pre = 0; 
                
                var payroll_apr_pre=result.bonus_pre.payroll_apr;
                if (isNaN(payroll_apr_pre)) payroll_apr_pre = 0; 
                
                var payroll_oct_pre=result.bonus_pre.payroll_oct;
                if (isNaN(payroll_oct_pre)) payroll_oct_pre = 0; 
                var total_sal_a=result.encashment.total_salaryA;
                if (isNaN(total_sal_a)) total_sal_a = 0; 
                var bonus_pre=result.bonus_pre.bonus;
                if(counter==1){
                    bonus_pre=$('.bonusA').val();
                }
               
                var total_apr_sep_pre = (parseFloat(tot_sal_pre_apr)+parseFloat(payroll_apr_pre))*parseFloat(apr_sep_pre);
                if (isNaN(total_apr_sep_pre)) total_apr_sep_pre = 0; 
                var total_oct_mar_pre = (parseFloat(tot_sal_pre_oct)+parseFloat(payroll_oct_pre))*parseFloat(oct_mar_pre);
                if (isNaN(total_oct_mar_pre)) total_oct_mar_pre = 0; 

                
                // alert(total_apr_sep_pre);
                // alert(total_oct_mar_pre);
                var bon_pre=result.bon_pre;
                var bonus_ctc_pre=parseFloat(bonus_pre)*parseFloat(checkdiff_pre)*parseFloat(bon_pre);
                bonus_ctc_pre=bonus_ctc_pre*parseFloat(tot_sal_pre_oct)/(parseFloat(total_apr_sep_pre)+parseFloat(total_oct_mar_pre));
                
                console.log("total_sal_a "+total_sal_a);
                console.log("apr_sep "+apr_sep_pre);
                console.log("payroll_apr "+payroll_apr_pre);
                console.log("total_apr_sep "+total_apr_sep_pre);

                console.log("oct_mar "+oct_mar_pre);
                console.log("payroll_oct "+payroll_oct_pre);
                console.log("total_oct_mar "+total_oct_mar_pre);

                if (isNaN(bonus_ctc_pre)) bonus_ctc_pre = 0; 
                $('.bonus_ctc_pre').text(bonus_ctc_pre.toFixed(2));
                $('.bonus_ctc_pre').val(bonus_ctc_pre.toFixed(2));
                }
                else{
                    $('.bonus_ctc_pre').text(0);
                    $('.bonus_ctc_pre').val(0);
                }

                var joining_date=result.bonus_ctc.doj;
                $('.join_d').text(joining_date);
                var diff=result.bonus_ctc.diff;
                diff=parseFloat(diff)/12;
                diff=diff.toFixed();
                var From_date = new Date(result.date_j);
                var To_date = new Date(result.levae);
                var diff_date =  To_date - From_date;
                
                var years = Math.floor(diff_date/31536000000);
                var months = Math.floor((diff_date % 31536000000)/2628000000);
                var days = Math.floor(((diff_date % 31536000000) % 2628000000)/86400000);

                if(counter==1){
                    total_sal_a=$('.gratuity_salA').val();
                }
                $('.duration').text(years+" years "+months+" months and "+days+"  days");
                $('.gratuity_salA').val(parseFloat(total_sal_a));
                console.log("diff is "+diff);
                
                if(diff<5){
                    var gratuity=0;
                }
                else{
                    var gratuity=(parseFloat(total_sal_a)*15)/30;
                    gratuity=gratuity*diff;
                }
               
                
                $('.gratuity').text(gratuity.toFixed(2));
                $('.gratuity').val(gratuity.toFixed(2));
                //salary A
        

        var full_final=parseFloat(leave_enc)+parseFloat(bonus_ctc)+parseFloat(gratuity)+parseFloat(net_salaryA)+parseFloat(net_salaryB)-parseFloat(balance_advance);
        var is_b=$('.is_b').val();
        if(is_b=="Yes"){
            full_final=full_final+bonus_ctc_pre;
        }
        
        $('.full_final').text(full_final.toFixed(2));
        $('.full_final').val(full_final.toFixed(2)); 
        $('.bon_ques').show();
            $('#ajax_loader_div').css('display','none');
            $('.submit_div').empty();
            $('.submit_div').append('<input type="submit" class="btn btn-primary" style="float:right" value="Submit">');
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
        <form action="/employee/full&final/settlement/create" method="post">
        @csrf
         
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
               
                <div class="container-fluid">
                <div class="row">
                        
                        <div class="col-md-4">
                            <label for="">Select Employee</label>

                            <select name="employee" id="" class="select2 input-css employee">
                            <option value="">Select Employee</option>
                            @foreach($emp as $key)
                                <option value="{{$key['id']}}">{{$key['name']." - ".$key['employee_number']}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                          <label for="">Select F&F settlement date</label>
                          <input type="text" autocomplete="off" class="date input-css datepicker" name="fnfdate">
                        </div>
                        <div class="col-md-4">
                          <label for="">Leaving date</label>
                          <p class="leave_date"></p>
                        </div>
                </div><br><br>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">No of days present for each month</label>
                        <table class="days table table-bordered table-striped" style="width:100%">
                            
                        </table>
                    </div>
                </div>
                <h4>Calculation of Leave Encashment:</h4>
                <div class="row">
                    <table class="table table-bordered table-striped" style="width:100%">
                            <tr>
                            <th>Total no of days present :</th>
                            <td><label class="present_days"></label></td>
                            <th>Present leave balance :</th>
                            <td><label class="present_leaves"></label></td>
                            <th>Leaves calculated (=total no of days present/20) :</th>
                            <td><label class="leaves_ctc"></label></td>
                            </tr>
                            <tr>
                            <th>Leave Encashment  :</th>
                            <td><label class="leaves_enc"></label>
                            <input type="hidden" name="leaves_enc" class="leaves_enc">
                            </td>
                            <th>salary figure on which leave encashment is calculated here</th>
                            <td><input type="number" name="salA" class="salA"></td>
                            </tr>
                    </table>
                </div><br><br>
                <h4>Bonus Calculation:</h4>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">Total Salary A paid to the employee</label>
                        <table class="table table-bordered table-striped" style="width:100%">
                        <thead>
                            <tr class="salaryA">
                            </tr>
                        </thead>
                        <tbody>
                        <tr class="values">
                        
                        </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">Total Salary B paid to the employee</label>
                        <table class="table table-bordered table-striped" style="width:100%">
                        <thead>
                        <tr class="salaryA">
                            </tr>
                        </thead>
                        <tbody>
                        <tr class="valuess">
                        
                        </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-bordered table-striped" style="width:100%">
                            <tr>
                            <th>Bonus on Salary A :</th>
                            <td><input type="number" name="bonusA" class="bonusA"></td>
                            <th>No. of months bonus is applicable :</th>
                            <td><label class="no_bonus"></label></td>
                            <th>Salary payable from Apr-Sep :</th>
                            <td><label class="salary_pay1"></label></td>
                            </tr>
                            <tr>
                            <th>Salary payable from Oct-Mar  :</th>
                            <td><label class="salary_pay2"></label></td>
                            <th>Bonus calculated : </th>
                            <td><label class="bonus_ctc"> </label>  
                            <input type="hidden" name="bonus_ctc" class="bonus_ctc"></td>
                            
                            </tr>
                    </table>
                    
                </div>
                <div class="row">
                    <div class="col-md-12">
                    <div class="col-md-6 bon_ques" style="display:none">
                        <label for=""> Is bonus is unpaid for previous financial year??</label>
                        <div class="col-md-2"><input type="radio" name="is_b" id="" value="Yes">Yes</div>
                        <div class="col-md-2"><input type="radio" name="is_b" id="" value="No">No</div>
                    </div>
                    <div class="col-md-6 bon_pre" style="display:none">
                    <label>Bonus calculated Previous: <span class="bonus_ctc_pre"></span></label>
                         
                    <input type="hidden" name="bonus_ctc_pre" class="bonus_ctc_pre"></td>
                    </div>
                    </div>
                </div>
                    <br><br>
                    <h4>Gratuity calculation:</h4>
                <div class="row">
                    <table class="table table-bordered table-striped" style="width:100%">
                            <tr>
                            <th>Employee joining date :</th>
                            <td><label class="join_d"></label>  </td>
                            <th>Duration of service till last working date :</th>
                            <td><label class="duration"></label>  </td>
                            <th>Gratuity Calculated:</th>
                            <td><label class="gratuity"></label> <input type="hidden" name="gratuity" class="gratuity"> </td>
                            </tr>
                            <tr>
                            <th>salary on which gratuity is calculated here  :</th>
                            <td>
                            <input type="text" name="gratuity_salA" class="gratuity_salA">
                            </td>
                            
                            </tr>
                    </table>
                </div><br><br>
                <div class="row">
                    <table class="table table-bordered table-striped" style="width:100%">
                            <tr>
                            <th>Balance Salary A payable :</th>
                            <td><label class="bal_SalA"></label> <input type="hidden" name="bal_SalA" class="bal_SalA"></td>
                            <th>Balance Salary B payable :</th>
                            <td><label class="bal_SalB"> </label><input type="hidden" name="bal_SalB" class="bal_SalB"></td>
                            <th>Advance balance :</th>
                            <td><label class="adv"></label> <input type="hidden" name="adv" class="adv"></td>
                            </tr>
            
                            <tr>
                            <th>Total Full & Final Payable :</th>
                            <td><label class="full_final"></label><input type="hidden" name="full_final" class="full_final"></td>
                            
                            </tr>
                    </table>
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
 

