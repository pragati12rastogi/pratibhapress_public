
<div class="row">
    <div style="text-align:center" class="col-md-1"><label for="">{{__('accounting/voucher.account')}}</label>
    </div>
    <div class="col-md-3">
        <input type="hidden" name="amount_type[]" value="{{$led_type=='dr'?'cr':'dr'}}">
        <select onchange="addBankLedger(this)" class="select input-css form-control account" id="account" name="account_{{$led_type=='dr'?'cr':'dr'}}[]" >                  
            <option value="default">Select Account</option>
            @foreach ($account as $key)
                <option value="{{$key->id}}" {{ ($voucher->party_name_ledger == $key->id)?'selected':'' }}>{{$key->name}}</option>
            @endforeach        
        </select>
    </div>
</div>