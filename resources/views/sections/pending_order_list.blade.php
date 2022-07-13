@extends($layout)

@section('title', 'Unbilled Service IOs')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Unbilled Service IOs</a></li> 
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
     
      dataTable = $('#pendingOrder').DataTable({
          "processing": true,
          "serverSide": true,
          "aaSorting":[],
          "responsive": true,
          "ajax": "/report/pending/order/list/api",
          "columns": [
            {"data":"io_number"},
            {"data":"name"},
            {"data":"created_time"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"IO_qty"},
            {"data":"taxqty"},
            {"data":"diffqty"}
            
            ],
            "columnDefs": [
             
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
        
        <div class="box">
            @section('titlebutton')
            <a href="/export/data/pendingtaxinvoice" ><button class="btn btn-primary">Export Unbilled Service IOs</button></a> 
            @endsection
                <div class="box-body">
                    <table id="pendingOrder" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                    <th>IO Number</th> 
                    <th>IO Type</th>
                    <th>IO Date</th>
                    <th>Reference</th>
                    <th>Item Name</th>
                    <th>IO Qty</th>
                    <th>Tax Invoice Qty</th>
                    <th>Remainings</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
               
                  </table>
                </div>
                
              </div>
        
      </section>
@endsection