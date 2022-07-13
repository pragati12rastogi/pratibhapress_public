
@extends($layout)

@section('title', __('accounting/group.create title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/group.create title')}}</i></a></li>
@endsection
@section('css')

@endsection

@section('js')


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
                <div class='box box-default'>  
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                    <br>
                    <div class="container-fluid wdt">
                            <div class="row">
                                <div  class="col-md-6">
                                    <label for="group_name">{{__('accounting/group.groupname')}}</label>
                                </div>
                                <div  class="col-md-6">
                                        <label for="">{{$group->name}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                        <label for="group_alias_name">{{__('accounting/group.groupaliasname')}}</label>
                                </div>
                                <div  class="col-md-6">
                                                <label for="">{{$group->alias}}</label>
                                        </div>
                                </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="group_under">{{__('accounting/group.groupunder')}}</label>
                                </div>
                                <div class="col-md-6">
                                                <label for="">{{$group->group_under}}</label>
                            </div>
                        </div>
                        @if (strtolower($group->group_under) == "primary")
                                
                                <div id="nature_div" class="row" >
                                        <div class="col-md-6">
                                                <label for="group_nature">{{__('accounting/group.nature')}}</label>
                                        </div>
                                        <div class="col-md-6">
                                                <label for="">{{$group->nature_of_acc}}</label>
                                        </div>
                                </div>
                                @if (strtolower($group->nature_of_acc) == "expenses" || strtolower($group->nature_of_acc) == "income")
                                
                                        <div id="gross_profit_div" class="row" >
                                                <div class="col-md-6">
                                                        <label for="gross_profit">{{__('accounting/group.effectGrossProfit')}}</label>
                                                </div>
                                                <div class="col-md-6">
                                                        <label for="">{{$group->affect_gross_profit==0?'No':'Yes'}}</label>
                                                </div>
                                        </div>
                                @endif
                        @endif
                                <div class="row">
                                        <div class="col-md-6">
                                                <label for="group_behave">{{__('accounting/group.groupbehavelikesubledger')}}</label>
                                        </div>
                                        <div class="col-md-6">
                                                        <label for="">{{$group->sub_ledger==0?'No':'Yes'}}</label>
                                                </div>
                                        </div>
                                <div class="row"> 
                                        <div class="col-md-6">
                                                <label for="group_nett">{{__('accounting/group.groupnettdebit/creditbalance')}}</label>
                                        </div>
                                        <div class="col-md-6">
                                                <label for="">{{$group->nett_debit_credit_reporting==0?'No':'Yes'}}</label>
                                        </div>
                                </div>
                                <div class="row"> 
                                    <div class="col-md-6">
                                        <label for="group_calc">{{__('accounting/group.groupuseforcalculation')}}</label>
                                </div>
                                <div class="col-md-6">
                                        <label for="">{{$group->use_for_calculation==0?'No':'Yes'}}</label>
                                </div>
                                <div class="row"> 
                                    <div class="col-md-6">
                                        <label for="group_method">{{__('accounting/group.groupmethodtoallocate')}}</label>
                                        </div>
                                    <div class="col-md-6">
                                                <label for="">{{$group->method_to_allocate==0?'Not Applicable':($group->method_to_allocate==1?'Appropiate by Quantity':'Appropiate by Value')}}</label>
                                        </div>
                                </div>
                                <hr>
                    </div>
                </div>
            </div>                 
    </section><!--end of section-->
@endsection


