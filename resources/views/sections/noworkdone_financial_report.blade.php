@extends($layout)

@section('title', 'No Work Done IOs Financials')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>No Work Done IOs Financials </a></li> 
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
          "ajax": "/report/noworkdone/io/financial/list/api",
          "columns": [
            {"data":"io_number"},
            {"data":"created_time"},
            {"data":"name"},
           {"data":"referencename"},
            {"data":"item_name"},
            {"data":"io_qty"},            
            {"data":"io_rate"},
            {"data":"amount"},
            ],
            "columnDefs": [
              // { "orderable": false, "targets": 0 }
            
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
            @section('titlebutton')
            <a href="/export/data/noworkdoneiofinancial" ><button class="btn btn-primary">Export No Work Done IO's Financial</button></a>
            @endsection
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>IO Number</th>
                          <th>IO Date</th>
                          <th>IO Type</th>
                          <th>Reference Name</th>
                          <th>Item Name</th>
                          <th>IO Quantity</th>
                          <th>IO Rate</th>
                          <th>Amount</th>
                          
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