
<!DOCTYPE html>
<html lang="en">

<head>

        <link rel="stylesheet" href="css/io_templates.css">
        {{-- <style>
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
        </style> --}}
</head>
<body>
   @php
       $i=0;
   @endphp                 
    <div class="box">  
        <div class="box-body">
                        {{-- <div class="box-header with-border" style="margin-left:0px">
                                        <div class="col" style="width:100px;height:100px;margin-left:0px">
                                       
                                            <img src="./images/logo.jpg"  class="logopp" style="width:80px; ">
                                                
                                        </div>
                                       <div  style="margin-left:140px;text-align:center;width:400px">
                                        <h3><u>	PURCHASE REQUISITION	
                                        </u></h3>
                                       
                                        </div>
                                        <div  style="margin-left:140px;text-align:right;width:600px">
                                              
                                                <p>
                                                        {{$return[0]->indent_num}}
                                                </p>
                                               
                                                </div>
                                       
                                </div> --}}
                                <div class="box-header with-border">
                                    <div class="col" style="width:200px;height: 40px; ">
                                   
                                        <img src="./images/logo.jpg"  class="logopp" style="width:80px;">
                                            
                                    </div>
                                   <div  style="float: right; margin-right: 10px;text-align: right;">
                                    <p>&nbsp;</p><p style="margin-left:10px;text-align:center;width:260px"><b><u>	PURCHASE REQUISITION	</u></b></p>
                                    {{$return[0]->indent_num}}
                                    </div>
                                    
                            </div>
                                <table class="tablestyle1" style="width:100%;">
                                        <tr>
                                            <th style="width:50%">
                                                Date
                                            </th>
                                         
                                            <td>
                                              
                                                    @php
                                                    $dateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata')); 
                                                    echo $dateTime->format("d/m/y  H:i A"); 
                                                @endphp
                                            </td>
                                           
                                        </tr>
                                </table><br>
                                <div class="row">
                                        <div class="col-md-12">
                                           <center><h4 style="text-align:center">ITEM DETAILS</h4></center>
                                        </div>
                                    </div>
                                <table class="tablestyle1" style="width:100%;">
                                   
                                    <tr>
                                        <th>Item Category	</th>
                                        <td>{{$return[0]->item_category}}</td>
                                    </tr>
                                    <tr>
                                        <th>Item Name	</th>
                                        <td>{{$return[0]->item_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Item Qty Required	</th>
                                        <td>{{$return[0]->item_qty}}</td>
                                    </tr>
                                    <tr>
                                        <th>Item Required by Date	</th>
                                        <td>{{$return[0]->item_req_date}}</td>
                                    </tr>
                                    
                                </table>
        </div>
        <!-- /.box-body -->
    
    </div>
<br>
<div class="box">  
    <div class="box-body">
                 <div class="row">
                     <div class="col-md-12">
                        <center><h4 style="text-align:center">P.R. DETAIL</h4></center>
                     </div>
                 </div>
                            <table class="tablestyle1" style="width:100%;">
                                <tr>
                                    <th>
                                        <p>Sr. No.</p>
                                    </th>
                                    <th>
                                        <p>P.R. Number </p>
                                    </th>
                                    <th>
                                        <p>Quantity</p>
                                    </th>
                                   
                                   
                                </tr>
                                
                                    @foreach ($return as $item)
                                    @php
                                        $i++;
                                    @endphp
                                    <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$item->purchase_req_number}}</td>
                                    <td>{{$item->qty}}</td>
                                </tr>
                                    @endforeach
                                   
                                   
                               
                                
                            </table><br><br>

                            
    </div>
    <!-- /.box-body -->

</div>


        <!-- /.box -->
      <br>
    
        <div class="box">
                <div class="box-body">
                        <table class="noBorder" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; border: none; width:100%;border:none">
                            
                            <tr style="border: none;">
                                       
                                        <th style="text-align:right;border: none;">Store In-Charge
                                        </th>
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