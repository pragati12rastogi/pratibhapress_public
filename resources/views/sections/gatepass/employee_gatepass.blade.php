
@extends($layout)

@section('title', __('gatepass.title1'))

{{-- TODO: fetch from auth --}}
@section('user', 'Aakanksha Jain')

@section('breadcrumb')
<li><a href="#"><i class="">{{__('gatepass.mytitle1')}}</i></a></li>
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
    $('.employee').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Employee is required"}
        });    
    });
    $('.reason').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Reason is required"}
        });    
    });
    $('.desc').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Reason Description is required"}
        });    
    });
    $('.duration').each(function(e) { 
        $(this).rules("add",{ 
                required: true,
                messages: { required: "Estimated Duration is required"}
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
if(message=="Employee"){
    document.getElementById("message1").click();
}
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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('gatepass.mytitle1')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <form method="POST" action="/gatepass/employee/insert" id="gatepass_form">
                                @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                                <label for="">{{__('gatepass.employee')}}<sup>*</sup></label>
                                                <select value="{{ old('employee') }}" name="employee"  class="select2 input-css employee" id="employee">
                                                    <option value="">Select Employee</option>
                                                        @foreach ($employee as $item)
                                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                        @endforeach
                                                 </select>
                                                 <label id="employee-error" class="error" for="employee"></label>
                                                {!! $errors->first('employee', '<p class="help-block">:message</p>') !!}
                                        </div>
                                       
                                        <div class="col-md-6">
                                                <label for="">{{__('gatepass.reason')}}<sup>*</sup></label>
                                            <select value="{{ old('reason') }}" name="reason" class="select2 input-css reason">
                                                <option value="">Select Reason</option>
                                                <option value="Personal">Personal</option>
                                                <option value="Tea">Tea</option>
                                                <option value="Office Work">Office Work</option>
                                                <option value="Leave for the day">Leave for the day</option>
                                                <option value="Other">Other</option>
                                            </select>

                                            <label id="reason-error" class="error" for="reason"></label>
                                            {!! $errors->first('reason', '<p class="help-block">:message</p>') !!}

                                        </div>
                                        
                                    </div>
                                   
                                    <br><br>
                                    <div class="row">
                                        
                                            <div class="col-md-6">
                                                    <label for="">{{__('gatepass.desc')}}<sup>*</sup></label>
                                                <input type="text" value="{{ old('desc') }}" name="desc"  class="input-css desc" id="desc"> 
                                            
                                                {!! $errors->first('desc', '<p class="help-block">:message</p>') !!}
                                            </div> 
                                            <div class="col-md-6">
                                                    <label for="">{{__('gatepass.duration')}}<sup>*</sup></label>
                                                <input type="text" value="{{ old('duration') }}" name="duration"  class="input-css duration" id="duration"> 
                                                
                                                {!! $errors->first('duration', '<p class="help-block">:message</p>') !!}
                                            </div> 
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


