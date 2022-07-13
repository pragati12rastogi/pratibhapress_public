<!DOCTYPE html>
<html lang="en">

<head>
    <style>        
        *{
            margin: 0px;
            padding: 0px;
            font-size: 8px;
        }
        .body{
            margin: 0px;
            padding: 0px;
            
        }
        .tablestyle {
            border: 3px black;
            font-family: 'Arial Narrow', Arial, sans-serif;
        }
        .tablestyle td {
            border: 1px solid;
        }
        .tablestyle td h5 {
            margin-bottom: 0;
        }
        .colcenter {
            text-align: center;
        }
        .partyaddress{
            width: 100%;
        }
        tr:hover {background-color: #f5f5f5;}
        .box-title{
            text-align: right;
        }
        table {
            border-collapse: collapse;
        }
        
        table, td, th {
            text-align: left;
            border: 1px solid black;
            padding: 5px;
            font-size: 12px;
        }
        body {
            padding: 1rem;
        }
        .foot{
            text-align: right;
            margin-top: 0px;
        }
        .head{
            text-align: center;
        }
        .tablestyle {
            border: 2px solid;
            font-family: 'Arial Narrow', Arial, sans-serif;
        }
        .tablestyle td {
            border: 1px solid;
        }
        .tablestyle td h5 {
            margin-bottom: 0;
        }
        .colcenter {
            text-align: center;
        }
        .cen{
            text-align: center;
        }
        .lft{
            display: inline-block;
        }
        .col {
    float: left; 
    position: relative;
    
}
.page-break {
                    page-break-inside: avoid;
            }
            @page {
	footer: page-footer;
}
    </style>
</head>

<body>
@php
     $counter=0;
@endphp
    <div class="box">
            <div class="box-header with-border">
                    <div class="col" style="width:200px;height: 40px; ">
                   
                        <img src="./images/logo.jpg"  class="logopp" style="width:80px;">
                            
                    </div>
                   <div  style="float: right; margin-right: 10px;text-align: right;">
                    <p>&nbsp;</p>
                     @php
                        if(isset( $dc[0]['challan_number'])){echo $dc[0]['challan_number']."<br>";}
                            else{echo "-";}
                        //    echo "Status: ".$job['status'];
                    @endphp
                
                    </div>
                    
            </div>
        <div class="box-header with-border" style="margin-top: 0px">
            <h3 class="head">DELIVERY CHALLAN </h3>

        </div>
        <div class="box-body">
            <table class="tablestyle page-break" style="width:100%;">
                <tr>
                    <td colspan="4">
                        <h5>Pratibha Press & Multimedia Pvt.Ltd</h5>
                    </td>
                    <td colspan="1">
                        <p id="small"><h5>Delivery Challan No</h5></p>
                    </td>
                    <td colspan="4">
                        <p id="small">{{$dc[0]['challan_number']}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" rowspan="4">
                      
                        <p>Gate No.2, 6-Ashok Nagar(Near Bans Mandi Churaha),Latouche Road, Lucknow-226018</p>
                        <p>Ph:0522-2630064, 4000686</p>
                        <p>GSTIN   : 09AAFCP7858P1ZD</p>
                        <P>Pan No. :AAFCP7858P</P>
                    </td>
                    <td colspan="1"> <h5>Date </h5></td>
                    <td colspan="5">{{date('d-m-Y',strtotime($dc[0]['delivery_date']))}}</td>
                   
                </tr>
                <tr> <td colspan="1"> <h5>Created By </h5></td>
                    <td colspan="4">{{$party['created_by_name']}}</td></tr>
                <tr>
                        <td colspan="1"><h5>Internal Order Number</h5></td>
                        <td colspan="1" style="border:none"><h5> Internal Order Date</h5> </td>
                       
                    </tr>
                    <tr>
                            <td colspan="1">@php
                                    foreach ($dc as $item) {
                                          $arr[]=$item['io_number'];
                                      }
                                      $unique_data = array_unique($arr);
                                       // now use foreach loop on unique data
                                       foreach($unique_data as $val) {
                                           echo $val.'<br>';
                                       }
                               @endphp</td>
                       
                        <td colspan="4">@php
                            foreach($dc as $item){
                              
                               echo $item['io_time'].'<br>';
                            }
                        @endphp</td>
                    </tr>
                    <tr style="width:100%;">
                            <td colspan="4"><h5> Consignee </h5></td>
                            <td colspan="4"><h5>Buyer</h5> </td>
                        </tr>
        
                        <tr>
                            <td colspan="4">
                               
                                <p>{{explode('-',$party['cname'])[0]}}</p><br>
                                <p>{{$party['caddr']}}</p><br>
                                <p>{{$party['ccity']}}, {{$party['cstate']}} - {{$party['cpcode']}}</p><br>
                                <b>States</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$party['cstate']}}<br>
                                <b>GSTIN/UIN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$party['cgst']}}
                                <br>
                                <b>PAN/IT</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$party['cpan']}}
                            </td>
                            <td colspan="4">
                               
                                <p>{{explode('-',$party['pname'])[0]}}</p><br>
                               
                                <p>{{$party['paddr']}}</p><br>
                                <p>{{$party['pcity']}}, {{$party['pstate']}} - {{$party['ppcode']}}</p><br>
                                <b>States</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$party['pstate']}}<br>
                                <b>GSTIN/UIN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                @if ($party['gst_pointer']==0)
                                {{"NA"}}
                                @elseif ($party['gst_pointer']==1)
                                {{$party['pgst']}}
                                @else
                                {{$party['pgst']}}                                
                                @endif<br>
                                <b>PAN/IT</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$party['ppan']}}
                            </td>
                        </tr>

            </table>
     
           
        </div>
        <!-- /.box-body -->

    </div>
 
    <div class="box-header with-border">

    </div><br>
    <table class="table1 page-break" style="width:100%;">
        <tr>
            <th style="text-align:center;width:300px">Good Description</th>
            <th style="text-align:center">HSN/SAC</th>
            <th style="text-align:center">Quantity</th>
            <th style="text-align:center">Rate</th>
            <th style="text-align:center">Per</th>
            <th style="text-align:center">GST %</th>
            <th style="text-align:center">Amount Taxable Value</th>
        </tr>
        @foreach($dc as $vl)
        @php
            $counter++;
        @endphp
        <tr style="border:none">
            
            <td class="cen">{{$vl['good_desc']}}</td>
            <td class="cen">{{$vl['hsn']}}</td>
            <td class="cen">{{$vl['good_qty']}}</td>
            <td class="cen">{{round($vl['challan_rate'],2)}}</td>
            <td class="cen">{{$vl['uom_name']}}</td>
            <td class="cen">{{$vl['gst']}}</td>
            <td class="cen">
                    @php
                                
                    $rate=$vl['challan_rate'];
                    $qty=$vl['good_qty'];
                    $gst=$vl['gst'];
                    $amount=$rate*$qty;
                    $amount_gst=$amount+ (($amount*$gst)/100);
                    $amount_gst=round($amount_gst);
                     @endphp
                    {{number_format($amount_gst).".00"}}    

            </td>

        </tr>
        @endforeach


    </table>
    <h5 class="">Packing Details</h5>
    <table class="table table-bordered table-striped box page-break" style="width:100%">
           
            <tr> 
                    <th style="text-align:center">Good Description</th>
                    <th style="text-align:center">Packing Details</th>
                    
                </tr>
                @foreach($dc as $vl)
                @php
                    $counter++;
                @endphp
                <tr style="border:none">
                    
                    <td class="cen">
                           
                           {{$vl['good_desc']}}
                       </td>
                    <td class="cen">{{$vl['packing_details']}}
                            </td>
                </tr>
                @endforeach
</table>
    
    <div class="box page-break" style="margin-top: 0px">
        <div class="box-header with-border">
            <h5 class="">Goods Dispatch Details</h5>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" style="width:100%">
                    <tr style="width:100%;">
                        <th colspan="3"> Goods Dispatch mode :</th>
                        <td colspan="8">{{$party['mode']}}</td>
                    </tr>
                    <tr style="width:100%;">
                        <th colspan="3"> Carrier's Name :</th>
                        <td colspan="8">
                            @foreach ($goods_dispatch as $cn)
                            {!! $cn['courier_name']."<br>" !!}
                            @endforeach
                        </td>
                    </tr>
                    @if($party['mode']!="Self")
                    <tr style="width:100%;">
                        <th colspan="3">Transporter/Courier Company Address :</th>
                        <td colspan="8">{{$party['address']}}</td>
                    </tr>
                    @endif
                    @if($party['mode']=="Self")
                    <tr style="width:100%;">
                        <th colspan="3"> Vehicle Number :</th>
                        <td colspan="8">{{$party['vehicle']}}</td>
                    </tr>
                    @else
                    <tr style="width:100%;">
                        <th colspan="3"> Bilty/Docket Number :</th>
                        <td colspan="8">{{$dc[0]['bilty_docket']}}</td>
                    </tr>
                    <tr style="width:100%;">
                        <th colspan="3"> Bilty/Docket Date :</th>
                        <td colspan="8">{{CustomHelpers::showDate($dc[0]['docket_date'],'d-m-Y')}}</td>
                    </tr>
                    <tr style="width:100%;">
                        <th colspan="3"> Transporter GSTIN Number :</th>
                        <td colspan="8">{{$party['dp_gst']}}</td>
                    </tr>
                    @endif
        </table><br> 
        <table class="noBorder page-break" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; border: none; width:100%;border:none">
            <tr style="border: none;"><th colspan="2" style="border: none;font-size:10px"><i>Received the above items in full quantity and in sound <br> condition according to our order and instructions</i></th><td colspan="4" style="border: none;"></td></tr>
            <tr style="border: none;">
                    <th style="border: none;">Receiver Name:</th>
                   
                    <th style="text-align:right;border: none;">For Pratibha Press & Multimedia Pvt.Ltd</th>
            </tr>
            <tr style="border: none;">
                
          <th style="border: none;">Receiving Date:</th>
            </tr>
    </table>
        </div>
        <!-- /.box-body -->

    </div>
    <!-- /.box -->


   



    <htmlpagefooter name="page-footer">
        Page {PAGENO} of {nb}
    </htmlpagefooter>


</body>

</html>