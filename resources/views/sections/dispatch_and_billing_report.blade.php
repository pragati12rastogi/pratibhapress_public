@extends($layout)

@section('title', 'Dispatch VS Billing Gap')

@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i>Dispatch VS Billing Gap</a></li> 
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
          "ajax": "/report/pending/dispatchVsbilling/api",
          "columns": [
            {"data":"io_number"},
            {"data":"name"},
            {"data":"created_time"},
            {"data":"referencename"},
            {"data":"item_name"},
            {"data":"io_qty"},
            // {"data":"dispatch_qty"},
            // {"data":"taxqty"},
            { 
                "data":"dispatch_qty","render": function(data, type, full, meta){
                  if(data)
                  return data;
                  else
                    return "-";
                } 
            },
            { 
                "data":"taxqty","render": function(data, type, full, meta){
                  if(data)
                  return data;
                  else
                    return "-";
                } 
            },
            { "data" : function(data, type, full, meta){
                  if(data.unbilled_qty){
                    return data.unbilled_qty;
                  }
                 
                  else{
                    var dc=data.dispatch_qty;
                    var ti=data.taxqty;
                    if(!dc)
                        dc=0;
                    if(!ti)
                        ti=0;

                     return dc-ti;
                  }
                    
                    
                } 
            },
            { "data" : function(data, type, full, meta){
                  
                    var io=data.io_qty;
                    var ti=data.taxqty;
                    if(!io)
                        io=0;
                    if(!ti)
                        ti=0;

                     return io-ti;
                  
                    
                    
                } 
            }
            // {"data":"unbilled_qty"}
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
                    <a href="/export/data/dispatchvsbilling" ><button class="btn btn-primary">Export Dispatch and Billing Report</button></a> 
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
                    <th>Dispatch Qty</th>
                    <th>Tax Invoice Qty</th>
                    <th>Dispatched Unbilled Quantity</th>
                    <th>Unbilled Order Quantity</th>
                    
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
               
                  </table>
                </div>
                
              </div>
        
      </section>
@endsection