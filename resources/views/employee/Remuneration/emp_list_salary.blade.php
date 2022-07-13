@extends($layout)

@section('title', 'Employee Remuneration List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Employee Remuneration List</a></li> 
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
      dataTable = $('#working').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/employeeworking/list/api",
          "columns": [
            {"data":"name_with_code"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"doj"},
                   {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/employee/salary/form/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> Salary </button></a> &nbsp;"+
                    "<button class='btn btn-warning btn-xs' onclick='ctc_fn("+data+")' data-toggle='modal' data-target='#myModal_ctc'> CTC Calculator </button> &nbsp;"
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 }
            
            ]
        });
    });

function ctc_fn(empid){
  $('#ajax_loader_div').css('display','block');
  $.ajax({
      type:'get',
      url: "/salary/ctc/calculator",
      data: {'empid':empid},
      contentType: "application/json",
      dataType: "json",
      success:function(result) {
        $('#ajax_loader_div').css('display','none');
        $("#ctc_data").empty();
        
        if(result.salary_c != null){
          $.each(result.salary_c, function(key, value) {
            if(key != "bonus" ){
              $("#ctc_data").append('<td>' + value + '</td>');
            }
          });
        }else{
          $("#ctc_data").append('<td colspan ="6" align="center">No data available</td>')
        }

        if(result.pf != null){
          $("#pf").text(parseFloat(result.pf).toFixed(2));
        }
        if(result.esi != null){
          $("#esi").text(parseFloat(result.esi).toFixed(2));
        }
        if(result.leave_encash != null){
          $("#leave_encash").text(parseFloat(result.leave_encash).toFixed(2));
        }
        if(result.bonus != null){
          $("#bonus").text(parseFloat(result.bonus).toFixed(2));
        }
        if(result.gratuity != null){
          $("#gratuity").text(parseFloat(result.gratuity).toFixed(2));
        }
        if(result.month_ctc != null){
          $("#month_ctc").text(parseFloat(result.month_ctc).toFixed(2));
        }
        if(result.year_ctc != null){
          $("#year_ctc").text(parseFloat(result.year_ctc).toFixed(2));
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
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    
                      @endsection

                       
                    
                         <div class="row">
                           <table id="working" class="table table-bordered table-striped" >
                              <thead>
                                <tr>
                                 <th>Employee Name</th> 
                                  <th>Number</th>
                                  <th>Department</th>
                                  <th>Designation</th>
                                  <th>Date Of Joining</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                         
                          </table>
                         </div>
                        

                         <div id="myModal_ctc" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                          
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">CTC Calculator</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                          <table class="table table-bordered table-striped" >
                                              <thead>
                                                <tr>
                                                 <th>Basic</th> 
                                                  <th>DA</th>
                                                  <th>HRA</th>
                                                  <th>Conveyance allowance</th>
                                                  <th>Telephone allowance</th>
                                                  <th>Other allowance</th>
                                                  <th>WC Premium Amount</th>
                                                </tr>
                                              </thead>
                                              <tbody >
                                                <tr id="ctc_data"></tr>
                                              </tbody>
                                          </table>
                                        </div><br>
                                        <div class="row">
                                          <div  class="col-md-4">
                                            <label class="md_label"> PF : </label><span id="pf"></span>
                                          </div>
                                          <div  class="col-md-4">
                                            <label class="md_label"> ESI : </label><span id="esi"></span>
                                          </div>
                                          <div  class="col-md-4">
                                            <label class="md_label"> Leave Encashment : </label><span id="leave_encash"></span>
                                          </div>
                                        </div><br>
                                        <div class="row">
                                          <div  class="col-md-4">
                                            <label class="md_label"> Bonus  : </label><span id="bonus"></span>
                                          </div>
                                          <div  class="col-md-4">
                                            <label class="md_label"> Gratuity  : </label><span id="gratuity"></span>
                                          </div>
                                          <div  class="col-md-4">
                                            <label class="md_label"> Monthly CTC : </label><span id="month_ctc"></span>
                                          </div>
                                        </div><br>
                                        <div class="row">
                                          <div  class="col-md-4">
                                            <label class="md_label"> Annual CTC  : </label><span id="year_ctc"></span>
                                          </div>
                                          
                                        </div><br>
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