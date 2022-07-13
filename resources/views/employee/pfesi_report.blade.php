@extends($layout)

@section('title', 'Not Enrolled In PF And ESI')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Not Enrolled In PF And ESI</a></li> 
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
.select2{
  width: 160px;
}
</style>
<link rel="stylesheet" href="/css/responsive.bootstrap.css">    
@endsection
@section('js')
<script src="/js/dataTables.responsive.js"></script>
  <script>
    var dataTable1;
    
   
    function notpfesi(){
       if(dataTable1){
        dataTable1.destroy();
      }  
      dataTable1 = $('#table_pfesi').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/employee/pfesi/report/both/api",
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"doj"}
            
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 5 }
            
            ]
          
        });
    }
    function notpf(){ 
      if(dataTable1){
        dataTable1.destroy();
      }   
      dataTable1 = $('#table_pf').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/employee/pfesi/report/pf/api",
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"doj"}
            
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 5 }
            
            ]
          
        });
    }
   function notesi(){
      if(dataTable1){
        dataTable1.destroy();
      }
        dataTable1 = $('#table_esi').DataTable({
            "processing": true,
            "serverSide": true,
            "aaSorting":[],
            "responsive": true,
            "ajax": "/employee/pfesi/report/esi/api",
            "columns": [
              {"data":"employee_number"},
                {"data":"name"},
                {"data":"mobile"},
                {"data":"department"},
                {"data":"designation"},
                {"data":"doj"}
              
              ],
              "columnDefs": [
                // { "orderable": false, "targets": 5 }
              
              ]
            
          });
      }

   $("#npe").click(function(){
        notpfesi();
    })
    $("#no_pf").click(function(){
        notpf();
    })
    $("#no_esi").click(function(){
        notesi();
    })
    // Data Tables
    $(document).ready(function() {
      
     notpfesi();
    
     
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
                  <div class="box-header ">
                      <div class="box-header ">
                        <ul class="nav nav1 nav-pills">
                          <li class="nav-item active" id="npe">
                            <a href="/employee/pfesi/report">Not Enrolled PF & ESI</a>
                          </li>
                          <li class="nav-item " id="no_pf">
                            <a href="/employee/pfesi/report/pf">Enrolled PF</a>
                          </li>
                          <li class="nav-item " id="no_esi">
                            <a href="/employee/pfesi/report/esi">Enrolled ESI</a>
                          </li>
                        </ul>
                      </div>
                  </div>
                  <div class="tab-content"> 
                    <div class="box-header with-border tab-pane fade active in" id="notpfandesi" >
                      <table id="table_pfesi" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                        <th>Employee Number</th> 
                          <th>Name</th>
                          <th>Number</th>
                          <th>Department</th>
                          <th>Designation</th>
                          <th>Joining Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                    <div class="box-header with-border tab-pane fade " id="notpf" >
                      <table id="table_pf" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                        <th>Employee Number</th> 
                          <th>Name</th>
                          <th>Number</th>
                          <th>Department</th>
                          <th>Designation</th>
                          <th>Joining Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                    <div class="box-header with-border tab-pane fade " id="notesi" >
                      <table id="table_esi" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                        <th>Employee Number</th> 
                          <th>Name</th>
                          <th>Number</th>
                          <th>Department</th>
                          <th>Designation</th>
                          <th>Joining Date</th>
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