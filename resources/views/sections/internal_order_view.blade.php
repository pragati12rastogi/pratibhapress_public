
@extends($layout)
@section('title', __('internal_order.mytitle'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('internal_order.mytitle')}}</i></a></li>
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
  
<div class="box-header with-border">
        <div class='box box-default'>
            <div class="container-fluid">
                <h2>Internal Order</h2>
                    @php
                    if($internal['status']=="Open"){echo "<button class='btn btn-success'>".$internal['status']."</button>";}
                    if($internal['status']=="Closed"){echo "<button class='btn btn-danger'>".$internal['status']."</button>";}
            @endphp
            <br><br>
                    <div class="row">
                        <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                        @if ($internal['status']=="Closed")
                                        <tr style="background-color:thistle"><th>Closed Date : </th>
                                           
                                           <td>@php
                                               if(isset($internal['closed_date'])){echo $internal['closed_date'];}
                                               @endphp
                                           </td>
                                           <th>Closed By : </th>
                                           <td>@php
                                                 echo $closed_by['closed_by_name'];
                                                  
                                           @endphp</td>
                                       </tr>
                                           <br> 
                                        @endif
                                        <tr>
                                            <th colspan="3">Internal Order Date:</th>
                                            <td >
                                                    {{$internal['created_time']}}
                                            </td>
                                            
                                            <th colspan="3">
                                                    Internal Order Number:
                                                </th>
                                                <td >
                                                    {{$internal['io_number']}}
                                                </td>
                                        </tr>
                                       
                                      
                                        <tr>
                                            <th colspan="3">Item Category:</th>
                                            <td>{{$internal['item_category']}} <br>     
                                                 {{$internal['other_item_name']}}</td>

                                        <th colspan="3">Job Date:</th>
                                        <td >{{$internal['job_date']}}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3">IO Type:</th>
                                            <td >{{$internal['io_type']}}</td>
                                       
                                            <th colspan="3">Delivery Date:</th>
                                            <td >{{$internal['delivery_date']}}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Final Job Size:</th>
                                            <td >{{$internal['job_size'].' '.$internal['dimension']}}</td>
                                            <th colspan="3">Marketing Person:</th>
                                            <td>{{$internal['marketing_name']}}</td>
                                        </tr>
                                    </table> 
                                    <table class="table table-bordered table-striped">
                                       
                                            <tr>
                                                <th colspan="4">Party Reference Name</th>
                                                
                                             
                                        </tr>
                                        <tr>
                                                <td colspan="4">{{$internal['reference_name']}}</td>
                                               

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
               <h3>Job Details</h3>
                
            <br>
                    <div class="row">
                        <div class="col-md-12">
                                <table class="table table-bordered table-striped">
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
                                                <td><p><b>Front Color:   </b>{{$internal['front_color']}}&nbsp;&nbsp;&nbsp;    <b>Back Color:   </b>{{$internal['back_color']}}</p>
                                                {{$internal['details']}}</td>
                                                <td>{{$internal['rate_per_qty']}}</td>
                                        <td id="operation">{{$amount}}</td>
                                        </tr>
                                        <tr>
                                                <th colspan="4">
                                                    Tax % Applicable
                                                </th>
                        
                                                <td colspan="1">
                                                        <h4 id="book">{{$internal['gst']}}</h4>
                                                </td>
                                                <td colspan="1">
                                                <h4 id="small taxPer">
                                        {{$tax_applicable}}        
                                        </h4>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th colspan="5">
                                                        Total
                                                </th>
                                                
                                                <td colspan="1">
                                                        <h4 id="small totalper">{{$total}}</h4>
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
                
            <br>
                    <div class="row">
                        <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                        <tr>
                                                <th>
                                                        <h4> Paper Supplied :</h4>
                                                </th>
                                                <td colspan="6">
                                                        <p>{{$internal['is_supplied_paper']}}</p>
                                                </td>
                                      
                                                <th>
                                                        <h4>Plate Supplied :</h4>
                                                </th>
                                                <td colspan="6">
                                                        <p id="small">{{$internal['is_supplied_plate']}}</p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th>
                                                        <h4>Transporation :</h4>
                                                </th>
                                                <td colspan="6">
                                                        <p id="small">{{$internal['transportation_charge']}}</p>
                                                </td>
                                      
                                                <th>
                                                        <h4>Other Charges :</h4>
                                                </th>
                                                <td colspan="6">
                                                        <p id="small">{{$internal['other_charge']}}</p>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th>
                                                        <h4>Payment Terms :</h4>
                                                </th>
                                                <td colspan="6">
                                                        <p id="small">{{$internal['value']}}</p>
                                                </td>
                                      
                                                <th>
                                                        <h4>Remarks :</h4>
                                                </th>
                                                <td colspan="6">
                                                        <p id="small">{{$internal['remarks']}}</p>
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
</div>
</section>
@endsection
