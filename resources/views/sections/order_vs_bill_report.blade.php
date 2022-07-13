@extends($layout)

@section('title', 'Order VS Billing')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Order VS Billing</a></li> 
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
          "ajax": "/report/ordervsbilling/api",
          "columns": [
            {"data":"io_number"},
            {"data":"name"},
            {"data":"created_time"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"io_qty"},
            {"data":"taxqty"},
            {"data":"unbilled_qty"},
            {"data":"tax_invoice_no","render": function(data, type, full, meta){
                  if(data){
                    var i = 0, strLength = data.length;
                    for(i; i < strLength; i++) {
                        data = data.replace(",", "<br>");
                    }
                    return data;
                  }
                  else
                    return "";
                } 
            }
            
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
                    <a href="/export/data/ordervsbilling" ><button class="btn btn-primary">Export Order Vs Billing Report</button></a> 
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
                    <th>Unbilled Order Qty</th>
                    <th>Invoice Number</th>
                   
                    
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
               
                  </table>
                </div>
                
              </div>
        
      </section>
@endsection