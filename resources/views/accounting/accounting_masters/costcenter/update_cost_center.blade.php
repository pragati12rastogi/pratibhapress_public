
@extends($layout)

@section('title', __('accounting/costcenter.update title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/costcenter.update title')}}</i></a></li>
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
<script src="/js/accounting/CostCenter.js"></script>
<script>
    // add select2 to select element with class 'input-css'
$('.select').select2({
                containerCssClass:"input-css"
            });
</script>
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
                        <form method="POST" action="/accounting/costcenter/update/{{$cc->id}}" id="cost_center_form">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="cost_category">{{__('accounting/costcenter.cost category')}}</label>
                                    <select class="form-control select input-css cost_category" name="cost_category" id="cost_category">
                                        <option value="default">Select Cost Category</option>
                                        @foreach ($cost_category as $key)
                                            <option value="{{$key->id}}" {{$cc->category_under ==$key->id ?'selected="selected"':''}}>{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="name">{{__('accounting/costcenter.name')}}</label>
                                    <input class="form-control input-css name" value="{{$cc->name}}" type="text" name="name" id="name">
                                    <input  value="{{$cc->id}}" type="hidden" name="id">
                                </div>
                                @if ($alias_with_name==1)
                                    <div class="col-md-6">
                                        <label for="alias">{{__('accounting/costcenter.alias')}}</label>
                                        <input class="form-control input-css alias" value="{{$cc->alias}}" type="text" name="alias" id="alias">
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                      <label for="alias">{{__('accounting/costcenter.under')}}</label>
                                      <select class="form-control select input-css under" name="under" id="under">
                                        <option value="default">Select Cost Center</option>
                                        @foreach ($cost_center as $key)
                                            <option value="{{$key->id}}" {{$cc->under ==$key->id ?'selected="selected"':''}}>{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="job_costing">{{__('accounting/costcenter.job costing')}}</label>
                                    <select class="form-control select input-css job_costing" id="job_costing" name="job_costing">
                                        <option value="1" {{$cc->use_for_job_costing ==1 ?'selected="selected"':''}}>Yes</option>
                                        <option value="0" {{$cc->use_for_job_costing ==0 ?'selected="selected"':''}}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class=" btn btn-success">
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
    </section><!--end of section-->
@endsection


