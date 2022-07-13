
@extends($layout)

@section('title', __('accounting/scenario.create title'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/scenario.create title')}}</i></a></li>
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
<script>
$(document).ready(function(){
    // add select2 to select element with class 'input-css'
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
                        <form method="POST" action="/accounting/scenario/create" id="asn_form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/scenario.name')}}</label>
                                    <input type="text" name="name" id="name" class="input-css form-control name">
                                </div>
                                @if ($show_alias==1)                                    
                                    <div class="col-md-6">
                                        <label for="">{{__('accounting/scenario.alias')}}</label>
                                        <input type="text" name="alias" id="alias" class="input-css form-control alias">
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/scenario.include actuals')}}</label>
                                    <select  name="include_actuals" id="include_actuals" class="select input-css form-control include_actuals">
                                        <option value="0">No</option>
                                        <option value="1" selected="selected">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/scenario.exclude forex')}}</label>
                                    <select  name="exclude_forex" id="exclude_forex" class="select input-css form-control exclude_forex">
                                        <option value="0" selected="selected">No</option>
                                        <option value="1" >Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/scenario.include')}}</label>
                                    <select multiple name="include[]" id="include" class="select input-css form-control include">
                                        <option value="default"> select Option</option>
                                        @foreach ($voucher_type as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">{{__('accounting/scenario.exclude')}}</label>
                                    <select multiple name="exclude[]" id="exclude" class="select input-css form-control exclude">
                                        <option value="default"> select Option</option>
                                        @foreach ($voucher_type as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>
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


