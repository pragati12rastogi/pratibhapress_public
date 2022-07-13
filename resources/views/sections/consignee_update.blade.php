
@extends($layout)

@section('title', __('consignee_form.update'))
 
{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="{{url('consignee/list')}}"><i class=""> {{__('consignee_form.consignee')}}</i></a></li>
<li><a href="#"><i class=""> {{__('consignee_form.update')}}</i></a></li>
@endsection
@section('css')
  <link rel="stylesheet" href="/css/consignee.css">
@endsection

@section('js')
<script src="/js/views/consignee.js"></script>
<script>
$(document).ready(
  function (){
    $('.country').trigger('change');
  }
);


    function countries(input) {
          var $countryid = $(input).val();
          var $siblings=$(input).parent().parent().parent().children().find('select').eq(1);
          $('#ajax_loader_div').css('display','block');

          $.ajax({
              url: "/state/" + $countryid,
              type: "GET",
              success: function(result) {
                 $siblings.empty();
                  for (var i = 0; i < result.length; i++) {
                   $siblings.append($('<option value="' + result[i].id + '">' + result[i].name + '</option>'));
                  }
                  $('#ajax_loader_div').css('display','none');

              }

          });
      }

     function states(input) {
          var $stateid = $(input).val();
          var $query1=$(input).parent().parent().parent().children().find('select').eq(2);
          $('#ajax_loader_div').css('display','block');

          $.ajax({
              url: "/city/" + $stateid,
              type: "GET",
              success: function(result) {
                  $query1.empty();
                  for (var i = 0; i < result.length; i++) {
                    $query1.append($('<option value="' + result[i].id + '">' + result[i].city + '</option>'));
                  }
                  $('#ajax_loader_div').css('display','none');

              }

          });
      }

      
    // select the defined country
    $("#country_select").select2();
    $("#country_select").val("{{$consignee->country}}").trigger("change");
    
    // select the defined state
    $("#state_select").select2();
    $("#state_select").val("{{$consignee->state}}").trigger("change");
    
    // select the defined city
    $("#city_select").select2();
    $("#city_select").val("{{$consignee->city}}").trigger("change");

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
    <!-- Default box -->
        @if(in_array(1, Request::get('userAlloweds')['section']))
        <p></p>
        @endif

    <form method="POST" action="/consignee/update" id="consignee_form">
            @csrf
        <div class="box box-default" id="consignee">
            <div class="container-fluid">
              <div class="fieldset-wrapper">

                <h3 class="fieldset-title">Client Name: {{$consignee->partyname}}</h3>
                  <div class="">
                    <div class="make-box c-bg-gray" style="margin-bottom: 61px;">
                      <input type="hidden" name="_id" value="{{$consignee->id}}">
                      <div class="row">
                        <div class="col-md-12 {{ $errors->has('update_reason') ? 'has-error' : ''}}">
                          <label>Update Reason<sup>*</sup></label>
                          <input type="text" autocomplete="off" required value="{{$errors->any() ? old('update_reason') : ''}}" class="form-control  input-css" name="update_reason">
                          {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                        </div><!--col-md-3-->
                      </div>
                      
                      <div class="row">
                        <div class="col-md-4 {{ $errors->has('name') ? 'has-error' : ''}}">
                          <label>{{__('consignee_form.name')}}<sup>*</sup></label>
                          <input type="text" autocomplete="off" value="{{$errors->any() ? old('name') : $consignee->consignee_name}}" class="form-control name_input input-css" name="name">
                          <p class="error_form" id="name_error"></p>
                          {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                        </div><!--col-md-3-->
                      </div><!--row-->
                      <div class="row">
                        <div class="col-md-6">
                                <div class="form-group">
                                        <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                    <div class="po_type_label_er">
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input type="radio" value="1" {{$consignee->gst=='NA' ? '' : 'checked=checked'}} class="gst_type" name="gst_type" >Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input type="radio" {{$consignee->gst=='NA' ? 'checked=checked' : ''}} value="0" class="gst_type" name="gst_type" >No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 {{ $errors->has('gst') || $errors->has('gst_sel') ? 'has-error' : ''}}">
                                    <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                    <div id="gst_entry" {{$consignee->gst=='NA' ? 'style=display:none' : 'style=display:block'}}>
                                            <input type="text" class="form-control input-css gst_input" name="gst" value="{{ $consignee->gst=='NA' ? '' : $consignee->gst}}" id="gst">
                                            {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div id="gst_sel" {{$consignee->gst=='NA' ? 'style=display:block' : 'style=display:none'}}>
                                        <select name="gst_sel" style="width:100%" class="form-control input-css select gst_sel" id="gst_select">
                                                <option value="NA" {{$consignee->gst=='NA' ?'selected="selected"':''}}>NA</option>
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
                                                <label><input type="radio" value="1" {{$consignee->pan=='NA' ? '' : 'checked=checked'}} class="pan_type" name="pan_type" >Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="radio">
                                                <label><input type="radio" {{$consignee->pan=='NA' ? 'checked=checked' : ''}} value="0" class="pan_type" name="pan_type" >No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                {{-- <input type="text" class="form-control input-css" name="pan" value="{{ $party->pan }}">
                                {!! $errors->first('pan', '<p class="help-block">:message</p>') !!} --}}
                            </div>
                            <div class="col-md-6 {{ $errors->has('pan') || $errors->has('pan_sel') ? 'has-error' : ''}}">
                                    <label>{{__('party_form.PAN')}} <sup>*</sup></label><br>
                                    <div id="pan_entry" {{$consignee->pan=='NA' ? 'style=display:none' : 'style=display:block'}}>
                                            <input type="text" class="form-control input-css pan_input" name="pan" value="{{ $consignee->pan=='NA' ? '' : $consignee->pan}}" id="pan">
                                            {!! $errors->first('pan', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div id="pan_sel" {{$consignee->pan=='NA' ? 'style=display:block' : 'style=display:none'}}>
                                        <select name="pan_sel" style="width:100%" class="form-control input-css select pan_sel" id="pan_select">
                                                <option value="NA" {{$consignee->pan =='NA' ?'selected=selected':''}}>NA</option>
                                        </select>
                                        {!! $errors->first('pan_sel', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
               
                </div>
                      <div class="row">
                       
                        <div class="col-sm-8 {{ $errors->has('area') ? 'has-error' : ''}}">
                          <div class="form-group">
                            <label>{{__('consignee_form.area')}}<sup>*</sup></label>
                            <input value="{{$errors->any() ? old('area') : $consignee->address}}" type="text" autocomplete="off" class="form-control area_input input-css" name="area">
                            {!! $errors->first('area', '<p class="help-block">:message</p>') !!}
                          </div>
                        </div>
                        <div class="col-sm-4 {{ $errors->has('pincode') ? 'has-error' : ''}}">
                          <div class="form-group">
                              <label>{{__('consignee_form.PIN')}}<sup>*</sup></label>
                              <input value="{{$errors->any() ? old('pincode') : $consignee->pincode}}" type="number" autocomplete="off" class="form-control pincode_input input-css" name="pincode">
                              {!! $errors->first('pincode', '<p class="help-block">:message</p>') !!}
                          </div>
                        </div>
                      </div>

                      <div class="row">
                            <div class="col-sm-4 {{ $errors->has('country') ? 'has-error' : ''}}">
                                    <div class="form-group">
                                        <label>{{__('consignee_form.country')}}<sup>*</sup></label>
                                        <select id="country_select" class="form-control country_input select2 country" data-placeholder="" name="country">
                                            <option value="">Select country</option>
                                               @foreach ($countries as $key)
                                                  <option value="{{$key->id}}" {{ $consignee->country==$key->id ?'selected="selected"':''}}>{{$key->name}}</option>
                                                @endforeach
                                        </select>
                                        {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
                                    </div>
                                  </div>
                        <div class="col-md-4 {{ $errors->has('state') ? 'has-error' : ''}}">
                            <label>{{__('consignee_form.state')}}<sup>*</sup></label>
                            <select class="form-control state_input select2 state" id="state_select" data-placeholder="" name="state">
                                <option value="">Select country first</option>
                                @foreach ($state as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                            <input type="hidden" id="hidden_state_val" value="{{$errors->any() ? old('state') : $consignee->state}}">
                          
                        </div>

                        <div class="col-md-4 {{ $errors->has('city') ? 'has-error' : ''}}">
                            <label>{{__('consignee_form.city')}}<sup>*</sup></label>
                            <select class="form-control city_input select2 city" id="city_select" data-placeholder="" name="city">
                                <option value="">Select state first</option>
                                @foreach ($city as $key)
                                    <option value="{{$key->id}}">{{$key->city}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
                            <input type="hidden" id="hidden_city_val" value="{{$errors->any() ? old('city') : $consignee->city}}">

                        </div>
                      </div><!--pin row ---->
                    </div>
                  </div>
              </div>
            </div>
        </div><!--end of container-fluid-->
        <div id="newConsignee">
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-success">Submit</button>
          <a href="/consignee/list?pid={{$consignee->party_id}}" class="btn btn-primary"> Back </a>
        </div>
    </form>

</section><!--end of section-->



@endsection

