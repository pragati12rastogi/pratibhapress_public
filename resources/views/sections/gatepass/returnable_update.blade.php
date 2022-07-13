
@extends($layout)

@section('title', __('gatepass.title5'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('gatepass.mytitle2')}}</i></a></li>
@endsection
@section('css')
<style>

</style>
@endsection

@section('js')
<script>
    
    $(".select2").on("select2:close", function (e) {
    $(this).valid();});
$('#gatepass_form').on('submit', function(event) {
    $('.remark').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Remark is required"}
        });    
    });
    $('.challan_num').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Challan Number is required"}
        });    
    });
    $('.ichallan_num').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Internal Delivery Challan Number is required"}
        });    
    });
    $('.return').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Return Date is required"}
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
if(message=="Returnable"){
    document.getElementById("message1").click();
}
$("input[name=challan_type]").change(function(){
    if(this.value=="delivery_challan")
    {
        $("#delivery_challan_div").toggle();
        $("#internal_dc_div").toggle();
    }
    else
    {
        $("#delivery_challan_div").toggle();
        $("#internal_dc_div").toggle();
    }
    $(".select2").select2();
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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('gatepass.mytitle2')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <form method="POST" action="/gatepass/returnable/update/form/{{$detail['id']}}" id="gatepass_form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                        <input type="text" name="update_reason" required="" class="form-control input-css " id="update_reason">
                                        {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                    </div><!--col-md-4-->
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="challan_type">{{__('gatepass.challan_type')}}</label>
                                        <div class="col-md-6">
                                            <label><input checked type="radio" value="delivery_challan" {{$detail['challan_type']=="delivery_challan"?'checked':''}} class="challan_type" id="challan_type" name="challan_type">
                                                {{__('gatepass.challan')}}</label>
                                        </div>
                                        <div class="col-md-6">
                                            <label><input type="radio" class="challan_type" value="internal_dc" {{$detail['challan_type']=="internal_dc"?'checked':''}} id="challan_type" name="challan_type">
                                                {{__('gatepass.ichallan')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="{{$detail['challan_type']=='internal_dc'?'':'display:none'}}" id="internal_dc_div">
                                            <label for="">{{__('gatepass.ichallan')}}<sup>*</sup></label>
                                        <select value="{{ old('ichallan_num') }}" name="ichallan_num" class="select2 input-css ichallan_num" id="ichallan_num">
                                            <option value="">Select Challan Number</option>
                                            @foreach ($internal_dc as $item)
                                        <option value="{{$item['id']}}" {{ $detail['challan_type']=='internal_dc' && $item['id']==$detail['challan_id'] ? 'selected=selected':''}}>{{$item['idc_number']}}</option>
                                            @endforeach
                                        </select>
                                        <label id="challan_num-error" class="error" for="challan_num"></label>
                                        {!! $errors->first('challan_num', '<p class="help-block">:message</p>') !!}

                                    </div>
                                    <div class="col-md-6" style="{{$detail['challan_type']=='delivery_challan'?'':'display:none'}}" id="delivery_challan_div">
                                        <label for="">{{__('gatepass.challan')}}<sup>*</sup></label>
                                    <select value="{{ old('challan_num') }}" name="challan_num" class="select2 input-css challan_num" id="challan_num">
                                        <option value="">Select Challan Number</option>
                                        @foreach ($delivery as $item)
                                    <option value="{{$item['id']}}" {{ $detail['challan_type']=='delivery_challan' && $item['id']==$detail['challan_id'] ? 'selected=selected':''}}>{{$item['challan_number']}}</option>
                                        @endforeach
                                    </select>
                                    <label id="challan_num-error" class="error" for="challan_num"></label>
                                    {!! $errors->first('challan_num', '<p class="help-block">:message</p>') !!}

                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                            <label for="">{{__('gatepass.remark')}}<sup>*</sup></label>
                                    <input type="text" value="{{$detail['remark']}}" name="remark" id="remark" class="input-css remark">
                                        {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}

                                    </div>
                                    <div class="col-md-6">
                                            <label for="">{{__('gatepass.return')}}<sup>*</sup></label>
                                            <input type="text" name="return" value="{{date("d/m/Y", strtotime($detail['return_date']))}}" id="return" class="input-css return datepicker1">
                                        <label id="return-error" class="error" for="return"></label>
                                        
                                        {!! $errors->first('return', '<p class="help-block">:message</p>') !!}

                                    </div>
                                </div>
                                <br><br>
                                    
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


