@extends($layout)

@section('title', 'Binder Create')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Binder Master</a></li>
    

@endsection
@section('js')
<script src="/js/vendor/vendor.js"></script>
<script>
    $('#gst_select').select2();
    $('input[type=radio][name=gst_type]').change(function() {
        if (this.value == "0") {
            $('#gst_sel').slideDown();
            $('#gst').slideUp();    
        }
        if (this.value == "1") {
            $('#gst').slideDown();
            $('#gst_sel').slideUp();
    }
    });
    $('#pan_select').select2();
    $('input[type=radio][name=pan_type]').change(function() {
        if (this.value == "0") {
            $('#pan_sel').slideDown();
            $('#pan').slideUp();  
        }
        if (this.value == "1") {
            $('#pan').slideDown();
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
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <form method="POST" action="/binder/create" id="vendor" enctype="multipart/form-data">
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
                                <h2 class="box-title" style="font-size: 28px;margin-left:20px">Binder</h2><br><br><br>
                            <div class="container-fluid wdt">
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('name') ? 'has-error' : ''}}">
                                        <label>{{__('vendor/vendor.name')}}<sup>*</sup></label>
                                        <input style="width:100%" name="name" id="name"  class="input-css name" value="{{ $errors->has('name')?'': ($flag==1? '': session('lastformdata')['name'])}}">
                                        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('address') ? 'has-error' : ''}}">
                                        <label>{{__('vendor/vendor.address')}}<sup>*</sup></label>
                                        <input style="width:100%" name="address" id="address"  class="input-css address" value="{{ $errors->has('address')?'': ($flag==1? '': session('lastformdata')['address'])}}">
                                        {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
                                    </div>

                                </div><br><br>
                                    <div class="row">
                                        <div class="col-md-6 {{ $errors->has('state') ? 'has-error' : ''}}">
                                            <label>{{__('vendor/vendor.state')}}<sup>*</sup></label>
                                            <select style="width:100%" name="state" id="state" class="select2 input-css state">
                                                <option value="">Select State</option>
                                                @foreach ($state as $key)
                                            <option value="{{$key->id}}" {{$errors->has('state') ? '' : ($flag==1?'':(session('lastformdata')['state']==$key->id? 'selected="selected"':''))}}>{{$key->name}}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('person') ? 'has-error' : ''}}">
                                            <label>{{__('vendor/vendor.per')}}<sup>*</sup></label>
                                            <input style="width:100%" name="person" id="person"  class="input-css person" value="{{ $errors->has('person')?'': ($flag==1? '': session('lastformdata')['person'])}}">
                                            {!! $errors->first('person', '<p class="help-block">:message</p>') !!}
                                        </div>
        
                                    </div>

                                <br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('num') ? 'has-error' : ''}}">
                                        <label>{{__('vendor/vendor.num')}}<sup>*</sup></label>
                                        <input type="number" style="width:100%" name="num" id="num"  class="input-css num" value="{{ $errors->has('num')?'': ($flag==1? '': session('lastformdata')['num'])}}">
                                        {!! $errors->first('num', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('alt') ? 'has-error' : ''}}">
                                        <label>{{__('vendor/vendor.alt')}}<sup></sup></label>
                                        <input type="number" style="width:100%" name="alt" id="alt"  class="input-css alt" value="{{ $errors->has('alt')?'': ($flag==1? '': session('lastformdata')['alt'])}}">
                                        {!! $errors->first('alt', '<p class="help-block">:message</p>') !!}
                                    </div>
    
                                </div>

                            <br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('email') ? 'has-error' : ''}}">
                                    <label>{{__('vendor/vendor.email')}}<sup></sup></label>
                                    <input type="email" style="width:100%" name="email" id="email"  class="input-css num" value="{{ $errors->has('email')?'': ($flag==1? '': session('lastformdata')['email'])}}">
                                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('pay') ? 'has-error' : ''}}">
                                    <label>{{__('vendor/vendor.pay')}}<sup>*</sup></label>
                                    <select style="width:100%" name="pay" id="pay" class="select2 input-css pay">
                                        <option value="">Select Payment Term</option>
                                        @foreach ($payment as $key)
                                    <option value="{{$key->id}}" {{$errors->has('pay') ? '' : ($flag==1?'':(session('lastformdata')['pay']==$key->id? 'selected="selected"':''))}}>{{$key->value}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('pay', '<p class="help-block">:message</p>') !!}
                                </div>

                            </div>

                        <br><br>
                        {{-- <div class="row">
                            <div class="col-md-6 {{ $errors->has('gst') ? 'has-error' : ''}}">
                                <label>{{__('vendor/vendor.gst')}}<sup>*</sup></label>
                                <input type="text" style="width:100%" name="gst" id="gst"  class="input-css gst" value="{{ $errors->has('gst')?'': ($flag==1? '': session('lastformdata')['gst'])}}">
                                {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('pan') ? 'has-error' : ''}}">
                                <label>{{__('vendor/vendor.pan')}}<sup>*</sup></label>
                                <input type="text" style="width:100%" name="pan" id="pan"  class="input-css pan" value="{{ $errors->has('pan')?'': ($flag==1? '': session('lastformdata')['pan'])}}">
                                {!! $errors->first('pan', '<p class="help-block">:message</p>') !!}
                            </div>

                        </div> --}}
                        <div class="row">
                                <div class="col-md-6">
                                        <div class="form-group">
                                                <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                            <div class="po_type_label_er">
                                                <div class="col-md-2">
                                                    <div class="radio">
                                                        <label><input type="radio" value="1" checked class="gst_type" name="gst_type" >Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="radio">
                                                        <label><input type="radio" value="0" class="gst_type" name="gst_type" >No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('gst') || $errors->has('gst_sel') ? 'has-error' : ''}}">
                                            <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                            <div id="gst_entry">
                                                    <input type="text" class="form-control input-css" name="gst" value="{{ old('gst') }}" id="gst">
                                                    {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div id="gst_sel" style="display:none">
                                                <select name="gst_sel" style="width:100%" class="form-control input-css select" id="gst_select">
                                                        <option value="NA" {{old('gst_sel')=='0' ?'selected="selected"':''}}>NA</option>
                                                </select>
                                                {!! $errors->first('gst_sel', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                            {{-- <div class="col-md-6 {{ $errors->has('gst') ? 'has-error' : ''}}">
                               
                                <input type="text" class="form-control input-css" name="gst" value="{{ old('gst') }}">
                                {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                            </div> --}}
    
                           
                        </div><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('pan') ? 'has-error' : ''}}">
                                        <label>{{__('party_form.PAN')}} <sup>*</sup></label><br>
                                        <div class="po_type_label_er">
                                                <div class="col-md-2">
                                                    <div class="radio">
                                                        <label><input type="radio" value="1" checked class="pan_type" name="pan_type" >Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="radio">
                                                        <label><input type="radio"  value="0" class="pan_type" name="pan_type" >No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                               
                                        {{-- <input type="text" class="form-control input-css" name="pan" value="{{ $party->pan }}">
                                        {!! $errors->first('pan', '<p class="help-block">:message</p>') !!} --}}
                                    </div>
                                    <div class="col-md-6">
                                            <label>{{__('party_form.PAN')}} <sup>*</sup></label><br>
                                            <div id="pan_entry">
                                                    <input type="text" class="form-control input-css" name="pan"  id="pan">
                                                    {!! $errors->first('pan', '<p class="help-block">:message</p>') !!}
                                            </div>
                                            <div id="pan_sel" style="display:none">
                                                <select name="pan_sel" style="width:100%" class="form-control input-css select" id="pan_select">
                                                        <option value="NA" {{ old('pan')=='NA' ?'selected="selected"':''}}>NA</option>
                                                </select>
                                                {!! $errors->first('pan_sel', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                        </div>
                    <br><br>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('aadhar_file') ? 'has-error' : ''}}">
                            <label>Aadhar Upload <sup>*</sup></label><br>
                            <input type="file" class="" name="aadhar_file" required="">
                            {!! $errors->first('aadhar_file', '<p class="help-block">:message</p>') !!} 
                        </div>
                        <div class="col-md-6 {{ $errors->has('aadhar_no') ? 'has-error' : ''}}">
                            <label>Aadhar Number <sup>*</sup></label><br>
                            <input type="text" name="aadhar_no" class="form-control input-css" maxlength="12">
                            {!! $errors->first('aadhar_no', '<p class="help-block">:message</p>') !!} 
                        </div>
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('contract_file') ? 'has-error' : ''}}">
                            <label>Contract Upload <sup></sup></label><br>
                            <input type="file" class="" name="contract_file" >
                            {!! $errors->first('contract_file', '<p class="help-block">:message</p>') !!} 
                        </div>
                        
                    </div><br><br>
                    <h3>Bank Details</h3>
                <br>
                
                <div class="row">
                    <div class="col-md-4 {{ $errors->has('acc_name') ? 'has-error' : ''}}">
                            <label for="">Account Name <sup></sup></label>
                            <input type="text" name="acc_name" id="" class="input-css acc_name" >
                            {!! $errors->first('acc_name', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-4 {{ $errors->has('acc_number') ? 'has-error' : ''}}">
                            <label for="">Account Number<sup></sup></label>
                            <input type="number" min="0" step="none" name="acc_number" id="" class="input-css acc_number" >
                            {!! $errors->first('acc_number', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="col-md-4 {{ $errors->has('acc_ifsc') ? 'has-error' : ''}}">
                            <label for="">Account IFSC Code<sup></sup></label>
                            <input type="text" name="acc_ifsc" id="" class="input-css acc_ifsc" >
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
