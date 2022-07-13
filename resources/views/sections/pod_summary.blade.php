@extends($layout)

@section('title', 'Proof Of Delivery Summary')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')

    <li><a href="#"><i class=""></i>Proof Of Delivery Summary</a></li> 
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
          "ajax": "/proof/of/delivery/summary/api",
          "columns": [
              {"data":"challan_number"},
              {"data":"delivery_date"},
              {"data":"pod_recieved"},
              {"data":"partyname"},
              {"data":"itemss"},
              {"data":"qtys"},
                {
                  "targets": [ -1 ],
                  data : function(data,type,full,meta)
                  {
                    if(data.pod_recieved =="Docket"){
                      return "<a href='/upload/dc/"+data.docket_upload+"' target='_blank'><button class='btn btn-primary btn-xs'> View POD </button></a> &nbsp;"
                      ;
                    }
                    else{
                      return "<a href='/upload/dc/"+data.dc_upload+"' target='_blank'><button class='btn btn-openid btn-xs'>View POD</button></a> &nbsp;"
                    ;
                    }
                  
                  }
              }
            ],
            "columnDefs": [
             
              { "orderable": false, "targets": 2 },
              { "orderable": false, "targets": 4 },
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
                    @yield('content')
                    </div>
        <!-- Default box -->
        <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    @section('titlebutton')
                    <a href="/export/data/proofofdelivery" ><button class="btn btn-primary">Export Proof Of Delivery</button></a> 
                      @endsection
                    <table id="table" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Delivery Challan Number</th>
                      <th>Delivery Date</th>
                      <th>POD Recieved on</th>
                      <th>Client Name</th>
                      <th>Item Name</th>
                      <th>Quantity</th>
                      <th>View</th>
                      
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