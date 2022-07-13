
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
                                                <h3><u>MATERIAL GATE PASS</u></h3>
                                                <p style="font-size:10px;margin-top:0px">"Pratibha Press & Multimedia Pvt. Ltd. <br>
                                                            Gate no-2,6 Ashok Nagar(Near Bansmandi Chauraha) <br>
                                                                Latouche Road, Lucknow-226018"		
                                                                        </p>
                                                </div>
                                                <div style="margin-left:140px;text-align:center;width:400px">
                                                    <p style="font-size:14px;margin-top:0px">Gate Pass No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;                  
                                                {{$pass['gatepass_number']}}</p>
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
                                                    <p>Carrier Name:</p>
                                                </td>
                                                <td colspan="4" style="border-bottom: 1px solid black;text-align:left;">
                                                   
                                                        @php
                                                      
                                                          if($pass['mode']==2)
                                                          {
                                                                echo $pass['courier_name'];
                                                          }
                                                           
                                                     
                                                        @endphp
                                                </td>
                                            </tr><br>
                                            <tr>
                                                    <td style="width:135px;">
                                                        <p>Dispatch Mode: </p>
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;text-align:left;width:185px;"> 
                                                      @php
                                                     
                                                                if($pass['mode']==2)
                                                                {
                                                                        echo "Self";
                                                                }
                                                                if($pass['mode']==1)
                                                                {
                                                                        echo "Transporter";
                                                                }
                                                                if($pass['mode']==3)
                                                                {
                                                                        echo "Courier";
                                                                }
                                                                
                                                       
                                             
                                              @endphp
                                                            
                                                      
                                                    </td>
                                                    <td style="rext-align:right">
                                                            <p>Transporter/Courier: </p>
                                                        </td>
                                                        <td style="border-bottom: 1px solid black;text-align:left;width:195px;">
                                                           @php
                                                              
                                                               
                                                                        if($pass['mode']=="1" || $pass['mode']=="3"){
                                                                        echo $pass['courier_name'];
                                                               }
                                                                
                                                              
                                                           @endphp
    
                                                        </td>
                                                </tr>
                                               
                                        </table><br>
                        <table class="tablestyle" style="width:100%;">
                                <tr>
                                        <td>
                                                <h4>Challan Number</h4>
                                        </td>
                                       
                                        <td>
                                                <h4 id="book">Item Name</h4>
                                        </td>
                                       
                                        <td>
                                                <h4 id="book"> Quantity </h4>
                                        </td>
                                      
                                        <td>
                                                <h4> Buyer </h4>
                                        </td>
                                        <td>
                                                        <h4> Packing Details </h4>
                                                </td>
                                </tr>
                                @if ($pass['challan_type']=="PPML/DCN/")
                                @foreach ($dc as $item)
                                @foreach ($item as $key)
                                    <tr>
                                            <td>
                                                    {{$key['challan_number']}}
                                            </td>
                                            <td>
                                                    @if ($key['item_category_id']!=15)
                                                        {{$key['name']."-".$key['job_size']}}
                                                    @else
                                                        {{$key['other_item_name']."-".$key['job_size']}}
                                                    @endif
                                              </td>

                                            <td>
                                                      {{$key['good_qty']}}
                                              </td>
                                              
                                              <td>
                                                              {{$key['partyname']}}
                                                      </td>
                                                      <td>
                                                                {{$key['packing_details']}}
                                                        </td>
                                    </tr>
                              @endforeach
                              @endforeach
                                @endif
                                @if ($pass['challan_type']=="PPML/IDC/")
                                @foreach ($mat as $item)
                                @foreach ($item as $key)
                                    <tr>
                                            <td>
                                                    {{$key['idc_number']}}
                                            </td>
                                            <td>
                                                      {{$key['item_desc']}}
                                              </td>

                                            <td>
                                                      {{$key['item_qty']}}
                                              </td>
                                              
                                              <td>
                                                        {{$key['dispatch_to']}}
                                                </td>
                                                <td>
                                                                {{$key['packing_desc']}}
                                                        </td>
                                    </tr>
                              @endforeach
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