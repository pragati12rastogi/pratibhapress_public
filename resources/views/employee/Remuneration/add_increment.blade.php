@extends($layout)
 
@section('title', 'Add Increment')
 
@section('user', Auth::user()->name)
 
@section('breadcrumb')
    
    <li><a href="#"><i class=""></i>Add Increment</a></li>

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
function change_type(type){
    
    if(type.value == 'cr'){
        $("#change_type").text('Increment ');
    }else if(type.value == 'dr'){
        $("#change_type").text('Decrement ');
    }else{
        $("#change_type").text('');
    }
}
function cat_change(data){
   if(data.value == 'A basic' || data.value == 'B basic'){
        $(".show_this").show();
    }else {
        $(".show_this").hide();
    } 
}
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
        <form action="/remuneration/increment/add" method="POST" id="increment_form" enctype="multipart/form-data">
        @csrf
            <div class='box box-default'>  
                <br><br>
                <div class="container-fluid wdt" style="padding-bottom:20px ">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('emp_name') ? 'has-error' : ''}}">
                                    <label for="">Employee name <sup>*</sup></label>
                                    <select name="emp_name" class="select2 input-css emp_name" >
                                        <option value="">Select Employee Name</option>
                                        @foreach($employee as $emp)
                                            <option value="{{$emp->id}}">{{$emp->name}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('emp_name', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('month_name') ? 'has-error' : ''}}">
                                    <label for="">Increment applicable from which month<sup>*</sup></label>
                                    <input name="month_name" class="input-css month_name" id="month_name" autocomplete="off">
                                    <!-- <select name="month_name" class="select2 input-css month_name">
                                        <option value="">Select Month</option>
                                        <option value="January">January</option>
                                        <option value="Feburary">Feburary</option>
                                        <option value="March">March</option>
                                        <option value="April">April</option>
                                        <option value="May">May</option>
                                        <option value="June">June</option>
                                        <option value="July">July</option>
                                        <option value="August">August</option>
                                        <option value="September">September</option>
                                        <option value="October">October</option>
                                        <option value="November">November</option>
                                        <option value="December">December</option>
                                    </select> -->
                                    {!! $errors->first('month_name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('amount_type') ? 'has-error' : ''}}">
                                    <label for="">Type<sup>*</sup></label>
                                    <select name="amount_type" class="select2 input-css amount_type" onchange="change_type(this)">
                                        <option value="">Select Type</option>
                                        <option value="cr">Increment</option>
                                        <option value="dr">Decrement</option>
                                    </select>
                                    {!! $errors->first('amount_type', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('amount') ? 'has-error' : ''}}">
                                    <label for=""><span id="change_type"></span>Amount<sup>*</sup></label>
                                    <input type="number" name="amount" min="0" class="input-css amount" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" >
                                    {!! $errors->first('amount', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('incr_cat') ? 'has-error' : ''}}">
                                    <label for="">Increment category<sup>*</sup></label>
                                    <select name="incr_cat" class="select2 input-css incr_cat" id="incr_cat" onchange="cat_change(this)">
                                        <option value="">Select Increment category</option>
                                        <option value="A basic">Salary A Basic</option>
                                        <option value="B basic">Salary B Basic</option>
                                        <option value="A overtime">Overtime eligible on salary A</option>
                                        <option value="B overtime">Overtime eligible on salary B</option>
                                    </select>
                                    {!! $errors->first('incr_cat', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('incr_adjust_c') ? 'has-error' : ''}} show_this" style="display: none">
                                    <label for="">Increment adjustment in salary C<sup>*</sup></label>
                                    <select name="incr_adjust_c" class="select2 input-css incr_adjust_c" id="incr_adjust_c" style="width: 100%">
                                        <option value="">Select Adjustment in salary C</option>   
                                        <option value="basic">Basic</option>   
                                        <option value="da">DA</option>   
                                        <option value="hra">HRA</option>   
                                        <option value="conveyance">Conveyance Allowance</option>   
                                        <option value="telephone">Telephone allowance</option>   
                                        <option value="other">Other allowance</option>
                                        <option value="NA">NA</option>   
                                    </select>
                                    {!! $errors->first('incr_adjust_c', '<p class="help-block">:message</p>') !!}
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
 

