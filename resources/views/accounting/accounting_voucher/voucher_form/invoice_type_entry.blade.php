    <div class="row">
        <div class="col-md-3">
            <label for="">{{__('accounting/voucher.name of item')}}</label>
        </div>
        <div class="col-md-3"></div>
        <div style="text-align:right" class="col-md-2">
            <label for="">{{__('accounting/voucher.quantity')}}</label>
        </div>
        <div style="text-align:right" class="col-md-1">
            <label for="">{{__('accounting/voucher.rate')}}</label>
        </div>
        <div style="text-align:right" class="col-md-1">
            <label for="">{{__('accounting/voucher.per')}}</label>
        </div>
        <div style="text-align:right" class="col-md-2">
            <label for="">{{__('accounting/voucher.amount')}}</label>
        </div> 
    </div>
    {{-- content inside #first_row will replicate and all child element's id should end with '_1'  --}}
    <div id="first_row"> 
        <div class="row">
            <div class="col-md-3">
                <select type="text" onchange="checkStringEndOfList(this);changeItem(this)" name="item_name[]" id="particular_item_1" class="input-css select form-control particular particular-item">
                    <option value="default">Select Particular</option>
                    @foreach ($item as $key)                                            
                    <option value="{{$key->id}}">{{$key->item_name}}</option>
                    @endforeach
                    <option value="eol">End Of List</option>                    
                </select>        
            </div>
            <div class="col-md-3">
            </div>
            <div style="text-align:right" class="col-md-2">
                <input type="number" onfocus="focusQuantityItem(this);" onchange="changeQuantity(this)" min="0" id="quantity_1" name="item_quantity[]" class="input-css form-control quantity">
            </div>
            <div style="text-align:right" class="col-md-1">
                <input type="number" onchange="changeRate(this)" min="0" id="rate_1" name="item_rate[]" class="input-css form-control rate">            
            </div>
            <div style="text-align:right" class="col-md-1">
                <select id="uom_1" name="item_uom[]" class="input-css form-control uom select">
                    <option value="default">Select Per</option>
                </select>
            </div>
            <div style="text-align:right" class="col-md-2">
                <input type="number" onfocus="focusAmountItem(this);" onchange="changeAmount(this);changeItemAmount(this)" onblur="blurAmountInvoice(this)" min="0" id="amount_item_1" name="item_amount[]" class="input-css form-control amount">
            </div>
        </div>
       
    </div>
    <div class="row">
    </div>
    {{-- div to store replicated data --}}
        <div id="particularDiv">

        </div>
        <div class="row">
            <div class="col-md-9"></div>
            <div id="total_amount" style="text-align:center" class="col-md-3">
            </div>
            <input type="hidden" name="total_amount_elem" id="total_amt_elem">
        </div>
        <hr>
    {{-- content inside #tax_row will replicate and all child element's id should end with '_1'  --}}
        <div id="tax_row">
            <div class="row">
                <div class="col-md-3">
                    
                        {{-- value to be verified as cr or dr --}}
                        <input type="hidden" name="amount_type[]" id="amount_type_1" value={{$led_type}}>
                    <select type="text" onchange="checkStringEndOfList(this);" name="tax[]" id="particular_1" class="input-css select form-control tax">
                        <option value="default">Select Ledger</option>
                        @foreach ($tax as $key)                                            
                            <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                        <option value="eol">End Of List</option>

                    </select>        
                </div>
                <div class="col-md-3">
                </div>
                <div style="text-align:right" class="col-md-2">
                </div>
                <div style="text-align:right" class="col-md-1">
                    <input type="number" onchange="calcTax(this)"  min="0" id="tax_rate_1" name="tax_rate[]" class="input-css form-control rate">%
                </div>
                <div style="text-align:right" class="col-md-1">
                </div>
                <div style="text-align:right" class="col-md-2">
                    <input type="number" onfocus="focusAmount(this);" onblur="blurAmountInvoiceTax(this)" min="0" id="tax_amount_1" name="tax_amount[]"  class="input-css form-control tax_amount">
                    <!-- onchange="changeAmountInvoice(this)"  -->
                </div>
            </div>
            <br>
            <div class="row" id="tax_modal_div">
                {{-- this div will contain modal button --}}
                <div id="modal_tax_div_button_1" style="margin-top: 2rem;" class="form-group"></div>
                {{-- this div will contain modal --}}   
                <div id="modal_tax_div_1" style="display:inline"></div>
            </div>
        </div>
         
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
                    <label for="narration">{{__('accounting/voucher.narration')}}</label>
                    <textarea type="text" name="narration" id="narration" class="input-css form-control narration"></textarea>      
                @endif
            </div>
            <div class="col-md-3"></div>
        <div id="total_amount_tax" style="text-align:right" class="col-md-3">
        </div>
        <input type="hidden" name="total_amount_tax" id="total_amount_tax_inp" >

        </div>
        <br>
        <br>