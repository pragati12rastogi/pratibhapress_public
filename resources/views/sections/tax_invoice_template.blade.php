@php
    use \App\Http\Controllers\Template;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/tax_invoice_template.css">
    <style>
            .page-break {
                    page-break-inside: avoid;       
            }
            @page {
                margin-top:180px;
            }
            body {
                font-family: DejaVu Sans;
            }
            </style>
            <script>
                var pqr = {{count($print_type)}};
                var pq = document.getElementsByClassName('page-footer1');
                console.log(pq);
            </script>
</head>

<body>
    @php
               foreach ($tax_detail as $item) {
                               $arr[]=$item['challan_number'];
                               $arrs[]=$item['io_number'];
                           }
                           $unique_data = array_unique($arr);
                           $unique_datas = array_unique($arrs);
                            // now use foreach loop on unique data
                            foreach($unique_data as $val) {
                                 $arr1[]=$val;
                            }
                               
                                $value=implode(',',$arr1);
    @endphp
@for($ptype_i=0;$ptype_i<count($print_type);$ptype_i++)
{{-- @foreach ($print_type as $typ1)     --}}

@php
     $total=0;
     $total_amt=0;
     $counter=0;
@endphp
    <div class="box">
        <div class="box-body">
                
            <table style="width:100%;">
                <tr>
                    {{-- <td colspan="2" style="border-right:none"></td> --}}
                    <th colspan="4" style="text-align:center;border-left:none;border-right:none"><h3 class="head">TAX INVOICE </h3></th>
                    <th colspan="8" style="text-align:center;border-right:none"><h3 class="head">{{$print_type_option[$print_type[$ptype_i]]}}</h3></th></tr>


                    <tr>

                   
                        <td colspan="2"><b>Tax invoice no.</b></td>
                        <td colspan="2"> <b>Tax invoice Date</b> </td>
                        <td colspan="8"><b> Delivery Challan</b></td>
                        
                    </tr>
                <tr>
                 
                    <td colspan="2">
                            
                              {{$tax_detail[0]['invoice_number']}}
                           
                        </td>
                   
                    <td colspan="2">
                           
                                    {{$tax_detail[0]['date']}}   
                               
                        </td>
                   
                    @php
                        echo '<td colspan="8" style="fomt-size:12px">';
                                $x=",";
                                $count=count($arr1);
                                foreach ($arr1 as $key => $value) {
                                   if($key%5==0){
                                    echo $value."<br>";
                                   }
                                   else if($key==$count-1){
                                    echo $value;
                                   }
                                   else{
                                    echo $value.$x; 
                                   }
                                }
                            echo '</td>';    
                       @endphp 
                </tr>
            

            </table><br>
            <table class="page-break" style="width:100%">
                <tr>

                   
                    <td colspan="2"><b>Internal order</b></td>
                    <td colspan="2"> <b>P.O NO</b> </td>
                    <td colspan="2"><b> P.O Date </b></td>
                    <td colspan="4"> <b>Payment Term</b> </td>
                    
                </tr>
                <tr>
                   
                       @php
                        echo '<td colspan="2">';
                                foreach ($io as $item) {
                             echo $item.'<br>';
                                }
                           
                            // foreach ($tax_detail as $item) {
                            //     if($item['is_po_provided']==0){
                            //         echo $item['io_number'].'<br>';
                            //      }
                               
                                // }
                          echo '</td>';      
                       @endphp 
                   
                    @php
                    echo '<td colspan="2">';
                        // if (count($cpo)>0) {
                            foreach ($client_po_date as $item) {
                                foreach($item as $key=>$value){
                                     echo $key .'<br>';
                                }
                                   

                                 }
                            // }
                            // else{
                            //     echo '<p>Verbal!!</p>';
                            // }
                             
                           echo '</td>';      
                         @endphp
                     @php
                     echo '<td colspan="2">';
                        foreach ($client_po_date as $item) {
                            foreach($item as $key=>$value){
                                     echo $value .'<br>';
                                }
                                 }
                            echo '</td>';      
                          @endphp
                           
                   @php
                   echo '<td colspan="4"  rowspan="2">';
                            // foreach ($tax_detail as $item) {
                            //  echo $item['tax_payment'].'<br>';
                            //     }
                                foreach ($tax_detail as $item) {
                                          $arrss[]=$item['tax_payment'];
                                      }
                                      $unique_datass = array_unique($arrss);
                                       // now use foreach loop on unique data
                                       foreach($unique_datass as $valss) {
                                           echo $valss.'<br>';
                                       }
                          echo '</td>';      
                        @endphp
                   
                </tr>
            </table><br>
            <table class="page-break" style="width:100%;">
                    <tr style="width:100%;">
                            <td colspan="6" style="width:50%"> <b>Consignee</b> </td>
                            <td colspan="6" style="width:50%"><b>Buyer </b></td>
                        </tr>
    
                        <tr>
                            <td colspan="6">
                              <b>{{explode('-',$tax_detail[0]['con_name'])[0]}}</b><br>
                              <p>{{$tax_detail[0]['con_address']}} , {{$tax_detail[0]['con_cities']}} , {{$tax_detail[0]['con_states']}},{{$tax_detail[0]['con_country']}} , {{$tax_detail[0]['con_pincode']}}<br>
                              </p><br>
                              <b>States</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$tax_detail[0]['con_states']}}</b><br>
                              <b>GSTIN/UIN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$tax_detail[0]['consignee_gst']}}</b><br>
                              <b>PAN/IT</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$tax_detail[0]['consignee_pan']}}</b>
                            </td>
                            <td colspan="6">
                                    <b>{{explode('-',$tax_detail[0]['partyname'])[0]}}</b><br>
                                    <p>{{$tax_detail[0]['party_address']}} , {{$tax_detail[0]['city']}} , {{$tax_detail[0]['state']}},{{$tax_detail[0]['country']}} , {{$tax_detail[0]['pincode']}}<br>
                                    </p><br>
                                    <b>States</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$tax_detail[0]['state']}}</b><br>
                                    <b>GSTIN/UIN</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> 
                                        @if ($tax_detail[0]['gst_pointer']==0)
                                        {{"NA"}}
                                            @elseif ($tax_detail[0]['gst_pointer']==1)
                                            {{$tax_detail[0]['party_gst']}}
                                            @else
                                            {{$tax_detail[0]['party_gst']}}                                
                                            @endif  </b>
                                   
                                    <br>
                                    <b>PAN/IT</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$tax_detail[0]['party_pan']}}</b>
                               
                            </td>
                        </tr>
            </table><br>
           @php
                foreach($unique_datas as $val) {
                    ${'a'.$val} = 0;
                } 
                // print_r($a6040)  ;
           @endphp
            <table class="page-break table" style="width:100%">
                    
                    <tr style="width:100%">
                        
                        <th colspan="4">Description</th>
                        <th>HSN/SAC</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Per</th>
                        <th>GST %</th>
                        <th>Disc%</th>
                        <th colspan="4">Amount</th>
                    </tr>
                     
                    @php
                    $newArr=Array();
                    $qty=0;
                    $ios=$tax_detail[0]['io_number'];
                 
                   foreach ($tax_detail as $item)
                   {
                           $newArr[$item['io_number']]=$item;
                           ${'a'.$item['io_number']}=${'a'.$item['io_number']} + $item['qty'];
                           $newArr[$item['io_number']]['sum_qty']=${'a'.$item['io_number']};
                   }
                   
                   @endphp    
                          
                            @foreach ($newArr as $item)
                            @if ($item['sum_qty']!==0)
                            @php
                                $counter++;
                            @endphp
                            <tr style="border:none">
                                    
                                    <td colspan="4" style="font-size:13px" class="cen">
                                    @php
                                        echo nl2br($item['goods']);
                                    @endphp</td>
                                    <td  class="cen" >{{$item['hsn']}}</td>
                                    <td  class="cen" >{{$item['sum_qty']}}</td>
                                    <td  class="cen">{{round($item['rate'],2)}}</td>
                                    <td  class="cen">{{$item['uom_name']}}.</td>
                                    <td  class="cen">{{$item['hsn_gst']}}%</td>
                                    <td  class="cen">{{$item['discount']}}%</td>
                                    <td colspan="4" class="cen">
                                        @php
                                        
                                        $rate=$item['rate'];
                                        $qty=$item['sum_qty'];
                                        $discount=$item['discount'];
                                        $gst=$item['hsn_gst'];
                                        $amount=$rate*$qty;
                                        
                                        $discount_amt=$amount-(($amount*$discount)/100);
                                        $total=$total+round($discount_amt, 2);
                                        $amount_gst=$discount_amt*$gst/100;
                                        $total_amt=$total_amt+$amount_gst;
                                         @endphp
                                        {{number_format($discount_amt,2)}}    
                                    </td>
                                </tr>
                            @endif
                   @endforeach
                          
                   @php
                   if($counter<5){
                    $counter=$counter+1;
                   }
                   else{
                       $counter=$counter;
                   }
                       for($i=0;$i<$counter;$i++){
                           echo '<tr style="border:none"><td colspan="4" class="cen"></td><td  class="cen"></td><td  class="cen"></td><td  class="cen"></td><td  class="cen"></td><td  class="cen"></td><td  class="cen"></td><td colspan="4" class="cen"></td></tr>';
                       }
                   @endphp
                        
                    <tr>
                        <td colspan="4">
                            <h4 class="lft">E.&O.E</h4>
                        </td>
    
                        <td colspan="6">
                            <h4 id="book">Total  Sale Value Before GST</h4>
                        </td>
                        <td colspan="4">
                            <h4 id="small">@php
                                echo number_format($total,2);
                            @endphp</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" rowspan="3">
                            <p id="sma"> Amount Chargeable (in words) </p>
                        </td>
                        <td colspan="6"> Transportation Charges </td>
                    <td colspan="4">{{$tax_detail[0]['transportation_charge']}}</td>
                    </tr>
                    <tr>
                        <td colspan="6">Other Charges</td>
                        <td colspan="4">{{$tax_detail[0]['other_charge']}}</td>
                    </tr>
                   
                   
                    <tr>
                           
                            <td colspan="6">CGST</td>
                        @if ($tax_detail[0]['gst_type']=="CGST/SGST")
                        <td colspan="4">@php
                            echo number_format($total_amt/2,2) ;
                        @endphp</td>
                        @else 
                        <td colspan="4"></td>
                        @endif
                    
                    </tr>
                   
                    <tr>
                            <td colspan="4" rowspan="3" style="height:60px">
                                    @php
                                    $amt=$total;
                                $tran=$tax_detail[0]['transportation_charge'];
                                $other=$tax_detail[0]['other_charge'];
                                $gst_rate=$total_amt;
                                $total_amt_gst=$amt+$tran+$other+$total_amt;
                                    $number=round($total_amt_gst);
                                    echo app('App\Http\Controllers\Template')->tax_words($number);
                                    
                                    @endphp
                                    
                                </td>
                                <td colspan="6">SGST</td>
                                @if ($tax_detail[0]['gst_type']=="CGST/SGST")
                                <td colspan="4">@php
                                    echo number_format($total_amt/2,2) ;
                                @endphp</td>
                                 @else 
                                 <td colspan="4"></td>
                                @endif
                    </tr>
                    <tr>
                           
                

                    <td colspan="6">IGST</td>
                    @if ($tax_detail[0]['gst_type']=="IGST")
                    <td colspan="4">{{number_format($total_amt,2)}}</td>
                    @else 
                    <td colspan="4"></td>
                    @endif
                    </tr>
                    <tr>
                           
                     


                        <td colspan="6"><b>Total Amount After Tax</b></td>
                        <td colspan="4">@php
                                $amt=$total;
                                $tran=$tax_detail[0]['transportation_charge'];
                                $other=$tax_detail[0]['other_charge'];
                                $gst_rate=$total_amt;
                                $total_amt_gst=$amt+$tran+$other+$total_amt;
                                echo number_format($total_amt_gst,2);
                            @endphp</td>
                    </tr>
                    <tr>
                        <td colspan="4">
                                
                        </td>
    
                        <td colspan="6">
                            <h4 id="book">Final Amount after Tax(Round off)</h4>
                        </td>
                        <td colspan="4">
                            <h4 id="small">{{number_format($total_amt_gst).".00"}}  </h4>
                        </td>
                    </tr>
                   
            </table>
           <div class="row page-break">
               <div class="col-md-12">
                <table class="page-break" style="width:100%" class="page-break">
                    <tr>
                            <td colspan="7">
                                <h4 id="book">Company's GST: &nbsp; &nbsp;<b>09AAFCP7858P1ZD</b></h4>
                                <p><u> Declarations: </u></p>
                                <p> We declare that this invoice shows the actual price of the
                                goods described and that all particulars are true and correct.  </p>
                                
                            </td>
                            <td colspan="5">
                                <h4 id="book">Company's Bank Details</h4>
                                <p>Bank Name:&nbsp;&nbsp;<b>State Bank of India </p>
                                <p>A/C No:&nbsp;&nbsp;<b>32944831388</p>
                                <p>Branch & IFSC:&nbsp;&nbsp;<b>Aminabad, Lucknow & IFSC: SBIN0001526</p>
        
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align:justify">
                                <h4 class="head" style="padding-top:0px;">Customer's Seal Signature</h4>
                                <br><br><br><br><br>
                               
                            </td>
                            <td colspan="3">
                                <h4 id="book">Pre Authenticated by</h4>
                                <h4 id="book">Approving Authority</h4>
                                <p>Name:</p>
                                <p>Designation:</p>
                                <br><br>
                        
                            </td>
                            <td colspan="4">
                                <h3 class="head">For Pratibha Press & Multimedia Pvt.Ltd </h3>
                                <h4 id="book">Authorised Signatory</h4>
                                <p>Name:</p>
                                <p>Designation:</p>
                    
                            </td>
                        </tr>
                </table>
                <div class="row">
                <div class="col-md-12">
                <center> <p style="font-size:10px;text-align:center">This is a Computer Generated invoice</p></center>
                </div>
                </div>
               </div>
           </div>
        </div> 
        <!-- /.box-body -->

    </div>
        @if ($ptype_i!=count($print_type)-1)
            <pagebreak resetpagenum="1"></pagebreak> 
        @endif
        <htmlpagefooter name="page-footer">
            Page {PAGENO} of {nbpg}
        </htmlpagefooter>
        
    @endfor
    
</body>

</html>