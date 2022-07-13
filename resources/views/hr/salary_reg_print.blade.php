
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="css/io_templates.css">
<style>
.tablestyle1 {
  border:none !important;
}
.tablestyle1 tr td{
  border:none;
  border-bottom: none;
  font-size: 15px;
}
.tablestyle1 td{    
}
    .rotation {
         
    
}

</style>
</head>
<body>
                    
    <div class="box">  
        <div class="box-body">
          <div class="box-header with-border">
              <div class="col" style="width:700px;height: 30px;text-align:center">
                  <img src="./images/logo.jpg"  class="logopp" style="width:80px;">
              </div>
          </div>
      <div  style="float: left; margin-right: 100px;text-align: center;height:30px;width:100%">
          <p style="padding-top:0px;"><b><u>Salary Register</u></b></p>
          </div>
        </div>
    </div>
<br>
<div class="box">
    <div class="box-body">

    </div>
</div>
<div class="box">  
    <div class="box-body">
     <div class="row">
         <div class="col-md-12">
            <center><h4 style="text-align:left">Employee Salary Register</h4></center>
         </div>
     </div>
      <table class="tables" style="width:100%;">
          <tr>
              <th><p>Name</p></th>
              <th><p>Employee Code </p></th>
              <th><p>Address</p></th>
              <th><p>Designation</p></th>
              <th><p>Department</p></th>
              <th><p>Father's Name</p></th>
              <th><p>Total Present days</p></th>
              <th><p>Total Absent Days</p></th>
              <th><p>Total Salary C paid</p></th>
              <th><p>PF Deduction</p></th>
              <th><p>ESI Deduction</p></th>
              <th><p>Opening advance</p></th>
              <th><p>Advance deducted</p></th>
              <th><p>Advance balance</p></th>
          </tr>
          <tr>
              <td>{{$jobdata[0]['emp_name']}}</td>
              <td>{{$jobdata[0]['employee_number']}}</td>
              <td>{{$jobdata[0]['local_address']}}</td>
              <td>{{$jobdata[0]['designation']}}</td>
              <td>{{$jobdata[0]['department']}}</td>
              <td>{{$jobdata[0]['father_name']}}</td>
              <td>{{$jobdata[0]['total_present_current']}}</td>
              <td>{{$jobdata[0]['total_absent_current']}}</td>
              <td>{{$jobdata[0]['total_salaryC']}}</td>
              <td>{{$jobdata[0]['pf_ded']}}</td>
              <td>{{$jobdata[0]['esi_ded']}}</td>
              <td>{{$jobdata[0]['opening_advance']}}</td>
              <td>{{$jobdata[0]['adv_ded']}}</td>
              <td>{{$jobdata[0]['balance_advance']}}</td>
          </tr>
           
          
      </table><br><br>

        <div class="box">  
        <div class="box-body">
         <div class="row">
             <div class="col-md-12">
                <center><h4 style="text-align:left"> Details</h4></center>
             </div>
             <table border="1" style="width:100%">
               <tr>
                @php
                  $month = date('m',strtotime($year));
                  $yr = date('Y',strtotime($year));
                  $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $yr); 
                  @endphp
                  @for($i = 1; $i <= $num_of_days; $i++){ 
                   <th><p>{{$i}}</p></th>
                  @endfor
                
                  
               </tr>
               <tbody>
                 <tr>
                    @for($i = 1; $i <= $num_of_days; $i++){ 
                     @php
                      if($i == 1 || $i == 2 || $i == 3 || $i == 4 || $i == 5|| $i == 6|| $i == 7 || $i == 8 || $i == 9){
                      $i = '0'.$i;
                    }else{
                    $i = $i;
                  }
                      $col = $i.'_'.$month_name; @endphp
                   
                    @if($jobdata[0][$col] == 'WO')
                     <td class="rotation" style=" -moz-transform: rotate(-90.0deg);-ms-transform: rotate(-90.0deg);-o-transform: rotate(-90.0deg);-webkit-transform: rotate(-90.0deg);transform: rotate(-90.0deg);height: auto;width:auto;margin-top: 40px;"><p class="">Sunday</p></td>
                    @else
                     <td><p>{{$jobdata[0][$col]}}</p></td>
                    @endif
                  
                  @endfor
                 </tr>
               </tbody>
             </table>
         </div>                  
        </div>
        <!-- /.box-body -->
    
    </div>

                            
    </div>
    <!-- /.box-body -->

</div>
 
    

          

          </div>      
        </div>    
    </div>
</body>
</html>