
@extends($layout)

@section('title', __(''))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('')}}</i></a></li>
 @endsection
@section('css')
<style>
    /* css for adding differentiation between focused and non focused element */
    .input-css:focus, .select2-search__field:focus{
        background-color: #FAEF9F !important;
    }
</style>
@endsection

@section('js')
<script src="/js/accounting/voucherEntry.js"></script>
<script>
    // definiing global variable 
    var fullledger, ledger, costcategories,ledger_costcategories=[],pymt_by_check_div=0;
    function removeUnWantedForm(ids,selected_id){
        /*
        * function to disable oprion of form type.
        * ids = , SEPERATED id of option to be disabled.
        * selected_id = option to be selected.
        */

        id = ids.split(',');
        $('.form_type_class').each(function(){
            console.log(this);
            $(this).removeAttr('disabled');
        })
        for(i=0;i<id.length;i++)
        {
            $($('.form_type_class')[id[i]]).attr('disabled','disabled');
            console.log($('.form_type_class')[id[i]]);
        }
        $('.form_type').val(selected_id);
        // add select2 to select element with class 'input-css'
        $('.form_type').select2({
            containerCssClass:"input-css"
        });
    }

    function getVoucherForm(formtype)
    {
        /*
        * function to load new form of voucher entry
        * formtype = value of formtype for which the page will open
        */
        // validating aganinst no value of voucher type
        if($('#voucherType').val()!='default')
        {
            // showing the loader
            $('#ajax_loader_div').css('display','block');
            // api for getting new form
            $.ajax({
                url: "/accounting/voucher/form/"+ $('#voucherType').val() +'/'+formtype ,
                type: "GET",
                success: function(result) {
                    console.log(result);
                    // create field for voucher number if voucher type has manual numbering.
                    if(result.voucherNumber=='manual')
                    {
                        $('#voucherNumberName').html('<input type="text" name="voucher_number" id="voucherNumberElem">');
                    }
                    else
                    {
                        $('#voucherNumber1').html(result.voucherNumber);
                        $('#voucherNumberElem').val(result.voucherNumber);
                    }
                    // display effective date div is ledger setting
                    if(result.effective_datesetting)
                        $('.effectiveDateDiv').css('display','block')
                    // removing unwanted form type.
                    removeUnWantedForm(result.notAllowedForm,result.form_type);
                    // append header of form
                    $('#voucherEntryFormAccount').html(result.voucher_account_layout);
                    // append body of form
                    $('#voucherEntryFormParticular').html(result.voucher_particular_layout);
                    // hiding the loader
                    $('#ajax_loader_div').css('display','none');
                    fullledger = result.fullledg;
                    ledger = result.ledger;
                    costcategories = result.costcategories;
                    // add select2 to select element with class 'input-css'
                    $('select').select2({
                        containerCssClass:"input-css"
                    });
                    $('.datepicker').datepicker({
                        autoclose: true,
                        format: 'd-m-yyyy'

                    }).datepicker();
                }
            });
        }
    }
    $(document).ready(function(){
        // add select2 to select element with class 'input-css'
        $('.select').select2({
            containerCssClass:"input-css"
        });
        $(".billwise-modal").on('hide.bs.modal', function(){
            console.log(this.id);      
        });
        $('#voucherType').change(function(e)
        {
            getVoucherForm("default");
        });
        $('#form_type').change(function(e)
        {
            getVoucherForm($('#form_type').val());
        });
    });
    function addBankLedger(element){
        /*
        * functionality to be checked.
        * function to add check details div if any bank ledger is available in particular.
        * element =  element which fires the function 
        **/
        if(pymt_by_check_div==0)//pymt_by_check_div is hidden
        {
            console.log('pymt_by_check_div',pymt_by_check_div);
            var val = '_';
            $(document).find($('.account')).each(function(){
                val+=$(this).val()+'_';
            });
            $(document).find($('.particular')).each(function(){
                val+=$(this).val()+'_';
            });
            // api to check whether the there is any particular eligible for bank details.
            $.ajax({
                url: "/accounting/voucher/checkbankledger/"+ val,
                type: "GET",
                success: function(result) {
                    if(result==1)// eligible
                    {
                        $(document).find($('#pymt_by_cheque_div')).css('display','block');
                        pymt_by_check_div=1;
                    }   
                    else
                    {
                        $(document).find($('#pymt_by_cheque_div')).css('display','none');
                        pymt_by_check_div=0;    
                    }  
                    // add select2 to select element with class 'input-css'
                    $('.select').select2({
                        containerCssClass:"input-css"
                    });
                }
            });
        }
    }
    function toggleJournalAmountType(element)
    { 
        // debugger
        /*
        * function to toggle the amount field on change of amount type
        * element = element which fires the request
        */
        // taking row num out from id 
        var id = element.id.split("_")[element.id.split("_").length-1];
        var value = element.value;
        if(value=='dr')
        {
            // display dr amount field
            $('#amount_dr_div_'+id).css('display','block');
            $('#amount_cr_div_'+id).css('display','none');
            $('#particular_'+id).attr('name','account_dr[]');            
        }
        else
        {
            // display cr amount field
            $('#amount_dr_div_'+id).css('display','none');
            $('#amount_cr_div_'+id).css('display','block');
            $('#particular_'+id).attr('name','account_cr[]');
           
        }
    }
    function debitAmount(ele)
    {
        /*
        * function to find total amount of dr type
        * ele = element which fires the request
        */
        var amt = 0;
        $(document).find($('.amount_dr')).each(function(){   
            if((!isNaN(this.value) || this.value!='') && this.value>0)  
                amt += parseFloat(this.value);
        });
        $(document).find($('#debit_total_amount')).html(amt);
    }
    function creditAmount(ele)
    {
        /*
        * function to find total amount of cr type
        * ele = element which fires the request
        */
        var amt = 0;
        $(document).find($('.amount_cr')).each(function(){
            if((!isNaN(this.value) || this.value!='') && this.value>0)  
                amt += parseFloat(this.value);
        });
        $(document).find($('#credit_total_amount')).html(amt);
    }
    
    function focusAmount(ele)
    {
        // debugger
        /*
        * function to focus particular when amount is focus without setting particular value
        * ele = element which fires the request
        */
        id = ele.id;

        id = id.toString().split("_")[id.toString().split("_").length-1];
        var values = $('#particular_'+id).val();
        if(values==="default")
        {
            $('#particular_'+id).focus();
        }
    }   
    function focusAmountItem(ele)
    {   
        // debugger;
        /*
        * function to focus item when amount is focus without setting particular item value
        * ele = element which fires the request
        */
        var id = ele.id;
        var id = id.toString().split("_")[2];
        var values = $('#particular_item_'+id).val();
        if(values==="default")
        {
            $('#particular_item_'+id).focus();
        }
    }   
    
    function changeAmount()
    {   
        // debugger
        /*
        * function to calculate amount on amount change
        */
        var amt = 0;
        $(document).find($('.amount')).each(function(){  
            if(!isNaN(parseFloat(this.value)))
                amt += parseFloat(this.value);
        });
        console.log('amount',amt);
        $(document).find($('#total_amount')).html(amt);
        $(document).find($('#total_amt_elem')).val(amt);
    } 
    // function changeAmountInvoice()
    // {   debugger
    //     /*
    //     * function to calculate amount on amount change
    //     */
    //     var amt = 0;
    //     $(document).find($('.tax_amount')).each(function(){  
    //         if(!isNaN(parseFloat(this.value)))
    //             amt += parseFloat(this.value);
    //     });
    //     console.log('tax_amount',amt);
    //     $(document).find($('#total_amount')).html(amt);
    //     $(document).find($('#total_amt_elem')).val(amt);
    // }
    function calcTax(element)
    {
        /*
        * function to calculate total tax of amount.
        * element = element which fires the request
        */
        var value = element.value;
        var id_num = parseInt(element.id.split("_")[element.id.split("_").length-1]);
        var rate =  parseFloat($(document).find($('#tax_rate_'+id_num)).val());
        var taxable_amt = $(document).find($('#total_amt_elem')).val();
        var tax_rate = (rate / 100) * taxable_amt ;
        $(document).find($('#tax_amount_'+id_num)).val(tax_rate);
        var amt = taxable_amt;
        console.log('amt',amt,taxable_amt);
        $(document).find($('.tax_amount')).each(function(){  
            if(!isNaN(parseFloat(this.value)))
                amt = parseFloat(amt) +  (parseFloat(this.value));
        });
        $(document).find($('#total_amount_tax')).html('<label>Total : <label>'+amt);
        $(document).find($('#total_amount_tax_inp')).val(amt);
    }
    function billwiseMethodTypeChange(element)
    {
        /*
        * function to toggle method type fields of billwise modal.
        * element = element which fires the request
        */
        var value = element.value;
        // getting row number of the element modal parent
        var row_num = parseInt(element.id.split("_")[element.id.split("_").length-2]);
        // getting id number of the element 
        var id_num = parseInt(element.id.split("_")[element.id.split("_").length-1]);
        if(value!="0")
        {
            if(value=="on account")
            {
                $(document).find($('#billwise_name_'+row_num+'_'+id_num)).val("");
                $(document).find($('#billwise_due_date_'+row_num+'_'+id_num)).val("");
                $(document).find($('#billwise_name_'+row_num+'_'+id_num)).css("display","none");
                $(document).find($('#billwise_due_date_'+row_num+'_'+id_num)).css("display","none");   
            }
            else
            {
                
                $(document).find($('#billwise_name_'+row_num+'_'+id_num)).css("display","block");
                $(document).find($('#billwise_due_date_'+row_num+'_'+id_num)).css("display","block");
            }
        }
    }
    function getBaseTransaction(){
        /*
        * function to return base transaction.
        */
        var res = $(document).find($('#base_transaction_div')).html().trim();
        console.log('base_transaction',res);
        return res;
    }

    function billwiseNewRow(element,parentId) //left out
    {
        /*
        * function to add new row in billwise modal
        * element = element which fires the request
        * parentId = row number which generate request in billwise modal
        */
        var value= element.value;
        console.log('value',value);
        // getting value of base transaction
        base_transaction=getBaseTransaction();
        // getting value of form type
        var form_type = $(document).find('#form_type').val();
        // getting the row number for which the modal is open
        var row_num = parseInt(element.id.split("_")[element.id.split("_").length-2]);
        // getting the amount type of parent row number.
        var amount_type = $(document).find('#billwise-total-amount-type_'+row_num).html().toLowerCase();
        console.log('base_transaction',base_transaction);
        var amt_element_id='amount_';
        // grtting the modal row number
        var id_num = parseInt(element.id.split("_")[element.id.split("_").length-1]);
        var new_id_num = id_num+1;
        // adding required string to id if form type is not 0
        if(form_type!='0')
        {
            base_transaction = $(document).find($('#amount_type_'+row_num)).val();
            // adding required string to amt_element_id if form type is not 0
            amt_element_id=amt_element_id+base_transaction+'_';
            console.log('amt_element_id',amt_element_id,id_num);
        }
        // if(value != amount_type)
        // {
            // checking for the total balance of the modal amount and row amount
            var amt=0;
            var amtn=0;
            $(document).find($('.billwise-amount_'+row_num)).each(function(){ 
                var id = this.id.split("_")[this.id.split("_").length-1];
                console.log('id',id);
                console.log('billwise_amt_type',$(document).find($('#billwise_amount_type_'+row_num+'_'+id)).val(),amount_type);
                if($(document).find($('#billwise_amount_type_'+row_num+'_'+id)).val() == amount_type)  
                    amt +=parseFloat(this.value);
                else
                    amtn += parseFloat(this.value);
            });
            // calculating the diff and storing to show_amt
            show_amt= parseFloat($(document).find($('#'+amt_element_id+parentId)).val())+amtn-amt;
            console.log('ids',amt_element_id,parentId);
            console.log('shhow amt',show_amt);
            console.log('length match',$(document).find($('.billwise-amount_'+row_num)).length,id_num);
            $(document).find('#billwise-total-amount_'+row_num).val(show_amt);
            // adding new row to billwise if the show amount is not zero
            if($(document).find($('.billwise-amount_'+row_num)).length==id_num+1 && show_amt!=0)
            {
                // destroying the select2 so that the row can be replicated
                $(document).find($('.select')).select2().select2('destroy');

                // getting the data of first row
                var newrow = $(document).find($('#billwise-newRow_'+row_num)).html();
                
                // creating pattern to pich all the elements that end with '_1"'
                var patt = new RegExp('_0"','g'); 

                // replacing the pattern with new id
                var res = newrow.replace(patt,'_'+new_id_num+'"');
                
                // adding the new element to page
                $(document).find($('.billwise-table_'+row_num)).append('<tr>'+res+'</tr>');
                // adding difference to the new entered table row data
                $(document).find($('#billwise_amount_'+row_num+"_"+new_id_num)).val(show_amt);
                // handling the update of modal so that scroll works properly.
                $(document).find($('.billwise-modal')).modal('handleUpdate');
                // add select2 to select element with class 'input-css'
                $(document).find($('.select')).select2({
                    containerCssClass:"input-css"
                });

            }
        // }
    }
    function costcenterNewRow(element,parentId){
        debugger;
        var value = element.value;
        var form_type = $(document).find('#form_type').val();
        var row_num = parseInt(element.id.split("_")[element.id.split("_").length-2]);
        var amount = $(document).find('#costcenter-total-amount_'+row_num).html();
        var id_num = parseInt(element.id.split("_")[element.id.split("_").length-1]);
        var new_id_num = id_num+1;
        var cost_category_val = $(document).find('#costcenter_category_'+id+'_'+id_num).val();
        var center_val = $(document).find('#costcenter_name_'+id+'_'+id_num).val();
        amt = 0;
        
        $(document).find($('.costcenter-name-'+row_num)).each(function(){
            var id = this.id.split("_")[this.id.split("_").length-1];
            var c_cat = $(document).find($('#costcenter_amount_'+row_num+'_'+id)).val();
            console.log('id',id);
            console.log('costcenter_amount_',$(document).find($('#costcenter_amount_'+row_num+'_'+id)).val(),amount);

            if($(document).find($('#costcenter_category_'+row_num+'_'+id)).val() == cost_category_val){
                amt += parseFloat(c_cat);
            }
        });
        show_amt = parseFloat(amount)-amt;
        if($(document).find($('.costcenter-name-'+row_num)).length==id_num+1 && show_amt != 0){
            // destroying the select2 so that the row can be replicated
            $(document).find($('.select')).select2().select2('destroy');
            //taking prev row
            var newrow = $(document).find($('#costcenter-newRow_'+row_num)).html();
            // finding 
            var patt = new RegExp('_0"','g');
            // replacing the pattern with new id
            var res = newrow.replace(patt,'_'+new_id_num+'"');
            $(document).find($('.costcenter-table_'+row_num)).append('<tr>'+res+'</tr>');
            $(document).find($('#costcenter_amount_'+row_num+"_"+new_id_num)).val(show_amt).prop('readonly');
            $(document).find($('#costcenter_category_'+row_num+"_"+new_id_num)).val(cost_category_val);
            // handling the update of modal so that scroll works properly.
            $(document).find($('.billwise-modal')).modal('handleUpdate');
            // add select2 to select element with class 'input-css'
            $(document).find($('.select')).select2({
                containerCssClass:"input-css"
            });
        }else{

        }
        var costcategoriesoption = '<option value="default">Select Cost Category</option>';
        for(cc=0;cc<costcategories.length;cc++)
        {
            costcategoriesoption +='<option value="'+costcategories[cc].id+'">'+costcategories[cc].name+'</option>';
        }
        costcategoriesoption +='<option value="eol">End Of List</option>';

    }
    function getLedgerPropertyByAmountId(ele,prop){
        // debugger;
        /*
        * function to get ledger property from amount field id
        * ele = element which fires the request
        * prop = property to be accessed
        */
        var res = null;

        var id = ele.id.split('_');
        var id = id[id.length-1];
        var val = $(document).find($('#particular_'+id)).val();
        var rel= $(document).find('#relation').html();
        // if(rel == '1ton'){

        //     var users = <?php 
        //     // $arr= [];
        //     // for($j=0;$j<count($account);$j++){
        //     //     $arr[$j]=$account[$j];
        //     // }
        //     echo json_encode($account); ?>;
        //     var len =Object.keys(users).length;
          
        //    for(i=0;i<len;i++){
        //     if(users[i]['id']==val){
        //         return users[i]['prop'];
        //     }else{
        //          return res;
        //     }
        //    }
            
           
        // }
        // getting id number of the element 
       
        // console.log(ledger.length,ledger[0].id,id);
        // console.log('ledger',ledger); 
        for(i=0;i<fullledger.length;i++)
            if(fullledger[i]['id']==val)
                return fullledger[i][prop];
        return res;
    }
    function validateModalFields(type,ele)
    {
        /*
            * function to validate modal fields
            * type = type attribute of form element
            * ele = element generating request
            */
        var err=0;
        // adding and removing error message against validation 
        if(type=='option')
        {
            if($(ele).val()==" " || $(ele).val()=="default")
            {    
                $(ele).parent().find('label.error').remove();
                $(ele).parent().append('<label class="error">This field is required</label>');
                err++;
            }
            else
            {
                $(ele).parent().find('label.error').remove();
            }
        }
        else
        {
            if((type=='email'|| type == 'text' || type == 'date') && $(ele).val()=="")
            {
                $(ele).parent().find('label.error').remove();
                $(ele).parent().append('<label class="error">This field is required</label>');
                err++;
            }
            else if(type == 'number' && ($(ele).val()==0 || isNaN(parseFloat($(ele).val()))))
            {
                $(ele).parent().find('label.error').remove();
                $(ele).parent().append('<label class="error">This field is required</label>');
                err++;
            }
            else
            {
                $(ele).parent().find('label.error').remove();
            }
        }
        return err;
    } 
    function invoiceValidateModal(ele_id)
    {
        /*
        * function to validate particular field of modal 
        * ele_id = id of the modal to be validated
        */
        var name1 = $(document).find('#'+ele_id).find('#basic_ship_delivery_note').val();
        var date1 = $(document).find('#'+ele_id).find('#basic_ship_delivery_date').val();
        console.log('name1',name1);
        console.log('date1',date1);
        if(name1!='' && date1=='' )
        {
            err = $("#form_status-"+ele_id).val();
            err++;
            $(document).find('#'+ele_id).find('#basic_ship_delivery_date').parent().find('label.error').remove();
            $(document).find('#'+ele_id).find('#basic_ship_delivery_date').parent().append('<label class="error">This field is required.</label>');
            $("#form_status-"+ele_id).val(err);
            $("#form_status-"+ele_id+'_div').show();
        }
        else if(name1=='' && date1!='' )
        {
            err = $("#form_status-"+ele_id).val();
            err++;
            $(document).find('#'+ele_id).find('#basic_ship_delivery_note').parent().find('label.error').remove();
            $(document).find('#'+ele_id).find('#basic_ship_delivery_note').parent().append('<label class="error">This field is required.</label>');
            $("#form_status-"+ele_id).val(err);
            $("#form_status-"+ele_id+'_div').show();
        }
        var name1 = $(document).find('#'+ele_id).find('#basic_purchase_order_no').val();
        var date1 = $(document).find('#'+ele_id).find('#basic_order_date').val();
        if(name1!='' && date1=='')
        {
            err = $("#form_status-"+ele_id).val();
            err++;
            $(document).find('#'+ele_id).find('#basic_order_date').parent().find('label.error').remove();
            $(document).find('#'+ele_id).find('#basic_order_date').parent().append('<label class="error">This field is required.</label>');
            $("#form_status-"+ele_id).val(err);
            $("#form_status-"+ele_id+'_div').show();
        }
        else if(name1=='' && date1!='')
        {
            err = $("#form_status-"+ele_id).val();
            err++;
            $(document).find('#'+ele_id).find('#basic_purchase_order_no').parent().find('label.error').remove();
            $(document).find('#'+ele_id).find('#basic_purchase_order_no').parent().append('<label class="error">This field is required.</label>');
            $("#form_status-"+ele_id).val(err);
            $("#form_status-"+ele_id+'_div').show();
        }
    }
    function costcenterValidateTaxModal(ele_id)
    {
        // debugger
        var id_num = ele_id.split('_');
        id_num = id_num[id_num.length-1];
        var amt_type = $('#amount_type_'+id_num).val();
        var form_type = $(document).find('#form_type').val();
        if(form_type==0)
            amt_type='';
        else
            amt_type+='_';
        var inv_tax_value = $('#tax_amount_'+id_num).val();
        var c_val = $(document).find('#'+ele_id).find('.costcenter-amount').val();
        console.log('inv_tax_value',inv_tax_value,'c_val',c_val);
        if(inv_tax_value != c_val)
        {
            err = $("#form_status-"+ele_id).val();
            err++;
            $(document).find('#'+ele_id).find('.costcenter-amount').parent().find('label.error').remove();
            $(document).find('#'+ele_id).find('.costcenter-amount').parent().append('<label class="error">Value not match to Amount Value.</label>');
            $("#form_status-"+ele_id).val(err);
            $("#form_status-"+ele_id+'_div').show();
        }
    } 
    function costcenterValidateModal(ele_id)
    {   
        /*
        * function to validate particular field of modal 
        * ele_id = id of the modal to be validated
        */
        var id_num = ele_id.split('_');
        id_num = id_num[id_num.length-1];
        var amt_type = $('#amount_type_'+id_num).val();
        var form_type = $(document).find('#form_type').val();
        if(form_type==0)
            amt_type='';
        else
            amt_type+='_';
        var out_value = $('#amount_'+amt_type+id_num).val();
        var c_val = $(document).find('#'+ele_id).find('.costcenter-amount').val();
        console.log('out_value',out_value,'c_val',c_val);
        if(out_value != c_val)
        {
            err = $("#form_status-"+ele_id).val();
            err++;
            $(document).find('#'+ele_id).find('.costcenter-amount').parent().find('label.error').remove();
            $(document).find('#'+ele_id).find('.costcenter-amount').parent().append('<label class="error">Value not match to Amount Value.</label>');
            $("#form_status-"+ele_id).val(err);
            $("#form_status-"+ele_id+'_div').show();
        }
    }
    function billwiseValidateModal(ele_id)
    {
        /*
        * function to validate particular field of modal 
        * ele_id = id of the modal to be validated
        */
        var id_num = ele_id.split('_');
        console.log('ele_id',ele_id);
        id_num = id_num[id_num.length-1];
        var amt_type = $('#amount_type_'+id_num).val();
        var form_type = $(document).find('#form_type').val();
        if(form_type==0)
            amt_type='';
        else
            amt_type +='_';
        var out_value = parseFloat($('#amount_'+amt_type+id_num).val());
        amt_type = $('#amount_type_'+id_num).val();
        $(document).find('#'+ele_id).find('.billwise-amount_'+id_num).each(function(i,e) {
            console.log('i',i);
            var billwise_amt_type = $(document).find('#'+ele_id).find('#billwise_amount_type_'+id_num+'_'+(i)).val(); 
            if(amt_type==billwise_amt_type)
            {
                if(!isNaN(parseFloat(out_value)) && !isNaN(parseFloat($(e).val())))
                    out_value = out_value - parseFloat($(e).val());
            }
            else
                if(!isNaN(parseFloat(out_value)) && !isNaN(parseFloat($(e).val())))
                    out_value = out_value + parseFloat($(e).val());
            console.log('billwise_amt_type',billwise_amt_type);
        });
        
        console.log('billwise out_val',out_value);
        console.log('amt_type',amt_type);
        if(out_value!=0)
        { 
            err = $("#form_status-"+ele_id).val();
            err++;
            $("#form_status-"+ele_id).val(err);
            $("#form_status-"+ele_id+'_div').show();
        }
    }
    
    function validateModal(ele_id) 
    {
        /*
        * function to validate modal 
        * ele_id = id of the modal to be validated
        */
        console.log(ele_id);
        var err=0;
        // validating each element of modal

        $(document).find('#'+ele_id).find('input[required]:visible').each(function(i,e) {
            err+= validateModalFields($(e).attr('type'),$(e));
            console.log('err1',i,err);
        });
        $(document).find('#'+ele_id).find('select[required]:visible').each(function(i,e) {
            err+=validateModalFields('option',$(e));
            console.log('err2',i,err);
        });

        $("#form_status-"+ele_id).val(err);
        // updating input value with number of error.
        if(err==0)
            $("#form_status-"+ele_id+'_div').hide();
            console.log('form-status-ele',$("#form_status-"+ele_id+'_div'));
        // hiding the modal
        $('#'+ele_id).modal('hide');
            
    }

    function checkBillWise(ele)
    {
        // debugger;
        /*
        * function to generate billwise modal
        * ele = element which fired the function 
        */
        // showing the loader
        $('#ajax_loader_div').css('display','block');
        if($(ele).val()!='default' )
        {
            // getting row number of the element 
            var id = parseInt(ele.id.split('_')[ele.id.split('_').length-1]);
            // getting amount type of the row.
            var type = $(document).find('#amount_type_'+id).val();
            // value selector is different for formtype
            if($('#form_type').val()!='0')
                var value = $(document).find('#amount_'+type+'_'+id).val();
            else
                var value = $(document).find('#amount_'+id).val();
            
            var currency = "{{$currency}}";
            // checking whether modal element is already created or not.
            var len = $(document).find($('#billwise_myModal_'+id)).length;
            // checking whether ledger is eligile for billwise modal
            var is_bill_wise_on = getLedgerPropertyByAmountId(ele,'is_bill_wise_on');
            console.log('is_bill_wise_on',is_bill_wise_on);
            console.log('len',len);

            if(is_bill_wise_on==1 && len==0)                    
            {
                // genetrating modal button
                var buttonElement = 
                '<div class="col-md-2">'+
                    '<button type="button" style="margin-right:1em" class="btn btn-sm btn-primary BillWiseToggleButton" data-toggle="modal" data-target="#billwise_myModal_'+id+'">Billwise Details</button>'+
                '</div>'+
                '<div id="form_status-billwise_myModal_'+id+'_div" class="col-md-4">'+
                    '<div class="col-md-4"><label>Billwise Errors:</label></div><div class="col-md-8">'+
                        '<input id="form_status-billwise_myModal_'+id+'" class="form_status form-control" max="0" type="number">'+
                    '</div>'+
                '</div>';
                // generating modal
                var modalElement = 
                            '<div class="container">'+
                                '<div class="modal fade billwise-modal" style="overflow-y:scroll;padding: 20px;" id="billwise_myModal_'+id+'" role="dialog">'+
                                '<div class="modal-dialog modal-lg">'+
                                ' <!-- Modal content-->'+
                                    '<div class="modal-content">'+
                                    '<div class="modal-header">'+
                                        '<button type="button" class="close" onclick="validateModal(\'billwise_myModal_'+id+'\');billwiseValidateModal(\'billwise_myModal_'+id+'\')">&times;</button>'+
                                        '<h4 class="modal-title">Bill Wise Details</h4>'+
                                        '<center>Upto : '+currency+' <span id="billwise-total-amount_'+id+'">'+value+'</span> <span id="billwise-total-amount-type_'+id+'" style="text-transform:capitalize">'+type+'</span></center>'+
                                        '<hr>'+
                                    '</div>'+
                                    '<div class="modal-body" style="overflow-y: auto;padding: 20px;height: 480px;position: relative;">'+
                                        '<div class="table-responsive">'+
                                            '<table class="table table-condensed billwise-table_'+id+'">'+
                                                '<tr>'+
                                                    '<th>Type of Ref</th>'+
                                                    '<th>Name</th>'+
                                                    '<th>Due Date, or credit days</th>'+
                                                    '<th>Amount</th>'+
                                                    '<th>Dr/Cr</th></tr>'+
                                                '<tr id="billwise-newRow_'+id+'">'+
                                                    '<td>'+
                                                        '<select required="required" class="select form-control input-css billwise " onchange="billwiseMethodTypeChange(this);validateModalFields(\'option\',this)" name="billwise_method['+id+'][]" id="billwise_method_'+id+'_0">'+
                                                            '<option value="advance">Advance</option>'+
                                                            '<option value="agst ref">Agst Ref</option>'+
                                                            '<option value="new ref">New Ref</option>'+
                                                            '<option selected="selected" value="on account">On Account</option></select>'+
                                                    '<td>'+
                                                        '<input required="required" type="text" style="display:none;" class="input-css form-control billwise " onchange="validateModalFields(\'text\',this)" name="billwise_name['+id+'][]" id="billwise_name_'+id+'_0"></td>'+
                                                    '<td>'+
                                                        '<input required="required" type="text" style="display:none;" class="input-css form-control billwise " onchange="validateModalFields(\'text\',this)" name="billwise_due_date['+id+'][]" id="billwise_due_date_'+id+'_0"></td>'+
                                                    '<td>'+
                                                        '<input required="required" type="number" class="input-css form-control billwise billwise-amount_'+id+' " onchange="validateModalFields(\'number\',this);" name="billwise_amount['+id+'][]" id="billwise_amount_'+id+'_0"></td>'+
                                                    '<td>'+
                                                        '<select required="required" class="select form-control input-css billwise billwise-amount-type" onchange="billwiseNewRow(this,\''+id+'\');validateModalFields(\'option\',this);" name="billwise_amount_type['+id+'][]" id="billwise_amount_type_'+id+'_0">'+
                                                            '<option value="default">select type</option>'+
                                                            '<option value="dr" {{$base_transaction=="dr"?"selected=selected":""}} >Dr</option>'+
                                                            '<option value="cr" {{$base_transaction=="cr"?"selected=selected":""}}>Cr</option></select></td></tr></table></div>'+
                                        '</div>'+
                                    '<div class="modal-footer">'+
                                        '<button type="button" class="btn btn-default" onclick="validateModal(\'billwise_myModal_'+id+'\');billwiseValidateModal(\'billwise_myModal_'+id+'\')">Close</button>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
                ;
                // adding modal to page
                $(document).find($('#modal_div_'+id)).append(modalElement);
                // adding modal button to page
                $(document).find($('#modal_div_button_'+id)).append(buttonElement);
                // add select2 to select element with class 'input-css'
                $('.select').select2({
                    containerCssClass:"input-css"
                });
                // showing the modal
                $(document).find($('#billwise_myModal_'+id)).modal({
                    backdrop: 'static'
                });
                // opening cost center modal on close of this modal
                $(document).find($('#billwise_myModal_'+id)).on('hide.bs.modal',function(){
                    // checking whether the ledger is eligible for cost center modal
                    var is_cost_centres_on = getLedgerPropertyByAmountId(ele,'is_cost_centres_on'); 
                    if(is_cost_centres_on==1)
                    {
                        // checking whether  modal element is created or not
                        if($(document).find($('#costcenter-myModal_'+id)).length>0)
                        {   
                            // showing modal element
                            $(document).find($('#costcenter-myModal_'+id)).modal('show');
                        }
                        else
                        {
                            // creating modal element
                            checkCostCenter(ele);                            
                        }
                    }
                });
            }
            // hiding the loader
                $('#ajax_loader_div').css('display','none');
            return is_bill_wise_on;
        }
            // hiding the loader
        $('#ajax_loader_div').css('display','none');
        return 0;
    }

    function getInvoiceDetails(detailsFor)
    {
        // debugger
        /*
        * function to open modal form for sale and invoice
        * detailsFor = name of the voucher type.
        */
        if(detailsFor == 'Sales')
        {
            // modal form for sales.
            var body = '<div class="row" style="text-align:center">'+
                            '<div class="col-md-12">'+
                                '<label for="">Dispatch Details</label>'+
                            '</div>'+
                        '</div> '+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Delivery Note No.</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<div class="col-md-6">'+
                                    '<input type="text" name="basic_ship_delivery_note" id="basic_ship_delivery_note" class="form-control input-css">'+
                                '</div>'+
                                '<div class="col-md-6">'+
                                    '<input type="text" name="basic_ship_delivery_date" onchange="validateModalFields(\'text\',this);"  id="basic_ship_delivery_date" placeholder="date of delivery note"'+
                                    'class="input-css form-control datepicker1">'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Dispatch Doc. No.</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basic_ship_document_no" id="dispatch_doc_no" class="input-css form-control">'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Dispatch Through</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basic_shipped_by" id="dispatch_through" class="input-css form-control">'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Destination</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basic_final_destination" id="destination" class="input-css form-control">'+
                            '</div>'+
                        '</div>'+
                        '<hr>'+
                        '<div class="row" style="text-align:center">'+
                            '<div class="col-md-12">'+
                                '<label for="">Order Details</label>'+
                            '</div>'+
                        '</<div> '+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Order No.</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<div class="col-md-6">'+
                                    '<input type="text" name="basic_purchase_order_no" id="basic_purchase_order_no" class="form-control input-css">'+
                                '</div>'+
                                '<div class="col-md-6">'+
                                    '<input type="text" name="basic_order_date" onchange="validateModalFields(\'text\',this);"  id="basic_order_date" placeholder="date of order"'+
                                    ' class="input-css form-control datepicker1 ">'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Mode/Terms of Payment</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basic_due_date_of_pymt" id="terms_of_pymt" class="input-css form-control">'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Order Reference</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basic_order_ref_no" id="order_ref_no" class="input-css form-control">'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Terms of Delivery</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basic_order_terms" id="order_terms" class="input-css form-control">'+
                            '</div>'+
                        '</div>'+
                        '<hr>'+
                        '<div class="row" style="text-align:center">'+
                            '<div class="col-md-12">'+
                                '<label for="">Buyer\'s Details</label>'+
                            '</div>'+
                        '</div>  '+
                
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Buyers</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basic_buyer_name" required="required" onchange="validateModalFields(\'text\',this);" id="buyers" class="form-control input-css">'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">address</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<textarea name="address" id="address" cols="30" rows="2" class="form-control input-css"></textarea>'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">TIN/Sales Tax Number</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basics_buyers_sales_tax_no" id="tin_no" class="form-control input-css">'+
                            '</div>'+
                        '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">type of dealer</label>'+
                            '</div>'+
                            '<div class="col-md-8">'+
                                '<select name="type_of_dealer" id="type_of_dealer" class="form-control input-css select">'+
                                    '<option value="na" selected>Unknown</option>'+
                                    '<option value="Composition">Composition</option>'+
                                    '<option value="Regular">Regular</option>'+
                                    '<option value="Unregistered">Unregistered</option>'+
                                '</select>'+
                            '</div>'+
                        '</div>'
                    ;
            
            // modal title for sales 
            var title = 'Sales Details'
        }
        else if(detailsFor == 'Purchase')
        {

            // modal title for purchase
            var title = 'Purchase Details'
            // modal form for purchase.
            var body =  
                        '<div class="row">'+
                            '<div class="col-md-12" style="text-align:center">'+
                                '<label for="">Supplier\'s Details</label>'+
                           '</div>'+
                       '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">Supplier</label>'+
                           '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" required="required" onchange="validateModalFields(\'text\',this)" name="basic_buyer_name" id="supplier" class="form-control input-css">'+
                           '</div>'+
                       '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">address</label>'+
                           '</div>'+
                            '<div class="col-md-8">'+
                                '<textarea name="address" id="address" cols="30" rows="2" class="form-control input-css"></textarea>'+
                           '</div>'+
                       '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">TIN/Sales Tax Number</label>'+
                           '</div>'+
                            '<div class="col-md-8">'+
                                '<input type="text" name="basics_buyers_sales_tax_no" id="tin_no" class="form-control input-css">'+
                           '</div>'+
                       '</div>'+
                        '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label for="">type of dealer</label>'+
                           '</div>'+
                            '<div class="col-md-8">'+
                                '<select name="type_of_dealer" id="type_of_dealer" class="form-control input-css select">'+
                                    '<option value="na" selected>Unknown</option>'+
                                    '<option value="Composition">Composition</option>'+
                                    '<option value="Regular">Regular</option>'+
                                    '<option value="Unregistered">Unregistered</option>'+
                                '</select>'+
                            '</div>'+
                       '</div>'
            ;
        }
        // button for modal
        var buttonElement = 
                    '<div class="col-md-2">'+
                        '<button type="button" class="btn btn-primary " data-toggle="modal" data-target="#invoiceDetail-myModal_0" >Invoice Details</button>'+
                    '</div>'+
                    '<div id="form_status-invoiceDetail-myModal_0_div" class="col-md-4">'+
                        '<div class="col-md-4">'+
                            '<label>Invoice Detail Error:</label>'+
                        '</div>'+
                        '<div class="col-md-8">'+
                            '<input max="0" type="number" id="form_status-invoiceDetail-myModal_0" class="form_status form-control">'+
                        '</div>' +
                    '</div>';
        // modal form
        var modalElement = 
                            '<div class="container">'+
                                '<div class="modal fade" id="invoiceDetail-myModal_0" role="dialog">'+
                                    '<div class="modal-dialog modal-lg">'+
                                    ' <!-- Modal content-->'+
                                        '<div class="modal-content">'+
                                            '<div class="modal-header">'+
                                                '<button type="button" class="close" onclick="validateModal(\'invoiceDetail-myModal_0\');invoiceValidateModal(\'invoiceDetail-myModal_0\');">&times;</button>'+
                                                '<h4 class="modal-title">'+title+'</h4>'+
                                            '</div>'+
                                            '<div class="modal-body">'+
                                            // modal form is added
                                                body+
                                            '</div>'+
                                            '<div class="modal-footer">'+
                                                '<button type="button" class="btn btn-default" onclick="validateModal(\'invoiceDetail-myModal_0\');invoiceValidateModal(\'invoiceDetail-myModal_0\');">Close</button>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';
                // empty div 
                $(document).find($('#invoice_detail_div')).empty();
                $(document).find($('#invoice_detail_button')).empty();
                // append of modal to page
                $(document).find($('#invoice_detail_div')).append(modalElement);
                // append of modal button to page
                $(document).find($('#invoice_detail_button')).append(buttonElement);
                 if($("#account").val() == 'default'){
                    $(document).find($('#invoice_detail_div')).empty();
                    $(document).find($('#invoice_detail_button')).empty();
                }
                // add select2 to select element with class 'input-css'
                $('.select').select2({
                    containerCssClass:"input-css"
                });
                $('.datepicker').datepicker({
                    autoclose: true,
                }).datepicker("setDate", new Date());

                $('.datepicker1').datepicker({
                    autoclose: true
                });
        $(document).find($('#invoiceDetail-myModal_0')).modal('show');
    }

    function costcenterCategoryTypeChange(ele)
    {debugger;
        /*
        * function to cet cost center options on change of coat category
        * ele = element which fired this function
        */
        // getting the row number of the element
        id=ele.id.split('_')[ele.id.split('_').length-2];
        var last_digit = ele.id.split('_')[ele.id.split('_').length-1];
        val = $(ele).val();
        not_req_id='';
        // getting ids of not wanted options
        $(document).find($('.costcenter-name-'+id)).each(function(){
            if(this.value!='' ||this.value!='default')  
                not_req_id += this.value+',';
        });
        // getting options for cost center
        $.ajax({
            url: "/accounting/voucher/costcentersbycategory/"+ val+"/"+not_req_id,
            type: "GET",
            success: function(result) {
                console.log(result);
                var opt = '<option value="default">Select Cost Center</option>';
                for(i=0;i<result.length;i++)
                    opt += '<option value="'+result[i].id+'">'+result[i].name+'</option>' 
                // adding option to cost center 
                $('#costcenter_name_'+id+'_'+last_digit).html(opt);
                // add select2 to select element with class 'input-css'
                $('.select').select2({
                    containerCssClass:"input-css"
                });
            }
        });
    }
    function checkCostCenter(ele)
    {
        /*
        * function to generate cost center modal
        * ele = element for which this function is fired
        */
        // showing the loader
        $('#ajax_loader_div').css('display','block');
        if($(ele).val()!='default')
        {
            // getting row number of element
            var id = parseInt(ele.id.split('_')[ele.id.split('_').length-1]);
            var type = $(document).find('#amount_type_'+id).val();
           
            // checking whether modal is already created or not.
            var len = $(document).find($('#costcenter-myModal_'+id)).length;
            // checking whether ledger is eligible for cost center modal.
            if($('#form_type').val()!='0')
                var value = $(document).find('#amount_'+type+'_'+id).val();
            else
                var value = $(document).find('#amount_'+id).val();
            
            currency = "{{$currency}}";
            var is_cost_centres_on = getLedgerPropertyByAmountId(ele,'is_cost_centres_on');
            if(is_cost_centres_on==1 && len==0)                    
            {
                // row num start from 1 and i want to access data from 0 so access_id is used
                var access_id = id-1;
                // adding cost category element.
                var costcategoriesoption = '<option value="default">Select Cost Category</option>'
                console.log('test ledger cost categoty',id, costcategories[0].id);
                for(cc=0;cc<costcategories.length;cc++)
                {
                    costcategoriesoption +='<option value="'+costcategories[cc].id+'">'+costcategories[cc].name+'</option>'
                }
                costcategoriesoption +='<option value="eol">End Of List</option>'
                // create button element for modal
                var buttonElement = '<div class="col-md-2">'+
                        '<button type="button" style="margin-right:1em" class="btn btn-primary btn-sm CostCenterToggleButton" data-toggle="modal" data-target="#costcenter-myModal_'+id+'">Cost Allocations Details</button>'+
                    '</div>'+
                    '<div id="form_status-costcenter-myModal_'+id+'_div" class="col-md-4">'+
                        '<div class="col-md-4">'+
                            '<label>Cost Center Error:</label>'+
                        '</div>'+
                        '<div class="col-md-8">'+
                            '<input max="0" type="number" id="form_status-costcenter-myModal_'+id+'" class="form_status form-control">'+
                        '</div>' +
                    '</div>'
                ;
                // create modal element for page
                var modalElement = 
                            '<div class="container">'+
                                '<div class="modal fade" id="costcenter-myModal_'+id+'" role="dialog">'+
                                    '<div class="modal-dialog">'+
                                    ' <!-- Modal content-->'+
                                        '<div class="modal-content">'+
                                            '<div class="modal-header">'+
                                                '<button type="button" class="close" onclick="validateModal(\'costcenter-myModal_'+id+'\')">&times;</button>'+
                                                '<h4 class="modal-title">Cost Allocations</h4>'+
                                                '<center>Upto : '+currency+' <span id="costcenter-total-amount_'+id+'">'+value+'</span> <span id="costcenter-total-amount-type_'+id+'" style="text-transform:capitalize">'+type+'</span></center>'+
                                                
                                            '</div>'+
                                            '<div class="modal-body">'+
                                                '<div class="table-responsive">'+
                                                    '<table class="table table-condensed costcenter-table_'+id+'" style="text-align:center">'+
                                                        '<tr>'+
                                                            '<th>Cost Category</th>'+
                                                            '<th>Name of Cost Centre</th>'+
                                                            '<th>Amount</th>'+
                                                        '</tr>'+
                                                        '<tr id="costcenter-newRow_'+id+'">'+
                                                            '<td>'+
                                                                '<select required="required" class="select form-control input-css costcenter" onchange="costcenterCategoryTypeChange(this);checkStringEndOfList(this);validateModalFields(\'option\',this)" name="costcenter_category['+access_id+'][]" id="costcenter_category_'+id+'_0">'+
                                                                costcategoriesoption+ 
                                                                '</select>'+
                                                            '</td>'+
                                                            '<td>'+
                                                                '<select required="required" class="select form-control input-css costcenter costcenter-name-'+id+'" onchange="validateModalFields(\'option\',this)"  name="costcenter_name['+access_id+'][]" id="costcenter_name_'+id+'_0">'+
                                                                    '<option value="default">Select cost category</option>'+
                                                                '</select>'+
                                                            '</td>'+
                                                            '<td>'+
                                                                '<input required="required" type="number" class="input-css form-control costcenter costcenter-amount" onchange="validateModalFields(\'number\',this);costcenterNewRow(this,'+id+')" name="costcenter_amount['+access_id+'][]" id="costcenter_amount_'+id+'_0"></td>'+
                                                            '</td>'+
                                                        '</tr>'+
                                                    '</table>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="modal-footer">'+
                                                '<button type="button" class="btn btn-default" onclick="validateModal(\'costcenter-myModal_'+id+'\');costcenterValidateModal(\'costcenter-myModal_'+id+'\')">Close</button>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';
                // adding modal button to page
                $(document).find($('#modal_div_button_'+id)).append(buttonElement);
                // adding modal to page
                $(document).find($('#modal_div_'+id)).append(modalElement);
                // showing the modal
                $(document).find($('#costcenter-myModal_'+id)).modal('show');
                $('.select').select2({
                    containerCssClass:"input-css"
                });

            }   
            // hiding the loader
            $('#ajax_loader_div').css('display','none');
            return is_cost_centres_on;
        }
        // hiding the loader
        $('#ajax_loader_div').css('display','none');
        return 0;
    }
    function checkCostCenterTax(ele)
    {
        // debugger
        $('#ajax_loader_div').css('display','block');
        if($(ele).val()!='default')
        {
            // getting row number of element
            var id = parseInt(ele.id.split('_')[ele.id.split('_').length-1]);
            var type = $(document).find('#amount_type_'+id).val();
            if( $('#form_type').val() != "0")
                var value = $(document).find('#tax_amount_'+id).val();
            else
                var value = $(document).find('#tax_amount_'+id).val();
            var currency = "{{$currency}}";
            // checking whether modal is already created or not.
            var len = $(document).find($('#costcenter-myModal_'+id)).length;
            // checking whether ledger is eligible for cost center modal.
            var is_cost_centres_on = getLedgerPropertyByAmountId(ele,'is_cost_centres_on');
            if(is_cost_centres_on==1 && len==0)                    
            {
                // row num start from 1 and i want to access data from 0 so access_id is used
                var access_id = id-1;
                // adding cost category element.
                var costcategoriesoption = '<option value="default">Select Cost Category</option>'
                console.log('test ledger cost categoty',id, costcategories[id]);
                for(cc=0;cc<costcategories.length;cc++)
                {
                    costcategoriesoption +='<option value="'+costcategories[cc].id+'">'+costcategories[cc].name+'</option>'
                }
                costcategoriesoption +='<option value="eol">End Of List</option>'
                // create button element for modal
                var buttonElement = '<div class="col-md-2">'+
                        '<button type="button" style="margin-right:1em" class="btn btn-primary btn-sm CostCenterToggleButton" data-toggle="modal" data-target="#costcenter-myModal_'+id+'">Cost Allocations Details</button>'+
                    '</div>'+
                    '<div id="form_status-costcenter-myModal_'+id+'_div" class="col-md-4">'+
                        '<div class="col-md-4">'+
                            '<label>Cost Center Error:</label>'+
                        '</div>'+
                        '<div class="col-md-8">'+
                            '<input max="0" type="number" id="form_status-costcenter-myModal_'+id+'" class="form_status form-control">'+
                        '</div>' +
                    '</div>'
                ;
                // create modal element for page
                var modalElement = 
                            '<div class="container">'+
                                '<div class="modal fade" id="costcenter-myModal_'+id+'" role="dialog">'+
                                    '<div class="modal-dialog">'+
                                    ' <!-- Modal content-->'+
                                        '<div class="modal-content">'+
                                            '<div class="modal-header">'+
                                                '<button type="button" class="close" onclick="validateModal(\'costcenter-myModal_'+id+'\')">&times;</button>'+
                                                '<h4 class="modal-title">Cost Allocations</h4>'+
                                                '<center>Upto : '+currency+' <span id="costcenter-total-amount_'+id+'">'+value+'</span> <span id="costcenter-total-amount-type_'+id+'" style="text-transform:capitalize">'+type+'</span></center>'+
                                                '<hr>'+
                                            '</div>'+
                                            '<div class="modal-body">'+
                                                '<div class="table-responsive">'+
                                                    '<table class="table table-condensed costcenter-table" style="text-align:center">'+
                                                        '<tr>'+
                                                            '<th>Cost Category</th>'+
                                                            '<th>Name of Cost Centre</th>'+
                                                            '<th>Amount</th>'+
                                                        '</tr>'+
                                                        '<tr id="costcenter-newRow">'+
                                                            '<td>'+
                                                                '<select required="required" class="select form-control input-css costcenter" onchange="costcenterCategoryTypeChange(this);checkStringEndOfList(this);validateModalFields(\'option\',this)" name="costcenter_category['+access_id+'][]" id="costcenter_category_'+id+'">'+
                                                                costcategoriesoption+ 
                                                                '</select>'+
                                                            '</td>'+
                                                            '<td>'+
                                                                '<select required="required" class="select form-control input-css costcenter costcenter-name-'+id+'" onchange="validateModalFields(\'option\',this)"  name="costcenter_name['+access_id+'][]" id="costcenter_name_'+id+'">'+
                                                                    '<option value="default">Select cost category</option>'+
                                                                '</select>'+
                                                            '</td>'+
                                                            '<td>'+
                                                                '<input required="required" type="number" class="input-css form-control costcenter costcenter-amount" onchange="validateModalFields(\'number\',this)" name="costcenter_amount['+access_id+'][]" id="costcenter_amount_'+id+'"></td>'+
                                                            '</td>'+
                                                        '</tr>'+
                                                    '</table>'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="modal-footer">'+
                                                '<button type="button" class="btn btn-default" onclick="validateModal(\'costcenter-myModal_'+id+'\');costcenterValidateTaxModal(\'costcenter-myModal_'+id+'\')">Close</button>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';
                // adding modal button to page
                $(document).find($('#modal_tax_div_button_'+id)).append(buttonElement);
                // adding modal to page
                $(document).find($('#modal_tax_div_'+id)).append(modalElement);
                // showing the modal
                $(document).find($('#costcenter-myModal_'+id)).modal('show');
                $('.select').select2({
                    containerCssClass:"input-css"
                });

            }   
            // hiding the loader
            $('#ajax_loader_div').css('display','none');
            return is_cost_centres_on;
        }
        // hiding the loader
        $('#ajax_loader_div').css('display','none');
        return 0;
    }
    function blurAmount(ele)
    {
        /*
        * function
        * ele = element which fires this function
        */
        if($(ele).val()!='')   
        {
            // getting row num of the element 
            var length = ele.id.split('_').length;
            var id = parseInt(ele.id.split('_')[length-1]);
            var values = $('#particular_'+id).val().toString();
            // flag to check whether new line is required(default required).
            var new_line_req_flag = 1;
            // getting total debit amount 
            var debit_amount_total = parseFloat($(document).find('#debit_total_amount').html());
            // getting total credit amount 
            var credit_amount_total = parseFloat($(document).find('#credit_total_amount').html());
            if(values!=="default")
            { 
               
                // stop gen of new line if credit aand debit amount match
                if(debit_amount_total==credit_amount_total)
                    new_line_req_flag=0;


                var rel= $(document).find('#relation').html();
                if(rel == '1ton'){
                    if(id == 0){
                       var billwise =  checkBillWise(ele);
                       console.log('BILLWISE',billwise);
                       if(billwise==0 ||billwise==null)
                            var costcenter = checkCostCenter(ele);
                    }
                }
                // checking new line is required based on number of particular .
                if($('.particular').length==id || ($('#particular_0').length>0 && $('.particular').length-1==id) )
                {   
                    if(new_line_req_flag == 1) 
                    {
                        var nid=id+1;
                        // add new row with id ending with row num +1
                        addrow(nid);
                    }
                    // creating billwise modal
                    var billwise = checkBillWise(ele);
                    var costcenter=-1;
                    console.log('BILLWISE',billwise);
                    console.log('ele',ele);
                    console.log('ele',costcenter);
                    // if billwise not eligible creating cost center modal
                    if(billwise==0 ||billwise==null)
                        costcenter = checkCostCenter(ele);
                }
                else
                {
                    console.log("id",id);
                    // showing the billwise  modal otherwise
                    $(document).find($('#billwise-total-amount_'+id)).html($(ele).val());
                    $(document).find($('#billwise_myModal_'+id)).modal({
                        backdrop: 'static'
                    });
                }
            }
            
            
        }
        else
        {}

    }    
    function addrow(id)
    {
        debugger;
        /*
        * function to add new line to page
        * id = new row number
        */

        // destroying the select2 so that the row can be replicated
        $(document).find($('.select')).select2().select2('destroy');
        // getting the data of first row
        var ele = $('#first_row').html();
        // creating pattern to pich all the elements that end with '_1"'
        var patt = new RegExp('_1"','g');
        // generating new element ending id (this will act as row num)
        var newstr = '_'+id+'"';
        // replacing the pattern with new id
        var res =  ele.replace(patt,newstr);
        // adding the new element to page
        $('#particularDiv').append(res+'<div class="row">'+
            '<div id="modal_div_'+id+'" style="display:inline"></div>'+
            '<div id="modal_div_button_'+id+'" class="form-group" style="margin-top: 2rem;"></div>'+
        '</div>');       
        // add select2 to select element with class 'input-css'
        $('.select').select2({
            containerCssClass:"input-css"
        });  
        console.log('done'); 
    }
    function addtaxrow(id){
        // debugger;
        /*
        * function to add new line to page
        * id = new row number
        */
        // destroying the select2 so that the row can be replicated
        $(document).find($('.select')).select2().select2('destroy');
        // getting the data of first row
        var ele = $('#tax_row').html();
        // creating pattern to pich all the elements that end with '_1"'
        var patt = new RegExp('_1','g');
        // generating new element ending id (this will act as row num)
        var newstr = '_'+id+'';
        // replacing the pattern with new id
        var res =  ele.replace(patt,newstr);
        // adding the new element to page and add select2 to select element with class 'input-css'
        $('#taxDiv').append(res+'<div class="row">'+
            '<div id="modal_tax_div_'+(id)+'" style="display:inline"></div>'+
            '<div id="modal_tax_div_button_'+(id)+'" class="form-group" style="margin-top: 2rem;"></div>'+
        '</div>').find('.select').select2({
            containerCssClass:"input-css"
        });
    }
    function focusQuantity(element)
    {        
        /*
        * unused function 
        *
        */
        var id = element.id;
        var id_num = parseInt(id.split("_")[id.split("_").length-1]);
        if($(document).find($('#particular_'+id_num)).val()=='default' )
            $(document).find($('#particular_'+id_num)).focus();
    }
    function focusQuantityItem(element)
    {        
        /*
        * function to focus item if item not selected and quantity is tried to open
        * element = element which fires the function 
        */
        var id = element.id;
        var id_num = parseInt(id.split("_")[id.split("_").length-1]);
        if( $(document).find($('#particular_item_'+id_num)).val() == 'default' )
            $(document).find($('#particular_item_'+id_num)).focus();
    }
    function changeQuantity(element)
    {
        /*
        * function to calculate total amount of item if rate of item is not null and quantity is not null.
        * element = element which fires the function 
        */
        var id = element.id;
        // getting row number of the element
        var id_num = parseInt(id.split("_")[id.split("_").length-1]);
        if(!isNaN(element.value))
        {
            if(!isNaN($(document).find($('#rate_'+id_num)).val()))
            {
                var tot_amt = parseFloat($(document).find($('#rate_'+id_num)).val()) * parseFloat(element.value)
                $(document).find($('#amount_item_'+id_num)).val(tot_amt);
            }

        }
    }
    
    function changeItemAmount(element)
    {
        // debugger
        /*
        * function to calculate total rate of item if item amount is change.
        * element = element which fires the function 
        */
        var id = element.id;
        // getting row number of the element
        var id_num = parseInt(id.split("_")[id.split("_").length-1]);
        if(!isNaN(element.value) && !isNaN($(document).find($('#quantity_'+id_num)).val()) )
        {
            var tot_amt =  parseFloat(element.value) / parseFloat($(document).find($('#quantity_'+id_num)).val()) 
            $(document).find($('#rate_'+id_num)).val(tot_amt);
        }
    }
    function changeRate(element)
    {
        /*
        * function to calculate total amount of item if rate of item is not null and quantity is not null.
        * element = element which fires the function 
        */
        var id = element.id;
        // getting row number of the element
        var id_num = parseInt(id.split("_")[id.split("_").length-1]);
        if(!isNaN(element.value) && !isNaN($(document).find($('#quantity_'+id_num)).val()))
        {
            var tot_amt = parseFloat($(document).find($('#quantity_'+id_num)).val()) * parseFloat(element.value)
            $(document).find($('#amount_item_'+id_num)).val(tot_amt);
        }
    }
    function changeItem(ele)
    {
        // debugger
        /*
        * function to get details of item 
        * ele = element which fires this function
        */
        if(ele.value!="eof")
        {    
            // getting row number of the element
            id= ele.id.split("_")[ele.id.split("_").length-1];
            // api to get item details
            $.ajax({
                url: "/accounting/item/data/"+ele.value,
                type: "get",
                success: function(result) {
                        console.log(result[0]);
                        // creating option for uom
                        var option = '<option>select per</option>';
                        for(i=0;i<result.length;i++)
                        {
                            option = option+'<option value="'+result[i].id+'">'+result[i].name+'</option>';
                        }
                        // add select2 to select element with class 'input-css'
                        $("#uom_"+id).empty().append(option).select2({
                            containerCssClass:"input-css"
                        });
                    }
            });
        }
    }
    
    function blurAmountInvoiceTax (ele)
    { 
        // debugger
        /*
        * function to add new tax row if amount is  not empty and particular is not default 
        * ele = element which fires this function
        */
        if($(ele).val()!='')   
        {
            var length = ele.id.split('_').length;
            // getting row number of the element
            id = parseInt(ele.id.split('_')[length-1]);
            var values = $('#particular_'+id).val().toString();
            if(values!=="default")
            { 
                if($('.tax').length==id)
                {    
                    var nid=id+1;
                    // add tax row
                    addtaxrow(nid);
                    // check for cost center because billwise is not eligible here
                    costcenter = checkCostCenterTax(ele);
                    // // add select2 to select element with class 'input-css'
                    // $('.select2').select2({
                    //     containerCssClass:"input-css"
                    // });
                    $('#particular_'+id).focus();
                }
            }

        }
    }
    function blurAmountInvoice(ele)
    {
        // debugger
        /*
        * function to add new normal row if amount is  not empty and particular is not default 
        * ele = element which fires the function
        */
        if($(ele).val()!='')   
        {
            var length = ele.id.split('_').length;
            // getting row number of the element
            var id = parseInt(ele.id.split('_')[length-1]);
            var values = $('#particular_item_'+id).val().toString();
            if(values !== "default")
            {
                if($('.particular-item').length==id || ($('#particular_item_1').length>0 && $('.particular-item').length-1==id))
                {
                    var nid=id+1;
                    // add tax row
                    addrow(nid);
                    // // add select2 to select element with class 'input-css'
                    // $('.select2').select2({
                    //     containerCssClass:"input-css"
                    // });
                    $('#particular_item_'+nid).focus();
                }
            }
            else 
            {
                if($('#debit_total_amount').val()==$('#credit_total_amount').val())
                    $('#narration').focus();                
            }
            changeAmount();
        }
        else
        {
            console.log('first if fail');
        }
    }
    function post_dated(){
        /*
        * function to implement the post dated data
        */
        $('#post_dated_div').toggle()
        $('#post_dated_enabled_div').toggle();
        $('#post_dated_disabled_div').toggle();
        var val = parseInt($('#is_post_dated').val());
        if(val == 1)
            $('#is_post_dated').val(0);
        else
            $('#is_post_dated').val(1);
    }
    function change_date(){
        // function to display the modal form of change date
        $('#myModal').modal('show');
    }
    function change_date_api(){
        /*
        * function to change current date of company
        */
        // api to change the current date
        $.ajax({
            url: "/accounting/compdate/change?date="+$('#current_date').val() ,
            type: "get",
            success: function(result) {
                    console.log(result);
                    // hiding the modal 
                    if(result==1)
                        $('#myModal').modal('hide');
                    // add select2 to select element with class 'input-css'
                    $('.select').select2({
                        containerCssClass:"input-css"
                    });
                }
            });
    }
    function checkStringEndOfList(ele)
    {
        /*
        * function to remove the row
        * ele = element which fires the function
        */
        // getting row number of the element
        var id= parseInt(ele.id.split("_")[ele.id.split("_").length-1]);

        if($(ele).val()=='eol' && id!=1)
        {
            $('#modal_div_'+id).parent().remove();
            $(ele).parent().parent().remove();
        }
    }
    function checkNumberEndOfList(ele)
    {
        /*
        * function to remove the row
        * ele = element which fires the function
        */

        // getting row number of the element
        var id= parseInt(ele.id.split("_")[ele.id.split("_").length-1]);

        if($(ele).val()=='0' && id!=1)
        {
            $(ele).parent().parent().remove();
        }
    }
</script>
@endsection
@section('main_section')
    <section class="content">
        <div class="row">
            <div style="text-align:center" class="col-md-12">
                <h3>
                    <label for="company_name">{{AccountingCustomHelper::getCompanyName()}}</label>
                </h3>    
            </div>
        </div>
        <div id="app">
                @include('sections.flash-message')
                @yield('content') 
        </div>
        @if($errors->any())
        {{-- display all  the errors if they are present. --}}
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Default box -->
        @if(in_array(1, Request::get('userAlloweds')['section']))
        @endif

        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <div class="row">
                    <div class="col-md-5">
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/voucher.AccountingvoucherEntry')}}</h2>
                    </div>
                    <div class="col-md-2">
                    </div>
                    <form method="POST" novalidate  action="/accounting/voucher/entry" id="voucher_entry_form">

                        <div class="col-md-5">
                            <button class="btn bg-red btn-sm" onclick="post_dated()">Post Dated</button>
                            <button class="btn bg-red btn.sm" onclick="change_date()">Change Date</button>
                            {{-- modal form for change date --}}
                            <div id="myModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Change Date</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="current_date">Date :</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-css datepicker1" value="{{date('m/d/Y',strtotime($currrent_date))}}" name="date" id="current_date">
                                                </div>
                                            </div>
                                            <input type="button" onclick="change_date_api()" value="Change Date" class="btn btn-sm btn-success">
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br><br><br>
                        <div class="container-fluid wdt">
                            @csrf
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{__('accounting/voucher.voucherEntryFor')}}</label>
                                    <select class="select input-css form-control voucherType" name="voucherType" id="voucherType">
                                        <option value="default">Select Voucher Type</option>
                                        @foreach ($voucher_type as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <label>{{__('accounting/voucher.voucherFormType')}}</label>
                                    {{-- different form types --}}
                                    <select class="select input-css form-control form_type" name="form_type" id="form_type">
                                        <option value="default" selected="selected">Default Form Type</option>
                                        <option class="form_type_class" value="0">As Voucher</option>
                                        <option class="form_type_class" value="1">As Invoice</option>
                                        <option class="form_type_class" value="2">As Journal</option>
                                        <!-- <option class="form_type_class" value="3">As Memorandum</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                {{-- row displaying date and voucher number  --}}
                                <div class="col-md-4">
                                    <div class="col-md-4" id="voucherNumberName" class="voucherNumberName">        
                                        <label>{{__('accounting/voucher.voucherNumber')}}</label>
                                        <input type="hidden" name="voucher_number" id="voucherNumberElem" class="voucherNumberElem">
                                    </div>
                                    <div class="col-md-4" id="voucherNumber"></div>
                                    <div class="col-md-4" id="voucherNumber1"></div>
                                </div>
                                {{-- post dated div --}}
                                <div class="col-md-4">
                                    <div id="post_dated_div" style="display:none; text-align:center">
                                        <h3>Post Dated</h3>
                                    </div>
                                </div>
                                <div class="col-md-4" style="text-align:right;">
                                    <div id="post_dated_enabled_div" style="display:none">
                                        <div class="col-md-4">
                                            {{__('accounting/voucher.date')}}
                                        </div>
                                        <div class="col-md-8">
                                        <input type="text" class="form-control input-css datepicker1" value="{{date('m/d/Y',strtotime($currrent_date))}}"  name="voucherDate" id="voucherDate">
                                    </div>
                                </div>
                                <input type="hidden" name="is_post_dated" value="0" id="is_post_dated">
                                <div id="post_dated_disabled_div" style=" display:inline-block">
                                            {{__('accounting/voucher.date')}}
                                            {{date('l',strtotime($currrent_date))}}, {{date('d-M-Y',strtotime($currrent_date))}}
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- effective date div --}}
                                        <div style="text-align:right;display:none;" class="col-md-12 effectiveDateDiv" >Effective Date : {{session('Active_date')}}</div>
                                </div>
                            </div>

                            <br>
                            {{-- header div --}}
                            <div class="voucherEntryForm" id="voucherEntryFormAccount">
                            </div>
                            <br>
                            <hr>
                            {{-- content div --}}
                            <div class="voucherEntryForm" id="voucherEntryFormParticular">
                            </div>
                            <div class="form-group">
                                <input class="btn btn-success" type="submit">    
                            </div>    
                        </div>
                    </form>
                </div>
            </div>   
        </div>
    </section><!--end of section-->
@endsection


