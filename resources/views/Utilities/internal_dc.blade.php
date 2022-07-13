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
    function validate_date() {
        var check = false;
        var value = $("#date").val();
        var re = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
            if( re.test(value)){
                var adata = value.split('-');
                var dd = parseInt(adata[0],10);
                var mm = parseInt(adata[1],10);
                var yyyy = parseInt(adata[2],10);
                var xdata = new Date(yyyy,mm-1,dd);
                if ( ( xdata.getFullYear() === yyyy ) && ( xdata.getMonth () === mm - 1 ) && ( xdata.getDate() === dd ) ) {
                check = true;
            }
            else {
                check = false;
            }
        } else {
            check = false;
        }
        if(!check)
            $("#date").siblings("#date-error").remove().parent().append('<label id="date-error" class="error" for="date">This field is required.</label>');
        return  check;
    }
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
</script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                        @include('sections.flash-message')
                       
                        @yield('content')
                </div>
       <form action="/internal/deliverychallan/create" method="POST" id="form">
        @csrf
        @php
        $flag=0;
        $start=0;
        @endphp

        @if (empty(session('lastformdata')))            
        @php
          $flag=1;
          $to=1;
        @endphp
         @else
        @php
          $to = 1;
        @endphp
        @endif 

        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <div class="container-fluid wdt">
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('entry_for') ? 'has-error' : ''}}">
                            <label>{{__('purchase/purchase_req.item')}}<sup>*</sup></label>
                            <div class="col-md-3">
                                <div class="radio">
                                    <label><input    autocomplete="off" type="radio" class="entry_for" {{ $errors->has('entry_for') ? '' :( $flag==1? '': (session('lastformdata')['entry_for']=='Outsource Order' ? 'checked=checked' :'' ))}} value="Outsource Order" name="entry_for">Outsource Order</label>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="radio">
                                    <label><input    autocomplete="off" type="radio" class="entry_for" {{ $errors->has('entry_for') ? '' :( $flag==1? '': (session('lastformdata')['entry_for']=='Other' ? 'checked=checked' :'' ))}} value="Other" name="entry_for">Other</label>
                                </div>
                            </div> 
                            
                            
                        </div><!--col-md-6-->
                        <label id="entry_for-error" class="error" for="entry_for"></label>
                        {!! $errors->first('entry_for', '<p class="help-block">:message</p>') !!}
                    </div><br><br>
                </div>
            </div>
    </div>
    <div class="box-header with-border order" {{ $errors->has('entry_for') ? '' :( $flag==1? 'style=display:none': (session('lastformdata')['entry_for']=='Outsource Order'? 'style=display:block' :'style=display:none' ) )}}>
        <div class='box box-default'>  <br>
            <div class="container-fluid wdt">
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">Outsource Order</h2><br><br>
                <div class="row">
                       
                    <div class="col-md-4 {{ $errors->has('out_order') ? 'has-error' : ''}}">
                            <label>{{__('Utilities/internal_dc.order')}}<sup>*</sup></label>
                        <input type="text" name="out_order" id="out_order" class="input-css out_order" value="{{ $errors->has('out_order')?'': ($flag==1? '': session('lastformdata')['out_order'])}}">
                        {!! $errors->first('out_order', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-4">
                        <label>{{__('internal_order.HSN')}} <sup>*</sup></label>
                        <select   value="{{ old('hsn') }}"   class="form-control select2 hsn"  data-placeholder=""style="width: 100%;" name="hsn">
                                <option value="">Select HSN</option>
                                @foreach($hsn as $key)
                                <option value="{{$key->id}}">{{$key->name." - ".$key->hsn."-".$key->gst_rate."%"}}</option>
                                @endforeach
                                </select>
                        {!! $errors->first('hsn', '<p class="help-block">:message</p>') !!}
                    </div><!--end of col-md-4-->
                    <div class="col-md-4 {{ $errors->has('rate') ? 'has-error' : ''}}" >
                        <label>{{__('internal_order.job rate')}}<sup>*</sup></label>
                        <input step="any"  value="{{ old('rate') }}"   type="number" class="form-control input-css" name="rate">
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
                                <input autocomplete="off" type="text" value="{{ $errors->has('date')?'': ($flag==1? '': session('lastformdata')['date'])}}" required  name="date" id="date" class="datepicker1 input-css">
                                {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('item_desc') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/internal_dc.desc')}}<sup>*</sup></label>
                                <input type="text" value="{{ $errors->has('item_desc')?'': ($flag==1? '': session('lastformdata')['item_desc'])}}" name="item_desc" id="item_desc" class="item_desc input-css">
                                {!! $errors->first('item_desc', '<p class="help-block">:message</p>') !!}
                            </div>
                           
                        </div><br><br>
                        <div class="row">
                           
                            <div class="col-md-6 {{ $errors->has('qty') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/internal_dc.qty')}}<sup>*</sup></label>
                                    <input type="number" value="{{ $errors->has('qty')?'': ($flag==1? '': session('lastformdata')['qty'])}}" name="qty" id="qty" class="qty input-css">
                                    {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6">
                                <label>{{__('internal_order.qty1')}}<sup>*</sup></label>
                                <select  class="select2 form-control input-css" name="qty_unit" style="width:100%">
                                        <option value="">Select Unit of Measurement</option>
                                        @foreach($uom as $key)
                                        <option value="{{$key->id}}">{{$key->uom_name}}</option>
                                        @endforeach
                                </select>    
                                {!! $errors->first('qty_unit', '<p class="help-block">:message</p>') !!}
                            </div><!--end of col-md-4-->
                        </div><br><br>
                        <div class="row">
                                
                                <div class="col-md-6 {{ $errors->has('detail') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/internal_dc.detail')}}<sup>*</sup></label>
                                    <input type="text" value="{{ $errors->has('detail')?'': ($flag==1? '': session('lastformdata')['detail'])}}" name="detail" id="detail" class="detail input-css">
                                    
                                    {!! $errors->first('detail', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('dispatch') ? 'has-error' : ''}}">
                                        <label>{{__('Utilities/internal_dc.dispatch')}}<sup>*</sup></label>
                                        <input type="text" value="{{ $errors->has('dispatch')?'': ($flag==1? '': session('lastformdata')['dispatch'])}}" name="dispatch" id="dispatch" class="dispatch input-css">
                                        {!! $errors->first('dispatch', '<p class="help-block">:message</p>') !!}
                                    </div>
                        </div><br><br>
                       
                        <div class="row">
                                <div class="col-md-12 {{ $errors->has('mode') ? 'has-error' : ''}}">
                                    <label>{{__('purchase/grn.mode')}}<sup>*</sup></label>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label><input    autocomplete="off" type="radio" class="mode" {{ $errors->has('mode') ? '' :( $flag==1? '': (session('lastformdata')['mode']=='By Self'? 'checked=checked' :'' ) )}} value="By Self" name="mode">By Self</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label><input autocomplete="off" type="radio" class="mode" {{ $errors->has('mode') ? '' :( $flag==1? '': (session('lastformdata')['mode']=='By Transporter'? 'checked=checked' :'' ) )}} value="By Transporter" name="mode">By Transporter</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" class="mode" {{ $errors->has('mode') ? '' :( $flag==1? '': (session('lastformdata')['mode']=='By Courier'? 'checked=checked' :'' ) )}}value="By Courier" name="mode">By Courier</label>
                                        </div>
                                    </div>
                                    <label id="mode-error" class="error" for="mode"></label>
                                    {!! $errors->first('mode', '<p class="help-block">:message</p>') !!}
                            </div><br><br><br><br>
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('reason') ? 'has-error' : ''}}">
                                    <label>{{__('Utilities/internal_dc.reason')}}<sup>*</sup></label>
                                    <input type="text" value="{{ $errors->has('reason')?'': ($flag==1? '': session('lastformdata')['reason'])}}" name="reason" id="reason" class="reason input-css">
                                    {!! $errors->first('reason', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('carrier') ? 'has-error' : ''}}">
                                <label>{{__('Utilities/internal_dc.carrier')}}<sup>*</sup></label>
                                <select name="carrier[]" id="carrier" class="select2 input-css carrier" required style="width: 100%;" multiple="multiple">
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
                     <input type="submit" class="btn btn-primary"  value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection