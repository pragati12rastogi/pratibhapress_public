
@extends($layout)

@section('title', __('accounting/dashboard.title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/dashboard.title')}}</i></a></li>
 @endsection
@section('css')
<style>
    /* css for adding differentiation between focused and non focused element */
    .input-css:focus, .select2-search__field:focus{
        background-color: #FAEF9F !important;
    }
</style>
@endsection

@section('js')

@endsection
@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                   
            </div>
            @if($errors->any())
        {{-- display all  the errors if they are present. --}}
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="list-style:none">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
      

            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/dashboard.title')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    
                                    <div class="col-md-4"><i>Current Period</i></div>
                                    <div class="col-md-3"></div>
                                    <div class="col-md-4"><i>Current Date</i></div>
                                    
                                </div>
                                <div class="row">

                                        <div class="col-md-1"></div>
                                        <div class="col-md-4"><b>to be confirmed</b></div>
                                        <div class="col-md-3"></div>
                                        <div class="col-md-4"><b>{{date("D, d M Y")}}</b></div>
                                        
                                </div>
                                <br>
                                <br>
                                <div class="row">
                                    <div class="col-md-12" style="text-align:center"><b>List of Companies</b></div>
                                </div>

                                <br>
                                <br>
                                <div class="row">
                                <div class="col-md-4"><i>Name of Company</i></div>
                                <div class="col-md-3"></div>
                                <div class="col-md-4" style="text-align:right"><i>Date of Last Entry</i></div>
                                </div>
                                
                                @foreach ($comp as $key)
                                    {{-- showing all the company --}}
                                    <div class="row">
                                        @if($key->id== session('Active_comp'))
                                            <div class="col-md-4"><b>{{$key->name}} </b>(Selected)</div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-4" style="text-align:right"></div>                                        
                                        @else
                                            <div class="col-md-4">{{$key->name}}</div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-4" style="text-align:right"></div>                                        
                                        
                                        @endif
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>                 
    </section><!--end of section-->
@endsection


