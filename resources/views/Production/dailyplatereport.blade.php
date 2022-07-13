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
        debugger
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
            "url": "/production/daily/plate/report/list/api",
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
            {"data":"e_plate_size"},
            {"data":"total_plates"},
            {"data":"planned_plates"},
            {"data":"actual"},
            {"data":"wastage"},
            {"data":"reason"},
            {
              "targets": [ -1 ],
              "data":"id", "render": function(data,type,full,meta)
                {
                  debugger
                    var today = new Date();
                    var pland = new Date(full.planned_date);
                    var date = today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear();
                    var datecheck = pland.getDate()+'-'+(pland.getMonth()+1)+'-'+pland.getFullYear();
                    var x = "";
                    if(date == datecheck && full.left_plate != 0){

                      x = "<button class='btn btn-primary btn-xs' onclick='unusual("+data+","+full.element_id+","+full.planned_plates+","+full.plan_id+","+full.left_plate+")' data-toggle='modal' data-target='#myModal_of'> {{__('Create')}} </button> &nbsp;" ;
                    }
                    else{
                      x = "";
                    }
                    if(date < datecheck || full.actual == 0){
                      x+= "<button style='margin:5px;'class='btn btn-success btn-xs' onclick='edit_fn("+full.plan_id+","+full.planned_plates+")' data-toggle='modal' data-target='#myModal_edit'> Edit</button> &nbsp;" ;
                    }else{
                      x+= "";
                    }

                    return x;
                }
              }
            ],
            "columnDefs": [
               { "orderable": false, "targets": 11 }
            
            ]
          
        });
    }
 
 $('#myModal_of').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
});

 $('#myModal_edit').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
});
  function unusual(id,ele,plan,plan_id,left){
    debugger
   $("#element").val(ele);
   $("#jc_no").val(id);
   $("#planned").val(plan);
   $("#plan_id").val(plan_id);
   $("#left_p").val(left);
   $("#text_p").text(left);
  }
  function check(){
    debugger;
    var ac = $("#no_plate").val();
    var pl= $("#planned").val();
    var wa = $("#if_wastage").val();
    var formula = parseInt(ac)-parseInt(wa);
    if(parseInt(pl) >= formula){
      $("#plates_err").hide();
      return 1;
    }else{
      $("#plates_err").text("increasing the planned limit").show();
      return 0;
    }
  }

  function edit_fn(id,plate){
    
   $("#upd_no_plate").val(plate);
   $("#upd_planned").val(plate)
   $("#upd_text_plate").text(plate);
   $("#upd_plan_id").val(id);
  }
 function upd_check(){
  debugger
  var fetch = $("#upd_planned").val();
  var typed = $("#upd_no_plate").val();
   if(parseInt(fetch) < parseInt(typed)){
      $("upd_plates_err").val("increasing the planned limit").show();
      return 0;
   }else{
      $("upd_plates_err").hide();
      return 1;
   }
 }
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

                    <table id="plate" class="table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Job Card Number</th>
                          <th>Reference Name</th>
                          <th>Item Name</th>
                          <th>Creative Name</th>
                          <th>Element Name</th>
                          <th>Plate Size</th>
                          <th>Total Plate Required</th>
                          <th>Planned Plates</th>
                          <th>Actual Plates</th>
                          <th>Wastage</th>
                          <th>Reason for Wastage</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody class="table">

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
                    <form action="/prod/dailyplatereport/submitted" enctype="multipart/form-data" method="POST" id="plate_creationForm" >
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
                        <div class="col-md-6 {{ $errors->has('no_plate') ? 'has-error' : ''}}">
                            <label>Actual Plates <sup>*</sup> (Max :<span id="text_p"></span>)</label><br>
                              <input type="number" class="form-control input-css no_plate"  name="no_plate" id="no_plate" onKeyPress="if(this.value.length==10) return false;" onchange="check()">
                              <label id="plates_err" class="error"></label>
                              {!! $errors->first('no_plate', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="col-md-6 {{ $errors->has('if_wastage') ? 'has-error' : ''}}">
                          <label>Wastage If Any</label><br>
                          <input type="number" class="input-css form-control if_wastage" value="0" name="if_wastage" id="if_wastage" onchange="check()"
                          onKeyPress="if(this.value.length==10) return false;">
                          {!! $errors->first('if_wastage', '<p class="help-block">:message</p>') !!}
                        </div>   
                      </div><br>
                      <div class="row">
                          <div class="col-md-6 {{ $errors->has('reason_wastage') ? 'has-error' : ''}}">
                            <label>Reason For Wastage </label>
                            <textarea class=" input-css" name="reason_wastage" id="reason_wastage"></textarea>
                            
                            {!! $errors->first('reason_wastage', '<p class="help-block">:message</p>') !!}
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
                    <form action="/Production/dailyprocess/updation" enctype="multipart/form-data" method="POST" id="plate_updForm" >
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
                            <label>Number Of Plates Planned <sup>*</sup>(max : <span id="upd_text_plate"></span> plates)</label><br>
                              <input type="number" class="form-control input-css upd_no_plate"  name="upd_no_plate" id="upd_no_plate" onKeyPress="if(this.value.length==10) return false;" onchange="upd_check()">
                              <label id="upd_plates_err" class="error"></label>
                              {!! $errors->first('upd_no_plate', '<p class="help-block">:message</p>') !!}
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