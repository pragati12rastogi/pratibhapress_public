
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
                    
    <div class="box">  
        <div class="box-body">
                        <div class="box-header with-border" style="margin-left:0px">
                                        <div class="col" style="width:100px;height:100px;margin-left:0px">
                                       
                                            <img src="./images/logo.jpg"  class="logopp" style="width:80px; ">
                                                
                                        </div>
                                       <div  style="margin-left:140px;text-align:center;width:400px">
                                        <h3><u>PURCHASE RETURN REQUEST
                                        </u></h3>
                                       
                                        </div>
                                        <div  style="margin-left:140px;text-align:right;width:600px">
                                              
                                                <p>
                                                        {{$return->return_number}}
                                                </p>
                                               
                                                </div>
                                       
                                </div>
                                <table class="tablestyle1" style="width:100%;">
                                    <tr>
                                        <th >
                                            Date
                                        </th>
                                        <td>
                                          
                                          {{$return->date}}
                                        </td>
                                       
                                    </tr>
                                    <tr>
                                        <th>Return Approved by</th>
                                        <td>{{$return->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>PO Number</th>
                                        <td>{{$return->po_num}}</td>
                                    </tr>
                                    <tr>
                                        <th>PO Date</th>
                                        <td>{{$return->po_date}}</td>
                                    </tr>
                                    <tr>
                                        <th>GRN Number</th>
                                        <td>{{$return->grn_number}}</td>
                                    </tr>
                                    <tr>
                                        <th>GRN Date</th>
                                        <td>{{$return->grn_date}}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier’s Name</th>
                                        <td>{{$return->supp_name}}</td>
                                    </tr>
                                </table><br>
        </div>
        <!-- /.box-body -->
    
    </div>
<br>
<div class="box">  
    <div class="box-body">
                 <div class="row">
                     <div class="col-md-12">
                        <center><h4 style="text-align:center">ITEM DETAILS</h4></center>
                     </div>
                 </div>
                            <table class="tablestyle1" style="width:100%;">
                                <tr>
                                    <th>
                                        <p>Item Description</p>
                                    </th>
                                    <th>
                                        <p>Quantity Received</p>
                                    </th>
                                    <th>
                                        <p>Quantity Returned</p>
                                    </th>
                                    <th>
                                        <p>Quantity Unit</p>
                                    </th>
                                   
                                </tr>
                                <tr>
                                    <td style="height:100px;text-align: center">{{$return->item_desc}}</td>
                                    <td style="height:100px;text-align: center">{{$return->item_qty_received}}</td>
                                    <td style="height:100px;text-align: center">{{$return->item_qty_returned}}</td>
                                    <td style="height:100px;text-align: center">{{$return->uom_name}}</td>
                                </tr>
                                <tr></tr>
                                <tr></tr>
                                <tr></tr>
                                <tr></tr>
                                <tr></tr>
                                <tr></tr>
                               
                            </table><br><br><br><br>

                            <table class="tablestyle1" style="width:100%;">
                                <tr>
                                    <th>
                                        <p>Reason for Return</p>
                                    </th>
                                    <td colspan="10">
                                      
                                            {{$return->reason}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Revised Payment Terms/Deductions</th>
                                    <td  colspan="10">{{$return->payment_desc}}</td>
                                </tr>
                               
                            </table><br>
    </div>
    <!-- /.box-body -->

</div>


        <!-- /.box -->
      <br>
        <!-- /.box --> <div class="box">
                <div class="box-body">
                        <table class="noBorder" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; border: none; width:100%;border:none">
                            <tr style="border: none;">
                                    <td style="border: none;"><u>Approved:</u></td>
                            </tr>   <br> 
                            
                        </table>
                </div>
                <!-- /.box-body -->

        </div><br>
        <div class="box">
                <div class="box-body">
                        <table class="noBorder" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; border: none; width:100%;border:none">
                            
                            <tr style="border: none;">
                                        <th style="border: none;">Purchase Head</th>
                                        <th style="text-align:right;border: none;">Managing Director
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