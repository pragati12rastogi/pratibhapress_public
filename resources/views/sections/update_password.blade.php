@extends($layout)
​
@section('title', __('registration.title1'))
​
{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)
​
@section('breadcrumb')
<li><a href="#"><i class="">{{__('registration.mytitle')}}</i></a></li>
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
            <form  action="/user/password/update" enctype="multipart/form-data" method="POST" id="form" files="true">
                    @csrf
                   
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('pass') ? 'has-error' : ''}}">
                            <label>{{__('registration.pass')}} <sup>*</sup></label><br>
                            <input autocomplete="off" type="password" class="form-control input-css" name="pass"
                            value="">
                            {!! $errors->first('pass', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->
                        <div class="col-md-6 {{ $errors->has('re_pass') ? 'has-error' : ''}}">
                            <label>{{__('registration.con_pass')}} <sup>*</sup></label><br>
                            <input autocomplete="off" type="password" class="form-control input-css" name="re_pass"
                            value="">
                            {!! $errors->first('re_pass', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!--col-md-4-->
                        <!--col-md-4-->
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