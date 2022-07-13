
<!DOCTYPE html>
<html lang="en">

<head>

        <link rel="stylesheet" href="css/io_templates.css">
        <style>
        .noBorder {
    border:none !important;
}
.tablestyle1 tr td{
border:none;
border-bottom: none;
font-size: 13px;
}
.tablestyle1 td{
        
}
        </style>
</head>
<body>
                    
        <div class="box">  
                <div class="box-body">
                                <div class="box-header with-border" style="margin-left:0px">
                                                <div class="col" style="width:100px;height:100px;margin-left:0px">
                                               
                                                    <img src="./images/logo.jpg"  class="logopp" style="width:80px; ">
                                                        
                                                </div>
                                               <div  style="margin-left:140px;text-align:center;width:400px">
                                                <h3><u>EMPLOYEE GATE PASS</u></h3>
                                                <p style="font-size:10px;margin-top:0px">"Pratibha Press & Multimedia Pvt. Ltd. <br>
                                                            Gate no-2,6 Ashok Nagar(Near Bansmandi Chauraha) <br>
                                                                Latouche Road, Lucknow-226018"		
                                                                        </p>
                                                </div>
                                                <div style="margin-left:140px;text-align:center;width:400px">
                                                    <p style="font-size:14px;margin-top:0px">Gate Pass No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;                  
                                                {{$employee['gatepass_number']}}</p>
                                                </div>
                                                <div style="text-align:right;">
                                                        <p style="font-size:14px;margin-top:0px"><b>Date & Time :&nbsp;&nbsp;&nbsp;                  
                                                    @php
                                                        $dateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata')); 
                                                        echo $dateTime->format("d/m/y  H:i A"); 
                                                    @endphp </b></p>
                                                    </div>
                                        </div>
                                        <table class="tablestyle1" style="width:100%;border:none">
                                            <tr>
                                                <td>
                                                    <p>Name:</p>
                                                </td>
                                                <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                   {{$employee['name']}}
                                                   
                                                </td>
                                            </tr>
                                            <tr>
                                                    <td style="width:155px;">
                                                        <p>Department: </p>
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;text-align:left;width:185px;"> 
                                                        {{$employee['department']}}

                                                    </td>
                                                    <td style="rext-align:right">
                                                            <p>Designation:</p>
                                                        </td>
                                                        <td style="border-bottom: 1px solid black;text-align:left;width:195px;">
                                                            {{$employee['designation']}}
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:155px;">
                                                        Emp. Contact Number:
                                                    </td>
                                                    <td colspan="3" style="border-bottom: 1px solid black;text-align:left;">
                                                        {{$employee['emp_contact']}}
                                                       
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:155px;">
                                                        <p>Reason:</p>
                                                    </td>
                                                    <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                        {{$employee['reason']}}
                                                       
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:155px;">
                                                        <p>Reason Description:</p>
                                                    </td>
                                                    <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                       
                                                        {{$employee['desc']}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:155px;">
                                                        <p>Out Time:</p>
                                                    </td>
                                                    <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                        @php
                                                        $dateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata')); 
                                                        echo $dateTime->format("d/m/y  H:i A"); 
                                                    @endphp
                                                       
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:155px;">
                                                        <p>Expected Duration:</p>
                                                    </td>
                                                    <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                        {{$employee['est_duration']}}
                                                       
                                                    </td>
                                                </tr>
                                        </table><br>
                </div>
                <!-- /.box-body -->

        </div>



        <!-- /.box -->
      <br>
        <!-- /.box -->
        <div class="box">
                <div class="box-body">
                        <table class="noBorder" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; border: none; width:100%;border:none">
                                <tr style="border: none;">
                                        <th style="border: none;">Authorized Sign.:</th>
                                        <th style="text-align:right;border: none;">Sign. Of Employee</th>
                                </tr>
                               
                        </table>
                </div>
                <!-- /.box-body -->

        </div>
        <!-- /.box -->
       {{-- <div class="row">
               <div class="col-md-12">
       <div class="col-md-6"style="float:left">
                <h4>Created By</h4>

                <h4>Approved By</h4>
       </div>
               </div>
       </div> --}}




</body>

</html>