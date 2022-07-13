@extends($layout)

@section('title', 'Attendance List')

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Attendance List</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
@media (max-width: 768px){
    .content-header>h1 {
      display: inline-block;
    }
  }
  @media (max-width: 425px){   
    .content-header>h1 {
      display: inline-block;
    }
  }
  .timepicker{
    z-index: 99999;
  }
  .wickedpicker{
    z-index: 99999;
  }

  .sun{
    color: #F50057;
  }

  .holy{
    color: #66BB6A;
  }


  
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
<script src="/js/Employee/profile.js"></script>
  <script>
$("#js-msg-success").hide();
$("#js-msg-error").hide();
// $("#exampleModalCenter").on("hidden.bs.modal", function () {
//   var x=$('.out_time').wickedpicker();
//   alert(x);
// });
$('#my_form').validate({ // initialize the plugin
        rules: {
          in_time: {
                required: true
            },
            out_time:{
                required: true  
            },
            status:{
                required: true  
            },
            deduction:{
                required: true  
            },
            lunch_in_time:{
                required: true  
            },
            lunch_out_time:{
                required: true  
            },
            half_day:{
                required: true  
            }
        }
    });
    function  EditStatus(el){
      var id = $(el).children('i').attr('class');
      $('#ajax_loader_div').css('display','block');
       $.ajax({  
                url:"/hr/get/attendance/"+id,  
                method:"get",  
                success:function(data){
                  $('#ajax_loader_div').css('display','none');
                 if(data) {
                  $("#id").val(data.id);
                  $("#in_time").val(data.in_time);
                  $("#from").val(data.in_time);
                  $("#out_time").val(data.out_time);
                  $("#to").val(data.out_time);
                  $("#lunch_in_time").val(data.lunch_from);
                  $("#lunch_rom").val(data.lunch_from);
                  $("#lunch_out_time").val(data.lunch_to);
                  $("#lunchto").val(data.lunch_to);
                  $("#deduction").val(data.deduction);
                  $("#deduction_to").val(data.deduction);
                  $("#status option[value='"+data.status+"']").prop('selected' , true);  
                  $("#half_day option[value='"+data.half_day+"']").prop('selected' , true);  
                  $('#exampleModalCenter').modal("show"); 
                  }
                      
                }  
           });
    }


 

  $('.in_time').on('click' , function (){
    var shift_from = $("#in_time").val();
    if(shift_from==""){
      $('.in_time').wickedpicker({setTime: null});
    }
    else{
      var hours = shift_from.split(':').shift();
    var minuts = shift_from.split(":")[1];
    var minuts = minuts.replace(/ /g,'');
    if ( minuts.indexOf("AM") > -1 ){
      var avoid = "AM";
    }else{
      var avoid = "PM";
    }
    var minuts = minuts.replace(avoid,'');
    var options = {
      now: hours+":"+minuts, //hh:mm 24 hour format only, defaults to current time
      twentyFour: false, //Display 24 hour format, defaults to false
      upArrow: 'wickedpicker__controls__control-up', //The up arrow class selector to use, for custom CSS
      downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
      close: 'wickedpicker__close', //The close class selector to use, for custom CSS
      hoverState: 'hover-state', //The hover state class to use, for custom CSS
      title: 'Timepicker', //The Wickedpicker's title,
      showSeconds: false, //Whether or not to show seconds,
      secondsInterval: 1, //Change interval for seconds, defaults to 1
      minutesInterval: 1, //Change interval for minutes, defaults to 1
      beforeShow: null, //A function to be called before the Wickedpicker is shown
      show: null, //A function to be called when the Wickedpicker is shown
      clearable: false, //Make the picker's input clearable (has clickable "x")
    }; 
            $('.in_time').wickedpicker(options);
            $('.in_time').wickedpicker('setTime', 0, hours+":"+minuts);
    }
    
});



    
     $('.out_time').on('click' , function (){
      var shift_to = $("#out_time").val();
      if(shift_to==""){
      $('.out_time').wickedpicker({setTime: null});
      }
      else{
        
      var tohours = shift_to.split(':').shift();
      var tominuts = shift_to.split(":")[1];
      var tominuts = tominuts.replace(/ /g,'');
      if ( tominuts.indexOf("AM") > -1 ){
        var avoid = "AM";
      }else{
        var avoid = "PM";
      }
      var tominuts = tominuts.replace(avoid,'');
      var options = {
      now: tohours+":"+tominuts, //hh:mm 24 hour format only, defaults to current time
      twentyFour: false, //Display 24 hour format, defaults to false
      upArrow: 'wickedpicker__controls__control-up', //The up arrow class selector to use, for custom CSS
      downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
      close: 'wickedpicker__close', //The close class selector to use, for custom CSS
      hoverState: 'hover-state', //The hover state class to use, for custom CSS
      title: 'Timepicker', //The Wickedpicker's title,
      showSeconds: false, //Whether or not to show seconds,
      secondsInterval: 1, //Change interval for seconds, defaults to 1
      minutesInterval: 1, //Change interval for minutes, defaults to 1
      beforeShow: null, //A function to be called before the Wickedpicker is shown
      afterShow: null,
      show: null, //A function to be called when the Wickedpicker is shown
      clearable: false, //Make the picker's input clearable (has clickable "x")
    }; 
            $('.out_time').wickedpicker(options);
            $('.out_time').wickedpicker('setTime', 0, tohours+":"+tominuts);
      }
    });

       $('.lunch_in_time').on('click' , function (){
      var shift_to = $("#lunch_in_time").val();
      if(shift_to==""){
      $('.lunch_in_time').wickedpicker({setTime: null});
    }
    else{
      var tohours = shift_to.split(':').shift();
      var tominuts = shift_to.split(":")[1];
      var tominuts = tominuts.replace(/ /g,'');
      if ( tominuts.indexOf("AM") > -1 ){
        var avoid = "AM";
      }else{
        var avoid = "PM";
      }
      var tominuts = tominuts.replace(avoid,'');
      var options = {
      now: tohours+":"+tominuts, //hh:mm 24 hour format only, defaults to current time
      twentyFour: false, //Display 24 hour format, defaults to false
      upArrow: 'wickedpicker__controls__control-up', //The up arrow class selector to use, for custom CSS
      downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
      close: 'wickedpicker__close', //The close class selector to use, for custom CSS
      hoverState: 'hover-state', //The hover state class to use, for custom CSS
      title: 'Timepicker', //The Wickedpicker's title,
      showSeconds: false, //Whether or not to show seconds,
      secondsInterval: 1, //Change interval for seconds, defaults to 1
      minutesInterval: 1, //Change interval for minutes, defaults to 1
      beforeShow: null, //A function to be called before the Wickedpicker is shown
      show: null, //A function to be called when the Wickedpicker is shown
      clearable: false, //Make the picker's input clearable (has clickable "x")
    }; 
            $('.lunch_in_time').wickedpicker(options);
            $('.lunch_in_time').wickedpicker('setTime', 0, tohours+":"+tominuts);
    }
      
    });

  $('.lunch_out_time').on('click' , function (){
      var shift_to = $("#lunch_out_time").val();
      if(shift_to==""){
      $('.lunch_out_time').wickedpicker({setTime: null});
    }
    else{
      var tohours = shift_to.split(':').shift();
      var tominuts = shift_to.split(":")[1];
      var tominuts = tominuts.replace(/ /g,'');
      if ( tominuts.indexOf("AM") > -1 ){
        var avoid = "AM";
      }else{
        var avoid = "PM";
      }
      var tominuts = tominuts.replace(avoid,'');
      var tominuts = tominuts.replace(avoid,'');
      var options = {
      now: tohours+":"+tominuts, //hh:mm 24 hour format only, defaults to current time
      twentyFour: false, //Display 24 hour format, defaults to false
      upArrow: 'wickedpicker__controls__control-up', //The up arrow class selector to use, for custom CSS
      downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
      close: 'wickedpicker__close', //The close class selector to use, for custom CSS
      hoverState: 'hover-state', //The hover state class to use, for custom CSS
      title: 'Timepicker', //The Wickedpicker's title,
      showSeconds: false, //Whether or not to show seconds,
      secondsInterval: 1, //Change interval for seconds, defaults to 1
      minutesInterval: 1, //Change interval for minutes, defaults to 1
      beforeShow: null, //A function to be called before the Wickedpicker is shown
      show: null, //A function to be called when the Wickedpicker is shown
      clearable: false, //Make the picker's input clearable (has clickable "x")
    }; 
            $('.lunch_out_time').wickedpicker(options);
            $('.lunch_out_time').wickedpicker('setTime', 0, tohours+":"+tominuts);
    }
      
    });

  $('.deduction').on('click' , function (){
      var shift_to = $("#deduction_to").val();
      if(shift_to==""){
      $('.deduction').wickedpicker({setTime: null});
    }
    else{
      var tohours = shift_to.split(':').shift();
      var tominuts = shift_to.split(":")[1];
      var tominuts = tominuts.replace(/ /g,'');
      if ( tominuts.indexOf("AM") > -1 ){
        var avoid = "AM";
      }else{
        var avoid = "PM";
      }
      var tominuts = tominuts.replace(avoid,'');
      var options = {
      now: tohours+":"+tominuts, //hh:mm 24 hour format only, defaults to current time
      twentyFour: false, //Display 24 hour format, defaults to false
      upArrow: 'wickedpicker__controls__control-up', //The up arrow class selector to use, for custom CSS
      downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
      close: 'wickedpicker__close', //The close class selector to use, for custom CSS
      hoverState: 'hover-state', //The hover state class to use, for custom CSS
      title: 'Timepicker', //The Wickedpicker's title,
      showSeconds: false, //Whether or not to show seconds,
      secondsInterval: 1, //Change interval for seconds, defaults to 1
      minutesInterval: 1, //Change interval for minutes, defaults to 1
      beforeShow: null, //A function to be called before the Wickedpicker is shown
      show: null, //A function to be called when the Wickedpicker is shown
      clearable: false, //Make the picker's input clearable (has clickable "x")
    }; 
            $('.deduction').wickedpicker(options);
            $('.deduction').wickedpicker('setTime', 0, tohours+":"+tominuts);
    }
      
    });





    // $('#btnUpdate').on('click', function() {
    //   var id = $('#id').val();
    //   var in_time = $('#in_time').val();
    //   var out_time = $('#out_time').val();
    //   var status = $('#status').val();
    //   var deduction = $('#deduction').val();
    //   var lunch_in_time = $('#lunch_in_time').val();
    //   var lunch_out_time = $('#lunch_out_time').val();
    //   var half_day = $('#half_day').val();
    //   $('#ajax_loader_div').css('display','block');
    //    $.ajax({
    //     method: "GET",
    //     url: "/hr/attendance/update",
    //     data: {
    //       'id':id,
    //       'in_time':in_time,
    //       'out_time':out_time,
    //       'status':status,
    //       'deduction':deduction,
    //       'lunch_in_time':lunch_in_time,
    //       'lunch_out_time':lunch_out_time,
    //       'half_day':half_day
    //     },
    //     success:function(data) {
    //       $('#ajax_loader_div').css('display','none');
    //         if (data.type == 'success') {
    //             $('#exampleModalCenter').modal("hide");
    //             $('#att_table').dataTable().api().ajax.reload();
    //             $("#js-msg-success").html(data.msg);
    //             $("#js-msg-success").show();
    //             $('#my_form')[0].reset(); 
    //            setTimeout(function() {
    //              $('#js-msg-success').fadeOut('fast');
    //             }, 10000);
    //         }if(data.type == 'error'){
    //             $('#exampleModalCenter').modal("hide");  
    //             $("#js-msg-error").html(data.msg);
    //             $("#js-msg-error").show();
    //            setTimeout(function() {
    //              $('#js-msg-error').fadeOut('fast');
    //             }, 4000);
    //         }
    //     },
    //   });
    // });

    var selected=[];
    var col_length =  <?php $a_date = date('Y-m-d'); 
      $date = new DateTime($a_date); 
      $date->modify('last day of this month'); 
      echo $last_day = $date->format('d'); ?>;
    var col_data = 'd';
    var id_data = 'id';

    var dataTable;

    function create_datatable(){
      var str = [];
      str.push({"data":"employee_number"});
      str.push({"data":"name"});
      str.push({"data":"date"});
      for(var i = 1 ; i <= col_length ; i++) {
        str.push({"data": col_data+i,
                  "render": function(data,type,full,meta) { 
                    var status = data.split(',').shift();
                    var id = data.split(",")[1];
                    if(status==""){
                      return status;
                    }
                    else if(status=="WO"){
                      var st = 'Sunday';
                      return '<span class="sun">'+st+'</span>';
                    }
                    else if(status!="P" && status!="A"){
                      return "<div class='rotation holy'>"+status+"</div>";
                    }
                    else{
                     return '<span onclick="EditStatus(this)"><i class="'+id+'"></i>'+status+'</span>';
                    }
                  },
                  "orderable": false});
      }
      // str.push({
      //     "targets": [ -1 ],
      //     "data":"emp_id", "render": function(data,type,full,meta) {
      //       return '';
      //     },
      //     "orderable": false
      // });
      if(dataTable){
        dataTable.destroy();
      }
      dataTable = $('#att_table').DataTable({
        "scrollX": true,
        "processing": true,
        "serverSide": true,
        "aaSorting":[],
        "responsive": true,
        "ajax": {
            "url": "/hr/attendance/summary/api",
            "datatype": "json",
                "data": function (data) {
                    var emp_name = $('#emp_name').val();
                    data.emp_name = emp_name;
                    var year = $('#year').val();
                    data.year = year;
                    var month = $('#month').val();
                    data.month = month;
                },
            },
        "columns": str
      });
    }
    // Data Tables
    $(document).ready(function() {
      create_datatable();
    });

    $('#emp_name').on( 'change', function () {
      dataTable.draw();
    });

    $('#year').datepicker({
      format: "yyyy",
      weekStart: 1,
      orientation: "bottom",
      keyboardNavigation: false,
      viewMode: "years",
      minViewMode: "years",
      autoclose: true
    });
    $('#month').datepicker({
      format: "mm",
      weekStart: 1,
      orientation: "bottom",
      keyboardNavigation: false,
      viewMode: "months",
      minViewMode: "months",
      autoclose: true
    });

    $('#year').on('change', function () {
      create_datatable();
    });
    $('#month').on('change', function () {
      create_datatable();
    });

  </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
              <div class="alert alert-success" id="js-msg-success">
                    @include('sections.flash-message')
                    @yield('content')
                    </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    
                    @endsection
                    <div class="row">
                      <div class="col-md-3">
                         <label>Employee Name</label>
                         
                            <select name="emp_name" class="input-css select2 selectValidation" id="emp_name">
                               <option value="">Select Employee</option>
                               @foreach($employee as $key => $val)
                               <option value="{{$val['id']}}"> {{$val['name']}}({{$val['employee_number']}})</option>
                               @endforeach
                            </select>
                         
                         {!! $errors->first('emp_name', '
                         <p class="help-block">:message</p>
                         ') !!}
                      </div>
                      <div class="col-md-3" >
                        <label>Select Year</label>
                        <input type="text" name="year" id="year" class="input-css" autocomplete="off">
                      </div>
                      <div class="col-md-3" >
                        <label>Select Month</label>
                        <input type="text" name="month" id="month" class="input-css" autocomplete="off">
                      </div>
                    </div><br>
                    <table id="att_table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>Employee Id</th>
                          <th>Name</th>
                          <th>Date</th>
                          <?php 
                            $date = date('Y-m-d');
                            $newdate = new DateTime($date);
                            $newdate->modify('last day of this month');
                            $last_day=$newdate->format('d');
                            for ($i=1; $i <= $last_day ; $i++) { 
                               echo "<th>".$i."</th>";
                            }
                          ?>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
               
                    </table>
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
         <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="margin-top: 100px!important;">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLongTitle">Update Attendance </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">                   
            <form id="my_form" action="/hr/attendance/update"  method="POST" >
                <div class="alert alert-danger" id="js-msg-error">
              </div>
              @csrf
              <input type="hidden" name="id" id="id">
                <div class="row">
                  <input type="hidden" name="from" id="from">
                  <input type="hidden" name="to" id="to">
                   <div class="col-md-6">
                          <label for="">In Time <sup>*</sup>   </label>
                          <input type="text" required name="in_time" id="in_time" class="in_time input-css">
                         {!! $errors->first('from', '<p class="help-block">:message</p>') !!}
                  </div>
                <div class="col-md-6">
                    <label>Out Time <sup>*</sup></label>
                    <input type="text" required name="out_time" id="out_time" class="out_time input-css">
                </div>
              </div><br>
              <div class="row">
                <div class="col-md-6">
                    <label>Status <sup>*</sup></label>
                    <select name="status" required id="status" class="input-css select" style="width:70%">
                      <option>Select status</option>
                      <option value="A">A</option>
                      <option value="P">P</option>
                      <option value="WO">WO</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Half Day <sup>*</sup></label>
                    <select name="half_day" id="half_day" required class="input-css select" style="width:70%">
                      <option>Select half day</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                </div>
              </div>
              <br>
               <div class="row">
                  <input type="hidden" name="lunch_from" id="lunch_from">
                  <input type="hidden" name="lunch_to" id="lunch_to">
                  <input type="hidden" name="deduction_to" id="deduction_to">
                   <div class="col-md-6">
                          <label for="">Lunch In Time <sup></sup>   </label>
                          <input type="text" required name="lunch_in_time" id="lunch_in_time" class="lunch_in_time input-css">
                         {!! $errors->first('from', '<p class="help-block">:message</p>') !!}
                  </div>
                <div class="col-md-6">
                    <label>Lunch Out Time <sup></sup></label>
                    <input type="text" required name="lunch_out_time" id="lunch_out_time" class="lunch_out_time input-css">
                </div>
              </div><br>
              <div class="row">
                <div class="col-md-6">
                    <label>Any Other Time Deduction<sub> (In Hour)</sub> <sup></sup></label>
                    <input type="text" required name="deduction" id="deduction" class="deduction input-css">
                </div>
              </div>

          </div>
          <div class="modal-footer">
            <button type="button" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" id="btnUpdate" class="btn btn-success">Update</button>
          </div>
          </form>
        </div>
      </div>
    </div>
      </section>
@endsection