@extends($layout)

@section('title', __('client_po.title'))

@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class=""></i>Client Purchase Order</a></li>
@endsection

@section('css')
    <style>
        .help-block {
            color: red;
        }
    </style>
@endsection

@php
        $d = $data[0];
        $old_is_consignee = old('is_consignee');
        $is_consignee = isset($old_is_consignee)?$old_is_consignee:$d->is_consignee;
@endphp

@section('js')
    <script src="/js/bootbox.min.js"></script>
    <script src="/js/bootbox.locales.min.js"></script>
    <script>
      
    // initializing prefix var to use in IO Select dropdown.
        var prefix_io = "{{$feed['prefix_io']}}/";
        var consignee ;
        var io_l = [];

        @if($data[0]->is_po_provided==1)
            var partyid = "{{$party[0]->party_name}}";
            var ref_name ="{{$d->reference_name}}";
        
           
        $(document).ready(function(e) 
        {
    
            $('#ajax_loader_div').css('display','block');
            $.ajax(
            {
                url: "/details/" + ref_name,
                type: "GET",
                success: function(result) 
                {
                    if(typeof io_l !== 'undefined' && io_l.length > 0)
                    {
                        
                        $(".io_id").val(old_io).trigger("change");

                        $("input[name=is_po_provided]").val([old_prov]);
                        if(old_prov == 1)
                        {
                            $('#provided-desc').slideDown();
                            $("input[name=po_number]").val(old_po_number);
                            $("#payment_terms").val(old_payment_terms).trigger("change");
                            $('#item_desc').text(old_item_desc);
                            $("input[name=discount]").val(old_discount);
                            $("#tax_applicable").val(old_tax_app).trigger("change");
                        } 
                        else
                        {
                            $('#provide-not-desc').slideDown();
                        }
                    }
                    $('#ajax_loader_div').css('display','none');

                }
            });
            $.ajax(
            {
                url: "/clientpo/details/party/" + partyid,
                type: "GET",
                success: function(result) 
                {
                    consignee = result['consg_list'];
                }
            });
            $('#ajax_loader_div').css('display','none');
       });
       $('.party').val(partyid).trigger('change');
       @endif
    </script>
    <script src="/js/views/client_po.js"></script>
 <script>
     var party_id=new Array();
     var m=0;
  @foreach($party as $item)
            party_id[m]="{{$item['party_name']}}";
            m=m+1;
           
@endforeach

console.log(party_id);

$('#party_select').val(party_id).trigger('change');

 </script>   
 <script>
 function addConsignee1(party_id){
    // console.log(party_id);
var x={!! json_encode($consignees, JSON_HEX_TAG) !!};
// console.log(x[partyid].length);

    var ls = '<div class="box">' +
        '<div class="row" style="margin-top:0px;">' +
        '<div class="col-md-10">' +
        '</div>' +
        '<div class="col-md-2" style="float:right;">' +
        '<button type="button" class="close" onclick="$(this).parent().parent().parent().remove();" id="removeconsignee" >X</button>' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-md-6">' +
        ' <div class="form-group">  ' +
        '<label for="">Consignee  Name <sup>*</sup></label>' +
        '<select  id="consignee_name' + cnt_c + '" style="width:100%" class="form-control select2 consignee cong_in input-css" name="consignee_name['+party_id+'][]">'+
        '<option value="">Select Consignee</option>';
        for(var m=0;m<x[party_id].length;m++){
            console.log(x[party_id][m]);
            for(var m=0;m<x[party_id].length;m++){
            ls=ls+'<option value="'+x[party_id][m]['id']+'">'+x[party_id][m]['consignee_name']+'</option>';
        }
        }
            
        ls=ls+'</select>  ' ;
        //   var index = Object.keys(consignee).indexOf(party_id);
        // var arr = Object.values(consignee)[index];
        // for (var i = 0; i <  arr.length; i++) {
        //     ls = ls + '<option value="' + arr[i].id + '">' + arr[i].consignee_name + '</option>';
        // }
    //     // console.log('argument',consignee[party_id].length);
    //     // for (var i = 0; i <  consignee[party_id].length; i++) {
    //     //     ls = ls + '<option value="' + consignee[party_id][i].id + '">' + consignee[party_id][i].consignee_name + '</option>';
    // }
    $('.cons-list_'+party_id).append(ls +
       
        '</div>' +
        '</div>' +
        '<div class="col-md-6">' +
        ' <div class="form-group">  ' +
        '<label for="">Quantity <sup>*</sup></label>' +
        '<input id="consignee_qty' + cnt_c + '" type="number" step="any" class="form-control input-css cong_qwty" placeholder="Quantity" name="consignee_qty['+party_id+'][]">' +
        '</div>' +
        '</div>' +
        '</div><br><br>' +
        '</div>'
    );
    $('.select2').select2();
    cnt_c = cnt_c + 1;
    $(document).find('.consignee_'+party_id).select2();

}
function addConsigneeDirect1(ele){
    var id = $(ele).attr("id").split("_");
    id = id[id.length-1];
    $('.cons-collect_'+id).show();
    // console.log(id);
    
    addConsignee1(id);
}
 </script>

 <script>
   $(document).on("change", ".poqty", function() {
        var sum = 0;
        $(".poqty").each(function(){
            sum += +$(this).val();
        });
        $("#qtys").val(sum);
        $("#qtyss").val(sum);
    });
    $(document).on("click", ".close", function() {
        var sum = 0;
        $(".poqty").each(function(){
            sum += +$(this).val();
        });
        $("#qtys").val(sum);
        $("#qtyss").val(sum);
    });
  var counter=0;
    $('.addpo').on('click',function(){
      counter++
      addpoDirect(counter);
 })
function addpoDirect(ins){
  var po_number_data={!! json_encode($po_number->toArray(), JSON_HEX_TAG) !!};
  var ls = '<div class="box">' +
    '<div class="row" style="margin-top:0px;">' +
    '<div class="col-md-10">' +
    '<h3 class="fieldset-title"></h3>' +
    '</div>' +
    '<div class="col-md-2" style="float:right;">' +
    '<button type="button" class="close" onclick="$(this).parent().parent().parent().remove();" id="removeconsignee" >X</button>' +
    '</div>' +
    '<div class="row">'+
   ' <div class="col-md-6">'+
       ' <div class="form-group">'+
           ' <label for="">PO Type<sup>*</sup></label>'+
            '<div class="po_type_label_er">'+
                '<div class="col-md-2">'+
                    '<div class="radio">'+
                        '<label><input type="radio" value="0" id="poex_'+ins+'" checked class="po_type potype_'+ins+'" name="po_type['+ins+']" >New</label>'+
                   ' </div>'+
                '</div>'+
                '<div class="col-md-2">'+
                   ' <div class="radio">'+
                        '<label><input type="radio" value="1" id="poex_'+ins+'" class="po_type potype_'+ins+'" name="po_type['+ins+']" >Existing</label>'+
                   ' </div>'+
               ' </div>'+
            '</div>'+
        '</div>'+
    '</div>'+
    
   ' </div>'+
    '<div class="row">'+
    '<div class="col-md-3">'+
            '<div class="form-group">'+
                '<label for="">P.O. Number<sup>*</sup></label>'+
                '<div style="display: none" class="po_type_label_er old_po" id="old_po_'+ins+'">'+
                    '<select class="form-control select2 po_number1 po_number_select input-css" data-placeholder="" id="poss_'+ins+'" style="width: 100%;" name="po_number1['+ins+']" >';
                    for (var i = 0; i < po_number_data.length; i++) {
                        ls=ls+'<option value="">Select Po</option><option value="'+po_number_data[i].po_number+'">' + po_number_data[i].po_number + '</option>';
                    }
                   ls=ls+' </select>'+
                   
                '</div>'+
                '<div  class="po_number_label_er new_po" id="new_po_'+ins+'">'+
                    '<input type="text"  aria-required="true" step="any" id="poos_'+ins+'" id="po_number_text" name="po_number['+ins+']" class="form-control po_number input-css"  placeholder="P.O. Number">'+
                '</div>'+
               
            '</div>'+
        '</div>'+
        '<div class="col-md-3">'+
        '<div class="form-group">'+
                    '<label for="">PO Qty<sup>*</sup></label>'+
                    '<input type="number" name="poqty['+ins+']" id="poo_'+ins+'" required  aria-required="true" class="form-control input-css poqty" placeholder="PO Quantity">'+
                   
                '</div>'+
       ' </div>'+
     '  <div class="col-md-3">'+
       '<div class="form-group">'+
           '<label for="">PO Date<sup>*</sup></label>'+
          ' <input name="po_dates['+ins+']" id="po_dates_'+ins+'" aria-required="true" type="text" class="form-control input-css po_dates datepicker" placeholder="PO Date">'+
      ' </div>'+
   '</div>'+
        '<div class="col-md-3">'+
        '<div class="form-group">'+
               ' <label for="">Client Purchase Order File<sup>*</sup></label>'+
               ' <input name="po_files['+ins+']" id="pof_'+ins+'" type="file" aria-required="true" class="form-control po_files input-css" placeholder="Discount">'+
                '<p>Allowed Formats: pdf,jpg,png .</p>'+
                
           ' </div>'+
        '</div>'+
    '</div><br>'+
    '</div>' +
    '</div>' ;

  $(".posss").append(ls);
  $('.select2').select2();
  $('.datepicker').datepicker({
        autoclose: true,
        format: 'd-m-yyyy'
  
    }).datepicker("setDate", new Date());
}
 </script>
@endsection

@section('main_section')
<!-- Main content -->
<section class="content">
<!-- Default box -->
<a href="{{'/download/format/clientpo/consignee'}}" >
                <button class="btn btn-primary" style="margin-bottom:10px">Download Consignee Upload Format</button>
                </a> <br>
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form enctype="multipart/form-data" id="form" action="/clientpo/updatedb/{{$id}}" files="true" method="POST">
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
                        <div class="col-md-8 {{ $errors->has('update_reason') ? 'has-error' : ''}}">
                            <label>Update Reason<sup>*</sup></label>
                            <input type="text" autocomplete="off" required value="{{$errors->any() ? old('update_reason') : ''}}" class="form-control  input-css" name="update_reason" class="update_reason">
                            {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                        </div><!--col-md-3-->
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('client_po.Ref Name')}}{{$d->p_ref_name}} <sup>*</sup></label>
                                <select class="form-control select2 reference_name input-css" name="ref_disabled" disabled aria-required="true" data-placeholder=""
                                    style="width: 100%;"  id="reference_name">
                                    <option value="default" disabled>Select Client Reference Name</option>
                                    @foreach($feed['reference'] as $key)
                                        <option value="{{$key->id}}" {{$key->id == $d->reference_name  ? 'selected="selected"':''}}>{{$key->referencename}}</option>
                                    @endforeach
                                </select>
                                
                            <input type="hidden" name="reference_name" value="{{$d->reference_name}}">
                                {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('client_po.Internal Order')}} <sup>*</sup></label>
                                <select class="form-control select2 io_id input-css" name="io_disabled" disabled data-placeholder="">
                                    @foreach ($internalorder as $key)                                    
                                        <option value="{{$key->id}}" {{$key->id==$d->io? 'selected="selected"' :''}}>{{$key->io_number}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="io" value="{{$d->io}}">
                                {!! $errors->first('io', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row is_client">
                        <div class="col-md-12">
                            <label>{{__('client_po.Is client providing a PO?')}}<sup>*</sup></label>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input autocomplete="off" {{$d->is_po_provided==1?'checked="checked"':''}} type="radio" class="yes" value="1"
                                    name="is_po_provided">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input autocomplete="off" type="radio" {{$d->is_po_provided==0?'checked="checked"':''}} class="verbal" value="0"
                                    name="is_po_provided">Verbal</label>
                                </div>
                            </div>
                            <div class="is_po_provided_label_er">
                                {!! $errors->first('is_po_provided', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="created_by" value="{{Auth::id()}}">   
                    

                    <div id="provided-desc" style="{{$d->is_po_provided==0?'display:none':''}}">
                        <h3>{{__('client_po.details')}}</h3><br>
                        <div class="row">
                            <div class="col-md-6">     
                                <div class="form-group">
                                    <label>{{__('client_po.client_name')}} <sup>*</sup></label>
                                   
                                    <select class="form-control select2 party input-css"  data-placeholder="" multiple
                                    style="width: 100%;" name="party_name[]" id="party_select">
                                        <option value="default" disabled>Select Client</option>
                                        @foreach($feed['party'] as $key)
                                            <option value="{{$key->id}}">{{$key->partyname}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('party_name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                      
                        @foreach($po as $item)
                        <input type="hidden" name="old_po_num[{{$item->id}}]" value="{{$item->po_number}}">
                        <input type="hidden" name="old_po_data_id[]" value="{{$item->id}}">
                     
                       
                        <div class="border">
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{__('client_po.po_num')}}<sup>*</sup></label>
                                    <div class="po_number_label_er new_po" id="new_po_0">
                                        <input type="text" value="{{$item->po_number}}" aria-required="true" step="any" id="poos" name="po_number_old[{{$item->id}}]" class="form-control  po_number input-css"
                                            placeholder="{{__('client_po.po_num')}}">
                                    </div>
                                    {!! $errors->first('po_number', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                            <div class="form-group">
                                        <label for="">PO Qty<sup>*</sup></label>
                                        <input type="number" value="{{$item->po_qty}}" name="poqty_old[{{$item->id}}]" required id="poold_{{$item->id}}"  aria-required="true" class="form-control input-css poqty"
                                            placeholder="PO Quantity">
                                        {!! $errors->first('poqty', '<p class="help-block">:message</p>') !!}
                                    </div>
                            </div>
                            <div class="col-md-3 po_date_label_er">
                                <div class="form-group">
                                    <label for="">{{__('client_po.po_date')}}<sup>*</sup></label>
                                    <input name="po_dates_old[{{$item->id}}]" value="{{$item->po_date}}" id="po_date"  type="text" 
                                        class="input-css po_dates datepicker1">
                                    {!! $errors->first('po_date', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                            <div class="form-group">
                                    <label for="">{{__('client_po.po file')}}<sup>*</sup></label>
                                    <input name="po_files_old[{{$item->id}}]" value="{{$item->po_upload}}" id="po_files" type="file" aria-required="true" class="form-control po_filess input-css"
                                        placeholder="Discount">
                                    <p>Allowed Formats: pdf,jpg,png .</p>
                                    @if($item['po_upload'])
                                                    @php 
                                                    $file_types=explode('.',$item['po_upload']);
                                                    $file_types = $file_types[count($file_types)-1];
                                                    @endphp

                                                    <a href="/upload/clientpo/{{$item->po_upload}}" target="_blank"><u>View File</u></a>
                                            @else
                                                    <p style="color:green">No File uploaded</p>
                                            @endif
                                    {!! $errors->first('po_file', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div><br>
                        </div>
                            @endforeach
                            <div class="row posss">
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <button type="button" class="btn-xs btn-success pull-right addpo" id="addpo_0">Add More</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{__('client_po.hsn')}}<sup>*</sup></label>
                                    <select class="form-control select2 input-css" id="hsn_select" style="width: 100%;"
                                        name="hsn">
                                        <option value="default">{{__('client_po.select')}} {{__('client_po.hsn')}}
                                        </option>
                                        @foreach($feed['hsn'] as $key)
                                            <option value="{{$key->id}}" {{$d->hsn==$key->id ? 'selected="selected"' :''}}>{{$key->name}} - {{$key->hsn}} - {{$key->gst_rate}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{__('client_po.discount')}}<sup>*</sup></label>
                                    <input name="discount" value="{{$d->discount}}" type="number" step="any" class="form-control input-css"
                                    placeholder="Discount">
                                    {!! $errors->first('discount', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.Item Description')}} <sup>*</sup></label>
                                        <textarea name="item_desc" id="item_desc" type="text"
                                        class="form-control input-css" placeholder="Item Description">{{$d->item_desc}}</textarea>
                                        {!! $errors->first('item_desc', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{__('client_po.Delivery Date')}}<sup>*</sup></label>
                                    <input name="delivery_date" value="{{CustomHelpers::showDate($d->delivery_date,'d-m-Y')}}" data-date-format='dd-mm-yyyy' id="delivery_date" type="text" class="form-control input-css datepicker1" placeholder="{{__('client_po.Delivery Date')}}">

                                    {!! $errors->first('delivery_date', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">{{__('client_po.qty')}}<sup>*</sup></label>
                                        <!-- <input type="text" value="{{$d->qty}}" name="qty" class="form-control input-css qty"
                                        placeholder="Quantity"> -->
                                        <input type="text" name="qtyss" value="{{$d->qty}}" max="{{$d->max_qty}}" min="0" disabled aria-required="true" id="qtys" class="form-control input-css qty"
                                            placeholder="Quantity">
                                            <input type="hidden" value="{{$d->qty}}" name="qty" max="{{$d->max_qty}}" min="0"  id="qtyss" class="qty">
                                        {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="row po">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('client_po.unit_m')}}<sup>*</sup></label>
                                <select style="width:100%" id="unit_of_measure"
                                class="form-control select2 input-css" name="unit_of_measure">
                                    <option value="default">{{__('client_po.select')}}
                                        {{__('client_po.unit_m')}}
                                    </option>
                                    @foreach($feed['uom'] as $key)
                                        <option value="{{$key->id}}" {{$d->unit_of_measure==$key->id ? 'selected="selected"' : ''}}>{{$key->uom_name}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('unit_of_measure', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('client_po.per_unit_price')}}<sup>*</sup></label>
                                <input type="number" value="{{$d->per_unit_price}}" class="form-control input-css" placeholder="Quantity"
                                name="per_unit_price" id="per_unit_price">
                                {!! $errors->first('per_unit_price', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="row po">
                           
                           
                        </div>
                        <div id="usr_upload_img">
                                <div class="container">
                                    <div class="modal fade usr_img_modal" id="usr_img_modal" role="dialog">
                                    <div class="modal-dialog modal-lg">
                                     <!-- Modal content-->
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Bill Wise Details</h4>
                                            <hr>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                @if($file_type=="")
                                                    <img height="480" width="720" alt="No File Uploaded">  
                                                @elseif ($file_type=="pdf")
                                                    <embed src="/upload/clientpo/{{$d->po_file_name}}" height="480" width="720" type="application/pdf">
                                                @else
                                                    <img src="/upload/clientpo/{{$d->po_file_name}}" height="480" width="720" alt="No File Uploaded">
                                                @endif
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     
                    </div>       
                    {{-- <div id="provide-not-desc" style="{{$d->is_po_provided==1?'display:none;':''}}"> --}}
                        {{-- <div class="row ">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('client_po.pay_term')}}<sup>*</sup></label>
                                    <select disabled class="form-control select2 input-css"
                                    style="width: 100%;" id="verbal_payment_terms" name="faltu">
                                        <option value="default">{{__('client_po.select')}} {{__('client_po.pay_term')}}
                                        </option>
                                        @foreach($feed['pay_term'] as $key)
                                            <option value="{{$key->id}}" >{{$key->value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                {{-- </div> <!-- /.box-body --> --}}
            </div><br>
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
                                        <select class="form-control select2 input-css payment" name="pay"
                                            style="width: 100%;" id="payment_terms_0" >
                                            {{-- name="payment_terms" --}}
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
                                                    {{-- name="is_consignee" --}}
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
                            <div class="cons-collect cons-collect_0" style="display:none">
                                <div class="cons-list cons-list_0">                   
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Consignee Name <sup>*</sup></label>
                                                <select id="consignee_name_0" style="width:100%" name="cons"
                                                    class="form-control select2 cong_in input-css consignee_0">
                                                    {{-- name="consignee_name[]" --}}
                                                    <option value="">select party first</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Quantity <sup>*</sup></label>
                                                <input id="consignee_qty_0"  type="number" name="quan"
                                                    class="form-control input-css cong_qwty" step="any" placeholder="Quantity">
                                                    {{-- name="consignee_qty[]" --}}
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

                </div>
        </div> 
            <div id="party_additional_data">
                @foreach ($data1 as $valu=>$vs)
                {{-- {{$vs['is_consignee']}} --}}
                    <input type="hidden" name="old_party[]" value="{{$vs['partyid']}}">
                    <input type="hidden" name="client_po_party_id[{{$vs['partyid']}}]" value="{{$vs['client_po_party_id']}}">
                    <div class="box box-default party_detail party_detail_{{$vs['partyid']}}" id="party_detail_{{$vs['partyid']}}" style="display_none">
                        <div class="box-header with-border">
                        <h4 id="partyname">{{$vs['partyname']}}</h4>
                        <h3 class="box-title">Party Details</h3>
                        {{-- {{$vs['consignee']}}   --}}
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6 payment_terms_label_er">
                                        <div class="form-group">
                                            <label for="">{{__('client_po.pay_term')}}<sup>*</sup></label>
                                            <select class="form-control select2 input-css payment" style="width: 100%;" id="payment_terms_{{$vs['partyid']}}" name="payment_terms[{{$vs['partyid']}}]">
                                                {{-- name="payment_terms" --}}
                                                <option value="">{{__('client_po.select')}} {{__('client_po.pay_term')}}
                                                </option>
                                                @foreach($feed['pay_term'] as $key)
                                                <option value="{{$key->id}}" {{$vs['payment_terms']==$key->id ? 'selected=selected' : ''}}>{{$key->value}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('payment_terms', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="form-group is_consignee_label_er">
                                        <div class="col-md-6">
                                            <label>{{__('client_po.Consignee list Available?')}}<sup>*</sup></label>
                                        </div>
                                        {{-- {{$valu['is_consignee']}} --}}
                                        <div class="col-md-6 col-sm-12">
                                            <div class="col-sm-6 col-md-6">
                                                <div class="radio">
                                                    <label style="font-style:bold">
                                                        {{-- name="is_consignee" --}}
                                                        <input class="list_avail is_consignee_{{$vs['partyid']}}" {{$vs['is_consignee']=='1' ? 'checked=checked' : ''}} name="is_consignee[{{$vs['partyid']}}]" onchange="list_avail_change_list_option(this)" autocomplete="off"
                                                            type="radio" value="1"  id="cons"> Yes </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="radio">
                                                    <label style="font-style:bold">
                                                        <input class="list_avail is_consignee_{{$vs['partyid']}}" {{$vs['is_consignee']=='0' ? 'checked=checked' : ''}} name="is_consignee[{{$vs['partyid']}}]" onchange="list_avail_change_list_option(this)" autocomplete="off"
                                                            value="0" type="radio" id="cons">
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
                                <div class="cons-collect cons-collect_{{$vs['partyid']}}" {{$vs['is_consignee']=='1' ? 'style=display:block' : 'style=display:none'}} >
                                    <div class="cons-list cons-list_{{$vs['partyid']}}">   
                                        @php
                                            $consignee=explode(',',$vs['consignee']);
                                            $qty=explode(',',$vs['cpoc_qty']);
                                            $con_id=explode(',',$vs['client_po_id']);
                                            
                                        @endphp   
                                       
                                            @foreach ($con_id as $cons)
                                                <input type="hidden" name="con_id[{{$vs['partyid']}}][]" value="{{$cons}}">
                                            @endforeach   

                                            
                                        @foreach ($consignee as $ls=>$ls_valu)
                                           <div class="row">
                                                
                                                {{-- @foreach ($consignees[$valu['partyid']] as $con)
                                                {{-- <option value="{{$con['id']}}" {{$ls_valu==$con->id ? 'selected=selected' : ''}}>{{$con['consignee_name']}}</option> --}}
                                               
                                                       {{-- @endforeach --}}
                                              
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Consignee Name <sup>*</sup></label> 
                                                        <select id="consignee_name_{{$vs['partyid']}}" style="width:100%" name="consignee_name[{{$vs['partyid']}}][]"
                                                            class="form-control select2 cong_in input-css consignee_{{$vs['partyid']}}">
                                                            {{-- name="consignee_name[]" --}}
                                                            <option value="">Select Consignee</option>
                                                           @foreach ($consignees[$vs['partyid']] as $con)
                                                    <option value="{{$con['id']}}" {{$ls_valu==$con->id ? 'selected=selected' : ''}}>{{$con['consignee_name']}}</option>
                                                           @endforeach
                                                        </select>
                                                        
                                                    </div>
                                                    {{-- <div class="div" style="display:none"> 
                                                            <select  id="old_consignee_{{$vs['partyid']}}" style="width:100%" name="old_consignee[{{$vs['partyid']}}][]"
                                                            class="form-control select2 cong_in input-css consignee_{{$vs['partyid']}}">
                                                            {{-- name="consignee_name[]" --}}
                                                            {{-- <option value=""></option>
                                                           @foreach ($consignees[$vs['partyid']] as $con)
                                                           <option value="{{$con['id']}}" {{$ls_valu==$con->id ? 'selected=selected' : ''}}>{{$con['consignee_name']}}</option>
                                                           @endforeach
                                                        </select>
                                                    </div> --}} 
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Quantity <sup>*</sup></label>
                                                        <input type="hidden" name="old_quantity[{{$vs['partyid']}}][]" value="{{$qty[$ls]}}">
                                                        <input type="hidden" name="old_consignee[{{$vs['partyid']}}][]" value="{{$consignee[$ls]}}">
                                                        
                                                        <input id="consignee_qty_{{$vs['partyid']}}" type="number"
                                                    class="form-control input-css cong_qwty" value="{{$qty[$ls]}}" name="consignee_qty[{{$vs['partyid']}}][]" step="any" placeholder="Quantity">
                                                            {{-- name="consignee_qty[]" --}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="cons-collect-file cons-collect-file_{{$vs['partyid']}}"  style="display:none">
                                        <div class="form-group">
                                            <label for="consg-excel">Consignee List Upload</label>
                                            <input type="file" required id="consg-excel_{{$vs['partyid']}}" name="excel[{{$vs['partyid']}}]">
                                            <p>An excel file including their Name and Quantity.</p>
                                        </div>
                                    </div>
                                </div>                    
                                <div class="row">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-success pull-right addConsignee" id="addConsignee_{{$vs['partyid']}}" onclick="addConsigneeDirect1(this)">Add More</button>
                                    </div>
                                </div>
                                    
                            </div> <!-- /.box-body -->
                        </div> <!-- /.box -->
                        <hr style="background: grey;height: 1px;">
                    </div> 
        @endforeach
            </div><br>
        <div class="divrow">
            <button type="submit" class="btn btn-success submit">Submit</button>
        </div><br>
    </form>
</section>

@endsection
