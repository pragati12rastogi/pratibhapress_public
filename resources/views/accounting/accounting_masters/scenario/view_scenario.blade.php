
@extends($layout)

@section('title', __('accounting/scenario.view title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/scenario.view title')}}</i></a></li>
 @endsection
@section('css')

@endsection

@section('js')
<script>
$(document).ready(function(){
    $('.select').select2();
    $('#include').val("{{$sd->include_voucher_type}}").trigger("change");
    $('#exclude').val("{{$sd->exclude_voucher_type}}").trigger("change");
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
        <!-- Default box -->
            @if(in_array(1, Request::get('userAlloweds')['section']))
            @endif
      

            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <div class="container-fluid wdt">
                            <div class="row">
                                <div class="col-md-4">
                
                                    <label for="name">{{__('accounting/scenario.name')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="name">{{$sd->name}}</label>
                                </div>
                            </div>
                                @if ($show_alias==1)  
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="alias">{{__('accounting/scenario.alias')}}</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label id="alias">{{$sd->alias}}</label>
                                        </div>
                                    </div>                          
                                @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="include_actuals">{{__('accounting/scenario.include actuals')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="include_actuals">{{$sd->include_actuals==1?'Yes':'No'}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="exclude_forex">{{__('accounting/scenario.exclude forex')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="exclude_forex">{{$sd->exclude_forex==1?'Yes':'No'}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="include">{{__('accounting/scenario.include')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="include">{{$sd1->include_voucher_type}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="exclude">{{__('accounting/scenario.exclude')}}</label>
                                </div>
                                <div class="col-md-4">
                                    <label id="exclude">{{$sd2->exclude_voucher_type}}</label>
                                </div>
                            </div>
                            <br>
                            
                            <br>
                        </div>
                </div>
            </div>                 
    </section><!--end of section-->
@endsection