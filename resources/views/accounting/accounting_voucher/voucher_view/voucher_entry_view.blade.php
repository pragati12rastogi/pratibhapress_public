<div class="row">
                                        
    <div class="col-md-4">
        <label for="">{{__('accounting/voucher.particulars')}}</label>
    </div>
    <div class="col-md-4">
        @if($multinarrationsetting==1)
            <label for="">{{__('accounting/voucher.narration')}}</label>
        @endif 
    </div>
    <div style="text-align:right" class="col-md-4">
        <label for="">{{__('accounting/voucher.amount')}}</label>
    </div>
</div>
<div id="base_transaction_div" style="display:none">
    {{$base_transaction}}
</div>

@foreach($ledger_entries as $list)
    @if($header_led_id != $list['id'])

        <div id="first_row">
            <div class="row">
                <div class="col-md-4">
                    {{$list['ledgerName']}} 
                    
                </div>
                <div class="col-md-4">
                    @if($multinarrationsetting==1 )
                        <p>{{$list['narration']}}</p>
                    @endif
                </div>
                <div style="text-align:right" class="col-md-4">
                    @if($list['is_deemed_positive']==1)
                       <span>{{$list['amount']*(-1)}} </span><span> Dr</span>
                    @endif
                    @if($list['is_deemed_positive']==0)
                        <span>{{$list['amount']}} </span><span> Cr</span>
                    @endif
                    
                </div>
            </div>
            
        </div>
    @endif
    <!-- {{-- foreach for cc --}}  -->
    @if(isset($cost_modal_list[$list['id']]))
   
        @foreach($cost_modal_list[$list['id']] as $cost)
        
                <div class="row">
                    <div class="col-sm-12"><i><b>{{$cost['CostCategoryName']}}</b></i></div>
                    <div class="col-sm-12">
                        <div class="col-sm-3">{{$cost['CostCenterName']}}</div>
                        <div class="col-sm-3 col-sm-offset-1">{{$cost['amount']}}</div>
                    </div>
                </div>
            
        @endforeach
    @endif
     {{-- foreach for bw --}}
     
     @if(isset($bill_bill_wise[$list['id']]))
        @foreach($bill_bill_wise[$list['id']] as $bill)
            
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-3"><i><b>{{$bill['bill_type']}}</i></b></div>
                        <div class="col-sm-2 col-sm-offset-1">{{$bill['name']}}</div>
                        <div class="col-sm-2 col-sm-offset-2">{{$bill['credit_period']}}</div>
                        <div class="col-sm-2 col-sm-offset-1">{{$bill['amount']}}</div>
                    </div>
                </div>
            
        @endforeach
    @endif
    
   
@endforeach
<!-- {{-- div to store replicated data --}} -->
<br>
<br>
<br>
<br>
<br>
<br>
<div class="row">
    <div class="col-md-3" id="pymt_by_cheque_div" style="display:none">
        <label for="pymt_by_cheque">Payment By Cheque</label>
        <select class="form-control input-css select pymt_by_cheque" name="pymt_by_cheque" id="pymt_by_cheque">
            <option value="0" selected>No</option>
            <option value="1">Yes</option>
        </select>
        <div id="cheque_details" style="display:none">
            <div class="col-md-3">
                <label for="cheque_number">Cheque Number</label>
                <input type="text" name="cheque_number" id="cheque_number" class="form-control input-css cheque_number">
            </div>
            <div class="col-md-3">
                <label for="cheque_date">Cheque date</label>
                <input type="date" name="cheque_date" id="cheque_date" class="form-control input-css cheque_date datepicker">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        @if($common_narrationsetting==1)
            <label for="narration">{{__('accounting/voucher.narration')}}</label>
            <p>{{$voucher->narration}}</p>
        @endif
    </div>
    <div id="total_amount" style="text-align:right" class="col-md-4">
    <label class="inline lbl_in_space">Total :</label>
        <span>{{$total_amount}}</span>
        
    </div>
       
</div>
    <br>
    <br>

