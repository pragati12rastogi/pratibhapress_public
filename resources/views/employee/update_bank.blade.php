@extends($layout)

@section('title', 'Bank Details')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Employee Bank Details</a></li>
    <style>
        .nav1>li>a {
            position: relative;
            display: block;
            padding: 10px 34px;
            background-color: white;
            margin-left: 10px;
        }
        /* .nav1>li>a:hover {
            background-color:#87CEFA;
        
        } */
        </style>
@endsection
@section('js')
<script src="/js/Employee/bank.js"></script>
<script>

</script>
@endsection
@section('main_section')
    <section class="content">
                <div id="app">
                                @include('sections.flash-message')
                                @yield('content')
                            </div>
        <!-- Default box -->
                        <form action="/employee/bank/update/{{$id}}" method="POST" id="form">
        @csrf
         @include('layouts.employee_tab')
              <br>
              
<div class="box-header with-border">
        <div class='box box-default'>  <br>
                    <h3 class="box-title" style="font-size: 28px;margin-left:20px">Bank Details</h3><br><br><br>
                <div class="container-fluid wdt">
                    @if(isset($employee))
                        <div class="row">
                                <div class="col-md-3 ">
                                        <label>{{__('layout.update_reason')}}<sup>*</sup></label>
                                        <input type="text" name="update_reason" required="" class="input-css" id="update_reason">
                                        {!! $errors->first('update_reason', '<p class="help-block">:message</p>') !!}
                                </div><!--col-md-4-->
                        </div>
                        <br><br>@endif
                        <div class="row">
                                
                                <div class="col-md-6 {{ $errors->has('bank_status') ? 'has-error' : ''}}">
                                <label for="">Is Bank Account Is New Or Existing ?<sup>*</sup></label>
                                        <div class="col-md-2">
                                                <div class="radio">
                                                <label><input autocomplete="off" type="radio" class="bank_status" {{isset($employee)==1?($employee->bank_status=="Existing" ? 'checked=checked' : ''):''}} value="Existing" name="bank_status">Existing</label>
                                                </div>
                                        </div>
                                        <div class="col-md-2">
                                                <div class="radio">
                                                <label><input autocomplete="off" type="radio" class="bank_status" {{isset($employee)==1? ($employee->bank_status=="New" ? 'checked=checked' : ''):''}} value="New" name="bank_status">New</label>
                                                </div>
                                        </div>
                                        <label id="bank_status-error" class="error" for="bank_status"></label>
                                        {!! $errors->first('bank_status', '<p class="help-block">:message</p>') !!}
                                </div>
                        </div><br>
                        <div class="row">
                        <div class="col-md-4 {{ $errors->has('acc_name') ? 'has-error' : ''}}">
                                <label for="">Account Name<sup>*</sup></label>
                                <input type="text" name="acc_name" value="{{isset($employee)==1?($employee->acc_name):''}}" id="" class="input-css acc_name">
                                {!! $errors->first('acc_name', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-4 {{ $errors->has('acc_number') ? 'has-error' : ''}}">
                                <label for="">Account Number<sup>*</sup></label>
                                <input type="number" min="0" step="none" value="{{isset($employee)==1?($employee->acc_number):''}}" name="acc_number" id="" class="input-css acc_number">
                                {!! $errors->first('acc_number', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-4 {{ $errors->has('acc_ifsc') ? 'has-error' : ''}}">
                                <label for="">Account IFSC Code<sup>*</sup></label>
                                <input type="text" name="acc_ifsc" id="" value="{{isset($employee)==1?($employee->acc_ifsc):''}}" class="input-css acc_ifsc">
                                {!! $errors->first('acc_ifsc', '<p class="help-block">:message</p>') !!}
                        </div>
                        </div><br><br>
                </div>
        </div>
</div>
        <div class="row">
                        <div class="col-md-12">
                                <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                        </div>
                </div><br><br>
        </form>
      
      </section>
@endsection
