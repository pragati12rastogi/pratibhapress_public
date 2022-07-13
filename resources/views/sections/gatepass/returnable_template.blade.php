
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
                                                <h3><u>RETURNABLE GATE PASS</u></h3>
                                                <p style="font-size:10px;margin-top:0px">"Pratibha Press & Multimedia Pvt. Ltd. <br>
                                                            Gate no-2,6 Ashok Nagar(Near Bansmandi Chauraha) <br>
                                                                Latouche Road, Lucknow-226018"		
                                                                        </p>
                                                </div>
                                                <div style="margin-left:140px;text-align:center;width:400px">
                                                    <p style="font-size:14px;margin-top:0px">Gate Pass No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;                  
                                                {{$returnable[0]['gatepass_number']}}</p>
                                                </div>
                                                <div style="text-align:right;">
                                                        <p style="font-size:14px;margin-top:0px"><b>Date & Time :&nbsp;&nbsp;&nbsp;                  
                                                    @php
                                                        $dateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata')); 
                                                        echo $dateTime->format("d/m/Y  H:i A"); 
                                                    @endphp </b></p>
                                                    </div>
                                        </div>
                                        <table class="tablestyle1" style="width:100%;border:none">
                                        
                                            <tr>
                                                    <td style="width:155px;">
                                                        <p>Delivery Challan No.: </p>
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;text-align:left;width:185px;"> 
                                                        @php
                                                        
                                                              
                                                               foreach ($returnable as $key) {
                                                                  
                                                                if($key['challan_type']=="delivery_challan"){
                                                                    echo $key['challan_number'];
                                                                }
                                                                if($key['challan_type']=="internal_dc"){
                                                                    foreach ($ret as $key) {
                                                                        echo $key['idc_number'];
                                                                    }
                                                                } 
                                                          
                                                      }
                                                       @endphp

                                                    </td>
                                                    <td style="rext-align:right">
                                                            <p>Return Date:</p>
                                                        </td>
                                                        <td style="border-bottom: 1px solid black;text-align:left;width:195px;">
                                                            
                                                            @php
                                                            $date=$returnable[0]['return_date'];
                                                            $dateTime = new DateTime($date, new DateTimeZone('Asia/Kolkata')); 
                                                            echo $dateTime->format("d/m/Y"); 
                                                        @endphp 
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p>Buyer:</p>
                                                    </td>
                                                    <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                       
                                                    
                                                         @if($returnable[0]['challan_type']=="delivery_challan")
                                                           
                                                                            {{$returnable[0]['partyname']}}
                                                 
                                                         @endif
                                                           
                                          
                                                        @if($returnable[0]['challan_type']=="internal_dc")
                                                        @foreach ($ret as $key) 
                                                       
                                                                        {{$key['dispatch_to']}}
                                                               
                                                        @endforeach
                                                    @endif
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="width:155px;">
                                                        <p>Out Time:</p>
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;text-align:left;">
                                                        @php
                                                        $dateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata')); 
                                                        echo $dateTime->format("d/m/y  H:i A"); 
                                                    @endphp 
                                                       
                                                    </td>
                                           
                                                    <td style="width:155px;">
                                                        <p>Dispatch Mode:</p>
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;text-align:left;">
                                                        @php
                                                        $idc_mode='';
                                                        foreach ($returnable as $key) {
                                                            if($key['challan_type']=="delivery_challan"){
                                                                $mode=$key['mode'];
                                                                if($mode==1){
                                                                    echo "Transporter";
                                                             }
                                                             if($mode==2){
                                                                echo "Self";
                                                             }
                                                             if($mode==3){
                                                                echo "Courier";
                                                             }
                                                            } 
                                                        if($key['challan_type']=="internal_dc"){
                                                            foreach ($ret as $key) {
                                                                echo $key['mode'];
                                                            }
                                                        } 
                                                            
                                                        }
                                                        
                                                    @endphp
                                                       
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:155px;">
                                                        <p>Carrier Name:</p>
                                                    </td>
                                                    <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                        @php
                                                        $challans='';
                                                        foreach ($returnable as $key) {
                                                            $challans=$key['challan_type'];
                                                            if($key['challan_type']=="delivery_challan"){
                                                                
                                                                $courier_name[]=$key['courier_name'];
                                                            } 
                                                            if($key['challan_type']=="internal_dc"){
                                                                foreach ($ret as $key) {
                                                                    echo $key['courier_name'];
                                                                }
                                                            } 
                                                            
                                                        }
                                                        if($challans=="delivery_challan"){
                                                            $data3 = array_unique($courier_name);
                                                            // now use foreach loop on unique data
                                                            foreach($data3 as $val5) {
                                                                $arr5[]=$val5;
                                                            }
                                                            $value5=implode(',',$arr5);
                                                            echo $value5;
                                                        } 
                                                        
                                                    
                                                    
                                                @endphp
                                                       
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:155px;">
                                                        <p>Remark if any:</p>
                                                    </td>
                                                    <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                       {{$returnable[0]['remark']}}
                                                       
                                                    </td>
                                                </tr>
                                        </table><br>     
                                       
                                        <table class="tablestyle" style="width:100%;">
                                            <tr>
                                                   
                                                    <td>
                                                            <h4 id="book">Item Name</h4>
                                                    </td>
                                                   
                                                    <td>
                                                            <h4 id="book"> Quantity </h4>
                                                    </td>
                                                   
                                            </tr>
                                            
                                            
                                           
                                            @if($returnable[0]['challan_type']=="delivery_challan")
                                            @foreach ($returnable as $key)
                                                <tr>     
                                                    <td>
                                                            @if ($key['item_category_id']==15)
                                                                {{$key['name']."-".$key['job_size']}}
                                                            @else
                                                                {{$key['other_item_name']."-".$key['job_size']}}
                                                            @endif
                                                    </td>
    
                                                    <td>
                                                            {{$key['qty']}}
                                                    </td>
                                                </tr>                  
                                           
                                            @endforeach
                                            @endif
                                            @if($returnable[0]['challan_type']=="internal_dc")
                                                @foreach ($ret as $key) 
                                                <tr>     
                                                        <td>
                                                                {{$key['item_desc']}}
                                                        </td>
        
                                                        <td>
                                                                {{$key['item_qty']}}
                                                        </td>
                                                    </tr>  
                                                @endforeach
                                            @endif
                                            
                                                 
                                              
                                                   
                                              
                                                    
                                                   
                                           
                                           
                                          
                                          
                                    </table>
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
                                        <th style="text-align:right;border: none;">Sign. Of Carrier</th>
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