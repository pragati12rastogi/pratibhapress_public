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
<script src="js/views/delivery_challan.js"></script>
<script>
    var last7Days = new Date(); 
    var currentDate = new Date();
currentDate.setDate(currentDate.getDate() + 1);
$('.datepickers').datepicker({
    format: 'dd-mm-yyyy',
      autoclose: true,
    //   startDate: last7Days,
      endDate:currentDate,
});
$('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
      autoclose: true,
    //   startDate: last7Days,
    //   endDate:currentDate,
});
$(".old_dc").keydown(function(e) {
    var oldvalue=$(this).val();
    var field=this;
    var set="{{$settings.'/'}}";
    setTimeout(function () {
        if(field.value.indexOf(set) !== 0) {
            $(field).val(oldvalue);
        } 
    }, 1);
});
</script>
<script>
    var selected_io = {};
    $(document).ready(function() {
            
            $('.io_id').trigger('change');

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
                required: true,
                notValidIfSelectFirst: "default",
            });
        } else{
            $(ielm).rules("add", {
                required: true,
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
                        // console.log(qty_tot+","+qty_done);
                        // console.log(qty_left);
                        
                        onIoChangeDoStuff(id, '.uom_input', value['unit'], 1);
                        onIoChangeDoStuff(id, '.del_date_input', value['delivery_date'], 0);
                        onIoChangeDoStuff(id, '.pak_input', value['pak_input'], 0);
                        onIoChangeDoStuff(id, '.goods_desc_input', value['goods_desc_input'], 0);
                        $("#"+id).parent().parent().parent().find('.goods_qty_input').attr('placeholder',("Max:" + qty_left));
                        $("#"+id).parent().parent().parent().find('.goods_qty_input').attr('data-qty', qty_left);
                        $("#"+id).parent().parent().parent().find('.rate_per_qty_input').val(value['rate_per_qty']);
                        $("#"+id).parent().parent().parent().find('.rate_per_qtys_input').val(value['rate_per_qty']);
                        $("#"+id).parent().parent().parent().find('.gst_rate_input').val(value['gst_rate']);
                    
                        var idp = $("#"+id).parent().parent().parent().find('.goods_qty_input');
                       
                        idp.val(value['goods_qty_input']);
                        var x = parseInt($(idp).attr('data-qty'));
                        //alert(x);
                        $(idp).rules("remove");
                        $(idp).rules("add", {
                            required: true,
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
           
            // console.log(col_name);
            
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
            console.log(id);
            
            $('#ajax_loader_div').css('display','block');
            $.ajax({
                url: "/details/internalOrder/" + id,
                type: "GET",
                success: function(result) {
                    var io_list = result['io_list'];
                    if(type=='all')
                    {    
                        $(".io_id").empty();
                        $(".io_id").append($('<option value="default">Select Internal Order</option>'));
                        for (var i = 0; i < io_list.length; i++) 
                            $(".io_id").append($('<option value="' + io_list[i].id + '">' + io_list[i].io_number + '</option>'));
                    }
                    else
                    {
                        $("#io_id_"+type).empty();
                        $("#io_id_"+type).append($('<option value="default">Select Internal Order</option>'));
                        for (var i = 0; i < io_list.length; i++) 
                            $("#io_id_"+type).append($('<option value="' + io_list[i].id + '">'  + io_list[i].io_number+ '</option>'));
                    }
                    $('#ajax_loader_div').css('display','none');
                }
            });
        }
    $('#addDelivery').click(function(){
        $('select').select2('destroy');
        $('.datepickers').datepicker("destroy");
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
                                '<select onchange="updateiodata(this)" class="form-control select2 input-css io_id" id="io_id_'+num+'"  data-placeholder="" style="width: 100%;" name="io_id[]">'+
                                    '<option value="default">Select Client first</option>'+
                               '</select>'+
                           '</div><!--end of col-md-4-->'+
                            '<div class="col-md-4">'+
                                '<label>Delivery Date<sup>*</sup></label>'+
                                '<input type="text" class="form-control datepickers input-css del_date_input" data-date-format="dd-mm-yyyy" id="delivery_date_'+num+'" name="delivery_date[]">'+
                           '</div><!--end of col-md-4-->'+
                            '<div class="col-md-4">'+
                                '<label>{{__('delivery_challan.UOM')}}<sup>*</sup></label>'+
                                '<select name="uom[]" class="form-control select2 input-css uom_input " id="uom_input_'+num+'"></select>'+
                           '</div><!--end of col-md-4-->'+
                       '</div><!--end of div row-->'+
                        '<br>'+
                        '<div class="row">                     '+
                            '<div class="col-md-4"> '+
                                '<label>{{__('delivery_challan.pak_detail')}}<sup>*</sup></label>'+
                                '<input type="text" class="form-control input-css pak_input" id="pak_input_'+num+'" name="pak_details[]"> '+
                           '</div><!--end of div row--> '+
                            '<div class="col-md-4"> '+
                                '<label>{{__('delivery_challan.goods_des')}}<sup>*</sup></label>'+
                                '<input type="text" class="form-control input-css goods_desc_input" id="goods_desc_'+num+'" name="goods_des[]">'+
                           '</div><!--end of col-md-4-->'+
                            '<div class="col-md-4"> '+
                                '<label>{{__('delivery_challan.goods_qty')}}<sup>*</sup></label>'+
                                '<input type="number" class="form-control input-css goods_qty_input" id="goods_qty_'+num+'" name="goods_qty[]">'+
                                '<input type="hidden" class="form-control input-css gst_rate_input" value="" id="gst_rate_'+num+'" name="gst_rate[]">'+
                                
                           '</div><!--end of col-md-4-->'+
                       '</div><!--end of div row-->  '+
                       '<div class="row">'+
                            '<div class="col-md-4">'+
                                '<label>{{__('delivery_challan.rate')}}<sup>*</sup></label>'+
                                    '<input type="text" class="form-control input-css rate_per_qtys_input" id="rate_per_qty_'+num+'"  value="" disabled>'+
                                    '<input type="hidden" class="form-control input-css rate_per_qty_input" id="rate_per_qty_'+num+'" name="rate_per_qty[]" value="">'+
                                   ' {!! $errors->first('rate_per_qty.{{$i}}', '<p class="help-block">:message</p>') !!}'+
                           '</div>'+
                       '</div>'+

                   '</div><!--end of container-fluid-->'+
               '</div><!--end of box box-body-->'+
           '</div><!--end of box-header with-border-->'+
       '</div>');
       $('.datepickers').datepicker({
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
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <section class="content">
        <form method="post" action="/deliveryinsert" id="form">
            @csrf
            <div id="cont-form">
                <input value="0" type="hidden" id="no_of_io">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{__('delivery_challan.mytitle')}}</h3>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('delivery_challan.ref_name')}} <span
                                                class="span">*</span></label>
                                        <select class="form-control select2 reference input-css" id="reference"
                                            data-placeholder="" style="width: 100%;" name="reference">
                                            <option value="default">Select Reference Name</option>
                                            @foreach($reference as $key)
                                            <option value="{{$key->id}}">{{$key->referencename}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('reference', '<p class="help-block">:message</p>') !!}
                                
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{__('delivery_challan.client_name')}} <span
                                                class="span">*</span></label>
                                        <select class="form-control select2 party input-css" id="party"
                                            data-placeholder="" style="width: 100%;" name="party">
                                            <option value="default">Select Client</option>
                                        </select>
                                        {!! $errors->first('party', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('delivery_challan.consignee_name')}}<sup>*</sup></label>
                                        <select class="form-control input-css select2 consignee_name"
                                            style="width: 100%;" name="consignee_name">
                                            <option value="default">Select Consignee</option>
                                        </select>
                                        {!! $errors->first('consignee_name', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                </div>
                                <!--end of col-md-4-->
                            </div>
                            <div class="row">
                               
                                <div class="col-md-8">
                            <label>Is Delivery Challan New Or Old ?<sup>*</sup></label>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input autocomplete="off" type="radio" class="challan_number_status" value="New" name="challan_number_status">New</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input     autocomplete="off" type="radio" class="challan_number_status" value="Old" name="challan_number_status">Old</label>
                                </div>
                            </div>
                            <div class="col-md-6 old_delivery" style="display:none">
                            <input type="text" name="old_dc" id="" class="input-css old_dc" value="{{$settings.'/'}}" required placeholder="Enter Delivery Challan Number">
                            </div>

                            </div>
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('delivery_challan.date')}}<sup>*</sup></label>
                                        <input autocomplete="off"  class="input-css  del_date datepicker"
                                            style="width: 100%;" required name="del_date_new">
                                        {!! $errors->first('del_date', '<p class="help-block">:message</p>') !!}
                                        <input autocomplete="off" class="input-css  del_date1 datepickers"
                                            style="width: 100%; display:none" required name="del_date_old">
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div class="box box-default">
                    <div class="box-header with-border">
                        <div class="box-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-4">
                                        @php
                                            $i=0;
                                        @endphp
                                        <label>{{__('delivery_challan.internal_order_nu')}}<sup>*</sup></label>
                                        <select onchange="updateiodata(this)"
                                            class="form-control select2 input-css io_id" id="io_id_0"
                                            style="width: 100%;" name="io_id[]">
                                            <option value="default">Select party first</option>
                                        </select>
                                        {!! $errors->first('io_id.{{$i}}', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                    <!--end of col-md-4-->
                                    <div class="col-md-4">
                                        <label>Delivery Date<sup>*</sup></label>
                                        <input type="text" autocomplete="off" data-date-format="dd-mm-yyyy" class="form-control datepickers input-css del_date_input"
                                            id="delivery_date_0" name="delivery_date[]">
                                            {!! $errors->first('delivery_date.{{$i}}', '<p class="help-block">:message</p>') !!}
                              
                                        </div>
                                    <!--end of col-md-4-->
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.UOM')}}<sup>*</sup></label>
                                        <select name="uom[]" class="form-control select2 input-css uom_input"
                                            id="uom_input_0">
                                            <option value="default">Select Unit of Measurement</option>
                                            @foreach($uom as $key)
                                            <option value="{{$key->id}}">{{$key->uom_name}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('uom.{{$i}}', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                    <!--end of col-md-4-->
                                </div>
                                <!--end of div row-->
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.pak_detail')}}<sup>*</sup></label>
                                        <input type="text" class="form-control input-css pak_input" id="pak_input_0"
                                            name="pak_details[]">
                                            {!! $errors->first('pak_details.{{$i}}', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                    <!--end of div row-->
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.goods_des')}}<sup>*</sup></label>
                                        <input type="text" class="form-control input-css goods_desc_input"
                                            id="goods_desc_0" name="goods_des[]">
                                            {!! $errors->first('goods_des.{{$i}}', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                    <!--end of col-md-4-->
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.goods_qty')}}<sup>*</sup></label>
                                        <input type="number" class="form-control input-css goods_qty_input"
                                            id="goods_qty_0" name="goods_qty[]">
                                            <input type="hidden" class="form-control input-css gst_rate_input" value="" id="gst_rate_0" name="gst_rate[]">
                                            {!! $errors->first('goods_qty.{{$i}}', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                    <!--end of col-md-4-->
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.rate')}}<sup>*</sup></label>
                                        <input type="text" class="form-control input-css rate_per_qtys_input" id="rate_per_qty_0" disabled value="">
                                            <input type="hidden" class="form-control input-css rate_per_qty_input" id="rate_per_qty_0" name="rate_per_qty[]" value="">
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
                                        <label><input autocomplete="off" type="radio" class="goods_dispatch" value="2"
                                                name="goods_dispatch">Self</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input autocomplete="off" type="radio" class="goods_dispatch" value="3"
                                                name="goods_dispatch">Courier</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input autocomplete="off" type="radio" class="goods_dispatch" value="1"
                                                name="goods_dispatch">Transporter</label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            {!! $errors->first('goods_dispatch', '<p class="help-block">:message</p>') !!}
                              
                        </div>
                        <br>
                        <br>
                        <div class="form1-a" style="display:none">
                            <div class="row">
                                <!--end of col-md-6-->
                                <div class="col-md-6">
                                    <label>{{__('delivery_challan.carrier')}}<sup>*</sup></label>
                                    <select class="form-control self-name input-css select2" data-placeholder=""
                                        style="width: 100%;" name="self_id[]"  multiple="multiple">
                                    </select>
                                    {!! $errors->first('self_id', '<p class="help-block">:message</p>') !!}
                              
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('delivery_challan.vechicle')}}<sup>*</sup></label>
                                    <select class="form-control vehicle-name input-css select2" data-placeholder=""
                                        style="width: 100%;" name="vehicle_no_self">
                                        <option value="default">Select vehicle</option>
                                        @foreach ($vehicle as $key)
                                        <option value="{{$key->id}}">{{$key->vehicle_number}}</option>
                                            
                                            
                                        @endforeach
                                    </select>
                                    {!! $errors->first('vehicle_no_self', '<p class="help-block">:message</p>') !!}
                              
                                </div>
                                <!--end of col-md-6-->
                            </div>
                        </div>
                        <br>
                        <div class="form1-b" style="display:none">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{__('delivery_challan.courier_name')}}<sup>*</sup></label>
                                    <select class="form-control company input-css select2" data-placeholder=""
                                        style="width: 100%;" name="goods_dispatch_id">

                                    </select>
                                    {!! $errors->first('goods_dispatch_id', '<p class="help-block">:message</p>') !!}
                              
                                </div>
                                <div class="col-md-4">
                                    <label>{{__('delivery_challan.bilty_docket')}}</label>
                                    <input type="text" class="form-control input-css bilty_docket" name="bilty_docket">
                                    {!! $errors->first('bitly_docket', '<p class="help-block">:message</p>') !!}
                              
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{__('delivery_challan.bilty_date')}}</label>
                                    <input type="text" autocomplete="off" class="form-control bilty_date datepicker1 input-css" data-date-format="dd-mm-yyyy" id="datepicker"
                                        name="bilty_date">
                                        {!! $errors->first('bitly_date', '<p class="help-block">:message</p>') !!}                              
                                </div>
                                <!--end of col-md-6-->
                            </div>
                        </div>
                        <br>
                        <div class="form1-c" style="display:none">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.courier_name')}}<sup>*</sup></label>
                                        <select class="form-control company input-css select2" data-placeholder=""
                                            style="width: 100%;" name="goods_dispatch_id1">
    
                                        </select>
                                        {!! $errors->first('goods_dispatch_id1', '<p class="help-block">:message</p>') !!}
                              
                                    </div>
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.bilty_docket')}}</label>
                                        <input type="text" class="form-control bilty_docket input-css" name="bilty_docket1">
                                        {!! $errors->first('bitly_docket1', '<p class="help-block">:message</p>') !!}

                                    </div>
                                    <div class="col-md-4">
                                        <label>{{__('delivery_challan.bilty_date')}}</label>
                                        <input type="text" autocomplete="off" class="form-control bilty_date datepicker1 input-css" data-date-format="dd-mm-yyyy" id="datepicker1"
                                            name="bilty_date1">
                                            {!! $errors->first('bitly_date1', '<p class="help-block">:message</p>') !!}
                              
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
    
 