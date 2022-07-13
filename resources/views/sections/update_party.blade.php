@extends($layout)

@section('title', __('party_form.update_client'))

@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class=""> {{__('party_form.mytitle')}}</i></a></li>
   
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
<script src="/js/views/partyupdate.js"></script>
<script>
        $('#reference_name_sel').select2();
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
    // select the defined country
    $("#country_select").select2();
    $("#country_select").val("{{$party->country_id}}").trigger("change");
    
    // select the defined state
    $("#state_select").select2();
    $("#state_select").val("{{$party->state_id}}").trigger("change");
    
    // select the defined city
    $("#city_select").select2();
    $("#city_select").val("{{$party->city_id}}").trigger("change");

    // select the defined payment_terms_id
    $("#payment_select").select2();
    $("#payment_select").val("{{$party->payment_term_id}}").trigger("change");

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
            <div class="box-header with-border">
                <div class='box box-default'> <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('party_form.mytitle')}}</h2><br><br><br>
                    <div class="container-fluid">
                        <form method="post" action="/client/update?id={{$party->id}}" method="POST" id="form">
                            @csrf
                             <div class="row form-group">
                                <div class="col-md-12 {{ $errors->has('update_reason') ? 'has-error' : ''}}">
                                    <label>Update Reason<sup>*</sup></label>
                                    <input type="text" autocomplete="off" value="{{$errors->any() ? old('update_reason') : ''}}" required class="form-control  input-css" name="update_reason">
                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('partyname') ? 'has-error' : ''}}">
                                    <label>{{__('party_form.name')}} <sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" name="partyname" value="{{ $party->partyname }}">
                                    {!! $errors->first('partyname', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->

                                {{-- <div class="col-md-4 {{ $errors->has('reference_name') ? 'has-error' : ''}}">
                                    <label>{{__('party_form.CRN')}} <sup>*</sup></label><br>
                                    <input type="text" class="form-control input-css" name="reference_name" value="{{ $party->referencename }}">
                                    <input type="hidden" class="form-control input-css" name="refer_id" value="{{ $party->refer_id }}">
                                    {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-6--> --}}

                                <div class="col-md-6 {{ $errors->has('contact_person') ? 'has-error' : ''}}">
                                        <label>{{__('party_form.CP')}} <sup>*</sup></label><br>
                                        <input type="text" class="form-control input-css" name="contact_person" value="{{ $party->contact_person }}">
                                        {!! $errors->first('contact_person', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><!--row-->

                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">{{__('party_form.ref_type')}}<sup>*</sup></label>
                                        <div class="po_type_label_er">
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" value="0" {{old('ref_type')==0 ? 'checked=checked' : ''}} class="ref_type" name="ref_type" >New</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="radio">
                                                    <label><input type="radio" value="1" class="ref_type" {{old('ref_type')==1 ? 'checked=checked' : ''}} checked name="ref_type" >Existing</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 {{ $errors->has('reference_name') || $errors->has('reference_name_sel') ? 'has-error' : ''}}">
                                    <label>{{__('party_form.CRN')}} <sup>*</sup></label><br>
                                    <div id="reference_name_entry" style="display:none">
                                        <input type="text" class="form-control input-css" name="reference_name" id="reference_name"
                                        value="{{ old('reference_name') }}">
                                        {!! $errors->first('reference_name', '<p class="help-block">:message</p>') !!}
                                        <ul class="list-group" id="result" style="position: absolute;z-index: 3;"></ul>
                                    </div>
                                    <div id="reference_name_select" style="display:block">
                                        <select name="reference_name_sel" style="width:100%" class="form-control input-css select" id="reference_name_sel">
                                            <option value=" ">Select Reference Name</option>
                                            @foreach ($reference_name as $key)
                                                <option value="{{$key->id}}" {{ $party->refer_id==$key->id?'selected="selected"':''}}>{{$key->referencename}}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('reference_name_sel', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                                        
                                <!--col-md-6-->
                            </div>
                            <div class="row ">
                                    <div class="col-md-12 {{ $errors->has('address') ? 'has-error' : ''}}">
                                        <div class="form-group">
                                                <label>{{__('party_form.add')}}<sup>*</sup></label><br>
                                                <textarea class="form-control input-css" rows="3" placeholder="Enter ..." name="address" >{{ $party->address }}</textarea>

                                        </div>
                                        {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
                                    </div><!--end of col-md-6-->
                                </div><!--row-->
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('country') ? 'has-error' : ''}}">
                                        <div class="form-group">
                                                <label>{{__('party_form.country')}}<sup>*</sup></label><br>
                                                <select id="country_select" class="form-control select2 country" data-placeholder="" style="width: 100%;" name="country">
                                                        <option value="">Select country</option>
                                                    @foreach ($countries as $key)
                                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                                    @endforeach
                                                </select>

                                        </div>
                                        {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
                                    </div><!--end of col-md-6-->
                                    <div class="col-md-6 {{ $errors->has('state') ? 'has-error' : ''}}">
                                        <div class="form-group">
                                                <label>{{__('party_form.state')}}<sup>*</sup></label><br>
                                                <select id="state_select" class="form-control select2 state"  data-placeholder="" style="width: 100%;" name="state">
                                                    <option value="">Select country first</option>
                                                    @foreach ($states as $key)
                                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                                    @endforeach
                                                </select>

                                        </div>
                                        {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                                    </div><!--end of col-md-6-->
                                </div><!--row-->
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('city') ? 'has-error' : ''}}">
                                        <div class="form-group">
                                                <label>{{__('party_form.city')}}<sup>*</sup></label><br>
                                                <select id="city_select" class="form-control select2 city"  data-placeholder="" style="width: 100%;" name="city">
                                                    <option value="">Select city first</option>
                                                    @foreach ($city as $key)
                                                        <option value="{{$key->id}}">{{$key->city}}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
                                    </div><!--end of col-md-6-->
                                    <div class="col-md-6 {{ $errors->has('pincode') ? 'has-error' : ''}}">
                                            <div class="form-group">
                                                    <label>{{__('party_form.pincode')}}<sup>*</sup></label><br>
                                                    <input type="number" class="form-control input-css" name="pincode" value="{{ $party->pincode }}">

                                            </div>
                                            {!! $errors->first('pincode', '<p class="help-block">:message</p>') !!}
                                    </div><!--end of col-md-6-->
                                </div><!--row-->
                                <div class="row">
                                        <div class="col-md-6 {{ $errors->has('phone') ? 'has-error' : ''}}">
                                            <label>{{__('party_form.CN')}}  <sup>*</sup></label><br>
                                            <input  type="text" minlength="10" maxlength="10" class="form-control input-css" name="phone" value="@if(!empty($party->contact)){{$party->contact}}@endif">
                                            {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
                                        </div> <!--col-md-6-->

                                        <div class="col-md-6">
                                                <label>{{__('party_form.ACN')}}</label><br>
                                                <input type="number" minlength="10" maxlength="10" class="form-control input-css" name="alt_contact" value="{{ $party->alt_contact }}">
                                        </div> <!--col-md-6-->
                                    </div><!--row-->

                                    <div class="row">
                                        <div class="col-md-6 {{ $errors->has('email') ? 'has-error' : ''}}">
                                            <label>{{__('party_form.email')}} <sup>*</sup></label><br>
                                            <input  type="email" class="form-control input-css" name="email" value="{{ $party->email }}">
                                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                                        </div>
                                        <div class="col-md-6 {{ $errors->has('payment_term_id') ? 'has-error' : ''}}">
                                            <label>{{__('party_form.payment')}}<sup>*</sup></label>
                                            <select id="payment_select" class="form-control select2"  data-placeholder="" style="width: 100%;" name="payment_term_id" >
                                                    <option value="">Select Payment Term</option>
                                                    @foreach ($payment as $key)
                                                        <option value="{{$key->id}}">{{$key->value}}</option>
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
                                                                    <label><input type="radio" value="1" {{$party->gst_pointer=='1' ? 'checked=checked' : ''}} class="gst_type" name="gst_type" >Yes</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="radio">
                                                                    <label><input type="radio" {{$party->gst_pointer=='0' ? 'checked=checked' : ''}} value="0" class="gst_type" name="gst_type" >No</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 {{ $errors->has('gst') || $errors->has('gst_sel') ? 'has-error' : ''}}">
                                                        <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                                        <div id="gst_entry" {{$party->gst_pointer=='0' ? 'style=display:none' : 'style=display:block'}}>
                                                                <input type="text" class="form-control input-css" name="gst" value="{{ $party->gst_pointer=='1' ? $party->gst : ''}}" id="gst">
                                                                {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                        <div id="gst_sel" {{$party->gst_pointer=='0' ? 'style=display:block' : 'style=display:none'}}>
                                                            <select name="gst_sel" style="width:100%" class="form-control input-css select" id="gst_select">
                                                                    <option value="NA" {{$party->gst_pointer=='0' ?'selected="selected"':''}}>NA</option>
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
                                                                    <label><input type="radio" value="1" {{$party->pan=='NA' ? '' : 'checked=checked'}} class="pan_type" name="pan_type" >Yes</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="radio">
                                                                    <label><input type="radio" {{$party->pan=='NA' ? 'checked=checked' : ''}} value="0" class="pan_type" name="pan_type" >No</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    {{-- <input type="text" class="form-control input-css" name="pan" value="{{ $party->pan }}">
                                                    {!! $errors->first('pan', '<p class="help-block">:message</p>') !!} --}}
                                                </div>
                                                <div class="col-md-6 {{ $errors->has('pan') || $errors->has('pan_sel') ? 'has-error' : ''}}">
                                                        <label>{{__('party_form.PAN')}} <sup>*</sup></label><br>
                                                        <div id="pan_entry" {{$party->pan=='NA' ? 'style=display:none' : 'style=display:block'}}>
                                                                <input type="text" class="form-control input-css" name="pan" value="{{ $party->pan=='NA' ? '' : $party->pan}}" id="pan">
                                                                {!! $errors->first('pan', '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                        <div id="pan_sel" {{$party->pan=='NA' ? 'style=display:block' : 'style=display:none'}}>
                                                            <select name="pan_sel" style="width:100%" class="form-control input-css select" id="pan_select">
                                                                    <option value="NA" {{$party->pan =='NA' ?'selected=selected':''}}>NA</option>
                                                            </select>
                                                            {!! $errors->first('pan_sel', '<p class="help-block">:message</p>') !!}
                                                        </div>
                                                    </div>
                                   
                                    </div>
                                    <div class="row">
                                        <div class="box-footer">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div><!--submit button row-->
                        </form>
                    </div><!--end of container-fluid-->
                </div><!------end of box box-default---->
            </div><!--end of box-header with-border-->
    </section><!--end of section-->
@endsection