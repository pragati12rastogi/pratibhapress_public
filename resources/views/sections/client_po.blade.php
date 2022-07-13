@extends($layout)

@section('title', __('client_po.title'))

@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class=""></i>client purchase order</a></li>
@endsection

@section('css')
<style>
    .help-block {
        color: red;
    }
</style>
@endsection

@section('js')
<script src="/js/views/client_po.js"></script>
<script src="/js/bootbox.min.js"></script>
<script src="/js/bootbox.locales.min.js"></script>
<script>
    // initializing prefix var to use in IO Select dropdown.
      var consignee;

</script>

<script>
$(document).on("click", ".close", function() {
    var sum = 0;
    $(".poqty").each(function(){
        sum += +$(this).val();
    });
    $("#qtys").val(sum);
    $("#qtyss").val(sum);
});
    @if(old('reference_name') != null)
        $(document).ready(function(e){
            $('#reference_name').val("{{old('reference_name')}}").trigger("change");
            $('.io_id ').val("{{old('io')}}").trigger("change");
        });
    @endif
    var counter=0;
    $('.addpo').on('click',function(){
      counter++
      addpoDirect(counter);
 });
 $('.reference_name').change(function(e) {
        var partyid = $(e.target).val();
       getio(partyid);
    });
    function getio(partyid){
        $(document).find($('#party_additional_data')).empty();
        $('#ajax_loader_div').css('display','block');
        $.ajax({
            url: "/details/" + partyid,
            type: "GET",
            success: function(result) {
                console.log(result);
                po_number_data=result['po_number'];
                $(".io_id").empty();
                $(".consignee").empty();
                $(".party").empty();                
               
                $(".io_id").append($('<option value="">Select Internal Order</option>'));
                for (var i = 0; i < result['io_list'].length; i++) {
                    $(".io_id").append($('<option value="' + result['io_list'][i].ioid + '">' + result['io_list'][i].io_number + '</option>'));
                }
                @if(old('io') != null)
      
                    $('.io_id ').val("{{old('io')}}").trigger("change");
                
                @endif 
                var party_opt = '<option value="" disabled>Select Client</option>';
                for (var i = 0; i < result['party'].length; i++) {
                    party_opt+='<option value="' + result['party'][i].id + '">' + result['party'][i].partyname + '</option>';
                }
                $(".party").append(party_opt);
                @if(old('party_name') != null)
                var Values=new Array();
                    @for( $i =0; $i < count(old('party_name')); $i++)  
                    // $('.party').val("{{ old('party_name.'.$i)}}").trigger("change");
                    var partyid ={{ old('party_name.'.$i)}};
                                getdetails(partyid);
                                Values.push("{{ old('party_name.'.$i)}}");
                                  
                    @endfor
                  
                    $('.party').val(Values).select2();
                    
                   
                
                @endif 
                $(".po_number_select").empty();
                $(".po_number_select").append($('<option value="" disabled>Select PO Number</option>'));
                for (var i = 0; i < result['po_number'].length; i++) {
                    $(".po_number_select").append($('<option value="'+result['po_number'][i].po_number+'">' + result['po_number'][i].po_number + '</option>'));
                }
                @if(old('po_number1') != null)
                @for( $i =0; $i < count(old('po_number1')); $i++)  
                    $('.pos_{{$i}}').val("{{ old('po_number1.'.$i)}}").trigger("change");
                               
                    @endfor
                @endif

              

                if(typeof io_l !== 'undefined' && io_l.length > 0){
                    $(".io_id").val(old_io).trigger("change");
                    $("input[name=is_po_provided]").val([old_prov]);
                    if(old_prov == 1){
                        $('#provided-desc').slideDown();
                        $("input[name=po_number]").val(old_po_number);
                        $("#payment_terms").val(old_payment_terms).trigger("change");
                        $('#item_desc').text(old_item_desc);
                        $("input[name=discount]").val(old_discount);
                        $("#tax_applicable").val(old_tax_app).trigger("change");
                    } else{
                        $('#provide-not-desc').slideDown();
                    }
                }
                $('#ajax_loader_div').css('display','none');

            }
        });
     
    }
    function getdetails(partyid){
       
            console.log("party_id",partyid);
          
                var id = partyid;
                var partyname = $('.party').find("option[value="+id+"]").text(); 
                if( $(document).find($('.party_detail_'+id)).length==1 )
                {
                    $(document).find($('.party_detail_'+id)).css("display","block");
                    console.log("hello done");
                }
                else if($(document).find($('.party_detail_'+id)).length==0)
                {
                    $(document).find($('.select2')).select2().select2('destroy');
                    var ele = $(document).find($('#party_data_first_row')).html();
                    var patt = new RegExp('_0"','g');
                    var newstr = '_'+id+'"';
                    var res =  ele.replace(patt,newstr);
                    $('#party_additional_data').append(res);       
                    $(document).find($('.select2')).select2(); 
                    $(document).find($('#payment_terms_'+id)).prop("name","payment_terms["+id+"]");
                    $(document).find($('.is_consignee_'+id)).prop("name","is_consignee["+id+"]");
                    $(document).find($('#consignee_name_'+id)).prop("name","consignee_name["+id+"][]");
                    $(document).find($('#consignee_qty_'+id)).prop("name","consignee_qty["+id+"][]");
                    $(document).find($('#consg-excel_'+id)).prop("name","excel["+id+"]");
                    $(document).find($('.party_detail_'+id)).css("display","block");
                    console.log('done');
                    $('#ajax_loader_div').css('display','block');
                    // @if(old('is_consignee') != null)
                    //         @for( $i =0; $i < count(old('is_consignee')); $i++)  
                    //             $('.list_avail is_consignee_'+id).val("{{ old('is_consignee.'.$i)}}").prop("checked",true);
                           
                    //         @endfor
                        
                        
                    // @endif 
                    party_details(id,partyname);

                }
            
                // $(document).find($('.party_detail')).each(function(){
                //     var id = $(this).attr("id");
                //     var num = parseInt(id.split("_")[id.split("_").length-1]);
                //     console.log("num",num , "partyid",partyid);
                //     if(partyid.indexOf(num+"")==-1 && num!=0)
                //     {
                //         $(this).remove();
                //         console.log("removed",num);
                //     }
                // });

    }
    function party_details(input,partyname){
        $.ajax({
            url: "/clientpo/details/party/" + input,
            type: "GET",
            success: function(result) {
                $(".consignee_"+input).empty();
                consignee[input] = result['consg_list'];
                party_pay[input]=result['party']
                console.log(input);
                
                console.log(party_pay);
                $('#partyname_'+input).append(partyname);    
                $(".consignee_"+input).append($('<option value="">Select Consignee</option>'));
                for (var i = 0; i < result['consg_list'].length; i++) {
                    $(".consignee_"+input).append($('<option value="' + result['consg_list'][i].id + '">' + result['consg_list'][i].consignee_name + '</option>'));
                    // $("#payment_terms_"+input).val().trigger("change");
                }
                    $("#payment_terms_"+input).val(result['party']['payment_term_id']).trigger("change");
             
                $("#verbal_payment_terms").val(result['party']['payment_term_id']).trigger("change");

                
                $('#ajax_loader_div').css('display','none');
    
            }
        });
     }



</script>
@endsection

@section('main_section')
<!-- Main content -->
<section class="content">
<a href="{{'/download/format/clientpo/consignee'}}" >
                <button class="btn btn-primary" style="margin-bottom:10px">Download Consignee Upload Format</button>
                </a> <br>
    <!-- Default box -->
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li style="list-style:none">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div id="sheet_cong" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">
                    <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <form enctype="multipart/form-data" files="true" id="form" action="/clientPoInsert" method="POST">
        @csrf
        <div class="box box-default">
            <div class="row">
                <div class="col-md-6">
                    <h3>{{__('client_po.mytitle')}}</h3>
                </div>
            </div>
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('client_po.Ref Name')}} <sup>*</sup></label>
                                <select class="form-control select2 reference_name input-css" aria-required="true" data-placeholder=""
                                    style="width: 100%;" name="reference_name" id="reference_name">
                                    <option value="">Select Client Reference Name</option>
                                    @foreach($feed['reference'] as $key)
                                    <option value="{{$key->id}}">{{$key->referencename}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('client_po.Internal Order')}} <sup>*</sup></label>
                                <select class="form-control select2 io_id input-css"  aria-required="true" name="io" data-placeholder="">
                                    <option value="">Select Internal Order</option>
                                </select>
                                {!! $errors->first('io', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row is_client">
                        <div class="col-md-12">
                            <label>{{__('client_po.Is client providing a PO?')}}<sup>*</sup></label>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input autocomplete="off" type="radio" {{old('is_po_provided')=="1" ? "checked=checked": ''}} class="yes is_po_provided" value="1"
                                        name="is_po_provided">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input autocomplete="off" type="radio" {{old('is_po_provided')=="0" ? "checked=checked": ''}}  class="verbal is_po_provided" value="0"
                                        name="is_po_provided">Verbal</label>
                                </div>
                            </div>
                            <div class="is_po_provided_label_er">
                                <label id="is_po_provided-error" class="error" for="is_po_provided"></label>
                                {!! $errors->first('is_po_provided', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="created_by" value="{{Auth::id()}}">
                    <div id="provided-desc"  {{old('is_po_provided')=="1" ? "style=display:block": "style=display:none"}}>
                        <h3>{{__('client_po.details')}}</h3><br>
                       
                            @php
                            $poo=0;
                            @endphp
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('client_po.Part Name')}} <sup>*</sup></label>
                                    <select aria-required="true" class="form-control party input-css select2" style="width: 100%;" name="party_name[]" multiple="multiple" id="party_select">
                                        <option value="">Select Reference Name First</option>
                                    </select>
                                    {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                     
                           
                        </div><br>
                        @if(old('po_type') != null)
                    @for( $i =0; $i < count(old('po_type')); $i++)   
                            <!-- $('.party').val("{{ old('party_name.'.$i)}}").select2();                   -->
                          
                   
                        <div class="border">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.po_type')}}<sup>*</sup></label>
                                        <div class="po_type_label_er">
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" id="poex_{{$i}}" {{old('po_type.'.$i)=="0" ? "checked=checked": ''}} value="0" checked class="po_type potype_{{$i}}" name="po_type[{{$i}}]" >New</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" id="poex_{{$i}}"  value="1" {{old('po_type.'.$i)=="1" ? "checked=checked": ''}} class="po_type potype_{{$i}}" name="po_type[{{$i}}]" >Existing</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.po_num')}}<sup>*</sup></label>
                                        <div  class="po_type_label_er old_po" id="old_po_{{$i}}"  {{old('po_type.'.$i)=="1" ? "style=display:block": "style=display:none"}}>
                                            <select aria-required="true" class="form-control select2 po_number1 pos_{{$i}} input-css po_type_label_er po_number_select old_po" data-placeholder=""
                                            style="width: 100%;" name="po_number1[{{$i}}]" id="poss_{{$i}}">
                                            <option value=" ">Select Client First</option>
                                            </select>
                                        
                                        </div>
                                        <div class="po_number_label_er new_po" id="new_po_{{$i}}"  {{old('po_type.'.$i)=="0" ? "style=display:block": "style=display:none"}}>
                                            <input type="text"  aria-required="true" step="any" value="{{ old('po_number.'.$i)}}" id="poos_{{$i}}" name="po_number[{{$i}}]" class="form-control  po_number input-css"
                                                placeholder="{{__('client_po.po_num')}}">
                                        </div>
                                        {!! $errors->first('po_number_{{$i}}', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                <div class="form-group">
                                            <label for="">PO Qty<sup>*</sup></label>
                                            <input type="number" name="poqty[{{$i}}]" value="{{ old('poqty.'.$i)}}" required id="poo_{{$i}}"  aria-required="true" class="form-control input-css poqty"
                                                placeholder="PO Quantity">
                                            {!! $errors->first('poqty_{{$i}}', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div>
                                <div class="col-md-3 po_date_label_er">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.po_date')}}<sup>*</sup></label>
                                        <input name="po_dates[{{$i}}]" id="po_date_{{$i}}"  value="{{ old('po_dates.'.$i)}}" aria-required="true" type="text" data-date-format='dd-mm-yyyy'
                                            class="form-control input-css po_dates datepicker1"
                                            placeholder="{{__('client_po.po_date')}}">
                                        {!! $errors->first('po_date_{{$i}}', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.po file')}}<sup>*</sup></label>
                                        <input name="po_files[0]" id="po_files" type="file" aria-required="true" class="form-control po_files input-css"
                                            placeholder="Discount">
                                        <p>Allowed Formats: pdf,jpg,png .</p>
                                        {!! $errors->first('po_file', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div><br>
                        </div>
                    @endfor
                    @else
                    @php 
                      $i=0;
                    @endphp
                    <div class="border">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.po_type')}}<sup>*</sup></label>
                                        <div class="po_type_label_er">
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" id="poex_{{$i}}" {{old('po_type.'.$i)=="0" ? "checked=checked": ''}} value="0" checked class="po_type potype_{{$i}}" name="po_type[{{$i}}]" >New</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" id="poex_{{$i}}"  value="1" {{old('po_type.'.$i)=="1" ? "checked=checked": ''}} class="po_type potype_{{$i}}" name="po_type[{{$i}}]" >Existing</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.po_num')}}<sup>*</sup></label>
                                        <div  class="po_type_label_er old_po" id="old_po_{{$i}}" style="display:none">
                                            <select aria-required="true" class="form-control select2 po_number1 pos_{{$i}} input-css po_type_label_er po_number_select old_po" data-placeholder=""
                                            style="width: 100%;" name="po_number1[{{$i}}]" id="poss_{{$i}}">
                                            <option value=" ">Select Client First</option>
                                            </select>
                                        
                                        </div>
                                        <div class="po_number_label_er new_po" id="new_po_{{$i}}">
                                            <input type="text"  aria-required="true" step="any"  id="poos_{{$i}}" name="po_number[{{$i}}]" class="form-control  po_number input-css"
                                                placeholder="{{__('client_po.po_num')}}">
                                        </div>
                                        {!! $errors->first('po_number_{{$i}}', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                <div class="form-group">
                                            <label for="">PO Qty<sup>*</sup></label>
                                            <input type="number" name="poqty[{{$i}}]" value="{{ old('poqty.'.$i)}}" required id="poo_{{$i}}"  aria-required="true" class="form-control input-css poqty"
                                                placeholder="PO Quantity">
                                            {!! $errors->first('poqty_{{$i}}', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div>
                                <div class="col-md-3 po_date_label_er">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.po_date')}}<sup>*</sup></label>
                                        <input name="po_dates[{{$i}}]" id="po_date_{{$i}}"  value="{{ old('po_dates.'.$i)}}" aria-required="true" type="text" data-date-format='dd-mm-yyyy'
                                            class="form-control input-css po_dates datepicker1"
                                            placeholder="{{__('client_po.po_date')}}">
                                        {!! $errors->first('po_date_{{$i}}', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.po file')}}<sup>*</sup></label>
                                        <input name="po_files[0]" id="po_files" type="file" aria-required="true" class="form-control po_files input-css"
                                            placeholder="Discount">
                                        <p>Allowed Formats: pdf,jpg,png .</p>
                                        {!! $errors->first('po_file', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div><br>
                        </div>
                    
                    @endif 
                        <div class="row posss">
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <button type="button" class="btn btn-success pull-left addpo" id="addpo_0">Add PO Details</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 po_type_label_er">
                                <div class="form-group">
                                    <label for="">{{__('client_po.hsn')}}<sup>*</sup></label>
                                    <select class="form-control select2 input-css hsn" aria-required="true" id="hsn_select" style="width: 100%;"
                                        name="hsn">
                                        <option value="">{{__('client_po.select')}} {{__('client_po.hsn')}}
                                        </option>
                                        @foreach($feed['hsn'] as $key)
                                        <option value="{{$key->id}}" {{old('hsn')==$key->id ? "selected=selected" : ''}}>{{$key->name}} - {{$key->hsn}} - {{$key->gst_rate}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-6 unit_of_measure_label_er">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.unit_m')}}<sup>*</sup></label>
                                        <select style="width:100%" id="unit_of_measure"
                                            class="form-control select2 input-css unit_of_measure"  aria-required="true" name="unit_of_measure">
                                            <option value="">{{__('client_po.select')}} {{__('client_po.unit_m')}}
                                            </option>
                                            @foreach($feed['uom'] as $key)
                                            <option value="{{$key->id}}" {{old('unit_of_measure')==$key->id ? "selected=selected" : ''}}>{{$key->uom_name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('unit_of_measure', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 item_desc_label_er">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.Item Description')}} <sup>*</sup></label>
                                        <textarea name="item_desc" id="item_desc" type="text"
                                            class="form-control input-css item" aria-required="true" placeholder="Item Description">{{old('item_desc')}}</textarea>
                                        {!! $errors->first('item_desc', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 delivery_date_label_er">
                                <div class="form-group">
                                    <label for="">{{__('client_po.Delivery Date')}}<sup>*</sup></label>
                                    <input name="delivery_date" data-date-format='dd-mm-yyyy' value="old('delivery_date')"  aria-required="true" id="delivery_date"
                                        type="text" class="form-control input-css delivery datepicker"
                                        placeholder="{{__('client_po.Delivery Date')}}">

                                    {!! $errors->first('delivery_date', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 qty_label_er">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.qty')}}<sup>*</sup></label>
                                        <input type="text" name="qtyss" min="0" disabled aria-required="true" value="{{old('qty')}}"  id="qtys" class="form-control input-css qty"
                                            placeholder="Quantity">
                                            <input type="hidden" name="qty" value="{{old('qty')}}" min="0"  id="qtyss" class="qty">
                                        {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row po">
                            <div class="col-md-6 per_unit_price_label_er">
                                <div class="form-group">
                                    <label>{{__('client_po.per_unit_price')}}<sup>*</sup></label>
                                    <input type="number" class="form-control per_unit_price input-css" value="{{old('per_unit_price')}}" aria-required="true" placeholder=""
                                        name="per_unit_price" id="per_unit_price">
                                    {!! $errors->first('per_unit_price', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-6 discount_label_er">
                                <div class="form-group">
                                    <label for="">{{__('client_po.discount')}}<sup>*</sup></label>
                                    <input name="discount" min="0" id="discount" type="number" value="{{old('discount')}}"   aria-required="true" step="any" class="form-control discount input-css"
                                        placeholder="Discount">
                                    {!! $errors->first('discount', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                       
                    </div>
                     {{--<div id="provide-not-desc" style="display:none;">
                        <div class="row ">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('client_po.pay_term')}}<sup>*</sup></label>
                                    <select disabled class="form-control select2 faltu input-css"
                                        style="width: 100%;" id="verbal_payment_terms" name="faltu">
                                        <option value="">{{__('client_po.select')}} {{__('client_po.pay_term')}}
                                        </option>
                                        @foreach($feed['pay_term'] as $key)
                                        <option value="{{$key->id}}">{{$key->value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div> <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        
        <div id="party_data_first_row" style="display:none">
            <div class="box box-default party_detail party_detail_0" id="party_detail_0" style="display_none">
                <div class="box-header with-border">
                    <h4 id="partyname_0"></h4>
                    <h3 class="box-title">Party Details</h3>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 payment_terms_label_er">
                                <div class="form-group">
                                    <label for="">{{__('client_po.pay_term')}}<sup>*</sup></label>
                                    <select name="pay" class="form-control select2 input-css payment"
                                        style="width: 100%;" id="payment_terms_0" >
                                        
                                        <option value="">{{__('client_po.select')}} {{__('client_po.pay_term')}}
                                        </option>
                                        @foreach($feed['pay_term'] as $key)
                                        <option value="{{$key->id}}">{{$key->value}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('payment_terms', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="form-group is_consignee_label_er">
                                <div class="col-md-6">
                                    <label>{{__('client_po.Consignee list Available?')}}<sup>*</sup></label>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="col-sm-6 col-md-6">
                                        <div class="radio">
                                            <label style="font-style:bold">
                                                <input class="list_avail is_consignee_0" name="is_con" onchange="list_avail_change_list_option(this)" autocomplete="off"
                                                    type="radio"  value="1" id="cons"> Yes </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="radio">
                                            <label style="font-style:bold">
                                                <input class="list_avail is_consignee_0" name="is_con" onchange="list_avail_change_list_option(this)" autocomplete="off"
                                                    value="0" type="radio">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                    <div class="list_avail_label_er">
                                        {!! $errors->first('list_avail', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div>
                            <label id="list_avail-error" class="error" for="is_po_provided"></label>
                        </div>
                        <div class="cons-collect cons-collect_0" >
                            <div class="cons-list cons-list_0">                   
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Consignee Name <sup>*</sup></label>
                                            <select id="consignee_name_0" style="width:100%"  class="form-control select2 cong_in input-css consignee_0" name="cons" onchange="updateiodata(this)">
                                                
                                                <option value="">select party first</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Quantity <sup>*</sup></label>
                                            <input id="consignee_qty_0"  type="number" class="form-control input-css cong_qwty" name="quan" step="any" placeholder="Quantity">
                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cons-collect-file cons-collect-file_0"  style="display:none">
                                <div class="form-group">
                                    <label for="consg-excel">Consignee List Upload</label>
                                    <input type="file" required id="consg-excel_0" name="excel">
                                    <p>An excel file including new consignee details and Quantity.</p>
                                </div>
                            </div>
                        </div>                    
                        <div class="row">
                            <div class="form-group">
                                <button type="button" class="btn btn-success pull-right addConsignee" id="addConsignee_0" onclick="addConsigneeDirect(this)">Add More</button>
                            </div>
                        </div>
                            
                    </div> <!-- /.box-body -->
                </div> <!-- /.box -->
                <hr style="background: grey;height: 1px;">
            </div>
        </div>    
        <div id="party_additional_data" {{old('is_po_provided')==1 ? "style=display:block" : "style=display:none"}}>

        </div>
        <div class="divrow">
            <button type="submit" class="btn btn-primary submit">Submit</button>
        </div>
    </form>
</section>
@endsection
