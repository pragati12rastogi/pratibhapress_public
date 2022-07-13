@extends($layout)
 
@section('title', 'Advance Create')
 
@section('user', Auth::user()->name)
 
@section('breadcrumb')
    
    <li><a href="#"><i class=""></i>Advance Create</a></li>

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
</style>
@endsection

@section('js')
<script src="/js/Employee/advance.js"></script>
<script>
    $current_d = new Date();
    $(document).ready(function(){
        $("#given_date").datepicker({
            format: "dd-mm-yyyy",
            startDate:$current_d
        })
    })
 
</script>
@endsection
@section('main_section')
    <section class="content">
                <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                </div>
        <!-- Default box -->
        <form action="/advance/create" method="POST" id="advance_form" enctype="multipart/form-data">
        @csrf
            <div class='box box-default'>  
                <br><br>
                <div class="container-fluid wdt" style="padding-bottom:20px ">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('emp_name') ? 'has-error' : ''}}">
                                    <label for="">Employee Name <sup>*</sup></label>
                                    <select name="emp_name" class="select2 input-css emp_name" >
                                        <option value="">Select Employee Name</option>
                                        @foreach($employee as $emp)
                                            <option value="{{$emp->id}}">{{$emp->name."(".$emp->employee_number.")"}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('emp_name', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('adv_amt') ? 'has-error' : ''}}">
                                    <label for="">Requested Amount<sup>*</sup></label>
                                    <input name="adv_amt" class=" input-css adv_amt" id="adv_amt">
                                    
                                    {!! $errors->first('adv_amt', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('given_date') ? 'has-error' : ''}}">
                                    <label for="">Date<sup>*</sup></label>
                                    <input name="given_date" class=" input-css given_date" id="given_date">
                                    {!! $errors->first('given_date', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="col-md-6 {{ $errors->has('deduct') ? 'has-error' : ''}}">
                                    <label for="">Deduction Installments<sup>*</sup></label>
                                    <input type="number" name="deduct" min="0" class="input-css deduct" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" >
                                    {!! $errors->first('deduct', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('adv_reason') ? 'has-error' : ''}}">
                                    <label for="">Advance for reason<sup>*</sup></label>
                                    <textarea name="adv_reason" class="input-css adv_reason" id="adv_reason"></textarea> 
                                    {!! $errors->first('adv_reason', '<p class="help-block">:message</p>') !!}
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
 

