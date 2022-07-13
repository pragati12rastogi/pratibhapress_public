
@extends($layout)
@section('title', __('tax_invoice.mytitle'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('tax_invoice.mytitle')}}</i></a></li>
@endsection

@section('css')
    <style>
        .help-block{
        color:red;
        }
        table.th{
            text-indent: 50px;
        }
    </style>
@endsection


@section('main_section')
    <section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
<!-- Default box -->
@php
$total=0;
$total_amt=0;
$counter=0;
@endphp
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
<div class="box-header with-border">
        <div class='box box-default'>
            <div class="container-fluid">
                <h4>{{$tax_detail[0]['invoice_number']}}</h4>
                  
            <br><br>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped">
                        <tr style="background-color: #e6fffa">
                                <th>Tax Invoice Dispatch Date :</th>
                                <td>
                                        {{$tax_detail[0]['dispatch_date']}}
                                </td>                        
                         
                                    <th>Tax Invoice Created Date :</th>
                                    <td>
                                            {{$tax_detail[0]['created_time']}}
                                    </td>                        
                                </tr>
                                <tr style="background-color: #ffcccc">
                                        <th>Tax Dispatch Courier Company Name :</th>
                                        <td>
                                                {{$tax_detail[0]['courier_company']}}
                                        </td>                        
                                 
                                            <th>Tax Dispatch Mode :</th>
                                            <td>
                                                    {{$tax_detail[0]['dispatch_mode']}}
                                            </td>                        
                                        </tr>
                                        <tr style="background-color: #e6fffa">
                                                <th>Tax Dispatch Courier Docket Number :</th>
                                                <td>
                                                        {{$tax_detail[0]['docket_number']}}
                                                </td>                        
                                         
                                                <th>Tax Dispatch Person Name :</th>
                                                <td>
                                                        {{$tax_detail[0]['person']}}
                                                </td>                     
                                                </tr>
                    </table>
                </div>
            </div>
                   
            </div>
        </div>
</div>
<div class="box-header with-border">
        <div class='box box-default'>
            <div class="container-fluid">
                    <h4>Tax Invoice Details </h4>
            <br>
            <div class="row">
                    <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                  
                                       <br> 
                                  
                                    <tr>
                                        <th>Tax invoice Date :</th>
                                        <td>
                                                {{$tax_detail[0]['created_at']}}
                                        </td>
                                        <th>
                                                Tax invoice no. :
                                            </th>
                                            <td>
                                                {{$tax_detail[0]['invoice_number']}}
                                            </td>
                                 
                                        <th>Delivery Challan :</th>
                                        <td colspan="4"> @php
                                                echo '<td colspan="2">';
                                                   foreach ($tax_detail as $item) {
                                                       $arr[]=$item['challan_number'];
                                                   }
                                                   $unique_data = array_unique($arr);
                                                    // now use foreach loop on unique data
                                                    foreach($unique_data as $val) {
                                                        echo $val.'<br>';
                                                    }
                        
                                                    echo '</td>';    
                                               @endphp </td>
                                       
                                    </tr>
                                    <tr>

               
                                            <td colspan="2"><b>Internal order</b></td>
                                            <td colspan="4"> <b>Payment Term</b> </td>
                                            <td colspan="2"> <b>P.O NO</b> </td>
                                            <td colspan="2"><b> P.O Date </b></td>
                                           
                                        </tr>
                                        <tr>
                                               
                                               @php
                                                echo '<td colspan="2">';
                                                        foreach ($po as $item) {
                                                            echo str_replace(',','<br>',$item['io_number']);
                                                        }
                                                    echo '</td>';    
                                               @endphp 
                                            
                                           @php
                                           echo '<td colspan="4"  rowspan="2">';
                                                    foreach ($po as $item) {
                                                     
                                                     echo str_replace(',','<br>',$item['payment_term']);
                                                        }
                                                  echo '</td>';      
                                                @endphp
                                            @php
                                            echo '<td colspan="2">';
                                                     foreach ($po as $item) {
                                                         
                                                            echo str_replace(',','<br>',$item['po_number']);
                                                         }
                                                      
                                                     
                                                         
                                                   echo '</td>';      
                                                 @endphp
                                             @php
                                             echo '<td colspan="2">';
                                                      foreach ($po as $item) {
                                                       
                                                            echo str_replace(',','<br>',$item['po_date']);
                                                        
                                                          }
                                                    echo '</td>';      
                                                  @endphp
                                            @php
                                               
                                                 @endphp
                                        </tr>
                        
                                   
                                </table> 

                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                        <tr style="width:100%;">
                                                <td colspan="6"> <b>Consignee</b> </td>
                                                <td colspan="6"><b>Buyer </b></td>
                                            </tr>
                        
                                            <tr>
                                                <td colspan="6">
                                                    
                                                <b>{{$tax_detail[0]['con_name']}}</b><br>
                                                <p>{{$tax_detail[0]['con_address']}} , {{$tax_detail[0]['con_cities']}} , {{$tax_detail[0]['con_states']}},{{$tax_detail[0]['con_country']}} , {{$tax_detail[0]['con_pincode']}}<br>
                                                </p>
                                              
                                                </td>
                                                <td colspan="6">
                                                        <b>{{$tax_detail[0]['partyname']}}</b><br>
                                                        <p>{{$tax_detail[0]['party_address']}} , {{$tax_detail[0]['city']}} , {{$tax_detail[0]['state']}},{{$tax_detail[0]['country']}} , {{$tax_detail[0]['pincode']}}<br>
                                                        </p>
                        
                                                </td>
                                            </tr>
                                    </table> 
                        </div>
                    </div>
            </div>
        </div>
</div>
@php
                foreach($unique_datas as $val) {
                    ${'a'.$val} = 0;
                } 
                // print_r($a6040)  ;
           @endphp
<div class="box-header with-border">
        <div class='box box-default'>
            <div class="container-fluid">
               
            <br>
                    <div class="row">
                        <div class="col-md-12">
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
                        </div>
                    </div>
            </div>
        </div>
</div>
{{-- <div class="box-header with-border">
        <div class='box box-default'>
            <div class="container-fluid">
               <h3>Advance Amount Details</h3>
                
            <br>
                    <div class="row">
                        <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                            <th colspan="1">
                                                    <h4>Amount</h4>
                                            </th>
                                            <td colspan="2">
                                                   
                                                    @if ($internal['amount']==null)
                                                    {{'-'}}
                                                    @else
                                                    {{$internal['amount']}}
                                                    @endif
                                                   
                                            </td>
                                    </tr>
                                    <tr>
                                            <th colspan="1">
                                                    <h4 id="book">Mode</h4>
                                            </th>
                                            <td colspan="2">
                                                
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
                                                    
                                                    
                                            </td>
                                    </tr>
                                        <tr>
                                             
                                               
                                                <th colspan="1">
                                                        <h4 id="book">Date</h4>
                                                </th>
                                                <td colspan="2">
                                                        @if ($internal['amount_received_date']==null)
                                                                        {{'-'}}
                                                            
                                                        @else
                                                          {{$internal['amount_received_date']}}  
                                                        @endif
                                                </td>
                                        </tr>
        
        
        
                                    </table> 
                        </div>
                    </div>
            </div>
        </div>
</div> --}}
</section>
@endsection
