@extends($layout)

@section('title', 'Delegation Evaluation Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Delegation Evaluation Summary</a></li> 
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
  .nav-pills>li {
    
    border: 1px solid #a9a0a0;
  }
</style>
 
@endsection
@section('js')

<script src="/js/dataTables.responsive.js"></script>
<script>
  var dataTable;

  // Data Tables
  $(document).ready(function() {
    pending();
    $(".select2").css('width','280px');
  });

  function pending(){
    if(dataTable){
      dataTable.destroy();
    }
    dataTable = $('#table_pend').DataTable({
      "processing": true,
      "serverSide": true,
      "aaSorting":[],
      "responsive": true,
      "ajax": {
        "url": "/delegation/employee/completed/api",
        "datatype": "json",
            "data": function (data) {
                var c_date = $('#c_date').val();
                data.c_date = c_date;
            }
        },
        "columns": [
          {"data":"name","render": function(data,type,full,meta){
            return data +"("+full.employee_number+")";
          }},
          {"data":"task_detail"},
          {"data":"assign_date"},
          {"data":"deadline"},
          {"data":"requirements"},
          {"data":"completion_date"},
          {"data":"delegation_status"},
          {"data":"detail"},
          {"data":"job_image","render": function(data,type,full,meta){
            return "<a href='/upload/completed_job_image/"+data+"' target='_blank'>Image</a> &nbsp;";
          }},
          {"data":"id","render":function(data,type,full,meta){
            return "<button id="+data+" onClick='getid("+data+")' class='comple btn btn-primary btn-xs' data-toggle='modal' data-target='#myModal_comp_stat'>Status</button> &nbsp;";
          }}
        ],
        "columnDefs": [
          { "orderable": false, "targets": 9 }
        ]
        
    });
  }

  function getid(i){
    var elmId = i;
    $("#comp_id").val(elmId);
  }

  function done(){
    if(dataTable){
      dataTable.destroy();
    }
    dataTable = $('#table_done').DataTable({
      "processing": true,
      "serverSide": true,
      "aaSorting":[],
      "responsive": true,
      "ajax": "/delegation/ea/completed/api",
      "columns": [
        {"data":"name","render": function(data,type,full,meta){
          return data +"("+full.employee_number+")";
        }},
        {"data":"task_detail"},
        {"data":"assign_date"},
        // {"data":"deadline"},
        {"data":"requirements"},
        {"data":"completion_date"},
        {"data":"detail"},
        {"data":"job_image","render": function(data,type,full,meta){
          return "<a href='/upload/completed_job_image/"+data+"' target='_blank'>Image</a> &nbsp;";
        }},
        {"data":"final_status"},
        {"data":"evaluation_reason"}
        
      ],
      "columnDefs": [
        { "orderable": false, "targets": 6 }
      ]
        
    });
  }

  $("#done").click(function(){
    done();
  })
  $("#pid").click(function(){
    pending();
  })

  function show_as_stat()
  { 
    var status = $("#status").val();
    if(status == 'completed'){
      $("#notcompleted_div").hide();
      $("#reason").removeAttr('required');
    }else if(status == 'not completed'){
      $("#notcompleted_div").show();
      $("#reason").attr('required','required');
    }else{
      $("#notcompleted_div").hide();
      $("#reason").removeAttr('required');
    }
  }
  
  $('#myModal_comp_stat').on('hidden.bs.modal', function(){
    $(this).find('form')[0].reset();
    $(".select2").val('').trigger("change");
  });
  $('#completion_st_form').submit(function(e){     

    e.preventDefault();
    
    var formvalidation=$("#completion_st_form").valid();
    var formData = new FormData(this);
    
      if(formvalidation==true)
      {
        $('#ajax_loader_div').css('display','block');
        $.ajax({
            type:'POST',
            url: "/delegation/final/status/update",
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(result) {
            // debugger;
              $('#ajax_loader_div').css('display','none');
              if((result.error).length > 0){
                $("#fs_err").text(result.error).show();
                setTimeout(function() { 
                    $('#fs_err').fadeOut('fast'); 
                }, 8000);
              }else if((result.msg).length > 0){
                 $('#myModal_comp_stat').modal('hide');
                 pending();
                 $(".goodmsg").show();
                 $("#mesg").text(result.msg);
              }
            }
          });
      }
  });
</script>

@endsection

@section('main_section')
    <section class="content">
            <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
            <div class="alert alert-success alert-block goodmsg" style="display: none;">
              <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong id="mesg"></strong>
            </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    
                      @endsection
                    <div class="box-header ">
                      <div class="box-header ">
                        <ul class="nav nav1 nav-pills">
                          <li class="nav-item active" id="pid">
                            <a data-toggle="pill" href="#pending_box">Pending</a>
                          </li>
                          <li class="nav-item " id="done">
                            <a data-toggle="pill" href="#done_box">Completed</a>
                          </li>
                          
                        </ul>
                      </div>
                    </div>
                    <div class="tab-content"> 
                      <div class="box-header with-border tab-pane fade active in" id="pending_box" >
                          <table id="table_pend" class="table table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th>Employee</th>
                                  <th>Task</th>
                                  <th>Assign Date</th>
                                  <th>Deadline</th>
                                  <th>Requirement</th>
                                  <th>Completion Date</th>
                                  <th>Delegation Status</th>
                                  <th>Detail</th>
                                  <th>Image</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                          </table>
                          <div id="myModal_comp_stat" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                          
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Add Status</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" id="completion_st_form" method="post" enctype="multipart/form-data">
                                          @csrf
                                          <span id="fs_err" style="color:red; display: none;"></span>
                                          <input type="text" name="comp_id" id="comp_id" hidden>
                                          <div class="row">
                                            <div class="col-md-6 {{ $errors->has('status') ? 'has-error' : ''}}">
                                                <label for="">Status<sup>*</sup></label>
                                                <select name="status" id="status" class="input-css select2 status" onchange="show_as_stat()" required="">
                                                  <option value="">Select Status</option>
                                                  <option value="completed">Completed</option>
                                                  <option value="not completed">Not Completed</option>
                                                </select>
                                                {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
                                            </div>
                                          </div><br><br>
                                          <div class="row" id="notcompleted_div" style="display: none;">
                                              <div class="col-md-6 {{ $errors->has('reason') ? 'has-error' : ''}}">
                                                <label for="">Reason<sup>*</sup></label>
                                                <textarea id="reason" name="reason" class="reason input-css" ></textarea>
                                                {!! $errors->first('reason', '<p class="help-block">:message</p>') !!}
                                              </div>
                                          </div><br>
                                          <div class="modal-footer">
                                              <input type="submit" value="Update" class="btn btn-primary">&nbsp;&nbsp;
                                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                          </div>
                                        </form>
                                    </div>
                
                                </div>
                            </div>
                          </div>
                      </div>
                      <div class="box-header with-border tab-pane fade " id="done_box" >
                          <table id="table_done" class="table table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th>Employee</th>
                                  <th>Task</th>
                                  <th>Assign Date</th>
                                  <!-- <th>Deadline</th> -->
                                  <th>Requirement</th>
                                  <th>Completion Date</th>
                                  <th>Detail</th>
                                  <th>Image</th>
                                  <th>After Evalution Status</th>
                                  <th>Reason</th>
                                  
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                          </table>
                      </div>
                    </div>
                  
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection