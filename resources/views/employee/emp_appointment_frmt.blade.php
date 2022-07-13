@extends($layout)

@section('title', 'Employee Appointment Format')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Employee Appointment Format</a></li>
@endsection
@section('css')
<style>
    #active_cat > li{
        border:1px solid #8a8787;
        margin: 5px;
    }
    .note-editable {
        min-height: 132.109px;
    }
</style>
@endsection
@section('js')
  <script src="/js/Employee/emp_format.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.15/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.15/dist/summernote.min.js"></script>
  <script>
    $(document).ready(function() {
        $('#active_cat.nav-pills > li > a').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#active_cat a[href="' + activeTab + '"]').tab('show');
        }
        cat_val();
        $('#let_content_of').summernote();
        $('#let_content_fx').summernote();
        $('#let_content_tr').summernote();
        $('#let_content_pr').summernote();
        $('#let_content_co').summernote();
        $('#let_content_ndh').summernote();
        $('#let_content_nde').summernote();
        $('#let_content_rl').summernote();
    });
    $("#active_cat li a").click(function(){
        var value =$(this).text();
            
        $(".cat_type").val(value);
    })
    function cat_val(){
            var value =$("#active_cat li.active a").text();   
            $(".cat_type").val(value);
       }
    // $(".let_type").change(function() {
    //     check();
    // });
    // function check(){
    //     var l_id = $(".let_type").val();
    //     $.ajax({
    //         type:"GET",
    //         url:"/employee/appointment/format/api/".l_id,
    //         success: function(result){
    //             console.log(result);
    //             if (result) {
    //                 $(".let_content").empty();
    //                 $(".let_content").append(result);   
    //             }
    //         }
       
    //     })
    // }

  </script>
@endsection
@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="" method="POST" id="letter_form" >
        @csrf

        <div class="box-header with-border">
            <div class='box box-default' style="margin-bottom: 0px;">
                
                <div class="container-fluid wdt" >
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('cat_type') ? 'has-error' : ''}}">
                            <h5><b>{{__('Employee/category.type')}}<sup>*</sup></b></h5>
                                <ul class="nav nav-pills" id="active_cat">
                                    <li class="active" style="background-color: {{ isset($let_cont['Offer Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#offer_letter_box">{{__('Employee/category.offer_letter')}}</a></li>
                                    <li style="background-color: {{ isset($let_cont['Trainee Appointment Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#trainee_box">{{__('Employee/category.trainee_app_letter')}}</a></li>
                                    <li style="background-color: {{ isset($let_cont['Probation Appointment Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#probation_box">{{__('Employee/category.prob_app_letter')}}</a></li>
                                    <li style="background-color: {{ isset($let_cont['Confirmation Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#confirmation_box">{{__('Employee/category.conf_letter')}}</a></li>
                                    <li style="background-color: {{ isset($let_cont['Fixed Term Appointment Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#fixed_term_box">{{__('Employee/category.fix_t_app_letter')}}</a></li>
                                    <li style="background-color: {{ isset($let_cont['Employee Relieving Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#employee_relieving_box">{{__('Employee/category.relieve_letter')}}</a></li>
                                    <li style="background-color: {{ isset($let_cont['Hindi No Dues Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#hn_no_due_letter_box">{{__('Employee/category.no_dues_letter')}}</a></li>
                                    <li style="background-color: {{ isset($let_cont['English No Dues Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#en_no_due_letter_box">{{__('Employee/category.no_dues_letter_en')}}</a></li>
                                </ul>
                               <input type="text" name="cat_type" class="cat_type" style="opacity:0">
                            {!! $errors->first('cat_type', '<p class="help-block">:message</p>') !!}
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content"> 
            <div class="box-header with-border tab-pane fade active in" id="offer_letter_box" >
                <div class='box box-default'>  <br>
                    {{-- <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.offer_letter')}}</h3><br><br><br> --}}
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('let_content_of') ? ' has-error' : ''}}">
                                        <label for="">Content <sup>*</sup></label>
                                        <textarea id="let_content_of" name="let_content_of" class="let_content_of">
                                            {{ isset($let_cont['Offer Letter']['content'])==1?$let_cont['Offer Letter']['content']:'' }}
                                        </textarea>
                                        {!! $errors->first('let_content_of', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="box-header with-border tab-pane fade" id="trainee_box"  >
                <div class='box box-default'>  <br>
                    {{-- <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.trainee_app_letter')}}</h3><br><br><br> --}}
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('let_content_tr') ? ' has-error' : ''}}">
                                        <label for="">Content <sup>*</sup></label>
                                        <textarea id="let_content_tr" name="let_content_tr" class="let_content_tr">
                                            {{ isset($let_cont['Trainee Appointment Letter']['content'])==1?$let_cont['Trainee Appointment Letter']['content']:'' }}
                                        </textarea>
                                        {!! $errors->first('let_content_tr', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-header with-border tab-pane fade" id="probation_box"  >
                <div class='box box-default'>  <br>
                    {{-- <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.prob_app_letter')}}</h3><br><br><br> --}}
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('let_content_pr') ? ' has-error' : ''}}">
                                        <label for="">Content <sup>*</sup></label>
                                        <textarea id="let_content_pr" name="let_content_pr" class="let_content_pr">
                                            {{ isset($let_cont['Probation Appointment Letter']['content'])==1?$let_cont['Probation Appointment Letter']['content']:'' }}
                                        </textarea>
                                        {!! $errors->first('let_content_pr', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-header with-border tab-pane fade" id="confirmation_box"  >
                <div class='box box-default'>  <br>
                    {{-- <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.conf_letter')}}</h3><br><br><br> --}}
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('let_content_co') ? ' has-error' : ''}}">
                                        <label for="">Content <sup>*</sup></label>
                                        <textarea id="let_content_co" name="let_content_co" class="let_content_co">
                                            {{ isset($let_cont['Confirmation Letter']['content'])==1?$let_cont['Confirmation Letter']['content']:'' }}
                                        </textarea>
                                        {!! $errors->first('let_content_co', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="box-header with-border tab-pane fade" id="fixed_term_box"  >
                <div class='box box-default'>  <br>
                    {{-- <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.fix_t_app_letter')}}</h3><br><br><br> --}}
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('let_content_fx') ? ' has-error' : ''}}">
                                        <label for="">Content <sup>*</sup></label>
                                        <textarea id="let_content_fx" name="let_content_fx" class="let_content_fx">
                                            {{ isset($let_cont['Fixed Term Appointment Letter']['content'])==1?$let_cont['Fixed Term Appointment Letter']['content']:'' }}
                                        </textarea>
                                        {!! $errors->first('let_content_fx', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="box-header with-border tab-pane fade" id="employee_relieving_box"  >
                <div class='box box-default'>  <br>
                    {{-- <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.relieve_letter')}}</h3><br><br><br> --}}
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('let_content_rl') ? ' has-error' : ''}}">
                                        <label for="">Content <sup>*</sup></label>
                                        <textarea id="let_content_rl" name="let_content_rl" class="let_content_rl">
                                            {{ isset($let_cont['Employee Relieving Letter']['content'])==1?$let_cont['Employee Relieving Letter']['content']:'' }}
                                        </textarea>
                                        {!! $errors->first('let_content_rl', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="box-header with-border tab-pane fade" id="hn_no_due_letter_box"  >
                <div class='box box-default'>  <br>
                    {{-- <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.no_dues_letter')}}</h3><br><br><br> --}}
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('let_content_ndh') ? ' has-error' : ''}}">
                                        <label for="">Content <sup>*</sup></label>
                                        <textarea id="let_content_ndh" name="let_content_ndh" class="let_content_ndh">
                                            {{ isset($let_cont['Hindi No Dues Letter']['content'])==1?$let_cont['Hindi No Dues Letter']['content']:'' }}
                                        </textarea>
                                        {!! $errors->first('let_content_ndh', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-header with-border tab-pane fade" id="en_no_due_letter_box"  >
                <div class='box box-default'>  <br>
                    {{-- <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.no_dues_letter')}}</h3><br><br><br> --}}
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->has('let_content_nde') ? ' has-error' : ''}}">
                                        <label for="">Content <sup>*</sup></label>
                                        <textarea id="let_content_nde" name="let_content_nde" class="let_content_nde">
                                            {{ isset($let_cont['English No Dues Letter']['content'])==1?$let_cont['English No Dues Letter']['content']:'' }}
                                        </textarea>
                                        {!! $errors->first('let_content_nde', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                            </div>
                        </div>
                    </div>
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
