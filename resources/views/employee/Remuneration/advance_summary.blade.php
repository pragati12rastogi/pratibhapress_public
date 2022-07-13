@extends($layout)

@section('title', 'Advance Summary ')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Advance Summary </a></li> 
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
.md_label{
  display: inline;
}
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;
   
    // Data Tables
    $(document).ready(function() {
      advance();
    });

    function advance(){
      // debugger;
     
        if(dataTable){
          dataTable.destroy();
        }
        dataTable = $('#advance').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/advance/summary/api",
          "columns": [
              {"data":"emp_name"},
              {"data":"requested_amount"},
              {"data":"advance_amount"},
              {"data":"advance_balance"},
              {"data":"installment"},
              {"data":"reason"},
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    if(full.advance_amount != 0){
                      return "<button class='btn btn-primary btn-xs' onclick='open_detail("+data+")' data-toggle='modal' data-target='#details_model'>Detail </button> &nbsp;";
                    }else{
                      return "<button class='btn btn-warning btn-xs' onclick='approve_fn("+data+","+full.requested_amount +")' data-toggle='modal' data-target='#myModal_approve'>Approve </button> &nbsp;"
                      ;
                    }
                    
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 6 }
            
            ]
        });
      
    }
    function close(){
      // debugger;
     
        if(dataTable){
          dataTable.destroy();
        }
        dataTable = $('#close').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/advance/summary/close/api",
          "columns": [
              {"data":"emp_name"},
              {"data":"advance_amount"},
              {"data":"advance_balance"},
              {"data":"installment"},
              {"data":"reason"},
              {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                      return "<button class='btn btn-primary btn-xs' onclick='open_detail("+data+")' data-toggle='modal' data-target='#details_model'>Detail </button> &nbsp;";
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 }
            
            ]
        });
      
    }
$("#done").click(function(){
        close();
    })
$("#pid").click(function(){
        advance();
      })

function approve_fn(id,max_amt)
{
  $("#row_id").text(id);
  $("#max_amt").text(max_amt);
  $("#apr_adv_amt").attr({
   "max" : max_amt
  });
}
function submit_amt(){
  var amt = $('#apr_adv_amt').val();
  var max = $("#max_amt").text();
  var id = $("#row_id").text();
  if(parseInt(amt) > parseInt(max)){
    $('#adv_err').text("Enter amount is greater than requested");
  }else if(amt == null && amt== 0){
    $('#adv_err').text("Required field");
  }else{
    $('#adv_err').text("");

    $.ajax({
      type:'get',
      url: "/advance/approval",
      data: {'amt':amt,'id':id},
      contentType: "application/json",
      dataType: "json",
      success:function(result) {
      
        console.log(result);
        if((result.error).length > 0){
                $("#fs_err").text(result.error).show();
                setTimeout(function() { 
                    $('#fs_err').fadeOut('fast'); 
                }, 8000);
              }else if((result.success).length > 0){
                 $('#myModal_approve').modal('hide');
                 advance();
                 $(".goodmsg").show();
                 $("#mesg").text(result.success);
              }
            }
    });
  }
}
function open_detail(id){
  $('#ajax_loader_div').css('display','block');
    $.ajax({
      type:'get',
      url: "/advance/paid/list",
      data: {'id':id},
      contentType: "application/json",
      dataType: "json",
      success:function(result) {
        $('#ajax_loader_div').css('display','none');

        console.log(result);
        $("#paid_list").empty();
        if(result.length > 0){
          $.each(result, function(key, value) {
              $("#paid_list").append('<tr><td>' + value.amount_paid + '</td><td>' + value.paid_category + '</td><td>' + value.created_date + '</td></tr>');
          });
        }else{
          $("#paid_list").append('<tr><td colspan ="3" align="center">No data available</td></tr>');
        }  
      }
    });
}
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
                            <a data-toggle="pill" href="#close_box">Close</a>
                          </li>
                          
                        </ul>
                      </div>
                    </div>
                  <div class="tab-content"> 
                      <div class="box-header with-border tab-pane fade active in" id="pending_box" >  
                        <div class="row" >
                         <table id="advance" class="table table-bordered table-striped" >
                            <thead>
                              <tr>
                               
                               <th>Employee Name</th> 
                                <th>Requested Advance amount</th> 
                                <th>Original Advance amount</th>
                                <th>Advance Balance</th>
                                <th>Deduction Installments</th>
                                <th>Advance Reason</th>
                                <th>Action</th>
                                
                              </tr>
                            </thead>
                            <tbody>

                            </tbody>
                       
                        </table>
                       </div>
                     </div>
                     <div class="box-header with-border tab-pane fade " id="close_box" >  
                        <div class="row" >
                         <table id="close" class="table table-bordered table-striped" >
                            <thead>
                              <tr>
                               
                               <th>Employee Name</th> 
                                <th>Original Advance amount</th>
                                <th>Advance Balance</th>
                                <th>Deduction Installments</th>
                                <th>Advance Reason</th>
                                <th>Action</th>
                                
                              </tr>
                            </thead>
                            <tbody>

                            </tbody>
                       
                        </table>
                       </div>
                     </div>
                      <div id="myModal_approve" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                          
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Approval</h4>
                                    </div>
                                    <div class="modal-body">
                                      <span id="row_id" hidden="hidden"></span>
                                      <span id="fs_err" style="color:red; display: none;"></span>
                                        <div class="row">
                                          <div  class="col-md-4">
                                            <label class="md_label"> Advance Amount: <sup>*</sup> Max(<span id="max_amt"></span>)</label>
                                            <input type="number" name="apr_adv_amt" class="input-css apr_adv_amt" name="apr_adv_amt" id="apr_adv_amt" min="0">
                                            <label class="error" id="adv_err"></label>
                                          </div>
                                          
                                        </div><br>
                                       <div class="row">
                                      <div class="col-md-12">
                                           <input type="button" style="float:right" class="btn btn-primary" value="Approve" onclick="submit_amt()">
                                      </div>
                                  </div>
                                    </div>
                                  
                                </div>
                            </div>
                      </div>

                      <div id="details_model" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                          
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Advance Paid Detail</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                          <table class="table table-bordered table-striped" >
                                              <thead>
                                                <tr>
                                                 <th>Amount</th> 
                                                  <th>Paid By</th>
                                                  <th>Date</th>
                                                </tr>
                                              </thead>
                                              <tbody id="paid_list">
                                                <tr ></tr>
                                              </tbody>
                                          </table>
                                        </div><br>
                                        
                                    </div>
                
                                </div>
                            </div>
                          </div>
                   </div>
                    
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection