@extends($layout)

@section('title', 'Employee List')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Employee List</a></li> 
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
   
   function first(){
    if(dataTable){
        dataTable.destroy();
      }
      dataTable = $('#table1').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/employee/profile/list/api",
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
                   {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/employee/profile/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" + 
                    '<a href="/employee/profile/view/'+data+'" target="_blank"><button class="btn btn-success btn-xs"> View </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 5 }
            
            ]
          
        });
   }

    // Data Tables
    $(document).ready(function() {
      first();
    });

    $("#allopen").click(function(){
        first();
    });
$('#leftopen').click(function(){
   if(dataTable){
        dataTable.destroy();
      }
  dataTable = $('#table2').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/employee/left/list/api",
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"doj"},
              {"data":"leaving_date"},
                   {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/employee/profile/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" + 
                    '<a href="/employee/profile/view/'+data+'" target="_blank"><button class="btn btn-success btn-xs"> View </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 7 }
            
            ]
          
        });
})
$("#workingoOpen").click(function(){
  if(dataTable){
        dataTable.destroy();
      }
         dataTable = $('#table3').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/employee/working/list/api",
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"doj"},
                   {
                  "targets": [ -1 ],
                  "data":"id", "render": function(data,type,full,meta)
                  {
                    return "<a href='/employee/profile/update/"+data+"' target='_blank'><button class='btn btn-primary btn-xs'> {{__('hsn.hsn_list_Edit')}} </button></a> &nbsp;" +
                     '<a href="/employee/profile/view/'+data+'" target="_blank"><button class="btn btn-success btn-xs"> View </button></a>' 
                    ; 
                    // '<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
                    ;
                  }
              }
            ],
            "columnDefs": [
              { "orderable": false, "targets": 6 }
            
            ]
          
        });
})
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
                    <a href="/export/data/employee"><button class="btn btn-sm btn-primary">Export Employee</button></a>
                      @endsection

                       <div class="box-header">
                          <div class="box-header ">
                            <ul class="nav nav1 nav-pills">
                              <li class="nav-item active" id="allopen">
                                <a data-toggle="pill" href="#all">All Employees</a>
                              </li>
                              <li class="nav-item " id="leftopen" >
                                <a data-toggle="pill" href="#left">Left Employees</a>
                              </li>
                              <li class="nav-item " id="workingoOpen">
                                <a data-toggle="pill" href="#working">Working Employees</a>
                              </li>
                            </ul>
                          </div>
                      </div>
                  <div class="tab-content"> 
                    <div class="box-header with-border tab-pane fade active in" id="all" >
                      <div class="row">
                        <table id="table1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                            <th>Employee Number</th> 
                              <th>Name</th>
                              <th>Number</th>
                              <th>Department</th>
                              <th>Designation</th>
                              
                              <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                       
                        </table>
                      </div>
                    </div>
                     <div class="box-header with-border tab-pane fade " id="left" >
                         <div class="row">
                          <table id="table2" class="table table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th>Employee Number</th> 
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Date Of Joining</th>
                                    <th>Leaving Date</th>
                                    <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                         
                          </table>
                        </div>
                    </div>
                    <div class="box-header with-border tab-pane fade " id="working" >
                         <div class="row">
                           <table id="table3" class="table table-bordered table-striped" >
                              <thead>
                                <tr>
                                 <th>Employee Number</th> 
                                  <th>Name</th>
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
                          
                    </div>
                  </div>
                    <!-- <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                    <th>Employee Number</th> 
                      <th>Name</th>
                      <th>Number</th>
                      <th>Department</th>
                      <th>Designation</th>
                      <th>Action</th>
                     
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
               
                  </table> -->
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection