@extends($layout)

@section('title', __('Employee/profile.title'))

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
        </style>
@endsection
@section('js')
<script src="/js/Employee/profile.js"></script>
<script>
  var shift_from = @php echo json_encode($shift_from) ;@endphp;
  var shift_to = @php echo json_encode($shift_to) ;@endphp;
  
  
  if (shift_from) {
    var hours = shift_from.split(':').shift();
    var minuts = shift_from.split(":")[1];
    var minuts = minuts.replace(/ /g,'');
    if ( minuts.indexOf("AM") > -1 ){
      var avoid = "AM";
    }else{
      var avoid = "PM";
    }
    var minuts = minuts.replace(avoid,'');
  }

  $('.from').on('click' , function (){
        $('.from').wickedpicker({
            now:hours + ':' + minuts,
  });
});


  if (shift_to) {
    var tohours = shift_to.split(':').shift();
    var tominuts = shift_to.split(":")[1];
    var tominuts = tominuts.replace(/ /g,'');
    if ( tominuts.indexOf("AM") > -1 ){
      var avoid = "AM";
    }else{
      var avoid = "PM";
    }
    var tominuts = tominuts.replace(avoid,'');
  }
 

$('.to').on('click' , function (){
        $('.to').wickedpicker({
        now:tohours + ':' + tominuts,
  });
});

$('.overtime').on('click' , function (){
        $('.overtime').wickedpicker({
        // setTime: null
  });
});

// var ast="{{$employee->assets}}";
// var assets=ast.split(',');
// $('.assets').val(assets).select2();

</script>
@endsection
@section('main_section')
    <section class="content">
                <div id="app">
                                @include('sections.flash-message')
                                @yield('content')
                            </div>
        <!-- Default box -->
                        <form action="/employee/profile/update/{{$id}}" method="POST" id="form">
        @csrf
         @include('layouts.employee_tab')
              <br>
              @php
              $shift=explode(' to ',$employee->shifting_timing);
          @endphp
        <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/profile.mytitle')}}</h3><br><br><br>
                    <div class="container-fluid wdt">
                    <div class="row">
                        <div class="col-md-3 ">
                            <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                            <input type="text" name="update_reason" required="" class="input-css" id="update_reason">
                            {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                        </div><!--col-md-4-->
                    </div>
                    <br><br>
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('emp_name') ? 'has-error' : ''}}">
                                 <label for="">{{__('Employee/profile.name')}} <sup>*</sup></label>
                                <input type="text" name="emp_name" id="emp_name" class="emp_name input-css" value="{{ $employee->name }}">
                                {!! $errors->first('emp_name', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('father') ? 'has-error' : ''}}">
                                    <label for="">{{__('Employee/profile.father')}} <sup>*</sup></label>
                                   <input type="text" name="father" id="father" class="father input-css" value="{{ $employee-> father_name}}">
                                   {!! $errors->first('father', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('emp_email') ? 'has-error' : ''}}">
                                     <label for="">Email <sup>*</sup></label>
                                    <input type="email" name="emp_email" id="emp_email" class="emp_email input-css" value="{{ $employee-> email}}" >
                                    {!! $errors->first('emp_email', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('emp_aadhar') ? 'has-error' : ''}}">
                                     <label for="">Aadhar No.<sup>*</sup></label>
                                    <input type="text" name="emp_aadhar" id="emp_aadhar" class="emp_aadhar input-css" value="{{ $employee-> aadhar}}" required>
                                    {!! $errors->first('emp_aadhar', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('dob') ? 'has-error' : ''}}">
                                     <label for="">{{__('Employee/profile.dob')}} <sup>*</sup></label>
                                    <input type="text" autocomplete="off" name="dob" id="dob" class="dob input-css datepicker1" value="{{ date('d-m-Y',strtotime($employee-> dob))}}">
                                    {!! $errors->first('dob', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('l_add') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.l_add')}} <sup>*</sup></label>
                                       <input type="text" name="l_add" id="l_add" class="l_add input-css" value="{{ $employee->local_address }}">
                                       {!! $errors->first('l_add', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('p_add') ? 'has-error' : ''}}">
                                     <label for="">{{__('Employee/profile.p_add')}} <sup>*</sup></label>
                                    <input type="text" name="p_add" id="p_add" class="p_add input-css" value="{{ $employee->permanent_address }}">
                                    {!! $errors->first('p_add', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('mob') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.mob')}} <sup>*</sup></label>
                                       <input type="number"  step="any" name="mob" id="mob" class="mob input-css" value="{{ $employee->mobile }}">
                                       {!! $errors->first('mob', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('h_land') ? 'has-error' : ''}}">
                                     <label for="">{{__('Employee/profile.h_land')}}    </label>
                                    <input type="number" step="any" name="h_land" id="h_land" class="h_land input-css"  value="{{ $employee->home_landline }}">
                                    {!! $errors->first('h_land', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('f_num') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.f_num')}} <sup>*</sup></label>
                                       <input type="number"  step="any" name="f_num" id="f_num" class="f_num input-css"  value="{{ $employee->family_number }}">
                                       {!! $errors->first('f_num', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('rel') ? 'has-error' : ''}}">
                                     <label for="">{{__('Employee/profile.rel')}}  <sup>*</sup>   </label>
                                    <select name="rel" id="rel" class="rel select2 input-css">
                                            <option value="">Select Relation</option>
                                            <option value="Self" {{$employee->relation_with_emp=="Self" ? 'selected=selected' : ''}}>Self</option>
                                            <option value="Father" {{$employee->relation_with_emp=="Father" ? 'selected=selected' : ''}}>Father</option>
                                            <option value="Mother" {{$employee->relation_with_emp=="Mother" ? 'selected=selected' : ''}}>Mother</option>
                                            <option value="Brother" {{$employee->relation_with_emp=="Brother" ? 'selected=selected' : ''}}>Brother</option>
                                            <option value="Sister" {{$employee->relation_with_emp=="Sister" ? 'selected=selected' : ''}}>Sister</option>
                                            <option value="Wife" {{$employee->relation_with_emp=="Wife" ? 'selected=selected' : ''}}>Wife</option>
                                            <option value="Husband" {{$employee->relation_with_emp=="Husband" ? 'selected=selected' : ''}}>Husband</option>
                                            <option value="Guardian" {{$employee->relation_with_emp=="Guardian" ? 'selected=selected' : ''}}>Guardian</option>
                                            <option value="Son" {{$employee->relation_with_emp=="Son" ? 'selected=selected' : ''}}>Son</option>
                                            <option value="Daughter" {{$employee->relation_with_emp=="Daughter" ? 'selected=selected' : ''}}>Daughter</option>

                                    </select>
                                    <label id="rel-error" class="error" for="rel"></label>
                                    {!! $errors->first('rel', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('doj') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.doj')}} <sup>*</sup></label>
                                       <input type="text" autocomplete="off"  name="doj" id="doj" class="doj input-css datepicker1" value="{{$employee->doj }}">
                                       {!! $errors->first('doj', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                               
                                <div class="col-md-6 {{ $errors->has('join') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.join')}} <sup>*</sup></label>
                                        <select name="join" id="join" class="join select2 input-css">
                                                <option value="">Is Signed Joining Paper</option>
                                                <option value="Yes" {{$employee->joining_paper_signed=="Yes" ? 'selected=selected' : ''}}>Yes</option>
                                                <option value="No" {{$employee->joining_paper_signed=="No" ? 'selected=selected' : ''}}>No</option>
    
                                        </select>
                                        <label id="join-error" class="error" for="join"></label>
                                       {!! $errors->first('join', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('desig') ? 'has-error' : ''}}">
                                                <label for="">{{__('Employee/profile.desig')}}   <sup>*</sup> </label>
                                               <input type="text" name="desig" id="desig" class="desig input-css" value="{{ $employee->designation }}">
                                               {!! $errors->first('desig', '<p class="help-block">:message</p>') !!}
                                           </div>
                        </div><br><br>
                        <div class="row">
                              
                                <div class="col-md-6 {{ $errors->has('skill') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.skill')}} <sup>*</sup></label>
                                        <select name="skill" id="skill" class="skill select2 input-css">
                                                <option value="">Is Signed Joining Paper</option>
                                                <option value="Skilled" {{$employee->employee_skill=="Skilled" ? 'selected=selected' : ''}}>Skilled</option>
                                                <option value="Semi Skilled" {{$employee->employee_skill=="Semi Skilled" ? 'selected=selected' : ''}}>Semi Skilled</option>
                                                <option value="Unskilled" {{$employee->employee_skill=="Unskilled" ? 'selected=selected' : ''}}>Unskilled</option>
    
                                        </select>
                                        <label id="skill-error" class="error" for="skill"></label>
                                       {!! $errors->first('skill', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('dept') ? 'has-error' : ''}}">
                                                <label for="">{{__('Employee/profile.dept')}} <sup>*</sup>   </label>
                                               <select name="dept" id="dept" class="dept select2 input-css">
                                                       <option value="">Select Department</option>
                                                       @foreach ($dept as $item)
                                               <option value="{{$item->id}}" {{$employee->department_id==$item->id ? 'selected=selected' : ''}}>{{$item->department}}</option>
                                                       @endforeach
                                                       
                                               </select>
                                               <label id="dept-error" class="error" for="dept"></label>
                                               {!! $errors->first('dept', '<p class="help-block">:message</p>') !!}
                                           </div>
                        </div><br><br>
                        <div class="row">
                              
                                
                                <div class="col-md-3 {{ $errors->has('from') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.from')}} <sup>*</sup>   </label>
                                        <input type="text" name="from" value="{{$shift_from}}" id="from" class="from input-css">
                                       {!! $errors->first('from', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-3 {{ $errors->has('to') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.to')}} <sup>*</sup>   </label>
                                        <input type="text" name="to" value="@if(isset($shift_to)) {{$shift_to}}@endif" id="to" class="to input-css">
                                       {!! $errors->first('to', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('reporthead') ? 'has-error' : ''}}">
                                        <label for="">Reporting Head <sup>*</sup></label>
                                        <select name="reporthead" id="reporthead" class="reporthead select2 input-css" required>
                                                <option value="">Select Reporting Head</option>
                                                @foreach($user as $head)
                                                  <option value="{{$head->id}}" {{$employee->reporting==$head->id ? 'selected=selected' : ''}}>{{$head->name}}</option>
                                                @endforeach
    
                                        </select>
                                        <label id="ot-error" class="error" for="ot"></label>
                                       {!! $errors->first('reporthead', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('ot') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.ot')}} <sup>*</sup></label>
                                        <select name="ot" id="ot" class="ot select2 input-css">
                                                <option value="">Is OverTime Elisible</option>
                                                <option value="Yes" {{$employee->is_OT=="Yes" ? 'selected=selected' : ''}}>Yes</option>
                                                <option value="No" {{$employee->is_OT=="No" ? 'selected=selected' : ''}}>No</option>
    
                                        </select>
                                        <label id="ot-error" class="error" for="ot"></label>
                                       {!! $errors->first('ot', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 overtime {{ $errors->has('overtime') ? 'has-error' : ''}}" {{ $employee->is_OT=="Yes" ? 'style=display:block' : 'style=display:none'}}>
                                        <label for="">Which Time Available For OverTime <sup>*</sup>   </label>
                                        <input type="text" name="overtime" value="{{$employee->overtime}}" id="to" class="overtime input-css" value="{{ old('overtime') }}">
                                       {!! $errors->first('overtime', '<p class="help-block">:message</p>') !!}
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
