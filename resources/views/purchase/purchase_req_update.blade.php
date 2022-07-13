@extends($layout)

@section('title', __('purchase/purchase_req.title2'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Purchase Indent</a></li>
   
@endsection
@section('js')
<script src="/js/purchase/purchase_req.js"></script>
<script>
    var currentDate = new Date();
$('.datepickers').datepicker({
    format: 'dd-mm-yyyy',
      autoclose: true,
      endDate:currentDate,
});
</script>
<script>
      var id=10;
    var id1=10;
    var id2=10;
$('#cio').click(function () {
    $('select').select2('destroy');
      $('.required_cio').append(
                '<div class="row">'+
            '<button type="button" class="close" onclick="$(this).parent().remove();" style="float:right;margin-right:16px;line-height: 0;font-size: 28px;" id="removeconsignee" >X</button>'+
                    '<div class="col-md-4">'+
                        '<label>Item Description<sup>*</sup></label>'+
                        '<input type="text"  name="item_desc_cio[]" id="item_desc_cio'+id2+'" class="desc_cio input-css">'+
                        '<label id="item_desc_cio'+id2+'-error" class="error"></label>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                       ' <label>Required Item Quantity<sup>*</sup></label>'+
                        '<input type="text" name="req_item_cio[]" id="req_item_cio'+id2+'" class="item_cio input-css">'+
                        '<label id="req_item_cio'+id2+'-error" class="error"></label>'+
                    '</div>'+
                   ' <div class="col-md-4">'+
                        '<label>Required Item Quantity Unit<sup>*</sup></label>'+
                        '<select name="req_item_unit_cio[]" id="req_item_unit_cio'+id2+'" class="unit_cio select2 input-css" style="width: 100%">'+
                        '<option value="">Select UOM</option>'+
                        @foreach ($unit as $key)
                        '<option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                        @endforeach
                        '</select>  '+  
                       '<label id="req_item_unit_cio'+id2+'-error" class="error" for="req_item_unit_cio'+id2+'"></label>'+
                    '</div>'+
                '</div><br>'
  
      );
      $('select').select2();
      id2++;
    });
  

    $('#dsio').click(function () {
    $('select').select2('destroy');
      $('.required_dsio').append(
                '<div class="row">'+
            '<button type="button" class="close" onclick="$(this).parent().remove();" style="float:right;margin-right:16px;line-height: 0;font-size: 28px;" id="removeconsignee" >X</button>'+
                    '<div class="col-md-4">'+
                        '<label>Item Description<sup>*</sup></label>'+
                        '<input type="text" name="item_desc_dsio[]" id="item_desc_dsio'+id+'" class="input-css item_desc_dsio">'+
                        '<label id="item_desc_dsio'+id+'-error" class="error"></label>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                       ' <label>Required Item Quantity<sup>*</sup></label>'+
                        '<input type="text" name="req_item_dsio[]" id="req_item_dsio'+id+'" class="input-css req_item_dsio">'+
                        '<label id="req_item_dsio'+id+'-error" class="error"></label>'+
                    '</div>'+
                   ' <div class="col-md-4">'+
                        '<label>Required Item Quantity Unit<sup>*</sup></label>'+
                        '<select name="req_item_unit_dsio[]" id="req_item_unit_dsio'+id+'" class="select2 input-css req_item_unit_dsio" style="width: 100%">'+
                        '<option value="">Select UOM</option>'+
                        @foreach ($unit as $key)
                        '<option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                        @endforeach
                        '</select>  '+  
                        '<label id="req_item_unit_dsio'+id+'-error" class="error"></label>'+
                    '</div>'+
                '</div><br>'
  
      );
      
      $('select').select2();
      id++;
    });

    $('#im').click(function () {
    $('select').select2('destroy');
      $('.required_im').append(
                '<div class="row">'+
            '<button type="button" class="close" onclick="$(this).parent().remove();" style="float:right;margin-right:16px;line-height: 0;font-size: 28px;" id="" >X</button>'+
                    '<div class="col-md-4">'+
                        '<label>Item Description<sup>*</sup></label>'+
                        '<input type="text" name="item_desc_im[]"  id="item_desc_im'+id1+'" class="input-css item_desc_im">'+
                        '<label id="item_desc_im'+id1+'-error" class="error"></label>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                       ' <label>Required Item Quantity<sup>*</sup></label>'+
                        '<input type="text" name="req_item_im[]" id="req_item_im'+id1+'" class="req_item_im input-css">'+
                        '<label id="req_item_im'+id1+'-error" class="error"></label>'+
                    '</div>'+
                   ' <div class="col-md-4">'+
                        '<label>Required Item Quantity Unit<sup>*</sup></label>'+
                        '<select name="req_item_unit_im[]" id="req_item_unit_im'+id1+'" class="req_item_unit_im select2 input-css" style="width: 100%">'+
                        '<option value="">Select UOM</option>'+
                        @foreach ($unit as $key)
                        '<option value="{{$key->id}}">{{$key->uom_name}}</option>'+
                        @endforeach
                        '</select>  '+ 
                        '<label id="req_item_unit_im'+id1+'-error" class="error"></label>'+ 
                    '</div>'+
                '</div><br>'
  
      );
      $('select').select2();
      id1++;
    });

    var message="{{Session::get('req')}}";
if(message=="successfull"){
    document.getElementById("req").click();
}
</script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
            @include('sections.flash-message')

            @yield('content')
    </div>
<form method="POST" action="/purchase/indent/update/{{$id}}" id="purchase">
                @csrf
                @foreach ($detail as $item)
                    
                @endforeach
                <div class="box-header with-border">
                        <div class='box box-default'>  <br>
                           
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/purchase_req.mytitle')}}</h2><br><br><br>
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
                                        <div class="col-md-12 {{ $errors->has('requested_by') ? 'has-error' : ''}}">
                                                <label for="">{{__('purchase/purchase_req.req')}}<sup>*</sup></label>
                                                <select type="text" name="requested_by" id="" class="select2 requested_by input-css" style="width:100%">
                                                        <option value="">Select Requested By</option>
                                                    @foreach ($user as $key)
                                                    <option value="{{$key->id}}" {{ $item->requested_by==$key->id ? 'selected="selected"' : ''}}>{{$key->name." - ".$key->department}}</option>
                                                    @endforeach
                                                    </select>
                                                    <label id="requested_by-error" class="error" for="requested_by"></label>
                                                 {!! $errors->first('requested_by', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br>
                                    <div class="row">
                                            <div class="col-md-12">
                                                    <label>{{__('purchase/purchase_req.item')}}</label>
                                                    
                                                    <div class="col-md-4">
                                                        <div class="radio">
                                                            <label><input    autocomplete="off" type="radio" class="req_for" disabled {{ $item->item_req_for=="Current Internal Order" ? 'checked="checked"' : ''}} value="Current Internal Order" >{{__('purchase/purchase_req.cio')}}</label>
                                                        </div>
                                                    </div>
                                                     <div class="col-md-4">
                                                        <div class="radio">
                                                            <label><input autocomplete="off" type="radio" class="req_for" disabled {{ $item->item_req_for=="Direct Supply Internal Order" ? 'checked="checked"' : ''}} value="Direct Supply Internal Order" >{{__('purchase/purchase_req.dsio')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="radio">
                                                            <label><input  autocomplete="off" type="radio" class="req_for" disabled {{ $item->item_req_for=="Inventory Maintenance" ? 'checked="checked"' : ''}}  value="Inventory Maintenance" >{{__('purchase/purchase_req.im')}}</label>
                                                        </div>
                                                    </div> 
                                                <input type="hidden" name="req_for" value="{{$item->item_req_for}}">
                                                </div>
                                                {!! $errors->first('req_for', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <br><br>
                                </div>  
                        </div>
                </div>  
                  
                        <div class="box-header with-border row_cio" {{ $item->item_req_for=="Current Internal Order" ? 'style="display:block"' : 'style=display:none'}}>
                            <div class='box box-default'> <br>
                                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/purchase_req.cio')}}</h2><br><br><br>
                                <div class="row cio">
                                        <div class="col-md-6 {{ $errors->has('cio') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/purchase_req.io')}}<sup>*</sup></label>
                                            <select name="cio" id="cio1" class="select2 input-css cio" style="width:100%">
                                                <option value="">Select Internal Order</option>
                                            @foreach ($io as $key)
                                            <option value="{{$key->id}}" {{ $item->io==$key->id ? 'selected="selected"' : ''}}>{{$key->io_number}}</option>
                                            @endforeach
                                            </select>
                                            <label id="cio1-error" class="error" for="cio1"></label>

                                            {!! $errors->first('cio', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('date_cio') ? 'has-error' : ''}}">
                                                <label>{{__('purchase/purchase_req.date')}}<sup>*</sup></label>
                                        <input type="text" name="date_cio" value="{{date('d-m-Y', strtotime($item->required_date))}}" id="" class="datepickers input-css date_cio">
                                                {!! $errors->first('date_cio', '<p class="help-block">:message</p>') !!}
                                        </div>
                                </div><br>
                                <h4 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/purchase_req.req_item')}}</h4><br><br><br>
                                @foreach ($cio as $val)
                                   @php
                                       $index=0;
                                   @endphp 
                               <input   name="old_cio[]" type="hidden" value="{{$val->io_id}}">
                                <div class="row">
                                    <div class="col-md-4 {{ $errors->has('item_desc_cio.'.$index) ? ' has-error' : ''}}">
                                            <label>{{__('purchase/purchase_req.desc')}}<sup>*</sup></label>
                                    <input type="text" value="{{$val->item_desc}}" id="item_desc_cio_{{$index}}" name="item_desc_cio[]"  class="desc_cio input-css">
                                            {!! $errors->first('item_desc_cio.'.$index, '<p class="help-block">:message</p>') !!}  
                                    </div>
                                    <div class="col-md-4 {{ $errors->has('req_item_cio.'.$index) ? ' has-error' : ''}}">
                                            <label>{{__('purchase/purchase_req.req_item')}}<sup>*</sup></label>
                                            <input type="number" min="0" value="{{$val->item_qty}}" id="req_item_cio_{{$index}}" name="req_item_cio[]"  class="item_cio input-css">
                                            {!! $errors->first('req_item_cio.'.$index, '<p class="help-block">:message</p>') !!}  
                                    </div>
                                    <div class="col-md-4 {{ $errors->has('req_item_unit_cio.'.$index) ? ' has-error' : ''}}">
                                            <label>{{__('purchase/purchase_req.req_item_unit')}}<sup>*</sup></label>
                                            <select name="req_item_unit_cio[]" id="req_item_unit_cio_{{$index}}" class="unit_cio select2 input-css" style="width: 100%">
                                                    <option value="">Select UOM</option>
                                                    @foreach ($unit as $key)
                                                   
                                                    <option value="{{$key->id}}" {{ $val->item_qty_unit==$key->id ? 'selected="selected"' : ''}}>{{$key->uom_name}}</option>
                                                    @endforeach
                                            </select>  
                                            <label id="req_item_unit_cio_0-error" class="error" for="req_item_unit_cio_0"></label>
                                            {!! $errors->first('req_item_unit_cio.'.$index, '<p class="help-block">:message</p>') !!}         
                                    </div>
                                </div><br>
                                @php
                                    $index++;
                                @endphp
                                @endforeach
                                <div class="row required_cio" >

                                </div>
                               
                                <div class="row">
                                    <div class="col-md-6">
                                            <button type="button" class="btn btn-success" id="cio">Add new</button>

                                    </div>
                                </div><br>
                            </div>
                        </div>

                <div class="box-header with-border row_dsio" {{ $item->item_req_for=="Direct Supply Internal Order" ? 'style="display:block"' : 'style=display:none'}}>
                        <div class='box box-default'> <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/purchase_req.dsio')}}</h2><br><br><br>
                            <div class="row cio">
                                        <div class="col-md-6 {{ $errors->has('dsio') ? 'has-error' : ''}}">
                                        <label>{{__('purchase/purchase_req.io')}}<sup>*</sup></label>
                                        <select name="dsio" id="" class="select2 input-css dsio" style="width:100%">
                                            <option value="">Select Internal Order</option>
                                        @foreach ($io as $key)
                                        <option value="{{$key->id}}" {{ $item->io==$key->id ? 'selected="selected"' : ''}}>{{$key->io_number}}</option>
                                        @endforeach
                                        </select>
                                        {!! $errors->first('dsio', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('date_dsio') ? 'has-error' : ''}}">
                                            <label>{{__('purchase/purchase_req.date')}}<sup>*</sup></label>
                                    <input type="text" name="date_dsio" value="{{date('d-m-Y', strtotime($item->date))}}" id="" class="datepickers input-css ">
                                            <label id="dsio-error" class="error" for="dsio"></label>
                                            {!! $errors->first('date_dsio', '<p class="help-block">:message</p>') !!}
                                    </div>
                            </div><br>
                            <h4 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/purchase_req.req_item')}}</h4><br><br><br>
                            @foreach ($dsio as $val)
                            @php
                                $index1=0;
                            @endphp 
                            <input   name="old_dsio[]" type="hidden" value="{{$val->io_id}}">
                            <div class="row">
                                <div class="col-md-4{{ $errors->has('item_desc_dsio.'.$index1) ? ' has-error' : ''}}">
                                        <label>{{__('purchase/purchase_req.desc')}}<sup>*</sup></label>
                                <input type="text" name="item_desc_dsio[]"value="{{$val->item_desc}}" id="item_desc_dsio_{{$index1}}"  class="input-css item_desc_dsio">
                                        {!! $errors->first('item_desc_dsio.'.$index1, '<p class="help-block">:message</p>') !!} 
                                </div>
                                <div class="col-md-4{{ $errors->has('req_item_dsio.'.$index1) ? ' has-error' : ''}}">
                                        <label>{{__('purchase/purchase_req.req_item')}}<sup>*</sup></label>
                                        <input type="number" min="0" name="req_item_dsio[]"value="{{$val->item_qty}}" id="req_item_dsio_{{$index1}}"  class="input-css req_item_dsio">
                                        {!! $errors->first('req_item_dsio.'.$index1, '<p class="help-block">:message</p>') !!} 
                                </div>
                                <div class="col-md-4{{ $errors->has('req_item_unit_dsio.'.$index1) ? ' has-error' : ''}}">
                                        <label>{{__('purchase/purchase_req.req_item_unit')}}<sup>*</sup></label>
                                        <select name="req_item_unit_dsio[]" class=" select2 req_item_unit_dsio input-css" id="req_item_unit_dsio_{{$index1}}" style="width: 100%">
                                        <option value="">Select UOM</option>
                                        @foreach ($unit as $key)
                                        <option value="{{$key->id}}" {{ $val->item_qty_unit==$key->id ? 'selected="selected"' : ''}}>{{$key->uom_name}}</option>
                                        @endforeach
                                </select> 
                                {!! $errors->first('req_item_unit_dsio.'.$index1, '<p class="help-block">:message</p>') !!}    
                                </div>
                            </div><br>
                            @php
                            $index1++;
                        @endphp
                        @endforeach
                            <div class="row required_dsio" >

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                        <button type="button" class="btn btn-success" id="dsio">Add new</button>

                                </div>
                            </div><br>
                        </div>
                </div>
                <div class="box-header with-border row_im" {{ $item->item_req_for=="Inventory Maintenance" ? 'style="display:block"' : 'style=display:none'}}>
                        <div class='box box-default'> <br>
                              
                            <h4 class="box-title" style="font-size: 28px;margin-left:20px">{{__('purchase/purchase_req.req_item')}}</h4><br><br><br>
                            @foreach ($im as $valu)
                            @php
                                $index2=0;
                            @endphp 
                        <input   name="old_im[]" type="hidden" value="{{$valu->io_id}}">
                            <div class="row">
                                <div class="col-md-4{{ $errors->has('item_desc_im.'.$index2) ? ' has-error' : ''}}">
                                   
                                        <label>{{__('purchase/purchase_req.desc')}}<sup>*</sup></label>
                                        <input type="text" name="item_desc_im[]"  value="{{$valu->item_desc}}" id="item_desc_im_{{$index2}}" class="item_desc_im input-css">
                                        {!! $errors->first('item_desc_im.'.$index2, '<p class="help-block">:message</p>') !!} 
                                    </div>
                                <div class="col-md-4{{ $errors->has('req_item_im.'.$index2) ? ' has-error' : ''}}">
                                   
                                        <label>{{__('purchase/purchase_req.req_item')}}<sup>*</sup></label>
                                <input type="number" min="0" name="req_item_im[]" value="{{$valu->item_qty}}" id="req_item_im_{{$index2}}" class="req_item_im input-css">
                                        {!! $errors->first('req_item_im.'.$index2, '<p class="help-block">:message</p>') !!} 
                                    </div>
                                <div class="col-md-4{{ $errors->has('req_item_unit_im.'.$index2) ? ' has-error' : ''}}">
                                   
                                        <label>{{__('purchase/purchase_req.req_item_unit')}}<sup>*</sup></label>
                                        <select name="req_item_unit_im[]"  id="req_item_unit_im_{{$index2}}" class="req_item_unit_im select2 input-css" style="width: 100%">
                                        <option value="">Select UOM</option>
                                        @foreach ($unit as $key)
                                        <option value="{{$key->id}}" {{ $valu->item_qty_unit==$key->id ? 'selected="selected"' : ''}}>{{$key->uom_name}}</option>
                                        @endforeach
                                </select> 
                                <label id="req_item_unit_im_0-error" class="error" for="req_item_unit_im_0"></label>
                                {!! $errors->first('req_item_unit_im.'.$index2, '<p class="help-block">:message</p>') !!}  
                                </div>
                            </div><br>
                            @php
                            $index2++;
                        @endphp
                        @endforeach
                            
                            <div class="row required_im" >

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                        <button type="button" class="btn btn-success" id="im">Add new</button>

                                </div>
                            </div><br>
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
