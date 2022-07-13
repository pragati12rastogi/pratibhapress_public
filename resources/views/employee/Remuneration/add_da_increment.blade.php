@extends($layout)
 
@section('title', 'Add DA Increment')
 
@section('user', Auth::user()->name)
 
@section('breadcrumb')
    
    <li><a href="#"><i class=""></i>Add DA Increment</a></li>

@endsection
@section('css')
<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
    .datepicker .prev,.datepicker .next{
        opacity: 0;
    }
</style>
@endsection

@section('js')
<script src="/js/Employee/increment.js"></script>
<script>
$current_d = new Date();
$('#month_name').datepicker({
    format: "MM",
    weekStart: 1,
    orientation: "bottom",
    keyboardNavigation: false,
    viewMode: "months",
    minViewMode: "months",
    autoclose: true,
    startDate:$current_d
});    
</script>
@endsection
@section('main_section')
    <section class="content">
                <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                </div>
        <!-- Default box -->
        <form action="/da/increment/add" method="POST" id="da_increment_form" enctype="multipart/form-data">
        @csrf
            <div class='box box-default'>  
                <br><br>
                <div class="container-fluid wdt" style="padding-bottom:20px ">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="row">
                                
                                <div class="col-md-6 {{ $errors->has('month_name') ? 'has-error' : ''}}">
                                    <label for="">Month Applicable From<sup>*</sup></label>
                                    <input name="month_name" class="input-css month_name" id="month_name" autocomplete="off">
                                    
                                    {!! $errors->first('month_name', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('da_cat') ? 'has-error' : ''}}">
                                    <label for="">DA Category<sup>*</sup></label>
                                    <select name="da_cat" class="select2 input-css da_cat">
                                        <option value="">Select Type</option>
                                        <option value="skilled">Skilled</option>
                                        <option value="unskilled">Unskilled</option>
                                        <option value="semi-skilled">Semi-Skilled</option>
                                    </select>
                                    {!! $errors->first('da_cat', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('sal_cat') ? 'has-error' : ''}}">
                                    <label for="">Salary Category<sup>*</sup></label>
                                    <select name="sal_cat" class="select2 input-css sal_cat" id="sal_cat">
                                        <option value="">Select category</option>
                                        <option value="SalaryA">Salary A</option>
                                        <option value="SalaryC">Salary C</option>
                                    </select>
                                    {!! $errors->first('sal_cat', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('amount') ? 'has-error' : ''}}">
                                    <label for="">Amount<sup>*</sup></label>
                                    <input type="number" name="amount" min="0" class="input-css amount" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" >
                                    {!! $errors->first('amount', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br><br>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        
            <div class="row">
                <div class="col-md-12">
                     <input type="submit" style="float:right" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </form>
      
      </section>
@endsection
 

