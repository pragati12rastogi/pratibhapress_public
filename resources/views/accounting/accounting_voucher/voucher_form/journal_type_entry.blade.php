    <div class="row">
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
    @if ($relation=='nton')
    {{-- content inside #first_row will replicate and all child element's id should end with '_1'  --}}
        <div  id="first_row">
            <div class="row">  
                <div class="col-md-2">
                    <select id="amount_type_1" onchange="toggleJournalAmountType(this);addBankLedger(this)" name="amount_type[]" class="input-css select2 select form-control amount_type">
                            <option value="dr" {{$led_type=="dr"?'selected="selected"':''}}>Dr</option>
                        <option value="cr" {{$led_type=="cr"?'selected="selected"':''}}>Cr</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$led_type=="dr"?'dr':'cr'}}[]" id="particular_1" class="input-css select2 select form-control particular">
                        <option value="default">Select Particular</option>
                        @foreach ($ledger as $key)                                            
                        <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                        <option value="eol">End Of List</option>

                    </select>        
                </div>
                <div class="col-md-2">
                    @if($multinarrationsetting==1)
                        <textarea class="form-control input-css narration" name="narration[]" id="multi_narration" cols="30" rows="1"></textarea>                    
                    @endif
                </div>
                <div class="col-md-2">
                    <div style="text-align:right;{{$led_type!="dr"?'display:none;':''}}"  id="amount_dr_div_1" >
                        <input type="number" min="0" value="0" id="amount_dr_1" name="amount_dr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="debitAmount(this)" class="input-css form-control amount_dr">
                    </div>
                    
                </div>
                <div class="col-md-2">
                    <div style="text-align:right;{{$led_type!="cr"?'display:none;':''}}" id="amount_cr_div_1" >
                        <input type="number" min="0" value="0" id="amount_cr_1" name="amount_cr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="creditAmount(this)" class="input-css form-control amount_cr">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            {{-- this div will contain modal --}}
            <div id="modal_div_1" style="display:inline"></div>
            {{-- this div will contain modal button --}}
            <div id="modal_div_button_1" style="margin-top: 2rem;" class="form-group row"></div>
        </div>
    @elseif($relation=='1ton')
        <div class="row">  
            <div class="col-md-1">
                <select id="amount_type_0" onchange="toggleJournalAmountType(this);" name="amount_type[]" class="input-css select2 select form-control amount_type">
                        <option value="{{$led_type}}">{{ucwords($led_type)}}</option>
                </select>
            </div>
            <div class="col-md-4">
                <select type="text" onchange="addBankLedger(this)" name="account_{{$led_type}}[]" id="particular_0" class="input-css select2 select form-control particular">
                    <option value="default">Select Particular</option>
                    @foreach ($account as $key)                                            
                    <option value="{{$key->id}}">{{$key->name}}</option>
                    @endforeach 
                </select>        
            </div>
            <div class="col-md-3">
                @if($multinarrationsetting==1)
                    <textarea class="form-control input-css narration" name="narration[]" id="multi_narration" cols="30" rows="1"></textarea>                    
                @endif
            </div>
            <div class="col-md-2">
                <div style="text-align:right;{{$led_type!="dr"?'display:none;':''}}"  id="amount_dr_div_0" >
                    <input type="number" min="0" value="0" id="amount_dr_0" name="amount_dr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="debitAmount(this)" class="input-css form-control amount_dr">
                </div>
                
            </div>
            <div class="col-md-2">
                <div style="text-align:right;{{$led_type!="cr"?'display:none;':''}}" id="amount_cr_div_0" >
                    <input type="number" min="0" value="0" id="amount_cr_0" name="amount_cr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="creditAmount(this)" class="input-css form-control amount_cr">
                </div>
            </div>
        </div>
        <div class="row">
            {{-- this div will contain modal --}}
            <div id="modal_div_0" style="display:inline"></div>
            {{-- this div will contain modal button --}}
            <div id="modal_div_button_0" style="margin-top: 2rem;" class="form-group row"></div>
        </div>
        {{-- content inside #first_row will replicate and all child element's id should end with '_1' --}}
        <div  id="first_row">
            <div class="row">  
                <div class="col-md-1">
                    
                        <select id="amount_type_1" onchange="toggleJournalAmountType(this)" name="amount_type[]" class="input-css select2 select form-control amount_type">
                                <option value="{{$led_type=="dr"?'cr':'dr'}}">{{$led_type=="dr"?'Cr':'Dr'}}</option>
                                <option value="{{$led_type=="dr"?'dr':'cr'}}">{{$led_type=="dr"?'Dr':'Cr'}}</option>
                        </select>
                    
                </div>
                <div class="col-md-4">
                    <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$led_type=="dr"?'cr':'dr'}}[]" id="particular_1" class="input-css select2 select form-control particular">
                        <option value="default">Select Particular</option>
                        @foreach ($ledger as $key)                                            
                            <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                        <option value="eol">End Of List</option>

                    </select>        
                </div>
                <div class="col-md-3">
                    @if($multinarrationsetting==1)
                        <textarea class="form-control input-css narration" name="narration[]" id="multi_narration" cols="30" rows="1"></textarea>                    
                    @endif
                </div>
                <div class="col-md-2">
                    <div style="text-align:right;{{$led_type=="dr"?'display:none;':''}}"  id="amount_dr_div_1" >
                        <input type="number" min="0" value="0" id="amount_dr_1" name="amount_dr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="debitAmount(this)" class="input-css form-control amount_dr">
                    </div>
                    
                </div>
                <div class="col-md-2">
                    <div style="text-align:right;{{$led_type=="cr"?'display:none;':''}}" id="amount_cr_div_1" >
                        <input type="number" min="0" value="0" id="amount_cr_1" name="amount_cr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="creditAmount(this)" class="input-css form-control amount_cr">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            {{-- this div will contain modal --}}   
            <div id="modal_div_1" style="display:inline"></div>
            {{-- this div will contain modal button --}}
            <div id="modal_div_button_1" style="margin-top: 2rem;" class="form-group row"></div>
        </div>
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
                <textarea type="text" name="narration" id="narration" class="input-css form-control narration"></textarea>      
            @endif    
        </div>
        <div class="col-md-2"></div>
        <div id="debit_total_amount" style="text-align:right" class="col-md-2">0
        </div>
        <div id="credit_total_amount" style="text-align:right" class="col-md-2">0
        </div>
        <div id="relation" hidden style="text-align:right" class="col-md-2">{{$relation}}</div>
        </div>
        <br>
        <br>