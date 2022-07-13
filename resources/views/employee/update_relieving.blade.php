@extends($layout)

@section('title', __('Employee/relieving.title'))

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
        </style>
@endsection
@section('js')
<script src="/js/Employee/relieving.js"></script>
<script>
      var leaving_date=null;
        var fnf_date=null;
        var resignation_d = null;
        $(document).ready(function(){
           var radio= $("input[name='fnf_complete']:checked").val();
            if(radio == "yes"){
                $(".show_of").show();
            }else{
                $(".show_of").hide();
            }
            
            $('#resignation_d').datepicker()
                .on('changeDate', function(ev){
                    resignation_d=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
                    if(leaving_date!=null&&leaving_date!='undefined'){
                        if(resignation_d <= leaving_date){
                                  $('#res_err_date').hide();
                        }else{
                             $('#res_err_date').html('Please select post resignation date than leaving.').show();
                                $('#leaving_date').val('');
                        }
                    }
                });
                $('#leaving_date').datepicker()
                .on('changeDate', function(ev){
                    leaving_date=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
                    if(fnf_date!=null&&fnf_date!='undefined'){
                        if(leaving_date <= fnf_date){
                                  $('#validate_date').hide();
                        }else{
                             $('#validate_date').html('Please select equal to leaving date or more');
                                $('#fnf_date').val('');
                        }
                    }
                    if(resignation_d!=null&&resignation_d!='undefined'){
                        if(leaving_date >= resignation_d){
                                  $('#res_err_date').hide();
                        }else{
                             $('#res_err_date').html('Please select post resignation date than leaving.');
                                $('#resignation_d').val('');
                        }
                    }
                });
            $("#fnf_date").datepicker()
                .on("changeDate", function(ev){
                    fnf_date=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
                    if(leaving_date!=null&&leaving_date!='undefined'){
                        if(leaving_date <= fnf_date){
                             $('#validate_date').hide();
                        }else{
                               $('#validate_date').html('Please select equal to leaving date or more').show();
                                $('#fnf_date').val('');
                        }
                    }
                });

        });
// jQuery('#fnf_date').on('change', function(){
//     var leaving_date = new Date($('#leaving_date').val()).getTime();
//     var fnf_date = new Date($('#fnf_date').val()).getTime();
//     if((leaving_date == fnf_date) || (leaving_date < fnf_date)){
//         $('#validate_date').hide();
//     }else{
//         $('#validate_date').html('Please select equal to leaving date or more');
//         $('#fnf_date').val('');

//     }
// });

$(".fnf_complete").change(function(){
    
    if($("input[name='fnf_complete']:checked").val() == "yes"){
        $(".show_of").show();
    }else{
        $(".show_of").hide();
    }
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
     <form action="/employee/relieving/update/{{$id}}" method="POST" id="form" enctype="multipart/form-data">
        @csrf
         @include('layouts.employee_tab')
      <br>
            
        <div class="box-header with-border">
            <div class='box box-default'>  <br> 
                <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/relieving.mytitle')}}</h3><br><br><br>
                <div class="container-fluid wdt">
                   @if(isset($relieving))
                    <div class="row">
                        <div class="col-md-3 ">
                            <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                            <input type="text" name="update_reason" class="input-css" id="update_reason">
                            {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                        </div><!--col-md-4-->
                    </div>
                    <br><br>
                    @endif 
                </div>
            </div> 
        </div>
        <div class='box box-default'>  <br>
            <div class="container-fluid wdt"> 
                <div class="row">
                    @if(count($assets))
                        @if(isset($relieving))
                          <?php $rev_assets = $relieving['leaving_assets'];
                                $rev_ass = explode(',', $rev_assets);
                                 ?>
                            <div class="col-md-6 {{ $errors->has('assets') ? 'has-error' : ''}}">
                                <label for="">{{__('Employee/relieving.assets')}} </label>

                                @foreach($assets as $ass)
                                @if(in_array($ass['asset_id'],$rev_ass))
                                 <input type="checkbox" name="assets[]" id="assets" value="{{$ass['asset_id']}}" style="margin-left: 10px;" checked=""> {{$ass['name']}}
                                 @else
                                <input type="checkbox" name="assets[]" id="assets" value="{{$ass['asset_id']}}" style="margin-left: 10px;" > {{$ass['name']}}
                                 @endif
                                @endforeach
                                {!! $errors->first('assets', '<p class="help-block">:message</p>') !!}
                            </div>
                            @else
                            <div class="col-md-6 {{ $errors->has('assets') ? 'has-error' : ''}}">
                                <label for="">{{__('Employee/relieving.assets')}} </label>
                                @if(count($assets)>0)
                                    @foreach($assets as $ass)
                                    <input type="checkbox" name="assets[]" id="assets" value="{{$ass['asset_id']}}" style="margin-left: 10px;" > {{$ass['name']}}
                                    @endforeach
                                    {!! $errors->first('assets', '<p class="help-block">:message</p>') !!}
                                @else
                                    <span style="color: red;"> No Asset Alloted </span>
                                @endif
                                
                            </div>
                        @endif
                    @endif
                        <div class="col-md-6 {{ $errors->has('resignation_d') ? 'has-error' : ''}}">
                            <label for="">Resignation Date<sup>*</sup></label>
                        <input type="text" autocomplete="off" name="resignation_d" id="resignation_d" value="{{isset($relieving)==1?(CustomHelpers::showDate($relieving['resignation_date'],'d-m-Y')):''}}" class="datepicker1 input-css" >
                        <span id="res_err_date" style="color: red;"></span>
                            {!! $errors->first('resignation_d', '<p class="help-block">:message</p>') !!}
                        </div>
                        
                </div><br><br>
                <div class="row">
                        <div class="col-md-6 {{ $errors->has('leaving_date') ? 'has-error' : ''}}">
                            <label for="">{{__('Employee/relieving.date')}} <sup>*</sup></label>
                        <input type="text" autocomplete="off" name="leaving_date" id="leaving_date" value="{{isset($relieving)==1?(CustomHelpers::showDate($relieving['leaving_date'],'d-m-Y')):''}}" class="datepicker1 input-css" >
                            {!! $errors->first('leaving_date', '<p class="help-block">:message</p>') !!}
                        </div>
                </div><br><br>
                <div class="row">
                        <div class="col-md-6 {{ $errors->has('fnf_complete') ? 'has-error' : ''}}">
                            <label for="">{{__('Employee/relieving.fnf_complete')}} <sup>*</sup></label>
                            <input type="radio" class="fnf_complete" name="fnf_complete" value="yes" <?php if(isset($relieving)){ if ($relieving['fnf_complete'] == 'yes') { echo "checked";
                            }}?> > Yes 
                            <input type="radio" class="fnf_complete" name="fnf_complete" value="no" <?php if(isset($relieving)){ if ($relieving['fnf_complete'] == 'no') { echo "checked";
                            }}?> style="margin-left: 15px;" > No
                            {!! $errors->first('fnf_complete', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('fnf_date') ? 'has-error' : ''}}">
                            <label for="">{{__('Employee/relieving.fnf_date')}} <sup>*</sup></label>
                            <input type="text" name="fnf_date" value="<?php if(isset($relieving)){
                                if($relieving['fnf_date'] != '1970-01-01'){
                                    echo(CustomHelpers::showDate($relieving['fnf_date'],'d-m-Y'));
                                    }else{
                                }
                                }?>" id="fnf_date" class="fnf_date datepicker1 input-css" >
                            <span id="validate_date" style="color: red;"></span>
                            {!! $errors->first('fnf_date', '<p class="help-block">:message</p>') !!}
                        </div>
                </div><br><br>
                <div class="row">
                        <div class="col-md-6 {{ $errors->has('pf_date') ? 'has-error' : ''}}">
                            @if(isset($relieving))
                                 @if (file_exists(public_path().'/upload/employee/'.$id.'/relieving/' .$relieving['certificate_file'] ))
                                 <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/relieving/'.$relieving['certificate_file'], PATHINFO_EXTENSION); ?>
                                    @if ($ext == 'pdf')
                                     <a href="{{ asset('upload/employee') }}/{{$id}}/relieving/{{$relieving['certificate_file']}}" target="_blank">See your Certificate</a>
                                    @else
                                         <img src="{{ asset('upload/employee') }}/{{$id}}/relieving/{{$relieving['certificate_file']}}" height="50" width="100">
                                   @endif
                                @endif
                            @endif
                            <label for="">{{__('Employee/relieving.certificate')}}</label>
                            <input type="file" name="certificate_file" value="{{isset($relieving)==1?($relieving['certificate_file']):''}}" id="certificate_file" class="certificate_file input-css">
                            {!! $errors->first('certificate_file', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('signed_copy_file') ? 'has-error' : ''}}">
                            @if(isset($relieving))
                                @if (file_exists(public_path().'/upload/employee/'.$id.'/relieving/' .$relieving['signed_copy_file'] ))
                                    <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/relieving/'.$relieving['signed_copy_file'], PATHINFO_EXTENSION); ?>
                                @if ($ext == 'pdf')
                                 <a href="{{ asset('upload/employee') }}/{{$id}}/relieving/{{$relieving['signed_copy_file']}}" target="_blank">See your signed copy</a>
                                @else
                                     <img src="{{ asset('upload/employee') }}/{{$id}}/relieving/{{$relieving['signed_copy_file']}}" height="50" width="100">
                               @endif
                                
                                @endif
                            @endif

                            <label for="">{{__('Employee/relieving.sign')}}</label>
                            <input type="file" name="signed_copy_file" value="{{isset($relieving)==1?($relieving['signed_copy_file']):''}}" id="signed_copy_file" class="signed_copy_file input-css">
                            {!! $errors->first('signed_copy_file', '<p class="help-block">:message</p>') !!}
                        </div>
                </div><br><br>
                <div class="row">
                        <div class="col-md-6 {{ $errors->has('resignation_latter_file') ? 'has-error' : ''}}">
                            @if(isset($relieving))
                                @if (file_exists(public_path().'/upload/employee/'.$id.'/relieving/' .$relieving['resignation_latter_file'] ))
                                 <?php $ext = pathinfo(storage_path().'/upload/employee/'.$id.'/relieving/'.$relieving['resignation_latter_file'], PATHINFO_EXTENSION); ?>
                                @if ($ext == 'pdf')
                                 <a href="{{ asset('upload/employee') }}/{{$id}}/relieving/{{$relieving['resignation_latter_file']}}" target="_blank">See your resignation latter</a>
                                @else
                                <img src="{{ asset('upload/employee') }}/{{$id}}/relieving/{{$relieving['resignation_latter_file']}}" height="50" width="100">
                               @endif
                                @endif
                            @endif
                            <label for="">{{__('Employee/relieving.reg_latter')}}</label>
                            <input type="file" name="resignation_latter_file" value="{{isset($relieving)==1?($relieving['resignation_latter_file']):''}}" id="resignation_latter_file" class="resignation_latter_file input-css">
                            {!! $errors->first('resignation_latter_file', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 show_of" style="display:none"> 
                            <!-- style="display:none" -->
                            <span style="color:red">Please fill required data before dowloading Formats </span><br>
                            <button type="submit" name="dwnld_frmt_ndh" id="dwnld_frmt_ndh" class="btn btn-success dwnld_frmt_ndh" formmethod="GET" formaction="/emp/relieving/pdf/template/{{$id}}/Hindi No Dues Letter">Hindi No Dues</button>
                            <button type="submit" name="dwnld_frmt_end" id="dwnld_frmt_end" class="btn btn-success dwnld_frmt_end" formmethod="GET" formaction="/emp/relieving/pdf/template/{{$id}}/English No Dues Letter">English No Dues</button>
                            <button type="submit" name="dwnld_frmt_rl" id="dwnld_frmt_rl" class="btn btn-success dwnld_frmt_rl" formmethod="GET" formaction="/emp/relieving/pdf/template/{{$id}}/Employee Relieving Letter">Relieving Format</button>
                        </div>
                </div><br><br>
                <div class="row">
                    <div class="col-md-10 {{ $errors->has('reason') ? 'has-error' : ''}}">
                        <label for="">{{__('Employee/relieving.reason')}} <sup>*</sup></label>
                        <textarea  name="reason" id="reason" class="form-control" >{{isset($relieving)==1?($relieving['leaving_reason']):''}}</textarea>
                        {!! $errors->first('reason', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                                               
            </div>
        <div class="row">
            <div class="col-md-12">
                <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
            </div>
        </div><br><br>
    </form>
      
    </section>
@endsection
