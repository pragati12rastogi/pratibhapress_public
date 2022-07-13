@extends($layout)

@section('title', __('Utilities/internal_dc.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Internal Delivery Challan</a></li>
   
@endsection
@section('js')
<script src="/js/utilities/internal_dc.js"></script>
<script>
    $('input[type=radio][name=mode]').change(function() {
           if (this.value == "By Self"){
               $('.carrier').empty();
               var vehicle_2={!! json_encode($vehicle_2->toArray(), JSON_HEX_TAG) !!};
               for(var i=0;i<vehicle_2.length;i++){
                   $('.carrier').append('<option value='+vehicle_2[i].id+'>'+vehicle_2[i].name+'</option>');
               }
               
              
           }
           if (this.value == "By Transporter"){
               $('.carrier').empty();
               var vehicle_1={!! json_encode($vehicle_1->toArray(), JSON_HEX_TAG) !!};
               for(var i=0;i<vehicle_1.length;i++){
                   $('.carrier').append('<option value='+vehicle_1[i].id+'>'+vehicle_2[i].name+'</option>');
               }
               
              
           }
           if (this.value == "By Courier"){
               $('.carrier').empty();
               var vehicle_3={!! json_encode($vehicle_3->toArray(), JSON_HEX_TAG) !!};
               for(var i=0;i<vehicle_3.length;i++){
                   $('.carrier').append('<option value='+vehicle_3[i].id+'>'+vehicle_2[i].name+'</option>');
               }
               
              
           }
       });
$(document).ready(function(){
@foreach ($idc as $item)
    
@endforeach
var mode="{{$item->mode}}";
var carrier_name_id="{{$item->carrier_name_id}}";
var carrier=carrier_name_id.split(',');
if (mode == "By Self"){
               $('.carrier').empty();
               var vehicle_2={!! json_encode($vehicle_2->toArray(), JSON_HEX_TAG) !!};
               for(var i=0;i<vehicle_2.length;i++){
                   $('.carrier').append('<option value='+vehicle_2[i].id+'>'+vehicle_2[i].name+'</option>');
               }
               $('.carrier').val(carrier).select2();    
               
              
           }
           if (mode == "By Transporter"){
               $('.carrier').empty();
               var vehicle_1={!! json_encode($vehicle_1->toArray(), JSON_HEX_TAG) !!};
               for(var i=0;i<vehicle_1.length;i++){
                   $('.carrier').append('<option value='+vehicle_1[i].id+'>'+vehicle_2[i].name+'</option>');
               }
               $('.carrier').val(carrier).select2();
              
           }
           if (mode == "By Courier"){
               $('.carrier').empty();
               var vehicle_3={!! json_encode($vehicle_3->toArray(), JSON_HEX_TAG) !!};
               for(var i=0;i<vehicle_3.length;i++){
                   $('.carrier').append('<option value='+vehicle_3[i].id+'>'+vehicle_2[i].name+'</option>');
               }
               $('.carrier').val(carrier).select2();
              
           }

})
   </script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                        @include('sections.flash-message')
                       
                        @yield('content')
                </div>
            <form action="/internal/deliverychallan/update/{{$id}}" method="POST" id="form">
        @csrf
      
@foreach ($idc as $item)
    
@endforeach
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <div class="container-fluid wdt">
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('entry_for') ? 'has-error' : ''}}">
                            <label>{{__('purchase/purchase_req.item')}}<sup>*</sup></label>
                            <div class="col-md-3">
                                <div class="radio">
                                    <label><input    autocomplete="off" type="radio" class="entry_for" {{ $item->for=="Outsource Order" ? 'checked="checked"' : ''}} value="Outsource Order" name="entry_for">Outsource Order</label>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="radio">
                                    <label><input    autocomplete="off" type="radio" class="entry_for" {{ $item->for=="Other" ? 'checked="checked"' : ''}} value="Other" name="entry_for">Other</label>
                                </div>
                            </div> 
                            
                            
                        </div><!--col-md-6-->
                        <label id="entry_for-error" class="error" for="entry_for"></label>
                        {!! $errors->first('entry_for', '<p class="help-block">:message</p>') !!}
                    </div><br><br>
                </div>
            </div>
    </div>
    <div class="box-header with-border order" {{ $item->for=="Outsource Order" ? 'style=dispaly:block' : 'style=display:none'}}>
        <div class='box box-default'>  <br>
            <div class="container-fluid wdt">
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">Outsource Order</h2><br><br>
                <div class="row">
                       
                    <div class="col-md-4 {{ $errors->has('out_order') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/internal_dc.order')}}<sup>*</sup></label>
                        <input type="text" name="out_order" value="{{$item->outsource_no}}" id="out_order" class="input-css out_order" value="">
                        {!! $errors->first('out_order', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-4">
                        <label>{{__('internal_order.HSN')}} <sup>*</sup></label>
                        <select  class="form-control select2 hsn"  data-placeholder=""style="width: 100%;" name="hsn">
                                <option value="">Select HSN</option>
                                @foreach($hsn as $key)
                                <option value="{{$key->id}}" {{$item->hsn==$key->id ? 'selected=selected' : ''}}>{{$key->name." - ".$key->hsn."-".$key->gst_rate."%"}}</option>
                                @endforeach
                                </select>
                        {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                    </div><!--end of col-md-4-->
                    <div class="col-md-4 {{ $errors->has('rate') ? 'has-error' : ''}}" >
                        <label>{{__('internal_order.job rate')}}<sup>*</sup></label>
                        <input step="any"  value="{{$item->rate}}"   type="number" class="form-control input-css" name="rate">
                        {!! $errors->first('rate', '<p class="help-block">:message</p>') !!}
                    </div><!--end of col-md-4-->
                </div>
                <br><br>
            </div>
        </div>
    </div>

        <div class="box-header with-border other">
                <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">Details</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('date') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/internal_dc.date')}}<sup>*</sup></label>
                            <input type="text" value="{{$item->date}}" name="date"  id="date" required class="datepicker1 input-css">
                                {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('item_desc') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/internal_dc.desc')}}<sup>*</sup></label>
                                <input type="text" value="{{$item->item_desc}}" name="item_desc" id="item_desc" class="item_desc input-css">
                                {!! $errors->first('item_desc', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div><br><br>
                        <div class="row">
                         
                            <div class="col-md-6 {{ $errors->has('qty') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/internal_dc.qty')}}<sup>*</sup></label>
                                    <input type="number" value="{{$item->item_qty}}" name="qty" id="qty" class="qty input-css">
                                    {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6">
                                <label>{{__('internal_order.qty1')}}<sup>*</sup></label>
                                <select  class="select2 form-control input-css" name="qty_unit" style="width:100%">
                                        <option value="">Select Unit of Measurement</option>
                                        @foreach($uom as $key)
                                        <option value="{{$key->id}}" {{$item->qty_unit==$key->id ? 'selected=selected' : ''}}>{{$key->uom_name}}</option>
                                        @endforeach
                                </select>    
                                {!! $errors->first('qty_unit', '<p class="help-block">:message</p>') !!}
                            </div><!--end of col-md-4-->
                        </div><br><br>
                        <div class="row">
                                
                                <div class="col-md-6 {{ $errors->has('detail') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/internal_dc.detail')}}<sup>*</sup></label>
                                    <input type="text" value="{{$item->packing_desc}}" name="detail" id="detail" class="detail input-css">
                                    
                                    {!! $errors->first('detail', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('dispatch') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/internal_dc.dispatch')}}<sup>*</sup></label>
                                        <input type="text" value="{{$item->dispatch_to}}" name="dispatch" id="dispatch" class="dispatch input-css">
                                        {!! $errors->first('dispatch', '<p class="help-block">:message</p>') !!}
                                    </div>
                        </div><br><br>
                       
                        <div class="row">
                                <div class="col-md-12 {{ $errors->has('mode') ? 'has-error' : ''}}">
                                    <label>{{__('purchase/grn.mode')}}<sup>*</sup></label>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label><input    autocomplete="off" type="radio" {{ $item->mode=="By Self" ? 'checked="checked"' : ''}} class="mode"  value="By Self" name="mode">By Self</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label><input autocomplete="off" type="radio" class="mode" {{ $item->mode=="By Transporter" ? 'checked="checked"' : ''}} value="By Transporter" name="mode">By Transporter</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" class="mode" {{ $item->mode=="By Courier" ? 'checked="checked"' : ''}} value="By Courier" name="mode">By Courier</label>
                                        </div>
                                    </div>
                                    <label id="mode-error" class="error" for="mode"></label>
                                    {!! $errors->first('mode', '<p class="help-block">:message</p>') !!}
                            </div><br><br><br><br>
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('reason') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/internal_dc.reason')}}<sup>*</sup></label>
                                    <input type="text" value="{{$item->reason}}" name="reason" id="reason" class="reason input-css">
                                    {!! $errors->first('reason', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('carrier') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/internal_dc.carrier')}}<sup>*</sup></label>
                                <select name="carrier[]" id="carrier" class="select2 input-css carrier" style="width: 100%;" multiple="multiple" required>
                                        <option value="">Select Carrier Name</option>
                                    </select>
                                    <label id="carrier-error" class="error" for="carrier"></label>
                                {!! $errors->first('carrier', '<p class="help-block">:message</p>') !!}
                            </div>
                           
                        </div><br><br>
                     
                    </div>
                </div>
        </div>
       
 
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
