
@extends($layout)

@section('title', __('accounting/costcategory.update title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/costcategory.update title')}}</i></a></li>
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
<script src="/js/accounting/CostCategory.js"></script>
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
                        <form method="POST" action="/accounting/costcategory/update/{{$cc->id}}" id="cost_category_form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="name">{{__('accounting/costcategory.name')}}</label>
                                    <input class="form-control input-css name" value="{{$cc->name}}" type="text" name="name" id="name">
                                    <input value="{{$cc->id}}" type="hidden" name="id" id="name">
                                </div>
                                @if ($alias_with_name==1)
                                    <div class="col-md-6">
                                        <label for="alias">{{__('accounting/costcategory.alias')}}</label>
                                        <input class="form-control input-css alias" value="{{$cc->alias}}" type="text" name="alias" id="alias">
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="revenue_item">{{__('accounting/costcategory.revenue item')}}</label>
                                    <select class="select form-control input-css revenue_item" name="revenue_item" id="revenue_item">
                                        <option value="1" {{$cc->allocate_revenue == 1?'selected="selected"':''}}>Yes</option>
                                        <option value="0" {{$cc->allocate_revenue == 0?'selected="selected"':''}}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                        <label for="revenue_item">{{__('accounting/costcategory.non revenue item')}}</label>
                                        <select class="select form-control input-css non_revenue_item" name="non_revenue_item" id="non_revenue_item">
                                            <option value="1" {{$cc->allocate_non_revenue == 1?'selected="selected"':''}}>Yes</option>
                                            <option value="0" {{$cc->allocate_non_revenue == 0?'selected="selected"':''}}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-success">
                                </div>
                        </form>
                    </div>
                </div>
            </div>
    </section><!--end of section-->
@endsection


