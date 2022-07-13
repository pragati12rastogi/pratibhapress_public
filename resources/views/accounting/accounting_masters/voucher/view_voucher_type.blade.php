
@extends($layout)

@section('title', __('accounting/voucher.createtitle'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/voucher.createtitle')}}</i></a></li>
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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/voucher.createtitle')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                            <div class="row">
                                    <div class="col-md-4">
                                            <label for="VoucherName">{{__('accounting/voucher.vouchername')}}</label>
                                    </div>
                                     <div class="col-md-4">
                                            <label id="VoucherName">{{$ledger_data['name']['current_value']}}</label>
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                        <label for="VoucherAlias">{{__('accounting/voucher.voucheralias')}}</label>
                                </div>
                                        <div class="col-md-4">
                                        <label id="VoucherAlias">{{$ledger_data['alias']['current_value']}}</label>
                                </div>
                            </div>
                        <br>
                        <div class="row">
                                <div class="col-md-4">
                                        <label for="VoucherType">{{__('accounting/voucher.vouchertype')}}</label>
                                </div>
                                        <div class="col-md-4">
                                        <label id="VoucherType">{{$ledger_data['parent']['current_value']}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                        <label for="VoucherAbbr">{{__('accounting/voucher.abbr')}}</label>
                                </div>
                                        <div class="col-md-4">
                                        <label id="VoucherAbbr">{{$ledger_data['mailing_name']['current_value']}}</label>
                                </div>
                            </div>
                            <br>
                        <hr>
                    </div>
                </div>
            </div>
            <div id="VoucherLayout" >
                    <div class="box-header with-border">
                        <div class="box box-default"><br>
                            <div class="container-fluid wdt">
                                <div class="row">
                                    @for($time=1;$time<4;$time++)
                                    <div class="col-md-4" id="VoucherLayout1">
                                        @if (count($led_data[$time])>0)
                                            {{-- showing data heading --}}
                                            <h2 class="box-title" style="font-size: 20px;margin-left:20px">{{$heading[$time]}}</h2><br><br><br>
                                        @endif
                                        @foreach ($led_data[$time] as $key => $value)
                                            {{-- showing data row by row --}}
                                            <div class="row">
                                                <div class="col-md-8">
                                                <label>{{$value['title']}}</label>
                                                </div>
                                                <div class="col-md-4">
                                                @if (isset($value['current_value']))                                                                        
                                                        <label>{{$value['current_value']}}</label>
                                                @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endfor
                                </div>
                            </div>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>         
    </section><!--end of section-->
@endsection


