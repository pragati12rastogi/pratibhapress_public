
@extends($layout)

@section('title', __('accounting/a'))

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li><a href="#"><i class="">{{__('accounting/a')}}</i></a></li>
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
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/a')}}</h2><br><br><br>
                    <div class="container-fluid wdt">
                        <form method="POST" action="/accounting/" id="asn_form">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                </div>
                            </div>
                            <div class="form-group">    
                                <input type="submit" class=" btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>                 
    </section><!--end of section-->
@endsection


