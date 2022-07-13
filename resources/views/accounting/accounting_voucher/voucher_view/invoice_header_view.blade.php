<div class="row">
    <div class="col-md-4">
        <label class="col-sm-6" for="ref_no">Ref : </label>
       <span class="col-sm-6"> {{$voucher->reference}}</span> 
    </div>
</div>
<div class="row">
    <div style="text-align:center" class="col-md-2"><label for="">{{__('accounting/voucher.party ac name')}}</label>
    </div>
    <div class="col-md-3">
       {{$voucher->LedgerName}}
    </div>
    {{-- this div will contain modal --}}   
    <div id="invoice_detail_div"></div>
    {{-- this div will contain modal button --}}
    <div id="invoice_detail_button"></div>
</div> 
<div class="row">
    <div style="text-align:center" class="col-md-2">
        <label for="">{{__('accounting/voucher.current balance')}}</label>
    </div>
    <div class="col-md-3" id="current_balance">
    </div>
</div>

<div class="row">
    
        <div style="text-align:center" class="col-md-2"><label for="">{{$ledger_type_name}} {{__('accounting/voucher.ledger')}}</label>
        </div>
        <div class="col-md-3">
           {{$sales_ledger->name}}
        </div>
   
</div>
{{-- <div class="row">
    <div style="text-align:center" class="col-md-2">
        <label for="">{{__('accounting/voucher.vat tax class')}}</label>
    </div>
    <div class="col-md-3" id="vat_tax_class">
    </div>
</div> --}}