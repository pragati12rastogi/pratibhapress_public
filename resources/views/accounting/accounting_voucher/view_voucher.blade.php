
@extends($layout)

@section('title', __(''))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('')}}</i></a></li>
 @endsection
@section('css')
<style>
    /* css for adding differentiation between focused and non focused element */
    .input-css:focus, .select2-search__field:focus{
        background-color: #FAEF9F !important;
    }
    .lbl_in_space{
        display: inline;margin-right:8px;
    }
    
</style>
@endsection

@section('js')
<script src="/js/accounting/voucherEntry.js"></script>

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
                <div class="row">
                    <div class="col-md-5">
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/voucher.AccountingvoucherEntry')}}</h2>
                    </div>
                    <div class="col-md-2">
                    </div>
                    <form method="POST" novalidate  action="/accounting/voucher/entry" id="voucher_entry_form">

                        <div class="col-md-5">
                            {{-- <button class="btn bg-red btn-sm" onclick="post_dated()">Post Dated</button> --}}
                            {{-- <button class="btn bg-red btn.sm" onclick="change_date()">Change Date</button> --}}
                            {{-- modal form for change date --}}
                            <div id="myModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Change Date</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="current_date">Date :</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control input-css datepicker1" value="" name="date" id="current_date">
                                                </div>
                                            </div>
                                            <input type="button" onclick="change_date_api()" value="Change Date" class="btn btn-sm btn-success">
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br><br><br>
                        <div class="container-fluid wdt">
                            @csrf
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="lbl_in_space">{{__('accounting/voucher.voucherEntryFor')}}</label>
                                <span> {{$voucherType}}</span>
                                </div>
                                
                                 <div class="col-md-offset-5 col-md-3">
                                    <!-- <div class="" id="voucherNumberName" class="voucherNumberName">         -->
                                        <label class="lbl_in_space">{{__('accounting/voucher.voucherNumber')}}</label>
                                    <span>{{$voucherNumber}}</span>
                                    <!-- </div> -->
                                    {{-- <label>{{__('accounting/voucher.voucherFormType')}}</label> --}}
                                    {{-- different form types --}}
                                    {{-- <select class="select input-css form-control form_type" name="form_type" id="form_type">
                                        <option value="default" selected="selected">Default Form Type</option>
                                        <option class="form_type_class" value="0">As Voucher</option>
                                        <option class="form_type_class" value="1">As Invoice</option>
                                        <option class="form_type_class" value="2">As Journal</option>
                                        <option class="form_type_class" value="3">As Memorandum</option> (not required)
                                    </select> --}}
                                </div> 
                            </div>
                            <div class="row">
                                {{-- row displaying date and voucher number  --}}
                                <div class="col-md-4">
                                    
                                    <div class="col-md-4" id="voucherNumber"></div>
                                    <div class="col-md-4" id="voucherNumber1"></div>
                                </div>
                                {{-- post dated div --}}
                                <div class="col-md-4">
                                    {{-- <div id="post_dated_div" style="display:none; text-align:center">
                                        <h3>Post Dated</h3>
                                    </div> --}}
                                </div>
                                <div class="col-md-4" style="text-align:right;">
                                    {{-- <div id="post_dated_enabled_div" style="display:none">
                                        <div class="col-md-4">
                                            {{__('accounting/voucher.date')}}
                                        </div>
                                        <div class="col-md-8">
                                        <input type="text" class="form-control input-css datepicker1" value=""  name="voucherDate" id="voucherDate">
                                    </div>
                                    </div>
                                    <input type="hidden" name="is_post_dated" value="0" id="is_post_dated">
                                    <div id="post_dated_disabled_div" style=" display:inline-block">
                                                {{__('accounting/voucher.date')}}
                                        </div> --}}
                                </div>
                                <div class="row">
                                    {{-- effective date div --}}
                                        <div style="text-align:right;display:none;" class="col-md-12 effectiveDateDiv" >Effective Date : {{session('Active_date')}}</div>
                                </div>
                            </div>

                            <br>
                            {{-- header div --}}
                            <div class="voucherEntryForm" id="voucherEntryFormAccount">
                             @php print_r($voucher_account_layout)
                             @endphp
                            </div>
                            <br>
                            <hr>
                            {{-- content div --}}
                            <div class="voucherEntryForm" id="voucherEntryFormParticular">
                                @php print_r($voucher_particular_layout)
                                @endphp
                            </div>
                            <div class="form-group">
                                <!-- <input class="btn btn-success" type="submit">     -->
                            </div>    
                        </div>
                    </form>
                </div>
            </div>   
        </div>
    </section><!--end of section-->
@endsection


