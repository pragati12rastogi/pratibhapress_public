@extends($layout)

@section('title', 'K Sampling & FOC Orders')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>K Sampling & FOC Orders </a></li> 
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
          "ajax": "/report/ksampling/foc/order/api",
          "columns": [
            {"data":"io_number"},
            {"data":"created_time"},
            {"data":"io_type"},
           {"data":"referencename"},
            {"data":"item_name"},
            {"data":"io_qty"},
            {"data":"io_rate"},
            {"data":"amount"},
            {"data":"advance_amt"},
            // {"data":"advance_mode"},
            { 
                data: function(data, type, full, meta){

                  if(data.advance_amt==0)
                    return "";
                  else
                    return data.advance_mode;
                } 
            },
            {"data":"balance"}
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
            <a href="/export/data/ksamplingandfocorder" ><button class="btn btn-primary">Export K Sampling and FOC</button></a>
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
                          <th>Advance Amount</th>
                          <th>Advance Mode</th>
                          <th>Balance</th>
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