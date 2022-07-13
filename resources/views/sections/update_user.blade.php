@extends($layout)
​
@section('title', __('registration.title1'))
​
{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)
​
@section('breadcrumb')
<li><a href="{{url('/admin')}}"><i class="">{{__('registration.mytitle')}}</i></a></li>
<li><a href="#"><i class="">{{__('registration.title1')}}</i></a></li>
@endsection
​
@section('css')
<link rel="stylesheet" href="/css/party.css">
@endsection
​
@section('js')
<script src="/js/views/user_update.js"></script>
@endsection
​
@section('main_section')
<section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
    <!-- Default box -->
    <div class="box-header with-border">
        <div class='box box-default'> <br>
            <div class="container-fluid">
            <form  action="/user/updateDb/{{$id}}" enctype="multipart/form-data" method="POST" id="form" files="true">
                    @csrf
                    <div class="row">
                        {{-- <input type="hidden" name="job_id" value="{{$job['job_id']}}"> --}}
                            <div class="col-md-12">
                                <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                <input type="text" name="update_reason" required="" class="form-control input-css " id="update_reason">
                                {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                            </div><!--col-md-4-->
                        </div><br>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('username') ? 'has-error' : ''}}">
                            <label>{{__('registration.name')}} <sup>*</sup></label><br>
                            <input autocomplete="off" type="text" class="form-control input-css" name="username"
                                value="{{ $detail->name }}">
                            {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->
                        <div class="col-md-6 {{ $errors->has('email') ? 'has-error' : ''}}">
                            <label>{{__('registration.email')}} <sup>*</sup></label><br>
                            <input autocomplete="off" type="email" class="form-control input-css" name="email"
                            value="{{ $detail->email}}">
                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->
                        <!--col-md-4-->
                    </div>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('pass') ? 'has-error' : ''}}">
                            <label>{{__('registration.pass')}} <sup></sup></label><br>
                            <input autocomplete="off" type="password" class="form-control input-css" name="pass"
                            value="">
                            {!! $errors->first('pass', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->
                        <div class="col-md-6 {{ $errors->has('re_pass') ? 'has-error' : ''}}">
                            <label>{{__('registration.con_pass')}} <sup></sup></label><br>
                            <input autocomplete="off" type="password" class="form-control input-css" name="re_pass"
                            value="">
                            {!! $errors->first('re_pass', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->
                        <!--col-md-4-->
                    </div>
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('phone') ? 'has-error' : ''}}">
                            <label>{{__('registration.phone')}} <sup>*</sup></label><br>
                            <input autocomplete="off" type="number" class="form-control input-css" name="phone"
                            value="{{ $detail->phone }}">
                            {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->
                        <div class="col-md-6 {{ $errors->has('landline') ? 'has-error' : ''}}">
                            <label>{{__('registration.landline')}} </label><br>
                            <input autocomplete="off" type="number" class="form-control input-css" name="landline"
                            value="{{ $detail->home_landline }}">
                            {!! $errors->first('landline', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->
                        <!--col-md-4-->
                    </div>
                    @if ($type=="admin")                        
                        <div class="row">
                            <div class="col-md-6 {{ $errors->has('user_type') ? 'has-error' : ''}}">
                                <div class="form-group">
                                    <label>{{__('registration.user_type')}}<sup>*</sup></label><br>
                                    <select class="form-control select2 user_type" data-placeholder="" id="user_type" style="width: 100%;" name="user_type">
                                    @if($auth_type =="superadmin")
                                    <option value="superadmin" {{$detail->user_type =="superadmin" ? 'selected=selected' : ''}}>SuperAdmin</option>
                                    @endif
                                        <option value="admin" {{$detail->user_type=="admin" ? 'selected=selected' : ''}}>{{__('registration.admin')}}</option>
                                        <option value="user" {{$detail->user_type=="user" ? 'selected=selected' : ''}}>{{__('registration.user')}}</option>
                                    </select>
                                    <label id="user_type-error" class="error" for="user_type"></label>
                                </div>
                                {!! $errors->first('user_type', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                    @endif
                    <input type="hidden" name="type" value="{{$type}}">
                    <div class="col-md-6 {{ $errors->has('depart') ? 'has-error' : ''}}">
                        <div class="form-group">
                            <label for="department">{{__('registration.dept')}}<sup>*</sup></label>
                            <select class="input-css depart select2" style="padding-top:2px" name="depart">
                                <option value="">{{__('registration.sel_dept')}}</option>
                                @foreach($department as $dept)
                                 <option value="{{$dept->id}}" {{ ( ($dept->id) == ($detail->department_id)) ? 'selected' : '' }}>{{$dept->department}}</option>
                                @endforeach
                            </select>
                        </div> 
                        {!! $errors->first('depart', '<p class="help-block">:message</p>') !!}              
                    </div>
                    <div class="row">
                        <div class="col-md-12 " style="text-indent:40px;">
                            
                            @if ($detail->profile_photo !=""||$detail->profile_photo != null)
                                @if (file_exists(public_path().'/userimages/'.$detail->profile_photo ))
                                    <img src="/userimages/{{$detail->profile_photo}}" class="img-circle" width="100px" height="100px" alt="">  
                                @endif    
                            @else
                                <img src="/userimages/avatar.png" class="img-circle" width="100px" height="100px" alt="">  
                            @endif
                            </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 {{ $errors->has('profile_pic') ? 'has-error' : ''}}">
                        <div class="form-group">
                            <label for="profile_pic">{{__('registration.profile_pic')}}</label>
                            <input type="file" class="input-css form-control" id="profile_pic"  name="profile_pic" accept="image/*">
                            <input type="hidden" name="hidden_image" value="{{$detail->profile_photo}}" accept="image/*">
                        </div> 
                        {!! $errors->first('profile_pic', '<p class="help-block">:message</p>') !!}
                                       
                    </div>
                </div>
                        <div class="row">
                            <div class="box-footer">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
        
                </form>
            </div>
            <!--end of container-fluid-->
        </div>
        <!------end of box box-default---->
    </div>
    <!--end of box-header with-border-->
</section>
<!--end of section-->
@endsection