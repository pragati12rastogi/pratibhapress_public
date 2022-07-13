
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
</style>
@endsection

@section('js')
<script src="/js/accounting/company.js"></script>

<script>
    // add select2 to select boxes with class 'input-css'
$(document).ready(
    function(){
        // add select2 to select element with class 'input-css'
        $('.select').select2({
                containerCssClass:"input-css"
            });
        $("#name").change(function(){
            var value = $(this).val();
            $("#MailName").val(value);
        })
    }
)
</script>

@endsection
@section('main_section')
    <section class="content">
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
    
        
        <form method="POST" action="/accounting/company/create" id="create_company_form">
            <div class="box-header with-border">
                <div class='box box-default'>  <br>
                    <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/company.companyCreate')}}</h2><br><br>
                    <div class="container-fluid wdt">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.name')}}</label>
                                            <input type="text" class="form-control input-css name" value="{{$errors->any()?($errors->has('name') ?'':old('name')):''}}" name="name" id="name">
                                        </div>
                                    </div>
                                    <br>
                                    
                                    <h2 class="box-title" style="font-size: 20px;margin-left:20px">{{__('accounting/company.MailContact')}}</h2><br><br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="">{{__('accounting/company.mailName')}}</label>
                                                <input type="text" class="form-control input-css MailName" value="{{$errors->any()?($errors->has('MailName') ?'':old('MailName')):''}}" name="MailName" id="MailName">
                                            </div>
                                        </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.address')}}</label>
                                            <input type="text" class="form-control input-css address" value="{{$errors->any()?($errors->has('address') ?'':old('address')):''}}" name="address" id="address">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.statutoryCompliance')}}</label>
                                            <select class="form-control input-css select country CompCountry"  name="CompCountry" id="CompCountry">
                                               <option value="default">Select Country</option>                                                    
                                                    
                                                @foreach ($country as $key)
                                                    <option value="{{$key->id}}" {{$errors->any()?($errors->has('CompCountry') ?'':($key->id==old('CompCountry')?'':'selected="selected"')):''}}>{{$key->name}}</option>                                                    
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-md-12">
                                        <label for="">{{__('accounting/company.state')}}</label>
                                        <select class="form-control input-css state select CompState" name="CompState" id="CompState">
                                            <option value="default">Select State</option> 
                                        </select>                                                   
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.pincode')}}</label>
                                            <input type="number" class="form-control input-css pincode" value="{{$errors->any()?($errors->has('pincode') ?'':old('pincode')):''}}" name="pincode" id="pincode">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.tele')}}</label>
                                            <input type="number" class="form-control input-css telephone" value="{{$errors->any()?($errors->has('telephone') ?'':old('telephone')):''}}" name="telephone" id="telephone">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.mail')}}</label>
                                            <input type="email" class="form-control input-css email" value="{{$errors->any()?($errors->has('email') ?'':old('email')):''}}" name="email" id="email">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <div class="row"></div>
                                    <h2 class="box-title" style="font-size: 20px;margin-left:20px">{{__('accounting/company.companyDet')}}</h2><br><br>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.currencySymbol')}}</label>
                                            <input type="text" class="form-control input-css CurrencySymbol" value="{{$errors->any()?($errors->has('CurrencySymbol') ?'':old('CurrencySymbol')):''}}" name="CurrencySymbol" id="CurrencySymbol">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.maintain')}}</label>
                                            <select class="form-control input-css select  maintain" value="{{$errors->any()?($errors->has('maintain') ?'':old('maintain')):''}}" name="maintain" id="maintain">
                                                <option value="1">Accounts only</option>
                                                <option value="1">Accounts and Inventory</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.fincanceYear')}}</label>
                                            <input type="date" class="form-control input-css YearFrom" value="{{$errors->any()?($errors->has('YearFrom') ?'':old('YearFrom')):''}}" name="YearFrom" id="YearFrom">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">{{__('accounting/company.BookYear')}}</label>
                                            <input type="date" class="form-control input-css BookBegin" value="{{$errors->any()?($errors->has('BookBegin') ?'':old('BookBegin')):''}}" name="BookBegin" id="BookBegin">
                                        </div>
                                    </div>
                                 </div>
                                
                            </div>
                            <br>
                        </div>
                    </div>
                </div>  
                <div class="box-header with-border">
                    <div class='box box-default'>  <br>
                        <h2 class="box-title" style="font-size: 28px;margin-left:20px">{{__('accounting/company.currencyInfo')}}</h2><br><br><br>
                        <div class="container-fluid wdt">
                            {{-- create currency form using AcCompanySetting table --}}
                            @php $i=0; @endphp
                            @foreach ($setting as $key)
                                @if($i%2==0)
                                {{-- creating new row if a row has 2 elements --}}
                                    <div class="row">   
                                @endif
                                @php $i=$i+1; @endphp
                                <div class="col-md-6">
                                    <label for="">{{$key->message}}</label>
                                    @if ( $key->type=='email'|| $key->type=='text' || $key->type=='number')
                                        <input class="form-control input-css userSetttxt" type="{{$key->type}}" value="{{$errors->any()?($errors->has($key->name) ?$key->value:old($key->name)):$key->value}}" name="{{$key->name}}">
                                    @elseif($key->type=='option')
                                        <select class="select form-control input-css userSettOpt" name="{{$key->name}}">
                                            @for ($j = 0; $j < count(explode(',',$key->v)); $j++)
                                                <option value="{{explode(',',$key->db_value)[$j]}}" {{$errors->any()?($errors->has($key->name) ?(explode(',',$key->db_value)[$j]==$key->value?'selected=selected':''):(old($key->name)==explode(',',$key->db_value)[$j]?'selected=selected':'')):(explode(',',$key->db_value)[$j]==$key->value?'selected=selected':'')}} >{{explode(',',$key->v)[$j]}}</option>                                                
                                            @endfor
                                        </select>
                                        <br>
                                    @endif
                                </div>
                                @if($i%2==0)
                                    </div>
                                @endif
                            @endforeach
                            <br>
                            <br>
                            <br>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" >
                            </div>

                        </div>
                    </div>
                </div>
        </form>
    </section><!--end of section-->
@endsection


