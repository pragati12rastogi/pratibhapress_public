
<div class="row">
    
    <div class="col-md-3">
        <label class="inline lbl_in_space">{{__('accounting/voucher.account')}}</label>
        <span>{{$voucher->LedgerName}}</span>
        <!-- <span hidden id="ac_id_span" onload="addBankLedger({{$voucher->party_name_ledger}})">{{$voucher->party_name_ledger}}</span> -->
    </div>
    <div class="col-md-offset-6 col-md-3">
        <label class="inline lbl_in_space">{{__('accounting/voucher.date')}}</label>
        <span>{{$voucher->date}}</span>
    </div>
</div>