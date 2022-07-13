@extends($layout)

@section('title', 'Proof Of Delivery Not Uploaded Report')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Proof Of Delivery Not Uploaded Report</a></li> 
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
          "ajax": "/proof/of/delivery/notuploaded/summary/api",
          "columns": [
              {"data":"challan_number"},
              {"data":"referencename"},
              {"data":"partyname"},
              {"data":"itemss"},
              {"data":"qtys"},
              {"data":"delivery_date"},
              {"data":"total_amount"}
                
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 6 }
            ]
          
        });
    });


  </script>
@endsection

@section('main_section')
    <section class="content">
    <div id="app">
                    @include('sections.flash-message')
                    <a href="/export/data/proofofdeliverynot" ><button class="btn btn-primary">Export Proof Of Delivery Not Uploaded</button></a>
                    @yield('content')
                    </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                      @endsection
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Delivery Challan Number</th>
                      <th>Reference Number</th>
                      <th>Party Name</th>
                      <th>Item Name</th>
                      <th>Qty</th>
                      <th>Delivery Date</th>
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