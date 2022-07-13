@extends($layout)

@section('title', 'Employee Completed Six Month')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Employee Completed Six Month</a></li> 
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
   

    function oneyear(){
      
      dataTable1 = $('#table_six').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/employee/sixmonth/report/api",
          "columns": [
            {"data":"employee_number"},
              {"data":"name"},
              {"data":"mobile"},
              {"data":"department"},
              {"data":"designation"},
              {"data":"doj"},{"data" :"month", "render": function(data,type,full,meta){
                return full.month+"."+full.day+" months";
              }
            }
              // {"data" :"month"}
            
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 5 }
            
            ]
          
        });
    }

   
    // Data Tables
    $(document).ready(function() {
      
     oneyear();
     
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
                 
                  <table id="table_six" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                    <th>Employee Number</th> 
                      <th>Name</th>
                      <th>Number</th>
                      <th>Department</th>
                      <th>Designation</th>
                      <th>Joining Date</th>
                      <th>Months</th>
                     
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                       
                 </table>
                   
                     
                  
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection