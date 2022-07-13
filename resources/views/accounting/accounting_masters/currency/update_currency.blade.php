
@extends($layout)

@section('title', __('accounting/currency.update title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/currency.update title')}}</i></a></li>
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
<script src="/js/accounting/Currency.js"></script>
<script>
    // add select2 to select element with class 'input-css'
    $(document).ready(function(){
        $('.select').select2({
                containerCssClass:"input-css"
            });
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
                        <form method="POST" action="/accounting/currency/update/{{$cd->id}}" id="currency_form">
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">{{__('accounting/currency.symbol')}}</label>
                                    <input type="text" name="symbol" value="{{$cd->currency_name}}" id="symbol" class="input-css form-control symbol validtxt">
                                    <input type="hidden" name="id" value="{{$cd->id}}">
                                </div>
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/currency.formal name')}}</label>
                                    <input type="text" name="formal_name" value="{{$cd->formal_name}}" id="formal_name" class="input-css form-control formal_name validtxt">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/currency.decimal places')}}</label>
                                    <input type="text" name="decimal_places" value="{{$cd->no_of_decimal}}" id="decimal_places" class="input-css form-control decimal_places validtxt">
                                </div>
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/currency.amt in million')}}</label>
                                    <select name="amt_in_million"  id="amt_in_million" class="input-css form-control select amt_in_million validopt">                  
                                        <option value="1" {{$cd->amt_in_million ==1 ?'selected="selected"':''}} >Yes</option>
                                        <option value="0" {{$cd->amt_in_million ==0 ?'selected="selected"':''}} >No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/currency.symbol suffix')}}</label>
                                    <select name="symbol_suffix" id="symbol_suffix" class=" symbol_suffix input-css form-control select validopt">
                                        <option value="0" {{$cd->symbol_suffix ==0 ?'selected="selected"':''}}>No</option>
                                        <option value="1" {{$cd->symbol_suffix ==1 ?'selected="selected"':''}}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/currency.space between')}}</label>
                                    <select  name="space_between" id="space_between" class="space_between input-css form-control select validopt">
                                        <option value="0" {{$cd->space_between ==1 ?'selected="selected"':''}}>No</option>
                                        <option value="1" {{$cd->space_between ==0 ?'selected="selected"':''}}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/currency.decimal position symbol')}}</label>
                                    <input type="text" value="{{$cd->symbol_decimal_portion}}" name="decimal_position_symbol" id="decimal_position_symbol" class="input-css form-control decimal_position_symbol validtxt">
                                </div>
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/currency.decimal places amount')}}</label>
                                    <input type="text" value="{{$cd->decimal_places}}" name="decimal_places_amount" id="decimal_places_amount" class="input-css form-control decimal_places_amount validtxt">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class=" btn btn-success">
                            </div>
                        </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
    </section><!--end of section-->
@endsection


