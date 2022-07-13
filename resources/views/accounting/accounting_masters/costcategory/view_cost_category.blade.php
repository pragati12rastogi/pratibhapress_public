
@extends($layout)

@section('title', __('accounting/costcategory.view title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/costcategory.view title')}}</i></a></li>
 @endsection
@section('css')

@endsection

@section('main_section')

    <section class="content">
        <div class="row">
            <div style="text-align:center" class="col-md-12">
                <h3>
                    <label for="company_name">{{AccountingCustomHelper::getCompanyName()}}</label>
                </h3>
            </div>
        </div>
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
                    <div class="container-fluid wdt">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="name">{{__('accounting/costcategory.name')}}</label>
                                </div>
                                <div class="col-md-4">
                                        <label id="name">{{$cc->name}}</label>
                                </div>
                        </div>
                        @if ($alias_with_name==1)
                        <div class="row">
                                <div class="col-md-4">
                                        <label for="alias">{{__('accounting/costcategory.alias')}}</label>
                                </div>
                                <div class="col-md-4">
                                        <label for="alias">{{$cc->alias}}</label>
                                </div>
                        </div>
                        @endif
                        <div class="row">
                                <div class="col-md-4">
                                        <label for="alias">{{__('accounting/costcategory.revenue item')}}</label>
                                </div>
                                <div class="col-md-4">
                                        <label for="alias">{{$cc->allocate_revenue==1?'Yes':'No'}}</label>
                                </div>
                        </div>
                        <div class="row">
                                        
                                <div class="col-md-4">
                                        <label for="alias">{{__('accounting/costcategory.non revenue item')}}</label>
                                </div>
                                <div class="col-md-4">
                                        <label for="alias">{{$cc->allocate_non_revenue==1?'Yes':'No'}}</label>
                                </div>
                        </div>
                            
                                
                    </div>
                </div>
            </div>
    </section><!--end of section-->
@endsection


