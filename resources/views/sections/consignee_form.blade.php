
@extends($layout)

@section('title', __('consignee_form.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('consignee_form.consignee')}}</i></a></li>
    
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


function gst_yes(inp){ 
var xs1=$(inp).parent().parent().parent().parent().parent().parent().siblings().children().eq(1).slideDown();
var xs=$(inp).parent().parent().parent().parent().parent().parent().siblings().children().eq(2).slideUp();

// console.log(xs1);

  }
  
  function gst_no(inp){ 
    var xs=$(inp).parent().parent().parent().parent().parent().parent().siblings().children().eq(2).slideDown();
var xs1=$(inp).parent().parent().parent().parent().parent().parent().siblings().children().eq(1).slideUp();


console.log(xs);
console.log(xs1);

  } 
$('#addConsignee').click(function () {
  var j = $('#Consignee_No').val();
    $('#newConsignee').append(
      '<div class="box box-default" id="consignee">'+
          '<div class="row" style="background-color:#eee;margin-top:0px;">'+
          '<div class="col-md-10">'+
            '<h3 class="fieldset-title">{{__('consignee_form.mytitle')}}</h3>'+
          '</div>'+
          '<div class="col-md-2" style="float:right;">'+
            '<button type="button" class="close" onclick="$(this).parent().parent().parent().remove();" id="removeconsignee" >X</button>'+
          '</div>'+
        '</div>'+
        '<div class="container-fluid">'+
          '<div class="fieldset-wrapper">'+
              '<div class="">'+
                '<div class="make-box c-bg-gray" style="margin-bottom: 61px;">'+
                  '<div class="row">'+
                    '<div class="col-md-4">'+
                    ' <label>{{__('consignee_form.name')}}<sup>*</sup></label>'+
                      '<input id="name_input_'+j+'" type="text"  autocomplete="off" class="form-control name_input input-css" name="name[]">'+
                            '<p class="error_form" id="name_error" ></p>'+
                      '</div>'+
                      '<div class="col-sm-4">'+
                      '<div class="form-group">'+
                        '<label>{{__('consignee_form.area')}}<sup>*</sup></label>'+
                        '<input id="area_input_'+j+'" type="text" autocomplete="off" class="form-control area_input input-css" name="area[]">'+
                      '</div>'+
                    '</div>'+

                    '<div class="col-sm-4">'+
                      '<div class="form-group">'+
                          '<label>{{__('consignee_form.PIN')}}<sup>*</sup></label>'+
                        ' <input id="pincode_input_'+j+'" type="number" autocomplete="off" class="form-control pincode_input input-css" name="pincode[]">'+
                      '</div>'+
                  '</div>'+

                  '</div>'+
                  
                  '<div class="row">'+
                    '<div class="col-sm-4">'+
                      '<div class="form-group">'+
                        ' <label>{{__('consignee_form.country')}}<sup>*</sup></label>'+
                          '<select id="country_input_'+j+'" class="form-control country_input select2 countries" onchange="countries(this)"  name="country[]">'+
                              '<option value="">Select country</option> '+
                                 @foreach($countries as $key)
                                 '<option value="{{$key->id}}">{{$key->name}}</option> '+
                                 @endforeach
                            '</select>'+
                          '</select>'+
                      '</div>'+
                    '</div>'+
                    '<div class="col-md-4">'+
                        '<label>{{__('consignee_form.state')}}<sup>*</sup></label>'+
                        '<select id="state_input_'+j+'" class="form-control state_input select2 states" onchange="states(this,3)" name="state[]">'+
                            '<option value="">Select state </option>  '+
                        '</select>'+
                    '</div>'+

                    '<div class="col-md-4">'+
                      '<label>{{__('consignee_form.city')}}<sup>*</sup></label>'+
                      '<select id="city_input_'+j+'" class="form-control city_input select2 cities" name="city[]">'+
                          '<option value="">Select city</option>'+
                      '</select>  '+
                    '</div>'+
                  '</div>'+
                  '<div class="row">'+
                    '<div class="col-md-6">'+
                                '<div class="form-group">'+
                                       ' <label>{{__('party_form.GST')}} <sup>*</sup></label><br>'+
                                   ' <div class="po_type_label_er">'+
                                        '<div class="col-md-2">'+
                                            '<div class="radio">'+
                                                '<label><input type="radio" value="1"  checked onchange="gst_yes(this)" id="'+j+'" name="gst_type['+j+']" >Yes</label>'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-2">'+
                                            '<div class="radio">'+
                                                '<label><input type="radio" value="0"  onchange="gst_no(this)"  name="gst_type['+j+']" >No</label>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-md-6">'+
                              '<label>{{__('consignee_form.GST')}}<sup>*</sup></label>'+
                                    '<div id="gst_entry">'+
                                     
                                        '<input id="gst_input_'+j+'" type="text"  class="gst_input input-css"  name="gst['+j+']">'+
          
                                    '</div>'+
                                    '<div style="display:none">'+
                                        '<select name="gst_sel['+j+']" style="width:100%" class="input-css select2">'+
                                                '<option value="NA">NA</option>'+
                                        '</select>'+
                                    '</div>'+
                                '</div>'+
                  '</div><br>'+
                  '<div class="row">'+
                    '<div class="col-md-6">'+
                                '<div class="form-group">'+
                                       ' <label>{{__('party_form.PAN')}} <sup>*</sup></label><br>'+
                                   ' <div class="po_type_label_er">'+
                                        '<div class="col-md-2">'+
                                            '<div class="radio">'+
                                                '<label><input type="radio" value="1"  checked onchange="gst_yes(this)" id="'+j+'" name="pan_type['+j+']" >Yes</label>'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-2">'+
                                            '<div class="radio">'+
                                                '<label><input type="radio" value="0"  onchange="gst_no(this)"  name="pan_type['+j+']" >No</label>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-md-6">'+
                              '<label>{{__('consignee_form.PAN')}}<sup>*</sup></label>'+
                                    '<div id="gst_entry">'+
                                     
                                        '<input id="pan_input_'+j+'" type="text"  class="pan_input input-css"  name="pan['+j+']">'+
          
                                    '</div>'+
                                    '<div style="display:none">'+
                                        '<select name="pan_sel['+j+']" style="width:100%" class="input-css select2">'+
                                                '<option value="NA">NA</option>'+
                                        '</select>'+
                                    '</div>'+
                                '</div>'+
                  '</div><br>'+
                '</div> '+
              '</div>'+
          '</div>  '+
        '</div>'+
      '</div>'

    );
    $('select').select2();
    j++;
  $('#Consignee_No').val(j);

  });
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
                  $siblings.trigger('change');
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
                    $query1.append($('<option value="' + result[i].id + '" >' + result[i].city + '</option>'));
                  }
                  $('#ajax_loader_div').css('display','none');

              }

          });
      }
      function ImportExcelActive()
      {
        $('#dataentrybtn').prop('disabled',false);
        $('#data_entry').css("display","none");
        $('#import_excel').css("display","block");
        $('#data_entry_submit_btn').css('display','none')         
        $('#importexcelbtn').prop('disabled',true);
        $('#consignee_form').attr('files',true);
        $('#consignee_form').attr('enctype','multipart/form-data');
        $('#consignee_form').attr('action','/consignee/insert/excel');
      }
      function dataEnttryActive()
      {
        $('#dataentrybtn').prop('disabled',true);
        $('#data_entry').css("display","block");
        $('#import_excel').css("display","none");
        $('#data_entry_submit_btn').css('display','block')         
        $('#importexcelbtn').prop('disabled',false);
        $('#consignee_form').attr('files',false);
        $('#consignee_form').attr('enctype','');
        $('#consignee_form').attr('action','/consignee/insert');
      }

     {{ session('data_submit_type')=='excel' ? 'ImportExcelActive();':'' }}
</script>
@endsection

@section('main_section')
<section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
        {!!nl2br(session('importerrors'))!!}
    </div>
    <!-- Default box -->

    @if(in_array(1, Request::get('userAlloweds')['section']))
        <p></p>
        @endif

    <form method="POST" action="/consignee/insert" id="consignee_form">
            @csrf
            @php
            $flag=0;
            @endphp

            @if (empty(session('lastformdata')))            
            @php
              $flag=1;
              $to=1;
            @endphp
            @else
            @php
              $to = count(session('lastformdata')['name']);
            @endphp
            @endif
           @php
              //  print_r($to);die;
           @endphp
            <div class="box box-default" id="consignee">
                <div class="container-fluid">
                  <div class="fieldset-wrapper">
                    <h3 class="fieldset-title">{{__('consignee_form.mytitle')}}</h3>
                    
                      <div class="">
                        <div class="make-box c-bg-gray" style="margin-bottom: 61px;">
                          <div class="row">
                            <div class="col-md-12 {{ $errors->has('party') ? 'has-error' : ''}}" >
                              <label>{{__('consignee_form.party')}}<sup>*</sup></label>
                              <select id="party_input_0" class="form-control party_input  select2 party" required="" data-placeholder="" name="party" >

                                <option value="">Select Party</option>
                                @foreach($party as $key)
                                <option value="{{$key->id}}" {{ $errors->has('party_input') ? '' :( $flag==1? '': (session('lastformdata')['party']==$key->id? 'selected="selected"' :'' ) )}} >{{$key->partyname}}</option>
                                @endforeach
                              </select>
                              {!! $errors->first('party', '<p class="help-block">:message</p>') !!}
                       
                            </div><!--col-md-3-->
                          </div><!--row-->
                        </div>
                      </div>
                  </div>
                </div>
            </div><!--end of container-fluid-->
            <div class="form-group">
              <button class="btn btn-primary " id="dataentrybtn" onclick="dataEnttryActive()" disabled >Data Entry</button>
              <button class="btn btn-primary " id="importexcelbtn" onclick="ImportExcelActive()">Import from Excel</button>                
            </div>
            <div id="data_entry">
            @for($index=0;$index!=$to;)
                      
            <div class="box box-default" id="consignee">
              <div class="container-fluid">
                <div class="fieldset-wrapper">
                  <h3 class="fieldset-title">{{__('consignee_form.mytitle')}}</h3>
                    <div class="">
                    @if($index!=0)
                      <div class="col-md-2" style="float:right;">
                  <button type="button" class="close" onclick="$(this).parent().parent().parent().remove();"   id="removeconsignee" >X</button>
                  </div>
                  @endif
                      <div class="make-box c-bg-gray" style="margin-bottom: 61px;">
                        <div class="row">
                          <div class="col-md-4{{ $errors->has('name.'.$index) ? ' has-error' : ''}}">
                            <label>{{__('consignee_form.name')}}<sup>*</sup></label>
                            <input id="name_input_{{$index}}" value="{{ $errors->has('name.'.$index) ? '' :  
                            ($flag==1? '': session('lastformdata')['name'][$index]) }}"
                             type="text" autocomplete="off" class="form-control name_input input-css" name="name[]">
                            {!! $errors->first('name.'.$index, '<p class="help-block">:message</p>') !!} 
                          </div><!--col-md-3-->
                          <div class="col-sm-4 {{ $errors->has('area.'.$index) ? 'has-error' : ''}}">
                            <div class="form-group">
                              <label>{{__('consignee_form.area')}}<sup>*</sup></label>
                              <input  value="{{ $errors->has('area.'.$index)?'': ($flag==1? '': session('lastformdata')['area'][$index])}}" id="area_input_{{$index}}" type="text" autocomplete="off" class="form-control area_input input-css" name="area[]">
                            </div>
                            {!! $errors->first('area.'.$index, '<p class="help-block">:message</p>') !!}
                         
                          </div>
                          <div class="col-sm-4 {{ $errors->has('pincode.'.$index) ? 'has-error' : ''}}">
                            <div class="form-group">
                                <label>{{__('consignee_form.PIN')}}<sup>*</sup></label>
                                <input id="pincode_input_{{$index}}" minlength="6" value="{{ $errors->has('pincode.'.$index)?'': ($flag==1? '':  session('lastformdata')['pincode'][$index])}}" type="number" autocomplete="off" class="form-control pincode_input input-css" name="pincode[]">
                            </div>
                            {!! $errors->first('pincode.'.$index, '<p class="help-block">:message</p>') !!}
                          </div>
                          {{-- <div class="col-md-4 {{ $errors->has('gst.'.$index) ? 'has-error' : ''}}">
                            <label>{{__('consignee_form.GST')}}<sup>*</sup></label>
                            <input  value="{{ $errors->has('gst.'.$index)?'': ($flag==1? '': session('lastformdata')['gst'][$index])}}" id="gst_input_{{$index}}" type="text"  class="form-control gst_input input-css"  name="gst[]">
                            {!! $errors->first('gst.'.$index, '<p class="help-block">:message</p>') !!}
                       
                          </div>
                          <!--col-md-3--> --}}

                          {{-- <div class="col-md-4 {{ $errors->has('pan.'.$index) ? 'has-error' : ''}}">
                            <label>{{__('consignee_form.PAN')}}<sup>*</sup></label>
                            <input  value="{{ $errors->has('pan.'.$index)?'': ($flag==1? '': session('lastformdata')['pan'][$index])}}" id="pan_input_{{$index}}" type="text" autocomplete="off" class="form-control pan_input input-css" name="pan[]">
                            {!! $errors->first('pan.'.$index, '<p class="help-block">:message</p>') !!}
                         
                          </div> --}}
                          <!--col-md-3-->
                        </div><!--row-->

                       
                  

                        <div class="row">
                          <div class="col-sm-4 {{ $errors->has('country.'.$index) ? 'has-error' : ''}}">
                            <div class="form-group">
                              <label>{{__('consignee_form.country')}}<sup>*</sup></label>
                              <select  id="country_input_{{$index}}" class="form-control country_input select2 country"  data-placeholder="" name="country[]">
                                  <option value="0">Select country</option>
                                  @foreach ($countries as $key)
                                  <option value="{{$key->id}}" {{$errors->has('country.'.$index) ? '' : ($flag==1?'':(session('lastformdata')['country'][$index]==$key->id? 'selected="selected"':''))}}">{{$key->name}}</option>
                                  @endforeach
                              </select>
                            </div>
                            {!! $errors->first('country.'.$index, '<p class="help-block">:message</p>') !!}       
                          </div>
                          <div class="col-md-4 {{ $errors->has('state.'.$index) ? 'has-error' : ''}}">
                            <label>{{__('consignee_form.state')}}<sup>*</sup></label>
                            <select  id="state_input_{{$index}}" class="form-control state_input select2 state" id="" data-placeholder="" name="state[]">
                                <option value="">Select state </option>
                            </select>
                          <input type="hidden" id="hidden_state_val" value="{{ $errors->any() ? ($flag==1?'':session('lastformdata')['state'][$index]):''}}">
                            {!! $errors->first('state.'.$index, '<p class="help-block">:message</p>') !!}
                          </div>
                          <div class="col-md-4 {{ $errors->has('city.'.$index) ? 'has-error' : ''}}">
                            <label>{{__('consignee_form.city')}}<sup>*</sup></label>
                            <select id="city_input_{{$index}}" class="form-control city_input select2 city" id="" data-placeholder="" name="city[]">
                                <option value="">Select city </option>
                            </select>
                            <input type="hidden" id="hidden_city_val" value="{{ $errors->any() ? ($flag==1?'':session('lastformdata')['city'][$index]):''}}">

                            {!! $errors->first('city.'.$index, '<p class="help-block">:message</p>') !!}
                           </div>
                        </div><!--pin row ---->
                        <div class="row">
                          <div class="col-md-6">
                                  <div class="form-group">
                                          <label>{{__('party_form.GST')}} <sup>*</sup></label><br>
                                      <div class="po_type_label_er">
                                          <div class="col-md-2">
                                              <div class="radio">
                                                  <label><input type="radio" value="1" checked class="gst_type" id="gst_type_0" name="gst_type[0]" onchange="gst_yes(this)">Yes</label>
                                              </div>
                                          </div>
                                          <div class="col-md-2">
                                              <div class="radio">
                                                  <label><input type="radio" value="0" class="gst_type" id="gst_type_0" name="gst_type[0]" onchange="gst_no(this)">No</label>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                          </div>
                              <div class="col-md-6 {{ $errors->has('gst_.'.$index) || $errors->has('gst_sel') ? 'has-error' : ''}}">
                                <label>{{__('consignee_form.GST')}}<sup>*</sup></label>
                                      <div id="gst_entry">
                                       
                                          <input  value="{{ $errors->has('gst.'.$index)?'': ($flag==1? '': session('lastformdata')['gst'][$index])}}" id="gst_input_{{$index}}" type="text"  class="form-control gst_input input-css"  name="gst[0]">
                                          {!! $errors->first('gst_.'.$index, '<p class="help-block">:message</p>') !!}
            
                                      </div>
                                      <div id="gst_sel" style="display:none">
                                          <select name="gst_sel[0]" style="width:100%" class="form-control input-css select" id="gst_select[]">
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
                    <div class="col-md-6">
                            <div class="form-group">
                                    <label>{{__('party_form.PAN')}} <sup>*</sup></label><br>
                                <div class="po_type_label_er">
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input type="radio" value="1" checked class="pan_type" id="pan_type_0" name="pan_type[0]" onchange="gst_yes(this)">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input type="radio" value="0" class="pan_type" id="pan_type_0" name="pan_type[0]" onchange="gst_no(this)">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                        <div class="col-md-6 {{ $errors->has('pan_.'.$index) || $errors->has('pan_sel') ? 'has-error' : ''}}">
                          <label>{{__('consignee_form.PAN')}}<sup>*</sup></label>
                                <div id="gst_entry">
                                 
                                    <input  value="{{ $errors->has('pan.'.$index)?'': ($flag==1? '': session('lastformdata')['pan'][$index])}}" id="pan_input_{{$index}}" type="text"  class="form-control pan_input input-css"  name="pan[0]">
                                    {!! $errors->first('pan_.'.$index, '<p class="help-block">:message</p>') !!}
      
                                </div>
                                <div id="pan_sel" style="display:none">
                                    <select name="pan_sel[0]" style="width:100%" class="form-control input-css select" id="pan_select[]">
                                            <option value="NA" {{old('pan_sel')=='0' ?'selected="selected"':''}}>NA</option>
                                    </select>
                                    {!! $errors->first('pan_sel', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                {{-- <div class="col-md-6 {{ $errors->has('gst') ? 'has-error' : ''}}">
                   
                    <input type="text" class="form-control input-css" name="gst" value="{{ old('gst') }}">
                    {!! $errors->first('gst', '<p class="help-block">:message</p>') !!}
                </div> --}}

               
            </div><br>
                    </div>
                  </div>
              </div>
            </div>

            @php    
              $index++;
            @endphp
          @endfor
        </div>
        <div id="newConsignee">
        </div>
        </div>
        <div id="import_excel" style="display:none" >
          <div class="col-md-12 {{ $errors->has('excel') ? 'has-error' : ''}}" >
            <input type="file" name="excel" id="excel_data" />
            <small class="text-muted">Accepted File Format : xls, xlt, xltm, xltx, xlsm and xlsx </small>
          {!! $errors->first('excel', '<p class="help-block">:message</p>') !!}
          
        </div>
          <br />
          <a href="/download/format/consignee"><input type="button" class=" btn btn-primary" value="Download Format"></a>
          <input type="submit"  class="btn btn-success" value="Import" />
        </div>
        <!--end of container-fluid-->
        <input type="hidden" value="{{$index}}" name="Consignee_No" id="Consignee_No" >
        <div class="form-group" id="data_entry_submit_btn">
          <button type="button" class="btn btn-primary" id="addConsignee">Add new</button>
          <button type="submit" class="btn btn-success">Submit</button>
        </div>      
    </form>


</section><!--end of section-->



@endsection

