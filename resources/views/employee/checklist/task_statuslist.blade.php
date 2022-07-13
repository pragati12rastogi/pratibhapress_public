@extends($layout)

@section('title', 'Task Status Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Task Status Summary</a></li> 
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
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable;

    // Data Tables
    $(document).ready(function() {
      pending();
    });

    function done(){
      if(dataTable){
        dataTable.destroy();
      }
      dataTable = $('#table_done').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/checklist/superadmin/task/status/list/api",
            "datatype": "json",
                "data": function (data) {
                    var employee = $('#emp').val();
                    data.employee = employee;
                }
            },
          "createdRow": function( row, data, dataIndex){
                if( data.score ==  'Work Not Done'){
                    $(row).addClass('bg-red');
                }else if(data.score ==  'Work Done'){
                  $(row).addClass('bg-green');
                }else if(data.score ==  'Late'){
                  $(row).addClass('bg-yellow');
                }
            },
          "columns": [
              {"data":"name","render": function(data,type,full,meta){
                return data +"("+full.employee_number+")";
              }},
              
              {"data":"task_name"},
              {"data":"st_date"},
              
            ],
            "columnDefs": [
             
              // { "orderable": false, "targets": 7 }
            ]
          
        });
    }

    function pending(){
      if(dataTable){
        dataTable.destroy();
      }
      dataTable = $('#table_ani').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": {
            "url": "/checklist/superadmin/status/pending/list/api",
            "datatype": "json",
                "data": function (data) {
                    var employee = $('#pen_emp').val();
                    data.employee = employee;
                }
            },
          "columns": [
              {"data":"name","render": function(data,type,full,meta){
                return data +"("+full.employee_number+")";
              }},
              
              {"data":"task_name"},
              {"data":"st_date"},
               {"data":"status","render": function(data,type,full,meta){
                // console.log(full);
                  // if(full.userid == null){
                    var now = new Date();
                    var form_date =now.toISOString().substr(0,10);
                    var js_Day =now.getDay();
                    var holiday=[];
                    if($("#full_holi_dates").text() != null || $("#full_holi_dates").text() != ''){
                      holiday = $("#full_holi_dates").text().split(',');
                    }

                    if(full.st_date<=form_date && !holiday.includes(form_date) && js_Day!=0){
                      return "<a href='/chklist/super/status/upd/Done/"+full.id+"' ><button class='btn btn-success btn-xs'onClick='return confirm("+'"Are you sure you want to update task status as Done?"'+");'>Done </button></a> &nbsp;"+ "<a href='/chklist/super/status/upd/Not Done/"+full.id+"' ><button class='btn btn-danger btn-xs'onClick='return confirm("+'"Are you sure you want to update task status as Not Done?"'+");'>Not Done</button></a> &nbsp;"+ "<a href='/chklist/super/status/upd/Not Required/"+full.id+"' ><button class='btn btn-facebook btn-xs'onClick='return confirm("+'"Are you sure you want to update task status as Not Required?"'+");'>Not Required</button></a> &nbsp;";
                    }else{
                      return "";
                    }
               }} 
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 3 }
            ]
          
        });
    }

$("#done").click(function(){
        done();
    })
$("#pid").click(function(){
        pending();
    })
function filter1() {
  dataTable.draw();
}
function filter2() {
  dataTable.draw();
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
                    <p id="full_holi_dates" hidden="">{{$holiday}}</p>
                    <div class="box-header ">
                      <div class="box-header ">
                        <ul class="nav nav1 nav-pills">
                          <li class="nav-item active" id="pid">
                            <a data-toggle="pill" href="#pending_box">Pending</a>
                          </li>
                          <li class="nav-item " id="done">
                            <a data-toggle="pill" href="#done_box">Done</a>
                          </li>
                          
                        </ul>
                      </div>
                    </div>
                    <div class="tab-content"> 
                      <div class="box-header with-border tab-pane fade active in" id="pending_box" >
                        <div class="col-md-4 pull-right ">
                          <label>Employee</label>
                          <select id="pen_emp" name="pen_emp" class="input-css select2" onchange="filter2()">
                            <option value="">Select Employee</option>
                            @foreach($employee as $emp)
                              <option value="{{$emp->id}}">{{$emp->name}}</option>
                            @endforeach
                          </select>                        
                        </div>
                          <table id="table_ani" class="table table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th>Employee</th>
                                  
                                  <th>Task</th>
                                  
                                  <th>Task Date</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                         
                          </table>
                      </div>
                      <div class="box-header with-border tab-pane fade " id="done_box" >
                          
                          <div class="col-md-4 pull-right ">
                            <label>Employee</label>
                            <select id="emp" name="emp" class="input-css select2" onchange="filter1()">
                              <option value="">Select Employee</option>
                              @foreach($employee as $emp)
                                <option value="{{$emp->id}}">{{$emp->name}}</option>
                              @endforeach
                            </select>                        
                          </div>
                          <table id="table_done" class="table table-bordered table-striped">
                              <thead>
                              <tr>
                                <th>Employee</th>
                                
                                <th>Task</th>
                                
                                <th>Task Month</th>
                               
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