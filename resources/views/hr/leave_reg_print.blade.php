
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
          <p style="padding-top:0px;"><b><u>Leave Register</u></b></p>
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
            <center><h4 style="text-align:left">Employee Leave Register</h4></center>
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
              <th><p>Opening Leave balance for the year</p></th>
          </tr>
            <tr>
              @php 
                 $total_p = $format['total_present'];
                 $total_l = $total_p/20;
                 $total_a = $format['absent'];
                 $closing_b = $total_l - $total_a;
                 $leave = $total_l+$format['carried_leave'];
              @endphp
              <td>{{$format['name']}}</td>
              <td>{{$format['employee_number']}}</td>
              <td>{{$format['local_address']}}</td>
              <td>{{$format['designation']}}</td>
              <td>{{$format['department']}}</td>
              <td>{{$format['father_name']}}</td>
              <td>{{$leave}}</td>
            </tr>
          
      </table><br><br>

                            
    </div>
    <!-- /.box-body -->

</div>
    <div class="box">  
        <div class="box-body">
         <div class="row">
             <div class="col-md-12">
             </div>
         </div>                  
        </div>
        <!-- /.box-body -->
    
    </div>

    <div class="box">  
        <div class="box-body">
         <div class="row">
             <div class="col-md-12">
                <center><h4 style="text-align:left"> Leave Details</h4></center>
             </div>

    
    <table border="1" style="width:100%">

     @if(count($details['emp']) != 0) 
     @php

      $emp = $details['emp'];
      $cc = $details['cc'];
      $mon = $details['mon'];
      $leave = $details['leave'];
      $days = $details['days'];
      @endphp
        <tr>
        <td>Months:</td>
            @for ($j = 1; $j <= $cc ; $j++) 
                    @if($j==12)
                    <td>{{"Dec"}}</td>
                    @else
                    <td>{{$mon[$j]}}</td>
                    @endif
                    
            @endfor
        </tr>
        <tr>
        <td>No. of days present in each month :</td>
        @for ($j = 1; $j <= $cc ; $j++) 
                    <td>{{$emp[0][$mon[$j]]}}</td>
        @endfor
        </tr>
        <tr>
        <td>Dates of leaves adjusted every month:</td>
        @for ($j = 1; $j <= $cc ; $j++) 
            @php  $leaves_count = array(); $leaves_count[$mon[$j]]="";

              @endphp
               
                @if($leave[$j][$mon[$j]] == "-")
                            <td>{{"0"}}</td>
                    @else 
                        @php $z=explode(',',$leave[$j][$mon[$j]]) ; @endphp
                        @for($i=0;$i<count($z);$i++)
                        
                            @php 
                            $date=explode(':',$z[$i]) ;
                              $start=$date[0];
                              $end = $date[1];
                           
                            $startDate = new DateTime($start);
                            $endDate = new DateTime($end);
                            
                                $month_s = $startDate->format('m'); 
                                $month_e =$endDate->format('m'); 

                                
                                    if($month_s==$month_e){
                                        $start=date('d-M',strtotime($start));
                                        $end=date('d-M',strtotime($end));
                                        if($leaves_count[$mon[$j]]=="")
                                                $leaves_count[$mon[$j]]=$start." to ".$end;
                                        else
                                                $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]."<br />".$start." to ".$end;
                                        
                                    }
                                    else if($month_s!=$month_e){

                                    if($month_s==$j){
                                        $end=date('Y-m-t',strtotime($start));
                                        $start=date('d-M',strtotime($start));
                                        $end=date('d-M',strtotime($end));
                                        if($leaves_count[$mon[$j]]=="")
                                                $leaves_count[$mon[$j]]=$start." to ".$end;
                                        else
                                                $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]."<br />".$start." to ".$end;
                                        
                                        }
                                        else if($month_e==$j){
                                        $start=date('Y-m-01',strtotime($end));
                                        $start=date('d-M',strtotime($start));
                                        $end=date('d-M',strtotime($end));
                                        if($leaves_count[$mon[$j]]=="")
                                                $leaves_count[$mon[$j]]=$start." to ".$end;
                                        else
                                                $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]."<br />".$start." to ".$end;

                                    }
                                    }
                            
                            
                            @endphp
                            
                        @endfor
                                    <td><p>@php echo $leaves_count[$mon[$j]]; @endphp</p></td>
                @endif
                            
                            
            @endfor
        </tr>

        <tr>
        <td>No. of leaves adjusted every month:</td>
        @for ($j = 1; $j <= $cc ; $j++) 
            @php  
            $leaves_count = array();
             $leaves_count[$mon[$j]]=0;
              @endphp
                @if($leave[$j][$mon[$j]]=="-" || $leave[$j][$mon[$j]]=="")
                            <td>{{"0"}}</td>
                    @else 
                        @php $z=explode(',',$leave[$j][$mon[$j]]) ; @endphp
                        
                        @for($i=0;$i<count($z);$i++)

                        
                            @php 
                            $date=explode(':',$z[$i]) ;
                        
                            $start=$date[0];
                            $end=$date[1];
                            $end =  substr($date[1],0,10);
                            $startDate = new DateTime($start);
                            $endDate = new DateTime($end);
                            
                                $month_s = $startDate->format('m'); 
                                $month_e =$endDate->format('m'); 

                                
                            
                                
                                    if($month_s==$month_e){
                                    
                                        $date_diff =abs(strtotime($end) - strtotime($start));
                                        $years = floor($date_diff / (365*60*60*24));
                                        $months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
                                        $days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
                                        $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]+$days+1;
                                        
                                    }
                                    else if($month_s!=$month_e){

                                    if($month_s==$j){
                                        $end=date('Y-m-t',strtotime($start));
                                        $date_diff = abs(strtotime($end) - strtotime($start));
                                        $years = floor($date_diff / (365*60*60*24));
                                        $months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
                                        $days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  
                                        $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]+$days+1;
                                        
                                        }
                                        else if($month_e==$j){
                                        $start=date('Y-m-01',strtotime($end));
                                        $date_diff = abs(strtotime($end) - strtotime($start));
                                        $years = floor($date_diff / (365*60*60*24));
                                        $months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
                                        $days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  
                                        $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]+$days+1;
                                        


                                    

                                    }
                                    }
                            
                            
                            @endphp
                            
                        @endfor
                                    <td>{{$leaves_count[$mon[$j]]}}</td>
                @endif
                            
                            
            @endfor
        </tr>
        <tr>
        <td>Closing monthly leave balance:</td>
        @for ($j = 1; $j <= $cc ; $j++) 
            @php  $leaves_count = array(); $leaves_count[$mon[$j]]=0;  @endphp
                @if($leave[$j][$mon[$j]]=="-" || $leave[$j][$mon[$j]]=="")
                            <td>{{"0"}}</td>
                    @else 
                        @php $z=explode(',',$leave[$j][$mon[$j]]) ; @endphp
                        
                        @for($i=0;$i<count($z);$i++)

                        
                            @php 
                            $date=explode(':',$z[$i]) ;
                        
                            $start=$date[0];
                            $end=$date[1];
                             $end =  substr($date[1],0,10);
                            $startDate = new DateTime($start);
                            $endDate = new DateTime($end);
                            
                                $month_s = $startDate->format('m'); 
                                $month_e =$endDate->format('m'); 

                                
                            
                                
                                    if($month_s==$month_e){
                                    
                                        $date_diff =abs(strtotime($end) - strtotime($start));
                                        $years = floor($date_diff / (365*60*60*24));
                                        $months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
                                        $days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
                                        $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]+$days+1;
                                        
                                    }
                                    else if($month_s!=$month_e){

                                    if($month_s==$j){
                                        $end=date('Y-m-t',strtotime($start));
                                        $date_diff = abs(strtotime($end) - strtotime($start));
                                        $years = floor($date_diff / (365*60*60*24));
                                        $months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
                                        $days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  
                                        $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]+$days+1;
                                        
                                        }
                                        else if($month_e==$j){
                                        $start=date('Y-m-01',strtotime($end));
                                        $date_diff = abs(strtotime($end) - strtotime($start));
                                        $years = floor($date_diff / (365*60*60*24));
                                        $months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
                                        $days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  
                                        $leaves_count[$mon[$j]]=$leaves_count[$mon[$j]]+$days+1;
                                        


                                    

                                    }
                                    }
                            
                            
                            @endphp
                            
                        @endfor
                                  @php 
                                  
                                        if($emp[0]['total_present']=="0"){
                                            $total=0;
                                            $mo_tot=0;
                                        }
                                        else{
                                            $total=$emp[0]['total_present']/20;
                                            $total=intval($total);
                                            $mo_tot=$total-$leaves_count[$mon[$j]];
                                        }
                                        
                                       
                                    @endphp
                                    <td>{{$mo_tot}}</td>
                @endif
                            
                            
            @endfor
        </tr>
        @endif
   
    </table>

    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <label for="">Total days present in current year : {{$emp[0]['total_present_current']}}</label>
            </div>
    
            <div class="col-md-6">
            @php if($emp[0]['total_present']=="0"){
                    $total=0;
                    $mo_tot=0;
                }
                else{
                    $total=$emp[0]['total_present']/20;
                    $total=intval($total);
                }
                
                
            @endphp

                <label for="">No of leaves calculated for the year : {{$total}} </label>
            </div>
    
  
            <div class="col-md-6">
                <label for="">Total leaves (Closing balance for the year+no of leaves calculated) : {{$total + $emp[0]['carried_leave']}}</label>
            </div>
   
            <div class="col-md-6">
                <label for="">Leaves carried forward : {{$emp[0]['carried_leave']}}</label>
            </div>
    
        </div>
    </div>
    
    <div class="row">
    <div class="col-md-12">
        <div class="col-md-6">
        <label for="">Leaves Paid : {{$emp[0]['paid_leave']}}</label>
        </div>
        </div>
    
    </div>
    

          

          </div>      
        </div>    
    </div>
      <br>
        <div class="row">
         <div class="col-md-12">
           <div class="col-md-6"style="float:left">
              <h4>Sign of Employee</h4>
              <h4>Authorized Signatory</h4>
           </div>
         </div>
       </div>
</body>
</html>