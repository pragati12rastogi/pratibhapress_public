
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
                                        <h3><u>PURCHASE ORDER
                                        </u></h3>
                                       
                                        </div>
                                        <div  style="margin-left:140px;text-align:right;width:600px">
                                              
                                                <p>
                                                        {{$order->po_num}}
                                                </p>
                                               
                                                </div>
                                       
                                </div>
                                <table class="tablestyle1" style="width:100%;">
                                    <tr>
                                        <th >
                                            Date
                                        </th>
                                        <td>
                                          
                                                {{$order->po_date}}
                                        </td>
                                       
                                    </tr>
                                    <tr>
                                        <th>Indent Number</th>
                                        <td> {{$order->indent_num}}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier Name</th>
                                        <td> {{$order->vendor_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier Address</th>
                                        <td> {{$order->address}}</td>
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
                                        <p>Item Category</p>
                                    </th>
                                    <th>
                                        <p>Item Name</p>
                                    </th>
                                    <th>
                                        <p>Quantity</p>
                                    </th>
                                    <th>
                                        <p>UOM</p>
                                    </th>
                                    <th>
                                            <p>Rate</p>
                                        </th>
                                        <th>
                                                <p>Tax%</p>
                                            </th>
                                            <th>
                                                    <p>Rate With Tax</p>
                                            </th>
                                            <th>
                                                    <p>Delivery Date</p>
                                                </th>
                                   
                                </tr>
                                @foreach ($order_detail as $item)
                                <tr>
                                <td>{{$item->sub_cat}}</td>
                                <td>{{$item->item_name}}</td>
                                <td>{{$item->item_qty}}</td>

                                <td>{{$item->uom_name}}</td>
                                <td>{{$item->item_rate}}</td>
                                <td>{{$item->tax_value}}</td>
                                <td>
                                    @php
                                        $rate=$item->item_rate;
                                        $tax=$item->tax_value;
                                        $new_rate=$rate+(($rate*$tax)/100);
                                        echo $new_rate;
                                    @endphp
                                </td>
                                <td>{{$item->delivery_date}}</td>
                            </tr>
                                @endforeach
                               
                               
                            </table><br><br><br><br>

                            <table class="tablestyle1" style="width:100%;">
                                <tr>
                                    <th>
                                        <p>Payment Terms</p>
                                    </th>
                                    <td colspan="10">
                                      
                                          {{$order->py_value}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Remarks</th>
                                    <td  colspan="10">{{$order->remark}}</td>
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