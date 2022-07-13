@extends($layout)

@section('title', 'Update Holiday')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    
    <li><a href="{{url('/holiday/summary')}}"><i class=""></i>Holiday List</a></li>
    <li><a href="#"><i class=""></i>Update Holiday</a></li>
@endsection
@section('css')
<style type="text/css">
    .datepicker{
        z-index: 10000 !important;
    }
</style>
@endsection
​
@section('js')
<script src="/js/hr/holiday.js"></script>
<script>

$(document).ready(function(){
    
    var fdate = new Date("{{date('Y-m-d',strtotime($holiday['start_date']))}}");
    $('#end_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        startDate:fdate
    });
});
 var currentDate = new Date();
$('#start_date').datepicker({
    format: 'dd-mm-yyyy',
        autoclose: true,
        startDate:currentDate,
});

$("#start_date").change(function(){

    $("#end_date").datepicker("destroy");
    var st_date = $("#start_date").val().split('-');
    var date = new Date(st_date[2],(st_date[1]-1),st_date[0]);
    $('#end_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        startDate:date,
    });
    // $('#end_date').removeAttr("disabled");
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
       <form action="/update/holiday/{{$id}}" method="POST" id="holiday">
        @csrf
            <div class="box-header with-border">
                <div class='box box-default'> <br>
                    <div class="container-fluid">
                        <div class="row">
                             <div class="col-md-6 ">
                                    <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                    <input type="text" name="update_reason" required class="input-css" id="update_reason">
                                    {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->
                             </div>
                        <br><br>
                        <div class="row">
                            <div class="col-md-12 {{ $errors->has('holiday_name') ? 'has-error' : ''}}">
                                <label>Holiday Name <sup>*</sup></label><br>
                                <input type="text" name="holiday_name"  class="input-css holiday_name" value="{{$holiday['name']}}" maxlength="30" required>
                                
                                {!! $errors->first('holiday_name', '<p class="help-block">:message</p>') !!}
                            </div>
                            <!-- <div class="col-md-6 {{ $errors->has('h_year') ? 'has-error' : ''}}">
                                <label>Year <sup>*</sup></label><br>
                                <input type="text" name="h_year"  class="input-css h_year" value="{{$holiday['year']}}" required>
                                
                                {!! $errors->first('h_year', '<p class="help-block">:message</p>') !!}
                            </div> -->
                            
                        </div><br><br>
                        <div class="row">
                            
                            <div class="col-md-4 {{ $errors->has('start_date') ? 'has-error' : ''}}">
                                <label>Start Date <sup>*</sup></label><br>
                                <input type="text" autocomplete="off" value="{{$holiday['start_date']}}" name="start_date" id="start_date" class=" input-css" required>
                                    {!! $errors->first('start_date', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-4 {{ $errors->has('end_date') ? 'has-error' : ''}}">
                                <label>End Date <sup>*</sup></label><br>
                                <input type="text" autocomplete="off" name="end_date" value="{{$holiday['end_date']}}" id="end_date" class=" input-css" required="">
                                    {!! $errors->first('end_date', '<p class="help-block">:message</p>') !!}
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
