@extends($layout)

@section('title', __('purchase/indent.title2'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Purchase Indent</a></li>
    

@endsection
@section('js')
<script src="/js/purchase/indent.js"></script>
<script>
    var currentDate = new Date();
    $('.datepickers').datepicker({
        format: 'dd-mm-yyyy',
          autoclose: true,
          endDate:currentDate,
    });
    </script>
<script>
    var id2=10;
    var selected_io = {};
    var pr = [];
    var i=0;
    @foreach($detail as $item)
    pr[i]="{{$item->pr_id}}";
    i++;
    @endforeach
    console.log(pr);
    
    function updateiodata(e)
        {
            
            var io = $(e).val();
            var id = $(e).attr('id');
            if(id="pr_number_10"){
                $('.enox' + id).remove();
                var er = '<label class="error enox' + id +'">' +
                'This P.R. Number has already been selected.</label>';
                for (var j=0;j<pr.length;j++) {

                    if (pr[j] == io) {
                        $(er).insertAfter(e);
                        return;
                    }
                }
            }
            var er = '<label class="error enox' + id +'">' +
                'This P.R. Number has already been selected.</label>';
                $('.enox' + id).remove();
            for (var k in selected_io) {

                if ($('#' + k).length > 0 && selected_io[k] == io) {
                    $(er).insertAfter(e);
                    return;
                }
            }
            selected_io[id] = io;
            $('.enox' + id).remove();
        }        
$('#add').click(function () {
    $('select').select2('destroy');
      $('.required_im').append(
                '<div class="row">'+
            '<button type="button" class="close" onclick="$(this).parent().remove();" style="float:right;margin-right:16px;line-height: 0;font-size: 28px;" id="removeconsignee" >X</button>'+
                   ' <div class="col-md-6">'+
                        '<label>P.R. Number<sup>*</sup></label>'+
                        ' <select onchange="updateiodata(this)" style="width:100%" name="pr_number[]" id="pr_number'+id2+'" class="select2 input-css pr_number">'+
                        ' <option value="">Select P.R. Number</option>'+
                        @foreach ($purchase as $key)
                        ' <option value="{{$key->id}}">{{$key->purchase_req_number}}</option>'+
                        @endforeach
                        '</select>  '+ 
                       '<label id="pr_number'+id2+'-error" class="error" for="pr_number'+id2+'"></label>'+
                    '</div>'+
                    '<div class="col-md-6">'+
                        '<label>Quantity<sup>*</sup></label>'+
                        '<input type="text"  name="qty[]" id="qty'+id2+'" class="qty input-css">'+
                        '<label id="qty'+id2+'-error" class="error"></label>'+
                    '</div>'+
                '</div>'
  
      );
      $('select').select2();
      id2++;
    });
    @foreach ($indent as $item)
    var stock_id="{{$item['stock_id']}}";
    var master_id="{{$item['master_cat_id']}}";
    var sub_cat_id="{{$item['sub_cat_id']}}";
    if(master_id==1){
        var countryid = sub_cat_id;
    var siblings=$('#paper_cat').parent().siblings().children().eq(1);
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/paper/item/" + countryid,
        type: "GET",
        success:function(result) {
            console.log(result);
           
             siblings.empty();
            for (var i = 0; i < result.length; i++) {
                siblings.append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
               $('#ajax_loader_div').css('display','none');
               $('#ink_item').val(stock_id).select2().trigger("change");
        }
    });
    }
    if(master_id==2){
        var countryid = sub_cat_id;
        var siblings=$('#ink_cat').parent().siblings().children().eq(1);
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/ink/item/" + countryid,
        type: "GET",
        success:function(result) {
            console.log(result);
           
             siblings.empty();
            for (var i = 0; i < result.length; i++) {
                siblings.append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
               $('#ajax_loader_div').css('display','none');
               $('#paper_item').val(stock_id).select2().trigger("change");
        }
    });
    }
    if(master_id==3){
        var countryid = sub_cat_id;
        var siblings=$('#plate_cat').parent().siblings().children().eq(1);
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/plate/item/" + countryid,
        type: "GET",
        success:function(result) {
            console.log(result);
           
             siblings.empty();
            for (var i = 0; i < result.length; i++) {
                siblings.append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
               $('#ajax_loader_div').css('display','none');
               $('#plate_item').val(stock_id).select2().trigger("change");
        }
    });
    }
    if(master_id==4){
        var countryid = sub_cat_id;
        var siblings=$('#misc_cat').parent().siblings().children().eq(1);
  $('#ajax_loader_div').css('display','block');
    $.ajax({
        url: "/misc/item/" + countryid,
        type: "GET",
        success:function(result) {
            console.log(result);
           
             siblings.empty();
            for (var i = 0; i < result.length; i++) {
                siblings.append($('<option value="' + result[i].id + '">' + result[i].item_name + '</option>'));
               }
               $('#ajax_loader_div').css('display','none');
               $('#misc_item').val(stock_id).select2().trigger("change");
        }
    });
    }

console.log(stock_id);  
   
    @endforeach

    var message="{{Session::get('indent')}}";
if(message=="successfull"){
    document.getElementById("indent").click();
}


</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
        
                    @yield('content')
            </div>
        <form method="POST" action="/purchase/requisition/update/{{$id}}" id="indent">
                   @csrf
                 
                   @foreach ($indent as $item)
                       
                   @endforeach
                    <div class="box-header with-border">
                        <div class='box box-default'>  <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/indent.title2')}}</h2><br><br><br>
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
                                    <div class="col-md-12 {{ $errors->has('entry_for') ? 'has-error' : ''}}">
                                        <label>{{__('purchase/purchase_req.item')}}<sup>*</sup></label>
                                        <div class="col-md-3">
                                            <div class="radio">
                                                <label><input    autocomplete="off" type="radio" class="entry_for" disabled {{ $item->name=="Paper" ? 'checked="checked"' : ''}}  value="Paper">{{__('purchase/indent.paper')}}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="radio">
                                                <label><input autocomplete="off" type="radio" class="entry_for" disabled {{ $item->name=="Inks & Chemicals" ? 'checked="checked"' : ''}}  value="Inks & Chemicals">{{__('purchase/indent.ink')}}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="radio">
                                                <label><input  autocomplete="off" type="radio" class="entry_for" disabled {{ $item->name=="Plate" ? 'checked="checked"' : ''}}  value="Plate">{{__('purchase/indent.plate')}}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                                <div class="radio">
                                                    <label><input  autocomplete="off" type="radio"  class="entry_for" disabled {{ $item->name=="Miscellaneous" ? 'checked="checked"' : ''}} value="Miscellaneous">{{__('purchase/indent.misc')}}</label>
                                                </div>
                                            <input type="hidden" name="entry_for" value="{{$item->name}}">
                                        </div>
                                        
                                    </div><!--col-md-6-->
                                    {!! $errors->first('entry_for', '<p class="help-block">:message</p>') !!}
                                </div><br><br>
                            </div>
                        </div>
                    </div>
                    <div class="box-header with-border paper" {{ $item->name=="Paper" ? 'style="display:block"' : 'style=display:none'}}>
                            <div class='box box-default'>  <br>
                                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/indent.paper')}}</h2><br><br><br>
                                <div class="container-fluid wdt">
                                    <div class="row">
                                       <div class="col-md-6 {{ $errors->has('paper_cat') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/indent.paper')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>
                                            <select style="width:100%" name="paper_cat" id="paper_cat" onchange="cat_change('paper_cat','paper_item');" class="select2 input-css paper_cat">
                                                <option value="default">Select Paper Item Category</option>
                                                @foreach ($paper as $key)
                                            <option value="{{$key->id}}" {{$item->master_cat_id==1 ?  $item->sub_cat_id==$key->id ? 'selected=selected' : '' : ''}}>{{$key->name}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('paper_cat', '<p class="help-block">:message</p>') !!}
                                       </div>

                                       <div class="col-md-6 {{ $errors->has('paper_item') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/indent.paper')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>
                                            <select style="width:100%" name="paper_item" id="paper_item"  class="select2 input-css paper_item">
                                                <option value="default">Select Paper Item Name</option>
                                                
                                            </select>
                                            {!! $errors->first('paper_item', '<p class="help-block">:message</p>') !!}
                                       </div>
                                    </div><br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('paper_item_qty') ? 'has-error' : ''}}">
                                                 <label>{{__('purchase/indent.item_qty')}}<sup>*</sup></label>
                                                 <input type="number" min="0" step="none" name="paper_item_qty" value="{{$item->master_cat_id==1 ?  $item->item_qty : ''}}" id="paper_item_qty" class="input-css paper_item_qty">
                                                 {!! $errors->first('paper_item_qty', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('paper_item_unit') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/indent.paper')}}  {{__('purchase/indent.unit')}}<sup>*</sup></label>
                                                    <select style="width:100%" name="paper_item_unit" id="paper_item_unit" class="select2 input-css paper_item_unit">
                                                        <option value="">Select Paper Qty Unit</option>
                                                        @foreach ($unit_paper as $key)
                                            <option value="{{$key->id}}" {{$item->master_cat_id==1 ?  $item->qty_unit==$key->id ? 'selected="selected"' : '' : ''}}>{{$key->uom_name}}</option>
                                                @endforeach
                                                    </select>
                                                    {!! $errors->first('paper_item_unit', '<p class="help-block">:message</p>') !!}
                                            </div>
                                         </div>
    
                                    <br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('paper_item_date') ? 'has-error' : ''}}">
                                                 <label>{{__('purchase/indent.item_date')}}<sup>*</sup></label>
                                                 <input type="text" autocomplete="off" name="paper_item_date" id="paper_item_date" value="{{$item->master_cat_id==1 ?  $item->item_req_date : ''}}" class="input-css paper_item_date datepickers">
                                                 {!! $errors->first('paper_item_date', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('paper_for') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/indent.for')}}<sup>*</sup></label>
                                                    <div class="col-md-3">
                                                            <div class="radio">
                                                                <label><input   {{$item->master_cat_id==1 ?  $item->for=="P.R. Entry" ? 'checked="checked"' : '' : ''}}  autocomplete="off" type="radio" class="paper_for"  value="P.R. Entry" name="paper_for">{{__('purchase/indent.pr')}}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="radio">
                                                                <label><input  {{$item->master_cat_id==1 ?  $item->for=="Inventory Maintenance" ? 'checked="checked"' : '' : ''}} autocomplete="off" type="radio" class="paper_for"   value="Inventory Maintenance" name="paper_for">{{__('purchase/indent.im')}}</label>
                                                            </div>
                                                        </div>
                                               </div>
                                         </div>
                                         {!! $errors->first('paper_for', '<p class="help-block">:message</p>') !!}
    
                                    <br><br>
                                    
                                </div>
                            </div>
                    </div>
                    <div class="box-header with-border ink" {{ $item->name=="Inks & Chemicals" ? 'style="display:block"' : 'style=display:none'}}>
                            <div class='box box-default'>  <br>
                                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/indent.ink')}}</h2><br><br><br>
                                <div class="container-fluid wdt">
                                    <div class="row">
                                       <div class="col-md-6 {{ $errors->has('ink_cat') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/indent.ink')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>
                                            <select style="width:100%" name="ink_cat" id="ink_cat" class="select2 input-css ink_cat" onchange="cat_change('ink_cat','ink_item');">
                                                <option value="default">Select Inks & Chemicals Item Category</option>
                                                @foreach ($ink as $key)
                                            <option value="{{$key->id}}" {{$item->master_cat_id==2 ?  $item->sub_cat_id==$key->id ? 'selected=selected' : '' : ''}} >{{$key->name}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('ink_cat', '<p class="help-block">:message</p>') !!}
                                       </div>
                                       <div class="col-md-6 {{ $errors->has('ink_item') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/indent.ink')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>
                                            <select style="width:100%" name="ink_item" id="ink_item" class="select2 input-css ink_item">
                                                <option value="default">Select Inks & Chemicals Item Name</option>
                                            </select>
                                            {!! $errors->first('ink_item', '<p class="help-block">:message</p>') !!}
                                       </div>
                                    </div><br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('ink_item_qty') ? 'has-error' : ''}}">
                                                 <label>{{__('purchase/indent.item_qty')}}<sup>*</sup></label>
                                                 <input type="number" min="0" value="{{$item->master_cat_id==2 ? $item->item_qty : ''}}" step="none" name="ink_item_qty" id="ink_item_qty" class="input-css ink_item_qty">
                                                 {!! $errors->first('ink_item_qty', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('ink_item_unit') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/indent.ink')}}  {{__('purchase/indent.unit')}}<sup>*</sup></label>
                                                    <select style="width:100%" name="ink_item_unit" id="ink_item_unit" class="select2 input-css ink_item_unit">
                                                        <option value="">Select Paper Qty Unit</option>
                                                        @foreach ($unit_ink as $key)
                                            <option value="{{$key->id}}" {{$item->master_cat_id==2 ?  $item->qty_unit==$key->id ? 'selected="selected"' : '' : ''}}>{{$key->uom_name}}</option>
                                                @endforeach
                                                    </select>
                                                    {!! $errors->first('ink_item_unit', '<p class="help-block">:message</p>') !!}
                                            </div>
                                         </div>
    
                                    <br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('ink_item_date') ? 'has-error' : ''}}">
                                                 <label>{{__('purchase/indent.item_date')}}<sup>*</sup></label>
                                                 <input type="text" autocomplete="off" value="{{$item->master_cat_id==2 ? $item->item_req_date : ''}}" name="ink_item_date" id="ink_item_date" class="input-css ink_item_date datepickers">
                                                 {!! $errors->first('ink_item_date', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('ink_for') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/indent.for')}}<sup>*</sup></label>
                                                    <div class="col-md-3">
                                                            <div class="radio">
                                                                <label><input    autocomplete="off" type="radio" class="ink_for" {{$item->master_cat_id==2 ?   $item->for=="P.R. Entry" ? 'checked="checked"' : '' : ''}} value="P.R. Entry" name="ink_for">{{__('purchase/indent.pr')}}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="radio">
                                                                <label><input autocomplete="off" type="radio" class="ink_for" {{$item->master_cat_id==2 ?   $item->for=="Inventory Maintenance" ? 'checked="checked"' : '' : ''}}  value="Inventory Maintenance" name="ink_for">{{__('purchase/indent.im')}}</label>
                                                            </div>
                                                        </div>
                                               </div>
                                         </div>
                                         {!! $errors->first('ink_for', '<p class="help-block">:message</p>') !!}
                                    <br><br>
                                    
                                </div>
                            </div>
                    </div>
                    <div class="box-header with-border plate"{{ $item->name=="Plate" ? 'style="display:block"' : 'style=display:none'}}>
                            <div class='box box-default'>  <br>
                                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/indent.plate')}}</h2><br><br><br>
                                <div class="container-fluid wdt">
                                    <div class="row">
                                       <div class="col-md-6 {{ $errors->has('plate_cat') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/indent.plate')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>
                                            <select style="width:100%" name="plate_cat" id="plate_cat" class="select2 input-css plate_cat" onchange="cat_change('plate_cat','plate_item');">
                                                <option value="default">Select Plate Item Category</option>
                                                @foreach ($plate as $key)
                                            <option value="{{$key->id}}"{{$item->master_cat_id==3 ?  $item->sub_cat_id==$key->id ? 'selected="selected"' : '' : ''}}>{{$key->name}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('plate_cat', '<p class="help-block">:message</p>') !!}
                                       </div>
                                       <div class="col-md-6 {{ $errors->has('plate_item') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/indent.plate')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>
                                            <select style="width:100%" name="plate_item" id="plate_item" class="select2 input-css plate_item">
                                                <option value="default">Select Plate Item Name</option>
                                            </select>
                                            {!! $errors->first('plate_item', '<p class="help-block">:message</p>') !!}
                                       </div>
                                    </div><br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('plate_item_qty') ? 'has-error' : ''}}">
                                                 <label>{{__('purchase/indent.item_qty')}}<sup>*</sup></label>
                                                 <input type="number" value="{{$item->master_cat_id==3 ?  $item->item_qty : ''}}" min="0" step="none" name="plate_item_qty" id="plate_item_qty" class="input-css plate_item_qty">
                                                 {!! $errors->first('plate_item_qty', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('plate_item_unit') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/indent.plate')}}  {{__('purchase/indent.unit')}}<sup>*</sup></label>
                                                    <select style="width:100%" name="plate_item_unit" id="plate_item_unit" class="select2 input-css plate_item_unit">
                                                        <option value="">Select Plate Qty Unit</option>
                                                        @foreach ($unit_plate as $key)
                                            <option value="{{$key->id}}" {{$item->master_cat_id==3 ?  $item->qty_unit==$key->id ? 'selected="selected"' : '' : ''}}>{{$key->uom_name}}</option>
                                                @endforeach
                                                    </select>
                                                    {!! $errors->first('plate_item_unit', '<p class="help-block">:message</p>') !!}
                                            </div>
                                         </div>
    
                                    <br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('plate_item_date') ? 'has-error' : ''}}">
                                                 <label>{{__('purchase/indent.item_date')}}<sup>*</sup></label>
                                                 <input type="text" autocomplete="off" value="{{$item->master_cat_id==3 ?  $item->item_req_date : ''}}" name="plate_item_date" id="plate_item_date" class="input-css plate_item_date datepickers">
                                                 {!! $errors->first('plate_item_date', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('plate_for') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/indent.for')}}<sup>*</sup></label>
                                                    <div class="col-md-3">
                                                            <div class="radio">
                                                                <label><input    autocomplete="off" type="radio" class="plate_for" {{$item->master_cat_id==3 ?  $item->for=="P.R. Entry" ? 'checked="checked"' : '' : ''}} value="P.R. Entry" name="plate_for">{{__('purchase/indent.pr')}}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="radio">
                                                                <label><input autocomplete="off" type="radio" class="plate_for" {{$item->master_cat_id==3 ?  $item->for=="Inventory Maintenance" ? 'checked="checked"' : '' : ''}}   value="Inventory Maintenance" name="plate_for">{{__('purchase/indent.im')}}</label>
                                                            </div>
                                                        </div>
                                               </div>
                                         </div>
                                         {!! $errors->first('plate_for', '<p class="help-block">:message</p>') !!}
                                    <br><br>
                                    
                                </div>
                            </div>
                    </div>
                    <div class="box-header with-border misc" {{ $item->name=="Miscellaneous" ? 'style="display:block"' : 'style=display:none'}}>
                            <div class='box box-default'>  <br>
                                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/indent.misc')}}</h2><br><br><br>
                                <div class="container-fluid wdt">
                                    <div class="row">
                                       <div class="col-md-6 {{ $errors->has('misc_cat') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/indent.misc')}}  {{__('purchase/indent.item')}}<sup>*</sup></label>
                                            <select style="width:100%" name="misc_cat" id="misc_cat" class="select2 input-css misc_cat" onchange="cat_change('misc_cat','misc_item');">
                                                <option value="default">Select misc Item Category</option>
                                                @foreach ($misc as $key)
                                            <option value="{{$key->id}}"{{$item->master_cat_id==4 ?  $item->sub_cat_id==$key->id ? 'selected="selected"' : '' : ''}}>{{$key->name}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('misc_cat', '<p class="help-block">:message</p>') !!}
                                       </div>
                                       <div class="col-md-6 {{ $errors->has('misc_item') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/indent.misc')}}  {{__('purchase/indent.name')}}<sup>*</sup></label>
                                            <select style="width:100%" name="misc_item" id="misc_item" class="select2 input-css misc_item">
                                                <option value="default">Select Plate Item Name</option>
                                                
                                            </select>
                                            {!! $errors->first('misc_item', '<p class="help-block">:message</p>') !!}
                                       </div>
                                    </div><br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('misc_item_qty') ? 'has-error' : ''}}">
                                                 <label>{{__('purchase/indent.item_qty')}}<sup>*</sup></label>
                                                 <input type="number" value="{{$item->master_cat_id==4 ?  $item->item_qty : ''}}" min="0" step="none" name="misc_item_qty" id="misc_item_qty" class="input-css misc_item_qty">
                                                 {!! $errors->first('misc_item_qty', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('misc_item_unit') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/indent.misc')}}  {{__('purchase/indent.unit')}}<sup>*</sup></label>
                                                    <select style="width:100%" name="misc_item_unit" id="misc_item_unit" class="select2 input-css misc_item_unit">
                                                        <option value="">Select Misc Qty Unit</option>
                                                        @foreach ($unit_misc as $key)
                                            <option value="{{$key->id}}"{{$item->master_cat_id==4 ?  $item->qty_unit==$key->id ? 'selected="selected"' : '' : ''}}>{{$key->uom_name}}</option>
                                                @endforeach
                                                    </select>
                                                    {!! $errors->first('misc_item_unit', '<p class="help-block">:message</p>') !!}
                                            </div>
                                         </div>
    
                                    <br><br>
                                    <div class="row">
                                            <div class="col-md-6 {{ $errors->has('misc_item_date') ? 'has-error' : ''}}">
                                                 <label>{{__('purchase/indent.item_date')}}<sup>*</sup></label>
                                                 <input type="text" autocomplete="off" value="{{$item->master_cat_id==4 ?  $item->item_req_date : ''}}" name="misc_item_date" id="misc_item_date" class="input-css misc_item_date datepickers">
                                                 {!! $errors->first('misc_item_date', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div class="col-md-6 {{ $errors->has('misc_for') ? 'has-error' : ''}}">
                                                    <label>{{__('purchase/indent.for')}}<sup>*</sup></label>
                                                    <div class="col-md-3">
                                                            <div class="radio">
                                                                <label><input    autocomplete="off" type="radio" class="misc_for" {{$item->master_cat_id==4 ?  $item->for=="P.R. Entry" ? 'checked="checked"' : '' : ''}}  value="P.R. Entry" name="misc_for">{{__('purchase/indent.pr')}}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="radio">
                                                                <label><input autocomplete="off" type="radio" class="misc_for" {{$item->master_cat_id==4 ?  $item->for=="Inventory Maintenance" ? 'checked="checked"' : '' : ''}}  value="Inventory Maintenance" name="misc_for">{{__('purchase/indent.im')}}</label>
                                                            </div>
                                                        </div>
                                               </div>
                                         </div>
                                         {!! $errors->first('misc_for', '<p class="help-block">:message</p>') !!}
                                    <br><br>
                                    
                                </div>
                            </div>
                    </div>
                    
                    <div class="box-header with-border pr" {{ $item->for=="P.R. Entry" ? 'style="display:block"' : 'style=display:none'}}>
                            <div class='box box-default'>  <br>
                                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/indent.paper')}}</h2><br><br><br>
                                <div class="container-fluid wdt">
                                     @php
                                         $index=0;
                                     @endphp  
                                    @foreach ($detail as $item)
                                    <input   name="old_pr[]" type="hidden" value="{{$item->pr_id}}">
                                    <input   name="old_id[]" type="hidden" value="{{$item->id}}">
                                    <div class="row">
                                        <div class="col-md-6 {{ $errors->has('pr_number.'.$index) ? ' has-error' : ''}}">
                                                <label>Purchase Indent<sup>*</sup></label>  
                                        <select onchange="updateiodata(this)" style="width:100%"  name="pr_number[]" id="pr_number_{{$index}}" class="select2 input-css pr_number">
                                                        <option value="">Select Indent Number</option>
                                                        @foreach ($purchase as $key)
                                            <option value="{{$key->id}}" {{ $item->pr_id==$key->id ? 'selected="selected"' : ''}}>{{$key->purchase_req_number}}</option>
                                                @endforeach
                                                    </select>
                                            {!! $errors->first('pr_number.'.$index, '<p class="help-block">:message</p>') !!}   
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('qty.'.$index) ? ' has-error' : ''}}">
                                                <label>{{__('purchase/indent.qty')}}<sup>*</sup></label>
                                                <input type="number"  value="{{$item->qty}}" min="0" step="none" name="qty[]" id="qty_{{$index}}" class="input-css qty">
                                                {!! $errors->first('qty.'.$index, '<p class="help-block">:message</p>') !!}          
                                        </div>
                                            
                                    </div><br>
                                    @php
                                        $index++;
                                    @endphp
                                    @endforeach
                                
                                    <div class="row required_im" >
    
                                        </div><br>
                                    <div class="row">
                                            <div class="col-md-6">
                                                    <button type="button" class="btn btn-success" id="add">Add new</button>
            
                                            </div>
                                        </div><br><br>
                                </div>
                            </div>
                    </div>
                    <div class="row">
                            <div class="col-md-12">
                                <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                            </div>
                    </div>  
               </form>
      </section>
@endsection
