@extends($layout)

@section('title', __('delivery_challan.title'))
{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)
@section('breadcrumb')
<li><a href="#"><i class="fa fa-upload"></i>{{__('delivery_challan.title')}}</a></li>
@endsection
@section('css')
@endsection
@section('js')
<script src="/js/views/delivery_challan.js"></script>
<script>
    var selected_io = {};
    $(document).ready(function() {            
        
            $('input[type=radio][name=goods_dispatch]').change(function() {
                if (this.value == "1") {
                    $('.form1-a').hide();
                    $('.form1-b').hide();
                    var trans={!! json_encode($trans->toArray()) !!};
                    $(".company").empty();
                    for (var i = 0; i < trans.length; i++) {
                        $(".company").append($('<option value="' + trans[i].id + '">'+ trans[i].courier_name + '</option>'));
                    }
                $('.form1-c').show();
                }
                if (this.value == "3") {
                    $('.form1-a').hide();
                    $('.form1-c').hide();
                    var courier={!! json_encode($courier->toArray()) !!};
                    $(".company").empty();
                    for (var i = 0; i < courier.length; i++) {
                        $(".company").append($('<option value="' + courier[i].id + '">'+ courier[i].courier_name + '</option>'));
                    }
                    $('.form1-b').show();
                }
                if (this.value == "2") {
                    $('.form1-b').hide();
                    $('.form1-c').hide();
                    var self = {!! json_encode($self->toArray()) !!};
                    for (var i = 0; i < self.length; i++) {
                        $(".self-name").append($('<option value="' + self[i].id + '">'+ self[i].courier_name + '</option>'));
                    }
                    $('.form1-a').show();
                    
                }
            });
        });
</script>
<script>

    function onIoChangeDoStuff(id, f, valx, isDrop){
        var ielm = $("#"+id).parent().parent().parent().find(f);
        ielm.val(valx);
        if(isDrop){
            ielm.trigger('change');
            $(ielm).rules("add", {
                required  : true,
                notValidIfSelectFirst: "default",
            });
        } else{
            $(ielm).rules("add", {
                required : true,
                minlength:1,
            });
        }
    }
    function updateiodata(e)
        {
            var io = $(e).val();
            var id = $(e).attr('id');
        
            var er = '<label class="error enox' + id +'">' +
                'This Internal Order has already been selected.</label>';
            for (var k in selected_io) {
                if ($('#' + k).length > 0 && selected_io[k] == io) {
                    $(er).insertAfter(e);
                    return;
                }
            }
            selected_io[id] = io;
            $('.enox' + id).remove();

            if(io>0)
            {
                $('#ajax_loader_div').css('display','block');

                $.ajax({
                    url: "/io/details/" + io,
                    type: "GET",
                    success: function(result) {
                       
                        
                        var value = result['0']['details']['0'];
                        var qty_done=result['0']['qty']['good_qty'];
                        var qty_tot=value['qty'];
                        var qty_left=qty_tot-qty_done;
                        onIoChangeDoStuff(id, '.uom_input', value['unit'], 1);
                        onIoChangeDoStuff(id, '.del_date_input', value['delivery_date'], 0);
                        onIoChangeDoStuff(id, '.pak_input', value['pak_input'], 0);
                        onIoChangeDoStuff(id, '.goods_desc_input', value['goods_desc_input'], 0);
                        $("#"+id).parent().parent().parent().find('.goods_qty_input').attr('placeholder',("Max:" + value['left_qty']));
                        $("#"+id).parent().parent().parent().find('.goods_qty_input').attr('data-qty', result[0]['left_qty']);
                        $("#"+id).parent().parent().parent().find('.goods_qty_input').attr('max', result[0]['left_qty']);

                        $("#"+id).parent().parent().parent().find('.rate_per_qty_input').val(value['rate_per_qty']);
                        $("#"+id).parent().parent().parent().find('.rate_per_qtys_input').val(value['rate_per_qty']);
                        $("#"+id).parent().parent().parent().find('.gst_rate_input').val(value['gst_rate']);
                        var idp = $("#"+id).parent().parent().parent().find('.goods_qty_input');
                        idp.val(value['goods_qty_input']);
                        var x = value['left_qty'];
                        $(idp).rules("remove");
                        $(idp).rules("add", {
                            required : true,
                            range:[0,x],
                            digits:true
                        });
                        $('#ajax_loader_div').css('display','none');

                    }
                });
            }
        }
        function getClient(name)
        {
            $('#ajax_loader_div').css('display','block');
           
            $.ajax({
                url: "/party/details/byref/" + name,
                type: "GET",
                success:function(result) {
                    consignee = result['party_list'];
                    $(".party").empty();
                    $(".consignee_name").empty();

                    $(".party").append($('<option value="default">select consignee</option>'));
                    for (var i = 0; i < consignee.length; i++) 
                        $(".party").append($('<option value="' + consignee[i].id + '">'+ consignee[i].partyname + '</option>'));
                    $('#ajax_loader_div').css('display','none');

                }
            });
        }
        function getConsignee(id){
            $('#ajax_loader_div').css('display','block');

            $.ajax({
                url: "/details/consignee/" + id,
                type: "GET",
                success:function(result) {
                    consignee = result['consg_list'];
                    $(".consignee_name").empty();
                    $(".consignee_name").append($('<option value="default">select consignee</option>'));
                    for (var i = 0; i < consignee.length; i++) 
                        $(".consignee_name").append($('<option value="' + consignee[i].id + '">'+ consignee[i].consignee_name + '</option>'));
                    $('#ajax_loader_div').css('display','none');

                }
            });
        }
        function getuom(type='all')
        {
            $('#ajax_loader_div').css('display','block');

            $.ajax({
                url: "/uom/details",
                type: "GET",
                success: function(result) {
                    var io_list = result['uom'];
                    if(type=='all')
                    {    
                        $(".uom_input").empty();
                        $(".uom_input").append($('<option value="default">Select Internal Order</option>'));
                        for (var i = 0; i < io_list.length; i++) 
                            $(".uom_input").append($('<option value="' + io_list[i].id + '">' + io_list[i].uom_name + '</option>'));
                    }
                    else
                    {
                        $("#uom_input_"+type).empty();
                        $("#uom_input_"+type).append($('<option value="default">Select Unit of measurement</option>'));
                        for (var i = 0; i < io_list.length; i++) 
                            $("#uom_input_"+type).append($('<option value="' + io_list[i].id + '">' + io_list[i].uom_name + '</option>'));
                    }
                    $('#ajax_loader_div').css('display','none');

                }
            });
        }

        function getio(id,type='all')
        {
         
            $('#ajax_loader_div').css('display','block');

            $.ajax({
                url: "/details/internalOrder/" + id,
                type: "GET",
                success: function(result) {
                    console.log(result);
                    var io_list = result['io_list'];
                    if(type=='all')
                    {    
                        $(".io_id").empty();
                        $(".io_id").append($('<option value="default">Select Internal Order</option>'));
                        for (var i = 0; i < io_list.length; i++) 
                            $(".io_id").append($('<option value="' + io_list[i].id + '">'  + io_list[i].io_number + '</option>'));
                    }
                    else
                    {
                        $("#io_id_"+type).empty();
                        $("#io_id_"+type).append($('<option value="default">Select Internal Order</option>'));
                        for (var i = 0; i < io_list.length; i++) 
                            $("#io_id_"+type).append($('<option value="' + io_list[i].id + '">'  + io_list[i].io_number + '</option>'));
                    }
                    $('#ajax_loader_div').css('display','none');
                }
            });
        }

    $('#addDelivery').click(function(){
        $('select').select2('destroy');
        $('.datepicker1').datepicker("destroy");
        var num = parseInt($('#no_of_io').val());
        num++;
        $('#no_of_io').val(num);
        $('.newbox').append(
        '<div class="box box-default">'+
            '<div class="box-header with-border">'+
                '<div class="box-body"> '+
                    '<div class="col-md-2" style="float:right;">'+
                        '<button type="button" class="close" onclick="$(this).parent().parent().parent().parent().remove();" id="removeconsignee" >X</button>'+
                    '</div>'+
                    '<div class="container-fluid">'+
                        '<div class="row">                     '+
                            '<div class="col-md-4">'+
                                '<label>{{__('delivery_challan.internal_order_nu')}}<sup>*</sup></label>'+
                                '<select   onchange="updateiodata(this)"  class="form-control select2 input-css io_id" id="io_id_'+num+'"  data-placeholder="" style="width: 100%;" name="io_id[]">'+
                                    '<option value="default">Select Client first</option>'+
                               '</select>'+
                              
                           '</div><!--end of col-md-4-->'+
                            '<div class="col-md-4">'+
                                '<label>{{__('delivery_challan.date')}}<sup>*</sup></label>'+
                                '<input   type="text" class="form-control datepicker1 input-css del_date_input" id="delivery_date_'+num+'" name="delivery_date[]">'+
                           '</div><!--end of col-md-4-->'+
                            '<div class="col-md-4">'+
                                '<label>{{__('delivery_challan.UOM')}}<sup>*</sup></label>'+
                                '<select    name="uom[]" class="form-control select2 input-css uom_input " id="uom_input_'+num+'">'+
                                        '<option value="default">Select Unit of Measurement</option>'+
                               '</select>'+
                               
                                '</div><!--end of col-md-4-->'+
                       '</div><!--end of div row-->'+
                        '<br>'+
                        '<div class="row">                     '+
                            '<div class="col-md-4"> '+
                                '<label>{{__('delivery_challan.pak_detail')}}<sup>*</sup></label>'+
                                '<input   type="text" class="form-control input-css pak_input" id="pak_input_'+num+'" name="pak_details[]"> '+
                           '</div><!--end of div row--> '+
                            '<div class="col-md-4"> '+
                                '<label>{{__('delivery_challan.goods_des')}}<sup>*</sup></label>'+
                                '<input   type="text" class="form-control input-css goods_desc_input" id="goods_desc_'+num+'" name="goods_des[]">'+
                           '</div><!--end of col-md-4-->'+
                            '<div class="col-md-4"> '+
                                '<label>{{__('delivery_challan.goods_qty')}}<sup>*</sup></label>'+
                                '<input   type="number" class="form-control input-css goods_qty_input" id="goods_qty_'+num+'" name="goods_qty[]">'+
                                '<input   type="hidden" class="form-control input-css gst_rate_input" value="" id="gst_rate_'+num+'" name="gst_rate[]">'+
                                '</div><!--end of col-md-4-->'+
                       '</div><!--end of div row-->  '+
                       '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label>{{__('delivery_challan.rate')}}<sup>*</sup></label>'+
                                    '<input type="number" disabled class="form-control input-css rate_per_qtys_input" id="rate_per_qty_'+num+'"  value="">'+
                                    '<input type="hidden" class="form-control input-css rate_per_qty_input" id="rate_per_qty_'+num+'" name="rate_per_qty[]" value="">'+
                                   ' {!! $errors->first('rate_per_qty.{{$i}}', '<p class="help-block">:message</p>') !!}'+
                           '</div>'+
                       '</div>'+

                   '</div><!--end of container-fluid-->'+
               '</div><!--end of box box-body-->'+
           '</div><!--end of box-header with-border-->'+
       '</div>');
       $('.datepicker1').datepicker({
      autoclose: true,
      format: 'd-m-yyyy'
  });
       getio(parseInt($('#reference').val()),num);

       getuom(num);
        $('select').select2();
     });
    var consignee;
    var consignee1;
    $('.reference').change(function(e) {
        var partyid = $(e.target).val();
        getio(partyid,'all');
        getClient(partyid);
    });
    $('.party').change(function(e) {
        var partyid = $(e.target).val();
       
        getConsignee(partyid);
    });
</script>
<script>
// var ref={{$ref_name}};
// getio(ref,'all');
</script>
<script>
var message="{{Session::get('message')}}";
if(message=="delivery"){
    document.getElementById("popup_message3").click();
}
</script>
@endsection
@section('main_section')
<section class="content">
    <!-- Main content -->
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    <section class="content">
    <form method="post" action="/deliverychallan/updatedb/{{$Delivery_challan->id}}" id="form">
            @csrf
            <div id="cont-form">
                @csrf
                <input value="0" type="hidden" id="no_of_io">

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{__('delivery_challan.mytitle')}}</h3>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8 {{ $errors->has('update_reason') ? 'has-error' : ''}}">
                                    <label>Update Reason<sup>*</sup></label>
                                    <input   type="text" autocomplete="off" value="{{$errors->any() ? old('update_reason') : ''}}" class="form-control  input-css update_reason" id="update"  name="update_reason">
                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-3-->
                            </div>
                
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('delivery_challan.ref_name')}} <span
                                                class="span">*</span></label>
                                        <select class="form-control select2 reference input-css reference" id="reference"
                                            data-placeholder="" style="width: 100%;" name="reference">
                                            <option value="default">Select Reference Name</option>
                                            @foreach($reference_name as $key)
                                                <option value="{{$key->id}}" {{$ref_name==$key->id?'selected="selected"':''}}>{{$key->referencename}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('reference', '<p class="help-block">:message</p>') !!}

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('delivery_challan.client_name')}} <span
                                                class="span">*</span></label>
                                        <select     class="form-control select2 party input-css party" id="party"
                                            data-placeholder="" style="width: 100%;" name="party">
                                            <option value="default">Select Client</option>
                                            @foreach($party as $key)
                                                <option value="{{$key->id}}" {{$Delivery_challan->party_id==$key->id?'selected="selected"':''}}>{{$key->partyname}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('party', '<p class="help-block">:message</p>') !!}

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('delivery_challan.consignee_name')}}<sup>*</sup></label>
                                        <select     class="form-control input-css select2 consignee_name"
                                            style="width: 100%;" name="consignee_name">
                                            <option value="default">Select Consignee</option>
                                            @foreach($consignee as $key)
                                            <option value="{{$key->id}}" {{$Delivery_challan->consignee_id==$key->id?'selected="selected"':''}}>{{$key->consignee_name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('consignee_name', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                                <!--end of col-md-4-->
                            </div>
                            <div class="row">
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('delivery_challan.date')}}<sup>*</sup></label>
                                        <input autocomplete="off" class="form-control input-css  del_date datepickers"
                                            style="width: 100%;" required name="del_date" value="{{date('d-m-Y',strtotime($Delivery_challan->delivery_date))}}">
                        
                                        {!! $errors->first('del_date', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $j=0;
                @endphp
                @php
                  
                @endphp
                @foreach($challan_per_io as $cha)
                @php
                    $i=0;
                    $io_id=$cha->io;
                    $max=$max_qty[$io_id];
                @endphp
                    <div class="box box-default">
                            <div class="box-header with-border">
                                <div class="box-body">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>{{__('delivery_challan.internal_order_nu')}}<sup>*</sup></label>
                                                <select  disabled   onchange="updateiodata(this)"
                                            class="form-control select2 input-css io_id" id="io_id_{{$i}}"
                                                    style="width: 100%;" name="not_required">
                                                   
                                                    @foreach($io as $key)
                                                    <option value="{{$key->id}}" {{$cha->io==$key->id?'selected="selected"':''}}>{{$key->io_number}}</option>
                                                    @endforeach
                                                </select>
                                            <input type="hidden" name="io_id[]" value="{{$cha->io}}">
                                                {{-- <label id="io_id_{{$i}}" class="error" for="io_id_1"></label> --}}
                                            <input   name="old_io[]" type="hidden" value="{{$cha->id}}">
                                                {!! $errors->first('io_id_.{{$i}}', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <!--end of col-md-4-->
                                            <div class="col-md-4">
                                                <label>{{__('delivery_challan.date')}}<sup>*</sup></label>
                                                <input   type="text" class="form-control datepicker1 input-css del_date_input" id="delivery_date_{{$i}}"
                                            value="{{date('d-m-Y',strtotime($cha->delivery_challan_date))}}" name="delivery_date[]">
                                            {!! $errors->first('delivery_date.{{$i}}', '<p class="help-block">:message</p>') !!}
                                        </div>
                                            <!--end of col-md-4-->
                                            <div class="col-md-4">
                                                <label>{{__('delivery_challan.UOM')}}<sup>*</sup></label>
                                                <select     name="uom[]" class="form-control select2 input-css uom_input" id="uom_input_{{$i}}  uom{{$j}}">
                                                    <option value="default">Select Unit of Measurement</option>
                                                    @foreach($uom as $key)
                                                        <option value="{{$key->id}}" {{$cha->uom_id==$key->id?'selected="selected"':''}}>{{$key->uom_name}}</option>
                                                    @endforeach
                                                </select>
                                                {{-- <label id="uom_input_{{$i}}" class="error" for="io_id_1"></label> --}}
                                                {!! $errors->first('uom.{{$i}}', '<p class="help-block">:message</p>') !!}
    
                                            </div>
                                            <!--end of col-md-4-->
                                        </div>
                                        <!--end of div row-->
                                        <br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>{{__('delivery_challan.pak_detail')}}<sup>*</sup></label>
                                                <input   type="text" class="form-control input-css pak_input" id="pak_input_{{$i}}"
                                                    name="pak_details[]" value="{{$cha->packing_details}}">
                                                {!! $errors->first('pak_details.{{$i}}', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <!--end of div row-->
                                            <div class="col-md-4">
                                                <label>{{__('delivery_challan.goods_des')}}<sup>*</sup></label>
                                                <input   type="text" class="form-control input-css goods_desc_input"
                                                    id="goods_desc_{{$i}}" value="{{$cha->good_desc}}" name="goods_des[]">
                                                {!! $errors->first('goods_des.{{$i}}', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <!--end of col-md-4-->
                                            <div class="col-md-4">
                                                <label>{{__('delivery_challan.goods_qty')}}<sup>*</sup></label>
                                                <input   type="number" class="form-control input-css goods_qty_input"
                                                id="goods_qty_{{$i}}  good{{$j}}" value="{{$cha->good_qty}}" name="goods_qty[]" placeholder="Max:{{$cha->good_qty+$cha->left_qty}}"  min="0" max="{{$cha->good_qty + $cha->left_qty}}">
                                                <input type="hidden" class="form-control input-css gst_rate_input" value="{{$cha->gst_rate}}" id="gst_rate_{{$i}}" name="gst_rate[]">
                                                <label id="goods_qty_{{$i}}-error" class="error" ></label>
                                                {!! $errors->first('goods_qty.{{$i}}', '<p class="help-block">:message</p>') !!}
                                            </div>
                                                <!--end of col-md-4-->
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>{{__('delivery_challan.rate')}}<sup>*</sup></label>
                                                <input   type="text" disabled class="form-control input-css rate_per_qtys_input" id="rate_per_qty_{{$i}}"
                                                  value="{{$cha->rate}}">
                                                 <input   type="hidden" class="form-control input-css rate_per_qty_input" id="rate_per_qty_{{$i}}"
                                                 name="rate_per_qty[]" value="{{$cha->rate}}">
                                                {!! $errors->first('rate_per_qty.{{$i}}', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        
                                        <!--end of div row-->
                                    </div>
                                    <!--end of container-fluid-->
                                </div>
                            <!--end of box box-body-->
                        </div>
                        <!--end of box-header with-border-->
                    </div>
                
                
                <!--end of box box-default-->
                @php
                    $i=$i+1;
                    $j=$j+1;
                @endphp
                @endforeach
 
                <div class="newbox">
                </div>
                <div class="row">
                    <div class="col-md-12" style="float:right">
                        <button type="button" class="btn btn-success" id="addDelivery">Add New</button><br>
                    </div>
                </div>
                <br>
                <br>
                <div class="box box-default">
                    <div class="box-header with-border">
                        <div class="box-body">
                            <label>
                                <h3>{{__('delivery_challan.goods_dispatch_mode')}}</h3>
                                <div class="goods_dispatch_label_er"></div>
                            </label>
                        </div>
                        <!--end of row-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" class="" value="2"
                                            {{$Delivery_challan->dispatch==2?'checked="checked"':''}}   name="goods_dispatch">Self</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input   autocomplete="off" type="radio" class="" value="3"
                                            {{$Delivery_challan->dispatch==3?'checked="checked"':''}}  name="goods_dispatch">Courier</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input   autocomplete="off" type="radio" class="" value="1"
                                            {{$Delivery_challan->dispatch==1?'checked="checked"':''}}    name="goods_dispatch">Transporter</label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            {!! $errors->first('goods_dispatch', '<p class="help-block">:message</p>') !!}                              
                        </div>
                        <br>
                        <br>
                        <div class="form1-a" style="{{$Delivery_challan->dispatch==2?'':'display:none'}}">
                            <div class="row">
                                <!--end of col-md-6-->
                                <div class="col-md-6">
                                    <label>{{__('delivery_challan.carrier')}}<sup>*</sup></label>
                                    <select     class="form-control self-name input-css select2 " multiple="multiple" data-placeholder=""
                                        style="width: 100%;"  name="self_id[]">
                                        @php
                                            $i=0;
                                            $dispatch_id = explode(',',$Delivery_challan->dispatch_id);                                        
                                        @endphp
                                    @foreach($self as $key)
                                        @if($i<count($dispatch_id) && $dispatch_id[$i]==$key->id)
                                        <option value="{{$key->id}}" selected="selected">{{$key->courier_name}}</option>
                                        @php $i++;
                                         @endphp
                                        @else
                                        <option value="{{$key->id}}" >{{$key->courier_name}}</option>
                                        @endif
                                    @endforeach
                      
                                    </select>
                                    {!! $errors->first('self_id', '<p class="help-block">:message</p>') !!}

                                </div>
                                <!--end of col-md-6-->
                                <div class="col-md-6">
                                    <label>{{__('delivery_challan.vechicle')}}<sup>*</sup></label>
                                    <select class="form-control vehicle-name input-css select2" data-placeholder=""
                                        style="width: 100%;" name="vehicle_no_self">
                                        <option value="default">Select vehicle</option>
                                        @foreach ($vehicle as $key)
                                            <option value="{{$key->id}}" {{$key->id==$Delivery_challan->vehicle_id?'selected="Selected"':''}}>{{$key->vehicle_number}}</option>                                            
                                        @endforeach
                                    </select>
                                    {!! $errors->first('self_id', '<p class="help-block">:message</p>') !!}
                              
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="form1-b" style="{{$Delivery_challan->dispatch==3?'':'display:none'}}">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{__('delivery_challan.courier_name')}}<sup>*</sup></label>
                                    <select     class="form-control company input-css select2" data-placeholder=""
                                        style="width: 100%;" name="goods_dispatch_id">
                                    @foreach($courier as $key)
                                        <option value="{{$key->id}}" {{$Delivery_challan->dispatch_id==$key->id?'selected="selected"':''}}>{{$key->courier_name}}</option>
                                    @endforeach

                                    </select>
                                    {!! $errors->first('goods_dispatch_id', '<p class="help-block">:message</p>') !!}

                                </div>
                                <div class="col-md-4">
                                    <label>{{__('delivery_challan.bilty_docket')}}</label>
                                <input   type="text" value="{{$Delivery_challan->dispatch==3?$Delivery_challan->bilty_docket:''}}" class="form-control input-css"
                                 name="bilty_docket">
                                {!! $errors->first('bilty_docket', '<p class="help-block">:message</p>') !!}
                            </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{__('delivery_challan.bilty_date')}}</label>
                                    <input   type="text" class="form-control datepicker1 input-css" id="datepicker"
                                        name="bilty_date" value="{{$Delivery_challan->dispatch==3?date('d-m-Y',strtotime($Delivery_challan->docket_date)):''}}">
                                        {!! $errors->first('bilty_date', '<p class="help-block">:message</p>') !!}
                    
                                </div>
                                <!--end of col-md-6-->
                            </div>
                        </div>
                        <br>
                        <div class="form1-c" style="{{$Delivery_challan->dispatch==1?'':'display:none'}}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.courier_name')}}<sup>*</sup></label>
                                        <select     class="form-control company input-css select2" data-placeholder=""
                                            style="width: 100%;" name="goods_dispatch_id1">
                                            {!! $errors->first('goods_dispatch_id1', '<p class="help-block">:message</p>') !!}
                                        @foreach($trans as $key)
                                            <option value="{{$key->id}}" {{$Delivery_challan->dispatch_id==$key->id?'selected="selected"':''}}>{{$key->courier_name}}</option>
                                        @endforeach
    
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.bilty_docket')}}</label>
                                        <input   type="text" value="{{$Delivery_challan->dispatch==1?$Delivery_challan->bilty_docket:''}}" class="form-control input-css"
                                         name="bilty_docket1">
                                         {!! $errors->first('bilty_docket1', '<p class="help-block">:message</p>') !!}
                        
                                    </div>
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.bilty_date')}}</label>
                                        <input   type="text" class="form-control datepicker1 input-css" id="datepicker"
                                            name="bilty_date1" value="{{$Delivery_challan->dispatch==1?date('d-m-Y',strtotime($Delivery_challan->docket_date)):''}}">
                                            {!! $errors->first('bilty_date1', '<p class="help-block">:message</p>') !!}
                    
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </section>
    @endsection
    
    {{-- {{__('delivery_challan.enrty_for')}} --}}