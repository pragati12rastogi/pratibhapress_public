@extends($layout)

@section('title', __('Employee/profile.title'))

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Employee Profile</a></li>
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
<script src="/js/Employee/profile.js"></script>
@endsection
@section('main_section')
    <section class="content">
                <div id="app">
                                @include('sections.flash-message')
                                @yield('content')
                            </div>
                            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Default box -->
       <form action="/employee/profile/create" method="POST" id="form">
        @csrf
        <ul class="nav nav1 nav-pills">
                <li class="nav-item">
                  <a class="nav-link active" style="background-color: #87CEFA"  href="{{url('/employee/profile/create') }}">Employee Details</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link " href="#" disabled>PF ESI Details</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#" disabled>Documents Upload</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link"  href="#" disabled>Relieving Details</a>
                </li>
                <li class="nav-item">
                        <a class="nav-link"  href="#" disabled>Employee Category</a>
                </li>
              </ul>
              <br>
           
        <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">{{__('Employee/profile.mytitle')}}</h3><br><br><br>
                    <div class="container-fluid wdt">
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('emp_name') ? 'has-error' : ''}}">
                                 <label for="">{{__('Employee/profile.name')}} <sup>*</sup></label>
                                <input type="text" name="emp_name" id="emp_name" class="emp_name input-css" value="{{ old('emp_name') }}">
                                {!! $errors->first('emp_name', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="col-md-6 {{ $errors->has('father') ? 'has-error' : ''}}">
                                    <label for="">{{__('Employee/profile.father')}} <sup>*</sup></label>
                                   <input type="text" name="father" id="father" class="father input-css" value="{{ old('father') }}">
                                   {!! $errors->first('father', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div><br><br>
                         <div class="row">
                                <div class="col-md-6 {{ $errors->has('emp_email') ? 'has-error' : ''}}">
                                     <label for="">Email <sup></sup></label>
                                    <input type="email" name="emp_email" id="emp_email" class="emp_email input-css" value="{{ old('emp_email') }}" >
                                    {!! $errors->first('emp_email', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('emp_aadhar') ? 'has-error' : ''}}">
                                     <label for="">Aadhar No.<sup>*</sup></label>
                                    <input type="text" name="emp_aadhar" id="emp_aadhar" class="emp_aadhar input-css" value="{{ old('emp_aadhar') }}" minlength="12" maxlength="12" required>
                                    {!! $errors->first('emp_aadhar', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('dob') ? 'has-error' : ''}}">
                                     <label for="">{{__('Employee/profile.dob')}} <sup>*</sup></label>
                                    <input type="text" autocomplete="off" name="dob" id="dob" class="dob input-css datepicker1" value="{{ old('dob') }}">
                                    {!! $errors->first('dob', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('l_add') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.l_add')}} <sup>*</sup></label>
                                       <input type="text" name="l_add" id="l_add" class="l_add input-css" value="{{ old('l_add') }}">
                                       {!! $errors->first('l_add', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('p_add') ? 'has-error' : ''}}">
                                     <label for="">{{__('Employee/profile.p_add')}} <sup>*</sup></label>
                                    <input type="text" name="p_add" id="p_add" class="p_add input-css" value="{{ old('p_add') }}">
                                    {!! $errors->first('p_add', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('mob') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.mob')}} <sup>*</sup></label>
                                       <input type="number"  step="any" name="mob" id="mob" class="mob input-css" value="{{ old('mob') }}">
                                       {!! $errors->first('mob', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('h_land') ? 'has-error' : ''}}">
                                     <label for="">{{__('Employee/profile.h_land')}}    </label>
                                    <input type="number" step="any" name="h_land" id="h_land" class="h_land input-css" value="{{ old('h_land') }}">
                                    {!! $errors->first('h_land', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('f_num') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.f_num')}} <sup>*</sup></label>
                                       <input type="number"  step="any" name="f_num" id="f_num" class="f_num input-css" value="{{ old('f_num') }}">
                                       {!! $errors->first('f_num', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('rel') ? 'has-error' : ''}}">
                                     <label for="">{{__('Employee/profile.rel')}}  <sup>*</sup>  </label>
                                    <select name="rel" id="rel" class="rel select2 input-css">
                                            <option value="">Select Relation</option>
                                            <option value="Self" {{old('rel')=="Self" ? 'selected=selected' : ''}}>Self</option>
                                            <option value="Father" {{old('rel')=="Father" ? 'selected=selected' : ''}}>Father</option>
                                            <option value="Mother" {{old('rel')=="Mother" ? 'selected=selected' : ''}}>Mother</option>
                                            <option value="Brother" {{old('rel')=="Brother" ? 'selected=selected' : ''}}>Brother</option>
                                            <option value="Sister" {{old('rel')=="Sister" ? 'selected=selected' : ''}}>Sister</option>
                                            <option value="Wife" {{old('rel')=="Wife" ? 'selected=selected' : ''}}>Wife</option>
                                            <option value="Husband" {{old('rel')=="Husband" ? 'selected=selected' : ''}}>Husband</option>
                                            <option value="Guardian" {{old('rel')=="Guardian" ? 'selected=selected' : ''}}>Guardian</option>
                                            <option value="Son" {{old('rel')=="Son" ? 'selected=selected' : ''}}>Son</option>
                                            <option value="Daughter" {{old('rel')=="Daughter" ? 'selected=selected' : ''}}>Daughter</option>

                                    </select>
                                    <label id="rel-error" class="error" for="rel"></label>
                                    {!! $errors->first('rel', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('doj') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.doj')}} <sup>*</sup></label>
                                       <input type="text"  name="doj" id="doj" class="doj input-css datepicker1" value="{{ old('doj') }}">
                                       {!! $errors->first('doj', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                               
                                <div class="col-md-6 {{ $errors->has('join') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.join')}} <sup>*</sup></label>
                                        <select name="join" id="join" class="join select2 input-css">
                                                <option value="">Is Signed Joining Paper</option>
                                                <option value="Yes" {{old('join')=="Yes" ? 'selected=selected' : ''}}>Yes</option>
                                                <option value="No" {{old('join')=="No" ? 'selected=selected' : ''}}>No</option>
    
                                        </select>
                                        <label id="join-error" class="error" for="join"></label>
                                       {!! $errors->first('join', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('desig') ? 'has-error' : ''}}">
                                                <label for="">{{__('Employee/profile.desig')}}   <sup>*</sup> </label>
                                               <input type="text" name="desig" id="desig" class="desig input-css" value="{{ old('desig') }}">
                                               {!! $errors->first('desig', '<p class="help-block">:message</p>') !!}
                                           </div>
                        </div><br><br>
                        <div class="row">
                               
                                <div class="col-md-6 {{ $errors->has('skill') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.skill')}} <sup>*</sup></label>
                                        <select name="skill" id="skill" class="skill select2 input-css">
                                                <option value="">Select Skill Category</option>
                                                <option value="Skilled" {{old('skill')=="Skilled" ? 'selected=selected' : ''}}>Skilled</option>
                                                <option value="Semi Skilled" {{old('skill')=="Semi Skilled" ? 'selected=selected' : ''}}>Semi Skilled</option>
                                                <option value="Unskilled" {{old('skill')=="Unskilled" ? 'selected=selected' : ''}}>Unskilled</option>
    
                                        </select>
                                        <label id="skill-error" class="error" for="skill"></label>
                                       {!! $errors->first('skill', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('dept') ? 'has-error' : ''}}">
                                                <label for="">{{__('Employee/profile.dept')}} <sup>*</sup>   </label>
                                               <select name="dept" id="dept" class="dept select2 input-css">
                                                       <option value="">Select Department</option>
                                                       @foreach ($dept as $item)
                                               <option value="{{$item->id}}" {{old('dept')==$item->id ? 'selected=selected' : ''}}>{{$item->department}}</option>
                                                       @endforeach
                                                       
                                               </select>
                                               <label id="dept-error" class="error" for="dept"></label>
                                               {!! $errors->first('dept', '<p class="help-block">:message</p>') !!}
                                           </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-3 {{ $errors->has('from') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.from')}} <sup>*</sup>   </label>
                                        <input type="text" name="from" id="from" class="from input-css timepicker" value="{{ old('from') }}">
                                       {!! $errors->first('from', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-3 {{ $errors->has('to') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.to')}} <sup>*</sup>   </label>
                                        <input type="text" name="to" id="to" class="to input-css timepicker" value="{{ old('to') }}">
                                       {!! $errors->first('to', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('reporthead') ? 'has-error' : ''}}">
                                        <label for="">Reporting Head <sup>*</sup></label>
                                        <select name="reporthead" id="reporthead" class="reporthead select2 input-css">
                                                <option value="">Select Reporting Head</option>
                                                @foreach($user as $head)
                                                  <option value="{{$head->id}}">{{$head->name}}</option>
                                                @endforeach
    
                                        </select>
                                        <label id="ot-error" class="error" for="ot"></label>
                                       {!! $errors->first('ot', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        <div class="row">
                                <div class="col-md-6 {{ $errors->has('ot') ? 'has-error' : ''}}">
                                        <label for="">{{__('Employee/profile.ot')}} <sup>*</sup></label>
                                        <select name="ot" id="ot" class="ot select2 input-css">
                                                <option value="">Is OverTime Eligible</option>
                                                <option value="Yes" {{old('ot')=="Yes" ? 'selected=selected' : ''}}>Yes</option>
                                                <option value="No" {{old('ot')=="No" ? 'selected=selected' : ''}}>No</option>
    
                                        </select>
                                        <label id="ot-error" class="error" for="ot"></label>
                                       {!! $errors->first('ot', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 overtime {{ $errors->has('overtime') ? 'has-error' : ''}}" style="display:none">
                                        <label for="">Which Time Available For OverTime <sup>*</sup>   </label>
                                        <input type="text" name="overtime" id="to" class="to input-css timepicker" value="{{ old('overtime') }}">
                                       {!! $errors->first('overtime', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br><br>
                        {{-- <div class="row">
                                
                                <div class="col-md-6 {{ $errors->has('bank_status') ? 'has-error' : ''}}">
                                <label for="">Is Bank Account Is New Or Existing ?<sup>*</sup></label>
                                        <div class="col-md-2">
                                                <div class="radio">
                                                <label><input autocomplete="off" type="radio" class="bank_status" value="Existing" name="bank_status">Existing</label>
                                                </div>
                                        </div>
                                        <div class="col-md-2">
                                                <div class="radio">
                                                <label><input     autocomplete="off" type="radio" class="bank_status" value="New" name="bank_status">New</label>
                                                </div>
                                        </div>
                                        <label id="bank_status-error" class="error" for="bank_status"></label>
                                        {!! $errors->first('bank_status', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br>
                        <div class="row">
                        <div class="col-md-4 {{ $errors->has('acc_name') ? 'has-error' : ''}}">
                                <label for="">Account Name<sup>*</sup></label>
                                <input type="text" name="acc_name" id="" class="input-css acc_name">
                                {!! $errors->first('acc_name', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-4 {{ $errors->has('acc_number') ? 'has-error' : ''}}">
                                <label for="">Account Number<sup>*</sup></label>
                                <input type="number" min="0" step="none" name="acc_number" id="" class="input-css acc_number">
                                {!! $errors->first('acc_number', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-4 {{ $errors->has('acc_ifsc') ? 'has-error' : ''}}">
                                <label for="">Account IFSC Code<sup>*</sup></label>
                                <input type="text" name="acc_ifsc" id="" class="input-css acc_ifsc">
                                {!! $errors->first('acc_ifsc', '<p class="help-block">:message</p>') !!}
                        </div>
                        </div><br><br> --}}
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
