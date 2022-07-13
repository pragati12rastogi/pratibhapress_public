
@extends($layout)

@section('title', __('gatepass.title'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('gatepass.mytitle')}}</i></a></li>
   
@endsection
@section('css')
<style>
p{
    font-size:21px;
}
</style>
@endsection

@section('js')
<script>
    $(".select2").on("select2:close", function (e) {
    $(this).valid();});
$('#gatepass_form').on('submit', function(event) {
    $('.challan_type').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Challan Type is required"}
        });    
    });
    $('.challan_num').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Challan Number is required"}
        });    
    });
    $('.carrier').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Carrier is required"}
        });    
    });
    $('.mode').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Dispatch Mode is required"}
        });    
    });
});
$("#gatepass_form").validate(
    {
        errorPlacement: function(error, element) {
            if(element.attr("name")=='item')
            {
                var v = $("#jqueryerror");
                error.insertAfter($(v));
            }
            else
            error.insertAfter(element);
        },
     
    }
);
var message="{{Session::get('pass')}}";
if(message=="Material"){
    document.getElementById("message1").click();
}


$('#challan_type').change(function(e) {
          var mode = $(e.target).val();
          var flag=0;
          console.log(mode);
          if(mode=='PPML/DCN/'){
            flag=1;
          }
          if(mode=='PPML/IDC/'){
            flag=2;
          }
          $('#ajax_loader_div').css('display','block');

          $.ajax({
              url: "/mode/challan/" + flag,
              type: "GET",
              success: function(result) {
                 
                  $('#challan_num').empty();

                 if(result!=''){
                    $("#challan_num").empty();
                    for (var i = 0; i < result.length; i++) {
                    $('#challan_num').append('<option value="'+result[i].id+'">'+result[i].challan_number+'</option>')
                  }
                  
                 }
                 else{
                     $("#challan_num").append('<option value="" disabled>No Challan</option>')
                 }
                 $('#ajax_loader_div').css('display','none');

            }
                  
        });
        });
</script>
@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                   
            </div>
            {{-- @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('gatepass.mytitle')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <form  action="/gatepass/material/insert" method="post" id="gatepass_form">
                                @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                                <label for="">{{__('gatepass.type')}}<sup>*</sup></label>
                                                <select value="{{ old('challan_type') }}" name="challan_type"  class="select2 input-css challan_type" id="challan_type">
                                                    <option value="">Select Challan Type</option>
                                                   <option value="PPML/DCN/">PPML/DCN</option>
                                                   <option value="PPML/IDC/">PPML/IDC</option>
                                                  
                                                 </select>
                                                 <label id="challan_type-error" class="error" for="challan_type"></label>
                                                {!! $errors->first('challan_type', '<p class="help-block">:message</p>') !!}
                                        </div>
                                           
                                    </div><br>
                                    <div class="row">
                                            <div class="col-md-4">
                                                    <label for="">{{__('gatepass.mode')}}<sup>*</sup></label>
                                                    <select name="mode" class="select2 input-css mode" id="mode">
                                                        <option value="">Select Dispatch Mode</option>
                                                       @foreach ($delivery as $item)
                                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                       @endforeach
                                                    </select>
                                                    <label id="mode-error" class="error" for="mode"></label>
                                                    {!! $errors->first('mode', '<p class="help-block">:message</p>') !!} 
                                                </div>
                                         
                                        <div class="col-md-4">
                                            <label for="">{{__('gatepass.challan')}}<sup>*</sup></label>
                                        <select name="challan_num[]" multiple class="select2 input-css challan_num" id="challan_num">
                                            <option value="" disabled>Select Challan Number</option>
                                        
                                        </select>
                                        <label id="challan_num-error" class="error" for="challan_num"></label>
                                        {!! $errors->first('challan_num', '<p class="help-block">:message</p>') !!}

                                    </div>
                                    <div class="col-md-4">
                                            <label for="">{{__('gatepass.carrier')}}<sup>*</sup></label>
                                        <select value="{{ old('carrier') }}" name="carrier"  class="select2 input-css carrier" id="carrier">
                                            <option value="">Select Carrier Name</option>
                                            @foreach ($carrier as $item)
                                            <option value="{{$item['id']}}">{{$item['courier_name']."-(".$item['name'].")"}}</option>
                                               @endforeach
                                        </select>
                                        <label id="carrier-error" class="error" for="carrier"></label>
                                       
                                        {!! $errors->first('carrier', '<p class="help-block">:message</p>') !!}

                                    </div>
                                    </div>
                                    {!! $errors->first('gatepass', '<p class="help-block">:message</p>') !!}
                                    <br><br>
                                    <div class="row tax_gatepass">
                                       
                                    </div>
                                    <div class="row">
                                        <div class="box-footer">
                                            <button type="submit" class="btn btn-primary" style="float:right">Submit</button>
                                        </div>
                                    </div><!--submit button row-->
                        </form>
                    </div><!--end of container-fluid-->
                </div><!------end of box box-default---->
            </div><!--end of box-header with-border-->
    </section><!--end of section-->
@endsection


