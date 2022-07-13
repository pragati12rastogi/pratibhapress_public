<div class="row">
    <div class="col-md-4">
        <label for="ref_no">Ref :</label>
        <input type="text" name="ref_no" id="ref_no" class="input-css form-control ref_no">
    </div>
</div>
<div class="row">
    <div style="text-align:center" class="col-md-2"><label for="">{{__('accounting/voucher.party ac name')}}</label>
    </div>
    <div class="col-md-3">
        <select class="select input-css form-control account" onchange="getInvoiceDetails('{{$ledger_type_name}}')" id="account" name="account" >                  
            <option value="default">Select Account</option>
            @foreach ($account as $key)
                <option value="{{$key->id}}">{{$key->name}}</option>
            @endforeach        
        </select>
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
        <select onchange="addBankLedger(this)" class="select input-css form-control account" id="ledger_account" name="ledger_account" >                  
            <option value="0">Select Account</option>
            @foreach ($sales_account as $key)
                <option value="{{$key->id}}">{{$key->name}}</option>
            @endforeach
        </select>
    </div>
</div>
{{-- <div class="row">
    <div style="text-align:center" class="col-md-2">
        <label for="">{{__('accounting/voucher.vat tax class')}}</label>
    </div>
    <div class="col-md-3" id="vat_tax_class">
    </div>
</div> --}}