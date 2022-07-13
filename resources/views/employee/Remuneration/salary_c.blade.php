@extends($layout)

@section('title', 'Salary Summary C')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Salary Summary C</a></li> 
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
      dataTable = $('#salary_ab').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/salary/list/c/api",
          "columns": [
            {"data":"employee_number"},
            {"data":"name"},
            {"data":"basic_salary"},
            {"data":"dearness_allowance"},
            {"data":"hra"},
            {"data":"conveyance"},
            {"data":"telephone"},
            {"data":"other"},
            {"data":"total_c"}
            ],
            "columnDefs": [
              { 
                // "orderable": false, "targets": 5
                 }
            
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
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    
                      @endsection

                       
                    
                         <div class="row">
                           <table id="salary_ab" class="table table-bordered table-striped" >
                              <thead>
                                <tr>
                                 <th>Employee Code</th> 
                                  <th>Employee Name</th>
                                  <th>Basic</th>
                                  <th>DA</th>
                                  <th>HRA</th>
                                  <th>Conveyance Allowance</th>
                                  <th>Telephone Allowance</th>
                                  <th>Other allowance</th>
                                  <th>Total salary</th>
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                         
                          </table>
                         </div>
                          
                 
            
                </div>
                <!-- /.box-body -->
              </div>
        <!-- /.box -->
      </section>
@endsection