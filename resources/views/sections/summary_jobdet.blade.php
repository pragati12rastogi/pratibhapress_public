

<link rel="stylesheet" href="/css/common.css">

{{-- <div class="summary-outline" >
  <div class="row " >
    @foreach($data1 as $r=>$v)
        <div class="col-md-2"><b >{{$r}}</b> : {{$v}}</div>
    @endforeach
  </div> 
  @php
    $p =$data2['val'];
    $rmp = $data2['rem'];
    $t = json_decode($p);
    $rmt = json_decode($rmp);
  @endphp
  <br>
  <div class="row">
    @if($t)
      @foreach($t as $k=>$v)
          <div class="col-md-2"><b>{{$k}}</b> : {{$v." ".$rmt->$k}}</div>          
      @endforeach         
    @else
        <div class="col-md-3">No Binding Details Found</div>
    @endif  
  </div>
</div> --}}
              <div class="row">
                  <div class="col-md-12">
                               <div class="col-md-3"><strong>Internal Order Date:</strong>{{$internal['created_time']}}</div>
                  
                               <div class="col-md-3"><strong>Internal Order Number:</strong>{{$internal['io_number']}}</div>
                               <div class="col-md-3"><strong>Item Category:</strong>{{$internal['item_category']}} {{$internal['other_item_name']}}</div>
                                <div class="col-md-3"><strong>Job Date:</strong>{{$internal['job_date']}}</div>
                                <div class="col-md-3"><strong>IO Type:</strong>{{$internal['io_type']}}</div>
                                <div class="col-md-3"><strong>Delivery Date:</strong>{{$internal['delivery_date']}}</div>
                                <div class="col-md-3"><strong>Final Job Size:</strong>{{$internal['job_size'].' '.$internal['dimension']}}</div>
                                <div class="col-md-3"><strong>Marketing Person:</strong>{{$internal['marketing_name']}}</div>
                                <div class="col-md-3"><strong>Party Reference Name:</strong>{{$internal['reference_name']}}</div>
                                <div class="col-md-3"><strong>Paper Supplied:</strong>{{$internal['is_supplied_paper']}}</div>
                                <div class="col-md-3"><strong>Plate Supplied:</strong>{{$internal['is_supplied_plate']}}</div>
                                <div class="col-md-3"><strong>Transporation:</strong>{{$internal['transportation_charge']}}</div>
                                <div class="col-md-3"><strong>Other Charges:</strong>{{$internal['other_charge']}}</div>
                                <div class="col-md-3"><strong>Remarks:</strong>{{$internal['remarks']}}</div>

                                <div class="col-md-3"><strong>Amount:</strong> 
                                  @if ($internal['amount']==null)
                                  {{'-'}}
                                  @else
                                  {{$internal['amount']}}
                                  @endif
                                </div>
                                <div class="col-md-3"><strong>Mode:</strong>
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
                                </div>
                                <div class="col-md-3"><strong>Date:</strong>
                                  @if ($internal['amount_received_date']==null)
                                  {{'-'}}
                      
                                  @else
                                    {{$internal['amount_received_date']}}  
                                  @endif  
                                </div>
                          
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                    @php
                    $tax=$internal['gst'];
                    $rate=$internal['rate_per_qty'];
                    $qty=$internal['qty'];
                    $tax_applicable=($tax*$rate*$qty)/100;
                    $amount=$rate*$qty;
                    $total=$tax_applicable+$amount;
                @endphp
       
                                          <div class="col-md-3"><strong>Quantity:</strong>{{$internal['qty']}}</div>
                                          <div class="col-md-3"><strong>HSN/SAC:</strong>{{$internal['uom_name']}}</div>
                                          <div class="col-md-3"><strong>Unit:</strong>{{$internal['uom_name']}}</div>
                                          <div class="col-md-3"><strong>Front Color:</strong>{{$internal['front_color']}}</div>
                                          <div class="col-md-3"><strong>Back Color:</strong>{{$internal['back_color']}}</div>
                                          <div class="col-md-3"><strong>Job Details:</strong>{{$internal['details']}}</div>
                                          <div class="col-md-3"><strong>Rate:</strong>{{$internal['rate_per_qty']}}</div>
                                          <div class="col-md-3"><strong>Amount:</strong>{{$amount}}</div>
                                          <div class="col-md-3"><strong>Tax % Applicable:</strong>{{$internal['gst']}}</div>
                                          <div class="col-md-3"><strong>Total:</strong>{{$total}}</div>
                              
                  </div>
              </div>


