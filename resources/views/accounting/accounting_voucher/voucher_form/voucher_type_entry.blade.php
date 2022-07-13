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
    {{-- content inside #first_row will replicate and all child element's id should end with '_1'  --}}
    <div id="first_row">
        <div class="row">
            <div class="col-md-4">
                <input type="hidden" name="amount_type[]" id="amount_type_1" value={{$led_type}}>
                <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$led_type}}[]" id="particular_1" class="input-css select form-control particular">
                    <option value="default">Select Particular</option>
                    @foreach ($ledger as $key)                                            
                    <option value="{{$key->id}}">{{$key->name}}</option>
                    @endforeach
                    <option value="eol">End Of List</option>

                </select>        
            </div>
            <div class="col-md-4">
                @if($multinarrationsetting==1 )
                    <textarea class="form-control input-css" name="narration[]" id="multi_narration" cols="30" rows="1"></textarea>
                @endif
            </div>
            <div style="text-align:right" class="col-md-4">
                <input type="number" onfocus="focusAmount(this);" onchange="changeAmount(this);" onblur=" blurAmount(this);" min="0" id="amount_1" name="amount_{{$led_type}}[]" class="input-css form-control amount">
            </div>
        </div>
        
    </div>
    <div class="row">
        {{-- this div will contain modal --}}   
        <div id="modal_div_1" style="display:inline"></div>
        {{-- this div will contain modal button --}}
        <div id="modal_div_button_1" style="margin-top: 2rem;" class="form-group"></div>
    </div>
    {{-- div to store replicated data --}}
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
                <textarea type="text" name="narration" id="narration" class="input-css form-control narration"></textarea>      
            @endif
        </div>
        <div id="total_amount" style="text-align:right" class="col-md-4">
        </div>
            <input type="hidden" name="amount_{{$led_type=='dr'?'cr':'dr'}}[]" id="total_amt_elem" >
    </div>
        <br>
        <br>

