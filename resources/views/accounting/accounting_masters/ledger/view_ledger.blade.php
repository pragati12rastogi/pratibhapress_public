@extends($layout)
@section('title', __(''))
{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)
@section('breadcrumb')
<li><a href="#"><i class="">{{__('')}}</i></a></li>
 @endsection
@section('css')
@endsection
@section('js')
    <script src="/js/accounting/Ledger.js"></script>
@endsection
@section('main_section')
    <section class="content">
        <div class="row">
            <div style="text-align:center" class="col-md-12">
               <div class="col-md-6"></div>
                <h3>
                    <label for="company_name">{{AccountingCustomHelper::getCompanyName()}}</label>
                </h3>    
            </div>
        </div>
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
        {{-- display all  the errors if they are present. --}}
        @if($errors->any())
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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/ledger.viewLedger')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="ledger_name">{{__('accounting/ledger.name')}}</label>
                                </div>
                                <div class="col-md-6">
                                    <label for="ledger_name">{{$ledger_data['name']['current_value']}}</label>
                                </div>
                            </div>
                            @if( $ledger_data['alias']['current_value']!='')
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="ledger_alias">{{__('accounting/ledger.alias')}}</label>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ledger_alias">{{$ledger_data['alias']['current_value']}}</label>
                                    </div>
                                </div>
                            @endif
                            @if( $ledger_data['notes']['current_value']!='')
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="ledger_notes">{{__('accounting/ledger.notes')}}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <label for="ledger_notes">{{$ledger_data['notes']['current_value']}}</label>
                                    </div>
                                </div>
                            @endif
                            <br>
                            <div class="row">             
                                <div class="col-md-6">
                                    <label for="ledger_under">{{__('accounting/ledger.under')}}</label>
                                </div>  
                                <div class="col-md-6">
                                    <label for="ledger_under">{{$ledger_data['group_name']['current_value']}}</label>
                                </div>    
                            </div>
                            <br><br><br>
                            <div id="ledgerData">
                                @php
                                $flag=0;
                                @endphp
                                <div class="col-md-6">
                                    {{-- display data section wise --}}
                                    @foreach ($sections as $section)
                                        @if ($flag == 0 && $section->part_name=="b")
                                            @php
                                                $flag++;   
                                            @endphp
                                            {{-- creating first div.col-md-6 --}}
                                            </div>
                                            <div class="col-md-6">
                                        @endif
                                        @if (count($led_data[$section->section_no])>0)
                                            {{-- adding section heading --}}
                                            <h4>{{$section->name}}</h4>
                                            @foreach ($led_data[$section->section_no] as $key => $value) 
                                                {{-- displaying data row wise. --}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>{{$value['title']}}</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if (isset($value['current_value']))
                                                            
                                                        <label>{{$value['current_value']}}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <br>
                            <br>
                            <div class="row">                                
                                <div class="col-md-8 form-group">
                                    <div class="col-md-3">  
                                        <label for="opening_balance">{{__('accounting/ledger.opening balance')}}</label>
                                    </div>
                                    <div class="col-md-3">  
                                        <label for="opening_balance">{{abs($ledger_data['opening_balance']['current_value'])}}</label>
                                    </div>
                                    <div class="col-md-3">       
                                        <label for="opening_balance">{{$ledger_data['opening_balance']['current_value']>=0?'Cr':'Dr'}}</label>
                                    </div>
                                </div>
                            </div>
                            <br>
                    </div>
                </div>
            </div>                 
    </section><!--end of section-->
@endsection