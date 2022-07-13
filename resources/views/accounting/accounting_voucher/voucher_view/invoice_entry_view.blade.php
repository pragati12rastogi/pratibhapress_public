<div class="row">
    <div class="col-md-3">
        <label for="">{{__('accounting/voucher.name of item')}}</label>
    </div>
    <div class="col-md-2"></div>
    <div style="text-align:right" class="col-md-2">
        <label for="">{{__('accounting/voucher.quantity')}}</label>
    </div>
    <div style="text-align:right" class="col-md-1">
        <label for="">{{__('accounting/voucher.rate')}}</label>
    </div>
    <div style="text-align:right" class="col-md-2">
        <label for="">{{__('accounting/voucher.per')}}</label>
    </div>
    <div style="text-align:right" class="col-md-2">
        <label for="">{{__('accounting/voucher.amount')}}</label>
    </div> 
</div>
{{-- content inside #first_row will replicate and all child element's id should end with '_1'  --}}
@foreach($ledger_entries as $ll)
@if(isset($inventory_alloc[$ll['id']]))
    @foreach($inventory_alloc[$ll['id']] as $inventory_a)
        <div id="first_row"> 
            <div class="row">
                <div class="col-md-3">
                        {{$inventory_a['item_name']}}
                </div>
                <div class="col-md-2">
                </div>
                <div style="text-align:right" class="col-md-2">
                    {{$inventory_a['stock_item_quantity']}}
                </div>
                <div style="text-align:right" class="col-md-1">
                    {{$inventory_a['stock_item_rate']}}
                </div>
                <div style="text-align:right" class="col-md-2">
                {{$inventory_a['stock_item_uom_id']}}
                </div>
                <div style="text-align:right" class="col-md-2">
                    @if($led_type == 'cr')
                        {{$inventory_a['amount']}}
                    @else
                        {{$inventory_a['amount']*(-1)}}
                    @endif
                </div>
            </div>
        
        </div>
    @endforeach
    @endif
@endforeach

<div class="row">
</div>
{{-- div to store replicated data --}}
    <div id="particularDiv">

    </div>
     <div class="row">
        <div class="col-md-10"></div>
        <div id="" style="text-align:right;margin-top: 2px;padding-top: 2px;border-top: 1px solid black; " class="col-md-2">{{$inve_sum}}
        </div>
        
    </div> 
    <hr>
{{-- content inside #tax_row will replicate and all child element's id should end with '_1'  --}}
@if($led_type == 'cr')
    @foreach($ledger_entries as $ll)
        @if($ll['is_deemed_positive'] == 1)
            <div id="tax_row">
                <div class="row">
                    <div class="col-md-3">
                    {{$ll['ledgerName']}}      
                    </div>
                    <div class="col-md-2">
                    </div>
                    <div style="text-align:right" class="col-md-2">
                    </div>
                    <div style="text-align:right" class="col-md-1">
                    {{$ll['basic_rate_of_invoice_tax']}}<span> %</span>
                    </div>
                    <div style="text-align:right" class="col-md-2">
                    </div>
                    <div style="text-align:right" class="col-md-2">
                        {{$ll['amount']*(-1)}}
                    </div>
                </div>
                <br>
                @if(isset($cost_modal_list[$ll['id']]))
                    @foreach($cost_modal_list[$ll['id']] as $cost)
                            <div class="row">
                                <div class="col-sm-10 "><i><b>{{$cost['CostCategoryName']}}</b></i></div>
                                <div class="col-sm-10 ">
                                    <div class="col-sm-2">{{$cost['CostCenterName']}}</div>
                                    <div class="col-sm-2 ">{{$cost['amount']}}</div>
                                </div>
                            </div>
                    @endforeach
                @endif
            </div>
        @endif
    @endforeach 

@elseif($led_type == 'dr')
    @foreach($ledger_entries as $ll)
        @if($ll['is_deemed_positive'] == 1)
            <div id="tax_row">
                <div class="row">
                    <div class="col-md-3">
                    {{$ll['ledgerName']}}      
                    </div>
                    <div class="col-md-2">
                    </div>
                    <div style="text-align:right" class="col-md-2">
                    </div>
                    <div style="text-align:right" class="col-md-1">
                    {{$ll['basic_rate_of_invoice_tax']}}<span> %</span>
                    </div>
                    <div style="text-align:right" class="col-md-2">
                    </div>
                    <div style="text-align:right" class="col-md-2">
                        {{$ll['amount']*(-1)}}
                    </div>
                </div>
                <br>
                @if(isset($cost_modal_list[$ll['id']]))
                    @foreach($cost_modal_list[$ll['id']] as $cost)
                            <div class="row">
                                <div class="col-sm-10 "><i><b>{{$cost['CostCategoryName']}}</b></i></div>
                                <div class="col-sm-10 ">
                                    <div class="col-sm-2">{{$cost['CostCenterName']}}</div>
                                    <div class="col-sm-2 ">{{$cost['amount']}}</div>
                                </div>
                            </div>
                    @endforeach
                @endif
            </div>
        @endif
    @endforeach
@endif
    {{-- div to store replicated data --}}
    <div id="taxDiv">

    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    
    <div class="row">
        <div class="col-md-6">
            @if($common_narrationsetting == 1)
                <label for="narration">{{__('accounting/voucher.narration')}} </label>
            <span> {{$voucher->narration}}</span>
            @endif
        </div>
        <div class="col-md-2"style="text-align:center;">
            <label for="total">Total Quantity:</label> <span>{{$inve_qty}} Nos.</span></div>
        <div id="total_amount_tax" style="text-align:right" class="col-md-4">
            <label for="total">Total:</label> <span> {{$totalcr}}</span>
        </div>
    </div>
    <br>
    <br>