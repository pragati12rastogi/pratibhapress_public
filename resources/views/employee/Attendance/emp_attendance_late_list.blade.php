@extends($layout)
@section('title', 'Late Calculation Summary')
@section('breadcrumb')
    <li><a href="#"><i class=""></i>Late Calculation Summary</a></li> 
@endsection
@section('css')
<style>
   .content{
    padding: 30px;
  } 
@media (max-width: 768px) {
    .content-header>h1 {
      display: inline-block;
    }
  }
  @media (max-width: 425px){
    .content-header>h1 {
      display: inline-block; 
    }
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
      dataTable = $('#table').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/hr/attendance/late/summary/api",
          "columns": [
              {"data":"employee_number"},
              {"data":"name"},
              {"data":"date"},
              {"data":"in_time"},
              {"data":"out_time"},
              {"data":"shift_from",
                "render": function(data,type,full,meta){ 
                    return 'From : '+full.shift_from + ' - To :' + full.shift_to;
                  }
              },
              {"data":"time"},
              {"data":"duration"},
              {"data" : "late"},
              {"data":"status"}
            ],
            "columnDefs": [
              { "orderable": false, "targets": 0 },
              { "orderable": false, "targets": 6 },
              { "orderable": false, "targets": 7 },
              { "orderable": false, "targets": 8 },
              { "orderable": false, "targets": 9 }
            ]
        });
    });


  </script>
@endsection

@section('main_section')
    <section class="content">
                <div id="app">
                    @include('sections.flash-message')
                    @yield('content')
                </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Employee Id</th>
                      <th>Name</th>
                      <th>Date</th>
                      <th>In Time</th>
                      <th>Out Time</th>
                      <th>Shift Time</th>
                      <th>Shift</th>
                      <th>Total Work Time</th>
                      <th>Late</th>
                      <th>Status</th>
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