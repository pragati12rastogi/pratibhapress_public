@extends($layout)

@section('title', 'Daily Plate Report')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Daily Plate Report</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  }
 
@media (max-width: 768px)  
  {
    
    .content-header>h1 {
      display: inline-block;
     
    }
  }
  @media (max-width: 425px)  
  {
   
    .content-header>h1 {
      display: inline-block;
      
    }
  }
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
<script src="/js/Production/platebypress_creation.js"></script>

<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      $("#date_f").val($('#selectDate').val());
      fulltable();

       $( "#plate_creationForm" ).submit(function( event ) {
        var x = check();
        if(x==0){
          event.preventDefault();
        }
        
       });

       $( "#plate_updForm" ).submit(function( event ) {
        var y = $("#upd_no_plate").val();
        var w = upd_check();
        if(parseInt(y)<= 0 || w==0){
          event.preventDefault();
          $("#upd_plates_err").val("plate is required").show();
        }
        
       });
    });
    $('#selectDate').change(function(){
      $("#date_f").val($('#selectDate').val());
      dataTable.draw();
    })
    function fulltable(){
      if(dataTable){
        dataTable.destroy();
      }
      
      dataTable = $('#plate').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/production/press/dailyplanning/report/api",
            "datatype": "json",
                "data": function (data) {
                    var selectDate = $('#selectDate').val();
                    data.selectDate = selectDate;
                }
          },
          "columns": [
            {"data":"job_number"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"creative_name"},
            {"data":"element_name"},
            // {"data":"e_plate_size"},
            {"data":"total_plates"},
            {"data":"machine"},
            {"data":"planned_plates"},
            {"data":"actual"},
            {
              "targets": [ -1 ],
              data : function(data,type,full,meta)
                {
                  return (data.planned_plates-data.actual);
                }
            },
            {
              "targets": [ -1 ],
              "data":"id", "render": function(data,type,full,meta)
                {
                  
                    var today = new Date();
                    var pland = new Date(full.planned_date);
                    var date = today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear();
                    var datecheck = pland.getDate()+'-'+(pland.getMonth()+1)+'-'+pland.getFullYear();
                    var x = "";
                    var ac11="";
                    var ac2="";
                    var ac6="";
                    if(full.actual_11am>0){
                      ac11="disabled";
                    }
                    if(full.actual_2pm>0){
                      ac2="disabled";
                    }
                    if(full.actual_6pm>0){
                      ac6="disabled";
                    }
                    if(date == datecheck && full.left_imp != 0){

                      x = "<button class='btn btn-primary btn-xs' "+ac11+" onclick='unusual("+data+","+full.element_id+","+full.planned_plates+","+full.plan_id+","+full.left_imp+","+full.actual+","+full.actual_11am+","+full.actual_2pm+","+full.actual_6pm+",\"11am\")' data-toggle='modal' data-target='#myModal_of'> 11 AM </button> &nbsp;" 
                      +"<button class='btn btn-warning btn-xs' "+ac2+" onclick='unusual("+data+","+full.element_id+","+full.planned_plates+","+full.plan_id+","+full.left_imp+","+full.actual+","+full.actual_11am+","+full.actual_2pm+","+full.actual_6pm+",\"2pm\")' data-toggle='modal' data-target='#myModal_of'> 2 PM </button> &nbsp;"+
                      "<button class='btn btn-danger btn-xs' "+ac6+" onclick='unusual("+data+","+full.element_id+","+full.planned_plates+","+full.plan_id+","+full.left_imp+","+full.actual+","+full.actual_11am+","+full.actual_2pm+","+full.actual_6pm+",\"6pm\")' data-toggle='modal' data-target='#myModal_of'> 6 PM </button> &nbsp;"
                      ;
                    }
                    else{
                      x = "";
                    }
                    if(date < datecheck || full.actual == 0){
                      var d=full.planneddate;
                      x+= '<button style="margin:5px;" class="btn btn-success btn-xs" onclick="edit_fn('+full.plan_id+','+full.planned_plates+',\''+d+'\')" data-toggle="modal" data-target="#myModal_edit"> Edit</button> &nbsp;' ;
                    }else{
                      x+= "";
                    }

                    return x;
                }
              }
            ],
            "columnDefs": [
               { "orderable": false, "targets": 9 }
            
            ]
          
        });
    }
 
 $('#myModal_of').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
});

 $('#myModal_edit').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
});
  function unusual(id,ele,plan,plan_id,left,actual,act11,act2,act6,text){
    
   $("#element").val(ele);
   $("#jc_no").val(id);
   $("#planned").val(plan);
   $("#plan_id").val(plan_id);
   $("#left_p").val(left);
   $("#text_p").text(left+actual);
  //  $(".text_p").attr('max',left+actual);
   $("#act_time_11").val(act11);
   $("#act_time_2").val(act2);
   $("#act_time_6").val(act6);
   if(text=="11am"){
  
    $("#act_time_11").attr('disabled',false);
    $("#act_time_2").attr('disabled',true);
    $("#act_time_6").attr('disabled',true);
   }
   else if(text=="2pm"){

    $("#act_time_2").attr('disabled',false);
    $("#act_time_11").attr('disabled',true);
    $("#act_time_6").attr('disabled',true);
   }
   else if(text=="6pm"){ 
  
   $("#act_time_6").attr('disabled',false);
   $("#act_time_2").attr('disabled',true);
    $("#act_time_11").attr('disabled',true);
   }
  
   var date=new Date();
  var hours = date.getHours();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  if(hours<=12){
    hours="11 am";
  }
  if(hours>12 && hours<=14){
  hours="2 pm";
  }
  if(hours>14 && hours<=18){
  hours="6 pm";
  }
  console.log(hours);
  $("#act_time").val(hours);
  }
 
  function edit_fn(id,plate,date){
    
   $("#upd_no_plate").val(plate);
   $("#upd_planned").val(plate);
   $(".date").val(date)
   $("#upd_text_plate").text(plate);
   $("#upd_plan_id").val(id);
  }


$("#cal").click(function () {
var act11 = $("#act_time_11").val();
var act2 = $("#act_time_2").val();
var act6 = $("#act_time_6").val();
var total = parseInt(act11) + parseInt(act2)+ parseInt(act6);
alert(total);
$("#no_plate").val(total);
});

$(document).on("change", ".act_time", function() {
    var sum = 0;
    $(".act_time").each(function(){
        sum += +$(this).val();
    });
    $("#no_plate").val(sum);
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
                    <div class="row">
                        <div class="col-md-6 {{ $errors->has('selectDate') ? 'has-error' : ''}}">
                            <label>Date<sup>*</sup></label>
                           <input autocomplete="off" type="text" id="selectDate" class="form-control date-range-filter datepicker" data-date-format="yyyy-mm-dd" placeholder="Date:">
                            {!! $errors->first('selectDate', '<p class="help-block">:message</p>') !!}
                        </div>
                       
                    </div><br><br>

                    <table id="plate" class="table table-bordered table-striped" style="width:100%">
                      <thead>
                        <tr>
                          <th>JC No.</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>Creative</th>
                          <th>Element</th>
                          <!-- <th>Plate Size</th> -->
                          <th>Total Impression</th>
                          <th>Machine</th>
                          <th>Planned</th>
                          <th>Actual</th>
                          <th>Balance</th>
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
          <div id="myModal_of" class="modal fade" role="dialog">
              <div class="modal-dialog modal-lg">
                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Create</h4>
                    </div>
                    <div class="modal-body">
                    <form action="/prod/press/dailyplanning/report/submitted" enctype="multipart/form-data" method="POST" id="plate_creationForm" >
                      @csrf
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" class="jc_no" name="jc_no" id="jc_no" hidden="hidden">
                          <input type="text" class="element" name="element" id="element" hidden="hidden">
                          <input type="text" class="planned" name="planned" id="planned" hidden="hidden">
                          <input type="text" class="plan_id" name="plan_id" id="plan_id" hidden="hidden">
                          <input type="text" class="left_p" name="left_p" id="left_p" hidden="hidden">
                          <input type="text" class="date_f" name="date_f" id="date_f" hidden="hidden">
                        </div>
                      </div>
                      <br>
                      <div class="row" id="new_show">
                        <div class="col-md-4 {{ $errors->has('act_time') ? 'has-error' : ''}}">
                            <label>Actual 11 AM <sup>*</sup> </label><br>
                              <input type="text"  class="form-control input-css act_time" disabled  name="act_time_11" id="act_time_11" required>
                              {!! $errors->first('act_time', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-4 {{ $errors->has('act_time') ? 'has-error' : ''}}">
                            <label>Actual 2 PM <sup>*</sup> </label><br>
                              <input type="text"  class="form-control input-css act_time" disabled name="act_time_2" id="act_time_2" required>
                              {!! $errors->first('act_time', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-4 {{ $errors->has('act_time') ? 'has-error' : ''}}">
                            <label>Actual 6 PM <sup>*</sup> </label><br>
                              <input type="text"  class="form-control input-css act_time" disabled  name="act_time_6" id="act_time_6" required>
                              {!! $errors->first('act_time', '<p class="help-block">:message</p>') !!}
                        </div>
                      </div><br>
                      <div class="row">
                      <div class="col-md-6 {{ $errors->has('no_plate') ? 'has-error' : ''}}">
                            <label>Actual Impressions <sup>*</sup> (Max :<span id="text_p"></span> Impression)</label><br>
                              <input type="number" min="0"  class="form-control input-css no_plate text_p"  name="no_plate" id="no_plate">
                              <label id="plates_err" class="error"></label>
                              {!! $errors->first('no_plate', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('wastage') ? 'has-error' : ''}}">
                            <label>Wastage <sup>*</sup></label><br>
                              <input type="number" min="0"  class="form-control input-css wastage" required  name="wastage" id="wastage">
                              <label id="wastage" class="error"></label>
                              {!! $errors->first('wastage', '<p class="help-block">:message</p>') !!}
                        </div>
                      </div>
                      <div class="row">
                     
                        <div class="col-md-12 {{ $errors->has('remark') ? 'has-error' : ''}}">
                            <label>Remark <sup>*</sup> </label><br>
                              <input type="text"  class="form-control input-css remark"  name="remark" id="remark" required>
                              {!! $errors->first('remark', '<p class="help-block">:message</p>') !!}
                        </div>
                      </div><br>
                      <div class="row">
                        <div class="col-md-12">
                          <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                      </div><br>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                 </div>
                </div>
            </div>
          </div>
          <div id="myModal_edit" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Change Planning</h4>
                    </div>
                    <div class="modal-body">
                    <form action="/production/press/dailyprocess/updation" enctype="multipart/form-data" method="POST" id="plate_updForm" >
                      @csrf
                      <div class="row">
                        <div class="col-md-6">
                          <!-- <input type="text" class="upd_jc_no" name="upd_jc_no" id="upd_jc_no" hidden="hidden">
                          <input type="text" class="upd_element" name="upd_element" id="upd_element" hidden="hidden"> -->
                          <input type="text" class="upd_planned" name="upd_planned" id="upd_planned" hidden="hidden">
                          <input type="text" class="upd_plan_id" name="upd_plan_id" id="upd_plan_id" hidden="hidden">
                          <!-- <input type="text" class="upd_left_p" name="upd_left_p" id="upd_left_p" hidden="hidden">
                          <input type="text" class="upd_date_f" name="upd_date_f" id="upd_date_f" hidden="hidden"> -->
                        </div>
                      </div>
                      <br>
                      <div class="row" id="new_show">
                        <div class="col-md-6 {{ $errors->has('upd_no_plate') ? 'has-error' : ''}}">
                            <label>Number Of Impression Planned <sup>*</sup>(max : <span id="upd_text_plate"></span> Impressions)</label><br>
                              <input type="number" class="form-control input-css upd_no_plate"  name="upd_no_plate" id="upd_no_plate" onKeyPress="if(this.value.length==10) return false;" onchange="upd_check()">
                              <label id="upd_plates_err" class="error"></label>
                              {!! $errors->first('upd_no_plate', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('date') ? 'has-error' : ''}}">
                            <label> Planned For Date <sup>*</sup></label><br>
                              <input type="text" autocomplete="off" class="form-control input-css date datepicker"  name="date" id="date">
                              <label id="upd_plates_err" class="error"></label>
                              {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                        </div>
                        <!-- <div class="col-md-6 {{ $errors->has('upd_pNew_date') ? 'has-error' : ''}}">
                           <label>Plates Date <sup>*</sup></label><br>
                            <input type="text" class="form-control input-css upd_pNew_date" name="upd_pNew_date" id="upd_pNew_date">
                            {!! $errors->first('upd_pNew_date', '<p class="help-block">:message</p>') !!}
                        </div> -->  
                      </div><br>
                      <div class="row">
                        <div class="col-md-12">
                          <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                      </div><br>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                 </div>
                </div>
            </div>
          </div>
        </div>
      </section>
@endsection