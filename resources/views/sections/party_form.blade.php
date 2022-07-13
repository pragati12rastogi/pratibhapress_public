@extends($layout)

@section('title', __('party_form.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('party_form.mytitle')}}</i></a></li>
<li><a href="#"><i class="">{{__('party_form.title')}}</i></a></li>
@endsection

@section('css')
<link rel="stylesheet" href="/css/party.css">
<style>
ul#result li {
    float:left;
    list-style: none;
    background-color: white;
    border:1px solid lightgrey;
    padding:2px;
    margin:2px;
    font-size: 12px;
}
</style>
@endsection

@section('js')
<script src="/js/views/party.js"></script>
<script>
$(document).ready(function(){
    $('#reference_name_sel').select2();
    $('.country').trigger('change');
    $('input[type=radio][name=ref_type]').change(function() {
        if (this.value == "1") {
            $('#reference_name_select').slideDown();
            $('#reference_name_entry').slideUp();    
        }
        if (this.value == "0") {
            $('#reference_name_entry').slideDown();
            $('#reference_name_select').slideUp();
    }
    });

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
})
$("#reference_name").keyup(function(){
    var text = $('#reference_name').val();
    console.log(text);
    $("#result").empty();
    $.ajax({
        type: "GET",
        url: '/reference/filter/api',
        data: {text: text},
        success: function(response) {
            $("#result").empty();
                if (response != '') {
                    $("#result").append('<li class="" style="color:grey;"> Added similar references :</li>');
                   
                } $.each(response,function(key, value){
                    $("#result").append('<li class="">'+value.referencename+'</li>');
                    //var cities = value.city.toLowerCase();
                    //if (cities = text.toLowerCase()) {
                    // $("#msg").append('This city is already added !');
                    //}
                }); 
                 
        },
        error: function() {
            $("#msg").html('');
            $("#msg").append('Internal Issue Try Again');
        }  
    }); 
})
</script>
@endsection

@section('main_section')
<section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    <!-- Default box -->
    <div class="box-header with-border">
        <div class='box box-default'> <br>
            <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('party_form.mytitle')}}</h2><br><br><br>
            <div class="container-fluid">
                <form  action="/client/insert" method="POST" id="form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('partyname') ? 'has-error' : ''}}">
                            <label>{{__('party_form.name')}} <sup>*</sup></label><br>
                            <input type="text" class="form-control input-css" name="partyname"
                                value="{{ old('partyname') }}">
                            {!! $errors->first('partyname', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->

                        

                        <div class="col-md-6 {{ $errors->has('contact_person') ? 'has-error' : ''}}">
                            <label>{{__('party_form.CP')}} <sup>*</sup></label><br>
                            <input type="text" class="form-control input-css" name="contact_person"
                                value="{{ old('contact_person') }}">
                            {!! $errors->first('contact_person', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                    <!--row-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{__('party_form.ref_type')}}<sup>*</sup></label>
                                <div class="po_type_label_er">
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input type="radio" value="0" checked class="ref_type" name="ref_type" >New</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input type="radio" value="1" class="ref_type" name="ref_type" >Existing</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 {{ $errors->has('reference_name') || $errors->has('reference_name_sel') ? 'has-error' : ''}}">
                            <label>{{__('party_form.CRN')}} <sup>*</sup></label><br>
                            <div id="reference_name_entry">
                                <input type="text" class="form-control input-css" name="reference_name" id="reference_name"
                                value="{{ old('reference_name') }}">
                                {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
                                <ul class="list-group" id="result" style="position: absolute;z-index: 3;"></ul>
                            </div>
                            <div id="reference_name_select" style="display:none">
                                <select name="reference_name_sel" style="width:100%" class="form-control input-css select" id="reference_name_sel">
                                    <option value=" ">Select Reference Name</option>
                                    @foreach ($reference_name as $key)
                                        <option value="{{$key->id}}" {{old('reference_name_sel')==$key->referencename?'selected="selected"':''}}>{{$key->referencename}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('reference_name_sel', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                                
                        <!--col-md-6-->
                    </div>
                    <div class="row row ">
                        <div class="col-md-12 {{ $errors->has('address') ? 'has-error' : ''}}">
                            <div class="form-group">
                                <label>{{__('party_form.add')}}<sup>*</sup></label><br>
                                <textarea class="form-control input-css" rows="3" placeholder="Enter ..." name="address"
                                    >{{ old('address') }}</textarea>

                            </div>
                            {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--end of col-md-6-->
                    </div>

                    <!--row-->
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('country') ? 'has-error' : ''}}">
                            <div class="form-group">
                                <label>{{__('party_form.country')}}<sup>*</sup></label><br>
                                <select class="form-control select2 country" data-placeholder="" style="width: 100%;"
                                    name="country">
                                    <option value="default">Select country</option>
                                    @foreach ($countries as $key)
                                    <option value="{{$key->id}}" {{$key->id==old('country')?'selected="selected"':''}}>{{$key->name}}</option>
                                    @endforeach
                                </select>
                                <label id="country-error" class="error" for="country"></label>
                            </div>
                            {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--end of col-md-6-->
                        <div class="col-md-6 {{ $errors->has('state') ? 'has-error' : ''}}">
                            <div class="form-group">
                                <label>{{__('party_form.state')}}<sup>*</sup></label><br>
                                <select class="form-control select2 state" data-placeholder="" style="width: 100%;"
                                    name="state">
                                    <option value="default">Select country first</option>
                                </select>
                                <label id="state-error" class="error" for="state"></label>
                                <input type="hidden" id="hidden_state_val" value="{{$errors->any() ? old('state'):''}}"/>

                            </div>

                            {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--end of col-md-6-->
                    </div>
                    <!--row-->
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('city') ? 'has-error' : ''}}">
                            <div class="form-group">
                                <label>{{__('party_form.city')}}<sup>*</sup></label><br>
                                <select class="form-control select2 city" data-placeholder="" style="width: 100%;"
                                    name="city">
                                    <option value="default">Select state first</option>
                                </select>
                                <label id="city-error" class="error" for="city"></label>
                                <input type="hidden" id="hidden_city_val" value="{{$errors->any() ? old('city'):''}}"/>
                            </div>
                            {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--end of col-md-6-->
                        <div class="col-md-6 {{ $errors->has('pincode') ? 'has-error' : ''}}">
                            <div class="form-group">
                                <label>{{__('party_form.pincode')}}<sup>*</sup></label><br>
                                <input type="number" class="form-control input-css" name="pincode"
                                    value="{{ old('pincode') }}">

                            </div>
                            {!! $errors->first('pincode', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--end of col-md-6-->
                    </div>
                    <!--row-->
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('phone') ? 'has-error' : ''}}">
                            <label>{{__('party_form.CN')}} <sup>*</sup></label><br>
                            <input type="number" minlength="10" maxlength="10" class="form-control input-css"
                                name="phone" value="{{ old('phone') }}">
                            {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-6-->

                        <div class="col-md-6">
                            <label>{{__('party_form.ACN')}}</label><br>
                            <input type="number" minlength="10" maxlength="10" class="form-control input-css"
                                name="alt_contact" value="{{ old('alt_contact') }}">
                        </div>
                        <!--col-md-6-->
                    </div>
                    <!--row-->

                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('email') ? 'has-error' : ''}}">
                            <label>{{__('party_form.email')}} <sup>*</sup></label><br>
                            <input type="email" class="form-control input-css" name="email" value="{{ old('email') }}">
                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('payment_term_id') ? 'has-error' : ''}}">
                            <label>{{__('party_form.payment')}}<sup>*</sup></label>
                            <select class="form-control select2" data-placeholder="" style="width: 100%;"
                                name="payment_term_id" value="{{ old('payment_term_id') }}">
                                <option value="">Select Payment Term</option>
                                @foreach ($payment as $key)
                                <option value="{{$key->id}}" {{$key->id = old('payment_term_id')?'selected="selected"':''}}>{{$key->value}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('payment_term_id', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
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
                    <!--row-->
                    <div class="row">
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    <!--submit button row-->
                </form>
            </div>
            <!--end of container-fluid-->
        </div>
        <!------end of box box-default---->
    </div>
    <!--end of box-header with-border-->
</section>
<!--end of section-->
@endsection
