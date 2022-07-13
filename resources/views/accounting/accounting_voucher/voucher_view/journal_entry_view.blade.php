<div class="row table">
    <div class="col-md-2">
        <label for="">{{__('accounting/voucher.amt type')}}</label>
    </div>
    <div class="col-md-4">
        <label for="">{{__('accounting/voucher.particulars')}}</label>
    </div>
    <div class="col-md-2">
        @if($multinarrationsetting==1)
            <label for="">{{__('accounting/voucher.narration')}}</label>
        @endif
    </div>
    <div style="text-align:right" class="col-md-2">
        <label for="">{{__('accounting/voucher.debit')}}</label>
    </div>
    <div style="text-align:right" class="col-md-2">
        <label for="">{{__('accounting/voucher.credit')}}</label>
    </div>
</div>
<div id="base_transaction_div" style="display:none">
    {{$base_transaction}}
</div>
@if ($relation=='nton' || $relation=='1ton')
{{-- content inside #first_row will replicate and all child element's id should end with '_1'  --}}
   @foreach($ledger_entries as $list)
        @if($list['is_deemed_positive'] == 1)
            <div  id="first_row">
                <div class="row">  
                    <div class="col-md-2">
                        Dr
                    </div>
                    <div class="col-md-4">
                           {{$list['ledgerName']}}    
                    </div>
                    <div class="col-md-2">
                        @if($multinarrationsetting==1)
                            <p>{{$list['narration']}}</p>
                        @endif
                    </div>
                    <div class="col-md-2" style="text-align: right">
                        <span>{{$list['amount']*(-1)}} </span><span> Dr</span>
                    </div>
                    <div class="col-md-2">
                       
                    </div>
                </div>
                
            </div>
        
        @elseif($list['is_deemed_positive'] == 0)
            <div  id="first_row">
                <div class="row">  
                    <div class="col-md-2">
                        Cr
                    </div>
                    <div class="col-md-4">
                           {{$list['ledgerName']}}    
                    </div>
                    <div class="col-md-2">
                        @if($multinarrationsetting==1)
                            <p>{{$list['narration']}}</p>
                        @endif
                    </div>
                    <div class="col-md-2" >
                    </div>
                    <div class="col-md-2" style="text-align: right">
                         <span>{{$list['amount']}} </span><span> Cr</span>
                    </div>
                </div>
                
            </div>
        @endif
        @if(isset($cost_modal_list[$list['id']]))
            @foreach($cost_modal_list[$list['id']] as $cost)
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-2"><i><b>{{$cost['CostCategoryName']}}</b></i></div>
                        <div class="col-sm-10 col-sm-offset-2">
                            <div class="col-sm-2">{{$cost['CostCenterName']}}</div>
                            <div class="col-sm-2 ">{{$cost['amount']}}</div>
                        </div>
                    </div>
            @endforeach
        @endif
        @if(isset($bill_bill_wise[$list['id']]))
            @foreach($bill_bill_wise[$list['id']] as $bill)
                
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <div class="col-sm-2"><i><b>{{$bill['bill_type']}}</b></i></div>
                            <div class="col-sm-2 ">{{$bill['name']}}</div>
                            <div class="col-sm-2 ">{{$bill['credit_period']}}</div>
                            <div class="col-sm-2 ">{{$bill['amount']}}</div>
                        </div>
                    </div>
                
            @endforeach
        @endif
        <hr/>
    @endforeach
@endif

    <div id="particularDiv">

    </div>
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

    <div class="col-md-6">
        @if($common_narrationsetting==1)
            <label for="narration">{{__('accounting/voucher.narration')}}</label>
            <p>{{$voucher->narration}}</p>
        @endif    
    </div>
    <div class="col-md-2"></div>
    <div id="debit_total_amount" style="text-align:right;border-top: 1px double #444444;border-bottom: 4px double #444444;    padding-top: 4px;" class="col-md-2">
    {{$total_debit}}
    </div>
    <div id="credit_total_amount" style="text-align:right;border-top: 1px double #444444;border-bottom: 4px double #444444;    padding-top: 4px;" class="col-md-2">
    {{$total_credit}}
    </div>
    </div>
    <br>
    <br>