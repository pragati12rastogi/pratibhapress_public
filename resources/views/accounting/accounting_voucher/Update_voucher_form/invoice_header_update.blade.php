<div class="row">
    <div class="col-md-4">
        <label for="ref_no">Ref :</label>
        <input type="text" name="ref_no" id="ref_no" value="{{$voucher->reference}}" class="input-css form-control ref_no">
    </div>
</div>
<div class="row">
    <div style="text-align:center" class="col-md-2"><label for="">{{__('accounting/voucher.party ac name')}}</label>
    </div>
    <div class="col-md-3">
        <select class="select input-css form-control account" onchange="getInvoiceDetails('{{$ledger_type_name}}')" id="account" name="account" >                  
            <option value="default">Select Account</option>
            @foreach ($account as $key)
                <option value="{{$key->id}}" {{$voucher->party_name_ledger == $key->id ? "selected":""}}>{{$key->name}}</option>
            @endforeach        
        </select>
    </div>
    {{-- this div will contain modal --}}   
    <div id="invoice_detail_div">
        <div class="container">
            <div class="modal fade" id="invoiceDetail-myModal_0" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" onclick="validateModal('invoiceDetail-myModal_0');invoiceValidateModal('invoiceDetail-myModal_0');">&times;</button>
                            <h4 class="modal-title">
                                @if($ledger_type_name == "Sales")
                                    Sales Details
                                @elseif($ledger_type_name =='Purchase')
                                    Purchase Details
                                @endif
                            </h4>
                        </div>
                        <div class="modal-body">
                            @if($ledger_type_name == "Sales")
                                <div class="row" style="text-align:center">
                                    <div class="col-md-12">
                                        <label for="">Dispatch Details</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Delivery Note No.</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="col-md-6">
                                            <input type="text" name="basic_ship_delivery_note" id="basic_ship_delivery_note" class="form-control input-css"value ="{{$basic_details->basic_ship_delivery_note}}">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="basic_ship_delivery_date" onchange="validateModalFields('text',this);"  id="basic_ship_delivery_date" placeholder="date of delivery note" <?php if($basic_details->basic_ship_delivery_date != '1970-01-01'){
                                                echo("value='".date('d-m-Y',strtotime($basic_details->basic_ship_delivery_date))."'");
                                            }?> class="input-css form-control datepicker">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Dispatch Doc. No.</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basic_ship_document_no" id="dispatch_doc_no" class="input-css form-control" value="{{$voucher->basic_ship_document_no}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Dispatch Through</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basic_shipped_by" id="dispatch_through" class="input-css form-control" value="{{$voucher->basic_shipped_by}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Destination</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basic_final_destination" id="destination" class="input-css form-control" value="{{$voucher->basic_final_destination}}">
                                    </div>
                                </div>
                                <hr>
                                <div class="row" style="text-align:center">
                                    <div class="col-md-12">
                                        <label for="">Order Details</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Order No.</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="col-md-6">
                                            <input type="text" name="basic_purchase_order_no" id="basic_purchase_order_no" class="form-control input-css" value ="{{$basic_details->basic_purchase_order_no}}">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="basic_order_date" onchange="validateModalFields('text',this);" id="basic_order_date" placeholder="date of order" class="input-css form-control datepicker " @php if($basic_details->basic_order_date != '1970-01-01'){
                                                echo("value='".date('d-m-Y',strtotime($basic_details->basic_order_date))."'");
                                            }@endphp" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Mode/Terms of Payment</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basic_due_date_of_pymt" id="terms_of_pymt" class="input-css form-control" value="{{$voucher->basic_due_date_of_pymt}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Order Reference</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basic_order_ref_no" id="order_ref_no" class="input-css form-control" value="{{$voucher->basic_order_ref_no}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Terms of Delivery</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basic_order_terms" id="order_terms" class="input-css form-control" value="{{$voucher->basic_order_terms}}">
                                    </div>
                                </div>
                                <hr>
                                <div class="row" style="text-align:center">
                                    <div class="col-md-12">
                                        <label for="">Buyer's Details</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Buyers</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basic_buyer_name" required="required" onchange="validateModalFields('text',this);" id="buyers" class="form-control input-css" value="{{$voucher->basic_buyer_name}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">address</label>
                                    </div>
                                    <div class="col-md-8">
                                        <textarea name="address" id="address" cols="30" rows="2" class="form-control input-css" >{{$voucher->address}}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">TIN/Sales Tax Number</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basics_buyers_sales_tax_no" id="tin_no" class="form-control input-css" value="{{$voucher->basics_buyers_sales_tax_no}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">type of dealer</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="type_of_dealer" id="type_of_dealer" class="form-control input-css select">
                                            <option value="na" {{($voucher->type_of_dealer =="na" || $voucher->type_of_dealer =="")?'selected':''}}>Unknown</option>
                                            <option value="Composition" {{($voucher->type_of_dealer =="Composition")?'selected':''}}>Composition</option>
                                            <option value="Regular" {{($voucher->type_of_dealer =="Regular")?'selected':''}}>Regular</option>
                                            <option value="Unregistered" {{($voucher->type_of_dealer =="Unregistered")?'selected':''}}>Unregistered</option>
                                        </select>
                                    </div>
                                </div>
                                @elseif($ledger_type_name =='Purchase')
                                <div class="row">
                                    <div class="col-md-12" style="text-align:center">
                                        <label for="">Supplier's Details</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Supplier</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" required="required" onchange="validateModalFields('text',this)" name="basic_buyer_name" id="supplier" class="form-control input-css" value="{{$voucher->basic_buyer_name}}">
                                   </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">address</label>
                                    </div>
                                    <div class="col-md-8">
                                        <textarea name="address" id="address" cols="30" rows="2" class="form-control input-css">{{$voucher->address}}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">TIN/Sales Tax Number</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="basics_buyers_sales_tax_no" id="tin_no" class="form-control input-css" value="{{$voucher->basics_buyers_sales_tax_no}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">type of dealer</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="type_of_dealer" id="type_of_dealer" class="form-control input-css select">
                                            <option value="na" {{($voucher->type_of_dealer =="na" || $voucher->type_of_dealer =="")?'selected':''}}>Unknown</option>
                                            <option value="Composition" {{($voucher->type_of_dealer =="Composition")?'selected':''}}>Composition</option>
                                            <option value="Regular" {{($voucher->type_of_dealer =="Regular")?'selected':''}}>Regular</option>
                                            <option value="Unregistered" {{($voucher->type_of_dealer =="Unregistered")?'selected':''}}>Unregistered</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="validateModal('invoiceDetail-myModal_0');invoiceValidateModal('invoiceDetail-myModal_0');">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- this div will contain modal button --}}
    <div id="invoice_detail_button">
        <div class="col-md-2">
            <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#invoiceDetail-myModal_0" >Invoice Details</button>
        </div>
        <div id="form_status-invoiceDetail-myModal_0_div" class="col-md-4">
            <div class="col-md-4">
                <label>Invoice Detail Error:</label>
            </div>
            <div class="col-md-8">
                <input max="0" type="number" id="form_status-invoiceDetail-myModal_0" class="form_status form-control">
            </div>
        </div>
    </div>
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
                <option value="{{$key->id}}" {{$sel_sales_ledger->ledger_name==$key->id?"selected":""}}>{{$key->name}}</option>
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