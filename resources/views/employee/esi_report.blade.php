@extends($layout)

@section('title', 'Enrolled In ESI')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Enrolled In ESI</a></li> 
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
                {"data":"doj"},
                {"data":"esi_no"},{"data":"esi_date"}
                
              
              ],
              "columnDefs": [
                // { "orderable": false, "targets": 5 }
              
              ]
            
          });
      }

   
    // Data Tables
    $(document).ready(function() {
      
     notesi();
    
     
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
                          <li class="nav-item " id="npe">
                            <a  href="/employee/pfesi/report">Not Enrolled PF & ESI</a>
                          </li>
                          <li class="nav-item " id="no_pf">
                            <a  href="/employee/pfesi/report/pf">Enrolled PF</a>
                          </li>
                          <li class="nav-item active" id="no_esi">
                            <a  href="/employee/pfesi/report/esi">Enrolled ESI</a>
                          </li>
                        </ul>
                      </div>
                  </div>
                  <div class="tab-content"> 
                    <div class="box-header with-border tab-pane fade active in" id="notesi" >
                      <table id="table_esi" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                        <th>Employee Number</th> 
                          <th>Name</th>
                          <th>Number</th>
                          <th>Department</th>
                          <th>Designation</th>
                          <th>Joining Date</th>
                          <th>ESI Number</th>
                          <th>ESI Date</th>
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