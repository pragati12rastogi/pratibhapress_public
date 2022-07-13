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
        <div id="first_row" HIDDEN>
            <div class="row">
                <div class="col-md-4">
                    <input type="hidden" name="amount_type[]" id="amount_type_0" value={{$led_type}}>
                    <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$led_type}}[]" id="particular_0" class="input-css select form-control particular">
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
                    <input type="number" onfocus="focusAmount(this);" onchange="changeAmount(this);" onblur=" blurAmount(this);" min="0" id="amount_0" name="amount_{{$led_type}}[]" class="input-css form-control amount" value="0">
                </div>
            </div>
        </div>
    <!-- 
        <div class="row">
            {{-- this div will contain modal --}}   
            <div id="modal_div_0" style="display:inline"></div>
            {{-- this div will contain modal button --}}
            <div id="modal_div_button_0" style="margin-top: 2rem;" class="form-group"></div>
        </div> -->

    {{-- div to store replicated data --}}
    <div id="particularDiv">
    @for($i=0;$i< count($ledger_entries);$i++)
    @if($header_led_id != $ledger_entries[$i]['id'])    
        <div class="row">
                <div class="col-md-4">
                    <input type="hidden" name="amount_type[]" id="amount_type_{{$i}}" value={{$led_type}}>
                    <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$led_type}}[]" id="particular_{{$i}}" class="input-css select form-control particular">
                        <option value="default">Select Particular</option>
                        @foreach ($ledger as $key)                                            
                        <option value="{{$key->id}}" {{$ledger_entries[$i]['ledger_name']==$key->id ?'selected':''}}>{{$key->name}}</option>
                        @endforeach
                        <option value="eol"{{$ledger_entries[$i]['ledger_name']=='eol' ?'selected':''}}>End Of List</option>

                    </select>        
                </div>
                <div class="col-md-4">
                    @if($multinarrationsetting==1 )
                        <textarea class="form-control input-css" name="narration[]" id="multi_narration" cols="30" rows="1">{{$ledger_entries[$i]['narration']}}</textarea>
                    @endif
                </div>
                <div style="text-align:right" class="col-md-4">
                    <input type="number" onfocus="focusAmount(this);" onchange="changeAmount(this);" onblur=" blurAmount(this);" min="0" id="amount_{{$i}}" name="amount_{{$led_type}}[]" class="input-css form-control amount"
                    value="{{$led_type=='cr'?$ledger_entries[$i]['amount']:$ledger_entries[$i]['amount']*(-1)}}">
                </div>
        </div>
        
        <div class="row">
            {{-- this div will contain modal --}}   
            <div id="modal_div_{{$i}}" style="display:inline">
                @if(isset($cost_modal_list[$ledger_entries[$i]['id']]))
                    <div class="container">
                        <div class="modal fade" id="costcenter-myModal_{{$i}}" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" onclick="validateModal('costcenter-myModal_{{$i}}')">&times;</button>
                                        <h4 class="modal-title">Cost Allocations</h4>
                                        <center>Upto : {{$currency}} <span id="costcenter-total-amount_{{$i}}">{{$led_type=='cr'?$ledger_entries[$i]['amount']:$ledger_entries[$i]['amount']*(-1)}}</span> <span id="costcenter-total-amount-type_{{$i}}" style="text-transform:capitalize">{{$led_type}}</span></center>
                                        <hr>
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
                                                        <select required="required" class="select form-control input-css costcenter" onchange="costcenterCategoryTypeChange(this);checkStringEndOfList(this);validateModalFields('option',this)" name="costcenter_category[{{$i-1}}][]" id="costcenter_category_{{$i}}">
                                                            <option value="default">Select Cost Category</option>
                                                                @foreach($costcategories as $cc)
                                                                    <option value="{{$cc['id']}}" {{$cost['cost_category_id']==$cc['id']?'selected':''}}>{{$cc['name']}}</option>
                                                                @endforeach
                                                                <option value="eol">End Of List</option>

                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select required="required" class="select form-control input-css costcenter costcenter-name-{{$i}}" onchange="validateModalFields('option',this)"  name="costcenter_name[{{$i-1}}][]" id="costcenter_name_{{$i}}">
                                                            <option value="default">Select cost category</option>
                                                            @foreach($cost_center[$ledger_entries[$i]['id']][0] as $cost_cente)
                                                                <option value="{{$cost_cente['id']}}"{{$cost_cente['id']==$cost['cost_center_id']?'selected':''}}>{{$cost_cente['name']}}</option>
                                                            @endforeach
                                                           
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input required="required" type="number" class="input-css form-control costcenter costcenter-amount" onchange="validateModalFields('number',this)" name="costcenter_amount[{{$i-1}}][]" id="costcenter_amount_{{$i}}" value="{{$cost['amount']}}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">      
                                        <button type="button" class="btn btn-default" onclick="validateModal('costcenter-myModal_{{$i}}');costcenterValidateModal('costcenter-myModal_{{$i}}')">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(isset($bill_bill_wise[$ledger_entries[$i]['id']]))
                        <div class="container">
                            <div class="modal fade billwise-modal" style="overflow-y:scroll;padding: 20px;" id="billwise_myModal_{{$i}}" role="dialog">
                                <div class="modal-dialog modal-lg">
                                <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" onclick="validateModal('billwise_myModal_{{$i}}');billwiseValidateModal('billwise_myModal_{{$i}}')">&times;</button>
                                            <h4 class="modal-title">Bill Wise Details</h4>
                                            <center>Upto : {{$currency}} <span id="billwise-total-amount_{{$i}}">{{$led_type=='cr'?$ledger_entries[$i]['amount']:$ledger_entries[$i]['amount']*(-1)}}</span> <span id="billwise-total-amount-type_{{$i}}" style="text-transform:capitalize">{{$led_type}}</span></center>
                                            <hr>
                                        </div>
                                        <div class="modal-body" style="overflow-y: auto;padding: 20px;height: 480px;position: relative;">
                                            <div class="table-responsive">
                                                <table class="table table-condensed billwise-table_{{$i}}">
                                                    <tr>
                                                        <th>Type of Ref</th>
                                                        <th>Name</th>
                                                        <th>Due Date, or credit days</th>
                                                        <th>Amount</th>
                                                        <th>Dr/Cr</th>
                                                    </tr>
                                                    <?php $x=0; ?>
                                                    @foreach($bill_bill_wise[$ledger_entries[$i]['id']] as $bill)
                                                    <tr <?php if($x==0){echo('id="billwise-newRow_'.$i.'"');}else{echo'';}?> >
                                                        <td>
                                                            <select required="required" class="select form-control input-css billwise " onchange="billwiseMethodTypeChange(this);validateModalFields('option',this)" name="billwise_method[{{$i}}][]" id="billwise_method_{{$i}}_{{$x}}">
                                                                <option value="advance" {{$bill['bill_type']=="advance"?'selected':''}}>Advance</option>
                                                                <option value="agst ref" {{$bill['bill_type']=="agst ref"?'selected':''}}>Agst Ref</option>
                                                                <option value="new ref" {{$bill['bill_type']=="new ref"?'selected':''}}>New Ref</option>
                                                                <option value="on account" {{$bill['bill_type']=="on account"?'selected':''}}>On Account</option>
                                                            </select>
                                                        <td>
                                                            <input required="required" type="text" style="{{$bill['bill_type']=="on account"?'display:none':''}}" class="input-css form-control billwise " onchange="validateModalFields('text',this)" name="billwise_name[{{$i}}][]" id="billwise_name_{{$i}}_{{$x}}" value="{{$bill['name']}}"></td>
                                                        <td>
                                                            <input required="required" type="text" style="{{$bill['bill_type']=="on account"?'display:none':''}}" class="input-css form-control billwise " onchange="validateModalFields('text',this)" name="billwise_due_date[{{$i}}][]" id="billwise_due_date_{{$i}}_{{$x}}" value="{{$bill['credit_period']}}"></td>
                                                        <td>
                                                            <input required="required" type="number" class="input-css form-control billwise billwise-amount_{{$i}} " onchange="validateModalFields('number',this);" name="billwise_amount[{{$i}}][]" id="billwise_amount_{{$i}}_{{$x}}" value="{{($bill['amount']<0)? ($bill['amount']*(-1)):$bill['amount']}}">
                                                        </td>
                                                        <td>
                                                            <select required="required" class="select form-control input-css billwise billwise-amount-type" onchange="billwiseNewRow(this,'{{$i}}');validateModalFields('option',this);" name="billwise_amount_type[{{$i}}][]" id="billwise_amount_type_{{$i}}_{{$x}}">
                                                                <option value="default">select type</option>
                                                                <option value="dr" {{($bill['amount']<0)? 'selected' :''}} >Dr</option>
                                                                <option value="cr" {{($bill['amount']<0)? '':'selected'}}>Cr</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <?php $x++; ?>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" onclick="validateModal('billwise_myModal_{{$i}}');billwiseValidateModal('billwise_myModal_{{$i}}')">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endif
            </div>
            {{-- this div will contain modal button --}}
            <div id="modal_div_button_{{$i}}" style="margin-top: 2rem;" class="form-group">
                @if(isset($cost_modal_list[$ledger_entries[$i]['id']]))
                    
                        <div class="col-md-2">
                            <button type="button" style="margin-right:1em" class="btn btn-primary btn-sm CostCenterToggleButton" data-toggle="modal" data-target="#costcenter-myModal_{{$i}}">Cost Allocations Details</button>
                        </div>
                        <div id="form_status-costcenter-myModal_{{$i}}_div" class="col-md-4">
                            <div class="col-md-4">
                                <label>Cost Center Error:</label>
                            </div>
                            <div class="col-md-8">
                                <input max="0" type="number" id="form_status-costcenter-myModal_{{$i}}" class="form_status form-control">
                            </div>
                        </div>
                    
                @endif
                @if(isset($bill_bill_wise[$ledger_entries[$i]['id']]))
                    
                        <div class="col-md-2">
                            <button type="button" style="margin-right:1em" class="btn btn-sm btn-primary BillWiseToggleButton" data-toggle="modal" data-target="#billwise_myModal_{{$i}}">Billwise Details</button>
                        </div>
                        <div id="form_status-billwise_myModal_{{$i}}_div" class="col-md-4">
                            <div class="col-md-4"><label>Billwise Errors:</label></div>
                            <div class="col-md-8">
                                <input id="form_status-billwise_myModal_{{$i}}" class="form_status form-control" max="0" type="number">
                            </div>
                        </div>
                    
                @endif
            </div>
        </div>
    @endif
    
@endfor
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
                <textarea type="text" name="narration" id="narration" class="input-css form-control narration">{{$voucher->narration}}</textarea>      
            @endif
        </div>
        <div id="total_amount" style="text-align:right" class="col-md-4">{{$total_amount}}
        </div>
            <input type="hidden" name="amount_{{$led_type=='dr'?'cr':'dr'}}[]" id="total_amt_elem" value="{{$total_amount}}">
    </div>
        <br>
        <br>

<script type="text/javascript">
    $(document).ready(function(){
       
    })
</script>