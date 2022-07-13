@extends($layout)

@section('title', 'Holiday')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Holiday</a></li>
   
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
       <form action="" method="POST" id="holiday">
        @csrf
            <div class="box-header with-border">
                <div class='box box-default'> <br>
                    <div class="container-fluid">
                  
                        <div class="row">
                            <div class="col-md-12 {{ $errors->has('holiday_name') ? 'has-error' : ''}}">
                                <label>Holiday Name <sup>*</sup></label><br>
                                <input type="text" name="holiday_name"  class="input-css holiday_name" value="{{old('holiday_name')}}" maxlength="30" >
                                
                                {!! $errors->first('holiday_name', '<p class="help-block">:message</p>') !!}
                            </div>
                            <!-- <div class="col-md-6 {{ $errors->has('h_year') ? 'has-error' : ''}}">
                                <label>Year <sup>*</sup></label><br>
                                <input type="text" name="h_year"  class="input-css h_year" value="{{old('h_year')}}" required>
                                
                                {!! $errors->first('h_year', '<p class="help-block">:message</p>') !!}
                            </div> -->
                            
                        </div><br><br>
                        <div class="row">
                            
                            <div class="col-md-4 {{ $errors->has('start_date') ? 'has-error' : ''}}">
                                <label>Start Date <sup>*</sup></label><br>
                                <input type="text" autocomplete="off" name="start_date" id="start_date" class=" input-css" >
                                    {!! $errors->first('start_date', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-4 {{ $errors->has('end_date') ? 'has-error' : ''}}">
                                <label>End Date <sup>*</sup></label><br>
                                <input type="text" autocomplete="off" name="end_date" id="end_date" class=" input-css" disabled="" >
                                    {!! $errors->first('end_date', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div><br><br>
                    </div>  <br>  <br>
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
