
@extends($layout)
@section('title', __('admin.Profile'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('admin.Profile')}}</i></a></li>
@endsection

@section('css')
{{-- <link rel="stylesheet" href="/css/bootstrap.min.css"> --}}
    <style>
        .help-block{
        color:red;
        }
        table.th{
            text-indent: 50px;
        }
    </style>
@endsection
@section('main_section')
    <section class="content">
    <div id="app">
        @include('sections.flash-message')
        @yield('content')
    </div>
<!-- Default box -->
{{-- <a href="/user/password/update"><button class="btn btn-success">Update Password</button> --}}
<a href="/profile/update"><button class="btn btn-success">Update Profile</button>
</a>

    <div class="box-header with-border">
        <div class='box box-default'><br>
            <div class="container-fluid">
                <div class="row ">
                    <div class="col-md-4 " style="text-indent:40px;">
                        
                        @if($data->profile_photo != "" || $data->profile_photo != null)
                             @if (file_exists(public_path().'/userimages/'.$data->profile_photo ))
                                 <img src="/userimages/{{$data->profile_photo}}" class="img-circle" width="100px" height="100px" alt="User Image">
                            @endif
                        @endif
                    </div>
                    <div class="col-md-8">
                            <div class="col-md-8 " style="text-indent:30px;font-size:28px">{{$data->name}}</div>
                            <br>
                            <br>
                            <br>
                            <div class="col-md-6" style="">{{__('admin.updated_at')}} {{$data->updated_at}}</div>
        
                    </div>
                </div>
                <br>
            </div>
        </div>
        <div class='box box-default'>
            <div class="container-fluid">
                <br>
                <div class="row ">
                    <div class="col-md-4 " style="text-indent:30px"><label>{{__('admin.email')}}</label></div>
                    <div class="col-md-4 " style="text-indent:30px">{{$data->email}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " style="text-indent:30px"><label>{{__('admin.phone')}}</label></div>
                    <div class="col-md-4 " style="text-indent:30px">{{$data->phone}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " style="text-indent:30px"><label>{{__('admin.landline')}}</label></div>
                    <div class="col-md-4 " style="text-indent:30px">{{$data->home_landline}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " style="text-indent:30px"><label>{{__('admin.created_at')}}</label></div>
                    <div class="col-md-4 " style="text-indent:30px">{{$data->created_at}}</div>
                </div>
                <div class="row ">
                    <div class="col-md-4 " style="text-indent:30px"><label>{{__('admin.user_type')}}</label></div>
                    <div class="col-md-4 " style="text-indent:30px">{{$data->user_type}}</div>
                </div>
                {{-- <div class="row ">
                    <div class="col-md-4 " style="text-indent:30px"><label>{{__('admin.login')}}</label></div>
                    <div class="col-md-4 " style="text-indent:30px">{{$data->login==1?"Active":"Inactive"}}</div>
                </div>
            </div> --}}
        </div>
    </div>
</section>
@endsection
