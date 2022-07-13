@extends($layout)

@section('title', 'Attendance Create')

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Attendance Create</a></li> 
@endsection
@section('css') 
<style>

.datetimepicker{
  z-index: 99999;
}
</style> 
@endsection
@section('js')
<script src="/js/Employee/profile.js"></script>
  <script>
$("#js-msg-success").hide();
$("#js-msg-error").hide();
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
            },
            bilty_docket1:{
                required: true
            },
            duration:{
                required: true  
            },
            late_by:{
                required: true
            },
            early_by:{
                required: true
            },
            
            ot:{
                required: true
            },
            shift:{
                required: true
            },
            
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

    //   var duration = $('#duration').val();
    //   var late_by = $('#late_by').val();
    //   var early_by = $('#early_by').val();
    //   var ot = $('#ot').val();
    //   var shift = $('#shift').val();

    //   var date = $('#year').val();

    //   $('#ajax_loader_div').css('display','block');
    //    $.ajax({
    //     method: "GET",
    //     url: "/hr/attendance/create",
    //     data: {
    //       'id':id,
    //       'in_time':in_time,
    //       'out_time':out_time,
    //       'status':status,
    //       'deduction':deduction,
    //       'lunch_in_time':lunch_in_time,
    //       'lunch_out_time':lunch_out_time,
    //       'half_day':half_day,

    //       'duration':duration,
    //       'late_by':late_by,
    //       'early_by':early_by,
    //       'ot':ot,
    //       'shift':shift,
    //       'date':date
    //     },
    //     success:function(data) {
    //       $('#ajax_loader_div').css('display','none');
    //         if (data.type == 'success') {
    //             $('#exampleModalCenter').modal("hide");
    //             $('#att_table').dataTable().api().ajax.reload();
    //             $("#js-msg-success").html(data.msg);
    //             $("#js-msg-success").show();
    //             $('#my-form')[0].reset(); 
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



    var dataTable;

    function create_datatable(){
  
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
            "url": "/hr/attendance/create/summary/api",
            "datatype": "json",
                "data": function (data) {
                    var year = $('#year').val();
                    data.year = year;
                 
                },
            },
            "columns": [
         
          {"data":"employee_number"},
          {"data":"name"},
          {"data":"department"},
          {"data":"id","render":function(data,type,full,meta){
            return "<button id="+data+"  onClick='getid("+data+")' class='comple btn btn-primary btn-xs' data-toggle='modal' data-target='#exampleModalCenter'>Add Attendance</button> &nbsp;";
          }}
        ],
      });
    }
    // Data Tables
    $(document).ready(function() {
      create_datatable();
    });

    function getid(i){
    var elmId = i;
    var year = $('#year').val();
    $("#id").val(elmId);
    $("#date").val(year);
  }

    $('#year').datepicker({
      autoclose: true,
      format: 'd-m-yyyy'

  }).datepicker("setDate", new Date());
   
    $('#year').on('change', function () {
      create_datatable();
    });
   
    $('input[type=radio][name=status]').change(function() {
        if (this.value == "P"){
          
            $('.div_p').show();
            
            
        }
            
        if (this.value == "A"){
            $('.div_p').hide();
           
        }
        if (this.value == "WO"){
            $('.div_p').hide();
           
        }
           
    });
    $('.timepicker').datetimepicker({
      'format': 'HH:mm:ss',
    
    'defaultDate':moment(dateNow).hours(0).minutes(0).seconds(0).milliseconds(0)    
   
  });
  var dateNow = new Date();
  $('.timepickerss').datetimepicker({
    'format': 'HH:mm:ss',
    
    'defaultDate':moment(dateNow).hours(0).minutes(0).seconds(0).milliseconds(0)     
    // 'datepicker':false,
   
  });
  </script>
@endsection

@section('main_section')
    <section class="content">
            <div id="app">
           
                    @include('sections.flash-message')
                    @yield('content')
                    
                   
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    
                    @endsection
                    <div class="row">
                   
                      <div class="col-md-3" >
                        <label>Select Date</label>
                        <input type="text" name="year" id="year" class="input-css" autocomplete="off">
                      </div>
                     
                    </div><br>
                    <table id="att_table" class="table table-bordered table-striped" style="width:100%">
                        <thead>
                        <tr>
                          <th>Employee Id</th>
                          <th>Name</th>
                          <th>Department</th>
                          <th>Action</th>
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
        <div class="modal-content" style="width: 737px!important;">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLongTitle">Create Attendance </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">                   
            <form id="my_form"  action="/hr/attendance/create" method="POST" >
                <div class="alert alert-danger" id="js-msg-error">
              </div>
              @csrf
              <input type="hidden" name="id" id="id">
              <input type="hidden" name="date" id="date">
              <div class="row">
                    <div class="col-md-12">
                    <div class="col-md-12 bon_ques">
                        <label for=""> Status</label>
                        <div class="col-md-4"><input type="radio" required name="status" id="status" value="P">Present</div>
                        <div class="col-md-4"><input type="radio" required name="status" id="status" value="A">Absent</div>
                        <div class="col-md-4"><input type="radio" required name="status" id="status" value="WO">Week Off</div>
                    </div>
                    </div>
                </div>
              <div class="div_p" style="display:none">
                <div class="row">
                   <div class="col-md-6">
                          <label for="">In Time <sup>*</sup>   </label>
                          <input type="text" name="in_time" id="in_time" required class="in_time input-css timepicker">
                  </div>
                  <div class="col-md-6">
                      <label>Out Time <sup>*</sup></label>
                      <input type="text" name="out_time" id="out_time" required class="out_time input-css timepicker">
                  </div>
                </div><br>
                <div class="row">
                    <div class="col-md-6">
                            <label for="">Duration <sup>*</sup>   </label>
                            <input type="text" name="duration" required id="duration" class="input-css timepickerss">
                         
                    </div>
                  <div class="col-md-6">
                      <label>Late By <sup>*</sup></label>
                      <input type="text" name="late_by" id="late_by" required class="input-css timepickerss">
                  </div>
                </div><br>
                <div class="row">
                  
                    <div class="col-md-6">
                            <label for="">Early By <sup>*</sup>   </label>
                            <input type="text" name="early_by" id="early_by" required class="input-css timepickerss">
                         
                    </div>
                  <div class="col-md-6">
                      <label>OverTime <sup>*</sup></label>
                      <input type="text" name="ot" id="ot" required class="input-css  timepickerss">
                  </div>
                </div><br>
              
                <div class="row">
                <div class="col-md-6">
                            <label for="">Shift <sup>*</sup>   </label>
                            <input type="text" name="shift" required id="shift" class="input-css">
                        
                    </div>
                  <div class="col-md-6">
                      <label>Half Day <sup>*</sup></label>
                      <select name="half_day" id="half_day" required class="input-css select" style="width:70%">
                        <option value="">Select half day</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                      </select>
                  </div>
                </div>
                <br>
                <div class="row">
               
                 
                    <div class="col-md-6">
                            <label for="">Lunch In Time <sup>*</sup>   </label>
                            <input type="text" name="lunch_in_time" required id="lunch_in_time" class="lunch_in_time input-css timepicker">
                          {!! $errors->first('from', '<p class="help-block">:message</p>') !!}
                    </div>
                  <div class="col-md-6">
                      <label>Lunch Out Time <sup>*</sup></label>
                      <input type="text" name="lunch_out_time" required id="lunch_out_time" class="lunch_out_time input-css timepicker">
                  </div>
                </div><br>
                <div class="row">
                  <div class="col-md-6">
                      <label>Any Other Time Deduction<sub> (In Hour)</sub> <sup>*</sup></label>
                      <input type="text" name="deduction" required id="deduction" class="input-css timepickerss">
                  </div>
                </div>
              </div>

          </div>
          <div class="modal-footer">
            <button type="button" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" id="btnUpdate" class="btn btn-success">Create</button>
          </div>
          </form>
        </div>
      </div>
    </div>
      </section>
@endsection