@extends($layout)

@section('title', 'Create Leave')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Leave</a></li>
   
@endsection

​
@section('js')
<script src="/js/hr/leave.js"></script>
<script>
    //function to convert 12 hr to 24 hr
const convertTime12to24 = (time12h) => {
  const [time, modifier] = time12h.split(' ');

  let [hours, minutes] = time.split(':');

  if (hours === '12') {
    hours = '00';
  }

  if (modifier === 'PM') {
    hours = parseInt(hours, 10) + 12;
  }

  return `${hours}:${minutes}`;
}

$("#employee_name").change(function(){
    var emp = $(this).val();
    $('#ajax_loader_div').css('display','block');
    $.ajax({
        type:"GET",
        url:"/get/emp/shift/time/api",
        data:{'emp':emp},
        success: function(result){
            if(result){
                // debugger;
                var shift = (result.shifting_timing).replace(/ : /g,':').split(' to ');
                var start = shift[0];
                var future_time = convertTime12to24(start);
                var dt1 = new Date();
                var future = new Date((new Date()).valueOf() + 1000*3600*24);
                var future_form_date = future.toISOString().substr(0,10);
                var dt2 = new Date(future_form_date+" "+future_time+":00");
                $("#total_time").text(diff_hours(dt2, dt1));
                var time_diff = diff_hours(dt2, dt1);
                if($("#total_time").text() >= 12){
                    $('.datepickers').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose: true,
                        startDate:'+1d',
                    });
                }else{
                    $('.datepickers').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose: true,
                        startDate:'+2d',
                    });
                }
                $('.datepickers').removeAttr("disabled");
                $('#ajax_loader_div').css('display','none');
            }
        }
    })
})
//function for getting difference
function diff_hours(dt2, dt1) 
 {

  var diff =(dt2.getTime() - dt1.getTime()) / 1000;
  diff /= (60 * 60);
  return Math.abs(Math.round(diff));
  
 }
 //getting start date for end date
$("#start_date").change(function(){
    
    var st_date = $("#start_date").val().split('-');
    var date = new Date(st_date[2],(st_date[1]-1),st_date[0]);
    $('#end_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        startDate:date,
    });
    $('#end_date').removeAttr("disabled");
})
</script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="/hr/leave/create" method="POST" id="form">
        @csrf
        <div class="box-header with-border">
            <div class='box box-default'> <br>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('employee_name') ? 'has-error' : ''}}">
                                <label>Name <sup>*</sup></label><br>
                                <select name="employee_name" id="employee_name" class="employee_name select2 input-css">
                                    <option value="">Select Name</option>
                                    @foreach ($emp as $item)
                                        <option value="{{$item->id}}" {{old('employee_name')==$item->id ? 'selected=selected' :''}}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('employee_name', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('email') ? 'has-error' : ''}}">
                                <label>Email Address <sup>*</sup></label><br>
                                <input type="text" name="email" id="email" class="email input-css" value="{{old('email')}}">
                                {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                        </div>
                        <span hidden="" id="total_time">0</span>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('contact') ? 'has-error' : ''}}">
                                    <label>Contact number when on leave <sup>*</sup></label><br>
                            <input type="number" name="contact" id="contact" class="input-css contact" value="{{old('contact')}}">
                                    {!! $errors->first('contact', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('adjust_leave') ? 'has-error' : ''}}">
                                <label>To be adjusted against leave balance ?<sup>*</sup></label><br>
                                <select name="adjust_leave" id="adjust_leave" class="adjust_leave select2 input-css">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                                {!! $errors->first('adjust_leave', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <div class="row">
                            <div class="col-md-6 {{ $errors->has('start_date') ? 'has-error' : ''}}">
                                    <label>Start Date <sup>*</sup></label><br>
                                    <input type="text" autocomplete="off" name="start_date" id="start_date"  class="start_date input-css datepickers" disabled="">
                                    {!! $errors->first('start_date', '<p class="help-block">:message</p>') !!}
                            </div>
                        
                            <div class="col-md-6 {{ $errors->has('end_date') ? 'has-error' : ''}}">
                                    <label>End Date <sup>*</sup></label><br>
                                    <input type="text" name="end_date" id="end_date" class="end_date input-css" disabled="">
                                    {!! $errors->first('end_date', '<p class="help-block">:message</p>') !!}
                            </div>
                    </div><br><br>
                    <!-- <div class="row">
                        <div class="col-md-6 {{ $errors->has('end_date') ? 'has-error' : ''}}">
                            <label>To be adjusted against leave balance? <sup>*</sup></label><br>
                            <div class="po_type_label_er">
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input type="radio" id="poex_0" value="Yes"  required checked class="po_type potype_0" name="to_adj" >Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input type="radio" id="poex_0"  value="No"  required class="po_type potype_0" name="to_adj" >No</label>
                                    </div>
                                </div>
                            </div>
                    </div> -->

                        <div class="row">
                            <div class="col-md-12 { $errors->has('reason') ? 'has-error' : ''}}" >
                                    <label>Reason For Leave <sup>*</sup></label><br>
                                    <textarea name="reason" id="reason" class="input-css reason" cols="30" rows="5">{{old('reason')}}</textarea>
                            </div>
                        </div><br>  <br>
                    </div>
                    
                </div> 
                <div class="row">
                    <div class="col-md-12">
                         <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                    </div>
                </div> 
            </div>
        </div>
            
        </form>
      
      </section>
@endsection
