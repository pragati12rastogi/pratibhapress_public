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
    <div id="first_row" HIDDEN> 
        <div class="row">
            <div class="col-md-3">
                <select type="text" onchange="checkStringEndOfList(this);changeItem(this)" name="item_name[]" id="particular_item_0" class="input-css select form-control particular particular-item">
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
                <input type="number" onfocus="focusQuantityItem(this);" onchange="changeQuantity(this)" min="0" id="quantity_0" name="item_quantity[]" class="input-css form-control quantity">
            </div>
            <div style="text-align:right" class="col-md-1">
                <input type="number" onchange="changeRate(this)" min="0" id="rate_0" name="item_rate[]" class="input-css form-control rate">            
            </div>
            <div style="text-align:right" class="col-md-1">
                <select id="uom_0" name="item_uom[]" class="input-css form-control uom select">
                    <option value="default">Select Per</option>
                </select>
            </div>
            <div style="text-align:right" class="col-md-2">
                <input type="number" onfocus="focusAmountItem(this);" onchange="changeAmount(this);changeItemAmount(this)" onblur="blurAmountInvoice(this)" min="0" id="amount_item_0" name="item_amount[]" class="input-css form-control amount">
            </div>
        </div>
    </div>
    @foreach($ledger_entries as $ll)
        @if(isset($inventory_alloc[$ll['id']]))
            @for($i = 0;$i< count($inventory_alloc[$ll['id']]);$i++)
                <div id="row"> 
                    <div class="row">
                        <div class="col-md-3">
                            <select type="text" onchange="checkStringEndOfList(this);changeItem(this)" name="item_name[]" id="particular_item_{{$i+1}}" class="input-css select form-control particular particular-item">
                                <option value="default">Select Particular</option>
                                @foreach ($item as $key)                                            
                                <option value="{{$key->id}}" {{$inventory_alloc[$ll['id']][$i]['stock_item_name'] == $key->id ?'selected':''}}>{{$key->item_name}}</option>
                                @endforeach
                                <option value="eol">End Of List</option>                    
                            </select>        
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div style="text-align:right" class="col-md-2">
                            <input type="number" onfocus="focusQuantityItem(this);" onchange="changeQuantity(this)" min="0" id="quantity_{{$i+1}}" name="item_quantity[]" class="input-css form-control quantity" value="{{$inventory_alloc[$ll['id']][$i]['stock_item_quantity']}}">
                        </div>
                        <div style="text-align:right" class="col-md-1">
                            <input type="number" onchange="changeRate(this)" min="0" id="rate_{{$i+1}}" name="item_rate[]" class="input-css form-control rate" value="{{$inventory_alloc[$ll['id']][$i]['stock_item_quantity']}}">            
                        </div>
                        <div style="text-align:right" class="col-md-1">
                            <select id="uom_{{$i+1}}" name="item_uom[]" class="input-css form-control uom select">
                                <option value="default">Select Per</option>
                                @foreach($perlist_array[$inventory_alloc[$ll['id']][$i]['id']] as $per)
                                    <option value="{{$per['id']}}" {{$inventory_alloc[$ll['id']][$i]['stock_item_uom_id']==$per['id']?'selected':''}}>{{$per['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="text-align:right" class="col-md-2">
                            <input type="number" onfocus="focusAmountItem(this);" onchange="changeAmount(this);changeItemAmount(this)" onblur="blurAmountInvoice(this)" min="0" id="amount_item_{{$i+1}}" name="item_amount[]" class="input-css form-control amount" value="{{($led_type == 'cr')?$inventory_alloc[$ll['id']][$i]['amount']:$inventory_alloc[$ll['id']][$i]['amount']*(-1)}}">
                        </div>
                    </div>
                </div>
            @endfor
        @endif
    @endforeach
    <div class="row">
    </div>
    {{-- div to store replicated data --}}
        <div id="particularDiv">

        </div>
        <div class="row">
            <div class="col-md-9"></div>
            <div id="total_amount" style="text-align:center" class="col-md-3">{{$inve_sum}}
            </div>
            <input type="hidden" name="total_amount_elem" id="total_amt_elem" value="{{$inve_sum}}">
        </div>
        <hr>
    {{-- content inside #tax_row will replicate and all child element's id should end with '_1'  --}}
        <div id="tax_row" HIDDEN>
            <div class="row">
                <div class="col-md-3">
                    
                        {{-- value to be verified as cr or dr --}}
                        <input type="hidden" name="amount_type[]" id="amount_type_0" value={{$led_type}}>
                    <select type="text" onchange="checkStringEndOfList(this);" name="tax[]" id="particular_0" class="input-css select form-control tax">
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
                <div style="text-align:right;display: flex;" class="col-md-1">
                    <input type="number" onchange="calcTax(this)"  min="0" id="tax_rate_0" name="tax_rate[]" class="input-css form-control rate"><span style="margin-top: 12px;">%</span>
                </div>
                <div style="text-align:right" class="col-md-1">
                </div>
                <div style="text-align:right" class="col-md-2">
                    <input type="number" onfocus="focusAmount(this);" onblur="blurAmountInvoiceTax(this)" min="0" id="tax_amount_0" name="tax_amount[]"  class="input-css form-control tax_amount">
                    <!-- onchange="changeAmountInvoice(this)"  -->
                </div>
            </div>
            <br>
            <div class="row" id="tax_modal_div">
                {{-- this div will contain modal button --}}
                <div id="modal_tax_div_button_0" style="margin-top: 2rem;" class="form-group"></div>
                {{-- this div will contain modal --}}   
                <div id="modal_tax_div_0" style="display:inline"></div>
            </div>
        </div>
    @if($led_type == 'cr')
        @for($i=0;$i< count($ledger_entries);$i++)
            @if($ledger_entries[$i]['is_deemed_positive'] == 1)
                <div class="row">
                    <div class="row">
                        <div class="col-md-3">
                            
                                {{-- value to be verified as cr or dr --}}
                                <input type="hidden" name="amount_type[]" id="amount_type_{{$i+1}}" value={{$led_type}}>
                                <select type="text" onchange="checkStringEndOfList(this);" name="tax[]" id="particular_{{$i+1}}" class="input-css select form-control tax">
                                    <option value="default">Select Ledger</option>
                                    @foreach ($tax as $key) 
                                        <option value="{{$key->id}}" {{$ledger_entries[$i]['ledger_name']==$key->id?'selected':''}}>{{$key->name}}</option>
                                    @endforeach
                                    <option value="eol">End Of List</option>

                                </select>        
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div style="text-align:right" class="col-md-2">
                        </div>
                        <div style="text-align:right;display: flex;" class="col-md-1">
                            <input type="number" onchange="calcTax(this)"  min="0" id="tax_rate_{{$i+1}}" name="tax_rate[]" value="{{$ledger_entries[$i]['basic_rate_of_invoice_tax']}}" class="input-css form-control rate"><span style="margin-top: 12px;">%</span>
                        </div>
                        <div style="text-align:right" class="col-md-1">
                        </div>
                        <div style="text-align:right" class="col-md-2">
                            <input type="number" onfocus="focusAmount(this);" onblur="blurAmountInvoiceTax(this)" min="0" id="tax_amount_{{$i+1}}" name="tax_amount[]"  class="input-css form-control tax_amount" value="{{$ledger_entries[$i]['amount']*(-1)}}">
                            <!-- onchange="changeAmountInvoice(this)"  -->
                        </div>
                    </div>
                    
                    @if(isset($cost_modal_list[$ledger_entries[$i]['id']]))
                        <div class="row" id="tax_modal_div">
                            {{-- this div will contain modal button --}}
                            <div id="modal_tax_div_button_{{$i+1}}" style="margin-top: 2rem;" class="form-group">
                                <div class="col-md-2">
                                    <button type="button" style="margin-right:1em" class="btn btn-primary btn-sm CostCenterToggleButton" data-toggle="modal" data-target="#costcenter-myModal_{{$i+1}}">Cost Allocations Details</button>
                                </div>
                                <div id="form_status-costcenter-myModal_{{$i+1}}_div" class="col-md-4">
                                    <div class="col-md-4">
                                        <label>Cost Center Error:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input max="0" type="number" id="form_status-costcenter-myModal_{{$i+1}}" class="form_status form-control">
                                    </div>
                                </div>
                                
                            </div>
                            {{-- this div will contain modal --}}   
                            <div id="modal_tax_div_{{$i+1}}" style="display:inline">
                                <div class="container">
                                    <div class="modal fade" id="costcenter-myModal_{{$i+1}}" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" onclick="validateModal('costcenter-myModal_{{$i+1}}')">&times;</button>
                                                    <h4 class="modal-title">Cost Allocations</h4>
                                                    <center>Upto : {{$currency}} <span id="costcenter-total-amount_{{$i+1}}">{{$ledger_entries[$i]['amount']*(-1)}}</span> <span id="costcenter-total-amount-type_{{$i+1}}" style="text-transform:capitalize">{{$led_type}}</span></center>
                                                    <!-- <hr> -->
                                                </div>
                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-condensed costcenter-table" style="text-align:center">
                                                            <tr>
                                                                <th>Cost Category</th>
                                                                <th>Name of Cost Centre</th>
                                                                <th>Amount</th>
                                                            </tr>
                                                            @foreach($cost_modal_list[$ledger_entries[$i]['id']] as $cost)
                                                            <tr id="costcenter-newRow">
                                                                <td>
                                                                    <select required="required" class="select form-control input-css costcenter costcenter-name-{{$i+1}}" onchange="validateModalFields('option',this)"  name="costcenter_category[{{$i}}][]" id="costcenter_category{{$i+1}}">
                                                                        <option value="default">Select cost category</option>
                                                                        @foreach($costcategories as $cc)
                                                                            <option value="{{$cc['id']}}" {{$cost['cost_category_id']==$cc['id']?'selected':''}}>{{$cc['name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select required="required" class="select form-control input-css costcenter costcenter-name-{{$i+1}}" onchange="validateModalFields('option',this)"  name="costcenter_name[{{$i}}][]" id="costcenter_name_{{$i+1}}">
                                                                        <option value="default">Select cost category</option>
                                                                        @foreach($cost_center[$ledger_entries[$i]['id']][0] as $cost_cente)
                                                                            <option value="{{$cost_cente['id']}}"{{$cost_cente['id']==$cost['cost_center_id']?'selected':''}}>{{$cost_cente['name']}}</option>
                                                                        @endforeach
                                                                       
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                <input required="required" type="number" class="input-css form-control costcenter costcenter-amount" onchange="validateModalFields('number',this)" name="costcenter_amount[{{$i}}][]" id="costcenter_amount_{{$i+1}}" value="{{$cost['amount']}}">
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" onclick="validateModal('costcenter-myModal_{{$i+1}}');costcenterValidateTaxModal('costcenter-myModal_{{$i+1}}')">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row" id="tax_modal_div">
                            {{-- this div will contain modal button --}}
                            <div id="modal_tax_div_button_{{$i+1}}" style="margin-top: 2rem;" class="form-group"></div>
                            {{-- this div will contain modal --}}   
                            <div id="modal_tax_div_{{$i+1}}" style="display:inline"></div>
                        </div>
                    @endif
                </div>
            @endif
        @endfor
    @elseif($led_type == 'dr')
        @for($i=0;$i< count($ledger_entries);$i++)
            @if($ledger_entries[$i]['is_deemed_positive'] == 1)
                <div class="row">
                    <div class="row">
                        <div class="col-md-3">
                                {{-- value to be verified as cr or dr --}}
                                <input type="hidden" name="amount_type[]" id="amount_type_{{$i+1}}" value={{$led_type}}>
                                <select type="text" onchange="checkStringEndOfList(this);" name="tax[]" id="particular_{{$i+1}}" class="input-css select form-control tax">
                                    <option value="default">Select Ledger</option>
                                    @foreach ($tax as $key)                                            
                                        <option value="{{$key->id}}" {{$ledger_entries[$i]['ledger_name']==$key->id?'selected':''}}>{{$key->name}}</option>
                                    @endforeach
                                    <option value="eol">End Of List</option>

                                </select>        
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div style="text-align:right" class="col-md-2">
                        </div>
                        <div style="text-align:right;display: flex;" class="col-md-1">
                            <input type="number" onchange="calcTax(this)"  min="0" id="tax_rate_{{$i+1}}" name="tax_rate[]" value="{{$ledger_entries[$i]['basic_rate_of_invoice_tax']}}" class="input-css form-control rate"><span style="margin-top: 12px;">%</span>
                        </div>
                        <div style="text-align:right" class="col-md-1">
                        </div>
                        <div style="text-align:right" class="col-md-2">
                            <input type="number" onfocus="focusAmount(this);" onblur="blurAmountInvoiceTax(this)" min="0" id="tax_amount_{{$i+1}}" name="tax_amount[]"  class="input-css form-control tax_amount" value="{{$ledger_entries[$i]['amount']*(-1)}}">
                            <!-- onchange="changeAmountInvoice(this)"  -->
                        </div>
                    </div>
                    
                    @if(isset($cost_modal_list[$ledger_entries[$i]['id']]))
                        <div class="row" id="tax_modal_div">
                            {{-- this div will contain modal button --}}
                            <div id="modal_tax_div_button_{{$i+1}}" style="margin-top: 2rem;" class="form-group">
                                <div class="col-md-2">
                                    <button type="button" style="margin-right:1em" class="btn btn-primary btn-sm CostCenterToggleButton" data-toggle="modal" data-target="#costcenter-myModal_{{$i+1}}">Cost Allocations Details</button>
                                </div>
                                <div id="form_status-costcenter-myModal_{{$i+1}}_div" class="col-md-4">
                                    <div class="col-md-4">
                                        <label>Cost Center Error:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input max="0" type="number" id="form_status-costcenter-myModal_{{$i+1}}" class="form_status form-control">
                                    </div>
                                </div>
                            </div>
                            {{-- this div will contain modal --}}   
                            <div id="modal_tax_div_{{$i+1}}" style="display:inline">
                                <div class="container">
                                    <div class="modal fade" id="costcenter-myModal_{{$i+1}}" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" onclick="validateModal('costcenter-myModal_{{$i+1}}')">&times;</button>
                                                    <h4 class="modal-title">Cost Allocations</h4>
                                                    <center>Upto : {{$currency}} <span id="costcenter-total-amount_{{$i+1}}">{{$ledger_entries[$i]['amount']*(-1)}}</span> <span id="costcenter-total-amount-type_{{$i+1}}" style="text-transform:capitalize">{{$led_type}}</span></center>
                                                    <!-- <hr> -->
                                                </div>
                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-condensed costcenter-table" style="text-align:center">
                                                            <tr>
                                                                <th>Cost Category</th>
                                                                <th>Name of Cost Centre</th>
                                                                <th>Amount</th>
                                                            </tr>
                                                            @foreach($cost_modal_list[$ledger_entries[$i]['id']] as $cost)
                                                            <tr id="costcenter-newRow">
                                                                <td>
                                                                    <select required="required" class="select form-control input-css costcenter costcenter-name-{{$i+1}}" onchange="validateModalFields('option',this)"  name="costcenter_category[{{$i}}][]" id="costcenter_category{{$i+1}}">
                                                                        <option value="default">Select cost category</option>
                                                                        @foreach($costcategories as $cc)
                                                                            <option value="{{$cc['id']}}" {{$cost['cost_category_id']==$cc['id']?'selected':''}}>{{$cc['name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select required="required" class="select form-control input-css costcenter costcenter-name-{{$i+1}}" onchange="validateModalFields('option',this)"  name="costcenter_name[{{$i}}][]" id="costcenter_name_{{$i+1}}">
                                                                        <option value="default">Select cost category</option>
                                                                        @foreach($cost_center[$ledger_entries[$i]['id']][0] as $cost_cente)
                                                                            <option value="{{$cost_cente['id']}}"{{$cost_cente['id']==$cost['cost_center_id']?'selected':''}}>{{$cost_cente['name']}}</option>
                                                                        @endforeach
                                                                       
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                <input required="required" type="number" class="input-css form-control costcenter costcenter-amount" onchange="validateModalFields('number',this)" name="costcenter_amount[{{$i}}][]" id="costcenter_amount_{{$i+1}}" value="{{$cost['amount']}}">
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" onclick="validateModal('costcenter-myModal_{{$i+1}}');costcenterValidateTaxModal('costcenter-myModal_{{$i+1}}')">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row" id="tax_modal_div">
                            {{-- this div will contain modal button --}}
                            <div id="modal_tax_div_button_{{$i+1}}" style="margin-top: 2rem;" class="form-group"></div>
                            {{-- this div will contain modal --}}   
                            <div id="modal_tax_div_{{$i+1}}" style="display:inline"></div>
                        </div>
                    @endif
                </div>
            @endif
        @endfor
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
                    <label for="narration">{{__('accounting/voucher.narration')}}</label>
                    <textarea type="text" name="narration" id="narration" class="input-css form-control narration">{{$voucher->narration}}</textarea>      
                @endif
            </div>
            <div class="col-md-3"></div>
        <div id="total_amount_tax" style="text-align:right" class="col-md-3">{{$totalcr}}
        </div>
        <input type="hidden" name="total_amount_tax" id="total_amount_tax_inp" value="{{$totalcr}}" >

        </div>
        <br>
        <br>