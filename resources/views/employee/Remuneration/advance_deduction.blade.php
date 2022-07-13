@extends($layout)
 
@section('title', 'Advance Deduction')
 
@section('user', Auth::user()->name)
 
@section('breadcrumb')
    
    <li><a href="#"><i class=""></i>Advance Deduction</a></li>

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
function callfn(emp){
	
	var empid = emp.value;
	$('#ajax_loader_div').css('display','block');
  	$.ajax({
      type:'get',
      url: "/advance/deduction/employee/record",
      data: {'empid':empid},
      contentType: "application/json",
      dataType: "json",
      success:function(result) {
      	
        $('#ajax_loader_div').css('display','none');
        console.log(result);
        $("#advance_pending").text(result.advance_balance);
        $("#adv_id").val(result.id);
        donchange();
      }
    });
}
function donchange(){
	
	$get_adv_text=	$("#advance_pending").text();
	$("#pen_adv_amt").val($get_adv_text);
}
function amt_valdation(amt){
   
	var amount= amt.value;
	var max_amt = $("#pen_adv_amt").val();
	if(parseInt(max_amt) < parseInt(amount)){
		$("#notmatch").text("Deduction Amount can't be greater then Pending");
		$("#deduct_adv").val(0);
	}

	$dect_amt = $("#deduct_adv").val();
	$adv_amt = $("#advance_pending").text();

	$balance = parseInt($adv_amt)-parseInt($dect_amt);
	$("#left_total").text("Rs. " +$balance);
} 
</script>
@endsection
@section('main_section')
    <section class="content">
                <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                </div>
        <!-- Default box -->
        <form action="/advance/deduction" method="POST" id="advance_form" enctype="multipart/form-data">
        @csrf
            <div class='box box-default'>  
                <br><br>
                <div class="container-fluid wdt" style="padding-bottom:20px ">
                    <div class="row">
                        <div class="col-md-12">
                            <span id="advance_pending" hidden="hidden"></span>
                            <input type="text" id="adv_id" name="adv_id" hidden="hidden">
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('emp_name') ? 'has-error' : ''}}">
                                    <label for="">Employee name <sup>*</sup></label>
                                    <select name="emp_name" class="select2 input-css emp_name" onchange="callfn(this)">
                                        <option value="">Select Employee Name</option>
                                        @foreach($employee as $emp)
                                            <option value="{{$emp->emp_id}}">{{$emp->emp_name}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('emp_name', '<p class="help-block">:message</p>') !!}
                                </div>

                                <div class="col-md-6 {{ $errors->has('pen_adv_amt') ? 'has-error' : ''}}">
                                    <label for="">Pending Advance amount<sup>*</sup></label>
                                    <input type="text" name="pen_adv_amt" class=" input-css pen_adv_amt" id="pen_adv_amt" readonly="readonly" onblur="donchange()" style="pointer-events: none;" disabled="disabled ">
                                    {!! $errors->first('pen_adv_amt', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('deduct_adv') ? 'has-error' : ''}}">
                                    <label for="">Deduct Advance<sup>*</sup></label>
                                    <input type="number" name="deduct_adv" class=" input-css deduct_adv" id="deduct_adv" onblur="amt_valdation(this)">
                                    <label class="error" id="notmatch"></label>
                                    {!! $errors->first('deduct_adv', '<p class="help-block">:message</p>') !!}
                                </div>
                                
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-6 {{ $errors->has('left_total') ? 'has-error' : ''}}">
                                    <label for="" style="display: inline;">Balance Advance :</label>
                                    <span id ="left_total" name="left_total" class="margin"></span>
                                    {!! $errors->first('left_total', '<p class="help-block">:message</p>') !!}
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
 

