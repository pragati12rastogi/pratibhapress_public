@extends($layout)

@section('title', 'Pending Dispatch Orders Financials')

@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Pending Dispatch Orders Financials</a></li> 
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
          "ajax": "/report/pending/dispatchorder/financial/list/api",
          "columns": [
            {"data":"io_number"},
            {"data":"name"},
            {"data":"created_time"},
           {"data":"item_name"},
            {"data":"referencename"},
            {"data":"io_qty"},
            {"data":"dispatch_qty"},
            {"data":"remaining_qty"},
            {"data":"io_rate"},
            {"data":"amount"}
            // {
            //   "targets": [ -1 ],
            //   "data":"id", "render": function(data,type,full,meta)
            //   {
            //     return "<a href='/clientpo' target='_blank'><button class='btn btn-primary btn-xs'> {{__('Create')}} </button></a> &nbsp;" //+ 
            //     //'<a href="/hsn/list/del/'+data+'"><button class="btn btn-success btn-xs"> {{__("hsn.hsn_list_Delete")}} </button></a>' 
            //     ;
            //   }
            //   }
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
            <a href="/export/data/pendingtaxdispatch/financial" ><button class="btn btn-primary">Export Tax Dispatch Financial</button></a> 
            @endsection
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="table" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Internal Order Number</th>
                          <th>IO Type</th>
                          <th>IO Date</th>
                         <th>Item Name</th>
                          <th>Reference Name</th>
                          <th>IO Quantity</th>
                          <th>Dispatch Quantity</th>
                          <th>Remaining Quantity</th>
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