
@extends($layout)

@section('title', __('purchase/order.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('purchase/order.title')}}</i></a></li>
 @endsection
@section('css')

@endsection

@section('js')
<script src="/js/purchase/order_update.js"></script>
<script src="/js/purchase/grn.js"></script>
<script>
        var currentDate = new Date();
    $('.datepickers').datepicker({
        format: 'dd-mm-yyyy',
          autoclose: true,
          endDate:currentDate,
    });
    </script>
<script>
    var ids=-1;
@foreach($po as $item)
ids++;
console.log(ids);

var master_cat_id="{{$item['master_cat_id']}}";
var sub_cat_id="{{$item['sub_cat_id']}}";
var item_name_id="{{$item['item_name_id']}}";
if(master_cat_id==1){
 
    $('#sub_cat_paper_'+ids).val(sub_cat_id).select2().trigger("change");
    paper(sub_cat_id,item_name_id,ids);
   
}
	

if(master_cat_id==2){
    $('#sub_cat_ink_'+ids).val(sub_cat_id).select2().trigger("change");
    ink(sub_cat_id,item_name_id,ids);
}

if(master_cat_id==3){
    $('#sub_cat_plate_'+ids).val(sub_cat_id).select2().trigger("change");
    plate(sub_cat_id,item_name_id,ids);
}

if(master_cat_id==4){
    $('#sub_cat_misc_'+ids).val(sub_cat_id).select2().trigger("change");
    misc(sub_cat_id,item_name_id,ids);
}
	

@endforeach
ids=0;

function paper(input,item_name_id,ids){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/paper/item/" + input,
        type: "GET",
        success:function(result) {
            console.log(result);
            
            $('#item_name_paper_'+ids).empty();
            for (var i = 0; i < result.length; i++) {
                $('#item_name_paper_'+ids).append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
               
               $('#item_name_paper_'+ids).val(item_name_id).select2().trigger("change");
               $('#ajax_loader_div').css('display','none');
        }
    });
}

function ink(input,item_name_id,ids){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/ink/item/" + input,
        type: "GET",
        success:function(result) {
            console.log(result);
            
            $('#item_name_ink_'+ids).empty();
            for (var i = 0; i < result.length; i++) {
                $('#item_name_ink_'+ids).append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
              
               $('#item_name_ink_'+ids).val(item_name_id).select2().trigger("change");
               $('#ajax_loader_div').css('display','none');
        }
    });
}
function plate(input,item_name_id,ids){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/plate/item/" + input,
        type: "GET",
        success:function(result) {
            console.log(result);
            
            $('#item_name_plate_'+ids).empty();
            for (var i = 0; i < result.length; i++) {
                $('#item_name_plate_'+ids).append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
              
               $('#item_name_plate_'+ids).val(item_name_id).select2().trigger("change");
               $('#ajax_loader_div').css('display','none');
        }
    });
}

function misc(input,item_name_id,ids){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/misc/item/" + input,
        type: "GET",
        success:function(result) {
            console.log(result);
            
            $('#item_name_misc_'+ids).empty();
            for (var i = 0; i < result.length; i++) {
                $('#item_name_misc_'+ids).append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
              
               $('#item_name_misc_'+ids).val(item_name_id).select2().trigger("change");
               $('#ajax_loader_div').css('display','none');
        }
    });
}

var message="{{Session::get('po')}}";
if(message=="successfull"){
    document.getElementById("po").click();
}
</script>

<script>

function add_more(master_id){
    console.log(master_id);
    var taxPercent={!! json_encode($taxPercent->toArray(), JSON_HEX_TAG) !!};
    var papers;
    var uom;
    if(master_id=="paper"){
        papers={!! json_encode($paper->toArray(), JSON_HEX_TAG) !!};
        uom={!! json_encode($unit_paper->toArray(), JSON_HEX_TAG) !!};
    }
    if(master_id=="ink"){
         papers={!! json_encode($ink->toArray(), JSON_HEX_TAG) !!};
         uom={!! json_encode($unit_ink->toArray(), JSON_HEX_TAG) !!};
    }
    if(master_id=="plate"){
        papers={!! json_encode($plate->toArray(), JSON_HEX_TAG) !!};
        uom={!! json_encode($unit_plate->toArray(), JSON_HEX_TAG) !!};
    }
    if(master_id=="misc"){
        papers={!! json_encode($misc->toArray(), JSON_HEX_TAG) !!};
        uom={!! json_encode($unit_misc->toArray(), JSON_HEX_TAG) !!};
    }
   
    console.log(papers);
    
       var ls= '<div  class="box-header with-border '+master_id+'_new" style="">'+  
                          '<div class="box box-default" >   <br>'+
                            '<div class="col-md-2" style="float:right;">'+
    '<button type="button" class="close" onclick="$(this).parent().parent().parent().remove();" id="removeconsignee">X'+
    '</button>'+
    '</div><br>'+
                              '<div class="container-fluid">'+
                                          '<div class="row">'+
                                              '<div class="col-md-6 ">'+
                                                  '<label>Sub Categories<sup>*</sup></label>'+
                                                  '<select style="width:100%" name="sub_cat_'+master_id+'[]" onchange="'+master_id+'item(this)" id="sub_cat_'+master_id+'" class="sub_cat_'+master_id+' select2 input-css">'+
      
                                                      '<option value="default">Select Sub Categories</option>';
                                                       for(var i=0;i<papers.length;i++){
                                                           ls=ls+ '<option value="'+papers[i].id+'">'+papers[i].name+'</option>';
                                                     }
                                                 ls= ls + '</select>';
                                             ls=ls+ '</div>'+
                                             ' <div class="col-md-6 ">'+
                                                  '<label>Item Name<sup>*</sup></label>'+
                                                  '<select style="width:100%" name="item_name_'+master_id+'[]" id="item_name_'+master_id+'" class="item_name_'+master_id+' select2 input-css">'+
      
                                                      '<option value="default">Select Item Name</option>'+
                                                  '</select>   '+
                                              '</div>'+
                                              
                                          '</div>'+
                                          '<br><br>'+
                                          '<div class="row">'+
                                              '<div class="col-md-6 ">'+
                                                  '<label>Item Quantity<sup>*</sup></label>'+
                                                  '<input type="number" min="0" step="none" name="item_qty_'+master_id+'[]"  id="item_qty_'+master_id+'" class="input-css item_qty">     '+ 
                                              '</div>'+
                                              '<div class="col-md-6 ">'+
                                                  '<label>Unit of Measurment<sup>*</sup></label>'+
                                                  '<select style="width:100%" name="uom_'+master_id+'[]" id="uom_'+master_id+'" class="select2 input-css">'+
      
                                                      '<option value="default">Select UOM</option>';
                                                      for(var i=0;i<uom.length;i++){
                                                           ls=ls+ '<option value="'+uom[i].id+'">'+uom[i].uom_name+'</option>';
                                                     }
                                                 ls=ls+'</select>'+
                                                  
                                              '</div>'+
                                          '</div>'+
                                          '<br><br>'+
                                          '<div class="row">'+
                                              '<div class="col-md-6 ">'+
                                                  '<label>Tax % Applicable<sup>*</sup></label>'+
                                                  '<select style="width:100%" name="tax_percent_'+master_id+'[]" id="tax_percent_'+master_id+'" class="select2 input-css">'+
      
                                                      '<option value="default">Select Tax</option>';
                                                      for(var i=0;i<taxPercent.length;i++){
                                                           ls=ls+ '<option value="'+taxPercent[i].id+'">'+taxPercent[i].value+'</option>';
                                                     }
      
                                                  ls=ls+ '</select> '+  
                                                  
                                              '</div>'+
                                              '<div class="col-md-6 ">'+
                                                  '<label>Delivery Date<sup>*</sup></label>'+
                                                  '<input type="text" autocomplete="off" name="delivery_date_'+master_id+'[]" id="delivery_date_'+master_id+'"  class="input-css datepickers delivery_date" placeholder="Select Date">'+
                                              '</div>'+
                                          '</div>'+
                                          '<br><br>'+
                                          '<div class="row">'+
                                              '<div class="col-md-6 ">'+
                                                      '<label>Item rate<sup>*</sup></label>'+
                                                      '<input type="number" min="0" step="none" name="item_rate_'+master_id+'[]" id="item_rate_'+master_id+'" class="input-css item_rate">   '+ 
                                              '</div>';
                                              if(master_id!="ink"){
                                                  ls=ls+'<div class="col-md-6">';
                                                    if(master_id=="paper"){
                                                        ls=ls+ ' <label>Job Card<sup></sup></label>';
                                                    }
                                                    else{
                                                        ls=ls+ ' <label>Job Card<sup></sup></label>';
                                                    }
                                                    
                                                    ls=ls+' <select type="text" name="paper_job[]" id="paper_job_'+master_id+'" class="input-css select2 paper_job" style="width: 100%">'+
                                                    ' <option value="">Select Job Card</option>'+
                                                        @foreach ($jobcard as $key)
                                                    ' <option value="{{$key->id}}">{{$key->job_number}}</option>'+
                                                        @endforeach
                                                        '</select>'+
                                                    ' </div>';
                                              }
                                          ls=ls+'</div>'+
                                              '<br> '+
                                      '</div>'+
                          
                          '</div>'+
         
                      '</div>';
                      $('#'+master_id+'_append').append(ls);
    $('.select2').select2();
    var currentDate = new Date();
    $('.datepickers').datepicker({
        format: 'dd-mm-yyyy',
          autoclose: true,
          endDate:currentDate,
    });
}
</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                   
            </div>
            @if($errors->any())
            <!-- <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div> -->
        @endif
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
        <form method="POST" action="/purchase/order/update/{{$id}}" id="asn_form">
            @csrf
            @foreach ($detail as $item)
               
            @endforeach

            
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/order.title')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                            <div class="row">
                                    <div class="col-md-3 ">
                                            <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                            <input type="text" name="update_reason" required="" class="input-css" id="update_reason">
                                            {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                        </div><!--col-md-4-->
                            </div>
                            <br><br>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Purchase Order Date<sup>*</sup></label>
                                <input type="text" name="purchase_ord" id="purchase_ord" value="{{CustomHelpers::showDate($item->po_date,'d-m-Y')}}" class="input-css purchase_ord datepickers" placeholder="Enter Date">  
                                    {!! $errors->first('purchase_ord', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 ">
                                    <label>P.R Number<sup>*</sup></label>
                                    <select style="width:100%" name="indent_no" id="indent_no" class="select2 indent_no input-css" >

                                        <option value="">Select P.R Number</option>
                                        @foreach($indent as $key) 
                                            <option value="{{$key['id']}}" {{$item['indent_num_id']==$key['id'] ? 'selected="selected"' : ''}}>{{$key['indent_num']}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('indent_no', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <br><br>
                            <div class="row">
                                {{-- <div class="col-md-6 ">
                                    <label>P.O. number if old P.O.<sup>*</sup></label>
                                <input type="text" name="po_num" id="po_num" value="{{$item->po_num}}" class="input-css po_num " placeholder="Enter PO Number">  
                                    {!! $errors->first('po_num', '<p class="help-block">:message</p>') !!}
                                </div> --}}
                                <div class="col-md-6 ">
                                    <label>Vendor<sup>*</sup></label>
                                    <select style="width:100%" name="vendor" id="vendor" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                        <option value="default">Select Vendor</option>
                                        @foreach($vendor as $key => $value)
                                            <option value="{{$value['id']}}" {{$item->vendor_id==$value['id'] ? 'selected="selected"' : ''}}>{{$value['name']}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('vendor', '<p class="help-block">:message</p>') !!}
                                </div>
                                <input type="hidden" name="status" value="{{$item->status}}">
                                <input type="hidden" name="status_date" value="{{$item->status_date}}">
                                <input type="hidden" name="status_by" value="{{$item->status_by}}">
                                <div class="col-md-6 ">
                                    <label>Payment terms<sup>*</sup></label>
                                    <select style="width:100%" name="payment_term" id="payment_term" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                        <option value="default">Select Payment Terms</option>
                                        @foreach($payment as $key => $value)
                                            <option value="{{$value['id']}}" {{$item->payment_term_id==$value['id'] ? 'selected="selected"' : ''}}>{{$value['value']}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('payment_term', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <br><br>
                            <div class="row">   
                                <div class="col-md-6">
                                    <label>Remark<sup>*</sup></label>
                                <input type="text" name="remark" id="remark" value="{{$item->remark}}" class="input-css remark " placeholder="Enter remark">  
                                    {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
                                </div>
                                    <div class="col-md-6 ">
                                        <label>Master Categories<sup>*</sup></label>
                                        <select style="width:100%" disabled  id="master_cat" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">

                                            <option value="default">Select Master categories</option>
                                            @foreach($master_item_cat as $key => $value)
                                                <option value="{{$value['id']}}" {{$item->master_cat_id==$value['id'] ? 'selected="selected"' : ''}}>{{$value['name']}}</option>
                                            @endforeach
                                        </select>  
                                    <input type="hidden" name="master_cat" value="{{$item->master_cat_id}}">
                                        {!! $errors->first('master_cat', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            <br><br> 
                               
                            </div>
                </div>
            </div>        
                <div id="paper" {{$item->master_cat_id==1 ? 'style=display:block' : 'style=display:none'}}>
                        @php
                        $index=0;
                        @endphp 
                    @if ($item->master_cat_id==1)
                    @foreach ($po as $item)  
                    <input   name="old_paperid[]" type="hidden" value="{{$item->id}}">
                    <div class="box-header with-border " id="div_paper" >
                       <div class='box box-default paper-form-div' >   <br>
                           <div class="container-fluid" id="div_paper_{{$index}}">
                                   
                                       <div class="row">
                                           <div class="col-md-6 ">
                                               <label>Sub Categories<sup>*</sup></label>
                                               <select style="width:100%" onchange="paperitem(this)" name="sub_cat_paper[]" id="sub_cat_paper_{{$index}}" class="sub_cat_paper select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
   
                                                   <option value="default">Select Sub Categories</option>
                                                   @foreach ($paper as $key)
                                                   <option value="{{$key->id}}" {{$item->name==$key['id'] ? 'selected="selected"' : ''}}>{{$key['name']}}</option>
                                                   @endforeach
                                               </select>
                                           </div>
                                           <div class="col-md-6 ">
                                               <label>Item Name<sup>*</sup></label>
                                               <select style="width:100%" name="item_name_paper[]" id="item_name_paper_{{$index}}" class="item_name_paper select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
   
                                                   <option value="default">Select Item Name</option>
                                               </select>   
                                           </div>
                                           
                                       </div>
                                       <br><br>
                                       <div class="row">
                                           <div class="col-md-6 ">
                                               <label>Item Quantity<sup>*</sup></label>
                                           <input type="number" min="0" step="none" name="item_qty_paper[]" value="{{$item->item_qty}}" id="item_qty_paper_{{$index}}" class="input-css item_qty">      
                                           </div>
                                           <div class="col-md-6 ">
                                               <label>Unit of Measurment<sup>*</sup></label>
                                               <select style="width:100%" name="uom_paper[]" id="uom_paper_{{$index}}" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
   
                                                   <option value="default">Select UOM</option>
   
                                                   @foreach($unit_paper as $key)
                                                       <option value="{{$key['id']}}" {{$item->uom_id==$key->id ? 'selected="selected"' : ''}}>{{$key['uom_name']}}</option>
                                                   @endforeach
                                               </select>  
                                               
                                           </div>
                                       </div>
                                       <br><br>
                                       <div class="row">
                                           <div class="col-md-6 ">
                                               <label>Tax % Applicable<sup>*</sup></label>
                                               <select style="width:100%" name="tax_percent_paper[]" id="tax_percent_paper_{{$index}}" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
   
                                                   <option value="default">Select Tax</option>
   
                                                   @foreach($taxPercent as $key => $value)
                                                       <option value="{{$value['id']}}" {{$item->tax_percent_id==$value['id'] ? 'selected="selected"' : ''}}>{{$value['value']}}</option>
                                                   @endforeach
                                               </select>   
                                               
                                           </div>
                                           <div class="col-md-6 ">
                                               <label>Delivery Date<sup>*</sup></label>
                                           <input type="text" name="delivery_date_paper[]" id="delivery_date_paper_{{$index}}" value="{{CustomHelpers::showDate($item->delivery_date,'d-m-Y')}}" class="input-css datepicker delivery_date" placeholder="Select Date">
                                           </div>
                                       </div>
                                       <br><br>
                                       <div class="row">
                                           <div class="col-md-6 ">
                                                   <label>Item rate<sup>*</sup></label>
                                                   <input type="number" min="0" step="none" name="item_rate_paper[]" value="{{$item->item_rate}}" id="item_rate_misc_{{$index}}" class="input-css item_rate">    
                                           </div>
                                           <div class="col-md-6">
                                               {{-- {{$item->job_card_id}} --}}
                                            <label>{{__('purchase/grn.job')}}<sup></sup></label>
                                            <select type="text" name="paper_job[]" id="paper_job_0" class="input-css select2 paper_job" style="width: 100%">
                                            <option value="">Select Job Card</option>
                                            @foreach ($jobcard as $key)
                                            <option value="{{$key->id}}" {{$item->job_card_id==$key->id ? 'selected=selected' : ''}}>{{$key->job_number}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                       </div>
                                           <br> 
                           </div>
                       </div>
                       
                   </div>
                    @php
                        $index++;
                    @endphp
                     @endforeach   
                    @endif
                   
               
                    <div id="paper_append">
                            
                    </div>
                    <div class="form-group mt-3" style="float:left";>    
                            <input type="button" class=" btn btn-success" value="Add More" onclick="add_more('paper')">
                    </div>
                </div>
               
          
            <div id="ink" {{$item->master_cat_id==2 ? 'style=display:block' : 'style=display:none'}}>
                    @php
                            $index=0;
                            @endphp
                            @if($item->master_cat_id==2)
                            @foreach ($po as $item)
                           
                            <input   name="old_inkid[]" type="hidden" value="{{$item->id}}">
                   <div  class="box-header with-border " id="div_ink" >
                         
                           <div class='box box-default ink-form-div' >   <br>
                               <div class="container-fluid" id="div_ink_{{$index}}">
                                           <div class="row">
                                               <div class="col-md-6 ">
                                                   <label>Sub Categories<sup>*</sup></label>
                                                   <select style="width:100%" name="sub_cat_ink[]" onchange="inkitem(this)" id="sub_cat_ink_{{$index}}" class="sub_cat_ink select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
       
                                                       <option value="default">Select Sub Categories</option>
                                                       @foreach ($ink as $key)
                                                       <option value="{{$key->id}}" {{$item->name==$key['id'] ? 'selected="selected"' : ''}}>{{$key['name']}}</option>
                                                       @endforeach
                                                   </select>
                                               </div>
                                               <div class="col-md-6 ">
                                                   <label>Item Name<sup>*</sup></label>
                                                   <select style="width:100%" name="item_name_ink[]" id="item_name_ink_{{$index}}" class="item_name_ink select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
       
                                                       <option value="default">Select Item Name</option>
                                                   </select>   
                                               </div>
                                               
                                           </div>
                                           <br><br>
                                           <div class="row">
                                               <div class="col-md-6 ">
                                                   <label>Item Quantity<sup>*</sup></label>
                                                   <input type="number" min="0" step="none" name="item_qty_ink[]" value="{{$item->item_qty}}" id="item_qty_ink_{{$index}}" class="input-css item_qty">      
                                               </div>
                                               <div class="col-md-6 ">
                                                   <label>Unit of Measurment<sup>*</sup></label>
                                                   <select style="width:100%" name="uom_ink[]" id="uom_ink_{{$index}}" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
       
                                                       <option value="default">Select UOM</option>
       
                                                       @foreach($unit_ink as $key)
                                                           <option value="{{$key->id}}" {{$item->uom_id==$key['id'] ? 'selected="selected"' : ''}}>{{$key['uom_name']}}</option>
                                                       @endforeach
                                                   </select>  
                                                   
                                               </div>
                                               <!-- <div class="col-md-6 ">
                                                   <label>Item rate<sup>*</sup></label>
                                                   <input type="number" min="0" step="none" name="item_rate" value="" id="item_rate" class="input-css item_rate">    
                                               </div>  -->
                                           </div>
                                           <br><br>
                                           <div class="row">
                                               <div class="col-md-6 ">
                                                   <label>Tax % Applicable<sup>*</sup></label>
                                                   <select style="width:100%" name="tax_percent_ink[]" id="tax_percent_ink_{{$index}}" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
       
                                                       <option value="default">Select Tax</option>
       
                                                       @foreach($taxPercent as $key => $value)
                                                           <option value="{{$value['id']}}" {{$item->tax_percent_id==$value['id'] ? 'selected="selected"' : ''}}>{{$value['value']}}</option>
                                                       @endforeach
                                                   </select>   
                                               </div>
                                               <div class="col-md-6 ">
                                                   <label>Delivery Date<sup>*</sup></label>
                                                   <input type="text" name="delivery_date_ink[]" id="delivery_date_ink_{{$index}}" value="{{CustomHelpers::showDate($item->delivery_date,'d-m-Y')}}" class="input-css datepicker delivery_date" placeholder="Select Date">
                                               </div>
                                           </div>
                                           <br><br>
                                           <div class="row">
                                               <div class="col-md-6 ">
                                                       <label>Item rate<sup>*</sup></label>
                                                       <input type="number" min="0" step="none" name="item_rate_ink[]" value="{{$item->item_rate}}" id="item_rate_ink_{{$index}}" class="input-css item_rate">    
                                               </div>
                                           </div>
                                           <div class="col-md-6" style="display:none">
                                            <label>{{__('purchase/grn.job')}}<sup>*</sup></label>
                                            <select type="text" name="ink_job[]" id="ink_job_0" class="input-css select2 ink_job" style="width: 100%">
                                            <option value="">Select Job Card</option>
                                            @foreach ($jobcard as $key)
                                            <option value="{{$key->id}}" {{$item->job_card_id==$key->id ? 'selected="selected"' : ''}}>{{$key->job_number}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                               <br> 
                                       </div>
                           </div>
                          
                   </div>
                   @php
                   $index++;
               @endphp
               @endforeach
                            @endif
                
                
                    <div id="ink_append" >
                           
                    </div>
               
                <div class="form-group mt-3" style="float:left";>    
                        <input type="button" class=" btn btn-success" value="Add More" onclick="add_more('ink')">
                </div>
            </div>
            <div id="plate" {{$item->master_cat_id==3 ? 'style=display:block' : 'style=display:none'}}>
                @php
                $index=0;
                @endphp 
            @if ($item->master_cat_id==3)
            @foreach ($po as $item)  
            <input   name="old_plateid[]" type="hidden" value="{{$item->id}}">
            <div class="box-header with-border " id="div_plate" >
                <div class='box box-default plate-form-div' >   <br>
                    <div class="container-fluid" id="div_plate_{{$index}}">
                            
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <label>Sub Categories<sup>*</sup></label>
                                        <select style="width:100%" onchange="plateitem(this)" name="sub_cat_plate[]" id="sub_cat_plate_{{$index}}" class="sub_cat_plate select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
        
                                            <option value="default">Select Sub Categories</option>
                                            @foreach ($plate as $key)
                                            <option value="{{$key->id}}" {{$item->name==$key['id'] ? 'selected="selected"' : ''}}>{{$key['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 ">
                                        <label>Item Name<sup>*</sup></label>
                                        <select style="width:100%" name="item_name_plate[]" id="item_name_plate_{{$index}}" class="item_name_plate select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
        
                                            <option value="default">Select Item Name</option>
                                        </select>   
                                    </div>
                                    
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <label>Item Quantity<sup>*</sup></label>
                                    <input type="number" min="0" step="none" name="item_qty_plate[]" value="{{$item->item_qty}}" id="item_qty_plate_{{$index}}" class="input-css item_qty">      
                                    </div>
                                    <div class="col-md-6 ">
                                        <label>Unit of Measurment<sup>*</sup></label>
                                        <select style="width:100%" name="uom_plate[]" id="uom_plate_{{$index}}" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
        
                                            <option value="default">Select UOM</option>
        
                                            @foreach($unit_plate as $key)
                                                <option value="{{$key['id']}}" {{$item->uom_id==$key->id ? 'selected="selected"' : ''}}>{{$key['uom_name']}}</option>
                                            @endforeach
                                        </select>  
                                        
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <label>Tax % Applicable<sup>*</sup></label>
                                        <select style="width:100%" name="tax_percent_plate[]" id="tax_percent_plate_{{$index}}" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
        
                                            <option value="default">Select Tax</option>
        
                                            @foreach($taxPercent as $key => $value)
                                                <option value="{{$value['id']}}" {{$item->tax_percent_id==$value['id'] ? 'selected="selected"' : ''}}>{{$value['value']}}</option>
                                            @endforeach
                                        </select>   
                                        
                                    </div>
                                    <div class="col-md-6 ">
                                        <label>Delivery Date<sup>*</sup></label>
                                    <input type="text" name="delivery_date_plate[]" id="delivery_date_plate_{{$index}}" value="{{CustomHelpers::showDate($item->delivery_date,'d-m-Y')}}" class="input-css datepicker delivery_date" placeholder="Select Date">
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6 ">
                                            <label>Item rate<sup>*</sup></label>
                                            <input type="number" min="0" step="none" name="item_rate_plate[]" value="{{$item->item_rate}}" id="item_rate_misc_{{$index}}" class="input-css item_rate">    
                                    </div>
                                    <div class="col-md-6" >
                                        <label>{{__('purchase/grn.job')}}<sup></sup></label>
                                        <select type="text" name="plate_job[]" id="plate_job_0" class="input-css select2 plate_job" style="width: 100%">
                                        <option value="">Select Job Card</option>
                                        @foreach ($jobcard as $key)
                                        <option value="{{$key->id}}" {{$item->job_card_id==$key->id ? 'selected="selected"' : ''}}>{{$key->job_number}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                    <br> 
                    </div>
                </div>
                
            </div>
            @php
                $index++;
            @endphp
                @endforeach   
            @endif
            
        
            <div id="plate_append">
                    
            </div>
            <div class="form-group mt-3" style="float:left";>    
                    <input type="button" class=" btn btn-success" value="Add More" onclick="add_more('plate')">
            </div>
        </div>
            <div id="misc" {{$item->master_cat_id==4 ? 'style=display:block' : 'style=display:none'}}>
                    @php
                    $index=0;
                    @endphp
                    @if ($item->master_cat_id==4)
                    @foreach ($po as $item)
                   
                    <input   name="old_miscid[]" type="hidden" value="{{$item->id}}">
                       <div  class="box-header with-border " id="div_misc">
                          
                           <div class='box box-default misc-from-div' >   <br>
                               <div class="container-fluid" id="div_misc_{{$index}}">
                                           <div class="row">
                                               <div class="col-md-6 ">
                                                   <label>Sub Categories<sup>*</sup></label>
                                                   <select style="width:100%" name="sub_cat_misc[]" onchange="miscitem(this)" id="sub_cat_misc_{{$index}}" class="sub_cat_misc select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
       
                                                       <option value="default">Select Sub Categories</option>
                                                       @foreach ($misc as $key)
                                                       <option value="{{$key->id}}" {{$item->name==$key['id'] ? 'selected="selected"' : ''}}>{{$key['name']}}</option>
                                                       @endforeach
                                                   </select>
                                               </div>
                                               <div class="col-md-6 ">
                                                   <label>Item Name<sup>*</sup></label>
                                                   <select style="width:100%" name="item_name_misc[]" id="item_name_misc_{{$index}}" class="item_name_misc select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
       
                                                       <option value="default">Select Item Name</option>
                                                   </select>   
                                               </div>
                                               
                                           </div>
                                           <br><br>
                                           <div class="row">
                                               <div class="col-md-6 ">
                                                   <label>Item Quantity<sup>*</sup></label>
                                                   <input type="number" min="0" step="none" name="item_qty_misc[]" value="{{$item->item_qty}}" id="item_qty_misc_{{$index}}" class="input-css item_qty">      
                                               </div>
                                               <div class="col-md-6 ">
                                                   <label>Unit of Measurment<sup>*</sup></label>
                                                   <select style="width:100%" name="uom_misc[]" id="uom_misc_{{$index}}" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
       
                                                       <option value="default">Select UOM</option>
       
                                                       @foreach($unit_misc as $key)
                                                           <option value="{{$key->id}}" {{$item->uom_id==$key['id'] ? '"selected=selected"' : ''}}>{{$key['uom_name']}}</option>
                                                       @endforeach
                                                   </select>  
                                                   
                                               </div>
                                           </div>
                                           <br><br>
                                           <div class="row">
                                               <div class="col-md-6 ">
                                                   <label>Tax % Applicable<sup>*</sup></label>
                                                   <select style="width:100%" name="tax_percent_misc[]" id="tax_percent_misc_{{$index}}" class="select2 input-css  select2-hidden-accessible valid" tabindex="-1" aria-hidden="true" aria-invalid="false">
       
                                                       <option value="default">Select Tax</option>
       
                                                       @foreach($taxPercent as $key => $value)
                                                           <option value="{{$value['id']}}" {{$item->tax_percent_id==$value['id'] ? 'selected="selected"' : ''}}>{{$value['value']}}</option>
                                                       @endforeach
                                                   </select>   
                                                   
                                               </div>
                                               <div class="col-md-6 ">
                                                   <label>Delivery Date<sup>*</sup></label>
                                                   <input type="text" name="delivery_date_misc[]" id="delivery_date_misc_{{$index}}" value="{{CustomHelpers::showDate($item->delivery_date,'d-m-Y')}}" class="input-css datepicker delivery_date" placeholder="Select Date">
                                               </div>
                                           </div>
                                           <br><br>
                                           <div class="row">
                                               <div class="col-md-6 ">
                                                       <label>Item rate<sup>*</sup></label>
                                                       <input type="number" min="0" step="none" name="item_rate_misc[]" value="{{$item->item_rate}}" id="item_rate_misc_{{$index}}" class="input-css item_rate">    
                                               </div>
                                               <div class="col-md-6">
                                                <label>{{__('purchase/grn.job')}}<sup>*</sup></label>
                                                <select type="text" name="misc_job[]" id="misc_job_0" class="input-css select2 misc_job" style="width: 100%">
                                                <option value="">Select Job Card</option>
                                                @foreach ($jobcard as $key)
                                                <option value="{{$key->id}}" {{$item->job_card_id==$key->id ? 'selected="selected"' : ''}}>{{$key->job_number}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                           </div>
                                               <br> 
                                       </div>
                           
                           </div>
                          
          
                       </div> 
                       @php
                       $index++;
                   @endphp
                    @endforeach   
                    @endif
                
                        <div id="misc_append">
                               
                        </div>
                     
              
                <div class="form-group mt-3" style="float:left";>    
                        <input type="button" class=" btn btn-success" value="Add More" onclick="add_more('misc')">
                </div>
            </div>
            <div class="form-group" style="float:right";>    
                <input type="submit" class=" btn btn-primary">
            </div> <br><br>    
        </form>   
    </section><!--end of section-->
@endsection


