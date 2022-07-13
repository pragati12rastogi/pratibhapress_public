@extends($layout)

@section('title', 'Assign Assets')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
<li>
    <a href="{{url('/master/assets/assign/employee/list')}}">Assign Assets List</a>
</li>
    <li><a href=""><i class=""></i>Assign Assets</a></li>
@endsection
@section('js')
<script src="/js/Employee/assets.js"></script>
<script>
$(document).ready(function(){
      var date=  new Date();
      var dd=  date.getDate();
      var mm = date.getMonth()+1;
      var yy = date.getFullYear();
    $("#from_date").val(dd+"-"+mm+"-"+yy);
    $("#from_date").datepicker({
        startDate:'today',
        format: 'd-m-yyyy'
    });
});
 function filter_assets_code(i){
     
    var assets=$(i).val();
    $('#ajax_loader_div').css('display','block');
    $.ajax({
        type:"GET",
        url:"/master/filter/assetcode/api/",
        data:{'assets':assets},
        success: function(result){
            if (result) {
                        $("#code").empty();
                        $("#code").append(' <option value="">--Select Code--</option>');
                        $.each(result, function(key, value) {
                            $("#code").append('<option value="' + key + '">' + value + '</option>');
                        });
                        $('#ajax_loader_div').css('display','none');
                    }
        }
    })
 }
// function checktime(){
//     var datefrom = $("#from_date").val();
//     var dateto = $("#to_date").val();
//     if(datefrom > dateto){
//         $("#to_date").val(datefrom);
//     }else{
       
//     }
// }

var startDate=null;
		var endDate=null;
		$(document).ready(function(){
			$('#from_date').datepicker()
				.on('changeDate', function(ev){
					startDate=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
					if(endDate!=null&&endDate!='undefined'){
						if(endDate<startDate){
								$("#from_date").val("");
						}
					}
				});
			$("#to_date").datepicker()
				.on("changeDate", function(ev){
					endDate=new Date(ev.date.getFullYear(),ev.date.getMonth(),ev.date.getDate(),0,0,0);
					if(startDate!=null&&startDate!='undefined'){
						if(endDate<startDate){
							$("#to_date").val("");
						}
					}
				});
		});
// function gen(){
    
//     var cat = $(".assets_category").val();
//     var code = $("#code").val();
//     var emp =$(".assets_emp").val();
//     var from = $(".assets_from_date").val();

//     if(cat=="" || code=="" || emp == "" || from==""){
       
//         $(".err").text("fill all required field first").show();
//         return false;
//     }else{
//         $(".err").hide();

//         //     $.ajax({
//         //     type:"GET",
//         //     url:"",
//         //     // data:{'cat':cat,
//         //     // 'code' :code,
//         //     // 'emp' :emp,
//         //     // 'from' :from
//         //     // },
//         //     success: function(result){
//         //         return result;
//         //     }
//         // });
//     }
// }

</script>
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div id="app">
                @include('sections.flash-message')
                @yield('content')
            </div>
       <form action="/master/assets/assign/employee" method="POST" id="form" enctype="multipart/form-data">
        @csrf

       <div class="box box-header">
           <br>

            <div class="row" >
                <div class="col-md-6 {{ $errors->has('assets_category') ? ' has-error' : ''}}">
                    <label for="">Assets Category <sup>*</sup></label>
                    <select class="input-css assets_category select2" style="padding-top:2px" name="assets_category" onchange="filter_assets_code(this)">
                        <option value="">--Select Category--</option>
                        @foreach($asset_category as $ac)
                            <option value="{{$ac->ac_id}}">{{ $ac->category_name}}</option>
                        @endforeach
                    </select>
                    {!! $errors->first('assets_category', '<p class="help-block">:message</p>') !!} 
                </div>
                <div class="col-md-6 {{ $errors->has('assets_code') ? ' has-error' : ''}}">
                    <label for="">Assets Codes <sup>*</sup></label>
                    <select class="input-css assets_code select2" id="code" style="padding-top:2px" name="assets_code" >
                        <option value="">--Select Code--</option>
                    </select>
                    {!! $errors->first('assets_code', '<p class="help-block">:message</p>') !!} 
                </div>
            
            </div><br><br>
            <div class="row" >
                <div class="col-md-6 {{ $errors->has('assets_emp') ? ' has-error' : ''}}">
                    <label for="">Employees<sup>*</sup></label>
                    <select class="input-css assets_emp select2" style="padding-top:2px" name="assets_emp" >
                        <option value="">--Select Employee--</option>
                        @foreach($emp_for_asset as $emp){
                        <option value="{{$emp->id}}">{{ $emp->name.'('.$emp->employee_number.')'}}</option>
                        @endforeach
                    </select>
                    {!! $errors->first('assets_emp', '<p class="help-block">:message</p>') !!} 
                </div>
                <div class="col-md-6 ">
                    <label for="">Duration <sup></sup></label>
                    <div class="col-md-6 {{ $errors->has('assets_from_date') ? ' has-error' : ''}}">
                        <label>From : <sup>*</sup></label>
                        <input type="text" name="assets_from_date" id="from_date" class="assets_from_date input-css" required="">
                        {!! $errors->first('assets_from_date', '<p class="help-block">:message</p>') !!} 
                        
                    </div>
                    <div class="col-md-6 {{ $errors->has('assets_to_date') ? ' has-error' : ''}}">
                        <label>To :</label>
                        <input type="text" name="assets_to_date" id="to_date" class="assets_to_date input-css" >
                        {!! $errors->first('assets_to_date', '<p class="help-block">:message</p>') !!} 
                    </div>

                </div>
                
            </div><br>
            <div class="row">
                <div class="col-md-6 {{ $errors->has('assets_form') ? ' has-error' : ''}}">
                    <label >Upload Signed Asset Form <sup>*</sup></label>
                     <input type="file" accept="application/pdf" name="assets_form" id="assets_form" class="assets_form ">
                    {!! $errors->first('assets_form', '<p class="help-block">:message</p>') !!} 
                    
                </div>
                <div class="col-md-12">
                    <label class="error err"></label>
                </div>
            </div><br><br>
       </div>
       
        <div class="row">
                <div class="col-md-12">
                     <input type="submit" class="btn btn-primary" value="Submit">
                     
                      <button type="submit" formtarget="_blank" class="btn btn-success" formmethod="GET" formaction="/master/assets/assign/generate/form">Generate Asset Form</button>
                </div>
            </div>
        </form>
      
      </section>
@endsection
