
@extends($layout)

@section('title', __('accounting/currency.view title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/currency.view title')}}</i></a></li>
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
            <div class="alert alert-danger">
        {{-- display all  the errors if they are present. --}}

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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/currency.view title')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="symbol">{{__('accounting/currency.symbol')}}</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label id="symbol">{{$cd->currency_name}}</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="formal_name">{{__('accounting/currency.formal name')}}</label>
                                    </div>
                                    <div class="col-md-4">
                                            <label id="formal_name">{{$cd->formal_name}}</label>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="decimal_places">{{__('accounting/currency.decimal places')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="decimal_places">{{$cd->no_of_decimal}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">{{__('accounting/currency.amt in million')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label for="">{{$cd->amt_in_million ==1 ?'Yes':'No'}}</label>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-4">
                                    <label for="symbol_suffix">{{__('accounting/currency.symbol suffix')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label for="symbol_suffix">{{$cd->symbol_suffix ==1 ?'Yes':'No'}}</label>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-4">
                                    <label for="space_between">{{__('accounting/currency.space between')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="space_between">{{$cd->space_between ==1 ?'Yes':'No'}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="decimal_position_symbol">{{__('accounting/currency.decimal position symbol')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="decimal_position_symbol">{{$cd->symbol_decimal_portion ==1 ?'Yes':'No'}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="decimal_places_amount">{{__('accounting/currency.decimal places amount')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="decimal_places_amount">{{$cd->decimal_places ==1 ?'Yes':'No'}}</label>
                                </div>
                            </div>
                        </div>
                            <br>
                        
                    </div>
                </div>
            </div>
    </section><!--end of section-->
@endsection


