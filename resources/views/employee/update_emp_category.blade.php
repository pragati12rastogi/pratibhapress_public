@extends($layout)
 
@section('title', __('Employee/category.title2'))
 
@section('user', Auth::user()->name)
 
@section('breadcrumb')
    <li><a href="/employee/profile/list"><i class=""></i> Employee List</a></li>
    <style>
        .nav1>li>a {
            position: relative;
            display: block;
            padding: 10px 34px;
            background-color: white;
            margin-left: 10px;
        }
        /* .nav1>li>a:hover {
            background-color:#87CEFA;
        
        } */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
    #active_cat > li{
        border:1px solid #8a8787;
    }
        </style>
@endsection

@section('js')
    <script src="/js/Employee/category.js"></script>
    <script>
        function offer() {
           
            $('.joining').datepicker("destroy");
            var date = new Date($('.offer_date').val().split('-').reverse().join('-'));
            var dd = date.getDate();
            var mm = date.getMonth() + 1;
            var yy = date.getFullYear();
            var day = dd + "-" + mm + "-" + yy;
            $('.joining').datepicker({
                autoclose: true,
                format: 'd-m-yyyy',
                startDate: date
            });
         }

    function sal_in_word(){
        var value = $(".prob_sal").val();
        var final = convertNumberToWords(value);
        $(".sal_word").val(final);
    }
       $("#active_cat li a").click(function(){
            var value =$(this).text();
               
            $(".cat_type").val(value);
       })
       function cat_val(){
            var value =$("#active_cat li.active a").text();   
            $(".cat_type").val(value);
       }
    
    $(document).ready(function(){
        $('#active_cat.nav-pills > li > a').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#active_cat a[href="' + activeTab + '"]').tab('show');
        }

        cat_val();
        
       
       @php if(isset($access_category['Offer Letter']['cat_date'])){ @endphp
           offer();
       @php } 
       if(isset($access_category['Probation Appointment Letter']['stipend'])){ @endphp
            sal_in_word();
       @php }
            if(isset($access_category['Offer Letter']['is_letter_issue'])){
                if($access_category['Offer Letter']['is_letter_issue']==1){
        @endphp
                $("#sup1").show();
                // $(".show_of").show();
                
        @php
                }
            }
            if(isset($access_category['Trainee Appointment Letter']['is_letter_issue'])){
                if($access_category['Trainee Appointment Letter']['is_letter_issue']==1){
        @endphp
                $("#sup2").show();
                // $(".show_tr").show();
        @php
                }
            }
            if(isset($access_category['Probation Appointment Letter']['is_letter_issue'])){
                if($access_category['Probation Appointment Letter']['is_letter_issue']==1){
        @endphp
                $("#sup3").show();
                // $(".show_pr").show();
        @php
                }
            }
            if(isset($access_category['Confirmation Letter']['is_letter_issue'])){
                if($access_category['Confirmation Letter']['is_letter_issue']==1){
        @endphp
                $("#sup4").show();
                // $(".show_co").show();
        @php
                }
            }
            if(isset($access_category['Fixed Term Appointment Letter']['is_letter_issue'])){
                if($access_category['Fixed Term Appointment Letter']['is_letter_issue']==1){
        @endphp
                $("#sup5").show();
                // $(".show_fx").show();
        @php
                }
            }
        @endphp
    })
    </script>
@endsection
@section('main_section')
    <section class="content">
                <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                </div>
        <!-- Default box -->
        <form action="/employee/category/update/{{$id}}" method="POST" id="emp_cat_from" enctype="multipart/form-data">
        @csrf
         @include('layouts.employee_tab')
              <br>
        <div class="box-header with-border">
            <div class='box box-default'>  <br>
                <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.title2')}}</h3><br><br><br>
                <div class="container-fluid wdt" style="padding-bottom:20px ">
                    <div class="row">
                        <div class="col-md-12 {{ $errors->has('prob_design') ? 'has-error' : ''}}">
                            <label>{{__('Employee/category.type')}}<sup>*</sup></label>
                                <ul class="nav nav-pills" id="active_cat">
                                    <li class="active" style="background-color: {{ isset($access_category['Offer Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#offer_letter_box">{{__('Employee/category.offer_letter')}}</a></li>
                                    <li style="background-color: {{ isset($access_category['Trainee Appointment Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#trainee_box">{{__('Employee/category.trainee_app_letter')}}</a></li>
                                    <li style="background-color: {{ isset($access_category['Probation Appointment Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#probation_box">{{__('Employee/category.prob_app_letter')}}</a></li>
                                    <li style="background-color: {{ isset($access_category['Confirmation Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#confirmation_box">{{__('Employee/category.conf_letter')}}</a></li>
                                    <li style="background-color: {{ isset($access_category['Fixed Term Appointment Letter'])==1?'#87CEFA':'' }}" >
                                    <a data-toggle="pill" href="#fixed_term_box">{{__('Employee/category.fix_t_app_letter')}}</a></li>
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
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.offer_letter')}}</h3><br><br><br>
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($access_category['Offer Letter']))
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Update Reason <sup>*</sup></label>
                                        <input type="text" name="update_reason_of" class="input-css update_reason_of" id="update_reason_of">
                                        {!! $errors->first('update_reason_of', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('offer_date') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/category.ofd')}} <sup>*</sup></label>
                                        <input type="text" name="offer_date" value="{{ isset($access_category['Offer Letter']['cat_date'])==1?date('d-m-Y',strtotime($access_category['Offer Letter']['cat_date'])):'' }}"
                                         class="datepicker1 input-css offer_date">
                                        {!! $errors->first('offer_date', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('designation') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/category.offer_desig')}} <sup>*</sup></label>
                                        <input type="text" name="designation" value="{{ isset($access_category['Offer Letter']['cat_designation'])==1?$access_category['Offer Letter']['cat_designation']:'' }}"
                                        class="designation input-css" >
                                        {!! $errors->first('designation', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('joining') ? 'has-error' : ''}}">
                                        <label for="">Joining Date <sup>*</sup></label>
                                        <input type="text" name="joining" value="{{ isset($access_category['Offer Letter']['joining'])==1?date('d-m-Y',strtotime($access_category['Offer Letter']['joining'])):'' }}"
                                        class="input-css joining" id="join" >
                                        {!! $errors->first('joining', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('letter_issue_of') ? 'has-error' : ''}}">
                                        <label for="">Appointment Letter Issue <sup>*</sup></label>
                                        <div class="radio">
                                            <label class="radio"><input type="radio" class="letter_issue_of" {{ isset($access_category['Offer Letter']['is_letter_issue'])==1 ? (($access_category['Offer Letter']['is_letter_issue']==1)?'checked=checked':'') :'' }}
                                            name="letter_issue_of" value="1" >Yes</label>
                                            <label class="radio"><input type="radio"class="letter_issue_of" {{ isset($access_category['Offer Letter']['is_letter_issue'])==1 ? (($access_category['Offer Letter']['is_letter_issue']==0)?'checked=checked':'') :'' }}
                                            name="letter_issue_of" value="0" >No</label>
                                        </div>
                                        {!! $errors->first('letter_issue_of', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('letter_upload_of') ? 'has-error' : ''}}">
                                        @if(isset($access_category['Offer Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Offer Letter']['apt_letter']))
                                                <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Offer Letter']['apt_letter']}}" data-toggle="modal" data-target="#myModal_of">See your Certificate</a>  
                                            @endif
                                        @endif
                                        <label for="">Appointment Letter Upload <sup id="sup1" style="display: none">*</sup></label>
                                        <input type="file" name="letter_upload_of" value="{{ isset($access_category['Offer Letter']['apt_letter']) ==1 ? $access_category['Offer Letter']['apt_letter'] : ''}}"
                                        accept="application/msword, application/pdf" class="letter_upload_of">
                                        {!! $errors->first('letter_upload_of', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <input type="text" name="old_file_of" value="{{ isset($access_category['Offer Letter']['apt_letter']) ==1 ? $access_category['Offer Letter']['apt_letter'] : ''}}" hidden>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 " >
                                        @if(isset($access_category['Offer Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Offer Letter']['apt_letter']))
                                                <!-- <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Offer Letter']['apt_letter']}}" download>
                                                    <input type="button" name="dwnld_btn_of" id="dwnld_btn_of" value="Download" class="btn btn-info dwnld_btn_of">
                                                </a> -->
                                            @endif
                                        @endif
                                    </div>
                                    <!-- <div class="col-md-6 show_of"> 
                                        <button type="submit" name="dwnld_frmt_of" id="dwnld_frmt_of" class="btn btn-success dwnld_frmt_of" formmethod="GET" formaction="/emp/download/template/pdf/{{$id}}/Offer Letter">Download Format</button>
                                        
                                    </div> -->
                                    
                                </div><br><br>
                                
                            </div>
                        </div>
                        <div id="myModal_of" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Offer Letter</h4>
                                    </div>
                                    <div class="modal-body">
                                        @if(isset($access_category['Offer Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Offer Letter']['apt_letter']))
                                                <embed src="{{ asset('upload/appointment_letter') }}/{{$access_category['Offer Letter']['apt_letter']}}" frameborder="0" width="100%" height="400px">
                                            @endif
                                        @endif
                
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="box-header with-border tab-pane fade" id="trainee_box"  >
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.trainee_app_letter')}}</h3><br><br><br>
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($access_category['Trainee Appointment Letter']))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Update Reason <sup>*</sup></label>
                                            <input type="text" name="update_reason_tr" required="" class="input-css update_reason_tr" id="update_reason_tr">
                                            {!! $errors->first('update_reason_tr', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br><br>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('t_app_date') ? 'has-error' : ''}}">
                                        <label for="">Trainee Application Date <sup>*</sup></label>
                                        <input type="text" name="t_app_date" value="{{ isset($access_category['Trainee Appointment Letter']['cat_date'])==1?date('d-m-Y',strtotime($access_category['Trainee Appointment Letter']['cat_date'])):'' }}"
                                        class="datepicker1 input-css t_app_date">
                                        {!! $errors->first('t_app_date', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('stipend') ? 'has-error' : ''}}">
                                        <label for="">Stipend <sup>*</sup></label>
                                        <input type='number' class="input-css stipend" value="{{ isset($access_category['Trainee Appointment Letter']['stipend'])==1?$access_category['Trainee Appointment Letter']['stipend']:'' }}"
                                        name="stipend" min="0" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">
                                        {!! $errors->first('stipend', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                
                                <div class="row">
                                    
                                    <div class="col-md-6 {{ $errors->has('letter_issue_tr') ? 'has-error' : ''}}">
                                        <label for="">Appointment Letter Issue <sup>*</sup></label>
                                        <div class="radio">
                                            <label class="radio"><input type="radio" {{ isset($access_category['Trainee Appointment Letter']['is_letter_issue'])==1 ? (($access_category['Trainee Appointment Letter']['is_letter_issue']==1)?'checked=checked':'') :'' }}
                                            class="letter_issue_tr" name="letter_issue_tr" value="1" >Yes</label>
                                            <label class="radio"><input type="radio" {{ isset($access_category['Trainee Appointment Letter']['is_letter_issue'])==1 ? (($access_category['Trainee Appointment Letter']['is_letter_issue']==0)?'checked=checked':'') :'' }}
                                            class="letter_issue_tr" name="letter_issue_tr" value="0" >No</label>
                                        </div>
                                        {!! $errors->first('letter_issue_tr', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('letter_upload_tr') ? 'has-error' : ''}}">
                                        @if(isset($access_category['Trainee Appointment Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Trainee Appointment Letter']['apt_letter']))
                                                <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Trainee Appointment Letter']['apt_letter']}}" data-toggle="modal" data-target="#myModal_tr">See your Certificate</a>  
                                            @endif
                                        @endif
                                        <label for="">Appointment Letter Upload <sup id="sup2" style="display: none">*</sup></label>
                                        <input type="file" name="letter_upload_tr" value="{{ isset($access_category['Trainee Appointment Letter']['apt_letter']) ==1 ? $access_category['Trainee Appointment Letter']['apt_letter'] : ''}}"
                                        accept="application/msword, application/pdf" class="letter_upload_tr" >
                                        {!! $errors->first('letter_upload_tr', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <input type="text" name="old_file_tr" value="{{ isset($access_category['Trainee Appointment Letter']['apt_letter']) ==1 ? $access_category['Trainee Appointment Letter']['apt_letter'] : ''}}" hidden>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 " >
                                        @if(isset($access_category['Trainee Appointment Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Trainee Appointment Letter']['apt_letter']))
                                                <!-- <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Trainee Appointment Letter']['apt_letter']}}" download>
                                                    <input type="button" name="dwnld_btn_tr" id="dwnld_btn_tr" value="Download" class="btn btn-info dwnld_btn_tr">
                                                </a> -->
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6 show_tr">
                                        <button type="submit" name="dwnld_frmt_tr" id="dwnld_frmt_tr" class="btn btn-success dwnld_frmt_tr" formmethod="GET" formaction="/emp/download/template/pdf/{{$id}}/Trainee Appointment Letter">Download Format</button>
                                        
                                        {!! $errors->first('dwnld_frmt_tr', '<p class="help-block">:message</p>') !!}

                                    </div>
                                    
                                </div><br><br>
                                
                            </div>
                        </div>
                        <div id="myModal_tr" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Trainee Appointment Letter</h4>
                                    </div>
                                    <div class="modal-body">
                                        @if(isset($access_category['Trainee Appointment Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Trainee Appointment Letter']['apt_letter']))
                                                <embed src="{{ asset('upload/appointment_letter') }}/{{$access_category['Trainee Appointment Letter']['apt_letter']}}" frameborder="0" width="100%" height="400px">
                                            @endif
                                        @endif
                
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-header with-border tab-pane fade" id="probation_box"  >
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.prob_app_letter')}}</h3><br><br><br>
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($access_category['Probation Appointment Letter']))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Update Reason <sup>*</sup></label>
                                            <input type="text" name="update_reason_pr" class="input-css update_reason_pr" id="update_reason_pr">
                                            {!! $errors->first('update_reason_pr', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br><br>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('prob_design') ? 'has-error' : ''}}">
                                        <label for="">Probation Designation <sup>*</sup></label>
                                        <input type="text" name="prob_design" value="{{ isset($access_category['Probation Appointment Letter']['cat_designation'])==1?$access_category['Probation Appointment Letter']['cat_designation']:'' }}"
                                        class="input-css prob_design">
                                        {!! $errors->first('prob_design', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('prob_sal') ? 'has-error' : ''}}">
                                        <label for="">Probation Salary <sup>*</sup></label>
                                        <input type="number" name="prob_sal" value="{{ isset($access_category['Probation Appointment Letter']['stipend'])==1?$access_category['Probation Appointment Letter']['stipend']:'' }}"
                                        class="prob_sal input-css" min="0" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">
                                        {!! $errors->first('prob_sal', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('prob_date') ? 'has-error' : ''}}">
                                        <label for="">Probation Date <sup>*</sup></label>
                                        <input type="text" name="prob_date" value="{{ isset($access_category['Probation Appointment Letter']['cat_date'])==1?date('d-m-Y',strtotime($access_category['Probation Appointment Letter']['cat_date'])):'' }}"
                                        class="datepicker1 input-css prob_date">
                                        {!! $errors->first('prob_date', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('sal_word') ? 'has-error' : ''}}">
                                        <label for="">Salary In Words <sup>*</sup></label>
                                        <input type="text" name="sal_word" class="input-css sal_word" readonly>
                                        {!! $errors->first('sal_word', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('letter_issue_pr') ? 'has-error' : ''}}">
                                        <label for="">Appointment Letter Issue <sup>*</sup></label>
                                        <div class="radio">
                                            <label class="radio"><input type="radio" {{ isset($access_category['Probation Appointment Letter']['is_letter_issue'])==1 ? (($access_category['Probation Appointment Letter']['is_letter_issue']==1)?'checked=checked':'') :'' }}
                                            class="letter_issue_pr" name="letter_issue_pr" value="1" >Yes</label>
                                            <label class="radio"><input type="radio" {{ isset($access_category['Probation Appointment Letter']['is_letter_issue'])==1 ? (($access_category['Probation Appointment Letter']['is_letter_issue']==0)?'checked=checked':'') :'' }}
                                            class="letter_issue_pr" name="letter_issue_pr" value="0" >No</label>
                                        </div>
                                        {!! $errors->first('letter_issue_pr', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('letter_upload_pr') ? 'has-error' : ''}}">
                                        @if(isset($access_category['Probation Appointment Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Probation Appointment Letter']['apt_letter']))
                                                <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Probation Appointment Letter']['apt_letter']}}" data-toggle="modal" data-target="#myModal_pr">See your Certificate</a>  
                                            @endif
                                        @endif
                                        <label for="">Appointment Letter Upload <sup id="sup3" style="display: none">*</sup></label>
                                        <input type="file" name="letter_upload_pr" value="{{ isset($access_category['Probation Appointment Letter']['apt_letter']) ==1 ? $access_category['Probation Appointment Letter']['apt_letter'] : ''}}"
                                        accept="application/pdf" class="letter_upload_pr" >
                                        {!! $errors->first('letter_upload_pr', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <input type="text" name="old_file_pr" value="{{ isset($access_category['Probation Appointment Letter']['apt_letter']) ==1 ? $access_category['Probation Appointment Letter']['apt_letter'] : ''}}" hidden>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6" >
                                        @if(isset($access_category['Probation Appointment Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Probation Appointment Letter']['apt_letter']))
                                               <!--  <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Probation Appointment Letter']['apt_letter']}}" download>
                                                    <input type="button" name="dwnld_btn_pr" id="dwnld_btn_pr" value="Download" class="btn btn-info dwnld_btn_pr">
                                                </a> -->
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6 show_pr">
                                        <button type="submit" name="dwnld_frmt_pr" id="dwnld_frmt_pr" class="btn btn-success dwnld_frmt_pr" formmethod="GET" formaction="/emp/download/template/pdf/{{$id}}/Probation Appointment Letter">Download Format</button>
                                        
                                        {!! $errors->first('dwnld_frmt_pr', '<p class="help-block">:message</p>') !!}
                                    
                                    </div>
                                   
                                </div><br><br>
                                
                            </div>
                        </div>
                        <div id="myModal_pr" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Probation Appointment Letter</h4>
                                    </div>
                                    <div class="modal-body">
                                        @if(isset($access_category['Probation Appointment Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Probation Appointment Letter']['apt_letter']))
                                                <embed src="{{ asset('upload/appointment_letter') }}/{{$access_category['Probation Appointment Letter']['apt_letter']}}" frameborder="0" width="100%" height="400px">
                                            @endif
                                        @endif
                
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-header with-border tab-pane fade" id="confirmation_box"  >
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.conf_letter')}}</h3><br><br><br>
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($access_category['Confirmation Letter']))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Update Reason <sup>*</sup></label>
                                            <input type="text" name="update_reason_co" class="input-css update_reason_co" id="update_reason_co">
                                            {!! $errors->first('update_reason_co', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br><br>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('conf_desig') ? 'has-error' : ''}}">
                                        <label for="">Confirm Designation <sup>*</sup></label>
                                        <input type="text" name="conf_desig" value="{{ isset($access_category['Confirmation Letter']['cat_designation'])==1?$access_category['Confirmation Letter']['cat_designation']:'' }}"
                                        class="input-css conf_desig">
                                        {!! $errors->first('conf_desig', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('conf_date') ? 'has-error' : ''}}">
                                        <label for="">Effective Date <sup>*</sup></label>
                                        <input type="text" name="conf_date" value="{{ isset($access_category['Confirmation Letter']['cat_date'])==1?date('d-m-Y',strtotime($access_category['Confirmation Letter']['cat_date'])):'' }}"
                                        class="datepicker1 conf_date input-css" >
                                        {!! $errors->first('conf_date', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('letter_issue_co') ? 'has-error' : ''}}">
                                        <label for="">Appointment Letter Issue <sup>*</sup></label>
                                        <div class="radio">
                                            <label class="radio"><input type="radio" {{ isset($access_category['Confirmation Letter']['is_letter_issue'])==1 ? (($access_category['Confirmation Letter']['is_letter_issue']==1)?'checked=checked':'') :'' }}
                                            class="letter_issue_co" name="letter_issue_co" value="1" >Yes</label>
                                            <label class="radio"><input type="radio" {{ isset($access_category['Confirmation Letter']['is_letter_issue'])==1 ? (($access_category['Confirmation Letter']['is_letter_issue']==0)?'checked=checked':'') :'' }}
                                            class="letter_issue_co" name="letter_issue_co" value="0" >No</label>
                                        </div>
                                        {!! $errors->first('letter_issue_co', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('letter_upload_co') ? 'has-error' : ''}}">
                                        @if(isset($access_category['Confirmation Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Confirmation Letter']['apt_letter']))
                                                <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Confirmation Letter']['apt_letter']}}" data-toggle="modal" data-target="#myModal_co">See your Certificate</a>  
                                            @endif
                                        @endif
                                        <label for="">Appointment Letter Upload <sup id="sup4" style="display: none">*</sup></label>
                                        <input type="file" name="letter_upload_co" value="{{ isset($access_category['Confirmation Letter']['apt_letter']) ==1 ? $access_category['Confirmation Letter']['apt_letter'] : ''}}"
                                        accept="application/pdf" class="letter_upload_co" >
                                        {!! $errors->first('letter_upload_co', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <input type="text" name="old_file_co" value="{{ isset($access_category['Confirmation Letter']['apt_letter']) ==1 ? $access_category['Confirmation Letter']['apt_letter'] : ''}}" hidden>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 " >
                                        @if(isset($access_category['Confirmation Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Confirmation Letter']['apt_letter']))
                                                <!-- <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Confirmation Letter']['apt_letter']}}" download>
                                                    <input type="button" name="dwnld_btn_co" id="dwnld_btn_co" value="Download" class="btn btn-info dwnld_btn_co">
                                                </a> -->
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6 show_co" >
                                        <!-- style="display:none"> -->
                                        <button type="submit" name="dwnld_frmt_co" id="dwnld_frmt_co" class="btn btn-success dwnld_frmt_co" formmethod="GET" formaction="/emp/download/template/pdf/{{$id}}/Confirmation Letter">Download Format</button>

                                        {!! $errors->first('dwnld_frmt_co', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    
                                </div><br><br>
                               
                            </div>
                        </div>
                        <div id="myModal_co" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Confirmation Letter</h4>
                                    </div>
                                    <div class="modal-body">
                                        @if(isset($access_category['Confirmation Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Confirmation Letter']['apt_letter']))
                                                <embed src="{{ asset('upload/appointment_letter') }}/{{$access_category['Confirmation Letter']['apt_letter']}}" frameborder="0" width="100%" height="400px">
                                            @endif
                                        @endif
                
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="box-header with-border tab-pane fade" id="fixed_term_box"  >
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/category.fix_t_app_letter')}}</h3><br><br><br>
                    <div class="container-fluid wdt" style="padding-bottom:20px ">
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($access_category['Fixed Term Appointment Letter']))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Update Reason <sup>*</sup></label>
                                            <input type="text" name="update_reason_fx" class="input-css update_reason_fx" id="update_reason_fx">
                                            {!! $errors->first('update_reason_fx', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div><br><br>
                                @endif
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('fx_date') ? 'has-error' : ''}}">
                                        <label for="">Fixed Application Date <sup>*</sup></label>
                                        <input type="text" name="fx_date" value="{{ isset($access_category['Fixed Term Appointment Letter']['cat_date'])==1?date('d-m-Y',strtotime($access_category['Fixed Term Appointment Letter']['cat_date'])):'' }}"
                                        class="datepicker1 input-css fx_date">
                                        {!! $errors->first('fx_date', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('fx_desig') ? 'has-error' : ''}}">
                                        <label for="">Fixed Designation <sup>*</sup></label>
                                        <input type="text" name="fx_desig" value="{{ isset($access_category['Fixed Term Appointment Letter']['cat_designation'])==1?$access_category['Fixed Term Appointment Letter']['cat_designation']:'' }}"
                                        class="fx_desig input-css" >
                                        {!! $errors->first('fx_desig', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('fx_per_date') ? 'has-error' : ''}}">
                                        <label for="">Fixed Period Date <sup>*</sup></label>
                                        <input type="text" name="fx_per_date" value="{{ isset($access_category['Fixed Term Appointment Letter']['period_date'])==1?date('d-m-Y',strtotime($access_category['Fixed Term Appointment Letter']['period_date'])):'' }}"
                                        class="datepicker1 input-css fx_per_date" id="fx_per_date">
                                        {!! $errors->first('fx_per_date', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('fx_date_six') ? 'has-error' : ''}}">
                                        <label for="">Fixed Period Date + 6 Months <sup>*</sup></label>
                                        <input type="text" name="fx_date_six" value="{{ isset($access_category['Fixed Term Appointment Letter']['p_six_date'])==1?date('d-m-Y',strtotime($access_category['Fixed Term Appointment Letter']['p_six_date'])):'' }}"
                                        id="fx_date_six" class=" input-css fx_date_six" readonly style="pointer-events: none;">
                                        {!! $errors->first('fx_date_six', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('fx_sal') ? 'has-error' : ''}}">
                                        <label for="">Fixed Salary <sup>*</sup></label>
                                        <input type="number" name="fx_sal" value="{{ isset($access_category['Fixed Term Appointment Letter']['stipend'])==1?$access_category['Fixed Term Appointment Letter']['stipend']:'' }}"
                                        class="input-css fx_sal" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57">
                                        {!! $errors->first('fx_sal', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 {{ $errors->has('letter_issue_fx') ? 'has-error' : ''}}">
                                        <label for="">Appointment Letter Issue <sup>*</sup></label>
                                        <div class="radio">
                                            <label class="radio"><input type="radio" {{ isset($access_category['Fixed Term Appointment Letter']['is_letter_issue'])==1 ? (($access_category['Fixed Term Appointment Letter']['is_letter_issue']==1)?'checked=checked':'') :'' }}
                                            class="letter_issue_fx" name="letter_issue_fx" value="1" >Yes</label>
                                            <label class="radio"><input type="radio" {{ isset($access_category['Fixed Term Appointment Letter']['is_letter_issue'])==1 ? (($access_category['Fixed Term Appointment Letter']['is_letter_issue']==0)?'checked=checked':'') :'' }}
                                            class="letter_issue_fx" name="letter_issue_fx" value="0" >No</label>
                                        </div>
                                        {!! $errors->first('letter_issue_fx', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <div class="col-md-6 {{ $errors->has('letter_upload_fx') ? 'has-error' : ''}}">
                                        @if(isset($access_category['Fixed Term Appointment Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Fixed Term Appointment Letter']['apt_letter']))
                                                <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Fixed Term Appointment Letter']['apt_letter']}}" data-toggle="modal" data-target="#myModal_fx">See your Certificate</a>  
                                            @endif
                                        @endif
                                        <label for="">Appointment Letter Upload <sup id="sup5" style="display: none">*</sup></label>
                                        <input type="file" name="letter_upload_fx" value="{{ isset($access_category['Fixed Term Appointment Letter']['apt_letter']) ==1 ? $access_category['Fixed Term Appointment Letter']['apt_letter'] : ''}}"
                                        accept="application/msword, application/pdf" class="letter_upload_fx" >
                                        {!! $errors->first('letter_upload_fx', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    <input type="text" name="old_file_fx" value="{{ isset($access_category['Fixed Term Appointment Letter']['apt_letter']) ==1 ? $access_category['Fixed Term Appointment Letter']['apt_letter'] : ''}}" hidden>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-6 " >
                                        @if(isset($access_category['Fixed Term Appointment Letter']['apt_letter']))
                                            @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Fixed Term Appointment Letter']['apt_letter']))
                                                <!-- <a href="{{ asset('upload/appointment_letter') }}/{{$access_category['Fixed Term Appointment Letter']['apt_letter']}}" download>
                                                    <input type="button" name="dwnld_btn_fx" id="dwnld_btn_fx" value="Download" class="btn btn-info dwnld_btn_fx">
                                                </a> -->
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-md-6 show_fx" >
                                    <!-- style="display:none"> -->
                                    <!-- <span style="color:red">Please submit form data before downloading format</span><br> -->
                                        <button type="submit" name="dwnld_frmt_fx" id="dwnld_frmt_fx" class="btn btn-success dwnld_frmt_fx" formmethod="GET" formaction="/emp/download/template/pdf/{{$id}}/Fixed Term Appointment Letter">Download Format</button>

                                        {!! $errors->first('dwnld_frmt_fx', '<p class="help-block">:message</p>') !!}
                                    </div>
                                    
                                </div><br><br>
                                
                            </div>
                        </div>
                    </div>
                    <div id="myModal_fx" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-lg">
            
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Fixed Term Appointment Letter</h4>
                                </div>
                                <div class="modal-body">
                                    @if(isset($access_category['Fixed Term Appointment Letter']['apt_letter']))
                                        @if (file_exists(public_path().'/upload/appointment_letter/'.$access_category['Fixed Term Appointment Letter']['apt_letter']))
                                            <embed src="{{ asset('upload/appointment_letter') }}/{{$access_category['Fixed Term Appointment Letter']['apt_letter']}}" frameborder="0" width="100%" height="400px">
                                        @endif
                                    @endif
            
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
            
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
 

