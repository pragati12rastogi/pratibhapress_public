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
        <div  id="first_row" HIDDEN>
            <div class="row">  
                <div class="col-md-2">
                    <select id="amount_type_0" onchange="toggleJournalAmountType(this);addBankLedger(this)" name="amount_type[]" class="input-css select2 select form-control amount_type">
                            <option value="dr" {{$led_type=="dr"?'selected="selected"':''}}>Dr</option>
                        <option value="cr" {{$led_type=="cr"?'selected="selected"':''}}>Cr</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$led_type=="dr"?'dr':'cr'}}[]" id="particular_0" class="input-css select2 select form-control particular">
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
        </div>
        @for($i=0;$i< count($ledger_entries);$i++)
            <div  class="row">
                <div class="row">  
                    <div class="col-md-2">
                        <select id="amount_type_{{$i+1}}" onchange="toggleJournalAmountType(this);addBankLedger(this)" name="amount_type[]" class="input-css select2 select form-control amount_type">
                            <option value="dr" {{$ledger_entries[$i]['is_deemed_positive']==1?'selected="selected"':''}}>Dr</option>
                            <option value="cr" {{$ledger_entries[$i]['is_deemed_positive']==0?'selected="selected"':''}}>Cr</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$ledger_entries[$i]['is_deemed_positive']==1?'dr':'cr'}}[]" id="particular_{{$i+1}}" class="input-css select2 select form-control particular">
                            <option value="default">Select Particular</option>
                            @foreach ($ledger as $key)                                            
                            <option value="{{$key->id}}" {{$ledger_entries[$i]['ledger_name']==$key->id ?'selected':''}}>{{$key->name}}</option>
                            @endforeach
                            <option value="eol">End Of List</option>

                        </select>        
                    </div>
                    <div class="col-md-2">
                        @if($multinarrationsetting==1)
                            <textarea class="form-control input-css narration" name="narration[]" id="multi_narration" cols="30" rows="1">{{$ledger_entries[$i]['narration']}}</textarea>                    
                        @endif
                    </div>
                    <div class="col-md-2">
                        <div style="text-align:right;{{$ledger_entries[$i]['is_deemed_positive']!=1?'display:none;':''}}"  id="amount_dr_div_{{$i+1}}" >
                            <input type="number" min="0" value="{{$ledger_entries[$i]['is_deemed_positive'] == 1?$ledger_entries[$i]['amount']*(-1):0}}" id="amount_dr_{{$i+1}}" name="amount_dr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="debitAmount(this)" class="input-css form-control amount_dr">
                        </div> 
                    </div>
                    <div class="col-md-2">
                        <div style="text-align:right;{{$ledger_entries[$i]['is_deemed_positive']!=0?'display:none;':''}}" id="amount_cr_div_{{$i+1}}" >
                            <input type="number" min="0" value="{{$ledger_entries[$i]['is_deemed_positive'] == 0?$ledger_entries[$i]['amount']:0}}" id="amount_cr_{{$i+1}}" id="amount_cr_{{$i+1}}" name="amount_cr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="creditAmount(this)" class="input-css form-control amount_cr">
                        </div>
                    </div>
                </div> 
            </div>
            <div class="row">
                {{-- this div will contain modal --}}
                <div id="modal_div_{{$i+1}}" style="display:inline">
                    @if(isset($bill_bill_wise[$ledger_entries[$i]['id']]))
                            <div class="container">
                                <div class="modal fade billwise-modal" style="overflow-y:scroll;padding: 20px;" id="billwise_myModal_{{$i+1}}" role="dialog">
                                    <div class="modal-dialog modal-lg">
                                    <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" onclick="validateModal('billwise_myModal_{{$i+1}}');billwiseValidateModal('billwise_myModal_{{$i+1}}')">&times;</button>
                                                <h4 class="modal-title">Bill Wise Details</h4>
                                                <center>Upto : {{$currency}} <span id="billwise-total-amount_{{$i+1}}">{{$ledger_entries[$i]['is_deemed_positive']==0?$ledger_entries[$i]['amount']:$ledger_entries[$i]['amount']*(-1)}}</span> <span id="billwise-total-amount-type_{{$i+1}}" style="text-transform:capitalize">{{$ledger_entries[$i]['is_deemed_positive']==0?'Cr':'Dr'}}</span></center>
                                                <hr>
                                            </div>
                                            <div class="modal-body" style="overflow-y: auto;padding: 20px;height: 480px;position: relative;">
                                                <div class="table-responsive">
                                                    <table class="table table-condensed billwise-table_{{$i+1}}">
                                                        <tr>
                                                            <th>Type of Ref</th>
                                                            <th>Name</th>
                                                            <th>Due Date, or credit days</th>
                                                            <th>Amount</th>
                                                            <th>Dr/Cr</th>
                                                        </tr>
                                                        <?php $x=0; ?>
                                                        @foreach($bill_bill_wise[$ledger_entries[$i]['id']] as $bill)
                                                        <tr <?php if($x==0){echo('id="billwise-newRow_'.($i+1).'"');}else{echo'';}?> >
                                                            <td>
                                                                <select required="required" class="select form-control input-css billwise " onchange="billwiseMethodTypeChange(this);validateModalFields('option',this)" name="billwise_method[{{$i+1}}][]" id="billwise_method_{{$i+1}}_{{$x}}">
                                                                    <option value="advance" {{$bill['bill_type']=="advance"?'selected':''}}>Advance</option>
                                                                    <option value="agst ref" {{$bill['bill_type']=="agst ref"?'selected':''}}>Agst Ref</option>
                                                                    <option value="new ref" {{$bill['bill_type']=="new ref"?'selected':''}}>New Ref</option>
                                                                    <option value="on account" {{$bill['bill_type']=="on account"?'selected':''}}>On Account</option>
                                                                </select>
                                                            <td>
                                                                <input required="required" type="text" style="{{$bill['bill_type']=="on account"?'display:none':''}}" class="input-css form-control billwise " onchange="validateModalFields('text',this)" name="billwise_name[{{$i+1}}][]" id="billwise_name_{{$i+1}}_{{$x}}" value="{{$bill['name']}}"></td>
                                                            <td>
                                                                <input required="required" type="text" style="{{$bill['bill_type']=="on account"?'display:none':''}}" class="input-css form-control billwise " onchange="validateModalFields('text',this)" name="billwise_due_date[{{$i+1}}][]" id="billwise_due_date_{{$i+1}}_{{$x}}" value="{{$bill['credit_period']}}"></td>
                                                            <td>
                                                                <input required="required" type="number" class="input-css form-control billwise billwise-amount_{{$i+1}} " onchange="validateModalFields('number',this);" name="billwise_amount[{{$i+1}}][]" id="billwise_amount_{{$i+1}}_{{$x}}" value="{{($bill['amount']<0)? ($bill['amount']*(-1)):$bill['amount']}}">
                                                            </td>
                                                            <td>
                                                                <select required="required" class="select form-control input-css billwise billwise-amount-type" onchange="billwiseNewRow(this,'{{$i+1}}');validateModalFields('option',this);" name="billwise_amount_type[{{$i+1}}][]" id="billwise_amount_type_{{$i+1}}_{{$x}}">
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
                                                <button type="button" class="btn btn-default" onclick="validateModal('billwise_myModal_{{$i+1}}');billwiseValidateModal('billwise_myModal_{{$i+1}}')">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endif
                    @if(isset($cost_modal_list[$ledger_entries[$i]['id']]))
                        <div class="container">
                            <div class="modal fade" id="costcenter-myModal_{{$i+1}}" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" onclick="validateModal('costcenter-myModal_{{$i+1}}')">&times;</button>
                                            <h4 class="modal-title">Cost Allocations</h4>
                                            <center>Upto : {{$currency}} <span id="costcenter-total-amount_{{$i+1}}">{{$ledger_entries[$i]['is_deemed_positive']==0?$ledger_entries[$i]['amount']:$ledger_entries[$i]['amount']*(-1)}}</span> <span id="costcenter-total-amount-type_{{$i+1}}" style="text-transform:capitalize">{{$ledger_entries[$i]['is_deemed_positive']==0?'Cr':'Dr'}}</span></center>
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
                                                            <select required="required" class="select form-control input-css costcenter" onchange="costcenterCategoryTypeChange(this);checkStringEndOfList(this);validateModalFields('option',this)" name="costcenter_category[{{$i}}][]" id="costcenter_category_{{$i+1}}">
                                                                <option value="default">Select Cost Category</option>
                                                                    @foreach($costcategories as $cc)
                                                                        <option value="{{$cc['id']}}" {{$cost['cost_category_id']==$cc['id']?'selected':''}}>{{$cc['name']}}</option>
                                                                    @endforeach
                                                                    <option value="eol">End Of List</option>

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
                                            <button type="button" class="btn btn-default" onclick="validateModal('costcenter-myModal_{{$i+1}}');costcenterValidateModal('costcenter-myModal_{{$i+1}}')">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                {{-- this div will contain modal button --}}
                <div id="modal_div_button_{{$i+1}}" style="margin-top: 2rem;" class="form-group row">
                    @if(isset($bill_bill_wise[$ledger_entries[$i]['id']]))
                        
                            <div class="col-md-2">
                                <button type="button" style="margin-right:1em" class="btn btn-sm btn-primary BillWiseToggleButton" data-toggle="modal" data-target="#billwise_myModal_{{$i+1}}">Billwise Details</button>
                            </div>
                            <div id="form_status-billwise_myModal_{{$i+1}}_div" class="col-md-4">
                                <div class="col-md-4"><label>Billwise Errors:</label></div>
                                <div class="col-md-8">
                                    <input id="form_status-billwise_myModal_{{$i+1}}" class="form_status form-control" max="0" type="number">
                                </div>
                            </div>
                    @endif
                    @if(isset($cost_modal_list[$ledger_entries[$i]['id']]))
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
                    @endif
                </div>
            </div>
        @endfor
    @elseif($relation=='1ton')
        {{-- content inside #first_row will replicate and all child element's id should end with '_1' --}}
        <div  id="first_row" HIDDEN>
            <div class="row">  
                <div class="col-md-1">
                    <select id="amount_type_0" onchange="toggleJournalAmountType(this)" name="amount_type[]" class="input-css select2 select form-control amount_type">
                            <option value="{{$led_type=="dr"?'cr':'dr'}}">{{$led_type=="dr"?'Cr':'Dr'}}</option>
                            <option value="{{$led_type=="dr"?'dr':'cr'}}">{{$led_type=="dr"?'Dr':'Cr'}}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$led_type=="dr"?'cr':'dr'}}[]" id="particular_0" class="input-css select2 select form-control particular">
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
                    <div style="text-align:right;{{$led_type=="dr"?'display:none;':''}}"  id="amount_dr_div_0" >
                        <input type="number" min="0" value="0" id="amount_dr_0" name="amount_dr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="debitAmount(this)" class="input-css form-control amount_dr">
                    </div>
                    
                </div>
                <div class="col-md-2">
                    <div style="text-align:right;{{$led_type=="cr"?'display:none;':''}}" id="amount_cr_div_0" >
                        <input type="number" min="0" value="0" id="amount_cr_0" name="amount_cr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="creditAmount(this)" class="input-css form-control amount_cr">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">  
            <div class="col-md-1">
                <select id="amount_type_1" onchange="toggleJournalAmountType(this);" name="amount_type[]" class="input-css select2 select form-control amount_type">
                        <option value="{{$led_type}}">{{ucwords($led_type)}}</option>
                </select>
            </div>
            <div class="col-md-4">
                <select type="text" onchange="addBankLedger(this)" name="account_{{$led_type}}[]" id="particular_1" class="input-css select2 select form-control particular">
                    <option value="default">Select Particular</option>
                    @foreach ($account as $key)                                            
                    <option value="{{$key->id}}" {{$ledger_entries[0]['ledger_name']==$key->id ?'selected':''}}>{{$key->name}}</option>
                    @endforeach 
                </select>        
            </div>
            <div class="col-md-3">
                @if($multinarrationsetting==1)
                    <textarea class="form-control input-css narration" name="narration[]" id="multi_narration" cols="30" rows="1">{{$ledger_entries[0]['narration']}}</textarea>                    
                @endif
            </div>
            <div class="col-md-2">
                <div style="text-align:right;{{$ledger_entries[0]['is_deemed_positive']!=1?'display:none;':''}}"  id="amount_dr_div_1" >
                    <input type="number" min="0" value="{{$ledger_entries[0]['is_deemed_positive'] == 1?$ledger_entries[0]['amount']*(-1):0}}" id="amount_dr_1" name="amount_dr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="debitAmount(this)" class="input-css form-control amount_dr">
                </div>
                
            </div>
            <div class="col-md-2">
                <div style="text-align:right;{{$ledger_entries[0]['is_deemed_positive']!=0?'display:none;':''}}" id="amount_cr_div_1" >
                    <input type="number" min="0" value="{{$ledger_entries[0]['is_deemed_positive'] == 0?$ledger_entries[0]['amount']:0}}" id="amount_cr_1" name="amount_cr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="creditAmount(this)" class="input-css form-control amount_cr">
                </div>
            </div>
        </div>
        <div class="row">
            {{-- this div will contain modal --}}
            <div id="modal_div_1" style="display:inline">
                @if(isset($cost_modal_list[$ledger_entries[0]['id']]))
                    <div class="container">
                        <div class="modal fade" id="costcenter-myModal_1" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" onclick="validateModal('costcenter-myModal_1')">&times;</button>
                                        <h4 class="modal-title">Cost Allocations</h4>
                                        <center>Upto : {{$currency}} <span id="costcenter-total-amount_1">{{$ledger_entries[0]['is_deemed_positive']==0?$ledger_entries[0]['amount']:$ledger_entries[0]['amount']*(-1)}}</span> <span id="costcenter-total-amount-type_1" style="text-transform:capitalize">{{$ledger_entries[0]['is_deemed_positive']==0?'Cr':'Dr'}}</span></center>
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
                                                @foreach($cost_modal_list[$ledger_entries[0]['id']] as $cost)
                                                <tr id="costcenter-newRow">
                                                    <td>
                                                        <select required="required" class="select form-control input-css costcenter" onchange="costcenterCategoryTypeChange(this);checkStringEndOfList(this);validateModalFields('option',this)" name="costcenter_category[0][]" id="costcenter_category_1">
                                                            <option value="default">Select Cost Category</option>
                                                                @foreach($costcategories as $cc)
                                                                    <option value="{{$cc['id']}}" {{$cost['cost_category_id']==$cc['id']?'selected':''}}>{{$cc['name']}}</option>
                                                                @endforeach
                                                                <option value="eol">End Of List</option>

                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select required="required" class="select form-control input-css costcenter costcenter-name-1" onchange="validateModalFields('option',this)"  name="costcenter_name[0][]" id="costcenter_name_1">
                                                            <option value="default">Select cost category</option>
                                                            @foreach($cost_center[$ledger_entries[0]['id']][0] as $cost_cente)
                                                                <option value="{{$cost_cente['id']}}"{{$cost_cente['id']==$cost['cost_center_id']?'selected':''}}>{{$cost_cente['name']}}</option>
                                                            @endforeach
                                                           
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input required="required" type="number" class="input-css form-control costcenter costcenter-amount" onchange="validateModalFields('number',this)" name="costcenter_amount[0][]" id="costcenter_amount_1" value="{{$cost['amount']}}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">      
                                        <button type="button" class="btn btn-default" onclick="validateModal('costcenter-myModal_1');costcenterValidateModal('costcenter-myModal_1')">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(isset($bill_bill_wise[$ledger_entries[0]['id']]))
                    <div class="container">
                        <div class="modal fade billwise-modal" style="overflow-y:scroll;padding: 20px;" id="billwise_myModal_1" role="dialog">
                            <div class="modal-dialog modal-lg">
                            <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" onclick="validateModal('billwise_myModal_1');billwiseValidateModal('billwise_myModal_1')">&times;</button>
                                        <h4 class="modal-title">Bill Wise Details</h4>
                                        <center>Upto : {{$currency}} <span id="billwise-total-amount_1">{{$ledger_entries[0]['is_deemed_positive']==0?$ledger_entries[0]['amount']:$ledger_entries[0]['amount']*(-1)}}</span> <span id="billwise-total-amount-type_1" style="text-transform:capitalize">{{$ledger_entries[0]['is_deemed_positive']==0?'Cr':'Dr'}}</span></center>
                                        <hr>
                                    </div>
                                    <div class="modal-body" style="overflow-y: auto;padding: 20px;height: 480px;position: relative;">
                                        <div class="table-responsive">
                                            <table class="table table-condensed billwise-table_1">
                                                <tr>
                                                    <th>Type of Ref</th>
                                                    <th>Name</th>
                                                    <th>Due Date, or credit days</th>
                                                    <th>Amount</th>
                                                    <th>Dr/Cr</th>
                                                </tr>
                                                <?php $x=0; ?>
                                                @foreach($bill_bill_wise[$ledger_entries[0]['id']] as $bill)
                                                <tr <?php if($x==0){echo('id="billwise-newRow_1"');}else{echo'';}?> >
                                                    <td>
                                                        <select required="required" class="select form-control input-css billwise " onchange="billwiseMethodTypeChange(this);validateModalFields('option',this)" name="billwise_method[1][]" id="billwise_method_1_{{$x}}">
                                                            <option value="advance" {{$bill['bill_type']=="advance"?'selected':''}}>Advance</option>
                                                            <option value="agst ref" {{$bill['bill_type']=="agst ref"?'selected':''}}>Agst Ref</option>
                                                            <option value="new ref" {{$bill['bill_type']=="new ref"?'selected':''}}>New Ref</option>
                                                            <option value="on account" {{$bill['bill_type']=="on account"?'selected':''}}>On Account</option>
                                                        </select>
                                                    <td>
                                                        <input required="required" type="text" style="{{$bill['bill_type']=="on account"?'display:none':''}}" class="input-css form-control billwise " onchange="validateModalFields('text',this)" name="billwise_name[1][]" id="billwise_name_1_{{$x}}" value="{{$bill['name']}}"></td>
                                                    <td>
                                                        <input required="required" type="text" style="{{$bill['bill_type']=="on account"?'display:none':''}}" class="input-css form-control billwise " onchange="validateModalFields('text',this)" name="billwise_due_date[1][]" id="billwise_due_date_1_{{$x}}" value="{{$bill['credit_period']}}"></td>
                                                    <td>
                                                        <input required="required" type="number" class="input-css form-control billwise billwise-amount_1 " onchange="validateModalFields('number',this);" name="billwise_amount[1][]" id="billwise_amount_1_{{$x}}" value="{{($bill['amount']<0)? ($bill['amount']*(-1)):$bill['amount']}}">
                                                    </td>
                                                    <td>
                                                        <select required="required" class="select form-control input-css billwise billwise-amount-type" onchange="billwiseNewRow(this,'1');validateModalFields('option',this);" name="billwise_amount_type[1][]" id="billwise_amount_type_1_{{$x}}">
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
                                        <button type="button" class="btn btn-default" onclick="validateModal('billwise_myModal_1');billwiseValidateModal('billwise_myModal_1')">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            {{-- this div will contain modal button --}}
            <div id="modal_div_button_1" style="margin-top: 2rem;" class="form-group row">
                @if(isset($bill_bill_wise[$ledger_entries[0]['id']]))
                    
                        <div class="col-md-2">
                            <button type="button" style="margin-right:1em" class="btn btn-sm btn-primary BillWiseToggleButton" data-toggle="modal" data-target="#billwise_myModal_1">Billwise Details</button>
                        </div>
                        <div id="form_status-billwise_myModal_1_div" class="col-md-4">
                            <div class="col-md-4"><label>Billwise Errors:</label></div>
                            <div class="col-md-8">
                                <input id="form_status-billwise_myModal_1" class="form_status form-control" max="0" type="number">
                            </div>
                        </div>
                @endif
                @if(isset($cost_modal_list[$ledger_entries[0]['id']]))
                    <div class="col-md-2">
                        <button type="button" style="margin-right:1em" class="btn btn-primary btn-sm CostCenterToggleButton" data-toggle="modal" data-target="#costcenter-myModal_1">Cost Allocations Details</button>
                    </div>
                    <div id="form_status-costcenter-myModal_1_div" class="col-md-4">
                        <div class="col-md-4">
                            <label>Cost Center Error:</label>
                        </div>
                        <div class="col-md-8">
                            <input max="0" type="number" id="form_status-costcenter-myModal_1" class="form_status form-control">
                        </div>
                    </div>   
                @endif
            </div>
        </div>
        @for($i=1;$i< count($ledger_entries);$i++)
            <div class="row">
                <div class="row">  
                    <div class="col-md-1">
                            <select id="amount_type_{{$i+1}}" onchange="toggleJournalAmountType(this)" name="amount_type[]" class="input-css select2 select form-control amount_type">
                                    <option value="{{$led_type=='dr'?'cr':'dr'}}"{{($ledger_entries[$i]['is_deemed_positive']==0 && $led_type=="dr")?'selected="selected"':''}}>{{$led_type=="dr"?'Cr':'Dr'}}</option>
                                    <option value="{{$led_type=='dr'?'dr':'cr'}}"{{($ledger_entries[$i]['is_deemed_positive']==1 && $led_type=="dr")?'selected="selected"':''}}>{{$led_type=="dr"?'Dr':'Cr'}}</option>
                            </select>
                        
                    </div>
                    <div class="col-md-4">
                        <select type="text" onchange="addBankLedger(this);checkStringEndOfList(this);" name="account_{{$ledger_entries[$i]['is_deemed_positive']==1?'dr':'cr'}}[]" id="particular_{{$i+1}}" class="input-css select2 select form-control particular">
                            <option value="default">Select Particular</option>
                            @foreach ($ledger as $key)                                            
                                <option value="{{$key->id}}"{{$ledger_entries[$i]['ledger_name']==$key->id ?'selected':''}}>{{$key->name}}</option>
                            @endforeach
                            <option value="eol">End Of List</option>

                        </select>        
                    </div>
                    <div class="col-md-3">
                        @if($multinarrationsetting==1)
                            <textarea class="form-control input-css narration" name="narration[]" id="multi_narration" cols="30" rows="1">{{$ledger_entries[$i]['narration']}}</textarea>                    
                        @endif
                    </div>
                    <div class="col-md-2">
                        <div style="text-align:right;{{$ledger_entries[$i]['is_deemed_positive']!=1?'display:none;':''}}"  id="amount_dr_div_{{$i+1}}" >
                            <input type="number" min="0" value="{{$ledger_entries[$i]['is_deemed_positive'] == 1?$ledger_entries[$i]['amount']*(-1):0}}" id="amount_dr_{{$i+1}}" name="amount_dr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="debitAmount(this)" class="input-css form-control amount_dr">
                        </div>
                        
                    </div>
                    <div class="col-md-2">
                        <div style="text-align:right;{{$ledger_entries[$i]['is_deemed_positive']!=0?'display:none;':''}}" id="amount_cr_div_{{$i+1}}" >
                            <input type="number" min="0" value="{{$ledger_entries[$i]['is_deemed_positive'] == 0?$ledger_entries[$i]['amount']:0}}" id="amount_cr_{{$i+1}}" name="amount_cr[]" onfocus="focusAmount(this)" onblur="blurAmount(this)" onchange="creditAmount(this)" class="input-css form-control amount_cr">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- this div will contain modal --}}   
                <div id="modal_div_{{$i+1}}" style="display:inline">
                    @if(isset($cost_modal_list[$ledger_entries[$i]['id']]))
                        <div class="container">
                            <div class="modal fade" id="costcenter-myModal_{{$i+1}}" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" onclick="validateModal('costcenter-myModal_{{$i+1}}')">&times;</button>
                                            <h4 class="modal-title">Cost Allocations</h4>
                                            <center>Upto : {{$currency}} <span id="costcenter-total-amount_{{$i+1}}">{{$ledger_entries[$i]['is_deemed_positive']==0?$ledger_entries[$i]['amount']:$ledger_entries[$i]['amount']*(-1)}}</span> <span id="costcenter-total-amount-type_{{$i+1}}" style="text-transform:capitalize">{{$ledger_entries[$i]['is_deemed_positive']==0?'Cr':'Dr'}}</span></center>
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
                                                            <select required="required" class="select form-control input-css costcenter" onchange="costcenterCategoryTypeChange(this);checkStringEndOfList(this);validateModalFields('option',this)" name="costcenter_category[{{$i}}][]" id="costcenter_category_{{$i}}">
                                                                <option value="default">Select Cost Category</option>
                                                                    @foreach($costcategories as $cc)
                                                                        <option value="{{$cc['id']}}" {{$cost['cost_category_id']==$cc['id']?'selected':''}}>{{$cc['name']}}</option>
                                                                    @endforeach
                                                                    <option value="eol">End Of List</option>

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
                                            <button type="button" class="btn btn-default" onclick="validateModal('costcenter-myModal_{{$i+1}}');costcenterValidateModal('costcenter-myModal_{{$i+1}}')">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(isset($bill_bill_wise[$ledger_entries[$i]['id']]))
                        <div class="container">
                            <div class="modal fade billwise-modal" style="overflow-y:scroll;padding: 20px;" id="billwise_myModal_{{$i+1}}" role="dialog">
                                <div class="modal-dialog modal-lg">
                                <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" onclick="validateModal('billwise_myModal_{{$i+1}}');billwiseValidateModal('billwise_myModal_{{$i+1}}')">&times;</button>
                                            <h4 class="modal-title">Bill Wise Details</h4>
                                            <center>Upto : {{$currency}} <span id="billwise-total-amount_{{$i+1}}">{{$ledger_entries[$i]['is_deemed_positive']==0?$ledger_entries[$i]['amount']:$ledger_entries[$i]['amount']*(-1)}}</span> <span id="billwise-total-amount-type_{{$i+1}}" style="text-transform:capitalize">{{$ledger_entries[$i]['is_deemed_positive']==0?'Cr':'Dr'}}</span></center>
                                            <hr>
                                        </div>
                                        <div class="modal-body" style="overflow-y: auto;padding: 20px;height: 480px;position: relative;">
                                            <div class="table-responsive">
                                                <table class="table table-condensed billwise-table_{{$i+1}}">
                                                    <tr>
                                                        <th>Type of Ref</th>
                                                        <th>Name</th>
                                                        <th>Due Date, or credit days</th>
                                                        <th>Amount</th>
                                                        <th>Dr/Cr</th>
                                                    </tr>
                                                    <?php $x=0; ?>
                                                    @foreach($bill_bill_wise[$ledger_entries[$i]['id']] as $bill)
                                                    <tr <?php if($x==0){echo('id="billwise-newRow_'.($i+1).'"');}else{echo'';}?> >
                                                        <td>
                                                            <select required="required" class="select form-control input-css billwise " onchange="billwiseMethodTypeChange(this);validateModalFields('option',this)" name="billwise_method[{{$i+1}}][]" id="billwise_method_{{$i+1}}_{{$x}}">
                                                                <option value="advance" {{$bill['bill_type']=="advance"?'selected':''}}>Advance</option>
                                                                <option value="agst ref" {{$bill['bill_type']=="agst ref"?'selected':''}}>Agst Ref</option>
                                                                <option value="new ref" {{$bill['bill_type']=="new ref"?'selected':''}}>New Ref</option>
                                                                <option value="on account" {{$bill['bill_type']=="on account"?'selected':''}}>On Account</option>
                                                            </select>
                                                        <td>
                                                            <input required="required" type="text" style="{{$bill['bill_type']=="on account"?'display:none':''}}" class="input-css form-control billwise " onchange="validateModalFields('text',this)" name="billwise_name[{{$i+1}}][]" id="billwise_name_{{$i+1}}_{{$x}}" value="{{$bill['name']}}"></td>
                                                        <td>
                                                            <input required="required" type="text" style="{{$bill['bill_type']=="on account"?'display:none':''}}" class="input-css form-control billwise " onchange="validateModalFields('text',this)" name="billwise_due_date[{{$i+1}}][]" id="billwise_due_date_{{$i+1}}_{{$x}}" value="{{$bill['credit_period']}}"></td>
                                                        <td>
                                                            <input required="required" type="number" class="input-css form-control billwise billwise-amount_{{$i+1}} " onchange="validateModalFields('number',this);" name="billwise_amount[{{$i+1}}][]" id="billwise_amount_{{$i+1}}_{{$x}}" value="{{($bill['amount']<0)? ($bill['amount']*(-1)):$bill['amount']}}">
                                                        </td>
                                                        <td>
                                                            <select required="required" class="select form-control input-css billwise billwise-amount-type" onchange="billwiseNewRow(this,'{{$i+1}}');validateModalFields('option',this);" name="billwise_amount_type[{{$i+1}}][]" id="billwise_amount_type_{{$i+1}}_{{$x}}">
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
                                            <button type="button" class="btn btn-default" onclick="validateModal('billwise_myModal_{{$i+1}}');billwiseValidateModal('billwise_myModal_{{$i+1}}')">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                {{-- this div will contain modal button --}}
                <div id="modal_div_button_{{$i+1}}" style="margin-top: 2rem;" class="form-group row">
                    @if(isset($bill_bill_wise[$ledger_entries[$i]['id']]))
                        
                            <div class="col-md-2">
                                <button type="button" style="margin-right:1em" class="btn btn-sm btn-primary BillWiseToggleButton" data-toggle="modal" data-target="#billwise_myModal_{{$i+1}}">Billwise Details</button>
                            </div>
                            <div id="form_status-billwise_myModal_{{$i+1}}_div" class="col-md-4">
                                <div class="col-md-4"><label>Billwise Errors:</label></div>
                                <div class="col-md-8">
                                    <input id="form_status-billwise_myModal_{{$i+1}}" class="form_status form-control" max="0" type="number">
                                </div>
                            </div>
                    @endif
                    @if(isset($cost_modal_list[$ledger_entries[$i]['id']]))
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
                    @endif
                </div>
            </div>
        @endfor
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
                <textarea type="text" name="narration" id="narration" class="input-css form-control narration">{{$voucher->narration}}</textarea>      
            @endif    
        </div>
        <div class="col-md-2"></div>
        <div id="debit_total_amount" style="text-align:right" class="col-md-2">{{$total_debit}}
        </div>
        <div id="credit_total_amount" style="text-align:right" class="col-md-2">{{$total_credit}}
        </div>
        <div id="relation" hidden style="text-align:right" class="col-md-2">{{$relation}}</div>
        </div>
        <br>
        <br>