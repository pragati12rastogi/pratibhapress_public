
<!DOCTYPE html>
<html lang="en">

<head>

        <link rel="stylesheet" href="css/io_templates.css">
        <style>
        .noBorder {
    border:none !important;
}
        </style>
</head>
<body>
                    
        <div class="box">  
                <div class="box-body">
                                <div class="box-header with-border">
                                               
                                                <div class="col" style="width:100px;height: 90px;">
                                               
                                                    <img src="./images/logo.jpg"  class="logopp" style="width:80px;">
                                                </div>
                                                <div style="display:inline-block;text-align:center;margin-right:100px">
                                                   <b>Internal Order</b>
                                                </div>
                                               <div style="display:inline-block;text-align:right;">
                                                 @php
                                                    if(isset($internal['io_number'])){echo $internal['io_number']."<br>";}
                                                        else{echo "-";}
                                                       echo "Status: ".$internal['status'];
                                                @endphp
                                            
                                                </div>
                                                
                                        </div>
                        <table class="tablestyle" style="width:100%;">
                                <tr>
                                        <td>
                                                <h4> Date </h4>
                                        </td>
                                        <td>
                                                <p id="small">{{$internal['created_time']}}</p>
                                        </td>
                                        <td>
                                                <h4 id="book"> PO Date </h4>
                                        </td>
                                        <td>
                                                <p id="small"></p>
                                        </td>
                                        <td>
                                                <h4 id="book"> PO Number </h4>
                                        </td>
                                        <td colspan="2"> </td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4> Reference Name </h4>
                                        </td>
                                        <td>
                                                <p>{{$internal['partyname']}}</p>
                                        </td>
                                        <td>
                                                <h4> Marketing Person </h4>
                                        </td>
                                        <td colspan="5">
                                                <p>{{$internal['marketing_name']}}</p>
                                        </td>
                                </tr>
                                <!-- <tr>
                                        <td>
                                                <h4>Party Address</h4>
                                        </td>
                                        <td colspan="6">
                                                <p id="small">{{$internal['address']." ".$internal['city']." ".$internal['states']." ".$internal['country']." ".$internal['pincode']}}</p>
                                        </td>
                                </tr> -->
                                <tr>
                                        <td colspan="1">
                                                <h4>Job Name</h4>
                                        </td>
                                        <td colspan="2">
                                        @php
                                        if(isset($internal['item_category'])){echo $internal['item_category'];}
                                                        else{echo "-";}
                                                        
                                if($internal['other_item_name']){echo " : ".$internal['other_item_name'];}
                                                
                        
                                @endphp
                                                
                                        </td>
                                        <td colspan="1">
                                                <h4 id="book">IO Type</h4>
                                        </td>
                                        <td colspan="3">
                                                <p id="small">{{$internal['io_type']}}</p>
                                        </td>
                                </tr>
                                <tr>
                                        <td colspan="1">
                                                <h4>Job Date</h4>
                                        </td>
                                        <td colspan="2">
                                                <p id="small">{{$internal['job_date']}}</p>
                                        </td>
                                        <td colspan="1">
                                                <h4 id="book">Delivery Date</h4>
                                        </td>
                                        <td colspan="3">
                                                <p id="small">{{$internal['delivery_date']}}</p>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4>Final Job Size</h4>
                                        </td>
                                        <td colspan="6">
                                                <p id="small">{{$internal['job_size'].' '.$internal['dimension']}}</p>
                                        </td>
                                </tr>
                        </table>
                </div>
                <!-- /.box-body -->

        </div>
        <div class="box-header with-border">
                <h3 class="head">Job Details </h3>
        </div>
        <table class="table1" style="width:100%;height:100%;">
                <tr>
                        <th>Quantity</th>
                        <th>HSN/SAC</th>
                        <th>Unit</th>
                        <th>Job Details</th>
                        <th>Rate</th>
                        <th>Amount</th>
                </tr>
                @php
                            $tax=$internal['gst'];
                            $rate=$internal['rate_per_qty'];
                            $qty=$internal['qty'];
                            $tax_applicable=($tax*$rate*$qty)/100;
                            $amount=$rate*$qty;
                            $total=$tax_applicable+$amount;
                        @endphp
                <tr>
                        <td>{{$internal['qty']}}</td>
                <td>{{$internal['hsn_name']}}</td>
                        <td>{{$internal['uom_name']}}</td>
                        <td style="height:150px"><p>Front Color:{{$internal['front_color']}}&nbsp;&nbsp;&nbsp;Back Color:{{$internal['back_color']}}</p>
                        {{$internal['details']}}</td>
                        <td>{{round($internal['rate_per_qty'],2)}}</td>
                <td id="operation">{{round($amount)}}</td>
                </tr>
                <tr>
                        <td colspan="4">
                                <h4>Tax % Applicable</h4>
                        </td>

                        <td colspan="1">
                                <h4 id="book">{{$internal['gst'].'%'}}</h4>
                        </td>
                        <td colspan="1">
                        <h4 id="small taxPer">
                {{round($tax_applicable)}}        
                </h4>
                        </td>
                </tr>
                <tr>
                        <td colspan="5">
                                <h4>Total</h4>
                        </td>
                        
                        <td colspan="1">
                                <h4 id="small totalper">{{round($total)}}</h4>
                        </td>
                </tr>
        </table>

        <!-- /.box -->
        <div class="box">
                <div class="box-header with-border">
                        
                </div>
                <div class="box-body">
                        <table class="tablestyle" style="width:100%;">

                                <tr>
                                        <td>
                                                <h4> Paper Supplied </h4>
                                        </td>
                                        <td colspan="6">
                                                <p>{{$internal['is_supplied_paper']}}</p>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4>Plate Supplied</h4>
                                        </td>
                                        <td colspan="6">
                                                <p id="small">{{$internal['is_supplied_plate']}}</p>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4>Transporation</h4>
                                        </td>
                                        <td colspan="6">
                                                <p id="small">{{$internal['transportation_charge']}}</p>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4>Other Charges</h4>
                                        </td>
                                        <td colspan="6">
                                                <p id="small">{{$internal['other_charge']}}</p>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4>Payment Terms</h4>
                                        </td>
                                        <td colspan="6">
                                                <p id="small">{{$internal['value']}}</p>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <h4>Remarks</h4>
                                        </td>
                                        <td colspan="6">
                                                <p id="small">{{$internal['remarks']}}</p>
                                        </td>
                                </tr>
                        </table>
                </div>
                <!-- /.box-body -->

        </div>
        <!-- /.box -->
        <div class="box">
                <div class="box-header with-border">
                        <h3 class="head">Advance Detail</h3>
                </div>
                <div class="box-body">
                        <table class="tablestyle" style="width:100%;">

                                <tr>
                                        <td colspan="1">
                                                <h4>Amount</h4>
                                        </td>
                                        <td colspan="2">
                                                <p id="small">
                                                @if ($internal['amount']==null)
                                                {{'-'}}
                                                @else
                                                {{round($internal['amount'],2)}}
                                                @endif
                                                </p>
                                        </td>
                                        <td colspan="1">
                                                <h4 id="book">Mode</h4>
                                        </td>
                                        <td colspan="2">
                                                <p id="small">
                                                @php
                                                if(isset($internal['mode_of_receive'])){
                                                        if($internal['mode_of_receive']==0){
                                                        echo "Cash";
                                                   }
                                                   if($internal['mode_of_receive']==1){
                                                        echo "Cheque";
                                                   }
                                                   if($internal['mode_of_receive']==2){
                                                        echo "RTGS";
                                                   }
                                                }
                                                 
                                                else{
                                                        echo "-";
                                                   }

                                                @endphp
                                                
                                                </p>
                                        </td>
                                        <td colspan="1">
                                                <h4 id="book">Date</h4>
                                        </td>
                                        <td colspan="2">
                                                <p id="small">@if ($internal['amount_received_date']==null)
                                                                {{'-'}}
                                                    
                                                @else
                                                  {{$internal['amount_received_date']}}  
                                                @endif</p>
                                        </td>
                                </tr>



                        </table>
                        <table class="noBorder" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; border: none; width:100%;border:none">
                                <tr style="border: none;">
                                        <th style="border: none;">Created By:</th>
                                        <th style="text-align:right;border: none;">Approved By:</th>
                                </tr>
                                <tr style="border: none;">
                                <td style="border: none;">{{$created['name']}}</td>
                                        <td style="border: none;"></td>
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


<htmlpagefooter name="page-footer">
	Page {PAGENO} of {nb}
</htmlpagefooter>

</body>

</html>