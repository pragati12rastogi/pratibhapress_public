<?php  //print($labels);die();?>

@extends($layout)

@section('title', __('accounting/company.SelectCompany'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/company.SelectCompany')}}</i></a></li>
 
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
    <script src="/js/accounting/company.js"></script>
@endsection
@section('css')
  {{-- <link rel="stylesheet" href="css/bootstrap.min.css"> --}}
  <link rel="stylesheet" href="/css/party.css">
<style>
.span
{
    color:red;
}
</style>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            </div>
            @if ($errors->any())
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
            
            <form enctype="multipart/form-data" id="form" action="/accounting/select/company" method="POST">
                @csrf
                <div class="box box-default">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>{{__('accounting/company.SelectCompany')}}</h3>
                        </div>
                    </div>
                    <div class="box-header with-border">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('accounting/company.SelectCompany')}} <sup>*</sup></label>
                                        <select class="form-control select2 comp input-css" aria-required="true" data-placeholder=""
                                            style="width: 100%;" name="comp" id="comp_select">
                                            <option value="default">Select Company</option>
                                            @foreach($comp as $key)
                                            <option value="{{$key->id}}">
                                                {{$key->name.' - '.$key->financial_year_begin.' to '.$key->financial_year_end}}
                                            </option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('comp', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-success">Select Company</button>
                </div>
            </form>




    </section><!--end of section-->
@endsection

