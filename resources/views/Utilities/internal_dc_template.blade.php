
<!DOCTYPE html>
<html lang="en">

<head>

        <link rel="stylesheet" href="css/io_templates.css">
        <style>
       table tr td{
           font-size: 12px;
       }
       th{
        font-size: 12px;
       }
        </style>
</head>
<body>
    @foreach ($idc as $item)
                                    
    @endforeach 
    
    @php
     $total=0;
     $total_amt=0;
     $counter=0;
@endphp
    <div class="box">  
        <div class="box-body">
                        <div class="box-header with-border" style="margin-left:0px">
                                        <div class="col" style="width:100px;height:40px;margin-left:0px">
                                       
                                            <img src="./images/logo.jpg"  class="logopp" style="width:80px; ">
                                                
                                        </div>
                                       <div  style="margin-left:140px;margin-bottom:0px;text-align:center;width:400px">
                                        <h3><u>INTERNAL DELIVERY CHALLAN		
                                        </u></h3>
                                    @if ($item->for=="Outsource Order")
                                    <h2><u>{{$item->for}}</u></h2>
                                    @endif
                                       
                                        </div>
                                   <br>
                                       
                                </div>
                                
                                <table class="tablestyle page-break" style="width:100%;">
                                    <tr>
                                        <td colspan="1">
                                            <h5>Pratibha Press & Multimedia Pvt.Ltd</h5>
                                        </td>
                                        <td colspan="2">
                                            <p id="small"><h5>IDC No.</h5></p>
                                        </td>
                                        <td colspan="6">
                                            <p id="small">{{$item->idc_number}}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="1" rowspan="4">
                                          
                                            <p>Gate No.2, 6-Ashok Nagar(Near Bans Mandi Churaha),</p>
                                            <p>Latouche Road, Lucknow-226018</p>
                                            <p>Ph:0522-2630064, 4000686</p>
                                            <p>GSTIN   : 09AAFCP7858P1ZD</p>
                                            <P>Pan No. :AAFCP7858P</P>
                                        </td>
                                        <td colspan="2"> <h5>Date </h5></td>
                                        <td colspan="6">{{date("d/m/Y", strtotime($item->date))}}</td>
                                       
                                    </tr>
                                
                                        <tr>
                                                <th colspan="2">
                                                    Dispatch to:
                                                </th>
                                           
                                            <td colspan="6">{{$item->dispatch_to}}</td>
                                        </tr>
                                       
                    
                                </table><br>
        </div>
        <!-- /.box-body -->
    
    </div>
<div class="box">  
    <div class="box-body">
                 <div class="row">
                     <div class="col-md-12">
                        <h4>ITEM DETAILS</h4>
                     </div>
                 </div>
                 @if ($item->for=="Other")
                            <table class="tablestyle1" style="width:100%;">
                                <tr>
                                    <th>
                                        <p>Item Description</p>
                                    </th>
                                    <th>
                                        <p>Quantity</p>
                                    </th>
                                    <th>
                                        <p>Unit Of Quantity</p>
                                    </th>
                                    <th>
                                        <p>Packing Detail</p>
                                    </th>
                                    <th>
                                            <p>Reason For Dispatch		</p>
                                        </th>
                                   
                                </tr>
                               <tr>
                                   <td style="height:250px;width:300px">{{$item->item_desc}}</td>
                                   <td>{{$item->item_qty}}</td>
                                   <td>{{$item->uom_name}}</td>
                                   <td>{{$item->packing_desc}}</td>
                                   <td>{{$item->reason}}</td>
                               </tr>
                            </table><br>
                 @endif
                            @if ($item->for=="Outsource Order")
                            <table class="tablestyle1" style="width:100%;">
                                <tr>
                                    <th>
                                        <p>Item Description</p>
                                    </th>
                                    <th>
                                        <p>Quantity</p>
                                    </th>
                                    <th>
                                        <p>Unit Of Quantity</p>
                                    </th>
                                    <th>
                                        <p>HSN/SAC</p>
                                    </th>
                                    <th>
                                        <p>Rate</p>
                                    </th>
                                    <th>
                                        <p>Gst %</p>
                                    </th>
                                    <th>
                                        <p>Value</p>
                                    </th>
                                   
                                </tr>
                               <tr>
                                   <td style="height:150px;width:300px">{{$item->item_desc}}</td>
                                   <td>{{$item->item_qty}}</td>
                                   <td>{{$item->uom_name}}</td>
                                   <td>{{$item->hs}}</td>
                                   <td>{{round($item->rate)}}</td>
                                   <td>{{$item->gst_rate}}</td>
                                   <td>
                                        @php
                                        
                                        $rate=$item['rate'];
                                        $qty=$item['item_qty'];
                                        $gst=$item['gst_rate'];
                                        $amount=$rate*$qty;
                                        $amount_gst=$amount*$gst/100;
                                        $total_amt=$amount+$amount_gst;
                                         @endphp
                                        {{round($total_amt)}}    
                                   </td>
                                 
                                  
                               </tr>
                              <tr>
                                  <td><b>Amount Chargeable (in words)</b></td>
                                 
                                        <td colspan="6">
                                               <p>
                                                   <b>
                                                        @php
                                                        echo app('App\Http\Controllers\Template')->tax_words($total_amt);
                                                    @endphp
                                                   </b>
                                               </p>
                                            </td>
                                 
                              </tr>
                            </table>
                            <h4>PACKING DETAILS</h4>
                            <table style="width:100%;">
                                    <tr>
                                            <th style="width:50%">
                                                   
                                                            <p>Packing Detail</p>
 
                                            </th>
                                            <td>{{$item->packing_desc}}</td>
                                           
                                        </tr>
                                        <tr>
                                            <th style="width:50%"> <p>Reason For Dispatch</p></th>
                                            <td>{{$item->reason}}</td>
                                        </tr>
                            </table>
                            @endif
                            <h4>ITEM DISPATCH DETAILS</h4>
                            <table class="tablestyle1" style="width:100%;">
                                <tr>
                                    <th style="width:50%">
                                        <p>Dispatch mode</p>
                                    </th>
                                    <td>
                                      {{$item->mode}}
                                         
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:50%">Carrier's Name</th>
                                    @php
                                        $name=explode(',',$item->name);
                                        $name1=implode('/',$name);
                                    @endphp
                                <td>{{$name1}}</td>
                                </tr>
                               
                            </table><br>
    </div>
    <!-- /.box-body -->

</div>


        <!-- /.box -->
      <br>
        <!-- /.box --> <div class="box">
                <div class="box-body" style="text-align:right">
                       <p style="float:right;width:100%"><u>For Pratibha Press & Multimedia Pvt. Ltd.</u></p><br>
                       <p><u>Signature:</u></p>
                </div>
                <!-- /.box-body -->

        </div><br>
   



</body>

</html>