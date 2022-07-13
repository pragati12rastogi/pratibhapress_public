@extends($layout)

@section('title', __('vendor/vendor.title2'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Vendor Master</a></li>
    

@endsection
@section('js')
<script src="/js/vendor/vendor.js"></script>
<script>
    $('#gst_select').select2();
    $('input[type=radio][name=gst_type]').change(function() {
        if (this.value == "0") {
            $('#gst_sel').slideDown();
            $('#gst').slideUp();  
            $('#gst_entry').css('display','none');  
        }
        if (this.value == "1") {
            $('#gst').slideDown();
            $('#gst_entry').css('display','block');
            $('#gst_sel').slideUp();
    }
    });

    $('#pan_select').select2();
    $('input[type=radio][name=pan_type]').change(function() {
        if (this.value == "0") {
            $('#pan_sel').slideDown();
            $('#pan').slideUp();  
            $('#pan_entry').css('display','none');  
        }
        if (this.value == "1") {
            $('#pan').slideDown();
            $('#pan_entry').css('display','block');
            $('#pan_sel').slideUp();
    }
    });
</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
        
                    @yield('content')
            </div>
        <form method="POST" action="/vendor/list/updateDb/{{$id}}" id="vendor">
                   @csrf
        
                @foreach ($vendor as $item)
                    
                @endforeach
                   <div class="box-header with-border">
                        <div class='box box-default'>  <br>
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('vendor/vendor.mytitle')}}</h2><br><br><br>
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
                                    <div class="col-md-6 {{ $errors->has('name') ? 'has-error' : ''}}">
                                        <label>{{__('vendor/vendor.name')}}<sup>*</sup></label>
                                    <input style="width:100%" name="name" id="name"  class="input-css name" value="{{$item->name}}">
                                        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('address') ? 'has-error' : ''}}">
                                        <label>{{__('vendor/vendor.address')}}<sup>*</sup></label>
                                        <input style="width:100%" name="address" id="address"  class="input-css address" value="{{$item->address}}">
                                        {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
                                    </div>

                                </div><br><br>
                                    <div class="row">
                                        <div class="col-md-6 {{ $errors->has('state') ? 'has-error' : ''}}">
                                            <label>{{__('vendor/vendor.state')}}<sup>*</sup></label>
                                            <select style="width:100%" name="state" id="state" class="select2 input-css state">
                                                <option value="">Select State</option>
                                                @foreach ($state as $key)
                                            <option value="{{$key->id}}" {{ $item->state==$key->id ? 'selected="selected"' : ''}}>{{$key->name}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('person') ? 'has-error' : ''}}">
                                            <label>{{__('vendor/vendor.per')}}<sup>*</sup></label>
                                            <input style="width:100%" name="person" id="person"  class="input-css person" value="{{$item->con_person}}">
                                            {!! $errors->first('person', '<p class="help-block">:message</p>') !!}
                                        </div>
        
                                    </div>

                                <br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('num') ? 'has-error' : ''}}">
                                        <label>{{__('vendor/vendor.num')}}<sup>*</sup></label>
                                        <input type="number" style="width:100%" name="num" id="num"  class="input-css num" value="{{$item->number}}">
                                        {!! $errors->first('num', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('alt') ? 'has-error' : ''}}">
                                        <label>{{__('vendor/vendor.alt')}}<sup>*</sup></label>
                                        <input type="number" style="width:100%" name="alt" id="alt"  class="input-css alt" value="{{$item->alt_num}}">
                                        {!! $errors->first('alt', '<p class="help-block">:message</p>') !!}
                                    </div>
    
                                </div>

                            <br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('email') ? 'has-error' : ''}}">
                                    <label>{{__('vendor/vendor.email')}}<sup></sup></label>
                                    <input type="email" style="width:100%" name="email" id="email"  class="input-css num" value="{{$item->email}}">
                                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('pay') ? 'has-error' : ''}}">
                                    <label>{{__('vendor/vendor.pay')}}<sup>*</sup></label>
                                    <select style="width:100%" name="pay" id="pay" class="select2 input-css pay">
                                        <option value="">Select Payment Term</option>
                                        @foreach ($payment as $key)
                                    <option value="{{$key->id}}" {{ $item->payment_term_id==$key->id ? 'selected="selected"' : ''}}>{{$key->value}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('pay', '<p class="help-block">:message</p>') !!}
                                </div>

                            </div>

                        <br><br>
                       
                        <div class="row">
                                <div class="col-md-6">
                                        <div class="form-group">
                                                <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                            <div class="po_type_label_er">
                                                <div class="col-md-2">
                                                    <div class="radio">
                                                        <label><input type="radio" value="1" {{$item->gst=='NA' ? '' : 'checked=checked'}} class="gst_type" name="gst_type" >Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="radio">
                                                        <label><input type="radio" {{$item->gst=='NA' ? 'checked=checked' : ''}} value="0" class="gst_type" name="gst_type" >No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('gst') || $errors->has('gst_sel') ? 'has-error' : ''}}">
                                            <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                            <div id="gst_entry" {{$item->gst=='NA' ? 'style=display:none' : 'style=display:block'}}>
                                                    <input type="text" class="form-control input-css" name="gst" value="{{ $item->gst=='NA' ? '' : $item->gst}}" id="gst">
                                                    {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div id="gst_sel" {{$item->gst=='NA' ? 'style=display:block' : 'style=display:none'}}>
                                                <select name="gst_sel" style="width:100%" class="form-control input-css select" id="gst_select">
                                                        <option value="NA" {{$item->gst=='NA' ?'selected="selected"':''}}>NA</option>
                                                </select>
                                                {!! $errors->first('gst_sel', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                       
                           
                        </div><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('pan') ? 'has-error' : ''}}">
                                        <label>{{__('party_form.PAN')}} <sup>*</sup></label><br>
                                        <div class="po_type_label_er">
                                                <div class="col-md-2">
                                                    <div class="radio">
                                                        <label><input type="radio" value="1" {{$item->pan=='NA' ? '' : 'checked=checked'}} class="pan_type" name="pan_type" >Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="radio">
                                                        <label><input type="radio" {{$item->pan=='NA' ? 'checked=checked' : ''}} value="0" class="pan_type" name="pan_type" >No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        {{-- <input type="text" class="form-control input-css" name="pan" value="{{ $item->pan }}">
                                        {!! $errors->first('pan', '<p class="help-block">:message</p>') !!} --}}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('pan') || $errors->has('pan_sel') ? 'has-error' : ''}}">
                                            <label>{{__('party_form.PAN')}} <sup>*</sup></label><br>
                                            <div id="pan_entry" {{$item->pan=='NA' ? 'style=display:none' : 'style=display:block'}}>
                                                    <input type="text" class="form-control input-css" name="pan" value="{{ $item->pan=='NA' ? '' : $item->pan}}" id="pan">
                                                    {!! $errors->first('pan', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div id="pan_sel" {{$item->pan=='NA' ? 'style=display:block' : 'style=display:none'}}>
                                                <select name="pan_sel" style="width:100%" class="form-control input-css select" id="pan_select">
                                                        <option value="NA" {{$item->pan =='NA' ?'selected=selected':''}}>NA</option>
                                                </select>
                                                {!! $errors->first('pan_sel', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                       
                        </div>
                    <br><br>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('lev1') ? 'has-error' : ''}}">
                            <label>{{__('vendor/vendor.lev1')}}<sup>*</sup></label>
                            <select style="width:100%" name="lev1" id="lev1" class="select2 input-css lev1">
                                <option value="">Select Level 1 Passing Authority</option>
                                @foreach ($authority1 as $key)
                            <option value="{{$key->id}}" {{ $item->level_authority1==$key->id ? 'selected="selected"' : ''}}>{{$key->user_name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('lev1', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('lev2') ? 'has-error' : ''}}">
                            <label>{{__('vendor/vendor.lev2')}}<sup>*</sup></label>
                            <select style="width:100%" name="lev2" id="lev2" class="select2 input-css lev2">
                                <option value="">Select Level 2 Passing Authority</option>
                                @foreach ($authority2 as $key)
                            <option value="{{$key->id}}" {{ $item->level_authority2==$key->id ? 'selected="selected"' : ''}}>{{$key->user_name}}</option>
                                @endforeach
                            
                            </select>
                            {!! $errors->first('lev2', '<p class="help-block">:message</p>') !!}
                        </div>

                    </div>
                <H3>Bank Details</H3>
                <br>
                <div class="row">
                    <div class="col-md-4 {{ $errors->has('acc_name') ? 'has-error' : ''}}">
                            <label for="">Account Name<sup>*</sup></label>
                            <input type="text" name="acc_name" value="{{$item->acc_name}}" id="" class="input-css acc_name">
                            {!! $errors->first('acc_name', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-4 {{ $errors->has('acc_number') ? 'has-error' : ''}}">
                            <label for="">Account Number<sup>*</sup></label>
                            <input type="number" min="0" step="none" value="{{$item->acc_number}}" name="acc_number" id="" class="input-css acc_number">
                            {!! $errors->first('acc_number', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-4 {{ $errors->has('acc_ifsc') ? 'has-error' : ''}}">
                            <label for="">Account IFSC Code<sup>*</sup></label>
                            <input type="text" name="acc_ifsc" id="" value="{{$item->acc_ifsc}}" class="input-css acc_ifsc">
                            {!! $errors->first('acc_ifsc', '<p class="help-block">:message</p>') !!}
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
